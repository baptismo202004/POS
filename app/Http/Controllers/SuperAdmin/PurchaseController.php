<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ProductType;
use App\Models\Purchase;
use App\Models\Refund;
use App\Models\SaleItem;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with(['items.product'])
            ->latest('purchase_date')
            ->paginate(100);

        return view('SuperAdmin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->with('category')->get();
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        $product_types = ProductType::all();
        $unit_types = UnitType::all();
        $suppliers = Supplier::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();

        return view('SuperAdmin.purchases.create', compact('products', 'brands', 'categories', 'product_types', 'unit_types', 'suppliers', 'branches'));
    }

    public function getProductUnitTypes(Product $product)
    {
        $productUnits = $product->unitTypes()
            ->select('unit_types.id', 'unit_types.unit_name')
            ->withPivot('conversion_factor', 'is_base')
            ->get();

        $units = $productUnits->map(function ($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->unit_name,
                'conversion_factor' => isset($unit->pivot->conversion_factor) ? (float) $unit->pivot->conversion_factor : 1.0,
                'is_base' => isset($unit->pivot->is_base) ? (bool) $unit->pivot->is_base : false,
            ];
        });

        return response()->json([
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic purchase information validation
            'purchase_date' => 'required|date',

            // CONDITION 4: Supplier Requirement - Supplier must be selected and must exist
            'supplier_id' => 'required|exists:suppliers,id',

            'reference_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('purchases', 'reference_number')->where(function ($query) {
                    return $query->whereNotNull('reference_number')->where('reference_number', '!=', '');
                }),
            ],
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',

            // Product item validations
            'items.*.is_new' => 'nullable|boolean',
            'items.*.product_id' => 'required_if:items.*.is_new,null|exists:products,id',
            'items.*.product_name' => 'required_if:items.*.is_new,1|string|max:255|unique:products,product_name',
            'items.*.primary_quantity' => 'required|numeric|min:1',
            'items.*.unit_type_id' => 'required|exists:unit_types,id',
            'items.*.cost' => 'required|numeric|min:0',

            // Serial number validations
            'items.*.serials' => 'nullable|array',

            // COMBINED CONDITIONS 2 & 3: Serial Number Validation
            // CONDITION 2: Serial Number Uniqueness - Must be unique within form and database
            // CONDITION 3: Serial Number Format Validation - Length and character constraints
            'items.*.serials.*.serial_number' => [
                'required_with:items.*.serials',
                'string',
                'min:8', // Minimum 8 characters
                'max:30', // Maximum 30 characters
                'regex:/^[A-Z0-9-]+$/i', // Only alphanumeric and hyphens allowed
                'distinct', // Ensures uniqueness within current form
                function ($attribute, $value, $fail) {
                    // Check uniqueness in database (CONDITION 2)
                    if (ProductSerial::where('serial_number', $value)->exists()) {
                        $fail('Duplicate serial number detected: '.$value);
                    }
                },
            ],

            'items.*.serials.*.warranty_expiry' => 'nullable|date',
        ]);

        $purchaseId = null;
        DB::transaction(function () use ($validated, &$purchaseId) {
            $totalCost = 0;
            $purchaseItemsData = [];

            $serialsByIndex = [];

            // Preload product category types for validation logic
            $productIds = collect($validated['items'])
                ->filter(fn ($it) => empty($it['is_new']) && ! empty($it['product_id']))
                ->pluck('product_id')
                ->unique()
                ->values();

            $products = Product::with('category')
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            // Preload conversion factors for product/unit combos so we can validate serial counts properly
            $unitTypeIds = collect($validated['items'])
                ->pluck('unit_type_id')
                ->filter()
                ->unique()
                ->values();

            $conversionMap = DB::table('product_unit_type')
                ->whereIn('product_id', $productIds)
                ->whereIn('unit_type_id', $unitTypeIds)
                ->get()
                ->reduce(function ($carry, $row) {
                    $carry["{$row->product_id}_{$row->unit_type_id}"] = (float) $row->conversion_factor;

                    return $carry;
                }, []);

            foreach ($validated['items'] as $item) {
                $productId = null;
                if (! empty($item['is_new'])) {
                    $newProduct = Product::create([
                        'product_name' => $item['product_name'],
                        'barcode' => 'BC-'.uniqid(),
                        'status' => 'active',
                        'tracking_type' => 'none',
                        'warranty_type' => 'none',
                    ]);
                    $newProduct->unitTypes()->sync([
                        $item['unit_type_id'] => [
                            'conversion_factor' => 1.0,
                            'is_base' => true,
                        ],
                    ]);
                    $productId = $newProduct->id;
                } else {
                    $productId = $item['product_id'];
                }

                $categoryType = null;
                if (! empty($productId) && isset($products[$productId])) {
                    $categoryType = $products[$productId]?->category?->category_type;
                }

                $primaryQty = (float) $item['primary_quantity'];

                $conversionFactor = 1.0;
                if (! empty($item['unit_type_id']) && ! empty($productId)) {
                    $key = "{$productId}_{$item['unit_type_id']}";
                    $conversionFactor = $conversionMap[$key] ?? 1.0;
                }

                $requiredSerialCount = (int) round($primaryQty * $conversionFactor);

                // CONDITION 1: Number of serials MUST equal quantity
                // Check if product requires serial numbers (electronic with serial)
                $requiresSerials = $categoryType === 'electronic_with_serial';
                $serials = $item['serials'] ?? [];

                if (! is_array($serials)) {
                    $serials = [];
                }

                // If product requires serials, ensure serials are provided
                if ($requiresSerials && count($serials) === 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => ['Serials are required for Electronic (with serial) products.'],
                    ]);
                }

                // CONDITION 1: Serial count MUST exactly match required quantity
                // Example: Qty = 5 → must input exactly 5 unique serial numbers
                if (count($serials) > 0 && ($requiredSerialCount <= 0 || count($serials) !== $requiredSerialCount)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => ["Serial numbers must match quantity. Required: {$requiredSerialCount}, Provided: ".count($serials)],
                    ]);
                }

                $serialsByIndex[] = $item['serials'] ?? [];

                // Purchases now store the entered quantity in the selected unit.
                $qty = $primaryQty;

                $subtotal = $primaryQty * $item['cost'];
                $totalCost += $subtotal;

                $purchaseItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'unit_type_id' => $item['unit_type_id'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $subtotal,
                ];
            }

            $purchase = Purchase::create([
                'purchase_date' => $validated['purchase_date'],
                'supplier_id' => $validated['supplier_id'],
                'total_cost' => $totalCost,
                'payment_status' => $validated['payment_status'],
                'reference_number' => ! empty($validated['reference_number']) ? $validated['reference_number'] : null,
            ]);

            $purchaseId = $purchase->id;
            $purchaseDate = $validated['purchase_date'] ?? now()->toDateString();

            $createdItems = $purchase->items()->createMany($purchaseItemsData);

            $warrantyService = app(\App\Services\WarrantyService::class);

            foreach ($createdItems as $index => $purchaseItem) {
                $serials = $serialsByIndex[$index] ?? [];
                if (empty($serials)) {
                    // Non-serial item — create warranty record at purchase time
                    $warrantyService->createForPurchase($purchaseItem, [], $purchaseDate);

                    continue;
                }

                foreach ($serials as $s) {
                    $serialPayload = [
                        'product_id' => $purchaseItem->product_id,
                        'branch_id' => null,
                        'serial_number' => $s['serial_number'],
                        'status' => 'purchased',
                        'warranty_expiry_date' => $s['warranty_expiry'] ?? null,
                        'sale_item_id' => null,
                    ];

                    if (Schema::hasColumn('product_serials', 'purchase_id')) {
                        $serialPayload['purchase_id'] = $purchase->id;
                    }

                    ProductSerial::create($serialPayload);
                }

                // Serial items — create one warranty record per serial
                $warrantyService->createForPurchase($purchaseItem, $serials, $purchaseDate);
            }
        });

        // If AJAX request, return JSON with purchase data for the stock-in modal
        if (request()->expectsJson() || request()->ajax()) {
            $purchase = Purchase::with(['items.product', 'items.unitType'])->find($purchaseId);
            $items = $purchase->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'unit_type_id' => $item->unit_type_id,
                'product_name' => $item->product->product_name ?? 'Unknown',
                'unit_name' => $item->unitType->unit_name ?? 'pcs',
                'unit_cost' => (float) ($item->unit_cost ?? 0),
                'selling_price' => (float) ($item->product->selling_price ?? 0),
            ])->values();

            return response()->json([
                'success' => true,
                'purchase_id' => $purchaseId,
                'items' => $items,
            ]);
        }

        return redirect()->route('superadmin.purchases.show', ['purchase' => $purchaseId])
            ->with('success', 'Purchase created successfully.')
            ->with('prompt_stockin', true);
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'items.unitType');
        $serialsByProductId = ProductSerial::query()
            ->where('purchase_id', $purchase->id)
            ->orderBy('id')
            ->get()
            ->groupBy('product_id');

        return view('SuperAdmin.purchases.show', compact('purchase', 'serialsByProductId'));
    }

    public function lifecycle(Purchase $purchase): \Illuminate\View\View
    {
        $purchase->load([
            'supplier',
            'branch',
            'items.product.category',
            'items.unitType',
        ]);

        // ── Stock-ins linked to this purchase ──────────────────────────────────
        $stockIns = StockIn::with(['product', 'branch', 'unitType'])
            ->where('purchase_id', $purchase->id)
            ->get();

        $stockInIds = $stockIns->pluck('id');

        // ── Stock-outs that came from those stock-ins ──────────────────────────
        $stockOuts = StockOut::with(['product', 'branch', 'sale.cashier', 'sale.customer'])
            ->whereIn('stock_in_id', $stockInIds)
            ->get();

        $saleIds = $stockOuts->pluck('sale_id')->filter()->unique();

        // ── Sale items for those sales, filtered to this purchase's products ───
        $purchaseProductIds = $purchase->items->pluck('product_id')->unique();

        $saleItems = SaleItem::with(['sale.cashier', 'sale.branch', 'sale.customer', 'unitType'])
            ->whereIn('sale_id', $saleIds)
            ->whereIn('product_id', $purchaseProductIds)
            ->get();

        // ── Refunds on those sale items ────────────────────────────────────────
        $refunds = Refund::with(['cashier', 'sale.branch'])
            ->whereIn('sale_id', $saleIds)
            ->whereIn('product_id', $purchaseProductIds)
            ->get();

        // ── Serials from this purchase ─────────────────────────────────────────
        $serials = ProductSerial::with(['branch', 'saleItem.sale.cashier', 'saleItem.sale.customer'])
            ->where('purchase_id', $purchase->id)
            ->get();

        // ── Per-item fulfillment summary ───────────────────────────────────────
        // For each purchase item: how much was stocked in, how much sold, how much refunded
        $itemSummaries = $purchase->items->map(function ($pi) use ($stockIns, $saleItems, $refunds) {
            $productId = $pi->product_id;

            $stockedIn = $stockIns->where('product_id', $productId)->sum('quantity');
            $sold = $saleItems->where('product_id', $productId)->sum('quantity');
            $refunded = $refunds->where('product_id', $productId)->sum('quantity_refunded');
            $remaining = max(0, $stockedIn - $sold + $refunded);

            $pct = $stockedIn > 0 ? min(100, round($sold / $stockedIn * 100)) : 0;

            return [
                'item' => $pi,
                'stocked_in' => $stockedIn,
                'sold' => $sold,
                'refunded' => $refunded,
                'remaining' => $remaining,
                'sold_pct' => $pct,
            ];
        });

        // ── KPIs ───────────────────────────────────────────────────────────────
        $totalStockedIn = $stockIns->sum('quantity');
        $totalSold = $saleItems->sum('quantity');
        $totalRefunded = $refunds->sum('quantity_refunded');
        $totalRevenue = $saleItems->sum('subtotal');
        $totalRefundAmt = $refunds->where('status', 'approved')->sum('refund_amount');
        $grossProfit = $totalRevenue - (float) $purchase->total_cost;

        // ── Timeline ───────────────────────────────────────────────────────────
        $timeline = collect();

        // Purchase created
        $purchaseItemLines = $purchase->items->map(fn ($i) => [
            'name' => $i->product->product_name ?? '—',
            'qty' => rtrim(rtrim(number_format((float) $i->quantity, 6, '.', ''), '0'), '.'),
            'unit' => $i->unitType->unit_name ?? 'pcs',
            'cost' => number_format($i->unit_cost, 2),
        ])->values()->toArray();

        $timeline->push([
            'type' => 'purchase',
            'icon' => 'fa-shopping-cart',
            'color' => '#1976D2',
            'label' => 'Purchase Created',
            'date' => $purchase->purchase_date ?? $purchase->created_at,
            'summary' => $purchase->items->count().' item(s) · ₱'.number_format($purchase->total_cost, 2),
            'detail' => 'Supplier: '.($purchase->supplier->supplier_name ?? '—')
                .' · Ref: '.($purchase->reference_number ?? '—')
                .' · Payment: '.ucfirst($purchase->payment_status),
            'user' => null,
            'items' => $purchaseItemLines,
        ]);

        // Stock-ins
        foreach ($stockIns as $si) {
            $timeline->push([
                'type' => 'stock_in',
                'icon' => 'fa-arrow-down',
                'color' => '#10b981',
                'label' => 'Stocked In',
                'date' => $si->created_at,
                'summary' => ($si->product->product_name ?? '—').' · Qty: '.number_format($si->quantity, 0)
                    .' · Remaining: '.number_format($si->quantity - $si->sold, 0),
                'detail' => 'Branch: '.($si->branch->branch_name ?? '—')
                    .($si->reason ? ' · Reason: '.$si->reason : '')
                    .($si->notes ? ' · Notes: '.$si->notes : ''),
                'user' => null,
            ]);
        }

        // Sales
        foreach ($saleItems as $si) {
            $timeline->push([
                'type' => 'sale',
                'icon' => 'fa-cash-register',
                'color' => '#f59e0b',
                'label' => 'Sold',
                'date' => $si->sale->created_at ?? $si->created_at,
                'summary' => ($si->product->product_name ?? '—').' · Qty: '.number_format($si->quantity, 2)
                    .' × ₱'.number_format($si->unit_price, 2).' = ₱'.number_format($si->subtotal, 2),
                'detail' => 'Sale #'.($si->sale->reference_number ?? $si->sale_id)
                    .' · Branch: '.($si->sale->branch->branch_name ?? '—')
                    .' · Customer: '.($si->sale->customer->full_name ?? 'Walk-in')
                    .' · Payment: '.ucfirst($si->sale->payment_method ?? '—'),
                'user' => $si->sale->cashier->name ?? null,
                'status' => $si->sale->status ?? null,
            ]);
        }

        // Refunds
        foreach ($refunds as $r) {
            $timeline->push([
                'type' => 'refund',
                'icon' => 'fa-undo',
                'color' => '#ef4444',
                'label' => 'Refund',
                'date' => $r->created_at,
                'summary' => ($r->product->product_name ?? '—').' · Qty: '.$r->quantity_refunded
                    .' · ₱'.number_format($r->refund_amount, 2),
                'detail' => 'Reason: '.($r->reason ?? '—').($r->notes ? ' · '.$r->notes : ''),
                'user' => $r->cashier->name ?? null,
                'status' => $r->status,
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();

        return view('SuperAdmin.purchases.lifecycle', compact(
            'purchase', 'stockIns', 'stockOuts', 'saleItems', 'refunds',
            'serials', 'itemSummaries', 'timeline',
            'totalStockedIn', 'totalSold', 'totalRefunded',
            'totalRevenue', 'totalRefundAmt', 'grossProfit'
        ));
    }

    public function markPaid(Purchase $purchase)
    {
        if ($purchase->payment_status === 'paid') {
            return redirect()
                ->route('superadmin.purchases.show', $purchase)
                ->with('success', 'Purchase is already marked as paid.');
        }

        $purchase->update([
            'payment_status' => 'paid',
        ]);

        return redirect()
            ->route('superadmin.purchases.show', $purchase)
            ->with('success', 'Purchase marked as paid successfully.');
    }

    public function autoStockIn(Purchase $purchase): \Illuminate\Http\JsonResponse
    {
        $branchId = (int) request()->input('branch_id');

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'Branch is required.'], 422);
        }

        $purchase->loadMissing(['items.product', 'items.unitType']);

        $sellingPrices = request()->input('selling_prices', []);

        try {
            DB::transaction(function () use ($purchase, $branchId, $sellingPrices) {
                foreach ($purchase->items as $item) {
                    $productId = (int) $item->product_id;
                    $unitTypeId = (int) ($item->unit_type_id ?? 0);
                    $qty = (float) ($item->quantity ?? 0);
                    $unitCost = (float) ($item->unit_cost ?? 0);

                    if ($qty <= 0) {
                        continue;
                    }

                    $factor = (float) (DB::table('product_unit_type')
                        ->where('product_id', $productId)
                        ->where('unit_type_id', $unitTypeId)
                        ->value('conversion_factor') ?? 1);
                    $factor = $factor > 0 ? $factor : 1;
                    $qtyBase = (int) round($qty * $factor);

                    // Increase branch stock
                    \App\Models\BranchStock::query()
                        ->firstOrCreate(
                            ['branch_id' => $branchId, 'product_id' => $productId],
                            ['quantity_base' => 0]
                        )
                        ->increment('quantity_base', $qtyBase);

                    $stockInPayload = [
                        'product_id' => $productId,
                        'branch_id' => $branchId,
                        'purchase_id' => $purchase->id,
                        'unit_type_id' => $unitTypeId ?: null,
                        'quantity' => $qtyBase,
                        'price' => $unitCost,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('stock_ins', 'initial_quantity')) {
                        $stockInPayload['initial_quantity'] = $qtyBase;
                    }

                    $stockInId = DB::table('stock_ins')->insertGetId($stockInPayload);

                    // Update branch_id on product_serials linked to this purchase item's product
                    // for products that have serial tracking
                    $categoryType = DB::table('categories')
                        ->join('products', 'products.category_id', '=', 'categories.id')
                        ->where('products.id', $productId)
                        ->value('categories.category_type');

                    if ($categoryType === 'electronic_with_serial') {
                        \App\Models\ProductSerial::where('product_id', $productId)
                            ->where('purchase_id', $purchase->id)
                            ->whereNull('branch_id')
                            ->update(['branch_id' => $branchId]);
                    }

                    if (Schema::hasTable('stock_in_unit_prices') && $unitTypeId) {
                        $key = $productId.'_'.$unitTypeId;
                        $sellingPrice = isset($sellingPrices[$key]) ? (float) $sellingPrices[$key] : null;

                        if ($sellingPrice === null) {
                            $sellingPrice = DB::table('stock_in_unit_prices')
                                ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                                ->where('stock_ins.product_id', $productId)
                                ->where('stock_ins.branch_id', $branchId)
                                ->where('stock_in_unit_prices.unit_type_id', $unitTypeId)
                                ->where('stock_ins.id', '!=', $stockInId)
                                ->orderByDesc('stock_in_unit_prices.id')
                                ->value('stock_in_unit_prices.price');
                        }

                        if ($sellingPrice !== null) {
                            DB::table('stock_in_unit_prices')->insert([
                                'stock_in_id' => $stockInId,
                                'unit_type_id' => $unitTypeId,
                                'price' => $sellingPrice,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'All items stocked in successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Check serial numbers for duplicates in database
     * Used for AJAX validation during purchase creation
     * Checks against all product serials including existing inventory
     */
    public function checkSerials(Request $request)
    {
        $serialNumbers = $request->input('serial_numbers', []);

        if (! is_array($serialNumbers) || empty($serialNumbers)) {
            return response()->json(['duplicates' => []]);
        }

        // Check for existing serial numbers in ProductSerial table (all products)
        $existingSerials = ProductSerial::whereIn('serial_number', $serialNumbers)
            ->pluck('serial_number')
            ->toArray();

        // Also check against any product barcodes or other serial fields if they exist
        // This ensures uniqueness across all product identification numbers
        $barcodeSerials = Product::whereIn('barcode', $serialNumbers)
            ->pluck('barcode')
            ->toArray();

        // Merge all duplicates
        $allDuplicates = array_unique(array_merge($existingSerials, $barcodeSerials));

        return response()->json([
            'duplicates' => $allDuplicates,
            'total_checked' => count($serialNumbers),
            'duplicates_found' => count($allDuplicates),
        ]);
    }

    public function autoStockInCheck(Purchase $purchase): \Illuminate\Http\JsonResponse
    {
        $purchase->load(['items.product', 'items.unitType']);

        $pricingItems = [];

        foreach ($purchase->items as $item) {
            $productId = (int) $item->product_id;
            $unitTypeId = (int) ($item->unit_type_id ?? 0);

            if ($unitTypeId <= 0) {
                continue;
            }

            $existingPrice = DB::table('stock_in_unit_prices')
                ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                ->where('stock_ins.product_id', $productId)
                ->where('stock_in_unit_prices.unit_type_id', $unitTypeId)
                ->orderByDesc('stock_in_unit_prices.id')
                ->value('stock_in_unit_prices.price');

            $pricingItems[] = [
                'purchase_item_id' => $item->id,
                'product_id' => $productId,
                'unit_type_id' => $unitTypeId,
                'product_name' => $item->product->product_name ?? 'Unknown',
                'unit_name' => $item->unitType->unit_name ?? 'Unknown',
                'unit_cost' => (float) ($item->unit_cost ?? 0),
                'existing_price' => $existingPrice !== null ? (float) $existingPrice : null,
                'is_new' => $existingPrice === null,
            ];
        }

        return response()->json(['success' => true, 'pricing_items' => $pricingItems]);
    }
}
