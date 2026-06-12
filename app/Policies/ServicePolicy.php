<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    // Any authenticated user can list their own services.
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $service): bool
    {
        return $this->isOwner($user, $service);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Service $service): bool
    {
        return $this->isOwner($user, $service);
    }

    public function delete(User $user, Service $service): bool
    {
        return $this->isOwner($user, $service);
    }

    private function isOwner(User $user, Service $service): bool
    {
        return $service->user_id === $user->id;
    }
}
