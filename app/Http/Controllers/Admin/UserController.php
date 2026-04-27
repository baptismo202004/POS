<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\Branch;
use App\Models\User;
use App\Models\UserType;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            $roleName = optional(optional($user)->userType)->name ?? '';
            if (! in_array($roleName, config('rbac.super_roles', []))) {
                abort(403, 'Only Superadmin can access this section.');
            }

            return $next($request);
        });
    }

    public function create()
    {
        $userTypes = UserType::orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('branch_name')->get();

        return view('Admin.users.create', compact('userTypes', 'branches'));
    }

    public function show(User $user)
    {
        // Load user with relationships
        $user->load(['branch', 'userType', 'sales']);

        return view('Admin.users.show', compact('user'));
    }

    public function store(StoreUserRequest $request): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        // Handle profile picture upload if present
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        // Generate employee_id if not provided
        if (empty($data['employee_id'])) {
            $latestEmployee = User::orderBy('employee_id', 'desc')->first();
            $latestNumber = $latestEmployee ? (int) str_replace('EMP', '', $latestEmployee->employee_id) : 0;
            $data['employee_id'] = 'EMP'.str_pad($latestNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        // Password will be hashed automatically via cast in User model (password => 'hashed')
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'employee_id' => $data['employee_id'],
            'user_type_id' => $data['user_type_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'status' => $data['status'] ?? 'active',
            'profile_picture' => $data['profile_picture'] ?? null,
        ]);

        // Sync multi-branch assignments for Admin role
        if (! empty($data['branch_ids'])) {
            $user->branches()->sync($data['branch_ids']);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => "User '{$user->name}' created successfully."]);
        }

        return redirect()->route('dashboard')->with('success', 'User account created successfully.');
    }
}
