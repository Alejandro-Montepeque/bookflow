<?php

use App\Models\User;

it('auto-generates a slug from the user name on create', function () {
    $user = User::factory()->create(['name' => 'Alejandro Montepeque', 'slug' => null]);

    expect($user->slug)->toBe('alejandro-montepeque');
});

it('appends a suffix when slug already exists', function () {
    User::factory()->create(['name' => 'John Doe', 'slug' => null]);
    $second = User::factory()->create(['name' => 'John Doe', 'slug' => null]);

    expect($second->slug)->toBe('john-doe-1');
});

it('preserves a custom slug if provided', function () {
    $user = User::factory()->create(['name' => 'Alejandro Montepeque', 'slug' => 'my-custom-slug']);

    expect($user->slug)->toBe('my-custom-slug');
});

it('falls back to "user" when the name has no slug-able characters', function () {
    $user = User::factory()->create(['name' => '!@#$', 'slug' => null]);

    expect($user->slug)->toBe('user');
});

it('stores the user timezone', function () {
    $user = User::factory()->create(['timezone' => 'America/El_Salvador']);

    expect($user->timezone)->toBe('America/El_Salvador');
});

it('defaults the timezone column to UTC at the database level', function () {
    // Bypass the factory's faker timezone to verify the DB default kicks in.
    $user = new User();
    $user->name = 'Test';
    $user->email = 'test+default@example.com';
    $user->password = bcrypt('password');
    $user->save();

    expect($user->fresh()->timezone)->toBe('UTC');
});
