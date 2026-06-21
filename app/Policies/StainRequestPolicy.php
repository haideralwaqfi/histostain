<?php

namespace App\Policies;

use App\Enums\StainRequestStatus;
use App\Enums\UserRole;
use App\Models\StainRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StainRequestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isApproved();
    }

    public function view(User $user, StainRequest $request): bool
    {
        return match($user->role) {
            UserRole::Admin => true,
            UserRole::Doctor => $user->id === $request->doctor_id,
            UserRole::Tech => true, // techs see all requests in the queue
            default => false,
        };
    }

    public function create(User $user): bool
    {
        return $user->isDoctor();
    }

    /** Doctor can edit their own request at any status. */
    public function update(User $user, StainRequest $request): bool
    {
        return $user->isDoctor()
            && $user->id === $request->doctor_id;
    }

    /** Doctor can cancel their own pending request; admin can cancel anything active. */
    public function cancel(User $user, StainRequest $request): bool
    {
        if ($user->isAdmin()) {
            return ! in_array($request->status, [StainRequestStatus::Completed, StainRequestStatus::Cancelled]);
        }

        return $user->isDoctor()
            && $user->id === $request->doctor_id
            && $request->status === StainRequestStatus::Pending;
    }

    /** Only techs and admins can advance a request through the workflow. */
    public function transition(User $user, StainRequest $request): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTech()) {
            // Tech can only act on requests assigned to them or unassigned pending ones
            return $request->assigned_tech_id === null
                || $request->assigned_tech_id === $user->id;
        }

        return false;
    }

    /** Admin can assign any request to any tech. Tech can self-assign a pending request. */
    public function assign(User $user, StainRequest $request): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTech()) {
            return $request->status === StainRequestStatus::Pending
                && $request->assigned_tech_id === null;
        }

        return false;
    }

    public function addResultAttachment(User $user, StainRequest $request): bool
    {
        return ($user->isTech() && $request->assigned_tech_id === $user->id)
            || $user->isAdmin();
    }
}
