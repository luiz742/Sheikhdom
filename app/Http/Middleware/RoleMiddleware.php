<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    protected $roles = [
        'admin' => 1,
        'manager' => 2,
        'user' => 0, // 0 para usuário normal
    ];

    public function handle(Request $request, Closure $next, string $role = 'user'): Response
    {
        // Obtém o valor do papel necessário
        $requiredRole = $this->roles[$role] ?? null;

        // Verifica se o usuário está autenticado e tem o papel necessário
        if (auth()->check() && auth()->user()->role === $requiredRole) {
            return $next($request);
        }

        // Bloqueia acesso caso o papel não seja compatível
        abort(403, 'Access denied');
    }
}
