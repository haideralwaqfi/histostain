<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Enums\StainRequestPriority;
use App\Models\StainRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TechNotifyNewRequest extends Notification implements ShouldQueue
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
            'type'       => 'new_request',
            'title'      => $this->title(),
            'body'       => $this->body(),
            'action_url' => route('tech.queue'),
            'request_id' => $this->request->id,
            'ulid'       => $this->request->ulid,
            'priority'   => $this->request->priority->value,
        ];
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        $message = WebPushMessage::create()
            ->title($this->title())
            ->body($this->body())
            ->data(['url' => route('tech.queue')])
            ->icon('/icons/icon-192.png');

        // STAT requests get an urgent tag so the browser treats them as high-priority
        if ($this->request->priority === StainRequestPriority::Stat) {
            $message->urgency('high');
        }

        return $message;
    }

    public function toExpo(object $notifiable): array
    {
        $isStat = $this->request->priority === StainRequestPriority::Stat;

        return [
            'title'    => $this->title(),
            'body'     => $this->body(),
            'sound'    => 'default',
            'priority' => $isStat ? 'high' : 'normal',
            'data'     => ['route' => 'tech.queue'],
        ];
    }

    private function title(): string
    {
        $prefix = $this->request->priority === StainRequestPriority::Stat ? '🚨 STAT — ' : '';
        return "{$prefix}New {$this->request->type->shortLabel()} Request";
    }

    private function body(): string
    {
        return "Case {$this->request->case_number} · {$this->request->priority->label()} · from Dr. {$this->request->doctor->name}";
    }
}
