<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckCashierType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string ...$types)
    {
        // Pastikan pengguna sudah login dan tipe pengguna sesuai dengan salah satu tipe yang diizinkan
        if (Auth::check() && in_array(Auth::user()->tipe, $types)) {
            return $next($request);
        }

        // Redirect ke halaman lain jika tidak memiliki akses
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

}
