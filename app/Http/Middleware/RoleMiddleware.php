<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();
        if (!$user || $user->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Seller harus punya toko terverifikasi, kecuali untuk halaman setup toko
        if ($role === 'seller' && !$request->routeIs('seller.store.*', 'seller.dashboard')) {
            if (!$user->store?->is_verified) {
                return redirect()->route('seller.dashboard')->with('error', 'Toko Anda belum diverifikasi admin.');
            }
        }

        return $next($request);
    }
}
