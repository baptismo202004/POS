<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebugVoidedSalesQuery extends Command
{
    protected $signature = 'debug:voided-query';
    protected $description = 'Debug the exact voided sales query';

    public function handle()
    {
        $this->info("=== DEBUGGING VOIDED SALES QUERY ===");
        
        // Test the exact query from the controller
        $today = Carbon::today();
        $startDate = $today->copy()->startOfMonth();
        $endDate = $today->copy()->endOfDay();
        
        $this->info("Date range: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
        
        // Get voided sales with the exact query
        $voidedSales = DB::table('sales')
            ->where('voided', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get(['id', 'total_amount', 'created_at', 'voided']);
            
        $this->info("Voided sales found by controller query:");
        foreach ($voidedSales as $sale) {
            $this->info("  ID: {$sale->id}, Amount: {$sale->total_amount}, Date: {$sale->created_at}, Voided: {$sale->voided}");
        }
        
        // Also test without date filter
        $allVoided = DB::table('sales')
            ->where('voided', true)
            ->get(['id', 'total_amount', 'created_at']);
            
        $this->info("\nAll voided sales (no date filter):");
        foreach ($allVoided as $sale) {
            $this->info("  ID: {$sale->id}, Amount: {$sale->total_amount}, Date: {$sale->created_at}");
        }
        
        return 0;
    }
}
