<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FixSalesReferenceNumbers extends Command
{
    protected $signature = 'fix:sales-references';
    protected $description = 'Fix sales reference numbers with correct dates';

    public function handle()
    {
        $this->info('Fixing sales reference numbers with correct dates...');
        
        // Get all sales and group by date
        $sales = DB::table('sales')
            ->orderBy('created_at', 'asc')
            ->get(['id', 'created_at', 'reference_number']);
            
        // Group sales by date
        $salesByDate = [];
        foreach ($sales as $sale) {
            $date = Carbon::parse($sale->created_at)->format('Ymd');
            if (!isset($salesByDate[$date])) {
                $salesByDate[$date] = [];
            }
            $salesByDate[$date][] = $sale;
        }
        
        // Update reference numbers for each date group
        foreach ($salesByDate as $date => $dateSales) {
            $this->info("Processing sales for date: {$date}");
            
            foreach ($dateSales as $index => $sale) {
                $sequence = $index + 1;
                $newReference = "REF-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                
                DB::table('sales')
                    ->where('id', $sale->id)
                    ->update(['reference_number' => $newReference]);
                
                $this->info("  Sale ID {$sale->id}: {$sale->reference_number} → {$newReference}");
            }
        }
        
        $this->info('✅ All sales reference numbers have been fixed!');
        return 0;
    }
}
