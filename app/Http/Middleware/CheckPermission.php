<?php
// File: app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckPermission
{
    public function handle($request, Closure $next, $permissionId)
    {
        $user = Auth::user();

        // Pastikan user sudah login
        if (!$user) {
            return redirect('/login');
        }

        // Ambil role_id user
        $roleId = $user->role; // Asumsikan kolom `role` menyimpan role_id

        // Cek apakah role_id memiliki izin berdasarkan permission_id
        $hasPermission = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$hasPermission) {
            abort(403, 'You do not have access to this page.');
        }

        return $next($request);
    }
}
