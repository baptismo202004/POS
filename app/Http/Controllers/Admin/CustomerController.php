<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Credit;
use App\Models\CreditPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            // Get all customers with basic information for the simple table
            $customers = DB::table('customers')
                ->select([
                    'customers.id as customer_id',
                    'customers.full_name',
                    'customers.status',
                    'customers.created_at',
                    DB::raw('COALESCE(users.name, "System") as created_by'),
                    DB::raw('COALESCE(SUM(credits.credit_amount), 0) as total_credit'),
                    DB::raw('COALESCE(SUM(credits.remaining_balance), 0) as outstanding_balance')
                ])
                ->leftJoin('credits', 'customers.id', '=', 'credits.customer_id')
                ->leftJoin('users', 'customers.created_by', '=', 'users.id')
                ->groupBy('customers.id', 'customers.full_name', 'customers.status', 'customers.created_at', 'users.name')
                ->orderBy('customers.created_at', 'desc')
                ->paginate(20);

            return view('Admin.customers.index', compact('customers'));
            
        } catch (\Exception $e) {
            Log::error('Error loading customers: ' . $e->getMessage());
            
            // Fallback to basic customer list
            $customers = Customer::with('user')->orderBy('created_at', 'desc')->paginate(20);
            return view('Admin.customers.index', compact('customers'));
        }
    }

    public function show($customer)
    {
        try {
            $customerModel = Customer::findOrFail($customer);
            
            // Get customer details with credit information
            $customerDetails = DB::table('customers')
                ->select([
                    'customers.id as customer_id',
                    'customers.full_name',
                    'customers.phone',
                    'customers.email', 
                    'customers.address',
                    'customers.max_credit_limit',
                    'customers.status',
                    'customers.created_at',
                    DB::raw('COALESCE(users.name, "System") as created_by'),
                    DB::raw('COUNT(credits.id) as total_credits'),
                    DB::raw('COALESCE(SUM(credits.credit_amount), 0) as total_credit'),
                    DB::raw('COALESCE(SUM(credits.paid_amount), 0) as total_paid'),
                    DB::raw('COALESCE(SUM(credits.remaining_balance), 0) as outstanding_balance'),
                    DB::raw('MAX(credits.created_at) as last_credit_date'),
                    DB::raw('CASE 
                        WHEN COALESCE(SUM(credits.remaining_balance), 0) <= 0 THEN "Fully Paid"
                        WHEN COALESCE(SUM(credits.paid_amount), 0) / COALESCE(SUM(credits.credit_amount), 0) >= 0.8 THEN "Good Standing"
                        ELSE "Outstanding"
                    END as credit_status')
                ])
                ->leftJoin('credits', 'customers.id', '=', 'credits.customer_id')
                ->leftJoin('users', 'customers.created_by', '=', 'users.id')
                ->where('customers.id', $customer)
                ->groupBy('customers.id', 'customers.full_name', 'customers.phone', 'customers.email', 'customers.address', 'customers.max_credit_limit', 'customers.status', 'customers.created_at', 'users.name')
                ->first();

            // Get recent credits for this customer
            $recentCredits = Credit::where('customer_id', $customer)
                ->with(['cashier'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            return view('Admin.customers.show', compact('customerDetails', 'recentCredits'));
            
        } catch (\Exception $e) {
            Log::error('Error loading customer details: ' . $e->getMessage());
            return back()->with('error', 'Unable to load customer details');
        }
    }
    
    public function update(Request $request, $customer)
    {
        try {
            $customerModel = Customer::findOrFail($customer);
            
            Log::info('Customer update request:', [
                'customer_id' => $customerModel->id,
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customerModel->update([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
            ]);

            Log::info('Customer updated successfully:', ['customer' => $customerModel]);

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'customer' => $customerModel
            ]);
        } catch (\Exception $e) {
            Log::error('Customer update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
                'max_credit_limit' => 'required|numeric|min:0',
                'status' => 'required|in:active,blocked',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customer = Customer::create([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'max_credit_limit' => $request->max_credit_limit,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer
            ]);

        } catch (\Exception $e) {
            Log::error('Customer creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(Request $request, $customer)
    {
        try {
            $customerModel = Customer::findOrFail($customer);
            
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:active,blocked',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customerModel->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Customer status updated successfully',
                'customer' => $customerModel
            ]);

        } catch (\Exception $e) {
            Log::error('Customer status toggle error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function paymentHistory()
    {
        $paymentsQuery = CreditPayment::with(['credit', 'cashier'])
            ->orderBy('created_at', 'desc');
            
        $payments = $paymentsQuery->paginate(20);
        
        // Calculate remaining balance for each payment
        $payments->getCollection()->map(function ($payment) {
            // Calculate total payments made before this payment
            $previousPayments = CreditPayment::where('credit_id', $payment->credit_id)
                ->where('created_at', '<', $payment->created_at)
                ->sum('payment_amount');
            
            // Calculate remaining balance after this payment
            $remainingBalance = $payment->credit->credit_amount - ($previousPayments + $payment->payment_amount);
            
            $payment->remaining_balance_after_payment = max(0, $remainingBalance);
            $payment->previous_payments_total = $previousPayments;
            
            return $payment;
        });
            
        return view('Admin.customers.payment-history', compact('payments'));
    }
}
