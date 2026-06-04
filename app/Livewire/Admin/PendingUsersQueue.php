<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\UserApprovalStatusChanged;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PendingUsersQueue extends Component
{
    public ?int $rejectingUserId = null;
    public string $rejectionReason = '';

    public function approve(int $userId, string $role): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('approve', $user);

        $user->update([
            'status' => UserStatus::Approved,
            'role' => UserRole::from($role),
        ]);

        $user->notify(new UserApprovalStatusChanged(UserStatus::Approved));

        $this->dispatch('toast', message: "{$user->name} approved as {$user->role->label()}.", type: 'success');
    }

    public function startReject(int $userId): void
    {
        $this->rejectingUserId = $userId;
        $this->rejectionReason = '';
    }

    public function confirmReject(): void
    {
        $this->validate(['rejectionReason' => 'required|string|min:5|max:500']);

        $user = User::findOrFail($this->rejectingUserId);
        $this->authorize('reject', $user);

        $user->update([
            'status' => UserStatus::Rejected,
            'rejection_reason' => $this->rejectionReason,
        ]);

        $user->notify(new UserApprovalStatusChanged(UserStatus::Rejected));

        $this->rejectingUserId = null;
        $this->rejectionReason = '';

        $this->dispatch('toast', message: "{$user->name}'s registration rejected.", type: 'warning');
    }

    public function cancelReject(): void
    {
        $this->rejectingUserId = null;
        $this->rejectionReason = '';
    }

    public function render()
    {
        return view('livewire.admin.pending-users-queue', [
            'pendingUsers' => User::where('status', UserStatus::Pending)
                ->latest()
                ->get(),
            'roles' => UserRole::cases(),
        ]);
    }
}
