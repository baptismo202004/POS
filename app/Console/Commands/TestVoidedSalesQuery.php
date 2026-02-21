<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestVoidedSalesQuery extends Command
{
    protected $signature = 'test:voided-query';
    protected $description = 'Test the voided sales query';

    public function handle()
    {
        $today = Carbon::today();
        
        // Test the exact query from dashboard
        $voidedSalesToday = DB::table('sales')
            ->whereDate('created_at', '>=', $today->copy()->startOfMonth())
            ->whereDate('created_at', '<=', $today)
            ->where('voided', true)
            ->count();
        
        $this->info("Today: " . $today->format('Y-m-d'));
        $this->info("Start of month: " . $today->copy()->startOfMonth()->format('Y-m-d'));
        $this->info("Voided sales this month: " . $voidedSalesToday);
        
        // Show all voided sales
        $allVoided = DB::table('sales')
            ->where('voided', true)
            ->get(['id', 'created_at', 'total_amount']);
            
        $this->info("All voided sales:");
        foreach ($allVoided as $sale) {
            $this->info("  ID: {$sale->id}, Date: {$sale->created_at}, Amount: {$sale->total_amount}");
        }
        
        return 0;
    }
}
