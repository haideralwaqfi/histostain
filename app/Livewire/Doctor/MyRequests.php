<?php

namespace App\Livewire\Doctor;

use App\Enums\StainRequestStatus;
use App\Models\StainRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MyRequests extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $filterStatus = '';

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StainRequest::where('doctor_id', Auth::id())
            ->with(['assignedTech'])
            ->orderByRaw(StainRequest::priorityOrderSql())
            ->orderBy('created_at', 'desc');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.doctor.my-requests', [
            'requests'  => $query->paginate(15),
            'statuses'  => StainRequestStatus::cases(),
        ]);
    }
}
