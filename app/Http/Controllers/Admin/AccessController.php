<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AccessController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display role-based access matrix for all modules.
     * Admin can set per-role ability: none, view, edit, full.
     */
    public function index()
    {
        // Define manageable modules via config
        $modules = config('rbac.modules', []);

        $roles = UserType::whereNull('parent_id')->with('children')->orderBy('name')->get();

        // Load existing permissions keyed by role and module
        $existing = RolePermission::query()
            ->get()
            ->groupBy('user_type_id')
            ->map(function ($roles) {
                return $roles->groupBy('module')->map(function ($modules) {
                    return $modules->pluck('ability')->all();
                });
            });

        $allRoles = UserType::with('parent')->get();
        $permissions = $this->resolvePermissions($allRoles, $existing);

        return view('Admin.access.index', compact('modules', 'roles', 'permissions'));
    }

    /**
     * Persist submitted matrix. Expects payload like abilities[roleId][module] => ability
     */
    public function store(Request $request)
    {
        $abilities = $request->input('abilities', []);

        DB::transaction(function () use ($abilities) {
            $touchedRoleIds = [];
            foreach ($abilities as $roleId => $modules) {
                foreach ($modules as $moduleKey => $permissions) {
                    foreach ($permissions as $permission => $value) {
                        RolePermission::updateOrCreate(
                            ['user_type_id' => (int)$roleId, 'module' => $moduleKey, 'ability' => $permission],
                            ['ability' => $permission] // Simplified: presence means allowed
                        );
                    }
                }
                $touchedRoleIds[(int)$roleId] = true;
            }

            // Invalidate cached role permissions for affected roles
            foreach (array_keys($touchedRoleIds) as $rid) {
                Cache::forget("rp:" . $rid);
            }
        });

        return redirect()->route('admin.access.index')->with('success', 'Access configuration saved.');
    }

    private function resolvePermissions($allRoles, $existing)
    {
        $resolved = [];
        foreach ($allRoles as $role) {
            $resolved[$role->id] = $this->getInheritedPermissions($role, $existing, $allRoles);
        }
        return $resolved;
    }

    private function getInheritedPermissions($role, $existing, $allRoles)
    {
        $permissions = $existing->get($role->id, collect())->toArray();

        if ($role->parent) {
            $parentPermissions = $this->getInheritedPermissions($role->parent, $existing, $allRoles);
            $permissions = array_replace_recursive($parentPermissions, $permissions);
        }

        return $permissions;
    }
}
