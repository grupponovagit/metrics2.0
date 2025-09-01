<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ModuleAccessService
{
    /**
     * Moduli disponibili nel sistema
     */
    const MODULES = [
        'HOME' => 'home',
        'HR' => 'hr', 
        'AMMINISTRAZIONE' => 'amministrazione',
        'PRODUZIONE' => 'produzione',
        'MARKETING' => 'marketing',
        'ICT' => 'ict'
    ];

    /**
     * Ruoli con accesso completo a tutti i moduli
     */
    const FULL_ACCESS_ROLES = [
        'CEO',
        'CFO', 
        'CTO',
        'SVILUPPO',
        'WAR_ROOM'
    ];

    /**
     * Controlla se l'utente corrente ha accesso al modulo
     *
     * @param string $module
     * @return bool
     */
    public static function canAccess(string $module): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Super admin ha sempre accesso
        if ($user->hasRole(config('admin.roles.super_admin'))) {
            return true;
        }

        // Ruoli con accesso completo
        if ($user->hasAnyRole(self::FULL_ACCESS_ROLES)) {
            return true;
        }

        // Controlla il permesso specifico
        $permission = strtolower($module) . '.access';
        return $user->can($permission);
    }

    /**
     * Ottiene tutti i moduli accessibili dall'utente corrente
     *
     * @return array
     */
    public static function getAccessibleModules(): array
    {
        $accessible = [];
        
        foreach (self::MODULES as $name => $key) {
            if (self::canAccess($key)) {
                $accessible[$key] = [
                    'name' => $name,
                    'key' => $key,
                    'url' => route('admin.' . $key . '.index'),
                    'permissions' => self::getModulePermissions($key)
                ];
            }
        }
        
        return $accessible;
    }

    /**
     * Ottiene i permessi dell'utente per un modulo specifico
     *
     * @param string $module
     * @return array
     */
    public static function getModulePermissions(string $module): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        $permissions = [];
        
        if (!$user) {
            return $permissions;
        }

        $modulePermissions = [
            'access' => $module . '.access',
            'view' => $module . '.view', 
            'create' => $module . '.create',
            'edit' => $module . '.edit',
            'delete' => $module . '.delete',
            'reports' => $module . '.reports'
        ];

        foreach ($modulePermissions as $action => $permission) {
            if ($user->can($permission)) {
                $permissions[] = $action;
            }
        }

        return $permissions;
    }

    /**
     * Controlla se l'utente può eseguire un'azione specifica su un modulo
     *
     * @param string $module
     * @param string $action
     * @return bool
     */
    public static function canPerform(string $module, string $action): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Super admin e ruoli con accesso completo possono fare tutto
        if ($user->hasRole(config('admin.roles.super_admin')) || 
            $user->hasAnyRole(self::FULL_ACCESS_ROLES)) {
            return true;
        }

        $permission = strtolower($module) . '.' . strtolower($action);
        return $user->can($permission);
    }

    /**
     * Ottiene la struttura gerarchica dei ruoli
     *
     * @return array
     */
    public static function getRoleHierarchy(): array
    {
        return [
            1 => ['CEO', 'CFO'],
            2 => ['LEGALE'],
            3 => ['CONTABILITÀ'],
            4 => ['TESORERIA'],
            5 => ['AMM_PERSONALE', 'AFFARI_GENERALI'],
            6 => ['CTO', 'CMO', 'COMMERCIALE', 'OPERATION', 'QUALITÀ', 'COGE_REGIA'],
            7 => ['SVILUPPO', 'WAR_ROOM', 'PM_MANDATO', 'HR_SEL_FORM'],
            8 => ['CCM_LAMEZIA', 'CCM_RENDE', 'CCM_VIBO', 'CCM_CASTROVILLARI', 'CCM_CATANZARO', 'CCM_SAN_PIETRO'],
            9 => ['TL_LAMEZIA', 'TL_RENDE', 'TL_VIBO', 'TL_CASTROVILLARI', 'TL_CATANZARO', 'TL_SAN_PIETRO'],
            10 => ['OP_LAMEZIA', 'OP_RENDE', 'OP_VIBO', 'OP_CASTROVILLARI', 'OP_CATANZARO', 'OP_SAN_PIETRO']
        ];
    }
}
