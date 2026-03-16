<?php

namespace Database\Seeders\Pos;

use Illuminate\Support\Facades\DB;

class InventoryHelper
{
    public static function availableStock(int $productId, int $branchId, ?int $unitTypeId = null): float
    {
        $base = (float) (DB::table('branch_stocks')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->value('quantity_base') ?? 0);

        if (is_null($unitTypeId)) {
            return $base;
        }

        $factor = DB::table('product_unit_type')
            ->where('product_id', $productId)
            ->where('unit_type_id', $unitTypeId)
            ->value('conversion_factor');

        if (is_null($factor) || (float) $factor <= 0) {
            return 0;
        }

        return $base / (float) $factor;
    }

    public static function deductStockForSale(int $saleId, int $productId, int $branchId, int $unitTypeId, float $quantity, \DateTimeInterface $at): void
    {
        $factor = DB::table('product_unit_type')
            ->where('product_id', $productId)
            ->where('unit_type_id', $unitTypeId)
            ->value('conversion_factor');

        if (is_null($factor) || (float) $factor <= 0) {
            throw new \RuntimeException('Unit conversion not configured for product.');
        }

        $baseQty = $quantity * (float) $factor;

        $stock = DB::table('branch_stocks')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first(['id', 'quantity_base']);

        $current = (float) ($stock->quantity_base ?? 0);
        if ($current < $baseQty) {
            throw new \RuntimeException('Insufficient stock to allocate sale.');
        }

        DB::table('branch_stocks')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->update([
                'quantity_base' => $current - $baseQty,
                'updated_at' => $at,
            ]);

        DB::table('stock_movements')->insert([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'source_type' => 'sales',
            'source_id' => $saleId,
            'movement_type' => 'sale',
            'quantity_base' => -$baseQty,
            'created_at' => $at,
        ]);
    }
}
