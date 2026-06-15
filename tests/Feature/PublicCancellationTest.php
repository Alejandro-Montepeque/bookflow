<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->provider = User::factory()->create([
        'slug' => 'alejandro-cancel-test',
        'timezone' => 'UTC',
    ]);
    $this->service = Service::factory()->for($this->provider)->create();
});

// ---------------------------------------------------------------------------
// Show
// ---------------------------------------------------------------------------

it('shows the cancellation page for a valid token', function () {
    $booking = Booking::factory()->for($this->service)->confirmed()->create([
        'starts_at' => CarbonImmutable::now()->addDays(2),
    ]);

    $this->get("/cancel/{$booking->cancellation_token}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/CancelBooking')
            ->where('booking.cancellation_token', $booking->cancellation_token)
            ->where('booking.is_cancellable', true)
        );
});

it('returns 404 for an unknown cancellation token', function () {
    $this->get('/cancel/this-token-does-not-exist')->assertNotFound();
});

it('marks the booking as not cancellable when it is in the past', function () {
    $booking = Booking::factory()->for($this->service)->past()->create();

    $this->get("/cancel/{$booking->cancellation_token}")
        ->assertInertia(fn ($page) => $page->where('booking.is_cancellable', false));
});

it('marks the booking as not cancellable when it is already cancelled', function () {
    $booking = Booking::factory()->for($this->service)->cancelled()->create([
        'starts_at' => CarbonImmutable::now()->addDays(2),
    ]);

    $this->get("/cancel/{$booking->cancellation_token}")
        ->assertInertia(fn ($page) => $page->where('booking.is_cancellable', false));
});

// ---------------------------------------------------------------------------
// Store (actually cancel)
// ---------------------------------------------------------------------------

it('cancels a future confirmed booking via the public token', function () {
    $booking = Booking::factory()->for($this->service)->confirmed()->create([
        'starts_at' => CarbonImmutable::now()->addDays(2),
    ]);

    $this->post("/cancel/{$booking->cancellation_token}")
        ->assertRedirect("/cancel/{$booking->cancellation_token}");

    expect($booking->fresh()->status)->toBe(Booking::STATUS_CANCELLED);
});

it('refuses to cancel a past booking', function () {
    $booking = Booking::factory()->for($this->service)->past()->create();
    $originalStatus = $booking->status;

    $this->post("/cancel/{$booking->cancellation_token}")
        ->assertSessionHasErrors('booking');

    expect($booking->fresh()->status)->toBe($originalStatus);
});

it('refuses to cancel an already cancelled booking', function () {
    $booking = Booking::factory()->for($this->service)->cancelled()->create([
        'starts_at' => CarbonImmutable::now()->addDays(2),
    ]);

    $this->post("/cancel/{$booking->cancellation_token}")
        ->assertSessionHasErrors('booking');

    expect($booking->fresh()->status)->toBe(Booking::STATUS_CANCELLED);
});

it('returns 404 when posting cancel with an unknown token', function () {
    $this->post('/cancel/not-a-real-token')->assertNotFound();
});
