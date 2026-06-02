<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Buộc request API luôn được xử lý như JSON (validation, lỗi auth, v.v.).
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->routeIs('api.documents')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
