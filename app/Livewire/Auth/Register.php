<?php

namespace App\Livewire\Auth;

use App\Enums\UserStatus;
use App\Models\User;
use App\Notifications\NewPendingUserRegistered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;

#[Layout('layouts.guest')]
class Register extends Component
{
    #[Rule('required|string|max:255')]
    public string $name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|min:8|confirmed')]
    public string $password = '';

    #[Rule('required')]
    public string $password_confirmation = '';

    public function register(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'status' => UserStatus::Pending,
        ]);

        $admins = User::admins()->approved()->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewPendingUserRegistered($user));
        }

        Auth::login($user);

        $this->redirect(route('pending'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
