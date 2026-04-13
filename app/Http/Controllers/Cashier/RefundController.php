<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $branchId = (int) $user->branch_id;

        // Admins have no branch_id — resolve branch from the sale itself
        $resolvedBranchId = $branchId ?: null;

        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'sale_item_id' => 'required|exists:sale_items,id',
            'product_id' => 'required|exists:products,id',
            'quantity_refunded' => 'required|integer|min:1',
            'refund_amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request, $branchId, $user) {
                $saleItem = SaleItem::findOrFail($request->sale_item_id);
                $sale = Sale::findOrFail($request->sale_id);

                // Resolve the effective branch — cashier uses their own, admin uses the sale's branch
                $effectiveBranchId = $branchId ?: (int) $sale->branch_id;

                // Cashiers can only refund their own branch's sales
                if ($branchId && (int) $sale->branch_id !== $branchId) {
                    throw new \Exception('Unauthorized: sale does not belong to your branch.');
                }

                // Block refunds for credit sales
                if (strtolower($sale->payment_method) === 'credit') {
                    throw new \Exception('Refunds are not allowed for credit sales.');
                }

                // Check available quantity
                $totalRefunded = Refund::where('sale_item_id', $request->sale_item_id)
                    ->where('status', 'approved')
                    ->sum('quantity_refunded');

                if ($totalRefunded + $request->quantity_refunded > $saleItem->quantity) {
                    throw new \Exception('Cannot refund more items than were sold.');
                }

                // Create refund — cashier_id tracks who processed it
                Refund::create([
                    'sale_id' => $request->sale_id,
                    'sale_item_id' => $request->sale_item_id,
                    'product_id' => $request->product_id,
                    'cashier_id' => $user->id,
                    'quantity_refunded' => $request->quantity_refunded,
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->reason,
                    'status' => 'approved',
                    'notes' => $request->notes,
                ]);

                // Deduct refund amount from sale total (cash refunds only)
                if ($request->input('refund_type') !== 'replacement') {
                    $sale->total_amount = max(0, $sale->total_amount - $request->refund_amount);
                    $sale->save();
                }

                // Restore stock
                $product = Product::findOrFail($request->product_id);
                $service = app(\App\Services\InventoryService::class);

                $unitSaleItem = SaleItem::where('sale_id', (int) $request->sale_id)
                    ->where('product_id', (int) $request->product_id)
                    ->first(['unit_type_id']);

                $unitTypeId = (int) ($unitSaleItem?->unit_type_id ?? 0);
                if ($unitTypeId <= 0) {
                    throw new \RuntimeException('Cannot restore inventory: sale item unit not found.');
                }

                $baseQty = $service->convertToBaseQuantity((int) $product->id, $unitTypeId, (float) $request->quantity_refunded);
                $service->increaseStock($effectiveBranchId, (int) $product->id, $baseQty, 'adjustment', 'refunds', (int) $request->sale_id, now());

                // Update StockOut if present
                $stockOut = StockOut::where('sale_id', $request->sale_id)
                    ->where('product_id', $request->product_id)
                    ->first();

                if ($stockOut) {
                    $newQty = max(0, $stockOut->quantity - $request->quantity_refunded);
                    $newQty <= 0 ? $stockOut->delete() : $stockOut->update(['quantity' => $newQty]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Refund processed successfully.']);

        } catch (\Exception $e) {
            Log::error('Cashier refund error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
