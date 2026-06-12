<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    private const TABS = ['upcoming', 'past', 'cancelled'];

    public function index(Request $request): Response
    {
        $tab = in_array($request->input('tab'), self::TABS, true)
            ? $request->input('tab')
            : 'upcoming';

        $serviceId = $request->input('service_id');

        $base = Booking::query()
            ->whereHas('service', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['service:id,name,color,duration_minutes', 'payment:id,booking_id,status,amount_cents,currency']);

        if ($serviceId) {
            $base->where('service_id', $serviceId);
        }

        $bookings = (clone $base)
            ->when($tab === 'upcoming', fn ($q) => $q->upcoming())
            ->when($tab === 'past', fn ($q) => $q->past()->where('status', '!=', Booking::STATUS_CANCELLED))
            ->when($tab === 'cancelled', fn ($q) => $q->where('status', Booking::STATUS_CANCELLED)->orderByDesc('starts_at'))
            ->get();

        // Counts per tab so the UI badges always reflect total state.
        $counts = [
            'upcoming' => (clone $base)->upcoming()->count(),
            'past' => (clone $base)->past()->where('status', '!=', Booking::STATUS_CANCELLED)->count(),
            'cancelled' => (clone $base)->where('status', Booking::STATUS_CANCELLED)->count(),
        ];

        $services = auth()->user()
            ->services()
            ->orderBy('name')
            ->get(['id', 'name', 'color']);

        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
            'tab' => $tab,
            'counts' => $counts,
            'services' => $services,
            'filters' => ['service_id' => $serviceId ? (int) $serviceId : null],
        ]);
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return back()->with('success', 'Booking cancelled.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        $booking->update(['status' => Booking::STATUS_COMPLETED]);

        return back()->with('success', 'Booking marked as completed.');
    }
}
