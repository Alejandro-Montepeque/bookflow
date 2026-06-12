<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $endsAt = (clone $startsAt)->modify('+30 minutes');

        return [
            'service_id' => Service::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => Booking::STATUS_PENDING,
            'timezone' => 'UTC',
            'notes' => fake()->optional()->sentence(),
            'cancellation_token' => Str::random(48),
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => Booking::STATUS_CONFIRMED]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => Booking::STATUS_CANCELLED]);
    }

    public function past(): static
    {
        return $this->state(function () {
            $startsAt = fake()->dateTimeBetween('-30 days', '-1 day');

            return [
                'starts_at' => $startsAt,
                'ends_at' => (clone $startsAt)->modify('+30 minutes'),
                'status' => Booking::STATUS_COMPLETED,
            ];
        });
    }
}
