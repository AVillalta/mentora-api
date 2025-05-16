<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class MultiRoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user || !$user->hasAnyRole($roles)) {
            throw new UnauthorizedException(403, 'User does not have the right roles.');
        }
        return $next($request);
    }
}