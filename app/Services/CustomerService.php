<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Credit;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    /**
     * Find or create a customer for credit transactions
     */
    public static function findOrCreateCustomer($customerData, $branchId = null)
    {
        // If customer_id is provided, return existing customer
        if (isset($customerData['customer_id']) && $customerData['customer_id']) {
            return Customer::findOrFail($customerData['customer_id']);
        }

        // Try to find existing customer by phone or email
        $customer = null;
        
        if (!empty($customerData['phone'])) {
            $customer = Customer::where('phone', $customerData['phone'])->first();
        }
        
        if (!$customer && !empty($customerData['email'])) {
            $customer = Customer::where('email', $customerData['email'])->first();
        }

        // If customer exists, update their information and return
        if ($customer) {
            $customer->update([
                'full_name' => $customerData['full_name'] ?? $customer->full_name,
                'phone' => $customerData['phone'] ?? $customer->phone,
                'email' => $customerData['email'] ?? $customer->email,
                'address' => $customerData['address'] ?? $customer->address,
            ]);
            return $customer;
        }

        // Create new customer
        return Customer::create([
            'full_name' => $customerData['full_name'] ?? '',
            'phone' => $customerData['phone'] ?? null,
            'email' => $customerData['email'] ?? null,
            'address' => $customerData['address'] ?? null,
            'max_credit_limit' => $customerData['max_credit_limit'] ?? 0,
            'status' => 'active',
        ]);
    }

    /**
     * Create a new credit with customer handling
     */
    public static function createCredit($creditData, $branchId, $cashierId)
    {
        return DB::transaction(function () use ($creditData, $branchId, $cashierId) {
            // Handle customer creation/lookup
            $customer = self::findOrCreateCustomer($creditData, $branchId);

            // Generate reference number
            $referenceNumber = 'CR-' . date('Y') . '-' . str_pad(Credit::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create credit
            $credit = Credit::create([
                'reference_number' => $referenceNumber,
                'customer_id' => $customer->id,
                'sale_id' => $creditData['sale_id'] ?? null,
                'cashier_id' => $cashierId,
                'branch_id' => $branchId,
                'credit_amount' => $creditData['credit_amount'],
                'paid_amount' => $creditData['paid_amount'] ?? 0,
                'remaining_balance' => $creditData['credit_amount'] - ($creditData['paid_amount'] ?? 0),
                'status' => $creditData['status'] ?? 'active',
                'date' => $creditData['date'] ?? now(),
                'notes' => $creditData['notes'] ?? null,
                'credit_type' => $creditData['credit_type'] ?? 'cash',
            ]);

            return $credit;
        });
    }

    /**
     * Update credit and customer information
     */
    public static function updateCredit($credit, $creditData)
    {
        return DB::transaction(function () use ($credit, $creditData) {
            // Update customer information if provided
            if (isset($creditData['customer']) && is_array($creditData['customer'])) {
                $customer = self::findOrCreateCustomer($creditData['customer']);
                $creditData['customer_id'] = $customer->id;
                unset($creditData['customer']);
            }

            // Update credit
            $credit->update($creditData);

            return $credit;
        });
    }

    /**
     * Get customer credit summary
     */
    public static function getCustomerCreditSummary($customerId)
    {
        $customer = Customer::with(['credits' => function($query) {
            $query->with(['payments', 'branch']);
        }])->findOrFail($customerId);

        $totalCredit = $customer->credits->sum('credit_amount');
        $totalPaid = $customer->credits->sum('paid_amount');
        $outstandingBalance = $customer->credits->sum('remaining_balance');
        
        $activeCredits = $customer->credits->where('status', 'active')->count();
        $paidCredits = $customer->credits->where('status', 'paid')->count();

        return [
            'customer' => $customer,
            'total_credit' => $totalCredit,
            'total_paid' => $totalPaid,
            'outstanding_balance' => $outstandingBalance,
            'active_credits' => $activeCredits,
            'paid_credits' => $paidCredits,
            'credit_limit_utilization' => $customer->max_credit_limit > 0 ? 
                ($outstandingBalance / $customer->max_credit_limit) * 100 : 0,
        ];
    }

    /**
     * Find customer by various criteria
     */
    public static function findCustomer($search)
    {
        return Customer::where(function($query) use ($search) {
            $query->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })->where('status', 'active')->get();
    }
}
