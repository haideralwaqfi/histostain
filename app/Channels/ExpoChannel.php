<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoChannel
{
    private const EXPO_PUSH_URL = 'https://exp.host/--/api/v2/push/send';

    public function send(mixed $notifiable, Notification $notification): void
    {
        $token = $notifiable->expo_push_token ?? null;

        if (! $token || ! str_starts_with($token, 'ExponentPushToken[')) {
            return;
        }

        if (! method_exists($notification, 'toExpo')) {
            return;
        }

        $data = $notification->toExpo($notifiable);

        try {
            $response = Http::post(self::EXPO_PUSH_URL, array_merge($data, ['to' => $token]));

            $body = $response->json();
            $detail = $body['data'][0] ?? null;

            if ($detail && in_array($detail['status'] ?? '', ['DeviceNotRegistered', 'InvalidCredentials'], true)) {
                // Token is dead — null it so we stop sending to it.
                $notifiable->update(['expo_push_token' => null]);
                Log::warning('Expo push token invalidated.', ['user' => $notifiable->id]);
            }
        } catch (\Throwable $e) {
            // Never let a push failure break the request flow.
            Log::warning('Expo push failed.', ['user' => $notifiable->id, 'error' => $e->getMessage()]);
        }
    }
}
