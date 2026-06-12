<?php

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = Service::factory()->for($this->user)->create();
});

// ---------------------------------------------------------------------------
// Index — listing + tab filtering + ownership
// ---------------------------------------------------------------------------

it('redirects guests away from bookings index', function () {
    $this->get('/bookings')->assertRedirect('/login');
});

it('lists only the authenticated user bookings', function () {
    Booking::factory(2)->for($this->service)->confirmed()->create();
    Booking::factory(3)->confirmed()->create(); // another user's service

    $this->actingAs($this->user)
        ->get('/bookings')
        ->assertInertia(fn ($page) => $page
            ->component('Bookings/Index')
            ->has('bookings', 2)
            ->where('tab', 'upcoming')
            ->has('counts', fn ($c) => $c->where('upcoming', 2)->where('past', 0)->where('cancelled', 0))
        );
});

it('filters by tab=past', function () {
    Booking::factory(2)->for($this->service)->past()->create();
    Booking::factory(1)->for($this->service)->confirmed()->create();

    $this->actingAs($this->user)
        ->get('/bookings?tab=past')
        ->assertInertia(fn ($page) => $page
            ->where('tab', 'past')
            ->has('bookings', 2)
        );
});

it('filters by tab=cancelled', function () {
    Booking::factory(3)->for($this->service)->cancelled()->create();
    Booking::factory(2)->for($this->service)->confirmed()->create();

    $this->actingAs($this->user)
        ->get('/bookings?tab=cancelled')
        ->assertInertia(fn ($page) => $page
            ->where('tab', 'cancelled')
            ->has('bookings', 3)
        );
});

it('filters by service_id', function () {
    $otherService = Service::factory()->for($this->user)->create();
    Booking::factory(2)->for($this->service)->confirmed()->create();
    Booking::factory(3)->for($otherService)->confirmed()->create();

    $this->actingAs($this->user)
        ->get("/bookings?service_id={$otherService->id}")
        ->assertInertia(fn ($page) => $page->has('bookings', 3));
});

it('defaults to upcoming tab when given an invalid value', function () {
    $this->actingAs($this->user)
        ->get('/bookings?tab=garbage')
        ->assertInertia(fn ($page) => $page->where('tab', 'upcoming'));
});

it('excludes cancelled bookings from the past tab', function () {
    // 2 past+completed (should appear) and 1 cancelled past (should NOT appear).
    Booking::factory(2)->for($this->service)->past()->create();
    Booking::factory(1)->for($this->service)->cancelled()->create([
        'starts_at' => now()->subDays(2),
        'ends_at' => now()->subDays(2)->addMinutes(30),
    ]);

    $this->actingAs($this->user)
        ->get('/bookings?tab=past')
        ->assertInertia(fn ($page) => $page->has('bookings', 2));
});

// ---------------------------------------------------------------------------
// Cancel
// ---------------------------------------------------------------------------

it('cancels a booking the provider owns', function () {
    $booking = Booking::factory()->for($this->service)->confirmed()->create();

    $this->actingAs($this->user)
        ->patch("/bookings/{$booking->id}/cancel")
        ->assertRedirect();

    expect($booking->fresh()->status)->toBe(Booking::STATUS_CANCELLED);
});

it('forbids cancelling another provider booking', function () {
    $booking = Booking::factory()->confirmed()->create(); // another user's service

    $this->actingAs($this->user)
        ->patch("/bookings/{$booking->id}/cancel")
        ->assertForbidden();

    expect($booking->fresh()->status)->toBe(Booking::STATUS_CONFIRMED);
});

// ---------------------------------------------------------------------------
// Complete
// ---------------------------------------------------------------------------

it('marks a booking as completed', function () {
    $booking = Booking::factory()->for($this->service)->confirmed()->create();

    $this->actingAs($this->user)
        ->patch("/bookings/{$booking->id}/complete")
        ->assertRedirect();

    expect($booking->fresh()->status)->toBe(Booking::STATUS_COMPLETED);
});

it('forbids completing another provider booking', function () {
    $booking = Booking::factory()->confirmed()->create();

    $this->actingAs($this->user)
        ->patch("/bookings/{$booking->id}/complete")
        ->assertForbidden();
});
