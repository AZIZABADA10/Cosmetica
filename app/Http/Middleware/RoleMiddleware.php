<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Non authentifié.'
            ], 401);
        }

        $userRole = $request->user()->role->name;

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Rôle insuffisant.'
            ], 403);
        }

        return $next($request);
    }
}
