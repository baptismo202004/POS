<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckRealSales extends Command
{
    protected $signature = 'check:real-sales';
    protected $description = 'Check real sales structure';

    public function handle()
    {
        $this->info("=== CHECKING REAL SALES STRUCTURE ===");
        
        // Get recent real sales
        $realSales = DB::table('sales')
            ->select('id', 'total_amount', 'created_at', 'voided')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        $this->info("Recent real sales:");
        foreach ($realSales as $sale) {
            $this->info("  ID: {$sale->id}, Amount: {$sale->total_amount}, Voided: " . ($sale->voided ? 'YES' : 'NO'));
            
            // Check if this sale has items
            $itemCount = DB::table('sale_items')->where('sale_id', $sale->id)->count();
            $this->info("    Sale items count: {$itemCount}");
        }
        
        return 0;
    }
}
