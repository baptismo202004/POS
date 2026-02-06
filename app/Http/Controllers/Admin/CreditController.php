<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function index()
    {
        try {
            Log::info('Loading credits index page');
            
            // Get today's credits data
            $today = \Carbon\Carbon::today();
            
            Log::info('Querying today credits');
            $todayCredits = Credit::whereDate('created_at', $today)
                ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
                ->first();
            
            // Get this month's credits
            $thisMonth = \Carbon\Carbon::now()->startOfMonth();
            Log::info('Querying monthly credits');
            $monthlyCredits = Credit::whereDate('created_at', '>=', $thisMonth)
                ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
                ->first();
            
            // Get overdue credits
            Log::info('Querying overdue credits');
            $overdueCredits = Credit::where('date', '<', $today)
                ->where('status', 'active')
                ->selectRaw('COUNT(*) as total_overdue, COALESCE(SUM(remaining_balance), 0) as total_overdue_amount')
                ->first();
            
            // Get unique customers with their total credit information
            Log::info('Querying unique customers with total credit info');
            $customers = DB::table('customers')
                ->select([
                    'customers.id as customer_id',
                    'customers.full_name',
                    'customers.phone',
                    'customers.email', 
                    'customers.address',
                    DB::raw('COUNT(credits.id) as credit_giver_total'),
                    DB::raw('COALESCE(SUM(credits.credit_amount), 0) as total_credit'),
                    DB::raw('COALESCE(SUM(credits.paid_amount), 0) as total_paid'),
                    DB::raw('COALESCE(SUM(credits.remaining_balance), 0) as outstanding_balance'),
                    DB::raw('MAX(credits.created_at) as last_credit_date'),
                    DB::raw('MAX(credits.date) as last_due_date'),
                    DB::raw('CASE 
                        WHEN COALESCE(SUM(credits.remaining_balance), 0) <= 0 THEN "Fully Paid"
                        WHEN COALESCE(SUM(credits.paid_amount), 0) / COALESCE(SUM(credits.credit_amount), 0) >= 0.8 THEN "Good Standing"
                        ELSE "Outstanding"
                    END as status')
                ])
                ->leftJoin('credits', 'customers.id', '=', 'credits.customer_id')
                ->groupBy('customers.id', 'customers.full_name', 'customers.phone', 'customers.email', 'customers.address')
                ->orderByRaw('MAX(credits.created_at) DESC')
                ->paginate(20);
            
            Log::info('Credits loaded successfully, returning view');
            
            return view('Admin.credits.index', compact(
                'customers',
                'todayCredits',
                'monthlyCredits',
                'overdueCredits'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error loading credits index: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return a simple error response
            return response()->view('errors.500', ['message' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        // Get all registered customers for selection
        $customers = \App\Models\Customer::orderBy('full_name')->get();
        
        // Get walk-in customers who have credits
        $walkInCustomers = Credit::whereNotNull('customer_id')
            ->with('customer')
            ->distinct('customer_id')
            ->get(['customer_id'])
            ->map(function ($credit) {
                return (object) [
                    'id' => $credit->customer_id,
                    'full_name' => $credit->customer->full_name,
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
            'credit_type' => 'required|in:cash,grocery,electronics',
            'sale_id' => 'required_if:credit_type,grocery,electronics|nullable|exists:sales,id',
            'date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $customerId = null;
                
                // Check if it's a new customer (starts with 'new-')
                if (strpos($request->customer_id, 'new-') === 0) {
                    // Extract customer name from the ID
                    $customerName = substr($request->customer_id, 4);
                    
                    // Check if customer already exists in customers table to avoid duplicates
                    $existingCustomer = DB::table('customers')->where('full_name', $customerName)->first();
                    if (!$existingCustomer) {
                        // Only create customer record if it doesn't exist
                        $customerId = DB::table('customers')->insertGetId([
                            'full_name' => $customerName,
                            'email' => null,
                            'phone' => null,
                            'address' => null,
                            'max_credit_limit' => 0,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    } else {
                        $customerId = $existingCustomer->id;
                    }
                    
                } else {
                    // Existing customer (either registered or walk-in)
                    $customerId = $request->customer_id;
                }
                
                // Generate unique reference number
                $lastCredit = Credit::orderBy('id', 'desc')->first();
                $nextNumber = $lastCredit ? $lastCredit->id + 1 : 1;
                $referenceNumber = 'CR-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                
                $credit = Credit::create([
                    'reference_number' => $referenceNumber,
                    'customer_id' => $customerId,
                    'cashier_id' => Auth::id(),
                    'credit_amount' => $request->credit_amount,
                    'paid_amount' => 0,
                    'remaining_balance' => $request->credit_amount,
                    'status' => 'active',
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'credit_type' => $request->credit_type,
                    'sale_id' => $request->sale_id,
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

    public function customerCreditDetails($customerId)
    {
        try {
            // Get customer information
            $customer = \App\Models\Customer::find($customerId);
            if (!$customer) {
                return back()->with('error', 'Customer not found');
            }
            
            // Get ACTIVE credits summary for header (operational view only)
            $activeSummary = DB::table('credits')
                ->where('customer_id', $customerId)
                ->where('status', '!=', 'paid')
                ->selectRaw('
                    COUNT(*) as active_credits,
                    COALESCE(SUM(credit_amount), 0) as active_credit_amount,
                    COALESCE(SUM(paid_amount), 0) as total_paid_active,
                    COALESCE(SUM(remaining_balance), 0) as outstanding_balance
                ')
                ->first();
            
            // Determine operational status based on active credits only
            $status = $activeSummary->outstanding_balance > 0 ? 'Outstanding' : 'Good Standing';
            
            // Get ACTIVE credits (operational view)
            $activeCredits = Credit::where('customer_id', $customerId)
                ->where('status', '!=', 'paid')
                ->with(['cashier', 'payments' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function($credit) {
                    return \Carbon\Carbon::parse($credit->created_at)->format('Y-m-d');
                });
            
            // Get RECENTLY PAID credits (context only, limited to 3)
            $recentlyPaidCredits = Credit::where('customer_id', $customerId)
                ->where('status', 'paid')
                ->with(['cashier'])
                ->orderBy('updated_at', 'desc') // When they were marked as paid
                ->limit(3)
                ->get()
                ->groupBy(function($credit) {
                    return \Carbon\Carbon::parse($credit->created_at)->format('Y-m-d');
                });
            
            return view('admin.credits.customer-credit-details', compact(
                'customer',
                'activeSummary',
                'status',
                'activeCredits',
                'recentlyPaidCredits'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error loading customer credit details: ' . $e->getMessage());
            return back()->with('error', 'Unable to load customer credit details');
        }
    }

    public function fullCreditHistory($customerId)
    {
        try {
            // Get customer information
            $customer = \App\Models\Customer::find($customerId);
            if (!$customer) {
                return back()->with('error', 'Customer not found');
            }
            
            // Get LIFETIME summary (ALL credits)
            $lifetimeSummary = DB::table('credits')
                ->where('customer_id', $customerId)
                ->selectRaw('
                    COUNT(*) as total_credits_all_time,
                    COALESCE(SUM(credit_amount), 0) as lifetime_credit_amount,
                    COALESCE(SUM(paid_amount), 0) as lifetime_paid_amount,
                    COALESCE(SUM(remaining_balance), 0) as lifetime_outstanding_balance
                ')
                ->first();
            
            // Get ALL credits for history (no status filtering)
            $allCredits = Credit::where('customer_id', $customerId)
                ->with(['cashier', 'payments' => function($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Apply filters if provided
            $filters = request()->only(['date_from', 'date_to', 'status', 'credit_id', 'created_by']);
            
            if (!empty($filters)) {
                $query = Credit::where('customer_id', $customerId)
                    ->with(['cashier', 'payments' => function($query) {
                        $query->orderBy('created_at', 'desc');
                    }]);
                
                // Date range filter
                if (!empty($filters['date_from'])) {
                    $query->whereDate('created_at', '>=', $filters['date_from']);
                }
                if (!empty($filters['date_to'])) {
                    $query->whereDate('created_at', '<=', $filters['date_to']);
                }
                
                // Status filter
                if (!empty($filters['status'])) {
                    if ($filters['status'] === 'active') {
                        $query->where('status', 'active');
                    } elseif ($filters['status'] === 'partial') {
                        $query->where('status', 'partial');
                    } elseif ($filters['status'] === 'paid') {
                        $query->where('status', 'paid');
                    }
                }
                
                // Credit ID filter
                if (!empty($filters['credit_id'])) {
                    $query->where('id', $filters['credit_id']);
                }
                
                // Created by filter
                if (!empty($filters['created_by'])) {
                    $query->whereHas('cashier', function($q) use ($filters) {
                        $q->where('name', 'like', '%' . $filters['created_by'] . '%');
                    });
                }
                
                $filteredCredits = $query->orderBy('created_at', 'desc')->get();
            } else {
                $filteredCredits = $allCredits;
            }
            
            // Group credits by date for display
            $groupedCredits = $filteredCredits->groupBy(function($credit) {
                return \Carbon\Carbon::parse($credit->created_at)->format('Y-m-d');
            });
            
            // Get unique cashiers for filter dropdown
            $cashiers = \App\Models\User::join('credits', 'users.id', '=', 'credits.cashier_id')
                ->where('credits.customer_id', $customerId)
                ->pluck('users.name')->unique()->sort();
            
            return view('admin.credits.full-credit-history', compact(
                'customer',
                'lifetimeSummary',
                'groupedCredits',
                'filters',
                'cashiers',
                'allCredits'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error loading full credit history: ' . $e->getMessage());
            return back()->with('error', 'Unable to load credit history');
        }
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
                    'cashier_id' => Auth::id(),
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
            Log::error('Credit limit update error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
