<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Purchase;
use App\Models\StockIn;
use App\Models\StockInsHead;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('stock_ins_head')
                ->join('branches', 'stock_ins_head.branch_id', '=', 'branches.id')
                ->leftJoin('purchases', 'stock_ins_head.purchase_id', '=', 'purchases.id')
                ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->leftJoin('users', 'stock_ins_head.created_by', '=', 'users.id')
                ->select([
                    'stock_ins_head.*',
                    'branches.branch_name',
                    'purchases.reference_number as purchase_reference',
                    'suppliers.supplier_name',
                    'users.name as created_by_name'
                ]);
            
            // Apply sorting if requested
            if ($request->has('sort')) {
                $direction = $request->get('direction', 'asc');
                switch ($request->get('sort')) {
                    case 'reference':
                        $query->orderBy('stock_ins_head.reference_number', $direction);
                        break;
                    case 'branch':
                        $query->orderBy('branches.branch_name', $direction);
                        break;
                    case 'date':
                        $query->orderBy('stock_ins_head.stock_in_date', $direction);
                        break;
                    default:
                        $query->orderBy('stock_ins_head.created_at', 'desc');
                }
            } else {
                $query->orderBy('stock_ins_head.created_at', 'desc');
            }
            
            $stockIns = $query->paginate(15);
            
            return view('SuperAdmin.stockin.index', compact('stockIns'));
        } catch (\Exception $e) {
            Log::error('Error loading Stock In index page: ' . $e->getMessage());
            return response()->view('errors.500', ['message' => 'Error loading stock records: ' . $e->getMessage()], 500);
        }
    }

    public function stockInCreate()
    {
        $purchasedSub = DB::table('purchase_items')
            ->select('purchase_id', DB::raw('SUM(quantity) as total_purchased'))
            ->groupBy('purchase_id');

        $stockedInSub = DB::table('stock_ins')
            ->select('purchase_id', DB::raw('SUM(quantity) as total_stocked'))
            ->whereNotNull('purchase_id')
            ->groupBy('purchase_id');

        $purchases = DB::table('purchases')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->joinSub($purchasedSub, 'pi_totals', 'pi_totals.purchase_id', '=', 'purchases.id')
            ->leftJoinSub($stockedInSub, 'si_totals', 'si_totals.purchase_id', '=', 'purchases.id')
            ->select(
                'purchases.id',
                'purchases.reference_number',
                'purchases.purchase_date',
                'suppliers.supplier_name as supplier_name',
                DB::raw('pi_totals.total_purchased - COALESCE(si_totals.total_stocked, 0) as remaining_quantity')
            )
            ->orderBy('purchases.purchase_date', 'desc')
            ->get();

        $branches = Branch::orderBy('branch_name')->get();

        return view('SuperAdmin.stockin.create', compact('purchases', 'branches'));
    }

    public function stockInProductsByPurchase(\App\Models\Purchase $purchase)
    {
        try {
            $purchaseItems = $purchase->items()->with([
                'product.unitTypes' => function ($q) {
                    $q->withPivot('conversion_factor', 'is_base');
                },
                'product.category',
                'unitType',
            ])->get();

            $items = $purchaseItems->map(function ($item) {
                $purchaseUnitTypeId = (int) ($item->unit_type_id ?? 0);
                $purchaseUnitName = $item->unitType?->unit_name;

                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $item->product_id)
                    ->where('unit_type_id', $purchaseUnitTypeId)
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;

                $alreadyStockedBase = (float) StockIn::where('purchase_id', (int) $item->purchase_id)
                    ->where('product_id', (int) $item->product_id)
                    ->sum('quantity');

                $purchasedQty = (float) ($item->quantity ?? 0);
                $purchasedBase = $purchasedQty * $purchaseFactor;
                $remainingBase = (float) $purchasedBase - (float) $alreadyStockedBase;
                if ($remainingBase < 0) $remainingBase = 0;

                $primaryPurchased = (float) ($item->primary_quantity ?? 0);
                $primaryRemaining = $primaryPurchased;
                if ($primaryPurchased > 0 && (float) ($item->quantity ?? 0) > 0) {
                    $ratio = $remainingBase / (float) ($item->quantity ?? 0);
                    $primaryRemaining = $primaryPurchased * $ratio;
                }

                Log::info("[STOCK_IN_PRODUCTS] Product ID: {$item->product_id}, Remaining: {$remainingBase}, Unit Types: " . json_encode($item->product->unitTypes ?? []));

                $unitTypes = $item->product->unitTypes ?? collect();

                $unitTypesPayload = collect($unitTypes)->map(function ($ut) {
                    return [
                        'id' => $ut->id,
                        'unit_name' => $ut->unit_name,
                        'conversion_factor' => isset($ut->pivot->conversion_factor) ? (float) $ut->pivot->conversion_factor : 1.0,
                        'is_base' => isset($ut->pivot->is_base) ? (bool) $ut->pivot->is_base : false,
                    ];
                })->values();

                $baseUnit = collect($unitTypes)->firstWhere('pivot.is_base', true);
                $baseUnitName = $baseUnit?->unit_name;

                $conversionParts = [];
                if ($baseUnitName) {
                    foreach ($unitTypesPayload as $u) {
                        if (!empty($u['is_base'])) {
                            continue;
                        }
                        $f = (float) ($u['conversion_factor'] ?? 0);
                        if ($f > 0) {
                            $fText = rtrim(rtrim(number_format($f, 6, '.', ''), '0'), '.');
                            $conversionParts[] = '1 ' . $u['unit_name'] . ' × ' . $fText . ' ' . $baseUnitName;
                        }
                    }
                }
                $conversionSummary = implode(', ', $conversionParts);

                $result = [
                    'product_id' => $item->product_id,
                    'product' => $item->product,
                    'category_type' => $item->product?->category?->category_type ?? 'non_electronic',
                    'quantity' => $remainingBase,
                    'purchased_quantity' => $purchasedQty,
                    'remaining_quantity' => $remainingBase,

                    'purchase_unit_type_id' => $purchaseUnitTypeId,
                    'purchase_unit_name' => $purchaseUnitName,
                    'purchase_factor' => $purchaseFactor,
                    'base_unit_name' => $baseUnitName,

                    'purchased_qty' => $purchasedQty,
                    'purchased_base' => $purchasedBase,
                    'remaining_base' => $remainingBase,
                    'conversion_summary' => $conversionSummary,

                    'base_purchased_quantity' => $purchasedBase,
                    'base_remaining_quantity' => $remainingBase,
                    'unit_price' => $item->unit_cost,
                    'unit_types' => $unitTypesPayload,
                    'unit_type' => $item->unitType,
                    'primary_unit_name' => $item->unitType ? $item->unitType->unit_name : null,
                    'base_unit_type' => $baseUnit,
                    'base_unit_type_id' => $baseUnit?->id,
                ];

                Log::info("[STOCK_IN_PRODUCTS] Result for item {$item->product_id}: " . json_encode($result));
                return $result;
            })->values();

            Log::info("[STOCK_IN_PRODUCTS] Final items data: " . json_encode($items->toArray()));

            return response()->json([
                'items' => $items,
            ]);
        } catch (\Exception $e) {
            Log::error("[STOCK_IN_PRODUCTS_BY_PURCHASE] Error: " . $e->getMessage());
            return response()->json(['items' => [], 'error' => $e->getMessage()]);
        }
    }

    public function purchaseProductSerials(\App\Models\Purchase $purchase, Product $product)
    {
        $serials = ProductSerial::query()
            ->where('purchase_id', $purchase->id)
            ->where('product_id', $product->id)
            ->where('status', 'purchased')
            ->orderBy('id')
            ->get(['id', 'serial_number', 'warranty_expiry_date']);

        return response()->json([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'serials' => $serials,
        ]);
    }

    public function latestUnitPrices(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        $productId = (int) $data['product_id'];
        $branchId = (int) $data['branch_id'];

        $latest = StockIn::with(['unitPrices'])
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->whereHas('unitPrices')
            ->orderByDesc('id')
            ->first();

        $unitPrices = [];
        if ($latest) {
            foreach ($latest->unitPrices as $up) {
                $uId = (int) ($up->unit_type_id ?? 0);
                $p = (float) ($up->price ?? 0);
                if ($uId > 0 && $p > 0) {
                    $unitPrices[(string) $uId] = $p;
                }
            }
        }

        return response()->json([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'unit_prices' => $unitPrices,
        ]);
    }

    public function stockInStore(Request $request)
    {
        try {
            $data = $request->validate([
                'purchase_id' => 'required|exists:purchases,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.unit_type_id' => 'required|exists:unit_types,id',
                'items.*.base_unit_type_id' => 'nullable|exists:unit_types,id',
                'items.*.quantity' => 'required|numeric|min:0.0001',
                'items.*.new_price' => 'required|numeric|min:0',
                'items.*.unit_prices' => 'required|array|min:1',
                'items.*.unit_prices.*' => 'required|numeric|min:0',
                'items.*.unit_quantities' => 'nullable|array',
                'items.*.unit_quantities.*' => 'nullable|numeric|min:0',
                'items.*.original_price' => 'required|numeric|min:0',
                'items.*.branch_id' => 'required|exists:branches,id',
                'items.*.serial_ids' => 'nullable|array',
                'items.*.serial_ids.*' => 'integer|exists:product_serials,id',
            ]);

            foreach ($data['items'] as $item) {
                $originalPrice = (float) ($item['original_price'] ?? 0);

                // Baseline is purchase cost only (ignore existing stock-in base price)
                // `original_price` is the purchase unit cost, so convert it to base-unit cost first.
                $purchaseUnitTypeId = (int) (DB::table('purchase_items')
                    ->where('purchase_id', (int) $data['purchase_id'])
                    ->where('product_id', (int) $item['product_id'])
                    ->value('unit_type_id') ?? 0);
                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $item['product_id'])
                    ->where('unit_type_id', $purchaseUnitTypeId)
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;

                $baseReferencePrice = $originalPrice / $purchaseFactor;
                if (!is_finite($baseReferencePrice) || $baseReferencePrice < 0) {
                    $baseReferencePrice = 0;
                }

                $baseUnitTypeId = isset($item['base_unit_type_id']) ? (int) $item['base_unit_type_id'] : (int) ($item['unit_type_id'] ?? 0);

                $unitPrices = $item['unit_prices'] ?? [];

                $pricePoints = [];

                foreach ($unitPrices as $unitTypeId => $unitPrice) {
                    $unitPrice = (float) $unitPrice;
                    $unitTypeId = (int) $unitTypeId;

                    if ($unitPrice <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'All unit prices must be greater than zero.',
                        ], 422);
                    }

                    $factor = (float) (DB::table('product_unit_type')
                        ->where('product_id', (int) $item['product_id'])
                        ->where('unit_type_id', $unitTypeId)
                        ->value('conversion_factor') ?? 1);

                    $factor = $factor > 0 ? $factor : 1;
                    $baseEquiv = $unitPrice / $factor;
                    $pricePoints[] = ['factor' => $factor, 'base_equiv' => $baseEquiv];

                    $minAllowed = $baseReferencePrice * $factor;
                    if ($unitPrice < $minAllowed) {
                        return response()->json([
                            'success' => false,
                            'message' => 'New Price must not be smaller than the base price reference for the selected unit type.',
                        ], 422);
                    }
                }
            }

            $items = $data['items'];
            $purchaseId = $data['purchase_id'];
            $stockIns = [];

            $purchase = \App\Models\Purchase::with('items')->findOrFail($purchaseId);

            $itemsByProduct = collect($items)->groupBy('product_id');
            foreach ($itemsByProduct as $productId => $rows) {
                $purchaseItem = $purchase->items->firstWhere('product_id', (int) $productId);
                if (! $purchaseItem) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid product found in the stock-in request.',
                    ], 422);
                }

                $product = Product::with('category')->find((int) $productId);
                $categoryType = $product?->category?->category_type ?? 'non_electronic';

                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) ($purchaseItem?->unit_type_id ?? 0))
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;

                $purchasedBase = (float) ($purchaseItem->quantity ?? 0) * $purchaseFactor;

                $alreadyStockedBase = (float) StockIn::where('purchase_id', (int) $purchaseId)
                    ->where('product_id', (int) $productId)
                    ->sum('quantity');

                $remainingBase = (float) $purchasedBase - (float) $alreadyStockedBase;
                if ($remainingBase < 0) $remainingBase = 0;

                $requestedBase = 0.0;
                foreach ($rows as $row) {
                    $unitQuantities = $row['unit_quantities'] ?? [];
                    if (is_array($unitQuantities) && count($unitQuantities) > 0) {
                        foreach ($unitQuantities as $unitTypeId => $enteredQty) {
                            $enteredQty = (float) $enteredQty;
                            $unitTypeId = (int) $unitTypeId;
                            if ($enteredQty <= 0) continue;

                            $factor = (float) (DB::table('product_unit_type')
                                ->where('product_id', (int) $productId)
                                ->where('unit_type_id', $unitTypeId)
                                ->value('conversion_factor') ?? 1);
                            if ($factor <= 0) $factor = 1;

                            $requestedBase += ($enteredQty * $factor);
                        }
                        continue;
                    }

                    $requestedBase += (float) ($row['quantity'] ?? 0);
                }
                if ($requestedBase > $remainingBase) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Total Stock-In Qty for this product exceeds remaining to stock.',
                    ], 422);
                }

                // For electronic_with_serial, ensure selected serials match requested quantity.
                if ($categoryType === 'electronic_with_serial') {
                    $requestedPieces = (int) round($requestedBase);
                    if ($requestedPieces <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid stock-in quantity for serial-tracked product.',
                        ], 422);
                    }

                    $selectedSerialIds = collect($rows)
                        ->pluck('serial_ids')
                        ->filter(fn($v) => is_array($v))
                        ->flatten()
                        ->map(fn($v) => (int) $v)
                        ->filter(fn($v) => $v > 0)
                        ->unique()
                        ->values();

                    if ($selectedSerialIds->count() !== $requestedPieces) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected serials must match stock-in quantity for Electronic (with serial) products.',
                        ], 422);
                    }

                    $validSerialCount = ProductSerial::query()
                        ->whereIn('id', $selectedSerialIds)
                        ->where('purchase_id', (int) $purchaseId)
                        ->where('product_id', (int) $productId)
                        ->where('status', 'purchased')
                        ->count();

                    if ($validSerialCount !== $selectedSerialIds->count()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'One or more selected serials are not available for stock-in.',
                        ], 422);
                    }
                }
            }

            // Create stock_ins_head record
            $stockInHead = StockInsHead::create([
                'branch_id' => $items[0]['branch_id'], // Use first item's branch
                'purchase_id' => $purchaseId,
                'stock_in_date' => now(),
                'reference_number' => 'STK-' . date('Y') . '-' . str_pad(StockInsHead::count() + 1, 4, '0', STR_PAD_LEFT),
                'total_quantity' => collect($items)->sum(function($item) {
                    return (float) ($item['quantity'] ?? 0);
                }),
                'status' => 'completed',
                'created_by' => Auth::id(),
            ]);

            foreach ($items as $item) {
                $originalPrice = (float) ($item['original_price'] ?? 0);

                // Baseline is purchase cost only (ignore existing stock-in base price)
                // `original_price` is the purchase unit cost, so convert it to base-unit cost first.
                $purchaseUnitTypeId = (int) (DB::table('purchase_items')
                    ->where('purchase_id', (int) $purchaseId)
                    ->where('product_id', (int) $item['product_id'])
                    ->value('unit_type_id') ?? 0);
                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $item['product_id'])
                    ->where('unit_type_id', $purchaseUnitTypeId)
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;

                $baseReferencePrice = $originalPrice / $purchaseFactor;
                if (!is_finite($baseReferencePrice) || $baseReferencePrice < 0) {
                    $baseReferencePrice = 0;
                }

                $baseUnitTypeId = isset($item['base_unit_type_id']) ? (int) $item['base_unit_type_id'] : (int) ($item['unit_type_id'] ?? 0);

                $unitQuantities = $item['unit_quantities'] ?? [];
                $unitPrices = $item['unit_prices'] ?? [];

                if (!is_array($unitQuantities) || count($unitQuantities) === 0) {
                    Log::info('[STOCK_IN] Processing item (legacy)', [
                        'product_id' => $item['product_id'],
                        'branch_id' => $item['branch_id'],
                        'quantity' => $item['quantity'],
                        'new_price' => $item['new_price'],
                    ]);

                    $qtyBase = (float) ($item['quantity'] ?? 0);
                    $qtyBaseRounded = (float) (int) round($qtyBase);
                    $stockIn = StockIn::create([
                        'product_id' => $item['product_id'],
                        'branch_id' => $item['branch_id'],
                        'purchase_id' => $purchaseId,
                        'unit_type_id' => $baseUnitTypeId ?: $item['unit_type_id'],
                        'quantity' => (int) $qtyBaseRounded,
                        'price' => $baseReferencePrice,
                    ]);

                    // Reflect stock in branch_stocks (authoritative stock count used by UI)
                    BranchStock::query()
                        ->firstOrCreate(
                            ['branch_id' => (int) $item['branch_id'], 'product_id' => (int) $item['product_id']],
                            ['quantity_base' => 0]
                        )
                        ->increment('quantity_base', $qtyBaseRounded);

                    foreach ($unitPrices as $unitTypeId => $unitPrice) {
                        $stockIn->unitPrices()->updateOrCreate(
                            ['unit_type_id' => (int) $unitTypeId],
                            ['price' => (float) $unitPrice]
                        );
                    }

                    $stockIns[] = $stockIn->id;
                    continue;
                }

                foreach ($unitQuantities as $unitTypeId => $enteredQty) {
                    $enteredQty = (float) $enteredQty;
                    $unitTypeId = (int) $unitTypeId;

                    if ($enteredQty <= 0) {
                        continue;
                    }

                    $factor = (float) (DB::table('product_unit_type')
                        ->where('product_id', (int) $item['product_id'])
                        ->where('unit_type_id', $unitTypeId)
                        ->value('conversion_factor') ?? 1);
                    if ($factor <= 0) $factor = 1;

                    $qtyBase = $enteredQty * $factor;
                    $qtyBaseRounded = (float) (int) round($qtyBase);
                    $priceForUnit = array_key_exists($unitTypeId, $unitPrices) ? (float) $unitPrices[$unitTypeId] : (float) ($item['new_price'] ?? 0);

                    Log::info('[STOCK_IN] Processing unit', [
                        'product_id' => $item['product_id'],
                        'branch_id' => $item['branch_id'],
                        'unit_type_id' => $unitTypeId,
                        'entered_qty' => $enteredQty,
                        'qty_base' => $qtyBase,
                        'price' => $priceForUnit,
                    ]);

                    $stockIn = StockIn::create([
                        'product_id' => $item['product_id'],
                        'branch_id' => $item['branch_id'],
                        'purchase_id' => $purchaseId,
                        'unit_type_id' => $unitTypeId,
                        'quantity' => (int) $qtyBaseRounded,
                        'price' => $priceForUnit,
                    ]);

                    // Reflect stock in branch_stocks (authoritative stock count used by UI)
                    BranchStock::query()
                        ->firstOrCreate(
                            ['branch_id' => (int) $item['branch_id'], 'product_id' => (int) $item['product_id']],
                            ['quantity_base' => 0]
                        )
                        ->increment('quantity_base', $qtyBaseRounded);

                    foreach ($unitPrices as $upUnitTypeId => $unitPrice) {
                        $stockIn->unitPrices()->updateOrCreate(
                            ['unit_type_id' => (int) $upUnitTypeId],
                            ['price' => (float) $unitPrice]
                        );
                    }

                    $stockIns[] = $stockIn->id;
                }

                // Update selected serials for this item row (if any)
                $serialIds = $item['serial_ids'] ?? [];
                if (is_array($serialIds) && count($serialIds) > 0) {
                    ProductSerial::query()
                        ->whereIn('id', array_map('intval', $serialIds))
                        ->where('purchase_id', (int) $purchaseId)
                        ->where('product_id', (int) $item['product_id'])
                        ->where('status', 'purchased')
                        ->update([
                            'branch_id' => (int) $item['branch_id'],
                            'status' => 'in_stock',
                        ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => count($stockIns) . ' items added successfully. Reference: ' . $stockInHead->reference_number,
                'stock_in_ids' => $stockIns,
                'stock_in_head_id' => $stockInHead->id,
                'redirect_url' => route('superadmin.stockin.transaction', $stockInHead->id),
            ]);
        } catch (\Exception $e) {
            Log::error('[STOCK_IN] Error adding stock', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show stock-in details
     */
    public function show($stockMovement)
    {
        try {
            // Get the specific stock movement and related details
            $stockMovement = DB::table('stock_movements')
                ->join('products', 'stock_movements.product_id', '=', 'products.id')
                ->join('branches', 'stock_movements.branch_id', '=', 'branches.id')
                ->leftJoin('purchases', function($join) {
                    $join->on('stock_movements.source_type', '=', DB::raw("'purchases'"))
                         ->on('stock_movements.source_id', '=', 'purchases.id');
                })
                ->where('stock_movements.id', $stockMovement)
                ->select([
                    'stock_movements.*',
                    'products.product_name',
                    'products.barcode',
                    'branches.branch_name',
                    'purchases.reference_number as purchase_reference',
                    'purchases.purchase_date',
                ])
                ->first();

            if (!$stockMovement) {
                return redirect()->route('superadmin.stockin.index')
                    ->with('error', 'Stock-in record not found.');
            }

            // Get all related stock movements from the same purchase/branch combination
            $relatedMovements = DB::table('stock_movements')
                ->join('products', 'stock_movements.product_id', '=', 'products.id')
                ->where('stock_movements.source_type', $stockMovement->source_type)
                ->where('stock_movements.source_id', $stockMovement->source_id)
                ->where('stock_movements.branch_id', $stockMovement->branch_id)
                ->where('stock_movements.movement_type', $stockMovement->movement_type)
                ->select([
                    'stock_movements.*',
                    'products.product_name',
                    'products.barcode',
                ])
                ->orderBy('stock_movements.created_at', 'desc')
                ->get();

            // Get purchase details if applicable
            $purchaseDetails = null;
            if ($stockMovement->source_type === 'purchases') {
                $purchaseDetails = DB::table('purchases')
                    ->where('id', $stockMovement->source_id)
                    ->first();
            }

            return view('SuperAdmin.stockin.show', compact('stockMovement', 'relatedMovements', 'purchaseDetails'));
            
        } catch (\Exception $e) {
            Log::error('Error loading Stock In details: ' . $e->getMessage());
            return redirect()->route('superadmin.stockin.index')
                ->with('error', 'Error loading stock-in details: ' . $e->getMessage());
        }
    }

    /**
     * Show recent stock transaction details
     */
    public function showTransaction($stockInHeadId)
    {
        try {
            // Get the stock in head record with related data
            $stockInHead = StockInsHead::with([
                'branch',
                'purchase.supplier',
                'creator'
            ])->findOrFail($stockInHeadId);

            // Get stock items directly from stock_ins table for this transaction
            $stockItems = DB::table('stock_ins')
                ->join('products', 'stock_ins.product_id', '=', 'products.id')
                ->leftJoin('stock_in_unit_prices', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                ->leftJoin('unit_types', 'stock_in_unit_prices.unit_type_id', '=', 'unit_types.id')
                ->where('stock_ins.purchase_id', $stockInHead->purchase_id)
                ->where('stock_ins.branch_id', $stockInHead->branch_id)
                ->select([
                    'stock_ins.id',
                    'stock_ins.product_id',
                    'stock_ins.quantity',
                    'stock_ins.price',
                    'stock_ins.created_at',
                    'products.product_name',
                    'unit_types.unit_name',
                    'stock_in_unit_prices.price as unit_price'
                ])
                ->orderBy('stock_ins.created_at', 'desc')
                ->get();

            // Group by product and format the data
            $formattedMovements = $stockItems->groupBy('product_id')->map(function ($items, $productId) {
                $firstItem = $items->first();
                
                // Get unit prices for this product
                $unitPrices = $items->filter(function ($item) {
                    return !is_null($item->unit_name) && !is_null($item->unit_price);
                })->mapWithKeys(function ($item) {
                    return [
                        $item->unit_name => number_format($item->unit_price, 2)
                    ];
                });

                // Calculate total quantity for this product
                $totalQuantity = $items->sum('quantity');

                return [
                    'id' => $firstItem->id,
                    'product_name' => $firstItem->product_name,
                    'quantity' => $totalQuantity,
                    'unit_prices' => $unitPrices,
                    'created_at' => $firstItem->created_at
                ];
            });

            return view('SuperAdmin.stockin.transaction', compact('stockInHead', 'stockItems', 'formattedMovements'));
            
        } catch (\Exception $e) {
            Log::error('Error loading stock transaction details: ' . $e->getMessage());
            return redirect()->route('superadmin.stockin.index')
                ->with('error', 'Error loading transaction details: ' . $e->getMessage());
        }
    }

    // Legacy methods for backward compatibility
    public function create()
    {
        return $this->stockInCreate();
    }

    public function store(Request $request)
    {
        return $this->stockInStore($request);
    }

    public function getProductsByPurchase(Purchase $purchase)
    {
        return $this->stockInProductsByPurchase($purchase);
    }
}
