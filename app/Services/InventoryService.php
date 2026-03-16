<?php

namespace App\Services;

use App\Models\BranchStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function convertToBaseQuantity(int $productId, int $unitTypeId, float $quantity): float
    {
        $factor = DB::table('product_unit_type')
            ->where('product_id', $productId)
            ->where('unit_type_id', $unitTypeId)
            ->value('conversion_factor');

        if (is_null($factor)) {
            throw new \RuntimeException('Unit conversion not configured for product.');
        }

        $factor = (float) $factor;
        if ($factor <= 0) {
            throw new \RuntimeException('Invalid conversion factor.');
        }

        return $quantity * $factor;
    }

    public function increaseStock(int $branchId, int $productId, float $quantityBase, string $movementType, ?string $sourceType = null, ?int $sourceId = null, $at = null): void
    {
        $at = $at ?: now();

        DB::transaction(function () use ($branchId, $productId, $quantityBase, $movementType, $sourceType, $sourceId, $at) {
            $stock = BranchStock::query()->firstOrCreate(
                ['branch_id' => $branchId, 'product_id' => $productId],
                ['quantity_base' => 0]
            );

            $stock->increment('quantity_base', $quantityBase);

            StockMovement::query()->create([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'movement_type' => $movementType,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'quantity_base' => $quantityBase,
                'created_at' => $at,
            ]);
        });
    }

    public function decreaseStock(int $branchId, int $productId, float $quantityBase, string $movementType, ?string $sourceType = null, ?int $sourceId = null, $at = null): void
    {
        $at = $at ?: now();

        DB::transaction(function () use ($branchId, $productId, $quantityBase, $movementType, $sourceType, $sourceId, $at) {
            $stock = BranchStock::query()
                ->where('branch_id', $branchId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            $current = (float) ($stock?->quantity_base ?? 0);
            if ($current < $quantityBase) {
                throw new \RuntimeException('Insufficient stock.');
            }

            if (!$stock) {
                $stock = BranchStock::query()->create([
                    'branch_id' => $branchId,
                    'product_id' => $productId,
                    'quantity_base' => 0,
                ]);
            }

            $stock->decrement('quantity_base', $quantityBase);

            StockMovement::query()->create([
                'product_id' => $productId,
                'branch_id' => $branchId,
                'movement_type' => $movementType,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'quantity_base' => -$quantityBase,
                'created_at' => $at,
            ]);
        });
    }

    public function availableStockBase(int $productId, int $branchId): float
    {
        return (float) (BranchStock::query()
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->value('quantity_base') ?? 0);
    }

    public function availableStockInUnit(int $productId, int $branchId, int $unitTypeId): float
    {
        $base = $this->availableStockBase($productId, $branchId);

        $factor = DB::table('product_unit_type')
            ->where('product_id', $productId)
            ->where('unit_type_id', $unitTypeId)
            ->value('conversion_factor');

        if (is_null($factor) || (float) $factor <= 0) {
            return 0;
        }

        return $base / (float) $factor;
    }
}
