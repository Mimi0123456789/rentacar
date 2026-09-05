<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Vérifie que l'utilisateur possède l'un des rôles autorisés.
     */
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->droit, $roles, true)) {
            abort(403, 'Vous n’êtes pas autorisé à accéder à cette page.');
        }

        return $next($request);
    }
}