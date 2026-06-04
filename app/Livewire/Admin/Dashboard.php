<?php

namespace App\Livewire\Admin;

use App\Enums\StainRequestPriority;
use App\Enums\StainRequestStatus;
use App\Enums\UserStatus;
use App\Models\StainRequest;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    // Polled every 30 seconds for live counts
    public function render()
    {
        $stats = [
            'stat_active'      => StainRequest::where('priority', StainRequestPriority::Stat)
                                     ->whereNotIn('status', [StainRequestStatus::Completed, StainRequestStatus::Cancelled])
                                     ->count(),
            'pending'          => StainRequest::where('status', StainRequestStatus::Pending)->count(),
            'in_progress'      => StainRequest::whereIn('status', [StainRequestStatus::Accepted, StainRequestStatus::InProgress])->count(),
            'on_hold'          => StainRequest::where('status', StainRequestStatus::OnHold)->count(),
            'completed_today'  => StainRequest::where('status', StainRequestStatus::Completed)
                                     ->whereDate('updated_at', today())
                                     ->count(),
            'pending_users'    => User::where('status', UserStatus::Pending)->count(),
        ];

        $recentRequests = StainRequest::with('doctor')
            ->orderByRaw(StainRequest::priorityOrderSql())
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('livewire.admin.dashboard', compact('stats', 'recentRequests'));
    }
}
