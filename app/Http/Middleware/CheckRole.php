<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{

    public function handle(Request $request, Closure $next, ...$roles)
    {

        if (!Auth::check()) {
            return redirect('login');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            Log::info('Percobaan akses dengan peran tidak sah', ['role' => Auth::user()->role]);
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
