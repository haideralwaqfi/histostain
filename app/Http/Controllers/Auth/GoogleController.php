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
        // Google returns ?error=access_denied (or similar) when the user cancels
        // or when the redirect URI is misconfigured — handle it before Socialite
        // tries to exchange a missing code and throws a cryptic 400 exception.
        if (request()->has('error')) {
            return redirect()->route('login')
                ->with('google_error', 'Google sign-in was cancelled or failed. Please try again.');
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception) {
            return redirect()->route('login')
                ->with('google_error', 'Could not authenticate with Google. Please try again.');
        }

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
        session()->regenerate();

        return redirect($user->dashboardRoute());
    }
}
