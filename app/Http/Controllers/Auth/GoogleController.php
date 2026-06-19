<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewPendingUserRegistered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check for existing account by email first so a user who registered
        // with email/password can link their Google account seamlessly.
        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'status' => UserStatus::Pending,
            ]);

            $admins = User::admins()->approved()->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewPendingUserRegistered($user));
            }
        }

        Auth::login($user, remember: true);

        return redirect($user->dashboardRoute());
    }
}
