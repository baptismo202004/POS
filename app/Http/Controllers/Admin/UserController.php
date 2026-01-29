<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Models\User;
use App\Models\UserType;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function create()
    {
        $userTypes = UserType::orderBy('name')->get();
        $branches = Branch::where('status', 'active')->orderBy('branch_name')->get();
        return view('Admin.users.create', compact('userTypes', 'branches'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Handle profile picture upload if present
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        // Password will be hashed automatically via cast in User model (password => 'hashed')
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'user_type_id' => $data['user_type_id'],
            'branch_id' => $data['branch_id'] ?? null,
            'status' => $data['status'] ?? 'active',
            'profile_picture' => $data['profile_picture'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'User account created successfully.');
    }
}
