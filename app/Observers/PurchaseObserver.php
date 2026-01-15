<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Prevent duplicate expense creation
            if (Expense::where('purchase_id', $purchase->id)->exists()) {
                return;
            }

            $purchaseCategory = ExpenseCategory::where('name', 'Purchases')->first();

            if (!$purchaseCategory) {
                Log::error('"Purchases" expense category not found.');
                return;
            }

            Expense::create([
                'expense_category_id' => $purchaseCategory->id,
                'supplier_id' => $purchase->supplier_id,
                'purchase_id' => $purchase->id,
                'reference_number' => $purchase->reference_number,
                'description' => 'Expense for Purchase #' . $purchase->id,
                'amount' => $purchase->total_cost,
                'expense_date' => $purchase->purchase_date,
                'payment_method' => 'Purchase Order',
            ]);
        });
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "deleted" event.
     */
    public function deleted(Purchase $purchase): void
    {
        // Find and delete the related expense
        $expense = Expense::where('purchase_id', $purchase->id)->first();
        if ($expense) {
            $expense->delete();
        }
    }

    /**
     * Handle the Purchase "restored" event.
     */
    public function restored(Purchase $purchase): void
    {
        //
    }

    /**
     * Handle the Purchase "force deleted" event.
     */
    public function forceDeleted(Purchase $purchase): void
    {
        //
    }
}
