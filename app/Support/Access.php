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
    /** @var array<string,int> */
    protected static array $rank = [
        'none' => 0,
        'view' => 1,
        'edit' => 2,
        'full' => 3,
    ];

    public static function ability(?User $user, string $module): string
    {
        if (!$user || !$user->user_type_id) {
            return 'none';
        }
        // Super roles: grant full access across all modules
        $super = (array) config('rbac.super_roles', []);
        $roleName = optional($user->userType)->name;
        if ($roleName && in_array($roleName, $super, true)) {
            return 'full';
        }
        $roleId = (int) $user->user_type_id;
        $cacheKey = "rp:{$roleId}";

        // Cache role permissions map: module => ability
        $map = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($roleId) {
            return RolePermission::where('user_type_id', $roleId)
                ->get()
                ->pluck('ability', 'module')
                ->toArray();
        });

        return $map[$module] ?? 'none'; // default to none if not configured
    }

    public static function can(?User $user, string $module, string $required): bool
    {
        $ability = self::ability($user, $module);
        $a = self::$rank[$ability] ?? 0;
        $r = self::$rank[$required] ?? 0;
        return $a >= $r;
    }
}
