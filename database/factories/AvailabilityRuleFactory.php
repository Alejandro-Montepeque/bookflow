<?php

namespace Database\Factories;

use App\Models\AvailabilityRule;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvailabilityRule>
 */
class AvailabilityRuleFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'day_of_week' => fake()->numberBetween(1, 5),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ];
    }

    public function monday(): static
    {
        return $this->state(fn () => ['day_of_week' => AvailabilityRule::DAY_MONDAY]);
    }

    public function weekdays(): static
    {
        return $this->state(fn () => ['day_of_week' => fake()->numberBetween(1, 5)]);
    }
}
