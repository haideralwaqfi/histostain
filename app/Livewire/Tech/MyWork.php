<?php

namespace App\Livewire\Tech;

use App\Enums\StainRequestStatus;
use App\Models\StainRequest;
use App\Services\StainRequestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyWork extends Component
{
    use WithPagination, WithFileUploads;

    // On-hold modal state
    public ?int $onHoldId = null;
    public string $holdReason = '';
    public string $holdNote = '';

    // Result attachment modal state
    public ?int $attachingToId = null;
    public array $resultFiles = [];

    // ── Status transitions ─────────────────────────────────────

    public function advance(int $requestId, string $status, StainRequestService $service): void
    {
        $request = StainRequest::findOrFail($requestId);
        $this->authorize('transition', $request);

        $service->transition($request, StainRequestStatus::from($status), Auth::user());

        $this->dispatch('toast', message: "Marked as " . StainRequestStatus::from($status)->label() . '.', type: 'success');
    }

    // ── On-hold flow ──────────────────────────────────────────

    public function startOnHold(int $requestId): void
    {
        $this->onHoldId = $requestId;
        $this->holdReason = '';
        $this->holdNote = '';
    }

    public function confirmOnHold(StainRequestService $service): void
    {
        $this->validate([
            'holdReason' => 'required|string|min:5|max:500',
            'holdNote'   => 'nullable|string|max:500',
        ]);

        $request = StainRequest::findOrFail($this->onHoldId);
        $this->authorize('transition', $request);

        $note = $this->holdReason . ($this->holdNote ? "\nAdditional: {$this->holdNote}" : '');
        $service->transition($request, StainRequestStatus::OnHold, Auth::user(), $note);

        $this->onHoldId = null;
        $this->dispatch('toast', message: 'Request placed on hold.', type: 'warning');
    }

    public function cancelOnHold(): void
    {
        $this->onHoldId = null;
    }

    // ── Result attachment flow ────────────────────────────────

    public function startAttach(int $requestId): void
    {
        $this->attachingToId = $requestId;
        $this->resultFiles = [];
    }

    public function saveAttachments(): void
    {
        $this->validate([
            'resultFiles'   => 'required|array|min:1|max:5',
            'resultFiles.*' => 'file|max:20480|mimes:pdf,jpg,jpeg,png,tiff',
        ]);

        $request = StainRequest::findOrFail($this->attachingToId);
        $this->authorize('addResultAttachment', $request);

        foreach ($this->resultFiles as $file) {
            $request->addMedia($file->getRealPath())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('result_attachments');
        }

        $this->attachingToId = null;
        $this->resultFiles = [];
        $this->dispatch('toast', message: 'Result files attached.', type: 'success');
    }

    public function cancelAttach(): void
    {
        $this->attachingToId = null;
        $this->resultFiles = [];
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $requests = StainRequest::where('assigned_tech_id', Auth::id())
            ->whereIn('status', [
                StainRequestStatus::Accepted,
                StainRequestStatus::InProgress,
                StainRequestStatus::OnHold,
            ])
            ->with(['doctor'])
            ->orderByRaw(StainRequest::priorityOrderSql())
            ->orderBy('created_at')
            ->paginate(20);

        return view('livewire.tech.my-work', [
            'requests' => $requests,
        ]);
    }
}
