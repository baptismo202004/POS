<?php

namespace App\Support;

use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Central helper to evaluate role/module abilities.
 * Abilities order: none < view < edit < full
 * Usage:
 *   Access::can($user, 'purchases', 'edit')
 *   Access::ability($user, 'products') // returns one of none|view|edit|full
 */
class Access
{
    /**
     * Get all permissions for a user's role, keyed by module.
     * The result is cached.
     *
     * @param User|null $user
     * @return array<string, array<string>>
     */
    protected static function getPermissions(?User $user): array
    {
        if (!$user || !$user->user_type_id) {
            return [];
        }

        // Super roles: grant full access across all modules
        $super = (array) config('rbac.super_roles', []);
        $roleName = optional($user->userType)->name;
        if ($roleName && in_array($roleName, $super, true)) {
            $modules = config('rbac.modules', []);
            $allPermissions = [];
            foreach ($modules as $moduleKey => $moduleData) {
                $allPermissions[$moduleKey] = $moduleData['permissions'] ?? [];
            }
            return $allPermissions;
        }

        $roleId = (int) $user->user_type_id;
        $cacheKey = "rp:{$roleId}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($roleId) {
            return RolePermission::where('user_type_id', $roleId)
                ->get()
                ->groupBy('module')
                ->map(function ($permissions) {
                    return $permissions->pluck('ability')->all();
                })
                ->toArray();
        });
    }

    /**
     * Check if a user has a specific permission for a module.
     *
     * @param User|null $user
     * @param string $module
     * @param string $required
     * @return bool
     */
    public static function can(?User $user, string $module, string $required): bool
    {
        $permissions = self::getPermissions($user);

        return isset($permissions[$module]) && in_array($required, $permissions[$module]);
    }
}
