<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSaleItems extends Command
{
    protected $signature = 'check:sale-items';
    protected $description = 'Check sale items for voided sales';

    public function handle()
    {
        $this->info("=== CHECKING SALE ITEMS FOR VOIDED SALES ===");
        
        // Check voided sales
        $voidedSales = DB::table('sales')
            ->where('voided', true)
            ->get(['id', 'total_amount', 'created_at']);
            
        $this->info("Voided sales:");
        foreach ($voidedSales as $sale) {
            $this->info("  Sale ID: {$sale->id}, Amount: {$sale->total_amount}");
            
            // Check sale items for this sale
            $saleItems = DB::table('sale_items')
                ->where('sale_id', $sale->id)
                ->get(['product_id', 'quantity', 'unit_price', 'subtotal']);
                
            $this->info("    Sale items for Sale ID {$sale->id}:");
            if ($saleItems->count() > 0) {
                foreach ($saleItems as $item) {
                    $this->info("      Product ID: {$item->product_id}, Quantity: {$item->quantity}, Price: {$item->unit_price}, Subtotal: {$item->subtotal}");
                }
                $totalQuantity = $saleItems->sum('quantity');
                $this->info("    Total quantity: {$totalQuantity}");
            } else {
                $this->info("      No sale items found");
            }
            $this->info("");
        }
        
        return 0;
    }
}
