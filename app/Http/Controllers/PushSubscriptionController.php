<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PushSubscriptionController extends Controller
{
    /** Store or update a web-push subscription for the authenticated user. */
    public function update(Request $request): Response
    {
        $data = $request->validate([
            'endpoint'        => 'required|url',
            'keys.auth'       => 'required|string',
            'keys.p256dh'     => 'required|string',
            'contentEncoding' => 'nullable|string',
        ]);

        $request->user()->updatePushSubscription(
            endpoint:        $data['endpoint'],
            key:             $data['keys']['p256dh'],
            token:           $data['keys']['auth'],
            contentEncoding: $data['contentEncoding'] ?? 'aesgcm',
        );

        return response()->noContent();
    }

    /** Remove the subscription — called when the user disables push in the browser. */
    public function destroy(Request $request): Response
    {
        $request->validate(['endpoint' => 'required|string']);

        $request->user()->deletePushSubscription($request->input('endpoint'));

        return response()->noContent();
    }
}
