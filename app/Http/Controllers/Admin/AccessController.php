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

        $roles = UserType::orderBy('name')->get();

        // Load existing permissions keyed by role and module
        $existing = RolePermission::query()
            ->get()
            ->groupBy(['user_type_id', 'module']);

        return view('Admin.access.index', compact('modules', 'roles', 'existing'));
    }

    /**
     * Persist submitted matrix. Expects payload like abilities[roleId][module] => ability
     */
    public function store(Request $request)
    {
        $data = $request->input('abilities', []);

        // Valid ability values
        $valid = ['none','view','edit','full'];

        DB::transaction(function () use ($data, $valid) {
            $touchedRoleIds = [];
            foreach ($data as $roleId => $perModule) {
                foreach ($perModule as $module => $ability) {
                    $ability = in_array($ability, $valid, true) ? $ability : 'view';
                    RolePermission::updateOrCreate(
                        ['user_type_id' => (int)$roleId, 'module' => (string)$module],
                        ['ability' => $ability]
                    );
                    $touchedRoleIds[(int)$roleId] = true;
                }
            }
            // Invalidate cached role permissions for affected roles
            foreach (array_keys($touchedRoleIds) as $rid) {
                Cache::forget("rp:" . $rid);
            }
        });

        return redirect()->route('admin.access.index')->with('success', 'Access configuration saved.');
    }
}
