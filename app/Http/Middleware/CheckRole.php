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


    // public function handle(Request $request, Closure $next, ...$roles)
    // {
    //     // Jika login sebagai member, cek di guard 'member'
    //     if (Auth::guard('member')->check()) {
    //         if (!in_array('member', $roles)) {
    //             Log::info('Percobaan akses member ke halaman lain', ['role' => 'member']);
    //             return redirect()->route('member.dashboard')
    //                 ->with('error', 'Anda tidak memiliki akses ke halaman ini!');
    //         }
    //         return $next($request);
    //     }

    //     // Jika login sebagai admin/kasir, cek di guard 'web'
    //     if (Auth::guard('web')->check()) {
    //         $user = Auth::guard('web')->user();
    //         if (!in_array($user->role, $roles)) {
    //             Log::info('Percobaan akses tidak sah', ['role' => $user->role]);
    //             return redirect()->route('dashboard')
    //                 ->with('error', 'Anda tidak memiliki akses ke halaman ini!');
    //         }
    //         return $next($request);
    //     }

    //      Jika tidak ada yang login, arahkan ke halaman login
    //     return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    // }


}
