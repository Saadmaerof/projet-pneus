<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
{
    // On vérifie d'abord si l'utilisateur est bien connecté (non null) ET s'il est Admin
    if (Auth::check() && Auth::user()->role === 'Admin') {
        return $next($request);
    }

    // Retourne une erreur 403 propre si l'utilisateur n'est pas Admin ou pas connecté
    return response()->json(['message' => 'Unauthorized'], 403);
}
}
