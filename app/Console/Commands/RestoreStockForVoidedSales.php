<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreStockForVoidedSales extends Command
{
    protected $signature = 'restore:stock-voided-sales';
    protected $description = 'Manually restore stock for already voided sales';

    public function handle()
    {
        $this->info("=== RESTORING STOCK FOR VOIDED SALES ===\n");

        // Get all voided sales that don't have stock restored yet
        $voidedSales = DB::table('sales')
            ->where('voided', 1)
            ->get();

        $this->info("Found {$voidedSales->count()} voided sales");

        $restoredCount = 0;
        $errors = [];

        foreach ($voidedSales as $sale) {
            try {
                // Get sale items
                $saleItems = DB::table('sale_items')
                    ->where('sale_id', $sale->id)
                    ->get();

                if ($saleItems->count() === 0) {
                    $this->warn("No items found for sale ID {$sale->id}");
                    continue;
                }

                $this->info("Processing sale ID {$sale->id} (Amount: {$sale->total_amount})");

                foreach ($saleItems as $item) {
                    // Get product details
                    $product = DB::table('products')->find($item->product_id);
                    $productName = $product ? $product->product_name : 'Unknown Product';

                    // Record stock in
                    DB::table('stock_ins')->insert([
                        'product_id' => $item->product_id,
                        'branch_id' => $sale->branch_id,
                        'quantity' => $item->quantity,
                        'initial_quantity' => $item->quantity,
                        'unit_type_id' => $item->unit_type_id ?? 1,
                        'price' => 0, // No cost for voided sales
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->info("  ✓ Restored {$item->quantity} x {$productName} to stock");
                }

                $restoredCount++;
                $this->info("");

            } catch (\Exception $e) {
                $errors[] = "Error processing sale ID {$sale->id}: " . $e->getMessage();
                $this->error("✗ Error processing sale ID {$sale->id}: " . $e->getMessage());
            }
        }

        // Summary
        $this->info("=== SUMMARY ===");
        $this->info("Total voided sales processed: {$voidedSales->count()}");
        $this->info("Successfully restored stock for: {$restoredCount} sales");
        
        if (!empty($errors)) {
            $this->error("\nErrors encountered:");
            foreach ($errors as $error) {
                $this->error("  - {$error}");
            }
        }

        $this->info("\nStock restoration completed.");
        return 0;
    }
}
