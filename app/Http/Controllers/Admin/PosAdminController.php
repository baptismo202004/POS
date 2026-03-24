<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Credit;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use App\Models\StockIn;
use Carbon\Carbon;
use App\Services\CustomerService;

class PosAdminController extends Controller
{
    public function index()
    {
        return view('Admin.pos.index');
    }

    public function electronicsIndex()
    {
        return view('Admin.pos.electronics');
    }

    public function lookup(Request $request)
    {
        // For POST requests, get data from form data
        $keyword = trim((string) $request->input('barcode', ''));
        $mode = $request->input('mode', 'list');
        $electronicsOnly = (bool) $request->boolean('electronics_only');
        
        Log::info("[POS_ADMIN_LOOKUP] keyword='{$keyword}', mode='{$mode}', electronics_only=" . ($electronicsOnly ? '1' : '0'));

        // Validate only if barcode is provided
        if (! empty($keyword)) {
            $request->validate(['barcode' => 'required|string']);
        }

        // First, let's check if any products exist at all
        $totalProducts = Product::count();
        $totalStockRecords = StockIn::count();
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
                            ['branch_id' => 1, 'branch_name' => 'Main Branch', 'stock' => 5],
                        ],
                    ],
                    [
                        'product_id' => 2,
                        'name' => 'Test Product 2 (No DB Products)',
                        'barcode' => 'TEST002',
                        'price' => 200.00,
                        'total_stock' => 3,
                        'branches' => [
                            ['branch_id' => 1, 'branch_name' => 'Main Branch', 'stock' => 3],
                        ],
                    ],
                ],
            ]);
        }

        // List mode for typeahead/multi results
        if ($mode === 'list') {
            $branchNames = Branch::pluck('branch_name', 'id');
            $allBranchIds = Branch::pluck('id')->toArray();

            $inStockProductIds = StockIn::query()
                ->select('product_id')
                ->groupBy('product_id')
                ->havingRaw('SUM(quantity - sold) > 0')
                ->pluck('product_id');

            // If keyword is empty, get all products from stock_ins table
            if (empty($keyword)) {
                $matchesQuery = Product::query();
                if (!$electronicsOnly) {
                    // Non-electronics POS still lists only products that are currently in stock (across any branch)
                    $matchesQuery->whereIn('products.id', $inStockProductIds);
                }

                if ($electronicsOnly) {
                    $matchesQuery
                        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                        ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                        ->where(function ($q) {
                            $q->whereRaw("LOWER(TRIM(categories.category_type)) LIKE 'electronic%'")
                                ->orWhere('product_types.is_electronic', true)
                                ->orWhere('product_types.type_name', 'LIKE', '%elect%');
                        })
                        ->select('products.*');
                }

                $matches = $matchesQuery->get();
                Log::info("[POS_ADMIN_LOOKUP] Getting all products from stock_ins: " . count($matches) . " products found");
            } else {
                $matchesQuery = Product::query();
                if (!$electronicsOnly) {
                    $matchesQuery->whereIn('products.id', $inStockProductIds);
                }

                if ($electronicsOnly) {
                    $matchesQuery
                        ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                        ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                        ->where(function ($q) {
                            $q->whereRaw("LOWER(TRIM(categories.category_type)) LIKE 'electronic%'")
                                ->orWhere('product_types.is_electronic', true)
                                ->orWhere('product_types.type_name', 'LIKE', '%elect%');
                        })
                        ->select('products.*');
                }

                $matches = $matchesQuery
                    ->where(function ($q) use ($keyword) {
                        $q->where('product_name', 'LIKE', "%{$keyword}%")
                            ->orWhere('barcode', 'LIKE', "%{$keyword}%")
                            ->orWhere('model_number', 'LIKE', "%{$keyword}%");
                    })
                    ->limit(20)
                    ->get();
                Log::info('[POS_ADMIN_LOOKUP] Found '.count($matches)." products matching keyword: '{$keyword}'");
            }

            $items = $matches->map(function ($p) use ($branchNames, $allBranchIds, $electronicsOnly) {
                // Calculate available stock and branch-specific latest prices from stock_ins
                $stockRecords = StockIn::with('unitType')
                    ->where('product_id', $p->id)
                    ->whereColumn('quantity', '>', 'sold')
                    ->orderBy('id', 'asc') // ensure later records override earlier ones
                    ->get();
                $totalStock = 0;
                $branchStocks = [];

                if ($electronicsOnly) {
                    foreach ($allBranchIds as $bId) {
                        $branchStocks[$bId] = [
                            'branch_id' => (int) $bId,
                            'branch_name' => $branchNames[$bId] ?? null,
                            // stock is tracked in base units
                            'stock' => 0,
                            // prices are resolved later from latest saved unit prices
                            'stock_units' => [],
                            'latest_price' => 0.00,
                        ];
                    }
                }

                // Preload product unit conversion factors (unit_type_id => conversion_factor)
                $unitFactors = DB::table('product_unit_type')
                    ->where('product_id', (int) $p->id)
                    ->pluck('conversion_factor', 'unit_type_id')
                    ->map(function ($v) {
                        $f = (float) $v;
                        return $f > 0 ? $f : 1.0;
                    })
                    ->toArray();

                foreach ($stockRecords as $stock) {
                    $availableStock = $stock->quantity - $stock->sold;
                    $totalStock += $availableStock;

                    if (!isset($branchStocks[$stock->branch_id])) {
                        $branchStocks[$stock->branch_id] = [
                            'branch_id' => $stock->branch_id,
                            'branch_name' => $branchNames[$stock->branch_id] ?? null,
                            // stock is tracked in base units
                            'stock' => 0,
                            // prices are resolved later from latest saved unit prices
                            'stock_units' => [],
                            'latest_price' => 0.00,
                        ];
                    }

                    // StockIn.quantity is already stored in base units in this codebase
                    if ($availableStock > 0) {
                        $branchStocks[$stock->branch_id]['stock'] += (float) $availableStock;
                    }
                }

                // Resolve unit prices per branch from latest saved unit prices PER unit_type.
                // This prevents "only the last stocked unit type" from showing.
                $fallbackUnitPrices = [];
                if ($electronicsOnly && Schema::hasTable('stock_in_unit_prices')) {
                    $fallbackUnitPrices = DB::table('stock_in_unit_prices')
                        ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                        ->leftJoin('unit_types', 'unit_types.id', '=', 'stock_in_unit_prices.unit_type_id')
                        ->where('stock_ins.product_id', (int) $p->id)
                        ->orderByDesc('stock_ins.id')
                        ->get([
                            'stock_in_unit_prices.unit_type_id as unit_type_id',
                            'stock_in_unit_prices.price as price',
                            'unit_types.unit_name as unit_name',
                        ])
                        ->toArray();
                }

                foreach ($branchStocks as $bId => $bData) {
                    $rows = [];
                    if (Schema::hasTable('stock_in_unit_prices')) {
                        $rows = DB::table('stock_in_unit_prices')
                            ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                            ->leftJoin('unit_types', 'unit_types.id', '=', 'stock_in_unit_prices.unit_type_id')
                            ->where('stock_ins.product_id', (int) $p->id)
                            ->where('stock_ins.branch_id', (int) $bId)
                            ->orderByDesc('stock_ins.id')
                            ->get([
                                'stock_in_unit_prices.unit_type_id as unit_type_id',
                                'stock_in_unit_prices.price as price',
                                'unit_types.unit_name as unit_name',
                            ])
                            ->toArray();
                    }

                    if (empty($rows) && !empty($fallbackUnitPrices)) {
                        $rows = $fallbackUnitPrices;
                    }

                    $latestPriceByUnitType = [];
                    foreach ($rows as $r) {
                        $ut = (int) ($r->unit_type_id ?? 0);
                        $price = (float) ($r->price ?? 0);
                        if ($ut <= 0 || $price <= 0) {
                            continue;
                        }
                        if (!array_key_exists($ut, $latestPriceByUnitType)) {
                            $latestPriceByUnitType[$ut] = [
                                'unit_type_id' => $ut,
                                'unit_name' => $r->unit_name ?? null,
                                'price' => $price,
                            ];
                        }
                    }

                    $units = [];
                    foreach ($latestPriceByUnitType as $ut => $payload) {
                        $factor = isset($unitFactors[$ut]) ? (float) $unitFactors[$ut] : 1.0;
                        if (!is_finite($factor) || $factor <= 0) {
                            $factor = 1.0;
                        }

                        $baseAvailable = (float) ($bData['stock'] ?? 0);
                        $unitAvailable = $factor > 0 ? ($baseAvailable / $factor) : 0;

                        $units[] = [
                            'unit_type_id' => (int) $payload['unit_type_id'],
                            'unit_name' => $payload['unit_name'],
                            'stock' => (int) round($unitAvailable),
                            'price' => (float) $payload['price'],
                        ];
                    }

                    $branchStocks[$bId]['stock_units'] = $units;
                    $branchStocks[$bId]['latest_price'] = isset($units[0]) ? (float) ($units[0]['price'] ?? 0) : 0.00;
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
                                'price' => (float) ($u['price'] ?? 0.00),
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
                    'warranty_coverage_months' => (int) ($p->warranty_coverage_months ?? 0),
                    'branches' => $branches,
                ];
            })->values();

            Log::info('[POS_ADMIN_LOOKUP] Returning '.count($items)." items for keyword: '{$keyword}'");

            return response()->json(['items' => $items]);
        }

        // Exact matching phases similar to cashier POS
        $product = Product::where('barcode', $keyword)->first()
            ?: Product::where('model_number', $keyword)->first()
            ?: Product::where('product_name', $keyword)->first()
            ?: Product::where('product_name', 'LIKE', "%{$keyword}%")->first();

        if (! $product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        // Calculate available stock and branch-specific latest prices from stock_ins
        $stockRecords = StockIn::with('unitType')
            ->where('product_id', $product->id)
            ->orderBy('id', 'asc') // ensure later records override earlier ones
            ->get();
        $totalStock = 0;
        $branchStocks = [];

        $unitFactors = DB::table('product_unit_type')
            ->where('product_id', (int) $product->id)
            ->pluck('conversion_factor', 'unit_type_id')
            ->map(function ($v) {
                $f = (float) $v;
                return $f > 0 ? $f : 1.0;
            })
            ->toArray();

        $allBranchIds = Branch::pluck('id')->toArray();
        if ($electronicsOnly) {
            foreach ($allBranchIds as $bId) {
                $branch = Branch::find($bId);
                $branchStocks[$bId] = [
                    'branch_id' => (int) $bId,
                    'branch_name' => optional($branch)->branch_name,
                    'stock' => 0,
                    'stock_units' => [],
                    'latest_price' => 0.00,
                ];
            }
        }

        foreach ($stockRecords as $stock) {
            $availableStock = $stock->quantity - $stock->sold;
            $totalStock += $availableStock;

            if (! isset($branchStocks[$stock->branch_id])) {
                $branch = Branch::find($stock->branch_id);
                $branchStocks[$stock->branch_id] = [
                    'branch_id' => $stock->branch_id,
                    'branch_name' => optional($branch)->branch_name,
                    'stock' => 0,
                    'stock_units' => [],
                    'latest_price' => 0.00,
                ];
            }

            if ($availableStock > 0) {
                $branchStocks[$stock->branch_id]['stock'] += (int) $availableStock;
            }
        }

        $fallbackUnitPrices = [];
        if ($electronicsOnly && Schema::hasTable('stock_in_unit_prices')) {
            $fallbackUnitPrices = DB::table('stock_in_unit_prices')
                ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                ->leftJoin('unit_types', 'unit_types.id', '=', 'stock_in_unit_prices.unit_type_id')
                ->where('stock_ins.product_id', (int) $product->id)
                ->orderByDesc('stock_ins.id')
                ->get([
                    'stock_in_unit_prices.unit_type_id as unit_type_id',
                    'stock_in_unit_prices.price as price',
                    'unit_types.unit_name as unit_name',
                ])
                ->toArray();
        }

        foreach ($branchStocks as $bId => $bData) {
            $rows = [];
            if (Schema::hasTable('stock_in_unit_prices')) {
                $rows = DB::table('stock_in_unit_prices')
                    ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                    ->leftJoin('unit_types', 'unit_types.id', '=', 'stock_in_unit_prices.unit_type_id')
                    ->where('stock_ins.product_id', (int) $product->id)
                    ->where('stock_ins.branch_id', (int) $bId)
                    ->orderByDesc('stock_ins.id')
                    ->get([
                        'stock_in_unit_prices.unit_type_id as unit_type_id',
                        'stock_in_unit_prices.price as price',
                        'unit_types.unit_name as unit_name',
                    ])
                    ->toArray();
            }

            if (empty($rows) && !empty($fallbackUnitPrices)) {
                $rows = $fallbackUnitPrices;
            }

            $latestPriceByUnitType = [];
            foreach ($rows as $r) {
                $ut = (int) ($r->unit_type_id ?? 0);
                $price = (float) ($r->price ?? 0);
                if ($ut <= 0 || $price <= 0) {
                    continue;
                }
                if (!array_key_exists($ut, $latestPriceByUnitType)) {
                    $latestPriceByUnitType[$ut] = [
                        'unit_type_id' => $ut,
                        'unit_name' => $r->unit_name ?? null,
                        'price' => $price,
                    ];
                }
            }

            $units = [];
            foreach ($latestPriceByUnitType as $ut => $payload) {
                $factor = isset($unitFactors[$ut]) ? (float) $unitFactors[$ut] : 1.0;
                if (!is_finite($factor) || $factor <= 0) {
                    $factor = 1.0;
                }

                $baseAvailable = (float) ($bData['stock'] ?? 0);
                $unitAvailable = $factor > 0 ? ($baseAvailable / $factor) : 0;

                $units[] = [
                    'unit_type_id' => (int) $payload['unit_type_id'],
                    'unit_name' => $payload['unit_name'],
                    'stock' => (int) round($unitAvailable),
                    'price' => (float) $payload['price'],
                ];
            }

            $branchStocks[$bId]['stock_units'] = $units;
            $branchStocks[$bId]['latest_price'] = isset($units[0]) ? (float) ($units[0]['price'] ?? 0) : 0.00;
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
                        'price' => (float) ($u['price'] ?? 0.00),
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

        return response()->json([
            'product_id' => $product->id,
            'name' => $product->product_name,
            'barcode' => $product->barcode,
            // Default to first branch price for single-result lookup
            'price' => isset($byBranch[0]) ? (float) ($byBranch[0]['price'] ?? 0) : 0.00,
            'total_stock' => (int) $totalStock,
            'warranty_coverage_months' => (int) ($product->warranty_coverage_months ?? 0),
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
            $customerId = ! empty($data['customer_id']) ? $data['customer_id'] : null;
            $customerName = ! empty($data['customer_name']) ? trim($data['customer_name']) : null;
            $creditDueDate = $data['credit_due_date'] ?? null;
            $creditNotes = $data['credit_notes'] ?? null;

            Log::info('[POS_STORE] Processing order with '.count($items)." items, total: ₱{$total}");
            Log::info("[POS_STORE] Customer ID: '{$customerId}'");
            Log::info("[POS_STORE] Customer name: '{$customerName}'");

            DB::beginTransaction();

            // Determine the branch from the first item, assuming all items are from the same branch for a single transaction
            $branchId = Auth::user()->branch_id; // Default to cashier's branch
            if (! empty($items)) {
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
                'payment_method' => $paymentMethod, // Use payment method from request
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

                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) $unitTypeId)
                    ->value('conversion_factor') ?? 1);
                if ($factor <= 0) {
                    $factor = 1.0;
                }

                $requestedBaseQty = (float) $quantity * $factor;
                if (!is_finite($requestedBaseQty) || $requestedBaseQty <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid quantity for the selected unit type.',
                    ], 422);
                }

                // Find stock records for the specific branch and deduct in base units.
                // This allows selling in other unit types (e.g., grams) as long as conversion exists.
                $stockRecords = StockIn::where('product_id', $productId)
                    ->where('branch_id', $branchId)
                    ->where('quantity', '>', DB::raw('sold'))
                    ->orderBy('id', 'asc')
                    ->get();

                $remainingBaseQuantity = $requestedBaseQty;

                foreach ($stockRecords as $stock) {
                    if ($remainingBaseQuantity <= 0) break;

                    $availableStock = $stock->quantity - $stock->sold;
                    $toDeduct = min($remainingBaseQuantity, $availableStock);

                    $stock->sold += $toDeduct;
                    $stock->save();

                    $remainingBaseQuantity -= $toDeduct;

                    // Create corresponding StockOut record per stock_in batch deducted
                    \App\Models\StockOut::create([
                        'stock_in_id' => $stock->id,
                        'product_id' => $productId,
                        'sale_id' => $sale->id,
                        'quantity' => $toDeduct,
                        'branch_id' => $branchId,
                    ]);

                    Log::info("[POS_STORE] Updated stock record {$stock->id}: +{$toDeduct} sold (base units), remaining base qty to deduct: {$remainingBaseQuantity}");
                }
                
                if ($remainingBaseQuantity > 0) {
                    DB::rollBack();
                    Log::error("[POS_STORE] Insufficient stock for product {$productId}");

                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$item['name']}",
                    ], 422);
                }

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
                $referenceNumber = 'CR-'.date('Y').'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Determine customer_id for credit
                $creditCustomerId = $customerId;
                if (! $creditCustomerId && $customerName) {
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
                            'updated_at' => now(),
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
                    'notes' => $creditNotes,
                ]);

                Log::info("[POS_STORE] Created credit record for sale #{$sale->id}");
            }

            DB::commit();

            Log::info("[POS_STORE] Order processed successfully: Sale #{$sale->id}");

            $response = [
                'success' => true,
                'message' => 'Order processed successfully',
                'order_id' => $sale->id,
            ];

            $response['receipt_pdf_url'] = route('admin.sales.receipt.pdf', $sale);

            // If payment method is cash, include receipt URL for automatic display
            if ($paymentMethod === 'cash') {
                $response['receipt_url'] = route('admin.sales.receipt', $sale);
                $response['auto_receipt'] = true;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[POS_STORE] Order processing failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function electronicsStore(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $items = $data['items'] ?? [];
            $total = $data['total'] ?? 0;
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $requestedOrderStatus = $data['order_status'] ?? 'completed';
            $notes = isset($data['notes']) ? trim((string) $data['notes']) : null;
            $customerId = !empty($data['customer_id']) ? $data['customer_id'] : null;
            $customerName = !empty($data['customer_name']) ? trim($data['customer_name']) : null;
            $customerCompanySchoolName = !empty($data['customer_company_school_name']) ? trim($data['customer_company_school_name']) : null;
            $customerPhone = !empty($data['customer_phone']) ? trim($data['customer_phone']) : null;
            $customerEmail = !empty($data['customer_email']) ? trim($data['customer_email']) : null;
            $customerFacebook = !empty($data['customer_facebook']) ? trim($data['customer_facebook']) : null;
            $customerAddress = !empty($data['customer_address']) ? trim($data['customer_address']) : null;
            $creditDueDate = $data['credit_due_date'] ?? null;
            $creditNotes = $data['credit_notes'] ?? null;

            Log::info("[POS_ELECTRONICS_STORE] Processing order with " . count($items) . " items, total: ₱{$total}");

            DB::beginTransaction();

            $branchId = Auth::user()->branch_id;
            if (!empty($items)) {
                $firstItem = reset($items);
                $branchId = $firstItem['branch_id'] ?? $branchId;
            }

            $shouldBePending = ($requestedOrderStatus === 'pending');
            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $itemBranchId = $item['branch_id'] ?? null;
                $unitTypeId = isset($item['unit_type_id']) ? (int) $item['unit_type_id'] : null;
                $quantity = $item['quantity'] ?? null;

                if (empty($productId) || empty($itemBranchId) || empty($unitTypeId)) {
                    continue;
                }

                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) $unitTypeId)
                    ->value('conversion_factor') ?? 1);
                if ($factor <= 0) {
                    $factor = 1.0;
                }

                $requestedBaseQty = (float) $quantity * $factor;
                $availableBaseQty = (float) (StockIn::where('product_id', (int) $productId)
                    ->where('branch_id', (int) $itemBranchId)
                    ->selectRaw('COALESCE(SUM(quantity - sold), 0) AS available')
                    ->value('available') ?? 0);

                if ($requestedBaseQty > $availableBaseQty) {
                    $shouldBePending = true;
                    break;
                }
            }

            $saleData = [
                'cashier_id' => Auth::id(),
                'employee_id' => Auth::id(),
                'branch_id' => $branchId,
                'total_amount' => $total,
                'tax' => 0,
                'payment_method' => $paymentMethod,
            ];

            if ($notes !== null && $notes !== '' && \Illuminate\Support\Facades\Schema::hasColumn('sales', 'notes')) {
                $saleData['notes'] = $notes;
            }

            $resolvedCustomerId = $customerId;
            if ($resolvedCustomerId === null) {
                $hasAnyCustomerData = ($customerName !== null && $customerName !== '')
                    || ($customerPhone !== null && $customerPhone !== '')
                    || ($customerEmail !== null && $customerEmail !== '')
                    || ($customerFacebook !== null && $customerFacebook !== '')
                    || ($customerCompanySchoolName !== null && $customerCompanySchoolName !== '')
                    || ($customerAddress !== null && $customerAddress !== '');

                if ($hasAnyCustomerData) {
                    $customer = CustomerService::findOrCreateCustomer([
                        'full_name' => $customerName ?? '',
                        'company_school_name' => $customerCompanySchoolName,
                        'phone' => $customerPhone,
                        'email' => $customerEmail,
                        'facebook' => $customerFacebook,
                        'address' => $customerAddress,
                    ], $branchId);
                    $resolvedCustomerId = $customer ? $customer->id : null;
                }
            }

            if ($resolvedCustomerId !== null) {
                $saleData['customer_id'] = $resolvedCustomerId;
            }

            $sale = Sale::create($saleData);

            if ($shouldBePending) {
                $sale->status = 'pending';
                $sale->save();
            }

            foreach ($items as $item) {
                $productId = $item['product_id'] ?? null;
                $branchId = $item['branch_id'] ?? null;
                $unitTypeId = isset($item['unit_type_id']) ? (int) $item['unit_type_id'] : null;
                $quantity = $item['quantity'] ?? null;
                $price = $item['price'] ?? null;
                $serialNumber = isset($item['serial_number']) ? trim((string) $item['serial_number']) : '';
                $warrantyMonths = isset($item['warranty_months']) ? (int) $item['warranty_months'] : 0;

                if (empty($productId) || empty($branchId) || empty($unitTypeId)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Product, branch, and unit type are required for each item.',
                    ], 422);
                }

                if ((float) $quantity !== 1.0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Electronic devices require quantity of 1 per item (per serial).',
                    ], 422);
                }

                if (!$shouldBePending && $serialNumber === '') {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Serial number is required for in-stock electronic devices.',
                    ], 422);
                }

                if ($warrantyMonths < 0) {
                    $warrantyMonths = 0;
                }

                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) $unitTypeId)
                    ->value('conversion_factor') ?? 1);
                if ($factor <= 0) {
                    $factor = 1.0;
                }

                $requestedBaseQty = (float) $quantity * $factor;
                if (!is_finite($requestedBaseQty) || $requestedBaseQty <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid quantity for the selected unit type.',
                    ], 422);
                }

                if (!$shouldBePending) {
                    $stockRecords = StockIn::where('product_id', $productId)
                        ->where('branch_id', $branchId)
                        ->where('quantity', '>', DB::raw('sold'))
                        ->orderBy('id', 'asc')
                        ->get();

                    $remainingBaseQuantity = $requestedBaseQty;

                    foreach ($stockRecords as $stock) {
                        if ($remainingBaseQuantity <= 0) {
                            break;
                        }

                        $availableStock = $stock->quantity - $stock->sold;
                        $toDeduct = min($remainingBaseQuantity, $availableStock);

                        $stock->sold += $toDeduct;
                        $stock->save();

                        $remainingBaseQuantity -= $toDeduct;

                        \App\Models\StockOut::create([
                            'stock_in_id' => $stock->id,
                            'product_id' => $productId,
                            'sale_id' => $sale->id,
                            'quantity' => $toDeduct,
                            'branch_id' => $branchId,
                        ]);
                    }

                    if ($remainingBaseQuantity > 0) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for product: " . ($item['name'] ?? 'Unknown'),
                        ], 422);
                    }
                }

                $saleItemPayload = [
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $price * $quantity,
                ];

                if (\Illuminate\Support\Facades\Schema::hasColumn('sale_items', 'warranty_months')) {
                    $saleItemPayload['warranty_months'] = $warrantyMonths;
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('sale_items', 'unit_type_id')) {
                    $saleItemPayload['unit_type_id'] = $unitTypeId;
                }

                $saleItem = SaleItem::create($saleItemPayload);

                $warrantyExpiry = null;
                if ($warrantyMonths > 0) {
                    $warrantyExpiry = Carbon::now()->addMonths($warrantyMonths)->toDateString();
                }

                if (!$shouldBePending) {
                    $serial = ProductSerial::where('serial_number', $serialNumber)->first();
                    if (!$serial) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid serial number (not found in inventory).',
                        ], 422);
                    }

                    if ((int) $serial->product_id !== (int) $productId) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Serial number does not match the selected product.',
                        ], 422);
                    }

                    if ((int) $serial->branch_id !== (int) $branchId) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Serial number does not belong to the selected branch.',
                        ], 422);
                    }

                    if ($serial->status !== 'in_stock') {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Serial number is not available (already sold/invalid).',
                        ], 422);
                    }

                    $serial->status = 'sold';
                    $serial->sold_at = now();
                    $serial->sale_item_id = $saleItem->id;
                    $serial->warranty_expiry_date = $warrantyExpiry;
                    $serial->save();
                }
            }

            if ($paymentMethod === 'credit' && $creditDueDate) {
                $lastCredit = Credit::orderBy('id', 'desc')->first();
                $nextNumber = $lastCredit ? $lastCredit->id + 1 : 1;
                $referenceNumber = 'CR-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $creditCustomerId = $customerId;
                if (!$creditCustomerId && $customerName) {
                    $existingCustomer = DB::table('customers')->where('full_name', $customerName)->first();
                    if ($existingCustomer) {
                        $creditCustomerId = $existingCustomer->id;
                    } else {
                        $creditCustomerId = DB::table('customers')->insertGetId([
                            'full_name' => $customerName,
                            'email' => null,
                            'phone' => null,
                            'address' => null,
                            'max_credit_limit' => 0,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
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
                    'notes' => $creditNotes,
                ]);
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Order processed successfully',
                'order_id' => $sale->id,
            ];

            $response['receipt_pdf_url'] = route('admin.sales.receipt.pdf', $sale);

            if (!$shouldBePending && $paymentMethod === 'cash') {
                $response['receipt_url'] = route('admin.sales.receipt', $sale);
                $response['auto_receipt'] = true;
            }

            if ($shouldBePending) {
                $response['message'] = 'Order saved as pending (quotation).';
            }

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[POS_ELECTRONICS_STORE] Order processing failed: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function validateCashier(Request $request)
    {
        // Placeholder for validateCashier method
        return response()->json(['message' => 'Cashier validation not implemented yet']);
    }
}
