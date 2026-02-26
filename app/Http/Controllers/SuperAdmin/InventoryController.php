<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter', 'all');

        if (! in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
            $sortBy = 'product_name';
        }

        $productsQuery = Product::with(['brand', 'category', 'stockIns', 'saleItems']);

        if ($search) {
            $productsQuery->where('product_name', 'like', "%{$search}%")
                ->orWhereHas('brand', function ($q) use ($search) {
                    $q->where('brand_name', 'like', "%{$search}%");
                })
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('category_name', 'like', "%{$search}%");
                });
        }

        // Apply filter based on parameter
        if ($filter !== 'all') {
            switch ($filter) {
                case 'out-of-stock':
                    // Filter will be applied after getting results using the accessor
                    break;
            }
        }

        $products = $productsQuery->get()->sortBy([
            [$sortBy, $sortDirection],
        ]);

        // Apply out-of-stock filter if needed
        if ($filter === 'out-of-stock') {
            $products = $products->filter(function ($product) {
                return $product->current_stock <= 15;
            });
        }

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 15),
            $products->count(),
            15,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('SuperAdmin.inventory.index', [
            'products' => $products->appends($request->query()),
            'branches' => $branches,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'filter' => $filter,
        ])->with('branchesJson', $branches->toJson());
    }

    public function stockIn(Request $request, Product $product)
    {
        // When coming from the Stock Management modal with purchase-based stock-in,
        // delegate to the dedicated flow that enforces purchase → stock traceability.
        if ($request->expectsJson() && $request->filled('purchase_id')) {
            return $this->adjustFromPurchase($request, $product);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $product->stockIns()->create([
            'quantity' => $request->quantity,
            'initial_quantity' => $request->quantity, // Save original quantity
            'branch_id' => $request->branch_id,
            'reason' => 'Stock In',
        ]);

        return back()->with('success', 'Stock added successfully.');
    }

    public function adjust(Request $request, Product $product)
    {
        // Branch-to-branch transfer from the Stock Management modal
        if ($request->expectsJson() && $request->input('adjustment_type') === 'transfer') {
            return $this->adjustFromTransfer($request, $product);
        }

        $request->validate([
            'new_stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
            'adjustment_type' => 'required|in:set,add,subtract',
        ]);

        try {
            $newStock = $request->input('new_stock');
            $reason = $request->input('reason');
            $adjustmentType = $request->input('adjustment_type');

            // Get current stock
            $currentStock = $product->current_stock;

            // Validate adjustment
            if ($adjustmentType === 'subtract' && $newStock > $currentStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove more stock than currently available',
                ], 400);
            }

            // Create stock adjustment record
            $product->stockIns()->create([
                'quantity' => $newStock,
                'sold' => 0,
                'price' => DB::table('stock_ins')->where('product_id', $product->id)->where('price', '>', 0)->avg('price') ?? 0,
                'reason' => $reason,
                'notes' => "Manual adjustment: {$adjustmentType} (was {$currentStock})",
                'branch_id' => 1, // Default branch or get from product
            ]);

            // Update out-of-stock count
            $outOfStockCount = DB::table('products')
                ->leftJoin('stock_ins', 'products.id', '=', 'stock_ins.product_id')
                ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
                ->select(
                    'products.id',
                    DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock')
                )
                ->groupBy('products.id')
                ->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15')
                ->count();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'new_stock' => $newStock,
                'outOfStockCount' => $outOfStockCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Error adjusting stock: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock',
            ], 500);
        }
    }

    public function getProductStock($productId)
    {
        try {
            // Get stock information for this product across all branches
            $stockData = DB::table('stock_ins')
                ->join('branches', 'stock_ins.branch_id', '=', 'branches.id')
                ->where('stock_ins.product_id', $productId)
                ->select(
                    'branches.id as branch_id',
                    'branches.branch_name',
                    DB::raw('SUM(stock_ins.quantity - stock_ins.sold) as current_stock')
                )
                ->groupBy('branches.id', 'branches.branch_name')
                ->get();

            return response()->json($stockData);
        } catch (\Exception $e) {
            Log::error('Error fetching product stock: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch stock data'], 500);
        }
    }

    public function getProductPurchases($productId)
    {
        try {
            // Get purchases that contain this product through the relationship
            $purchases = \App\Models\Purchase::with('items')
                ->whereHas('items', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->get();

            // Format the response
            $purchaseData = $purchases->map(function ($purchase) use ($productId) {
                $purchasedQty = (int) $purchase->items
                    ->where('product_id', (int) $productId)
                    ->sum('quantity');

                $stockRows = \App\Models\StockIn::where('purchase_id', $purchase->id)
                    ->where('product_id', (int) $productId)
                    ->get(['initial_quantity', 'sold']);

                $stockedQty = (int) $stockRows->sum('initial_quantity');
                $soldQty = (int) $stockRows->sum('sold');

                $remainingToStock = max(0, $purchasedQty - $stockedQty);
                $availableQty = max(0, $stockedQty - $soldQty);

                return [
                    'id' => $purchase->id,
                    'purchase_date' => $purchase->purchase_date,
                    'purchased_qty' => $purchasedQty,
                    'stocked_qty' => $stockedQty,
                    'sold_qty' => $soldQty,
                    'remaining_to_stock' => $remainingToStock,
                    'available_qty' => $availableQty,
                ];
            });

            return response()->json($purchaseData);
        } catch (\Exception $e) {
            Log::error('Error fetching product purchases: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch purchase data'], 500);
        }
    }

    public function getBranches()
    {
        try {
            $branches = \App\Models\Branch::all();

            return response()->json($branches);
        } catch (\Exception $e) {
            Log::error('Error fetching branches: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch branches'], 500);
        }
    }

    public function getProductSales($productId)
    {
        try {
            // Get sales data for this product over the last 14 days, grouped by branch and date.
            // Returns an array of rows: [{ branch_name, date, quantity }, ...]
            $salesData = DB::table('sales')
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->join('branches', 'sales.branch_id', '=', 'branches.id')
                ->where('sale_items.product_id', $productId)
                ->where('sales.created_at', '>=', now()->subDays(14))
                ->select(
                    'branches.branch_name',
                    DB::raw('DATE(sales.created_at) as date'),
                    DB::raw('SUM(sale_items.quantity) as quantity')
                )
                ->groupBy('branches.branch_name', DB::raw('DATE(sales.created_at)'))
                ->orderBy('date', 'asc')
                ->get();

            return response()->json($salesData);
        } catch (\Exception $e) {
            Log::error('Error fetching product sales: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch sales data'], 500);
        }
    }

    private function adjustFromPurchase(Request $request, Product $product)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'purchase_quantity' => 'required|integer|min:1',
        ]);

        // Product must be active
        if ($product->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This product is not active and cannot receive stock from purchase.',
            ], 400);
        }

        $purchaseId = (int) $request->input('purchase_id');
        $quantity = (int) $request->input('purchase_quantity');

        // Resolve target branch from request or product
        $branchId = (int) $request->input('branch_id', $product->branch_id ?? 1);

        // Branch must exist and be active
        $branch = Branch::find($branchId);
        if (! $branch || $branch->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'The selected branch is not active or does not accept inventory.',
            ], 400);
        }

        // Purchase must exist and (if branch-specific) belong to the same branch
        $purchase = Purchase::find($purchaseId);
        if (! $purchase) {
            return response()->json([
                'success' => false,
                'message' => 'The selected purchase could not be found.',
            ], 404);
        }

        if (! is_null($purchase->branch_id) && (int) $purchase->branch_id !== $branchId) {
            return response()->json([
                'success' => false,
                'message' => 'The selected purchase belongs to a different branch and cannot be used for this stock-in.',
            ], 400);
        }

        // Validation: Check remaining quantity from purchase for this product
        $purchasedQty = \App\Models\PurchaseItem::where('purchase_id', $purchaseId)
            ->where('product_id', $product->id)
            ->value('quantity');

        if ($purchasedQty === null) {
            return response()->json([
                'success' => false,
                'message' => 'The selected purchase does not contain this product.',
            ], 400);
        }

        $alreadyStocked = \App\Models\StockIn::where('purchase_id', $purchaseId)
            ->where('product_id', $product->id)
            ->sum('initial_quantity');

        $soldFromPurchase = \App\Models\StockIn::where('purchase_id', $purchaseId)
            ->where('product_id', $product->id)
            ->sum('sold');

        $remaining = (int) $purchasedQty - (int) $alreadyStocked;

        if ($quantity <= 0 || $quantity > $remaining) {
            return response()->json([
                'success' => false,
                'message' => "Cannot stock more than remaining purchasable quantity. Remaining: {$remaining} units from Purchase #{$purchaseId}",
            ], 400);
        }

        // Perform stock-in inside a DB transaction for safety
        DB::transaction(function () use ($product, $branchId, $quantity, $purchaseId) {
            $product->stockIns()->create([
                'quantity' => $quantity,
                'initial_quantity' => $quantity, // Save original quantity
                'sold' => 0,
                'branch_id' => $branchId,
                'price' => 0,
                'purchase_id' => $purchaseId,
            ]);
        });

        // Update out-of-stock count (for dashboard/sidebar widgets)
        $outOfStockCount = DB::table('products')
            ->leftJoin('stock_ins', 'products.id', '=', 'stock_ins.product_id')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('branches', 'stock_ins.branch_id', '=', 'branches.id')
            ->select(
                'products.id',
                'products.product_name',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock')
            )
            ->groupBy('products.id', 'products.product_name', 'branches.id', 'branches.branch_name')
            ->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15')
            ->count();

        return response()->json([
            'success' => true,
            'message' => "Stock adjusted successfully. Added {$quantity} units from purchase #{$purchaseId}. Remaining to stock: {$remaining} units.",
            'outOfStockCount' => $outOfStockCount,
            'purchased_qty' => (int) $purchasedQty,
            'stocked_qty' => (int) $alreadyStocked + (int) $quantity,
            'sold_qty' => (int) $soldFromPurchase,
            'available_qty' => max(0, ($alreadyStocked + $quantity) - $soldFromPurchase),
        ]);
    }

    private function adjustFromTransfer(Request $request, Product $product)
    {
        $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'to_branch' => 'required|exists:branches,id',
            'transfer_quantity' => 'required|integer|min:1',
        ]);

        // Product must be active
        if ($product->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This product is not active and cannot be transferred between branches.',
            ], 400);
        }

        $fromBranchId = (int) $request->input('from_branch');
        $toBranchId = (int) $request->input('to_branch');
        $quantity = (int) $request->input('transfer_quantity');

        // Source branch must be active
        $fromBranch = Branch::find($fromBranchId);
        if (! $fromBranch || $fromBranch->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'The source branch is not active and cannot be used for transfer.',
            ], 400);
        }

        if ($toBranchId === $fromBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'Source and destination branches must be different for a stock transfer.',
            ], 400);
        }

        $toBranch = Branch::find($toBranchId);
        if (! $toBranch || $toBranch->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'The destination branch is not active and cannot receive transferred stock.',
            ], 400);
        }

        // Validate transfer amount strictly per source branch using (quantity - sold)
        $availableInSource = \App\Models\StockIn::where('product_id', $product->id)
            ->where('branch_id', $fromBranchId)
            ->sum(DB::raw('quantity - sold'));

        if ($quantity <= 0 || $quantity > $availableInSource) {
            return response()->json([
                'success' => false,
                'message' => "Cannot transfer more units than available in source branch. Available: {$availableInSource}",
            ], 400);
        }

        // Perform transfer inside a DB transaction for safety
        DB::transaction(function () use ($product, $fromBranchId, $toBranchId, $quantity) {
            // Find stock_in record from source branch
            $sourceStock = $product->stockIns()
                ->where('branch_id', $fromBranchId)
                ->whereColumn('quantity', '>', 'sold') // Has available stock
                ->lockForUpdate()
                ->first();

            if (! $sourceStock) {
                throw new \RuntimeException('Source branch has insufficient stock for transfer.');
            }

            $availableStock = $sourceStock->quantity - $sourceStock->sold;
            $transferAmount = min($quantity, $availableStock);

            if ($transferAmount <= 0) {
                throw new \RuntimeException('No transferable stock available in source branch.');
            }

            // Update source branch (reduce stock)
            $sourceStock->increment('sold', $transferAmount);

            // Create new stock_in record for destination branch
            $product->stockIns()->create([
                'quantity' => $transferAmount,
                'initial_quantity' => $transferAmount, // Save original quantity
                'sold' => 0,
                'branch_id' => $toBranchId,
                'price' => 0,
            ]);
        });

        // Update out-of-stock count
        $outOfStockCount = DB::table('products')
            ->leftJoin('stock_ins', 'products.id', '=', 'stock_ins.product_id')
            ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->leftJoin('branches', 'stock_ins.branch_id', '=', 'branches.id')
            ->select(
                'products.id',
                'products.product_name',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock')
            )
            ->groupBy('products.id', 'products.product_name', 'branches.id', 'branches.branch_name')
            ->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15')
            ->count();

        return response()->json([
            'success' => true,
            'message' => "Successfully transferred {$quantity} units from source branch to destination branch.",
            'outOfStockCount' => $outOfStockCount,
        ]);
    }

    public function outOfStock(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $branchId = $request->query('branch_id');

        if (! in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
            $sortBy = 'product_name';
        }

        // Build base query with stock calculations
        $productsQuery = DB::table('products')
            ->leftJoin('stock_ins', function ($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function ($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->leftJoin('branches', function ($join) {
                $join->on('stock_ins.branch_id', '=', 'branches.id');
            })
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.product_name',
                'brands.brand_name',
                'categories.category_name',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock'),
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sale_items.subtotal), 0) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name', 'brands.brand_name', 'categories.category_name', 'branches.id', 'branches.branch_name');

        // Apply search filter
        if ($search) {
            $productsQuery->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('brands.brand_name', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%");
        }

        // Apply branch filter
        if ($branchId) {
            $productsQuery->where('stock_ins.branch_id', $branchId);
        }

        // Filter for out-of-stock items (≤ 15 units)
        $productsQuery->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15');

        // Apply sorting
        if ($sortBy === 'product_name') {
            $productsQuery->orderBy('products.product_name', $sortDirection);
        } elseif ($sortBy === 'current_stock') {
            $productsQuery->orderByRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0))', $sortDirection);
        } elseif ($sortBy === 'total_sold') {
            $productsQuery->orderBy('total_sold', $sortDirection);
        } elseif ($sortBy === 'total_revenue') {
            $productsQuery->orderBy('total_revenue', $sortDirection);
        } else {
            $productsQuery->orderBy('products.product_name', 'asc');
        }

        $products = $productsQuery->paginate(15);

        // Set current_branch_id for each product
        $productIds = $products->getCollection()->pluck('id');
        \App\Models\Product::whereIn('id', $productIds)->get()->each(function ($productModel) use ($products) {
            $collectionProduct = $products->firstWhere('id', $productModel->id);
            if ($collectionProduct) {
                $productModel->setAttribute('current_branch_id', $collectionProduct->branch_id);
            }
        });

        $totalOutOfStock = $productsQuery->count();

        // Store the count and total in session for sidebar display
        session([
            'out_of_stock_count' => $totalOutOfStock,
            'out_of_stock_total' => $totalOutOfStock,
        ]);

        return view('SuperAdmin.inventory.out-of-stock', [
            'products' => $products->appends($request->query()),
            'branches' => $branches,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'selectedBranchId' => $branchId,
        ])->with('branchesJson', $branches->toJson());
    }

    public function stockManagement(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $branchId = $request->query('branch_id');

        if (! in_array($sortBy, ['product_name', 'current_stock', 'unit_price', 'last_updated'])) {
            $sortBy = 'product_name';
        }

        // Build base query with stock calculations
        $productsQuery = DB::table('products')
            ->leftJoin('stock_ins', function ($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function ($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->leftJoin('branches', function ($join) {
                $join->on('stock_ins.branch_id', '=', 'branches.id');
            })
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.product_name',
                'brands.brand_name',
                'categories.category_name',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock'),
                DB::raw('MAX(stock_ins.created_at) as last_stock_update'),
                DB::raw('(SELECT AVG(price) FROM stock_ins WHERE product_id = products.id AND price > 0 LIMIT 1) as unit_price')
            )
            ->groupBy('products.id', 'products.product_name', 'brands.brand_name', 'categories.category_name', 'branches.id', 'branches.branch_name');

        // Apply search filter
        if ($search) {
            $productsQuery->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('brands.brand_name', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%");
        }

        // Apply branch filter
        if ($branchId) {
            $productsQuery->where('stock_ins.branch_id', $branchId);
        }

        // Apply sorting
        if ($sortBy === 'product_name') {
            $productsQuery->orderBy('products.product_name', $sortDirection);
        } elseif ($sortBy === 'current_stock') {
            $productsQuery->orderByRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0))', $sortDirection);
        } elseif ($sortBy === 'unit_price') {
            $productsQuery->orderByRaw('(SELECT AVG(price) FROM stock_ins WHERE product_id = products.id AND price > 0 LIMIT 1)', $sortDirection);
        } elseif ($sortBy === 'last_updated') {
            $productsQuery->orderBy('last_stock_update', $sortDirection);
        } else {
            $productsQuery->orderBy('products.product_name', 'asc');
        }

        $products = $productsQuery->paginate(15);

        // Calculate stock counts
        $lowStockCount = DB::table('products')
            ->leftJoin('stock_ins', function ($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function ($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->select(
                'products.id',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock')
            )
            ->groupBy('products.id')
            ->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) > 0 AND (COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15')
            ->count();

        $outOfStockCount = DB::table('products')
            ->leftJoin('stock_ins', function ($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function ($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->select(
                'products.id',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock')
            )
            ->groupBy('products.id')
            ->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 0')
            ->count();

        return view('SuperAdmin.inventory.stock-management', [
            'products' => $products->appends($request->query()),
            'branches' => $branches,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'selectedBranchId' => $branchId,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
        ])->with('branchesJson', $branches->toJson());
    }

    public function getProductStockHistory($productId)
    {
        try {
            $productId = (int) $productId;

            // 1) Stock-ins across all branches (manual + from purchase)
            $stockIns = DB::table('stock_ins')
                ->leftJoin('purchases', 'stock_ins.purchase_id', '=', 'purchases.id')
                ->leftJoin('branches', 'stock_ins.branch_id', '=', 'branches.id')
                ->where('stock_ins.product_id', $productId)
                ->select([
                    'stock_ins.quantity',
                    'stock_ins.price',
                    'stock_ins.created_at',
                    'branches.branch_name',
                    'purchases.id as purchase_id',
                ])
                ->orderByDesc('stock_ins.created_at')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    $reason = $row->purchase_id
                        ? 'Stock in from Purchase #'.$row->purchase_id
                        : 'Manual Stock In';

                    return [
                        'type' => 'in',
                        'quantity' => (int) $row->quantity,
                        'price' => $row->price,
                        'branch_name' => $row->branch_name ?? 'N/A',
                        'reason' => $reason,
                        'notes' => null,
                        'created_at' => $row->created_at,
                    ];
                });

            // 2) Sales stock-outs across all branches
            $sales = DB::table('sales')
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
                ->where('sale_items.product_id', $productId)
                ->select([
                    'sale_items.quantity',
                    'sales.created_at',
                    'branches.branch_name',
                ])
                ->orderByDesc('sales.created_at')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    return [
                        'type' => 'out',
                        'quantity' => (int) $row->quantity,
                        'price' => null,
                        'branch_name' => $row->branch_name ?? 'N/A',
                        'reason' => 'Sale',
                        'notes' => null,
                        'created_at' => $row->created_at,
                    ];
                });

            // 3) Transfer movements across all branches for this product
            $transfers = DB::table('stock_transfers')
                ->join('branches as from_b', 'stock_transfers.from_branch_id', '=', 'from_b.id')
                ->join('branches as to_b', 'stock_transfers.to_branch_id', '=', 'to_b.id')
                ->where('stock_transfers.product_id', $productId)
                ->select([
                    'stock_transfers.quantity',
                    'stock_transfers.status',
                    'stock_transfers.notes',
                    'stock_transfers.created_at',
                    'from_b.branch_name as from_branch_name',
                    'to_b.branch_name as to_branch_name',
                    'stock_transfers.from_branch_id',
                    'stock_transfers.to_branch_id',
                ])
                ->orderByDesc('stock_transfers.created_at')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    // Represent transfers from both perspectives in a neutral way
                    $reason = 'Transfer '.$row->from_branch_name.' → '.$row->to_branch_name;

                    return [
                        'type' => 'transfer',
                        'quantity' => (int) $row->quantity,
                        'price' => null,
                        'branch_name' => $row->from_branch_name.' → '.$row->to_branch_name,
                        'reason' => $reason,
                        'notes' => $row->notes,
                        'created_at' => $row->created_at,
                    ];
                });

            // Merge all movement types and sort by created_at DESC, then take latest 200
            $history = $stockIns
                ->merge($sales)
                ->merge($transfers)
                ->sortByDesc('created_at')
                ->values()
                ->take(200)
                ->all();

            return response()->json(['history' => $history]);
        } catch (\Exception $e) {
            Log::error('Error fetching stock history: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch stock history'], 500);
        }
    }

    public function bulkStockAdjustment(Request $request)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,subtract,set',
            'value' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $adjustmentType = $request->adjustment_type;
            $value = $request->value;
            $reason = $request->reason;

            // Get all products with their current stock
            $products = DB::table('products')
                ->leftJoin('stock_ins', function ($join) {
                    $join->on('products.id', '=', 'stock_ins.product_id');
                })
                ->leftJoin('sale_items', function ($join) {
                    $join->on('products.id', '=', 'sale_items.product_id');
                })
                ->select(
                    'products.id',
                    DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock')
                )
                ->groupBy('products.id')
                ->get();

            $updatedCount = 0;

            foreach ($products as $product) {
                $currentStock = $product->current_stock ?? 0;
                $newStock = $currentStock;

                if ($adjustmentType === 'add') {
                    $newStock = $currentStock + $value;
                } elseif ($adjustmentType === 'subtract') {
                    $newStock = max(0, $currentStock - $value);
                } elseif ($adjustmentType === 'set') {
                    $newStock = $value;
                }

                // Only update if there's a change
                if ($newStock !== $currentStock) {
                    // Create stock adjustment record
                    DB::table('stock_ins')->insert([
                        'product_id' => $product->id,
                        'quantity' => $newStock,
                        'sold' => 0,
                        'price' => 0,
                        'reason' => $reason,
                        'notes' => "Bulk adjustment: {$adjustmentType} {$value} (was {$currentStock})",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Bulk adjustment completed. Updated {$updatedCount} products.",
                'updated_count' => $updatedCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk stock adjustment: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk adjustment',
            ], 500);
        }
    }

    public function exportOutOfStockPDF(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $branchId = $request->query('branch_id');

        if (! in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
            $sortBy = 'product_name';
        }

        // Build base query with stock calculations
        $productsQuery = DB::table('products')
            ->leftJoin('stock_ins', function ($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function ($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->leftJoin('branches', function ($join) {
                $join->on('stock_ins.branch_id', '=', 'branches.id');
            })
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.id',
                'products.product_name',
                'brands.brand_name',
                'categories.category_name',
                'branches.id as branch_id',
                'branches.branch_name',
                DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock'),
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'),
                DB::raw('COALESCE(SUM(sale_items.subtotal), 0) as total_revenue')
            )
            ->groupBy('products.id', 'products.product_name', 'brands.brand_name', 'categories.category_name', 'branches.id', 'branches.branch_name');

        // Apply search filter
        if ($search) {
            $productsQuery->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('brands.brand_name', 'like', "%{$search}%")
                ->orWhere('categories.category_name', 'like', "%{$search}%");
        }

        // Apply branch filter
        if ($branchId) {
            $productsQuery->where('stock_ins.branch_id', $branchId);
        }

        // Filter for out-of-stock items (≤ 15 units)
        $productsQuery->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15');

        // Apply sorting
        if ($sortBy === 'product_name') {
            $productsQuery->orderBy('products.product_name', $sortDirection);
        } elseif ($sortBy === 'current_stock') {
            $productsQuery->orderByRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0))', $sortDirection);
        } elseif ($sortBy === 'total_sold') {
            $productsQuery->orderBy('total_sold', $sortDirection);
        } elseif ($sortBy === 'total_revenue') {
            $productsQuery->orderBy('total_revenue', $sortDirection);
        } else {
            $productsQuery->orderBy('products.product_name', 'asc');
        }

        $products = $productsQuery->get();

        // Debug: Check if we have products
        Log::info('Export Query Results', [
            'total_products' => $products->count(),
            'sample_products' => $products->take(3)->toArray(),
            'query_sql' => $productsQuery->toSql(),
            'bindings' => $productsQuery->getBindings(),
        ]);

        // Generate PDF with simplified approach
        try {
            // Configure DomPDF settings
            $options = new \Dompdf\Options;
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', false);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', false);

            $pdf = new \Dompdf\Dompdf($options);
            $pdf->setPaper('A4', 'landscape');

            // Generate simple HTML directly instead of using Blade view
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Out of Stock Report</title>
    <style>
        body { font-family: Arial; font-size: 12px; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Out of Stock Report</h1>
    <p>Generated: '.now()->format('F j, Y h:i A').'</p>
    <p>Total products needing purchase: '.$products->count().'</p>
    
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Stock</th>
                <th>Branch</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

            foreach ($products as $product) {
                $html .= '
                <tr>
                    <td>'.htmlspecialchars($product->product_name).'</td>
                    <td>'.$product->current_stock.'</td>
                    <td>'.htmlspecialchars($product->branch_name ?? 'All Branches').'</td>
                    <td>'.($product->current_stock <= 5 ? 'Critical' : 'Low Stock').'</td>
                </tr>';
            }

            $html .= '
        </tbody>
    </table>
</body>
</html>';

            Log::info('Simple HTML generated', [
                'html_length' => strlen($html),
                'products_count' => $products->count(),
            ]);

            $pdf->loadHtml($html);
            $pdfOutput = $pdf->output();

            Log::info('PDF generated successfully', [
                'output_length' => strlen($pdfOutput),
            ]);

            // Download PDF
            return response($pdfOutput)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="out-of-stock-'.date('Y-m-d').'.pdf"')
                ->header('Content-Length', strlen($pdfOutput));

        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return error response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF generation failed: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'PDF generation failed: '.$e->getMessage());
        }
    }
}
