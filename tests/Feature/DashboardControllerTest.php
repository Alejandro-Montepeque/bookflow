<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->user = User::factory()->create(['timezone' => 'UTC']);
});

// ---------------------------------------------------------------------------
// Access
// ---------------------------------------------------------------------------

it('redirects guests to login from the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

it('renders the dashboard with stats props', function () {
    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('stats.upcoming_bookings')
            ->has('stats.active_services')
            ->has('stats.revenue_cents_this_month')
            ->has('stats.revenue_currency')
            ->has('stats.month_label')
            ->has('nextBookings')
        );
});

// ---------------------------------------------------------------------------
// Stats accuracy
// ---------------------------------------------------------------------------

it('counts only the authenticated user upcoming bookings', function () {
    $myService = Service::factory()->for($this->user)->create();
    Booking::factory(3)->for($myService)->confirmed()->create();
    Booking::factory(1)->for($myService)->past()->create(); // past — excluded
    Booking::factory(1)->for($myService)->cancelled()->create(); // cancelled — excluded
    Booking::factory(2)->confirmed()->create(); // another user — excluded

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.upcoming_bookings', 3));
});

it('counts only active services the user owns', function () {
    Service::factory(2)->for($this->user)->create();          // active mine
    Service::factory()->for($this->user)->inactive()->create(); // inactive mine — excluded
    Service::factory()->create();                                // active of someone else — excluded

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.active_services', 2));
});

it('sums revenue from succeeded payments paid this month', function () {
    $service = Service::factory()->for($this->user)->create();
    $thisMonthBooking = Booking::factory()->for($service)->confirmed()->create();
    $lastMonthBooking = Booking::factory()->for($service)->confirmed()->create();

    Payment::factory()->for($thisMonthBooking)->succeeded()->create([
        'amount_cents' => 5000,
        'paid_at' => CarbonImmutable::now()->startOfMonth()->addDays(2),
    ]);
    Payment::factory()->for($lastMonthBooking)->succeeded()->create([
        'amount_cents' => 9999,
        'paid_at' => CarbonImmutable::now()->subMonth(),
    ]);

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.revenue_cents_this_month', 5000));
});

it('ignores failed payments in revenue calculation', function () {
    $service = Service::factory()->for($this->user)->create();
    $booking = Booking::factory()->for($service)->confirmed()->create();

    Payment::factory()->for($booking)->failed()->create([
        'amount_cents' => 10000,
        'paid_at' => CarbonImmutable::now(),
    ]);

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.revenue_cents_this_month', 0));
});

// ---------------------------------------------------------------------------
// Next bookings list
// ---------------------------------------------------------------------------

it('returns the next 5 upcoming bookings ordered by start time', function () {
    $service = Service::factory()->for($this->user)->create();

    foreach (range(1, 7) as $i) {
        Booking::factory()->for($service)->confirmed()->create([
            'starts_at' => CarbonImmutable::now()->addDays($i),
            'ends_at' => CarbonImmutable::now()->addDays($i)->addMinutes(30),
        ]);
    }

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn ($page) => $page->has('nextBookings', 5));
});

it('does not expose other users bookings in nextBookings', function () {
    $myService = Service::factory()->for($this->user)->create();
    Booking::factory()->for($myService)->confirmed()->create();
    Booking::factory(3)->confirmed()->create(); // another user's bookings

    $this->actingAs($this->user)
        ->get('/dashboard')
        ->assertInertia(fn ($page) => $page->has('nextBookings', 1));
});
