<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->canAccessAdmin()) {
            abort(403, 'Acceso restringido al área de administración.');
        }

        return $next($request);
    }
}
