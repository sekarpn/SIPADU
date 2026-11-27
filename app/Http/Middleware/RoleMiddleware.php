<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        $userRole = strtolower(trim((string) $request->user()->role));
        $allowedRoles = array_map(static fn (string $role): string => strtolower(trim($role)), $roles);

        abort_unless(
            in_array($userRole, $allowedRoles, true),
            403,
            'Anda tidak memiliki akses ke halaman ini.'
        );

        return $next($request);
    }
}