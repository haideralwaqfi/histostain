<?php

namespace App\Livewire\Shared;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationInbox extends Component
{
    use WithPagination;

    public function markRead(string $notificationId): void
    {
        Auth::user()->notifications()->findOrFail($notificationId)->markAsRead();
        $this->dispatch('notification-read');
    }

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        $this->dispatch('notification-read');
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('livewire.shared.notification-inbox', compact('notifications', 'unreadCount'));
    }
}
