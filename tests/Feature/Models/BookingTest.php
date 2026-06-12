<?php

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;

it('belongs to a service', function () {
    $service = Service::factory()->create();
    $booking = Booking::factory()->for($service)->create();

    expect($booking->service)->toBeInstanceOf(Service::class)
        ->and($booking->service->id)->toBe($service->id);
});

it('has one payment', function () {
    $booking = Booking::factory()->create();
    $payment = Payment::factory()->for($booking)->create();

    expect($booking->payment)->toBeInstanceOf(Payment::class)
        ->and($booking->payment->id)->toBe($payment->id);
});

it('auto-generates a cancellation_token on create', function () {
    $booking = Booking::factory()->create(['cancellation_token' => '']);

    expect($booking->cancellation_token)->not->toBeEmpty()
        ->and(strlen($booking->cancellation_token))->toBeGreaterThanOrEqual(40);
});

it('keeps a custom cancellation_token if provided', function () {
    $booking = Booking::factory()->create(['cancellation_token' => 'fixed-token-value']);

    expect($booking->cancellation_token)->toBe('fixed-token-value');
});

it('upcoming() scope returns future pending and confirmed bookings only', function () {
    $service = Service::factory()->create();
    Booking::factory(2)->for($service)->confirmed()->create();
    Booking::factory(1)->for($service)->past()->create();
    Booking::factory(1)->for($service)->cancelled()->create();

    expect(Booking::query()->upcoming()->count())->toBe(2);
});

it('past() scope returns bookings whose start time is in the past', function () {
    $service = Service::factory()->create();
    Booking::factory(2)->for($service)->past()->create();
    Booking::factory(3)->for($service)->confirmed()->create();

    expect(Booking::query()->past()->count())->toBe(2);
});

it('forEmail() scope filters by customer email', function () {
    Booking::factory()->create(['customer_email' => 'alice@example.com']);
    Booking::factory()->create(['customer_email' => 'alice@example.com']);
    Booking::factory()->create(['customer_email' => 'bob@example.com']);

    expect(Booking::query()->forEmail('alice@example.com')->count())->toBe(2);
});

it('isCancellable() returns true for future confirmed bookings', function () {
    $booking = Booking::factory()->confirmed()->create([
        'starts_at' => now()->addDays(2),
    ]);

    expect($booking->isCancellable())->toBeTrue();
});

it('isCancellable() returns false for past bookings', function () {
    $booking = Booking::factory()->past()->create();

    expect($booking->isCancellable())->toBeFalse();
});

it('isCancellable() returns false for cancelled bookings', function () {
    $booking = Booking::factory()->cancelled()->create([
        'starts_at' => now()->addDays(2),
    ]);

    expect($booking->isCancellable())->toBeFalse();
});
