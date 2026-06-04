<?php

namespace App\Providers;

use App\Channels\ExpoChannel;
use App\Models\StainRequest;
use App\Models\User;
use App\Policies\StainRequestPolicy;
use App\Policies\UserPolicy;
use App\StainTypes\StainTypeRegistry;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Expo channel so notification classes can route to it by class name
        $this->app->singleton(ExpoChannel::class);
    }

    public function boot(): void
    {
        // Boot the stain-type registry once at application start
        StainTypeRegistry::boot();

        // Register policies explicitly (auto-discovery would work too, but explicit is clear)
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(StainRequest::class, StainRequestPolicy::class);
    }
}
