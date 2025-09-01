<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class HasAccessAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        // Verifica se l'utente ha il permesso di accesso admin
        if ($user->can(config('admin.permission.access_admin'))) {
            return $next($request);
        }
        
        // Verifica se l'utente ha uno dei ruoli con accesso completo
        $superAdminRoles = ['CEO', 'CFO', 'CTO', 'SVILUPPO', 'WAR_ROOM'];
        if ($user->hasAnyRole($superAdminRoles)) {
            return $next($request);
        }
        
        // Verifica se l'utente ha il ruolo super-admin
        if ($user->hasRole(config('admin.roles.super_admin'))) {
            return $next($request);
        }

        return abort(403, 'Non hai i permessi per accedere all\'area amministrativa.');
    }
}
