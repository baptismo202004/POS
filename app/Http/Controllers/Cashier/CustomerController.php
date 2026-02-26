<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $search = $request->query('search');

        $customers = Customer::with(['sales' => function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        }])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('cashier.customers.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('cashier.customers.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'notes' => $request->notes,
                'created_by' => $user->id,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('cashier.customers.index')
                ->with('success', 'Customer created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error creating customer: '.$e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $customer->load(['sales' => function ($query) use ($branchId) {
            $query->where('branch_id', $branchId)
                ->with(['items.product'])
                ->orderBy('created_at', 'desc');
        }]);

        $totalSales = $customer->sales->sum('total_amount');
        $totalTransactions = $customer->sales->count();

        return view('cashier.customers.show', compact('customer', 'totalSales', 'totalTransactions'));
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        return view('cashier.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers,phone,'.$customer->id,
            'email' => 'nullable|email|max:255|unique:customers,email,'.$customer->id,
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ]);

        try {
            DB::beginTransaction();

            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'notes' => $request->notes,
                'status' => $request->status,
                'updated_by' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('cashier.customers.index')
                ->with('success', 'Customer updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error updating customer: '.$e->getMessage());
        }
    }
}
