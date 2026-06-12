<?php

use App\Models\AvailabilityRule;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->provider = User::factory()->create([
        'name' => 'Alejandro Test',
        'slug' => 'alejandro-test',
        'timezone' => 'UTC',
    ]);

    $this->service = Service::factory()->for($this->provider)->create([
        'name' => 'Strategy Call',
        'slug' => 'strategy-call',
        'duration_minutes' => 30,
        'buffer_minutes' => 0,
        'is_active' => true,
    ]);
});

// ---------------------------------------------------------------------------
// Show
// ---------------------------------------------------------------------------

it('renders the public booking page for an active service', function () {
    AvailabilityRule::factory()->for($this->service)->create([
        'day_of_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
    ]);

    $this->get('/u/alejandro-test/strategy-call')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/BookService')
            ->where('provider.name', 'Alejandro Test')
            ->where('service.name', 'Strategy Call')
            ->has('slots')
            ->has('range.from')
            ->has('range.to')
        );
});

it('returns 404 for an unknown provider slug', function () {
    $this->get('/u/non-existent/foo')->assertNotFound();
});

it('returns 404 for an inactive service', function () {
    $service = Service::factory()->for($this->provider)->inactive()->create([
        'slug' => 'private-stuff',
    ]);

    $this->get("/u/alejandro-test/{$service->slug}")->assertNotFound();
});

// ---------------------------------------------------------------------------
// Slot generation (via the SlotGenerator)
// ---------------------------------------------------------------------------

it('generates 30-minute slots within an availability rule', function () {
    // Monday rule 09:00-12:00 → 30min slots → 09:00, 09:30, 10:00, 10:30, 11:00, 11:30
    AvailabilityRule::factory()->for($this->service)->create([
        'day_of_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
    ]);

    $response = $this->get('/u/alejandro-test/strategy-call');

    $response->assertInertia(function ($page) {
        $slots = $page->toArray()['props']['slots'] ?? [];

        // Find the next Monday in the slots map.
        $nextMonday = CarbonImmutable::now('UTC')->next('Monday')->format('Y-m-d');
        if (!array_key_exists($nextMonday, $slots)) {
            // If today is already Monday and not all slots are in the past,
            // accept either today's or next Monday's slots.
            $nextMonday = CarbonImmutable::now('UTC')->format('Y-m-d');
        }

        expect($slots)->toHaveKey($nextMonday);
        expect(count($slots[$nextMonday]))->toBeGreaterThanOrEqual(6);
    });
});

it('excludes slots that conflict with existing bookings', function () {
    AvailabilityRule::factory()->for($this->service)->create([
        'day_of_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '11:00:00',
    ]);

    // Take the 10:00 slot on next Monday.
    $nextMonday = CarbonImmutable::now('UTC')->next('Monday')->setTime(10, 0);
    Booking::factory()->for($this->service)->confirmed()->create([
        'starts_at' => $nextMonday,
        'ends_at' => $nextMonday->addMinutes(30),
    ]);

    $response = $this->get('/u/alejandro-test/strategy-call');

    $response->assertInertia(function ($page) use ($nextMonday) {
        $slots = $page->toArray()['props']['slots'] ?? [];
        $dayKey = $nextMonday->format('Y-m-d');

        expect($slots[$dayKey] ?? [])->not->toContain('10:00');
    });
});

// ---------------------------------------------------------------------------
// Store
// ---------------------------------------------------------------------------

it('creates a booking from the public form and redirects to confirmation', function () {
    AvailabilityRule::factory()->for($this->service)->create([
        'day_of_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
    ]);

    $slot = CarbonImmutable::now('UTC')->next('Monday')->setTime(10, 0);

    $response = $this->post('/u/alejandro-test/strategy-call', [
        'customer_name' => 'Customer One',
        'customer_email' => 'customer@example.com',
        'starts_at' => $slot->toIso8601String(),
        'notes' => 'Looking forward to it.',
    ]);

    $booking = Booking::where('customer_email', 'customer@example.com')->first();
    expect($booking)->not->toBeNull()
        ->and($booking->status)->toBe(Booking::STATUS_CONFIRMED);

    $response->assertRedirect("/booking/{$booking->cancellation_token}");
});

it('rejects a booking in the past', function () {
    $past = CarbonImmutable::now()->subDay()->setTime(10, 0);

    $this->post('/u/alejandro-test/strategy-call', [
        'customer_name' => 'Anyone',
        'customer_email' => 'anyone@example.com',
        'starts_at' => $past->toIso8601String(),
    ])->assertSessionHasErrors('starts_at');
});

it('rejects a booking with invalid email', function () {
    $future = CarbonImmutable::now()->addDays(3)->setTime(10, 0);

    $this->post('/u/alejandro-test/strategy-call', [
        'customer_name' => 'Anyone',
        'customer_email' => 'not-an-email',
        'starts_at' => $future->toIso8601String(),
    ])->assertSessionHasErrors('customer_email');
});

it('rejects a double-booking of the same slot', function () {
    AvailabilityRule::factory()->for($this->service)->create([
        'day_of_week' => 1,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
    ]);

    $slot = CarbonImmutable::now('UTC')->next('Monday')->setTime(10, 0);

    Booking::factory()->for($this->service)->confirmed()->create([
        'starts_at' => $slot,
        'ends_at' => $slot->addMinutes(30),
    ]);

    $this->post('/u/alejandro-test/strategy-call', [
        'customer_name' => 'Late Bird',
        'customer_email' => 'late@example.com',
        'starts_at' => $slot->toIso8601String(),
    ])->assertSessionHasErrors('starts_at');
});

// ---------------------------------------------------------------------------
// Confirmation
// ---------------------------------------------------------------------------

it('shows the confirmation page when given a valid token', function () {
    $booking = Booking::factory()->for($this->service)->confirmed()->create();

    $this->get("/booking/{$booking->cancellation_token}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/BookingConfirmed')
            ->where('booking.cancellation_token', $booking->cancellation_token)
            ->where('service.name', 'Strategy Call')
            ->where('provider.name', 'Alejandro Test')
        );
});

it('returns 404 for an unknown confirmation token', function () {
    $this->get('/booking/not-a-real-token')->assertNotFound();
});
