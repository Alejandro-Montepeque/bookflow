<?php

use App\Models\AvailabilityRule;
use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->service = Service::factory()->for($this->user)->create();
});

function syncUrl(int $serviceId): string
{
    return "/services/{$serviceId}/availability";
}

it('redirects guests when syncing availability', function () {
    $this->put(syncUrl($this->service->id), ['rules' => []])
        ->assertRedirect('/login');
});

it('replaces the existing rules with the new payload', function () {
    AvailabilityRule::factory(2)->for($this->service)->create();

    $payload = [
        'rules' => [
            ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '12:00'],
            ['day_of_week' => 3, 'start_time' => '14:00', 'end_time' => '17:00'],
        ],
    ];

    $this->actingAs($this->user)
        ->put(syncUrl($this->service->id), $payload)
        ->assertRedirect("/services/{$this->service->id}/edit");

    expect($this->service->fresh()->availabilityRules)->toHaveCount(2);
    $this->assertDatabaseHas('availability_rules', [
        'service_id' => $this->service->id,
        'day_of_week' => 1,
        'start_time' => '09:00:00',
    ]);
});

it('clears all rules when an empty array is sent', function () {
    AvailabilityRule::factory(3)->for($this->service)->create();

    $this->actingAs($this->user)
        ->put(syncUrl($this->service->id), ['rules' => []])
        ->assertRedirect();

    expect($this->service->fresh()->availabilityRules)->toHaveCount(0);
});

it('rejects a rule with end_time before start_time', function () {
    $this->actingAs($this->user)
        ->put(syncUrl($this->service->id), [
            'rules' => [['day_of_week' => 1, 'start_time' => '17:00', 'end_time' => '09:00']],
        ])
        ->assertSessionHasErrors('rules.0.end_time');
});

it('rejects an invalid day_of_week', function () {
    $this->actingAs($this->user)
        ->put(syncUrl($this->service->id), [
            'rules' => [['day_of_week' => 9, 'start_time' => '09:00', 'end_time' => '17:00']],
        ])
        ->assertSessionHasErrors('rules.0.day_of_week');
});

it('rejects malformed time strings', function () {
    $this->actingAs($this->user)
        ->put(syncUrl($this->service->id), [
            'rules' => [['day_of_week' => 1, 'start_time' => '9am', 'end_time' => '5pm']],
        ])
        ->assertSessionHasErrors(['rules.0.start_time', 'rules.0.end_time']);
});

it('forbids syncing availability of another user service', function () {
    $otherService = Service::factory()->create();

    $this->actingAs($this->user)
        ->put(syncUrl($otherService->id), ['rules' => []])
        ->assertForbidden();
});

it('keeps the original rules intact when validation fails (transaction safety)', function () {
    AvailabilityRule::factory(2)->for($this->service)->create();

    $this->actingAs($this->user)
        ->put(syncUrl($this->service->id), [
            'rules' => [['day_of_week' => 1, 'start_time' => '17:00', 'end_time' => '09:00']],
        ])
        ->assertSessionHasErrors();

    expect($this->service->fresh()->availabilityRules)->toHaveCount(2);
});
