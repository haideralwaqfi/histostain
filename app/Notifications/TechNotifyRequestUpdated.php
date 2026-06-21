<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Models\StainRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TechNotifyRequestUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly StainRequest $request) {}

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class, ExpoChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'request_updated',
            'title'      => $this->title(),
            'body'       => $this->body(),
            'action_url' => route('tech.my-work'),
            'request_id' => $this->request->id,
            'ulid'       => $this->request->ulid,
            'priority'   => $this->request->priority->value,
        ];
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return WebPushMessage::create()
            ->title($this->title())
            ->body($this->body())
            ->data(['url' => route('tech.my-work')])
            ->icon('/icons/icon-192.png');
    }

    public function toExpo(object $notifiable): array
    {
        return [
            'title'    => $this->title(),
            'body'     => $this->body(),
            'sound'    => 'default',
            'priority' => 'normal',
            'data'     => ['route' => 'tech.my-work'],
        ];
    }

    private function title(): string
    {
        return "Request Updated — {$this->request->type->shortLabel()}";
    }

    private function body(): string
    {
        return "Case {$this->request->case_number} was updated by Dr. {$this->request->doctor->name}.";
    }
}
