<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function show(Branch $branch)
    {
        // Get branch statistics
        $todaySales = DB::table('sales')
            ->where('branch_id', $branch->id)
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('total_amount') ?? 0;
            
        $monthlySales = DB::table('sales')
            ->where('branch_id', $branch->id)
            ->whereBetween('created_at', [\Carbon\Carbon::now()->startOfMonth(), \Carbon\Carbon::now()->endOfMonth()])
            ->sum('total_amount') ?? 0;
            
        $totalTransactions = DB::table('sales')
            ->where('branch_id', $branch->id)
            ->count() ?? 0;
            
        // Get today's sales for this branch
        $todaySalesData = DB::table('sales')
            ->join('users', 'sales.cashier_id', '=', 'users.id')
            ->where('sales.branch_id', $branch->id)
            ->whereDate('sales.created_at', \Carbon\Carbon::today())
            ->select('sales.*', 'users.name as cashier_name')
            ->orderBy('sales.created_at', 'desc')
            ->get();
            
        // Get all sales for this branch (last 30 days)
        $allSalesData = DB::table('sales')
            ->join('users', 'sales.cashier_id', '=', 'users.id')
            ->where('sales.branch_id', $branch->id)
            ->whereBetween('sales.created_at', [\Carbon\Carbon::now()->subDays(30), \Carbon\Carbon::now()])
            ->select('sales.*', 'users.name as cashier_name')
            ->orderBy('sales.created_at', 'desc')
            ->get();
            
        return view('SuperAdmin.branches.show', compact('branch', 'todaySales', 'monthlySales', 'totalTransactions', 'todaySalesData', 'allSalesData'));
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