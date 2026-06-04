<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\UserApprovalStatusChanged;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserManagement extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'role')]
    public string $filterRole = '';

    #[Url(as: 'status')]
    public string $filterStatus = '';

    // Inline edit state
    public ?int $editingRoleId = null;
    public string $newRole = '';

    // Reject from this screen too
    public ?int $rejectingId = null;
    public string $rejectReason = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterRole(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    public function startEditRole(int $userId, string $currentRole): void
    {
        $this->editingRoleId = $userId;
        $this->newRole = $currentRole;
    }

    public function saveRole(): void
    {
        $this->validate(['newRole' => 'required|in:admin,doctor,tech']);

        $user = User::findOrFail($this->editingRoleId);
        $this->authorize('assignRole', $user);

        $user->update(['role' => UserRole::from($this->newRole)]);

        $this->editingRoleId = null;
        $this->dispatch('toast', message: "Role updated to {$user->role->label()}.", type: 'success');
    }

    public function cancelEditRole(): void
    {
        $this->editingRoleId = null;
    }

    public function startReject(int $userId): void
    {
        $this->rejectingId = $userId;
        $this->rejectReason = '';
    }

    public function confirmReject(): void
    {
        $this->validate(['rejectReason' => 'required|string|min:5|max:500']);

        $user = User::findOrFail($this->rejectingId);
        $this->authorize('reject', $user);

        $user->update([
            'status' => UserStatus::Rejected,
            'rejection_reason' => $this->rejectReason,
        ]);

        $user->notify(new UserApprovalStatusChanged(UserStatus::Rejected));

        $this->rejectingId = null;
        $this->dispatch('toast', message: "{$user->name} rejected.", type: 'warning');
    }

    public function cancelReject(): void
    {
        $this->rejectingId = null;
    }

    public function deactivate(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        $user->update(['status' => UserStatus::Rejected]);
        $this->dispatch('toast', message: "{$user->name} deactivated.", type: 'warning');
    }

    public function reactivate(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->authorize('update', $user);

        $user->update(['status' => UserStatus::Approved]);
        $this->dispatch('toast', message: "{$user->name} reactivated.", type: 'success');
    }

    public function render()
    {
        $query = User::where('id', '!=', auth()->id()) // can't manage self
            ->orderBy('name');

        if ($this->search) {
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            );
        }
        if ($this->filterRole)   $query->where('role', $this->filterRole);
        if ($this->filterStatus) $query->where('status', $this->filterStatus);

        return view('livewire.admin.user-management', [
            'users'    => $query->paginate(20),
            'roles'    => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }
}
