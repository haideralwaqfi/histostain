<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpoPushTokenController extends Controller
{
    /** Register or update the Expo push token for the authenticated user. */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'regex:/^ExponentPushToken\[.+\]$/'],
        ]);

        $request->user()->update(['expo_push_token' => $request->input('token')]);

        return response()->json(['ok' => true]);
    }

    /** Remove the Expo push token (called when the user logs out of the mobile app). */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->update(['expo_push_token' => null]);

        return response()->json(['ok' => true]);
    }
}
