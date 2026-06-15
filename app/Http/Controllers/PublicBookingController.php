<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicBookingRequest;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\SlotGenerator;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PublicBookingController extends Controller
{
    public function __construct(private readonly SlotGenerator $slots) {}

    /**
     * Public provider profile — lists all the active services a provider publishes.
     * Used as the landing page when a provider shares /u/{slug}.
     */
    public function profile(string $userSlug): Response
    {
        $provider = User::query()->where('slug', $userSlug)->firstOrFail();

        $services = $provider->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'duration_minutes', 'price_cents', 'currency', 'color']);

        return Inertia::render('Public/ProviderProfile', [
            'provider' => [
                'name' => $provider->name,
                'slug' => $provider->slug,
                'timezone' => $provider->timezone ?? 'UTC',
            ],
            'services' => $services,
        ]);
    }

    /**
     * Public service page — what a customer sees when they hit /u/{user}/{service}.
     */
    public function show(string $userSlug, string $serviceSlug): Response
    {
        $provider = User::query()->where('slug', $userSlug)->firstOrFail();
        $service = $provider->services()
            ->where('slug', $serviceSlug)
            ->where('is_active', true)
            ->with('availabilityRules')
            ->firstOrFail();

        $tz = $provider->timezone ?? 'UTC';
        $from = CarbonImmutable::now($tz);
        $to = $from->addDays(30);

        return Inertia::render('Public/BookService', [
            'provider' => [
                'name' => $provider->name,
                'slug' => $provider->slug,
                'timezone' => $tz,
            ],
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'duration_minutes' => $service->duration_minutes,
                'price_cents' => $service->price_cents,
                'currency' => $service->currency,
                'color' => $service->color,
            ],
            'slots' => $this->slots->generate($service, $from, $to),
            'range' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ],
        ]);
    }

    public function store(StorePublicBookingRequest $request, string $userSlug, string $serviceSlug): RedirectResponse
    {
        $provider = User::query()->where('slug', $userSlug)->firstOrFail();
        $service = $provider->services()
            ->where('slug', $serviceSlug)
            ->where('is_active', true)
            ->firstOrFail();

        // Parse the chosen slot in the provider's timezone, then store as UTC.
        $tz = $provider->timezone ?? 'UTC';
        $startsAt = CarbonImmutable::parse($request->input('starts_at'), $tz);
        $endsAt = $startsAt->addMinutes($service->duration_minutes);

        // In-the-future check, now with the correct timezone applied.
        if ($startsAt->lte(CarbonImmutable::now($tz))) {
            return back()->withErrors(['starts_at' => 'That slot is in the past.']);
        }

        // Wrap the conflict check + insert in a transaction so two concurrent
        // bookings can't both succeed on the same slot.
        try {
            $booking = DB::transaction(function () use ($service, $startsAt, $endsAt, $tz, $request) {
                $hasConflict = $service->bookings()
                    ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                    ->where(function ($q) use ($startsAt, $endsAt) {
                        // Overlap definition: existing.starts_at < new.ends_at AND existing.ends_at > new.starts_at.
                        $q->where('starts_at', '<', $endsAt)
                          ->where('ends_at', '>', $startsAt);
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($hasConflict) {
                    throw new \RuntimeException('slot-taken');
                }

                return $service->bookings()->create([
                    'customer_name' => $request->input('customer_name'),
                    'customer_email' => $request->input('customer_email'),
                    'starts_at' => $startsAt->utc(),
                    'ends_at' => $endsAt->utc(),
                    'status' => Booking::STATUS_CONFIRMED,
                    'timezone' => $tz,
                    'notes' => $request->input('notes'),
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'slot-taken') {
                return back()->withErrors(['starts_at' => 'That slot was just taken. Please pick another one.']);
            }
            throw $e;
        }

        return redirect()->route('public.booking.confirmation', $booking->cancellation_token);
    }

    public function confirmation(string $token): Response
    {
        return $this->renderBookingPage($token, 'Public/BookingConfirmed');
    }

    public function cancelShow(string $token): Response
    {
        return $this->renderBookingPage($token, 'Public/CancelBooking');
    }

    public function cancelStore(string $token): RedirectResponse
    {
        $booking = Booking::query()
            ->where('cancellation_token', $token)
            ->firstOrFail();

        if (!$booking->isCancellable()) {
            return back()->withErrors([
                'booking' => 'This booking can no longer be cancelled.',
            ]);
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return redirect()->route('public.booking.cancel.show', $token)
            ->with('success', 'Your booking has been cancelled.');
    }

    /**
     * Shared payload builder for the Confirmation and Cancel pages.
     */
    private function renderBookingPage(string $token, string $component): Response
    {
        $booking = Booking::query()
            ->where('cancellation_token', $token)
            ->with(['service.user'])
            ->firstOrFail();

        return Inertia::render($component, [
            'booking' => [
                'id' => $booking->id,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'starts_at' => $booking->starts_at->toIso8601String(),
                'ends_at' => $booking->ends_at->toIso8601String(),
                'status' => $booking->status,
                'timezone' => $booking->timezone,
                'cancellation_token' => $booking->cancellation_token,
                'notes' => $booking->notes,
                'is_cancellable' => $booking->isCancellable(),
            ],
            'service' => [
                'name' => $booking->service->name,
                'color' => $booking->service->color,
                'duration_minutes' => $booking->service->duration_minutes,
            ],
            'provider' => [
                'name' => $booking->service->user->name,
            ],
        ]);
    }
}
