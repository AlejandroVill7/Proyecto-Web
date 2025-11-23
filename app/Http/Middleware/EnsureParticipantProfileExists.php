<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParticipantProfileExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('Participante')) {

            $perfil = $user->participante;

            if (! $perfil || empty($perfil->telefono)) {

                if (! $request->routeIs('participante.registro.*') && ! $request->routeIs('logout')) {
                    return redirect()->route('participante.registro.inicial'); 
                }
            }
        }

        return $next($request);
    }
}
