<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserType;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class AccessPermissionController extends Controller
{
    public function index()
    {
        // Get users with user types - fallback to empty collection if no users exist
        $users = User::with('userType')->orderBy('name')->get();
        
        // Get user types (roles) - fallback to empty collection if no roles exist
        $roles = UserType::with('users', 'rolePermissions')->orderBy('name')->get();
        
        // Get role permissions - fallback to empty collection if no permissions exist
        $permissions = RolePermission::with('userType')->orderBy('module')->get();
        
        return view('Admin.access.index', compact('users', 'roles', 'permissions'));
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:user_types,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'user_type_id' => $request->role_id,
            'email_verified_at' => now(),
            'status' => 'active'
        ]);

        $role = UserType::find($request->role_id);

        return response()->json([
            'success' => true,
            'message' => "User '{$user->name}' created with role '{$role->name}'",
            'user' => $user->load('userType')
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'success' => true,
            'message' => "User '{$user->name}' updated successfully",
            'user' => $user->load('userType')
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "User '{$userName}' deleted successfully"
        ]);
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:user_types,name',
            'description' => 'nullable|string'
        ]);

        $role = UserType::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' created successfully",
            'role' => $role
        ]);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:user_types,id'
        ]);

        $user = User::find($request->user_id);
        $role = UserType::find($request->role_id);
        
        $user->user_type_id = $role->id;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' assigned to {$user->name}"
        ]);
    }

    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:user_types,id'
        ]);

        $user = User::find($request->user_id);
        $role = UserType::find($request->role_id);
        
        // Set to default role or null
        $defaultRole = UserType::first();
        $user->user_type_id = $defaultRole ? $defaultRole->id : null;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => "Role '{$role->name}' removed from {$user->name}"
        ]);
    }

    public function updatePermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:user_types,id',
            'module' => 'required|string',
            'action' => 'required|string|in:view,create,edit,delete',
            'checked' => 'required|boolean'
        ]);

        $role = UserType::find($request->role_id);
        
        if ($request->checked) {
            // Add permission
            RolePermission::updateOrCreate(
                [
                    'user_type_id' => $role->id,
                    'module' => $request->module,
                    'ability' => $request->action
                ]
            );
            $action = 'granted';
        } else {
            // Remove permission
            RolePermission::where('user_type_id', $role->id)
                ->where('module', $request->module)
                ->where('ability', $request->action)
                ->delete();
            $action = 'revoked';
        }
        
        // Clear cache for this role to ensure permissions are updated immediately
        $cacheKey = "rp:{$role->id}";
        Cache::forget($cacheKey);
        
        return response()->json([
            'success' => true,
            'message' => "Permission '{$request->action}' for '{$request->module}' {$action} for role '{$role->name}'"
        ]);
    }

    public function getPermissions($roleId)
    {
        $role = UserType::find($roleId);
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }

        // Get default permissions structure
        $defaultPermissions = $this->getDefaultPermissions();

        // Get existing permissions for this role
        $existingPermissions = RolePermission::where('user_type_id', $roleId)
            ->get()
            ->groupBy('module')
            ->map(function ($permissions) {
                return $permissions->pluck('ability')->all();
            })
            ->toArray();

        // Build permissions array with actual saved permissions
        $permissions = [];
        foreach ($defaultPermissions as $default) {
            $module = $default['module'];
            $permissions[] = [
                'module' => $module,
                'view' => in_array('view', $existingPermissions[$module] ?? []),
                'create' => in_array('create', $existingPermissions[$module] ?? []),
                'edit' => in_array('edit', $existingPermissions[$module] ?? []),
                'delete' => in_array('delete', $existingPermissions[$module] ?? [])
            ];
        }

        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    private function getDefaultPermissions() {
        return [
            ['module' => 'dashboard', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'products', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'product_category', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'purchases', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'inventory', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'stock_in', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'stock_transfer', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'sales', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'sales_report', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'refund_return', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'credit', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'expenses', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'customer', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'credit_limits', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'payment_history', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'aging_reports', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'reports', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'roles_permissions', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'user_management', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'access_logs', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'settings', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'branch', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'brands', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'unit_types', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'tax', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false],
            ['module' => 'receipt_templates', 'view' => false, 'create' => false, 'edit' => false, 'delete' => false]
        ];
    }
}
