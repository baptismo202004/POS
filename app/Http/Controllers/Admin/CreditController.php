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
        // Get today's credits data
        $today = \Carbon\Carbon::today();
        
        $todayCredits = Credit::whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
            ->first();
        
        // Get this month's credits
        $thisMonth = \Carbon\Carbon::now()->startOfMonth();
        $monthlyCredits = Credit::whereDate('created_at', '>=', $thisMonth)
            ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
            ->first();
        
        // Get overdue credits
        $overdueCredits = Credit::where('date', '<', $today)
            ->where('status', 'active')
            ->selectRaw('COUNT(*) as total_overdue, COALESCE(SUM(remaining_balance), 0) as total_overdue_amount')
            ->first();
        
        // Get recent credits for the table
        $credits = Credit::with(['customer', 'sale', 'cashier', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('Admin.credits.index', compact(
            'credits',
            'todayCredits',
            'monthlyCredits',
            'overdueCredits'
        ));
    }

    public function create()
    {
        return view('Admin.credits.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'nullable|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'credit_amount' => 'required|numeric|min:0',
            'date' => 'required|date|after:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $credit = Credit::create([
                    'customer_id' => $request->customer_id,
                    'sale_id' => $request->sale_id,
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
        $credit->load(['customer', 'sale', 'cashier', 'payments' => function($query) {
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
}
