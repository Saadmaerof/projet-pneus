<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isTechnicien
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
  public function handle(Request $request, Closure $next): Response
{
    // On vérifie d'ici que l'utilisateur existe ET qu'il est Technicien
    if (Auth::check() && Auth::user()->role === 'Technicien') {
        return $next($request);
    }

    return response()->json(['message' => 'Unauthorized'], 403);
}
}
