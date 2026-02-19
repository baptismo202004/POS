<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSalesReferences extends Command
{
    protected $signature = 'check:sales-references';
    protected $description = 'Check sales reference numbers';

    public function handle()
    {
        $this->info("=== CHECKING SALES REFERENCE NUMBERS ===");
        
        // Get all sales
        $sales = DB::table('sales')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'reference_number', 'created_at', 'voided']);
            
        $this->info("Total sales: {$sales->count()}");
        
        foreach ($sales as $sale) {
            $refStatus = $sale->reference_number ? $sale->reference_number : 'N/A';
            $voidedStatus = $sale->voided ? 'YES' : 'NO';
            $this->info("Sale ID {$sale->id}: Reference = {$refStatus}, Voided = {$voidedStatus}, Date = {$sale->created_at}");
        }
        
        return 0;
    }
}
