<?php

namespace App\Livewire\Doctor;

use App\Enums\StainRequestStatus;
use App\Models\StainRequest;
use App\Services\StainRequestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RequestDetail extends Component
{
    public string $ulid;
    public bool $confirmingCancel = false;

    public function mount(string $ulid): void
    {
        $this->ulid = $ulid;
    }

    public function startCancel(): void
    {
        $this->confirmingCancel = true;
    }

    public function cancelRequest(StainRequestService $service): void
    {
        $request = StainRequest::where('ulid', $this->ulid)->firstOrFail();
        $this->authorize('cancel', $request);

        $service->transition($request, StainRequestStatus::Cancelled, Auth::user(), 'Cancelled by doctor.');

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

        $this->authorize('view', $request);

        return view('livewire.doctor.request-detail', [
            'request'     => $request,
            'transitions' => $request->transitions,
        ]);
    }
}
