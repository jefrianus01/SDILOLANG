<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Guru wajib mengganti password awal sebelum memakai aplikasi.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $allowed = [
                'profile.edit',
                'profile.update',
                'profile.photo.destroy',
                'password.update',
                'logout',
            ];

            if (! $request->routeIs($allowed)) {
                return redirect()
                    ->route('profile.edit')
                    ->with('warning', 'Anda wajib mengganti password awal sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
