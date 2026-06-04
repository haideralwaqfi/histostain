<?php

namespace App\Livewire\Tech;

use App\Enums\StainRequestStatus;
use App\Models\StainRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class WorkHistory extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = StainRequest::where('assigned_tech_id', Auth::id())
            ->whereIn('status', [StainRequestStatus::Completed, StainRequestStatus::Cancelled])
            ->with(['doctor'])
            ->orderBy('updated_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('case_number', 'like', "%{$this->search}%")
                  ->orWhere('mrn', 'like', "%{$this->search}%")
                  ->orWhere('lab_number', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.tech.work-history', [
            'requests' => $query->paginate(20),
        ]);
    }
}
