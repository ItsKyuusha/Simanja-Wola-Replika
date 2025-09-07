<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        // Jika belum login
        if (!$user) {
            // Bisa redirect ke login atau kembalikan JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }

        $userRole = strtolower($user->role);
        $allowedRoles = array_map('strtolower', $roles);

        // Jika role tidak sesuai
        if (!in_array($userRole, $allowedRoles)) {
            // Bisa redirect ke halaman dashboard sesuai role
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access'], 403);
            }

            // Redirect default
            return redirect()->route('unauthorized'); // buat route /unauthorized
        }

        return $next($request);
    }
}
