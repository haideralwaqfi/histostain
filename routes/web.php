<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PushSubscriptionController;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\PendingUsersQueue;
use App\Livewire\Admin\RequestBrowser;
use App\Livewire\Admin\RequestDetail as AdminRequestDetail;
use App\Livewire\Admin\StainConfig;
use App\Livewire\Admin\UserManagement;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\PendingGate;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\RejectedGate;
use App\Livewire\Doctor\CreateRequest;
use App\Livewire\Doctor\MyRequests;
use App\Livewire\Doctor\RequestDetail;
use App\Livewire\Shared\NotificationInbox;
use App\Livewire\Shared\Profile;
use App\Livewire\Tech\MyWork;
use App\Livewire\Tech\Queue as TechQueue;
use App\Livewire\Tech\WorkHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Guest routes ───────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// ── Google OAuth (guest only — already-authenticated users skip to dashboard) ──
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// ── Logout ─────────────────────────────────────────────────────
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

// ── Gate screens (auth required, no approval check) ───────────
Route::middleware('auth')->group(function () {
    Route::get('/pending', PendingGate::class)->name('pending');
    Route::get('/rejected', RejectedGate::class)->name('rejected');
});

// ── Web push subscription (auth, no approval gate required) ───
Route::middleware('auth')->group(function () {
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'update'])
        ->name('push-subscriptions.update');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])
        ->name('push-subscriptions.destroy');
});

// ── Root redirect ──────────────────────────────────────────────
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }
    /** @var \App\Models\User $user */
    $user = Auth::user();
    return redirect($user->dashboardRoute());
})->name('home');

// ── Authenticated + approved routes ───────────────────────────
Route::middleware(['auth', 'approved'])->group(function () {

    // Shared
    Route::get('/notifications', NotificationInbox::class)->name('notifications');
    Route::get('/profile', Profile::class)->name('profile');

    // ── Admin ──────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/approvals', PendingUsersQueue::class)->name('approvals');
        Route::get('/requests', RequestBrowser::class)->name('requests');
        Route::get('/requests/{ulid}', AdminRequestDetail::class)->name('requests.show');
        Route::get('/users', UserManagement::class)->name('users');
        Route::get('/config', StainConfig::class)->name('config');
    });

    // ── Doctor ─────────────────────────────────────────────────
    Route::middleware('role:doctor')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/requests', MyRequests::class)->name('requests');
        Route::get('/requests/new', CreateRequest::class)->name('requests.create');
        Route::get('/requests/{ulid}', RequestDetail::class)->name('requests.show');
    });

    // ── Tech ───────────────────────────────────────────────────
    Route::middleware('role:tech')->prefix('tech')->name('tech.')->group(function () {
        Route::get('/queue', TechQueue::class)->name('queue');
        Route::get('/my-work', MyWork::class)->name('my-work');
        Route::get('/history', WorkHistory::class)->name('history');
    });
});
