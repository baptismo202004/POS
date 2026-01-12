<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('assignedUser')->latest()->get();
        $users = User::all();
        return view('SuperAdmin.branches.index', compact('branches', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|unique:branches,branch_name',
            'address' => 'nullable|string',
            'assign_to' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive'
        ]);

        Branch::create([
            'branch_name' => $request->branch_name,
            'address' => $request->address,
            'assign_to' => $request->assign_to,
            'status' => $request->status
        ]);

        return back()->with('success', 'Branch added successfully');
    }

    public function create()
    {
        return view('SuperAdmin.branches.index');
    }

    public function edit(Branch $branch)
    {
        return view('SuperAdmin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'branch_name' => 'required|unique:branches,branch_name,' . $branch->id,
            'address' => 'nullable|string',
            'assign_to' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive'
        ]);

        $branch->update([
            'branch_name' => $request->branch_name,
            'address' => $request->address,
            'assign_to' => $request->assign_to,
            'status' => $request->status
        ]);

        return redirect()->route('superadmin.branches.index')->with('success', 'Branch updated successfully');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return back()->with('success', 'Branch deleted successfully');
    }
}