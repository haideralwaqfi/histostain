<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return match($user->status) {
            UserStatus::Pending => redirect()->route('pending'),
            UserStatus::Rejected => redirect()->route('rejected'),
            default => $next($request),
        };
    }
}
