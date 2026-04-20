<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsLecteur
{
    /**
     * Vérifie que l'utilisateur est connecté (tout rôle accepté).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            abort(403, 'Vous devez être connecté.');
        }

        return $next($request);
    }
}
