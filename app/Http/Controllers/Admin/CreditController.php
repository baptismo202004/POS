<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditController extends Controller
{
    public function index()
    {
        try {
            \Log::info('Loading credits index page');
            
            // Get today's credits data
            $today = \Carbon\Carbon::today();
            
            \Log::info('Querying today credits');
            $todayCredits = Credit::whereDate('created_at', $today)
                ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
                ->first();
            
            // Get this month's credits
            $thisMonth = \Carbon\Carbon::now()->startOfMonth();
            \Log::info('Querying monthly credits');
            $monthlyCredits = Credit::whereDate('created_at', '>=', $thisMonth)
                ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
                ->first();
            
            // Get overdue credits
            \Log::info('Querying overdue credits');
            $overdueCredits = Credit::where('date', '<', $today)
                ->where('status', 'active')
                ->selectRaw('COUNT(*) as total_overdue, COALESCE(SUM(remaining_balance), 0) as total_overdue_amount')
                ->first();
            
            // Get recent credits for the table - try without relationships first
            \Log::info('Querying recent credits without relationships');
            $credits = Credit::orderBy('created_at', 'desc')
                ->paginate(20);
            
            \Log::info('Credits loaded successfully, returning view');
            
            return view('Admin.credits.index', compact(
                'credits',
                'todayCredits',
                'monthlyCredits',
                'overdueCredits'
            ));
            
        } catch (\Exception $e) {
            \Log::error('Error loading credits index: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return a simple error response
            return response()->view('errors.500', ['message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        // Get all registered customers for selection
        $customers = \App\Models\Customer::orderBy('full_name')->get();
        
        // Get walk-in customers who have credits
        $walkInCustomers = Credit::whereNotNull('customer_name')
            ->distinct('customer_name')
            ->pluck('customer_name')
            ->unique()
            ->map(function ($name) {
                return (object) [
                    'id' => 'walk-in-' . \Illuminate\Support\Str::slug($name),
                    'full_name' => $name . ' (Walk-in Customer)',
                    'is_walk_in' => true
                ];
            });
            
        return view('Admin.credits.create', compact('customers', 'walkInCustomers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|string|max:255',
            'credit_amount' => 'required|numeric|min:0',
            'date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $customerName = null;
                
                // Check if it's a new customer (starts with 'new-')
                if (strpos($request->customer_id, 'new-') === 0) {
                    // Extract customer name from the ID
                    $customerName = substr($request->customer_id, 4);
                    
                } elseif (strpos($request->customer_id, 'walk-in-') === 0) {
                    // Handle existing walk-in customers
                    $walkInId = $request->customer_id;
                    // Extract name from the walk-in ID
                    $customerName = str_replace('walk-in-', '', $walkInId);
                    $customerName = str_replace('-', ' ', $customerName);
                    
                } else {
                    // Existing registered customer - get name from customers table
                    $customer = \App\Models\Customer::find($request->customer_id);
                    $customerName = $customer ? $customer->full_name : 'Unknown Customer';
                }
                
                // Generate unique reference number
                $lastCredit = Credit::orderBy('id', 'desc')->first();
                $nextNumber = $lastCredit ? $lastCredit->id + 1 : 1;
                $referenceNumber = 'CR-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                
                $credit = Credit::create([
                    'reference_number' => $referenceNumber,
                    'customer_name' => $customerName,
                    'cashier_id' => auth()->id(),
                    'credit_amount' => $request->credit_amount,
                    'paid_amount' => 0,
                    'remaining_balance' => $request->credit_amount,
                    'status' => 'active',
                    'date' => $request->date,
                    'notes' => $request->notes,
                ]);

                return $credit;
            });

            return response()->json(['success' => true, 'message' => 'Credit created successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Credit $credit)
    {
        $credit->load(['sale', 'cashier', 'payments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);
        
        return view('Admin.credits.show', compact('credit'));
    }

    public function makePayment(Request $request, Credit $credit)
    {
        $validator = Validator::make($request->all(), [
            'payment_amount' => 'required|numeric|min:0.01|max:' . $credit->remaining_balance,
            'payment_method' => 'required|in:cash,card,bank_transfer,other',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $credit) {
                // Create payment record
                $payment = CreditPayment::create([
                    'credit_id' => $credit->id,
                    'cashier_id' => auth()->id(),
                    'payment_amount' => $request->payment_amount,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                ]);

                // Update credit
                $credit->paid_amount += $request->payment_amount;
                $credit->remaining_balance -= $request->payment_amount;
                
                if ($credit->remaining_balance <= 0) {
                    $credit->status = 'paid';
                    $credit->remaining_balance = 0;
                }
                
                $credit->save();

                return $payment;
            });

            return response()->json(['success' => true, 'message' => 'Payment recorded successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, Credit $credit)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,paid,overdue',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $credit->update(['status' => $request->status]);
        
        return response()->json(['success' => true, 'message' => 'Credit status updated successfully']);
    }
    
    public function updateCustomerName(Request $request, Credit $credit)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $credit->update(['customer_name' => $request->customer_name]);
            
            return response()->json(['success' => true, 'message' => 'Customer name updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateWalkInCustomer(Request $request, Credit $credit)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $credit->update([
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
            ]);
            
            return response()->json(['success' => true, 'message' => 'Customer information updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function creditLimitsData()
    {
        try {
            // Get all credits with customer names grouped by customer
            $creditsByCustomer = Credit::selectRaw('credits.customer_name, COUNT(*) as total_credits, SUM(credits.credit_amount) as total_credit_limit, SUM(credits.paid_amount) as total_paid, SUM(credits.remaining_balance) as total_remaining, COALESCE(customers.max_credit_limit, 0) as max_credit_limit')
                ->leftJoin('customers', 'customers.full_name', '=', 'credits.customer_name')
                ->whereNotNull('credits.customer_name')
                ->where('credits.customer_name', '!=', '')
                ->groupBy('credits.customer_name', 'customers.max_credit_limit')
                ->orderBy('credits.customer_name')
                ->get();
                
            // Get overall statistics
            $totalCreditLimit = $creditsByCustomer->sum('total_credit_limit');
            $totalPaid = $creditsByCustomer->sum('total_paid');
            $totalRemaining = $creditsByCustomer->sum('total_remaining');
            $totalCustomers = $creditsByCustomer->count();
            
            return response()->json([
                'success' => true,
                'creditsByCustomer' => $creditsByCustomer,
                'totalCreditLimit' => $totalCreditLimit,
                'totalPaid' => $totalPaid,
                'totalRemaining' => $totalRemaining,
                'totalCustomers' => $totalCustomers
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function updateCreditLimit(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string',
                'max_credit_limit' => 'required|numeric|min:0'
            ]);
            
            $customerName = $request->customer_name;
            $maxCreditLimit = $request->max_credit_limit;
            
            // Use DB::table for more reliable update/insert
            $existing = DB::table('customers')->where('full_name', $customerName)->first();
            
            if ($existing) {
                // Update existing customer
                DB::table('customers')
                    ->where('full_name', $customerName)
                    ->update(['max_credit_limit' => $maxCreditLimit, 'updated_at' => now()]);
            } else {
                // Insert new customer record
                DB::table('customers')->insert([
                    'full_name' => $customerName,
                    'max_credit_limit' => $maxCreditLimit,
                    'email' => null,
                    'phone' => null,
                    'address' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => "Credit limit for {$customerName} has been set to ₱" . number_format($maxCreditLimit, 2)
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Credit limit update error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
