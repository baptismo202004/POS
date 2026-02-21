<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;

class CreateTestVoidedSale extends Command
{
    protected $signature = 'test:voided-sale';
    protected $description = 'Create a test voided sale';

    public function handle()
    {
        $sale = new Sale();
        $sale->cashier_id = 1;
        $sale->branch_id = 1;
        $sale->total_amount = 100;
        $sale->payment_method = 'cash';
        $sale->voided = true;
        $sale->save();
        
        $this->info("Test voided sale created with ID: " . $sale->id);
        return 0;
    }
}
