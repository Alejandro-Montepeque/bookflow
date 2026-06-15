<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'slug' => 'test-user',
            'timezone' => 'UTC',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    expect($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->slug)->toBe('test-user')
        ->and($user->timezone)->toBe('UTC')
        ->and($user->email_verified_at)->toBeNull();
});

test('slug must be unique across users', function () {
    $other = User::factory()->create(['slug' => 'taken-slug']);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => 'taken-slug',
            'timezone' => 'UTC',
        ])
        ->assertSessionHasErrors('slug');
});

test('slug can keep its current value (no false-positive uniqueness error)', function () {
    $user = User::factory()->create(['slug' => 'my-slug']);

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => 'my-slug',
            'timezone' => $user->timezone,
        ])
        ->assertSessionHasNoErrors();
});

test('slug rejects invalid characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => 'Has Spaces!',
            'timezone' => 'UTC',
        ])
        ->assertSessionHasErrors('slug');
});

test('timezone must be a valid IANA identifier', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'slug' => $user->slug,
            'timezone' => 'Fictional/Place',
        ])
        ->assertSessionHasErrors('timezone');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
            'slug' => $user->slug,
            'timezone' => $user->timezone,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/profile')
        ->delete('/profile', ['password' => 'wrong-password'])
        ->assertSessionHasErrors('password')
        ->assertRedirect('/profile');

    expect($user->fresh())->not->toBeNull();
});
