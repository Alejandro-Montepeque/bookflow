<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->randomElement([
            '30-min Consultation',
            'Initial Discovery Call',
            'Strategy Session',
            'Code Review',
            'Pair Programming',
            'Career Coaching',
            'Tarot Reading',
            'Massage Therapy',
        ]);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'description' => fake()->sentence(12),
            'duration_minutes' => fake()->randomElement([15, 30, 45, 60, 90]),
            'price_cents' => fake()->randomElement([2500, 5000, 7500, 10000, 15000]),
            'currency' => 'USD',
            'color' => fake()->randomElement(['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#3b82f6']),
            'buffer_minutes' => fake()->randomElement([0, 5, 10, 15]),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
