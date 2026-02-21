<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Credit;

class AutoVoidCreditSales extends Command
{
    protected $signature = 'auto:void-credit-sales';
    protected $description = 'Automatically void sales for overdue credits';

    public function handle()
    {
        $this->info("=== AUTOMATIC VOIDING OF OVERDUE CREDIT SALES ===\n");

        // Get overdue credits (credits that are past their due date and still active)
        $overdueCredits = DB::table('credits')
            ->where('status', 'active')
            ->where('date', '<', Carbon::today())
            ->get();

        $this->info("Found {$overdueCredits->count()} overdue credits");

        if ($overdueCredits->count() === 0) {
            $this->info("No overdue credits to process.");
            return 0;
        }

        $voidedCount = 0;
        $errors = [];

        foreach ($overdueCredits as $credit) {
            try {
                DB::transaction(function () use ($credit, &$voidedCount) {
                    // Get the associated sale
                    $sale = Sale::find($credit->sale_id);
                    
                    if (!$sale) {
                        $this->warn("Sale not found for credit ID: {$credit->id}");
                        return;
                    }

                    if ($sale->voided) {
                        $this->warn("Sale ID {$sale->id} is already voided");
                        return;
                    }

                    // Void the sale
                    $sale->update([
                        'status' => 'voided',
                        'voided' => true,
                        'voided_by' => 1, // System user ID (you may want to create a system user)
                        'voided_at' => now(),
                    ]);

                    // Update credit status
                    DB::table('credits')
                        ->where('id', $credit->id)
                        ->update([
                            'status' => 'overdue',
                            'notes' => ($credit->notes ?? '') . ' | AUTOMATICALLY VOIDED ON ' . now()->format('Y-m-d H:i:s') . ' - OVERDUE'
                        ]);

                    // Restore stock (similar to manual voiding)
                    $this->restoreStockForSale($sale);

                    $voidedCount++;
                    $this->info("✓ Voided sale ID {$sale->id} (Credit Ref: {$credit->reference_number})");
                });
            } catch (\Exception $e) {
                $errors[] = "Error processing credit ID {$credit->id}: " . $e->getMessage();
                $this->error("✗ Error processing credit ID {$credit->id}: " . $e->getMessage());
            }
        }

        // Summary
        $this->info("\n=== SUMMARY ===");
        $this->info("Total overdue credits processed: {$overdueCredits->count()}");
        $this->info("Successfully voided sales: {$voidedCount}");
        
        if (!empty($errors)) {
            $this->error("\nErrors encountered:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        $this->info("\nAutomatic voiding completed.");
        return 0;
    }

    private function restoreStockForSale($sale)
    {
        // Get sale items and restore stock
        $saleItems = DB::table('sale_items')
            ->where('sale_id', $sale->id)
            ->get();

        foreach ($saleItems as $item) {
            // Record stock in for voided sales
            DB::table('stock_ins')->insert([
                'product_id' => $item->product_id,
                'branch_id' => $sale->branch_id,
                'quantity' => $item->quantity,
                'initial_quantity' => $item->quantity,
                'unit_type_id' => $item->unit_type_id ?? 1, // Default to 1 if null
                'price' => 0, // No cost for voided sales
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
