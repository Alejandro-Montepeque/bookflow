<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'stripe_payment_intent_id' => 'pi_'.fake()->bothify('??????????????'),
            'stripe_checkout_session_id' => 'cs_'.fake()->bothify('??????????????'),
            'amount_cents' => fake()->randomElement([2500, 5000, 7500, 10000]),
            'currency' => 'USD',
            'status' => Payment::STATUS_PENDING,
            'paid_at' => null,
        ];
    }

    public function succeeded(): static
    {
        return $this->state(fn () => [
            'status' => Payment::STATUS_SUCCEEDED,
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => ['status' => Payment::STATUS_FAILED]);
    }
}
