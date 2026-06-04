<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Enums\UserStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class UserApprovalStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly UserStatus $newStatus) {}

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class, ExpoChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return match($this->newStatus) {
            UserStatus::Approved => [
                'type' => 'account_approved',
                'title' => 'Account Approved',
                'body' => 'Your HistoStains account has been approved. You can now log in.',
                'action_url' => route('home'),
            ],
            UserStatus::Rejected => [
                'type' => 'account_rejected',
                'title' => 'Registration Not Approved',
                'body' => 'Your HistoStains registration was not approved.',
                'action_url' => route('rejected'),
            ],
            default => [],
        };
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $isApproved = $this->newStatus === UserStatus::Approved;

        return WebPushMessage::create()
            ->title($isApproved ? 'Account Approved' : 'Registration Not Approved')
            ->body($isApproved
                ? 'Your HistoStains account is ready.'
                : 'Your registration was not approved. See the app for details.')
            ->icon('/icons/icon-192.png');
    }

    public function toExpo(object $notifiable): array
    {
        $isApproved = $this->newStatus === UserStatus::Approved;

        return [
            'title' => $isApproved ? 'Account Approved' : 'Registration Not Approved',
            'body' => $isApproved
                ? 'Your HistoStains account is ready.'
                : 'Your registration was not approved.',
            'sound' => 'default',
        ];
    }
}
