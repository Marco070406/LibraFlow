<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsBibliothecaire
{
    /**
     * Vérifie que l'utilisateur a le rôle bibliothecaire OU admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['bibliothecaire', 'admin'])) {
            abort(403, 'Accès réservé aux bibliothécaires.');
        }

        return $next($request);
    }
}
