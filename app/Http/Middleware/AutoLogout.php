<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $maxIdleTime = 15 * 60; 

            if ($user->last_activity && (time() - $user->last_activity->getTimestamp() > $maxIdleTime)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('info', 'Zostałeś wylogowany z powodu bezczynności.');
            }

            $user->update(['last_activity' => now()]);
        }

        return $next($request);
    }
}