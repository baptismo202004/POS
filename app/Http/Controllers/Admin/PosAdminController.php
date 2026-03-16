<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Credit;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PosAdminController extends Controller
{
    public function index()
    {
        return view('Admin.pos.index');
    }

    public function lookup(Request $request)
    {
        // For POST requests, get data from form data
        $keyword = trim((string) $request->input('barcode', ''));
        $mode = $request->input('mode', 'list');
        
        Log::info("[POS_ADMIN_LOOKUP] keyword='{$keyword}', mode='{$mode}'");

        // Validate only if barcode is provided
        if (!empty($keyword)) {
            $request->validate(['barcode' => 'required|string']);
        }

        // First, let's check if any products exist at all
        $totalProducts = Product::count();
        $totalStockRecords = (int) DB::table('branch_stocks')->count();
        Log::info("[POS_ADMIN_LOOKUP] Total products in DB: {$totalProducts}, Total stock records: {$totalStockRecords}");

        // Return test data if no products in database
        if ($totalProducts === 0) {
            return response()->json([
                'items' => [
                    [
                        'product_id' => 1,
                        'name' => 'Test Product 1 (No DB Products)',
                        'barcode' => 'TEST001',
                        'price' => 100.00,
                        'total_stock' => 5,
                        'branches' => [
                            ['branch_id' => 1, 'branch_name' => 'Main Branch', 'stock' => 5]
                        ]
                    ],
                    [
                        'product_id' => 2,
                        'name' => 'Test Product 2 (No DB Products)',
                        'barcode' => 'TEST002',
                        'price' => 200.00,
                        'total_stock' => 3,
                        'branches' => [
                            ['branch_id' => 1, 'branch_name' => 'Main Branch', 'stock' => 3]
                        ]
                    ]
                ]
            ]);
        }

        // List mode for typeahead/multi results
        if ($mode === 'list') {
            // If keyword is empty, get all products that currently have stock
            if (empty($keyword)) {
                // Get all products that have stock records
                $productIds = DB::table('branch_stocks')->distinct()->pluck('product_id');
                $matches = Product::whereIn('id', $productIds)->get();
                Log::info("[POS_ADMIN_LOOKUP] Getting all products with stock: " . count($matches) . " products found");
            } else {
                $matches = Product::query()
                    ->where(function ($q) use ($keyword) {
                        $q->where('product_name', 'LIKE', "%{$keyword}%")
                          ->orWhere('barcode', 'LIKE', "%{$keyword}%")
                          ->orWhere('model_number', 'LIKE', "%{$keyword}%");
                    })
                    ->limit(20)
                    ->get();
                Log::info("[POS_ADMIN_LOOKUP] Found " . count($matches) . " products matching keyword: '{$keyword}'");
            }

            $items = $matches->map(function ($p) {
                // Calculate available stock (base units) per branch
                $stockRows = DB::table('branch_stocks')
                    ->where('product_id', (int) $p->id)
                    ->get(['branch_id', 'quantity_base']);

                $totalStock = (float) $stockRows->sum('quantity_base');
                $branchStocks = [];

                foreach ($stockRows as $row) {
                    $branch = Branch::find((int) $row->branch_id);
                    $branchStocks[(int) $row->branch_id] = [
                        'branch_id' => (int) $row->branch_id,
                        'branch_name' => optional($branch)->branch_name,
                        'stock' => (float) ($row->quantity_base ?? 0),
                        'stock_units' => [],
                        'latest_price' => 0.00,
                    ];
                }

                // Finalize branch list with latest price per branch
                $branches = array_values(array_map(function ($branchData) {
                    $price = (float) ($branchData['latest_price'] ?? 0.00);
                    $units = $branchData['stock_units'] ?? [];

                    // Normalize stock_units to an array
                    if (is_array($units)) {
                        $units = array_values(array_map(function ($u) {
                            return [
                                'unit_type_id' => (int) ($u['unit_type_id'] ?? 0),
                                'unit_name' => $u['unit_name'] ?? null,
                                'stock' => (float) ($u['stock'] ?? 0),
                                'price' => (float) ($u['latest_price'] ?? 0.00),
                            ];
                        }, $units));
                    } else {
                        $units = [];
                    }

                    return [
                        'branch_id' => $branchData['branch_id'],
                        'branch_name' => $branchData['branch_name'],
                        'stock' => $branchData['stock'],
                        'stock_units' => $units,
                        'price' => $price,
                    ];
                }, $branchStocks));

                // Default price shown for the product row is the first branch's price
                $defaultPrice = isset($branches[0]) ? (float) ($branches[0]['price'] ?? 0) : 0.00;

                Log::info("[POS_ADMIN_LOOKUP] Product: {$p->product_name} (ID: {$p->id}) - Available Stock: {$totalStock}");
                
                return [
                    'product_id' => $p->id,
                    'name' => $p->product_name,
                    'barcode' => $p->barcode,
                    'price' => $defaultPrice,
                    'total_stock' => (int) $totalStock,
                    'branches' => $branches,
                ];
            })->filter(function ($item) {
                // Only show products with available stock > 0
                $hasStock = $item['total_stock'] > 0;
                Log::info("[POS_ADMIN_LOOKUP] Product: {$item['name']} - Stock: {$item['total_stock']}, Has Stock: " . ($hasStock ? 'YES' : 'NO'));
                return $hasStock;
            })->values();

            Log::info("[POS_ADMIN_LOOKUP] Returning " . count($items) . " items for keyword: '{$keyword}'");
            return response()->json(['items' => $items]);
        }

        // Exact matching phases similar to cashier POS
        $product = Product::where('barcode', $keyword)->first()
            ?: Product::where('model_number', $keyword)->first()
            ?: Product::where('product_name', $keyword)->first()
            ?: Product::where('product_name', 'LIKE', "%{$keyword}%")->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Calculate available stock and branch-specific prices
        $stockRows = DB::table('branch_stocks')
            ->where('product_id', (int) $product->id)
            ->get(['branch_id', 'quantity_base']);

        $totalStock = (float) $stockRows->sum('quantity_base');
        $branchStocks = [];

        foreach ($stockRows as $row) {
            $branch = Branch::find((int) $row->branch_id);
            $branchStocks[(int) $row->branch_id] = [
                'branch_id' => (int) $row->branch_id,
                'branch_name' => optional($branch)->branch_name,
                'stock' => (float) ($row->quantity_base ?? 0),
                'stock_units' => [],
                'latest_price' => 0.00,
            ];
        }

        $byBranch = array_values(array_map(function ($branchData) {
            $price = (float) ($branchData['latest_price'] ?? 0.00);
            $units = $branchData['stock_units'] ?? [];
            if (is_array($units)) {
                $units = array_values(array_map(function ($u) {
                    return [
                        'unit_type_id' => (int) ($u['unit_type_id'] ?? 0),
                        'unit_name' => $u['unit_name'] ?? null,
                        'stock' => (float) ($u['stock'] ?? 0),
                        'price' => (float) ($u['latest_price'] ?? 0.00),
                    ];
                }, $units));
            } else {
                $units = [];
            }

            return [
                'branch_id' => $branchData['branch_id'],
                'branch_name' => $branchData['branch_name'],
                'stock' => $branchData['stock'],
                'stock_units' => $units,
                'price' => $price,
            ];
        }, $branchStocks));

        if ($totalStock <= 0) {
            return response()->json(['error' => 'Product is out of stock'], 422);
        }

        return response()->json([
            'product_id' => $product->id,
            'name' => $product->product_name,
            'barcode' => $product->barcode,
            // Default to first branch price for single-result lookup
            'price' => isset($byBranch[0]) ? (float) ($byBranch[0]['price'] ?? 0) : 0.00,
            'total_stock' => (int) $totalStock,
            'branches' => $byBranch,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $items = $data['items'] ?? [];
            $total = $data['total'] ?? 0;
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $customerId = !empty($data['customer_id']) ? $data['customer_id'] : null;
            $customerName = !empty($data['customer_name']) ? trim($data['customer_name']) : null;
            $creditDueDate = $data['credit_due_date'] ?? null;
            $creditNotes = $data['credit_notes'] ?? null;

            Log::info("[POS_STORE] Processing order with " . count($items) . " items, total: ₱{$total}");
            Log::info("[POS_STORE] Customer ID: '{$customerId}'");
            Log::info("[POS_STORE] Customer name: '{$customerName}'");

            DB::beginTransaction();

            // Determine the branch from the first item, assuming all items are from the same branch for a single transaction
            $branchId = Auth::user()->branch_id; // Default to cashier's branch
            if (!empty($items)) {
                $firstItem = reset($items);
                $branchId = $firstItem['branch_id'] ?? $branchId;
            }

            // Create sale record with all required fields
            $saleData = [
                'cashier_id' => Auth::id(),
                'employee_id' => Auth::id(), // Use the numeric user ID
                'branch_id' => $branchId,
                'total_amount' => $total,
                'tax' => 0, // No tax for now
                'payment_method' => $paymentMethod // Use payment method from request
            ];
            
            // Only include customer_id if it's not null
            if ($customerId !== null) {
                $saleData['customer_id'] = $customerId;
            }
            
            $sale = Sale::create($saleData);

            Log::info("[POS_STORE] Created sale record: {$sale->id}");

            // Process each item
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $branchId = $item['branch_id'];
                $unitTypeId = isset($item['unit_type_id']) ? (int) $item['unit_type_id'] : null;
                $quantity = $item['quantity'];
                $price = $item['price'];

                if (empty($unitTypeId)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Unit type is required for each item.',
                    ], 422);
                }

                Log::info("[POS_STORE] Processing item: Product {$productId} from Branch {$branchId}, Quantity: {$quantity}");

                $inventory = app(InventoryService::class);
                $baseQty = $inventory->convertToBaseQuantity((int) $productId, (int) $unitTypeId, (float) $quantity);
                $availableBase = $inventory->availableStockBase((int) $productId, (int) $branchId);
                if ($availableBase < $baseQty) {
                    DB::rollBack();
                    Log::error("[POS_STORE] Insufficient stock for product {$productId}");
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$item['name']}"
                    ], 422);
                }

                $inventory->decreaseStock((int) $branchId, (int) $productId, (float) $baseQty, 'sale', 'sales', (int) $sale->id, now());
                
                // Create sale item record
                $saleItemPayload = [
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $price * $quantity,
                ];

                // If sale_items has unit_type_id column, include it
                if (\Illuminate\Support\Facades\Schema::hasColumn('sale_items', 'unit_type_id')) {
                    $saleItemPayload['unit_type_id'] = $unitTypeId;
                }

                $saleItem = SaleItem::create($saleItemPayload);
                
                Log::info("[POS_STORE] Created sale item and stock out for product {$productId}");
            }
            
            // Create credit record if payment method is credit
            if ($paymentMethod === 'credit' && $creditDueDate) {
                // Generate unique reference number
                $lastCredit = Credit::orderBy('id', 'desc')->first();
                $nextNumber = $lastCredit ? $lastCredit->id + 1 : 1;
                $referenceNumber = 'CR-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                
                // Determine customer_id for credit
                $creditCustomerId = $customerId;
                if (!$creditCustomerId && $customerName) {
                    // Try to find existing customer by name
                    $existingCustomer = DB::table('customers')->where('full_name', $customerName)->first();
                    if ($existingCustomer) {
                        $creditCustomerId = $existingCustomer->id;
                    } else {
                        // Create new customer record
                        $creditCustomerId = DB::table('customers')->insertGetId([
                            'full_name' => $customerName,
                            'email' => null,
                            'phone' => null,
                            'address' => null,
                            'max_credit_limit' => 0,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
                
                Credit::create([
                    'reference_number' => $referenceNumber,
                    'customer_id' => $creditCustomerId,
                    'sale_id' => $sale->id,
                    'cashier_id' => Auth::id(),
                    'branch_id' => $branchId,
                    'credit_amount' => $total,
                    'paid_amount' => 0,
                    'remaining_balance' => $total,
                    'status' => 'active',
                    'date' => $creditDueDate,
                    'notes' => $creditNotes
                ]);
                
                Log::info("[POS_STORE] Created credit record for sale #{$sale->id}");
            }
            
            DB::commit();
            
            Log::info("[POS_STORE] Order processed successfully: Sale #{$sale->id}");
            
            $response = [
                'success' => true,
                'message' => 'Order processed successfully',
                'order_id' => $sale->id
            ];
            
            // If payment method is cash, include receipt URL for automatic display
            if ($paymentMethod === 'cash') {
                $response['receipt_url'] = route('admin.sales.receipt', $sale);
                $response['auto_receipt'] = true;
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[POS_STORE] Order processing failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateCashier(Request $request)
    {
        // Placeholder for validateCashier method
        return response()->json(['message' => 'Cashier validation not implemented yet']);
    }

    // Stock In methods for Admin
    public function stockInCreate()
    {
        // Only show purchases that still have remaining quantity to be stocked in
        $purchases = DB::table('purchases')
            ->join('purchase_items', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->leftJoin('stock_movements', function ($join) {
                $join->on('stock_movements.source_id', '=', 'purchases.id')
                    ->where('stock_movements.source_type', '=', 'purchases')
                    ->where('stock_movements.movement_type', '=', 'purchase');
            })
            ->select(
                'purchases.id',
                'purchases.reference_number',
                'purchases.purchase_date',
                'suppliers.supplier_name as supplier_name',
                DB::raw('SUM(purchase_items.quantity) - COALESCE(SUM(stock_movements.quantity_base), 0) as remaining_quantity')
            )
            ->groupBy('purchases.id', 'purchases.reference_number', 'purchases.purchase_date', 'suppliers.supplier_name')
            ->havingRaw('SUM(purchase_items.quantity) > COALESCE(SUM(stock_movements.quantity_base), 0)')
            ->orderBy('purchases.purchase_date', 'desc')
            ->get();
            
        $branches = \App\Models\Branch::orderBy('branch_name')->get();
            
        return view('Admin.stockin.create', compact('purchases', 'branches'));
    }

    public function stockInProductsByPurchase(\App\Models\Purchase $purchase)
    {
        try {
            $purchaseItems = $purchase->items()->with(['product.unitTypes', 'unitType'])->get();

            $items = $purchaseItems->map(function ($item) {
                // Calculate how many units have already been stocked in for this purchase + product
                $alreadyStockedBase = (float) DB::table('stock_movements')
                    ->where('source_type', 'purchases')
                    ->where('source_id', (int) $item->purchase_id)
                    ->where('product_id', (int) $item->product_id)
                    ->where('movement_type', 'purchase')
                    ->sum('quantity_base');

                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $item->product_id)
                    ->where('unit_type_id', (int) ($item->unit_type_id ?? 0))
                    ->value('conversion_factor') ?? 1);
                $factor = $factor > 0 ? $factor : 1;

                $purchasedBase = (float) ($item->quantity ?? 0) * $factor;
                $remainingBase = (float) $purchasedBase - (float) $alreadyStockedBase;
                if ($remainingBase < 0) {
                    $remainingBase = 0;
                }

                // Debug: Log the unit types data
                Log::info("[STOCK_IN_PRODUCTS] Product ID: {$item->product_id}, Remaining: {$remainingBase}, Unit Types: " . json_encode($item->product->unitTypes ?? []));

                $unitTypes = $item->product->unitTypes ?? collect();

                // If the product has no unit types assigned, fallback to all unit types
                if ($unitTypes instanceof \Illuminate\Support\Collection ? $unitTypes->isEmpty() : empty($unitTypes)) {
                    $unitTypes = \App\Models\UnitType::orderBy('unit_name')->get();
                }

                $unitTypesPayload = collect($unitTypes)->map(function ($ut) {
                    return [
                        'id' => $ut->id,
                        'unit_name' => $ut->unit_name,
                        'conversion_factor' => isset($ut->pivot->conversion_factor) ? (float) $ut->pivot->conversion_factor : 1.0,
                        'is_base' => isset($ut->pivot->is_base) ? (bool) $ut->pivot->is_base : false,
                    ];
                })->values();

                $result = [
                    'product_id' => $item->product_id,
                    'product' => $item->product,
                    // Remaining quantity (base units) - authoritative for validation
                    'quantity' => $remainingBase,
                    'purchased_quantity' => (float) ($purchasedBase ?? 0),
                    'remaining_quantity' => $remainingBase,
                    'unit_price' => $item->unit_cost, // use unit_cost from purchase item
                    'unit_types' => $unitTypesPayload,
                    'unit_type' => $item->unitType,
                    'primary_unit_name' => $item->unitType ? $item->unitType->unit_name : null,
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

    public function stockInStore(Request $request)
    {
        try {
            $data = $request->validate([
                'purchase_id' => 'required|exists:purchases,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.unit_type_id' => 'required|exists:unit_types,id',
                'items.*.quantity' => 'required|numeric|min:0.0001',
                'items.*.new_price' => 'required|numeric|min:0',
                'items.*.unit_prices' => 'required|array|min:1',
                'items.*.unit_prices.*' => 'required|numeric|min:0',
                'items.*.unit_quantities' => 'nullable|array',
                'items.*.unit_quantities.*' => 'nullable|numeric|min:0',
                'items.*.original_price' => 'required|numeric|min:0',
                'items.*.branch_id' => 'required|exists:branches,id',
            ]);

            foreach ($data['items'] as $item) {
                $originalPrice = (float) ($item['original_price'] ?? 0);
                $unitPrices = $item['unit_prices'] ?? [];

                // Use purchase item cost as a stable base reference (Option B: no StockIn price lots)
                $purchaseUnitCost = (float) (DB::table('purchase_items')
                    ->where('purchase_id', (int) $data['purchase_id'])
                    ->where('product_id', (int) $item['product_id'])
                    ->value('unit_cost') ?? 0);

                $baseReferencePrice = $purchaseUnitCost > 0 ? $purchaseUnitCost : $originalPrice;

                $existingBasePrice = 0.0;

                // Arbitrage prevention across unit types (flexible retail pricing)
                // Allow smaller units to be more expensive per base unit, but never cheaper.
                // Compute base-equivalent: base_equiv = unit_price / factor
                // Enforce: for smaller factor (smaller unit), base_equiv must be >= larger-unit base_equiv.
                $pricePoints = [];

                // If there is no existing stock_in base price, derive a base reference price from submitted unit prices
                // by taking the cheapest per-base-unit price (bulk). This supports flexible pricing where smaller
                // units can be more expensive, but not cheaper than bulk.
                if ($existingBasePrice <= 0 && count($unitPrices) > 0) {
                    $derived = [];
                    foreach ($unitPrices as $uId => $uPrice) {
                        $uPrice = (float) $uPrice;
                        $uId = (int) $uId;
                        if ($uPrice <= 0) continue;
                        $f = (float) (\Illuminate\Support\Facades\DB::table('product_unit_type')
                            ->where('product_id', (int) $item['product_id'])
                            ->where('unit_type_id', $uId)
                            ->value('conversion_factor') ?? 1);
                        $f = $f > 0 ? $f : 1;
                        $derived[] = $uPrice / $f;
                    }
                    if (count($derived) > 0) {
                        $baseReferencePrice = min($derived);
                    }
                }

                foreach ($unitPrices as $unitTypeId => $unitPrice) {
                    $unitPrice = (float) $unitPrice;
                    $unitTypeId = (int) $unitTypeId;

                    if ($unitPrice <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'All unit prices must be greater than zero.',
                        ], 422);
                    }

                    // Enforce the requested rule: unit price must not be smaller than purchased price converted to that unit.
                    $factor = (float) (\Illuminate\Support\Facades\DB::table('product_unit_type')
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

                if (count($pricePoints) >= 2) {
                    usort($pricePoints, function($a, $b) {
                        // larger units first (bigger factor)
                        return $b['factor'] <=> $a['factor'];
                    });

                    $tolerance = 0.001; // allow tiny rounding differences
                    $prev = null;
                    foreach ($pricePoints as $p) {
                        if ($prev !== null) {
                            // If current unit is smaller (factor decreased), it may be more expensive per base unit,
                            // but must not be cheaper. Therefore base_equiv must be non-increasing as factor decreases.
                            if ($p['base_equiv'] - $tolerance > $prev['base_equiv']) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Smaller units must not be cheaper than larger units after conversion (no arbitrage).',
                                ], 422);
                            }
                        }
                        $prev = $p;
                    }
                }
            }

            $items = $data['items'];
            $purchaseId = $data['purchase_id'];

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

                $alreadyStockedBase = (float) DB::table('stock_movements')
                    ->where('source_type', 'purchases')
                    ->where('source_id', (int) $purchaseId)
                    ->where('product_id', (int) $productId)
                    ->where('movement_type', 'purchase')
                    ->sum('quantity_base');

                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) ($purchaseItem?->unit_type_id ?? 0))
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;
                $purchasedBase = (float) ($purchaseItem->quantity ?? 0) * $purchaseFactor;

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

                            $factor = (float) (\Illuminate\Support\Facades\DB::table('product_unit_type')
                                ->where('product_id', (int) $productId)
                                ->where('unit_type_id', $unitTypeId)
                                ->value('conversion_factor') ?? 1);
                            if ($factor <= 0) $factor = 1;
                            $requestedBase += ($enteredQty * $factor);
                        }
                        continue;
                    }

                    // row['quantity'] is entered in row['unit_type_id']
                    $factor = (float) (DB::table('product_unit_type')
                        ->where('product_id', (int) $productId)
                        ->where('unit_type_id', (int) ($row['unit_type_id'] ?? 0))
                        ->value('conversion_factor') ?? 1);
                    if ($factor <= 0) $factor = 1;
                    $requestedBase += (float) ($row['quantity'] ?? 0) * $factor;
                }

                if ($requestedBase > $remainingBase) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Total Stock-In Qty for this product exceeds remaining to stock.',
                    ], 422);
                }
            }

            DB::transaction(function () use ($items, $purchaseId) {
                $inventory = app(InventoryService::class);

                foreach ($items as $item) {
                    $unitQuantities = $item['unit_quantities'] ?? [];

                    if (is_array($unitQuantities) && count($unitQuantities) > 0) {
                        foreach ($unitQuantities as $uId => $enteredQty) {
                            $enteredQty = (float) $enteredQty;
                            $uId = (int) $uId;
                            if ($enteredQty <= 0) {
                                continue;
                            }

                            $baseQty = $inventory->convertToBaseQuantity((int) $item['product_id'], $uId, $enteredQty);
                            $inventory->increaseStock((int) $item['branch_id'], (int) $item['product_id'], (float) $baseQty, 'purchase', 'purchases', (int) $purchaseId, now());
                        }

                        continue;
                    }

                    $baseQty = $inventory->convertToBaseQuantity((int) $item['product_id'], (int) $item['unit_type_id'], (float) $item['quantity']);
                    $inventory->increaseStock((int) $item['branch_id'], (int) $item['product_id'], (float) $baseQty, 'purchase', 'purchases', (int) $purchaseId, now());
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error("[STOCK_IN] Error adding stock", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: ' . $e->getMessage()
            ], 500);
        }
    }
}
