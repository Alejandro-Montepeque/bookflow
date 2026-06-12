<?php

use App\Models\Service;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

// ---------------------------------------------------------------------------
// Index
// ---------------------------------------------------------------------------

it('redirects guests away from the services index', function () {
    $this->get('/services')->assertRedirect('/login');
});

it('lists the authenticated user services on index', function () {
    $services = Service::factory(3)->for($this->user)->create();
    Service::factory()->create(); // another user's service — should not appear

    $response = $this->actingAs($this->user)->get('/services');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->component('Services/Index')
        ->has('services', 3)
    );
});

// ---------------------------------------------------------------------------
// Create / Store
// ---------------------------------------------------------------------------

it('renders the create page for authenticated users', function () {
    $this->actingAs($this->user)
        ->get('/services/create')
        ->assertInertia(fn ($page) => $page->component('Services/Create'));
});

it('stores a new service and redirects to its edit page', function () {
    $payload = [
        'name' => '45-min Strategy Session',
        'description' => 'Deep dive into your roadmap.',
        'duration_minutes' => 45,
        'price_cents' => 7500,
        'currency' => 'USD',
        'color' => '#6366f1',
        'buffer_minutes' => 10,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->user)
        ->post('/services', $payload);

    $this->assertDatabaseHas('services', [
        'user_id' => $this->user->id,
        'name' => '45-min Strategy Session',
        'price_cents' => 7500,
    ]);

    $service = Service::where('user_id', $this->user->id)->latest('id')->first();
    $response->assertRedirect("/services/{$service->id}/edit");
});

it('rejects a service with invalid color format', function () {
    $payload = Service::factory()->raw([
        'color' => 'red',
    ]);

    $this->actingAs($this->user)
        ->post('/services', $payload)
        ->assertSessionHasErrors('color');
});

it('rejects a service with negative duration', function () {
    $payload = Service::factory()->raw([
        'duration_minutes' => -5,
    ]);

    $this->actingAs($this->user)
        ->post('/services', $payload)
        ->assertSessionHasErrors('duration_minutes');
});

it('rejects a service with unsupported currency', function () {
    $payload = Service::factory()->raw([
        'currency' => 'JPY',
    ]);

    $this->actingAs($this->user)
        ->post('/services', $payload)
        ->assertSessionHasErrors('currency');
});

// ---------------------------------------------------------------------------
// Edit / Update
// ---------------------------------------------------------------------------

it('shows the edit page for the service owner', function () {
    $service = Service::factory()->for($this->user)->create();

    $this->actingAs($this->user)
        ->get("/services/{$service->id}/edit")
        ->assertInertia(fn ($page) => $page
            ->component('Services/Edit')
            ->has('service', fn ($s) => $s->where('id', $service->id)->etc())
        );
});

it('forbids editing another user service', function () {
    $service = Service::factory()->create(); // belongs to a different user

    $this->actingAs($this->user)
        ->get("/services/{$service->id}/edit")
        ->assertForbidden();
});

it('updates a service with valid data', function () {
    $service = Service::factory()->for($this->user)->create([
        'name' => 'Old name',
        'price_cents' => 5000,
    ]);

    $payload = [
        'name' => 'New name',
        'description' => null,
        'duration_minutes' => 30,
        'price_cents' => 9000,
        'currency' => 'USD',
        'color' => '#10b981',
        'buffer_minutes' => 0,
        'is_active' => true,
    ];

    $this->actingAs($this->user)
        ->put("/services/{$service->id}", $payload)
        ->assertRedirect('/services');

    expect($service->fresh()->name)->toBe('New name')
        ->and($service->fresh()->price_cents)->toBe(9000);
});

it('forbids updating another user service', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->user)
        ->put("/services/{$service->id}", Service::factory()->raw())
        ->assertForbidden();
});

// ---------------------------------------------------------------------------
// Destroy
// ---------------------------------------------------------------------------

it('deletes a service owned by the user', function () {
    $service = Service::factory()->for($this->user)->create();

    $this->actingAs($this->user)
        ->delete("/services/{$service->id}")
        ->assertRedirect('/services');

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

it('forbids deleting another user service', function () {
    $service = Service::factory()->create();

    $this->actingAs($this->user)
        ->delete("/services/{$service->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('services', ['id' => $service->id]);
});
