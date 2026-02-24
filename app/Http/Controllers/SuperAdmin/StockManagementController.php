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
        $branchId = $this->resolveBranchId($request);

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
        $branchId = $this->resolveBranchId($request);

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

        $movements = DB::table('stock_ins')
            ->where('product_id', (int) $id)
            ->where('branch_id', $branchId)
            ->orderByDesc('created_at')
            ->limit(200)
            ->get()
            ->flatMap(function ($row) {
                $items = [];

                if ((int) $row->quantity > 0) {
                    $items[] = [
                        'type' => 'in',
                        'quantity' => (int) $row->quantity,
                        'reference' => $row->reference_number ?? 'N/A',
                        'created_at' => $row->created_at,
                    ];
                }

                if ((int) $row->sold > 0) {
                    $items[] = [
                        'type' => 'out',
                        'quantity' => (int) $row->sold,
                        'reference' => 'Sale',
                        'created_at' => $row->created_at,
                    ];
                }

                return $items;
            })
            ->values();

        return response()->json($movements);
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

    private function baseStockQuery(int $branchId)
    {
        $stockAgg = DB::table('stock_ins')
            ->where('branch_id', $branchId)
            ->groupBy('product_id')
            ->selectRaw('product_id, COALESCE(SUM(quantity - sold), 0) as current_stock, MAX(created_at) as last_stock_update, AVG(NULLIF(price, 0)) as unit_price');

        return DB::table('products')
            ->leftJoinSub($stockAgg, 'stock', function ($join) {
                $join->on('products.id', '=', 'stock.product_id');
            })
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('branches', 'branches.id', '=', DB::raw((int) $branchId))
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
                DB::raw((int) $branchId.' as branch_id'),
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

    private function calculateStockStatistics(int $branchId): array
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
