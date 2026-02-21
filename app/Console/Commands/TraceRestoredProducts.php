<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TraceRestoredProducts extends Command
{
    protected $signature = 'trace:restored-products';
    protected $description = 'Trace products restored from auto-voided credit sales';

    public function handle()
    {
        $this->info("=== TRACING RESTORED PRODUCTS FROM AUTO-VOIDED CREDIT SALES ===\n");

        // Get all stock_ins with price = 0 (indicating auto-voided sales)
        $restoredStock = DB::table('stock_ins')
            ->where('price', 0)  // Auto-voided sales have price = 0
            ->orderBy('created_at', 'desc')
            ->limit(50)  // Limit to last 50 items
            ->get();

        if ($restoredStock->count() === 0) {
            $this->info("No products restored from auto-voided sales found.");
            return 0;
        }

        $this->info("Products restored from auto-voided credit sales:");
        $this->info("Total restored items: {$restoredStock->count()}\n");

        foreach ($restoredStock as $stock) {
            $this->showStockDetails($stock);
        }

        // Show summary by product
        $this->showProductSummary();

        return 0;
    }

    private function showStockDetails($stock)
    {
        // Get product details
        $product = DB::table('products')->find($stock->product_id);
        $productName = $product ? $product->product_name : 'Unknown Product';
        
        // Get branch details
        $branch = DB::table('branches')->find($stock->branch_id);
        $branchName = $branch ? $branch->branch_name : 'Unknown Branch';

        $this->info("📦 Product: {$productName}");
        $this->info("   Quantity: +{$stock->quantity} units");
        $this->info("   Branch: {$branchName}");
        $this->info("   Unit Type ID: {$stock->unit_type_id}");
        $this->info("   Restored at: {$stock->created_at}");
        $this->info("   Stock ID: {$stock->id}");
        $this->info("");
    }

    private function showProductSummary()
    {
        $this->info("=== SUMMARY BY PRODUCT ===");
        
        $summary = DB::table('stock_ins')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(*) as restoration_count'))
            ->where('price', 0)
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->get();

        foreach ($summary as $item) {
            $product = DB::table('products')->find($item->product_id);
            $productName = $product ? $product->product_name : 'Unknown Product';
            
            $this->info("{$productName}: +{$item->total_quantity} units ({$item->restoration_count} times)");
        }
    }
}
