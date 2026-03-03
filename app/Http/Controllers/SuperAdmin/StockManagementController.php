<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockManagementController extends Controller
{
    /**
     * Display stock management page with filters
     */
    public function index(Request $request)
    {
        // Resolve user branch for other operations, but show ALL branches in the main listing
        $branchId = $this->resolveBranchId($request);

        // For stock management overview, aggregate per product per branch across all branches
        $query = $this->baseStockQuery(null);

        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->string('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('products.product_name', 'like', "%{$searchTerm}%")
                    ->orWhere('products.barcode', 'like', "%{$searchTerm}%")
                    ->orWhere('products.model_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->integer('category_id'));
        }

        if ($request->filled('supplier_id')) {
            $supplierId = $request->integer('supplier_id');

            // Supplier is determined from: stock_ins.purchase_id -> purchases.supplier_id
            // For admin overview, consider all branches (branch filter can be added separately if needed)
            $query->whereExists(function ($exists) use ($supplierId) {
                $exists->select(DB::raw(1))
                    ->from('stock_ins')
                    ->join('purchases', 'stock_ins.purchase_id', '=', 'purchases.id')
                    ->whereColumn('stock_ins.product_id', 'products.id')
                    ->where('purchases.supplier_id', $supplierId);
            });
        }

        $stockLevels = $request->input('stock_levels');
        if (is_array($stockLevels) && count($stockLevels) > 0) {
            $this->applyStockLevelFilters($query, $stockLevels);
        }

        if ($request->filled('date_range')) {
            $this->applyDateRangeFilter($query, (string) $request->string('date_range'));
        }

        if ($request->filled('movement')) {
            $this->applyMovementFilter($query, (string) $request->string('movement'));
        }

        $this->applySorting(
            $query,
            (string) $request->string('sort_by', 'product_name'),
            Str::lower((string) $request->string('sort_direction', 'asc')) === 'desc' ? 'desc' : 'asc'
        );

        $products = $query->paginate(15)->appends($request->query());

        $categories = DB::table('categories')
            ->orderBy('category_name')
            ->pluck('category_name', 'id');

        $suppliers = DB::table('suppliers')
            ->orderBy('supplier_name')
            ->pluck('supplier_name', 'id');

        $branches = DB::table('branches')->orderBy('branch_name')->get();

        // Statistics across all branches
        $stockStats = $this->calculateStockStatistics(null);

        return view('superadmin.inventory.stock-management', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'branches' => $branches,
            'stockStats' => $stockStats,
            'selectedBranchId' => $branchId,
        ]);
    }

    /**
     * Branch-scoped stock management for cashiers.
     * Uses the authenticated user's branch_id so a cashier only sees their own branch.
     */
    public function cashierIndex(Request $request)
    {
        $user = $request->user();
        $branchId = (int) optional($user)->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Aggregate stock for this specific branch only
        $query = $this->baseStockQuery($branchId);

        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->string('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('products.product_name', 'like', "%{$searchTerm}%")
                    ->orWhere('products.barcode', 'like', "%{$searchTerm}%")
                    ->orWhere('products.model_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->integer('category_id'));
        }

        if ($request->filled('supplier_id')) {
            $supplierId = $request->integer('supplier_id');

            // Supplier is determined from: stock_ins.purchase_id -> purchases.supplier_id
            $query->whereExists(function ($exists) use ($branchId, $supplierId) {
                $exists->select(DB::raw(1))
                    ->from('stock_ins')
                    ->join('purchases', 'stock_ins.purchase_id', '=', 'purchases.id')
                    ->whereColumn('stock_ins.product_id', 'products.id')
                    ->where('stock_ins.branch_id', $branchId)
                    ->where('purchases.supplier_id', $supplierId);
            });
        }

        $stockLevels = $request->input('stock_levels');
        if (is_array($stockLevels) && count($stockLevels) > 0) {
            $this->applyStockLevelFilters($query, $stockLevels);
        }

        if ($request->filled('date_range')) {
            $this->applyDateRangeFilter($query, (string) $request->string('date_range'));
        }

        if ($request->filled('movement')) {
            $this->applyMovementFilter($query, (string) $request->string('movement'));
        }

        $this->applySorting(
            $query,
            (string) $request->string('sort_by', 'product_name'),
            Str::lower((string) $request->string('sort_direction', 'asc')) === 'desc' ? 'desc' : 'asc'
        );

        $products = $query->paginate(15)->appends($request->query());

        $categories = DB::table('categories')
            ->orderBy('category_name')
            ->pluck('category_name', 'id');

        $suppliers = DB::table('suppliers')
            ->orderBy('supplier_name')
            ->pluck('supplier_name', 'id');

        $branches = DB::table('branches')->orderBy('branch_name')->get();

        // Statistics for this cashier's branch only
        $stockStats = $this->calculateStockStatistics($branchId);

        return view('superadmin.inventory.stock-management', [
            'products' => $products,
            'categories' => $categories,
            'suppliers' => $suppliers,
            'branches' => $branches,
            'stockStats' => $stockStats,
            'selectedBranchId' => $branchId,
        ]);
    }

    /**
     * API endpoint for suppliers
     */
    public function getSuppliers()
    {
        $suppliers = DB::table('suppliers')
            ->select('id', 'supplier_name')
            ->orderBy('supplier_name')
            ->get();

        return response()->json($suppliers);
    }

    /**
     * API endpoint for filtered products
     */
    public function getFilteredProducts(Request $request)
    {
        // For API as well, default to all branches in the overview
        $branchId = $this->resolveBranchId($request);

        $query = $this->baseStockQuery(null);

        if ($request->filled('search')) {
            $searchTerm = trim((string) $request->string('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('products.product_name', 'like', "%{$searchTerm}%")
                    ->orWhere('products.barcode', 'like', "%{$searchTerm}%")
                    ->orWhere('products.model_number', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('products.category_id', $request->integer('category_id'));
        }

        if ($request->filled('supplier_id')) {
            $supplierId = $request->integer('supplier_id');
            $query->whereExists(function ($exists) use ($supplierId) {
                $exists->select(DB::raw(1))
                    ->from('stock_ins')
                    ->join('purchases', 'stock_ins.purchase_id', '=', 'purchases.id')
                    ->whereColumn('stock_ins.product_id', 'products.id')
                    ->where('purchases.supplier_id', $supplierId);
            });
        }

        $stockLevels = $request->input('stock_levels');
        if (is_array($stockLevels) && count($stockLevels) > 0) {
            $this->applyStockLevelFilters($query, $stockLevels);
        }

        $this->applySorting(
            $query,
            (string) $request->string('sort_by', 'product_name'),
            Str::lower((string) $request->string('sort_direction', 'asc')) === 'desc' ? 'desc' : 'asc'
        );

        $products = $query->limit(100)->get();

        return response()->json([
            'products' => $products,
            'total' => $products->count(),
            'branch_id' => $branchId,
        ]);
    }

    /**
     * Get product details for modal
     */
    public function getProductDetails($id)
    {
        $product = DB::table('products')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('products.id', (int) $id)
            ->select([
                'products.id',
                'products.product_name',
                'products.barcode',
                'products.model_number',
                'products.description',
                'products.min_stock_level',
                'products.max_stock_level',
                'products.low_stock_threshold',
                'products.updated_at',
                'categories.category_name as category_name',
                'suppliers.supplier_name as supplier_name',
                'brands.brand_name as brand_name',
            ])
            ->first();

        abort_if(! $product, 404);

        $branchId = $this->resolveBranchId(request());
        $currentStock = (int) (DB::table('stock_ins')
            ->where('product_id', (int) $id)
            ->where('branch_id', $branchId)
            ->selectRaw('COALESCE(SUM(quantity - sold), 0) as current_stock')
            ->value('current_stock') ?? 0);

        $min = (int) ($product->min_stock_level ?? 0);
        $max = (int) ($product->max_stock_level ?? 0);
        $low = (int) ($product->low_stock_threshold ?? 0);

        $effectiveMin = $min > 0 ? $min : 10;
        $effectiveMax = $max > 0 ? $max : 100;
        $effectiveLow = $low > 0 ? max($effectiveMin, $low) : $effectiveMin;

        return response()->json([
            'id' => $product->id,
            'product_name' => $product->product_name,
            'barcode' => $product->barcode,
            'model_number' => $product->model_number,
            'description' => $product->description ?? 'N/A',
            'category' => $product->category_name ?? 'N/A',
            'supplier' => $product->supplier_name ?? 'N/A',
            'brand' => $product->brand_name ?? 'N/A',
            'branch_id' => $branchId,
            'quantity' => $currentStock,
            'min_stock_level' => $effectiveMin,
            'low_stock_threshold' => $effectiveLow,
            'max_stock_level' => $effectiveMax,
            'updated_at' => $product->updated_at,
        ]);
    }

    /**
     * Get stock history for a product
     */
    public function getStockHistory($id)
    {
        $productExists = DB::table('products')->where('id', (int) $id)->exists();
        abort_if(! $productExists, 404);

        $branchId = $this->resolveBranchId(request());

        $productId = (int) $id;

        // 1) Stock-ins for this product and branch (manual + from purchase)
        $stockIns = DB::table('stock_ins')
            ->leftJoin('purchases', 'stock_ins.purchase_id', '=', 'purchases.id')
            ->leftJoin('branches', 'stock_ins.branch_id', '=', 'branches.id')
            ->where('stock_ins.product_id', $productId)
            ->where('stock_ins.branch_id', $branchId)
            ->select([
                'stock_ins.quantity',
                'stock_ins.price',
                'stock_ins.created_at',
                'branches.branch_name',
                'purchases.id as purchase_id',
            ])
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

        // 2) Sales stock-outs for this product and branch
        // Keep this query minimal and avoid relying on optional columns
        $sales = DB::table('sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
            ->where('sale_items.product_id', $productId)
            ->where('sales.branch_id', $branchId)
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

        // 3) Transfer movements for this product involving this branch
        $transfers = DB::table('stock_transfers')
            ->join('branches as from_b', 'stock_transfers.from_branch_id', '=', 'from_b.id')
            ->join('branches as to_b', 'stock_transfers.to_branch_id', '=', 'to_b.id')
            ->where('stock_transfers.product_id', $productId)
            ->where(function ($q) use ($branchId) {
                $q->where('stock_transfers.from_branch_id', $branchId)
                    ->orWhere('stock_transfers.to_branch_id', $branchId);
            })
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
            ->map(function ($row) use ($branchId) {
                $isOutgoing = (int) $row->from_branch_id === (int) $branchId;
                $type = $isOutgoing ? 'transfer_out' : 'transfer_in';
                $branchName = $isOutgoing ? ($row->from_branch_name ?? 'N/A') : ($row->to_branch_name ?? 'N/A');

                $reason = $isOutgoing
                    ? 'Transfer to '.$row->to_branch_name
                    : 'Transfer from '.$row->from_branch_name;

                return [
                    'type' => $type,
                    'quantity' => (int) $row->quantity,
                    'price' => null,
                    'branch_name' => $branchName,
                    'reason' => $reason,
                    'notes' => $row->notes,
                    'created_at' => $row->created_at,
                ];
            });

        // Merge and sort all movements by created_at DESC, then take latest 200
        $history = $stockIns
            ->merge($sales)
            ->merge($transfers)
            ->sortByDesc('created_at')
            ->values()
            ->take(200)
            ->all();

        return response()->json([
            'history' => $history,
        ]);
    }

    private function resolveBranchId(Request $request): int
    {
        $branchId = $request->integer('branch_id');
        if ($branchId) {
            return $branchId;
        }

        $userBranchId = (int) (optional($request->user())->branch_id ?? 0);
        if ($userBranchId > 0) {
            return $userBranchId;
        }

        return (int) (DB::table('branches')->orderBy('id')->value('id') ?? 1);
    }

    private function baseStockQuery(?int $branchId = null)
    {
        // Aggregate stock_ins per product and branch. If a specific branch is passed, scope to that branch only.
        $stockAgg = DB::table('stock_ins')
            ->when($branchId !== null, function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->groupBy('product_id', 'branch_id')
            ->selectRaw('
                product_id,
                branch_id,
                COALESCE(SUM(quantity - sold), 0) as current_stock,
                MAX(created_at) as last_stock_update,
                AVG(NULLIF(price, 0)) as unit_price
            ');

        // Only include products that have stock_ins rows (even if current_stock is zero)
        return DB::table('products')
            ->joinSub($stockAgg, 'stock', function ($join) {
                $join->on('products.id', '=', 'stock.product_id');
            })
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('branches', 'branches.id', '=', 'stock.branch_id')
            ->where('products.status', 'active')
            ->select([
                'products.id',
                'products.product_name',
                'products.barcode',
                'products.model_number',
                'products.category_id',
                'products.min_stock_level',
                'products.low_stock_threshold',
                'products.max_stock_level',
                DB::raw('COALESCE(stock.current_stock, 0) as current_stock'),
                DB::raw('COALESCE(stock.unit_price, 0) as unit_price'),
                DB::raw('stock.last_stock_update as last_stock_update'),
                DB::raw('COALESCE(brands.brand_name, "N/A") as brand_name'),
                DB::raw('COALESCE(categories.category_name, "N/A") as category_name'),
                DB::raw('COALESCE(branches.branch_name, "N/A") as branch_name'),
                DB::raw('stock.branch_id as branch_id'),
            ]);
    }

    private function applyStockLevelFilters($query, array $stockLevels): void
    {
        $stock = 'COALESCE(stock.current_stock, 0)';
        $min = '(CASE WHEN COALESCE(products.min_stock_level, 0) <= 0 THEN 10 ELSE products.min_stock_level END)';
        $max = '(CASE WHEN COALESCE(products.max_stock_level, 0) <= 0 THEN 100 ELSE products.max_stock_level END)';
        $low = '(CASE WHEN COALESCE(products.low_stock_threshold, 0) <= 0 THEN '.$min.' ELSE GREATEST('.$min.', products.low_stock_threshold) END)';

        $query->where(function ($q) use ($stockLevels, $stock, $min, $low, $max) {
            foreach ($stockLevels as $level) {
                switch ($level) {
                    case 'out_of_stock':
                        $q->orWhereRaw("{$stock} <= 0");
                        break;
                    case 'critical_stock':
                        $q->orWhereRaw("{$stock} > 0 AND {$stock} <= {$min}");
                        break;
                    case 'low_stock':
                        $q->orWhereRaw("{$stock} > {$min} AND {$stock} <= {$low}");
                        break;
                    case 'in_stock':
                        $q->orWhereRaw("{$stock} > {$low} AND {$stock} <= {$max}");
                        break;
                    case 'overstock':
                        $q->orWhereRaw("{$stock} > {$max}");
                        break;
                }
            }
        });
    }

    private function applyDateRangeFilter($query, string $dateRange): void
    {
        $dateRange = Str::lower($dateRange);

        switch ($dateRange) {
            case 'today':
                $query->whereDate('products.created_at', today());
                break;
            case 'week':
                $query->whereBetween('products.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('products.created_at', now()->month)->whereYear('products.created_at', now()->year);
                break;
            case 'quarter':
                $query->whereBetween('products.created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                break;
            case 'year':
                $query->whereYear('products.created_at', now()->year);
                break;
        }
    }

    private function applyMovementFilter($query, string $movement): void
    {
        $movement = Str::lower($movement);

        switch ($movement) {
            case 'recently_restocked':
                $query->whereDate('stock.last_stock_update', '>=', now()->subDays(7));
                break;
            case 'no_movement':
                $query->where(function ($q) {
                    $q->whereNull('stock.last_stock_update')
                        ->orWhereDate('stock.last_stock_update', '<=', now()->subDays(30));
                });
                break;
        }
    }

    private function applySorting($query, string $sortBy, string $sortDirection): void
    {
        $sortBy = Str::lower($sortBy);
        $sortDirection = $sortDirection === 'desc' ? 'desc' : 'asc';

        $stock = 'COALESCE(stock.current_stock, 0)';
        $min = '(CASE WHEN COALESCE(products.min_stock_level, 0) <= 0 THEN 10 ELSE products.min_stock_level END)';
        $max = '(CASE WHEN COALESCE(products.max_stock_level, 0) <= 0 THEN 100 ELSE products.max_stock_level END)';
        $low = '(CASE WHEN COALESCE(products.low_stock_threshold, 0) <= 0 THEN '.$min.' ELSE GREATEST('.$min.', products.low_stock_threshold) END)';

        if ($sortBy === 'stock_level') {
            $query->orderByRaw("
                CASE
                    WHEN {$stock} <= 0 THEN 1
                    WHEN {$stock} <= {$min} THEN 2
                    WHEN {$stock} <= {$low} THEN 3
                    WHEN {$stock} <= {$max} THEN 4
                    ELSE 5
                END {$sortDirection}
            ");

            return;
        }

        if (in_array($sortBy, ['product_name', 'unit_price', 'last_updated', 'current_stock'], true)) {
            match ($sortBy) {
                'product_name' => $query->orderBy('products.product_name', $sortDirection),
                'unit_price' => $query->orderBy('unit_price', $sortDirection),
                'last_updated' => $query->orderBy('last_stock_update', $sortDirection),
                'current_stock' => $query->orderBy('current_stock', $sortDirection),
            };

            return;
        }

        $query->orderBy('products.product_name', 'asc');
    }

    private function calculateStockStatistics(?int $branchId = null): array
    {
        $query = $this->baseStockQuery($branchId);

        $stats = DB::query()
            ->fromSub($query, 't')
            ->selectRaw('
                COUNT(*) as total_products,
                SUM(CASE WHEN current_stock <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE
                    WHEN current_stock > 0 AND current_stock <= (CASE WHEN COALESCE(min_stock_level, 0) <= 0 THEN 10 ELSE min_stock_level END) THEN 1
                    ELSE 0
                END) as critical_stock,
                SUM(CASE
                    WHEN current_stock > (CASE WHEN COALESCE(min_stock_level, 0) <= 0 THEN 10 ELSE min_stock_level END)
                     AND current_stock <= (CASE
                        WHEN COALESCE(low_stock_threshold, 0) <= 0 THEN (CASE WHEN COALESCE(min_stock_level, 0) <= 0 THEN 10 ELSE min_stock_level END)
                        ELSE GREATEST((CASE WHEN COALESCE(min_stock_level, 0) <= 0 THEN 10 ELSE min_stock_level END), low_stock_threshold)
                     END) THEN 1
                    ELSE 0
                END) as low_stock,
                SUM(CASE
                    WHEN current_stock > (CASE
                        WHEN COALESCE(low_stock_threshold, 0) <= 0 THEN (CASE WHEN COALESCE(min_stock_level, 0) <= 0 THEN 10 ELSE min_stock_level END)
                        ELSE GREATEST((CASE WHEN COALESCE(min_stock_level, 0) <= 0 THEN 10 ELSE min_stock_level END), low_stock_threshold)
                     END)
                     AND current_stock <= (CASE WHEN COALESCE(max_stock_level, 0) <= 0 THEN 100 ELSE max_stock_level END) THEN 1
                    ELSE 0
                END) as in_stock,
                SUM(CASE WHEN current_stock > (CASE WHEN COALESCE(max_stock_level, 0) <= 0 THEN 100 ELSE max_stock_level END) THEN 1 ELSE 0 END) as overstock
            ')
            ->first();

        return [
            'total_products' => (int) ($stats->total_products ?? 0),
            'out_of_stock' => (int) ($stats->out_of_stock ?? 0),
            'critical_stock' => (int) ($stats->critical_stock ?? 0),
            'low_stock' => (int) ($stats->low_stock ?? 0),
            'in_stock' => (int) ($stats->in_stock ?? 0),
            'overstock' => (int) ($stats->overstock ?? 0),
        ];
    }
}
