<?php

use App\Models\Booking;
use App\Models\Payment;

it('belongs to a booking', function () {
    $booking = Booking::factory()->create();
    $payment = Payment::factory()->for($booking)->create();

    expect($payment->booking)->toBeInstanceOf(Booking::class)
        ->and($payment->booking->id)->toBe($booking->id);
});

it('exposes status constants', function () {
    expect(Payment::STATUS_PENDING)->toBe('pending')
        ->and(Payment::STATUS_SUCCEEDED)->toBe('succeeded')
        ->and(Payment::STATUS_FAILED)->toBe('failed');
});

it('isSuccessful() returns true only when status is succeeded', function () {
    $succeeded = Payment::factory()->succeeded()->create();
    $pending = Payment::factory()->create();
    $failed = Payment::factory()->failed()->create();

    expect($succeeded->isSuccessful())->toBeTrue()
        ->and($pending->isSuccessful())->toBeFalse()
        ->and($failed->isSuccessful())->toBeFalse();
});

it('casts paid_at to a datetime instance when set', function () {
    $payment = Payment::factory()->succeeded()->create();

    // CarbonInterface covers both Carbon and CarbonImmutable depending on Laravel config.
    expect($payment->paid_at)->toBeInstanceOf(\Carbon\CarbonInterface::class);
});

it('enforces one-to-one relation: a booking has at most one payment', function () {
    $booking = Booking::factory()->create();
    Payment::factory()->for($booking)->create();

    expect(fn () => Payment::factory()->for($booking)->create())
        ->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});
