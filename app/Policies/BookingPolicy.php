<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Booking $booking): bool
    {
        return $this->isProviderOwner($user, $booking);
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->isProviderOwner($user, $booking);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $this->isProviderOwner($user, $booking);
    }

    // The booking belongs to a Service; the Service belongs to a User (provider).
    private function isProviderOwner(User $user, Booking $booking): bool
    {
        $booking->loadMissing('service:id,user_id');
        return $booking->service?->user_id === $user->id;
    }
}
