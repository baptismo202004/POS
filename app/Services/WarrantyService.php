<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\WarrantyRecord;
use Carbon\Carbon;

class WarrantyService
{
    /**
     * Create warranty records when a purchase is saved.
     *
     * Called once per purchase item after the item and its serials are persisted.
     *
     * @param  PurchaseItem  $purchaseItem
     * @param  array<int, array{serial_number: string, warranty_expiry: string|null}>  $serials
     * @param  string  $purchaseDate  Y-m-d
     */
    public function createForPurchase(PurchaseItem $purchaseItem, array $serials, string $purchaseDate): void
    {
        $product = Product::find($purchaseItem->product_id);
        if (! $product) {
            return;
        }

        $warrantyType    = $product->warranty_type ?? 'none';
        $coverageMonths  = (int) ($product->warranty_coverage_months ?? 0);

        // No warranty configured on the product — nothing to record
        if ($warrantyType === 'none' || $coverageMonths <= 0) {
            return;
        }

        $categoryType = $product->category?->category_type ?? 'non_electronic';

        if ($categoryType === 'electronic_with_serial' && ! empty($serials)) {
            // One warranty record per serial unit
            foreach ($serials as $s) {
                $serial = ProductSerial::where('serial_number', $s['serial_number'])
                    ->where('product_id', $product->id)
                    ->first();

                $expiryDate = ! empty($s['warranty_expiry'])
                    ? $s['warranty_expiry']
                    : Carbon::parse($purchaseDate)->addMonths($coverageMonths)->toDateString();

                WarrantyRecord::create([
                    'product_id'       => $product->id,
                    'product_serial_id' => $serial?->id,
                    'purchase_id'      => $purchaseItem->purchase_id,
                    'purchase_item_id' => $purchaseItem->id,
                    'warranty_type'    => $warrantyType,
                    'coverage_months'  => $coverageMonths,
                    'start_date'       => $purchaseDate,
                    'expiry_date'      => $expiryDate,
                    'status'           => 'active',
                    'quantity'         => 1,
                ]);
            }
        } else {
            // Non-serial or electronic_without_serial — one record for the whole batch
            $expiryDate = Carbon::parse($purchaseDate)->addMonths($coverageMonths)->toDateString();

            WarrantyRecord::create([
                'product_id'       => $product->id,
                'product_serial_id' => null,
                'purchase_id'      => $purchaseItem->purchase_id,
                'purchase_item_id' => $purchaseItem->id,
                'warranty_type'    => $warrantyType,
                'coverage_months'  => $coverageMonths,
                'start_date'       => $purchaseDate,
                'expiry_date'      => $expiryDate,
                'status'           => 'active',
                'quantity'         => (float) $purchaseItem->quantity,
            ]);
        }
    }

    /**
     * Activate / update warranty records when a sale item is completed.
     *
     * For serial items: links the existing purchase-time warranty record to the sale.
     * For non-serial items: creates a new warranty record activated at sale date.
     *
     * @param  SaleItem  $saleItem
     * @param  int       $branchId
     * @param  int|null  $customerId
     * @param  ProductSerial|null  $serial   Resolved serial (if any)
     */
    public function activateForSale(
        SaleItem $saleItem,
        int $branchId,
        ?int $customerId,
        ?ProductSerial $serial = null
    ): void {
        $product = Product::with('category')->find($saleItem->product_id);
        if (! $product) {
            return;
        }

        $categoryType   = $product->category?->category_type ?? 'non_electronic';
        $isElectronic   = in_array(strtolower(trim($categoryType)), ['electronic_with_serial', 'electronic_without_serial'], true);
        $warrantyType   = $product->warranty_type ?? 'none';
        $coverageMonths = (int) ($saleItem->warranty_months ?? $product->warranty_coverage_months ?? 0);

        // Only create a warranty record for electronic products
        if (! $isElectronic) {
            return;
        }

        $saleDate   = $saleItem->sale?->created_at?->toDateString() ?? now()->toDateString();
        $expiryDate = $coverageMonths > 0
            ? Carbon::parse($saleDate)->addMonths($coverageMonths)->toDateString()
            : null;

        // Resolve warranty_type — fall back to 'shop' for electronic items with no type set
        $recordWarrantyType = ($warrantyType !== 'none') ? $warrantyType : 'shop';

        if ($serial) {
            // Try to find the existing purchase-time warranty record for this serial
            $record = WarrantyRecord::where('product_serial_id', $serial->id)
                ->whereNull('sale_id')
                ->first();

            if ($record) {
                $record->update([
                    'sale_id'         => $saleItem->sale_id,
                    'sale_item_id'    => $saleItem->id,
                    'customer_id'     => $customerId,
                    'branch_id'       => $branchId,
                    'start_date'      => $saleDate,
                    'expiry_date'     => $expiryDate,
                    'coverage_months' => $coverageMonths,
                    'warranty_type'   => $recordWarrantyType,
                    'status'          => 'active',
                ]);
            } else {
                WarrantyRecord::create([
                    'product_id'        => $product->id,
                    'product_serial_id' => $serial->id,
                    'purchase_id'       => $serial->purchase_id,
                    'sale_id'           => $saleItem->sale_id,
                    'sale_item_id'      => $saleItem->id,
                    'customer_id'       => $customerId,
                    'branch_id'         => $branchId,
                    'warranty_type'     => $recordWarrantyType,
                    'coverage_months'   => $coverageMonths,
                    'start_date'        => $saleDate,
                    'expiry_date'       => $expiryDate,
                    'status'            => 'active',
                    'quantity'          => 1,
                ]);
            }
        } else {
            // Non-serial electronic item — always create a new record per sale item
            WarrantyRecord::create([
                'product_id'      => $product->id,
                'sale_id'         => $saleItem->sale_id,
                'sale_item_id'    => $saleItem->id,
                'customer_id'     => $customerId,
                'branch_id'       => $branchId,
                'warranty_type'   => $recordWarrantyType,
                'coverage_months' => $coverageMonths,
                'start_date'      => $saleDate,
                'expiry_date'     => $expiryDate,
                'status'          => 'active',
                'quantity'        => (float) $saleItem->quantity,
            ]);
        }
    }

    /**
     * Expire all warranty records whose expiry_date has passed.
     * Intended to be called from a scheduled command.
     */
    public function syncExpiredRecords(): int
    {
        return WarrantyRecord::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->toDateString())
            ->update(['status' => 'expired']);
    }
}
