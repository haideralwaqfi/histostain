<?php

namespace App\Livewire\Admin;

use App\Enums\StainRequestStatus;
use App\Enums\UserRole;
use App\Models\StainRequest;
use App\Models\User;
use App\Services\StainRequestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RequestDetail extends Component
{
    public string $ulid;
    public int|string $selectedTechId = '';
    public bool $confirmingCancel = false;

    public function mount(string $ulid): void
    {
        $this->ulid = $ulid;
    }

    public function assignTech(StainRequestService $service): void
    {
        $this->validate(['selectedTechId' => 'required|exists:users,id']);

        $request = StainRequest::where('ulid', $this->ulid)->firstOrFail();
        $this->authorize('assign', $request);

        $tech = User::findOrFail($this->selectedTechId);
        $service->assign($request, $tech, Auth::user());

        $this->dispatch('toast', message: "Assigned to {$tech->name}.", type: 'success');
    }

    public function startCancel(): void
    {
        $this->confirmingCancel = true;
    }

    public function cancelRequest(StainRequestService $service): void
    {
        $request = StainRequest::where('ulid', $this->ulid)->firstOrFail();
        $this->authorize('cancel', $request);

        $service->transition($request, StainRequestStatus::Cancelled, Auth::user(), 'Cancelled by admin.');

        $this->confirmingCancel = false;
        $this->dispatch('toast', message: 'Request cancelled.', type: 'warning');
    }

    public function dismissCancel(): void
    {
        $this->confirmingCancel = false;
    }

    public function render()
    {
        $request = StainRequest::where('ulid', $this->ulid)
            ->with(['doctor', 'assignedTech', 'transitions.performedBy', 'media'])
            ->firstOrFail();

        $techs = User::where('role', UserRole::Tech)
            ->where('status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.admin.request-detail', compact('request', 'techs'));
    }
}
