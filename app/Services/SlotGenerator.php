<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use Carbon\CarbonImmutable;

class SlotGenerator
{
    /**
     * Build a map of available slots between two dates (inclusive).
     *
     * Output shape:
     *   ['2026-06-12' => ['09:00', '09:30', ...], ...]
     *
     * Times are expressed in the provider's local timezone so the customer
     * can decide how to display them.
     *
     * @return array<string, array<int, string>>
     */
    public function generate(Service $service, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $service->loadMissing(['availabilityRules', 'user']);

        $tz = $service->user?->timezone ?? 'UTC';
        $duration = $service->duration_minutes;
        $buffer = $service->buffer_minutes;
        $stepMinutes = $duration + $buffer;

        // Pre-load conflicting bookings once (pending + confirmed only).
        $bookings = $service->bookings()
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->where('starts_at', '<=', $to->endOfDay()->utc()->toDateTimeString())
            ->where('ends_at', '>=', $from->startOfDay()->utc()->toDateTimeString())
            ->get(['starts_at', 'ends_at']);

        $rulesByDay = $service->availabilityRules->groupBy('day_of_week');
        $now = CarbonImmutable::now($tz);

        $result = [];

        for ($date = $from->startOfDay(); $date->lte($to->endOfDay()); $date = $date->addDay()) {
            $dayOfWeek = (int) $date->dayOfWeek;
            $dayKey = $date->format('Y-m-d');
            $daySlots = [];

            $rules = $rulesByDay->get($dayOfWeek, collect());

            foreach ($rules as $rule) {
                $slotStart = CarbonImmutable::createFromFormat(
                    'Y-m-d H:i',
                    $date->format('Y-m-d').' '.substr($rule->start_time, 0, 5),
                    $tz
                );
                $ruleEnd = CarbonImmutable::createFromFormat(
                    'Y-m-d H:i',
                    $date->format('Y-m-d').' '.substr($rule->end_time, 0, 5),
                    $tz
                );

                while ($slotStart->copy()->addMinutes($duration)->lte($ruleEnd)) {
                    $slotEnd = $slotStart->copy()->addMinutes($duration);

                    // Skip slots already in the past for the provider.
                    if ($slotStart->gt($now) && !$this->overlapsAnyBooking($slotStart, $slotEnd, $bookings)) {
                        $daySlots[] = $slotStart->format('H:i');
                    }

                    $slotStart = $slotStart->addMinutes($stepMinutes);
                }
            }

            if (!empty($daySlots)) {
                $result[$dayKey] = $daySlots;
            }
        }

        return $result;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Booking>  $bookings
     */
    private function overlapsAnyBooking(CarbonImmutable $start, CarbonImmutable $end, $bookings): bool
    {
        foreach ($bookings as $booking) {
            $bookingStart = CarbonImmutable::parse($booking->starts_at);
            $bookingEnd = CarbonImmutable::parse($booking->ends_at);

            if ($start->lt($bookingEnd) && $end->gt($bookingStart)) {
                return true;
            }
        }

        return false;
    }
}
