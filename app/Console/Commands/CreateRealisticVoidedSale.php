<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sale;
use App\Models\SaleItem;

class CreateRealisticVoidedSale extends Command
{
    protected $signature = 'test:realistic-voided-sale';
    protected $description = 'Create a realistic voided sale with items';

    public function handle()
    {
        $this->info("Creating realistic voided sale with items...");
        
        // Create sale
        $sale = new Sale();
        $sale->cashier_id = 1;
        $sale->branch_id = 1;
        $sale->total_amount = 150;
        $sale->payment_method = 'cash';
        $sale->voided = true;
        $sale->save();
        
        // Create sale items
        $item1 = new SaleItem();
        $item1->sale_id = $sale->id;
        $item1->product_id = 1;
        $item1->quantity = 2;
        $item1->unit_price = 50;
        $item1->subtotal = 100;
        $item1->save();
        
        $item2 = new SaleItem();
        $item2->sale_id = $sale->id;
        $item2->product_id = 2;
        $item2->quantity = 1;
        $item2->unit_price = 50;
        $item2->subtotal = 50;
        $item2->save();
        
        $this->info("Realistic voided sale created with ID: " . $sale->id);
        $this->info("Created 2 sale items for this sale");
        
        return 0;
    }
}
