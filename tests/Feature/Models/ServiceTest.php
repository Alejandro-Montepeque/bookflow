<?php

use App\Models\AvailabilityRule;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;

it('belongs to a user (the provider/owner of the service)', function () {
    $user = User::factory()->create();
    $service = Service::factory()->for($user)->create();

    expect($service->user)->toBeInstanceOf(User::class)
        ->and($service->user->id)->toBe($user->id);
});

it('exposes a hasMany relationship with availability rules', function () {
    $service = Service::factory()->create();
    AvailabilityRule::factory()->for($service)->count(3)->create();

    expect($service->availabilityRules)->toHaveCount(3);
});

it('exposes a hasMany relationship with bookings', function () {
    $service = Service::factory()->create();
    Booking::factory()->for($service)->count(2)->create();

    expect($service->bookings)->toHaveCount(2);
});

it('auto-generates a slug from the name when no slug is provided', function () {
    $user = User::factory()->create();
    $service = Service::factory()->for($user)->create([
        'name' => 'My Brand New Service',
        'slug' => '',
    ]);

    expect($service->slug)->toBe('my-brand-new-service');
});

it('keeps a user-provided slug intact', function () {
    $service = Service::factory()->create(['slug' => 'custom-slug']);

    expect($service->slug)->toBe('custom-slug');
});

it('formats the price using the formatted_price accessor', function () {
    $service = Service::factory()->create([
        'price_cents' => 5000,
        'currency' => 'USD',
    ]);

    expect($service->formatted_price)->toBe('50.00 USD');
});
