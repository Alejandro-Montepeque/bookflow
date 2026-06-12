<?php

namespace Database\Seeders;

use App\Models\AvailabilityRule;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate keeps the seeder idempotent: running `db:seed` twice
        // will not duplicate the demo provider nor blow up on unique constraints.
        $provider = User::updateOrCreate(
            ['email' => 'demo@bookflow.app'],
            [
                'name' => 'Alejandro Montepeque',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $consultation = $this->ensureService($provider, [
            'name' => '30-min Consultation',
            'slug' => '30min-consultation',
            'description' => 'Discovery call to scope a project together.',
            'duration_minutes' => 30,
            'price_cents' => 5000,
            'color' => '#6366f1',
        ]);

        $codeReview = $this->ensureService($provider, [
            'name' => 'Code Review (1 hour)',
            'slug' => 'code-review-1h',
            'description' => 'Deep review of one PR or repository, including a follow-up doc.',
            'duration_minutes' => 60,
            'price_cents' => 12000,
            'color' => '#10b981',
        ]);

        // Mon–Fri 9–17h for both services. Wipe and recreate so reseeds stay clean.
        foreach ([$consultation, $codeReview] as $service) {
            $service->availabilityRules()->delete();
            foreach (range(1, 5) as $day) {
                AvailabilityRule::factory()->for($service)->create([
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                ]);
            }
        }

        // Refresh demo bookings on each run so the dashboard never shows stale data.
        Booking::query()->whereIn('service_id', [$consultation->id, $codeReview->id])->delete();

        $upcoming = Booking::factory(3)->for($consultation)->confirmed()->create();
        Booking::factory(2)->for($consultation)->past()->create();

        Payment::factory()->for($upcoming->first())->succeeded()->create([
            'amount_cents' => $consultation->price_cents,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function ensureService(User $user, array $attributes): Service
    {
        return Service::updateOrCreate(
            ['user_id' => $user->id, 'slug' => $attributes['slug']],
            $attributes + ['user_id' => $user->id]
        );
    }
}
