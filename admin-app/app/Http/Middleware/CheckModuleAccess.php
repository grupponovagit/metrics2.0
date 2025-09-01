<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckModuleAccess
{
    /**
     * Controlla se l'utente ha accesso al modulo richiesto.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $module
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $module)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super admin ha sempre accesso
        if ($user->hasRole(config('admin.roles.super_admin'))) {
            return $next($request);
        }

        // Ruoli con accesso completo
        $fullAccessRoles = ['CEO', 'CFO', 'CTO', 'SVILUPPO', 'WAR_ROOM'];
        if ($user->hasAnyRole($fullAccessRoles)) {
            return $next($request);
        }

        // Controlla il permesso specifico per il modulo
        $permission = strtolower($module) . '.access';
        
        if (!$user->can($permission)) {
            abort(403, "Non hai i permessi per accedere al modulo {$module}.");
        }

        return $next($request);
    }
}
