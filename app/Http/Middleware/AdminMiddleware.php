<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
         if (!$request->user()) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

         if ($request->user()->role->name !== 'admin') {
            return response()->json([
                'message' => 'Accès refusé (admin seulement)'
            ], 403);
        }

        return $next($request);
    }
}