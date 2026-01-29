<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RolePermission;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            ->groupBy('user_type_id')
            ->map(function ($roles) {
                return $roles->groupBy('module')->map(function ($modules) {
                    return $modules->pluck('ability')->all();
                });
            });

        $allRoles = UserType::with('parent')->get();
        $permissions = $this->resolvePermissions($allRoles, $existing);

        // Get users with their user types for the dashboard
        $users = \App\Models\User::with('userType')->orderBy('name')->get();

        return view('Admin.access.index', compact('modules', 'roles', 'permissions', 'users'));
    }

    /**
     * Persist submitted matrix. Expects payload like abilities[roleId][module] => ability
     */
    public function store(Request $request)
    {
        $abilities = $request->input('abilities', []);

        DB::transaction(function () use ($abilities) {
            // Prevent changes to the Admin role (ID 1)
            if (isset($abilities[1])) {
                unset($abilities[1]);
            }
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

        public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:user_types,name',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        UserType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true, 'message' => 'New role created successfully.']);
    }

    public function updateRole(Request $request, $role)
    {
        $role = UserType::find($role);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found.'], 404);
        }

        $data = $request->all();
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255|unique:user_types,name,' . $role->id,
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $role->update($data);

        return response()->json(['success' => true, 'message' => 'Role updated successfully.']);
    }

    public function destroyRole(UserType $role)
    {
        // Add any checks here to prevent deletion of critical roles, e.g., Admin
        if ($role->id === 1) { // Assuming 1 is the ID for the Admin role
            return redirect()->route('admin.access.index')->with('error', 'Cannot delete the default Admin role.');
        }

        $role->delete();

        return redirect()->route('admin.access.index')->with('success', 'Role deleted successfully.');
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

    public function getUser($id)
    {
        try {
            $user = User::with('userType')->find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'nullable|exists:user_types,id'
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

        // Update role if provided
        if ($request->has('role_id')) {
            $user->user_type_id = $request->role_id;
            $user->save();
        }

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
        
        // Prevent deletion of admin user (ID 1)
        if ($user->id === 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete the admin user'
            ], 403);
        }
        
        $userName = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "User '{$userName}' deleted successfully"
        ]);
    }

    public function accessLogs()
    {
        // Get users with their user types, ordered by last activity
        $users = \App\Models\User::with('userType')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
            
        return view('Admin.access.logs_fullscreen', compact('users'));
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
