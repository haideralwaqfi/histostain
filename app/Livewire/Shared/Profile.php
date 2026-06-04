<?php

namespace App\Livewire\Shared;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public string $name = '';
    public string $email = '';

    // Password change (only for non-Google accounts)
    public string $currentPassword = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

    public bool $passwordSaved = false;
    public bool $profileSaved = false;

    public function mount(): void
    {
        $this->name  = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function saveProfile(): void
    {
        $user = Auth::user();

        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->profileSaved = true;
        $this->dispatch('toast', message: 'Profile updated.', type: 'success');
    }

    public function savePassword(): void
    {
        $user = Auth::user();

        // Google-only users have no password to update
        if (! $user->password) {
            $this->addError('currentPassword', 'Your account uses Google sign-in — no password to change.');
            return;
        }

        $this->validate([
            'currentPassword'        => 'required',
            'newPassword'            => 'required|min:8|confirmed',
            'newPasswordConfirmation' => 'required',
        ]);

        if (! Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->newPassword)]);

        $this->currentPassword = $this->newPassword = $this->newPasswordConfirmation = '';
        $this->passwordSaved = true;
        $this->dispatch('toast', message: 'Password changed.', type: 'success');
    }

    /** Called from JavaScript after a successful push (un)subscribe. */
    public function pushStatusChanged(bool $subscribed): void
    {
        $msg = $subscribed ? 'Push notifications enabled.' : 'Push notifications disabled.';
        $type = $subscribed ? 'success' : 'info';
        $this->dispatch('toast', message: $msg, type: $type);
    }

    public function render()
    {
        return view('livewire.shared.profile', [
            'user'          => Auth::user(),
            'vapidPublicKey' => config('webpush.vapid.public_key', ''),
        ]);
    }
}
