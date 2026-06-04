<?php

namespace App\Livewire\Tech;

use App\Enums\StainRequestStatus;
use App\Enums\StainRequestType;
use App\Models\StainRequest;
use App\Services\StainRequestService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Queue extends Component
{
    use WithPagination;

    #[Url(as: 'type')]
    public string $filterType = '';

    public function accept(int $requestId, StainRequestService $service): void
    {
        $request = StainRequest::findOrFail($requestId);
        $this->authorize('assign', $request);
        $this->authorize('transition', $request);

        // Assign and accept in one shot
        $request->update(['assigned_tech_id' => Auth::id()]);
        $service->transition($request, StainRequestStatus::Accepted, Auth::user());

        $this->dispatch('toast', message: 'Request accepted — added to your work queue.', type: 'success');
    }

    public function updatedFilterType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StainRequest::where('status', StainRequestStatus::Pending)
            ->with(['doctor'])
            ->orderByRaw(StainRequest::priorityOrderSql())
            ->orderBy('created_at');

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        return view('livewire.tech.queue', [
            'requests'    => $query->paginate(20),
            'typeOptions' => StainRequestType::cases(),
        ]);
    }
}
