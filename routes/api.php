<?php

use App\Http\Controllers\Api\ExpoPushTokenController;
use Illuminate\Support\Facades\Route;

/*
 * Authenticated API routes (session-cookie auth, same origin).
 * These are consumed by the Expo mobile app via WebView or native fetch.
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/expo-push-token', [ExpoPushTokenController::class, 'store'])
        ->name('api.expo-push-token.store');
    Route::delete('/expo-push-token', [ExpoPushTokenController::class, 'destroy'])
        ->name('api.expo-push-token.destroy');
});
