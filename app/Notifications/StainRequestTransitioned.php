<?php

namespace App\Notifications;

use App\Channels\ExpoChannel;
use App\Enums\StainRequestStatus;
use App\Models\StainRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class StainRequestTransitioned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly StainRequest $request,
        public readonly StainRequestStatus $from,
        public readonly StainRequestStatus $to,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class, ExpoChannel::class];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'       => 'request_transitioned',
            'title'      => $this->title(),
            'body'       => $this->body(),
            'action_url' => route('doctor.requests.show', $this->request->ulid),
            'request_id' => $this->request->id,
            'ulid'       => $this->request->ulid,
            'from'       => $this->from->value,
            'to'         => $this->to->value,
        ];
    }

    public function toWebPush(object $notifiable, $notification): WebPushMessage
    {
        return WebPushMessage::create()
            ->title($this->title())
            ->body($this->body())
            ->data(['url' => route('doctor.requests.show', $this->request->ulid)])
            ->icon('/icons/icon-192.png');
    }

    public function toExpo(object $notifiable): array
    {
        return [
            'title' => $this->title(),
            'body'  => $this->body(),
            'sound' => 'default',
            'data'  => ['ulid' => $this->request->ulid],
        ];
    }

    private function title(): string
    {
        return "Request {$this->to->label()} — {$this->request->type->shortLabel()}";
    }

    private function body(): string
    {
        return "Case {$this->request->case_number}: status changed from {$this->from->label()} to {$this->to->label()}.";
    }
}
