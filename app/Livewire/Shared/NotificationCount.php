<?php

namespace App\Livewire\Shared;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationCount extends Component
{
    // Re-renders when the inbox marks notifications as read
    #[On('notification-read')]
    public function refresh(): void {}

    // Also refreshed via a browser event dispatched from the service worker
    // when a push notification arrives (see sw.js → postMessage 'push-received').
    // Registered in the view via x-on:push-received.window.
    #[On('push-received')]
    public function onPushReceived(): void {}

    public function render()
    {
        $count = Auth::check() ? Auth::user()->unreadNotifications()->count() : 0;
        return view('livewire.shared.notification-count', compact('count'));
    }
}
