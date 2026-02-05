<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Credit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        // Get customers without the problematic credits relationship
        $customers = Customer::orderBy('created_at', 'desc')->paginate(20);
        
        // Get all credits to see what's in the database
        $walkInCredits = Credit::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Debug logging
        \Log::info('Customers count: ' . $customers->count());
        \Log::info('Credits count: ' . $walkInCredits->count());
        \Log::info('Total credits in DB: ' . Credit::count());
        
        // Check if credits table exists and has data
        try {
            $creditTableExists = \Schema::hasTable('credits');
            \Log::info('Credits table exists: ' . ($creditTableExists ? 'YES' : 'NO'));
            
            if ($creditTableExists) {
                $totalCredits = \DB::table('credits')->count();
                \Log::info('Total credits from DB::table: ' . $totalCredits);
                
                // Get some sample data
                $sampleCredits = \DB::table('credits')->limit(3)->get();
                foreach ($sampleCredits as $credit) {
                    \Log::info('Sample credit: ' . json_encode($credit));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error checking credits table: ' . $e->getMessage());
        }
        
        // Log first credit details for debugging
        if ($walkInCredits->count() > 0) {
            \Log::info('First credit from model: ' . json_encode($walkInCredits->first()));
        }
        
        return view('admin.customers.index', compact('customers', 'walkInCredits'));
    }
    
    public function update(Request $request, Customer $customer)
    {
        try {
            \Log::info('Customer update request:', [
                'customer_id' => $customer->id,
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $customer->update([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
            ]);

            \Log::info('Customer updated successfully:', ['customer' => $customer]);

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            \Log::error('Customer update error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function creditLimits()
    {
        $customers = Customer::with(['credits' => function($query) {
            $query->where('status', 'active');
        }])->get();
        
        $totalCreditLimit = $customers->sum(function($customer) {
            return $customer->credits->sum('credit_amount');
        });
        
        $totalUsed = $customers->sum(function($customer) {
            return $customer->credits->sum('paid_amount');
        });
        
        return view('admin.customers.credit-limits', compact('customers', 'totalCreditLimit', 'totalUsed'));
    }
    
    public function paymentHistory()
    {
        $payments = Credit::with(['sale', 'cashier', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.customers.payment-history', compact('payments'));
    }
}
