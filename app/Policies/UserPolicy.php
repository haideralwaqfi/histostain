<?php

namespace App\Policies;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $actor): bool
    {
        return $actor->isAdmin();
    }

    public function view(User $actor, User $target): bool
    {
        return $actor->isAdmin() || $actor->id === $target->id;
    }

    public function approve(User $actor, User $target): bool
    {
        return $actor->isAdmin() && $target->status === UserStatus::Pending;
    }

    public function reject(User $actor, User $target): bool
    {
        return $actor->isAdmin() && $target->status === UserStatus::Pending;
    }

    public function assignRole(User $actor, User $target): bool
    {
        return $actor->isAdmin();
    }

    public function update(User $actor, User $target): bool
    {
        return $actor->isAdmin() || $actor->id === $target->id;
    }

    public function delete(User $actor, User $target): bool
    {
        return $actor->isAdmin() && $actor->id !== $target->id;
    }
}
