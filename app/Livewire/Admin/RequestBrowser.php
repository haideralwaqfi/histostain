<?php

namespace App\Livewire\Admin;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestStatus;
use App\Enums\StainRequestType;
use App\Enums\UserRole;
use App\Models\StainRequest;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class RequestBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $filterStatus = '';

    #[Url(as: 'priority')]
    public string $filterPriority = '';

    #[Url(as: 'type')]
    public string $filterType = '';

    #[Url(as: 'doctor')]
    public int|string $filterDoctor = '';

    #[Url(as: 'from')]
    public string $dateFrom = '';

    #[Url(as: 'to')]
    public string $dateTo = '';

    public bool $showFilters = false;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedFilterPriority(): void { $this->resetPage(); }
    public function updatedFilterType(): void { $this->resetPage(); }
    public function updatedFilterDoctor(): void { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = $this->filterStatus = $this->filterPriority = '';
        $this->filterType = $this->filterDoctor = $this->dateFrom = $this->dateTo = '';
        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->search || $this->filterStatus || $this->filterPriority
            || $this->filterType || $this->filterDoctor || $this->dateFrom || $this->dateTo;
    }

    public function render()
    {
        $query = StainRequest::with(['doctor', 'assignedTech'])
            ->orderByRaw(StainRequest::priorityOrderSql())
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(fn($q) => $q
                ->where('case_number', 'like', "%{$this->search}%")
                ->orWhere('mrn', 'like', "%{$this->search}%")
                ->orWhere('lab_number', 'like', "%{$this->search}%")
            );
        }
        if ($this->filterStatus)   $query->where('status', $this->filterStatus);
        if ($this->filterPriority) $query->where('priority', $this->filterPriority);
        if ($this->filterType)     $query->where('type', $this->filterType);
        if ($this->filterDoctor)   $query->where('doctor_id', $this->filterDoctor);
        if ($this->dateFrom)       $query->whereDate('created_at', '>=', $this->dateFrom);
        if ($this->dateTo)         $query->whereDate('created_at', '<=', $this->dateTo);

        return view('livewire.admin.request-browser', [
            'requests'   => $query->paginate(20),
            'statuses'   => StainRequestStatus::cases(),
            'priorities' => StainRequestPriority::cases(),
            'types'      => StainRequestType::cases(),
            'doctors'    => User::where('role', UserRole::Doctor)->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
