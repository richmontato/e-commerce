<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotAdmin
{
    /**
     * Block administrators from accessing customer-only flows like cart or checkout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            abort(403, 'Administrators cannot access this section.');
        }

        return $next($request);
    }
}
