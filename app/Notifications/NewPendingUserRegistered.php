<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewPendingUserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly User $newUser) {}

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class, ExpoChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'new_pending_user',
            'title' => 'New Registration Pending',
            'body' => "{$this->newUser->name} has registered and is awaiting approval.",
            'action_url' => route('admin.approvals'),
            'user_id' => $this->newUser->id,
        ];
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return WebPushMessage::create()
            ->title('New Registration')
            ->body("{$this->newUser->name} is awaiting approval.")
            ->data(['url' => route('admin.approvals')])
            ->badge('/icons/badge-72.png')
            ->icon('/icons/icon-192.png');
    }

    public function toExpo(object $notifiable): array
    {
        return [
            'title' => 'New Registration',
            'body' => "{$this->newUser->name} is awaiting approval.",
            'data' => ['route' => 'admin.approvals'],
            'sound' => 'default',
        ];
    }
}
