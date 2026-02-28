<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $credits = Credit::with(['customer', 'sale', 'cashier'])
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.credit.index', compact('credits'));
    }

    public function create()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $customers = Customer::orderBy('name')->get();
        $sales = Sale::where('branch_id', $branchId)
            ->where('payment_method', 'credit')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.credit.create', compact('customers', 'sales'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:payment,credit',
            'description' => 'nullable|string|max:255',
            'due_date' => 'nullable|date|after:today',
        ]);

        try {
            DB::beginTransaction();

            $credit = Credit::create([
                'customer_id' => $request->customer_id,
                'sale_id' => $request->sale_id,
                'branch_id' => $branchId,
                'amount' => $request->amount,
                'type' => $request->type,
                'description' => $request->description,
                'due_date' => $request->due_date,
                'status' => 'active',
                'processed_by' => $user->id,
                'credit_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('cashier.credit.index')
                ->with('success', 'Credit transaction processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error processing credit transaction: '.$e->getMessage());
        }
    }

    public function show(Credit $credit)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($credit->branch_id !== $branchId) {
            abort(403, 'Unauthorized access to this credit transaction');
        }

        $credit->load(['customer', 'sale', 'cashier']);

        return view('cashier.credit.show', compact('credit'));
    }

    public function edit(Credit $credit)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($credit->branch_id !== $branchId) {
            abort(403, 'Unauthorized access to this credit transaction');
        }

        $customers = Customer::orderBy('name')->get();
        $credit->load(['customer']);

        return view('cashier.credit.edit', compact('credit', 'customers'));
    }

    public function update(Request $request, Credit $credit)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($credit->branch_id !== $branchId) {
            abort(403, 'Unauthorized access to this credit transaction');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Update credit customer association
            $credit->update([
                'customer_id' => $request->customer_id,
            ]);

            // Update customer information if provided
            if ($credit->customer) {
                $customerData = [];
                
                if ($request->filled('email')) {
                    $customerData['email'] = $request->email;
                }
                
                if ($request->filled('phone_number')) {
                    $customerData['phone'] = $request->phone_number;
                }
                
                if (!empty($customerData)) {
                    $credit->customer->update($customerData);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer information updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error updating customer information: ' . $e->getMessage()
            ]);
        }
    }
}
