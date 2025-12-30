<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->profiles()->count() === 0) {
            // Exclure les routes de création de profil et les routes admin
            if (!$request->routeIs('profiles.*') && !$request->routeIs('profile.*') && !$request->routeIs('logout') && !$request->routeIs('admin.*')) {
                return redirect()->route('profiles.create')
                    ->with('info', 'Veuillez créer un profil pour continuer.');
            }
        }

        return $next($request);
    }
}
