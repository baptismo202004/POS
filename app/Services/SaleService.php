<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function processSale(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Validate stock for all items
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception('Insufficient stock for product: ' . $product->product_name);
                }
            }

            // 2. Create the sale
            $sale = Sale::create([
                'cashier_id' => $data['cashier_id'],
                'employee_id' => $data['employee_id'] ?? '',
                'customer_id' => $data['customer_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'total_amount' => $data['total_amount'],
                'tax' => $data['tax'] ?? 0,
                'payment_method' => $data['payment_method'],
            ]);

            // 3. Create sale items and stock outs
            foreach ($data['items'] as $item) {
                $sale->saleItems()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);

                $sale->stockOuts()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $sale;
        });
    }
}
