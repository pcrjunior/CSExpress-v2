<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Exemplo: considere que o model User tenha um atributo 'is_admin'
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // Redireciona ou aborta se o usuário não for admin
        return redirect('/')->with('error', 'Você não tem permissão para acessar essa área.');
        // Ou:
        // abort(403, 'Acesso não autorizado.');
    }
}
