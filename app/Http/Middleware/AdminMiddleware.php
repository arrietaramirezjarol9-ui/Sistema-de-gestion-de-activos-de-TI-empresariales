<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        if ($request->ajax()) {
            return response()->json(['error' => 'Acceso denegado. Se requieren permisos de administrador.'], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Acceso denegado. Se requieren privilegios de administrador.');
    }
}
