<?php

use App\Models\AvailabilityRule;
use App\Models\Service;

it('belongs to a service', function () {
    $service = Service::factory()->create();
    $rule = AvailabilityRule::factory()->for($service)->create();

    expect($rule->service)->toBeInstanceOf(Service::class)
        ->and($rule->service->id)->toBe($service->id);
});

it('exposes day-of-week constants', function () {
    expect(AvailabilityRule::DAY_SUNDAY)->toBe(0)
        ->and(AvailabilityRule::DAY_MONDAY)->toBe(1)
        ->and(AvailabilityRule::DAY_SATURDAY)->toBe(6);
});

it('casts day_of_week to integer', function () {
    $rule = AvailabilityRule::factory()->monday()->create();

    expect($rule->day_of_week)->toBe(1);
});
