<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateExistingSalesReferences extends Command
{
    protected $signature = 'update:sales-references';
    protected $description = 'Update existing sales with reference numbers';

    public function handle()
    {
        $this->info('Updating existing sales with reference numbers...');
        
        // Get all sales without reference numbers
        $salesWithoutRef = DB::table('sales')
            ->whereNull('reference_number')
            ->orderBy('created_at', 'asc')
            ->get(['id', 'created_at']);
            
        $this->info("Found {$salesWithoutRef->count()} sales without reference numbers");
        
        foreach ($salesWithoutRef as $index => $sale) {
            $createdAt = Carbon::parse($sale->created_at);
            $referenceNumber = 'REF-' . $createdAt->format('Ymd') . '-' . str_pad($sale->id, 4, '0', STR_PAD_LEFT);
            
            DB::table('sales')
                ->where('id', $sale->id)
                ->update(['reference_number' => $referenceNumber]);
                
            $this->info("Updated Sale ID {$sale->id}: {$referenceNumber}");
        }
        
        $this->info('✅ All existing sales have been updated with reference numbers!');
        return 0;
    }
}
