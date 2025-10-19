<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // Permitir acceso temporal a todos los usuarios autenticados
        if (Auth::check()) {
            return $next($request);
        }

        return redirect('/'); // redirige si no está autenticado
    }
}
