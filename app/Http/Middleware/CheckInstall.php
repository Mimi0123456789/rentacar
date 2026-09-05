<?php

namespace App\Http\Middleware;

use Closure;

class CheckInstall
{
    public function handle($request, Closure $next)
    {
        $installed = file_exists(storage_path('installed.lock'));

        // Si installé -> interdit d'aller sur install
        if ($installed && $request->routeIs('install.*')) {
            return redirect('/login');
        }

        // Si pas installé -> tout redirige vers install sauf install.*
        if (!$installed && !$request->routeIs('install.*')) {
            return redirect()->route('install.index');
        }


        return $next($request);
    }

}
