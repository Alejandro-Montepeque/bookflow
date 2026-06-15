<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Service;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $tz = $user->timezone ?? 'UTC';
        $now = CarbonImmutable::now($tz);
        $startOfMonth = $now->startOfMonth();
        $endOfMonth = $now->endOfMonth();

        // Upcoming bookings: pending or confirmed, starts_at in the future,
        // belonging to a service the current user owns.
        $upcomingCount = Booking::query()
            ->whereHas('service', fn ($q) => $q->where('user_id', $user->id))
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->where('starts_at', '>=', now())
            ->count();

        // Services the user currently has published.
        $activeServicesCount = Service::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->count();

        // Revenue collected this month = sum of succeeded payments whose paid_at
        // is inside the user's local current month.
        $revenueCents = Payment::query()
            ->whereHas('booking.service', fn ($q) => $q->where('user_id', $user->id))
            ->where('status', Payment::STATUS_SUCCEEDED)
            ->whereBetween('paid_at', [$startOfMonth->utc(), $endOfMonth->utc()])
            ->sum('amount_cents');

        // Next 5 upcoming bookings to display under the stats.
        $nextBookings = Booking::query()
            ->whereHas('service', fn ($q) => $q->where('user_id', $user->id))
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->where('starts_at', '>=', now())
            ->with(['service:id,name,color,duration_minutes', 'payment:id,booking_id,status,amount_cents,currency'])
            ->orderBy('starts_at')
            ->limit(5)
            ->get();

        // Default to USD for the revenue currency — once Stripe is wired up the
        // user will be able to pick. For MVP, all services share a single currency.
        $revenueCurrency = Service::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->value('currency') ?? 'USD';

        return Inertia::render('Dashboard', [
            'stats' => [
                'upcoming_bookings' => $upcomingCount,
                'active_services' => $activeServicesCount,
                'revenue_cents_this_month' => (int) $revenueCents,
                'revenue_currency' => $revenueCurrency,
                'month_label' => $now->format('F Y'),
            ],
            'nextBookings' => $nextBookings,
        ]);
    }
}
