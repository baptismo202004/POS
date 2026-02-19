<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DebugVoidedSales extends Command
{
    protected $signature = 'debug:voided-sales';
    protected $description = 'Debug voided sales issue';

    public function handle()
    {
        $this->info("=== DEBUGGING VOIDED SALES ===");
        
        // 1. Check all sales
        $totalSales = DB::table('sales')->count();
        $this->info("Total sales: {$totalSales}");
        
        // 2. Check voided column values
        $voidedCounts = DB::table('sales')
            ->select('voided', DB::raw('COUNT(*) as count'))
            ->groupBy('voided')
            ->get();
            
        $this->info("Voided column distribution:");
        foreach ($voidedCounts as $count) {
            $this->info("  voided = '{$count->voided}': {$count->count} records");
        }
        
        // 3. Show recent sales with their voided status
        $recentSales = DB::table('sales')
            ->select('id', 'voided', 'created_at', 'total_amount')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $this->info("\nRecent sales (last 10):");
        foreach ($recentSales as $sale) {
            $voidedStatus = $sale->voided ? 'TRUE' : 'FALSE';
            $this->info("  ID: {$sale->id}, voided: {$voidedStatus}, date: {$sale->created_at}, amount: {$sale->total_amount}");
        }
        
        // 4. Test the exact dashboard query
        $today = Carbon::today();
        $this->info("\nTesting dashboard query:");
        $this->info("Today: {$today->format('Y-m-d')}");
        $this->info("Start of month: {$today->copy()->startOfMonth()->format('Y-m-d')}");
        
        $dashboardQuery = DB::table('sales')
            ->whereDate('created_at', '>=', $today->copy()->startOfMonth())
            ->whereDate('created_at', '<=', $today)
            ->where('voided', true)
            ->count();
            
        $this->info("Dashboard query result: {$dashboardQuery}");
        
        // 5. Test simpler query
        $simpleQuery = DB::table('sales')->where('voided', true)->count();
        $this->info("Simple query result: {$simpleQuery}");
        
        return 0;
    }
}
