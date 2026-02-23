<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockManagementController extends Controller
{
    /**
     * Display stock management page with filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);
        
        // Apply stock level filters
        if ($request->has('stock_levels') && is_array($request->stock_levels)) {
            $query = $this->applyStockLevelFilters($query, $request->stock_levels);
        }
        
        // Apply category filter
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('category_name', $request->category);
            });
        }
        
        // Apply supplier filter
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('barcode', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('model_number', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        // Apply date range filter
        if ($request->filled('date_range')) {
            $query = $this->applyDateRangeFilter($query, $request->date_range);
        }
        
        // Apply movement filter
        if ($request->filled('movement')) {
            $query = $this->applyMovementFilter($query, $request->movement);
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'name_asc');
        $query = $this->applySorting($query, $sortBy);
        
        // Get results with pagination
        $products = $query->with(['category', 'supplier', 'brand'])
                          ->paginate(50);
        
        // Add calculated attributes to paginated results
        $products->getCollection()->transform(function($product) {
            // Calculate current stock
            $currentStock = $product->stockIns()->sum('quantity') - $product->stockIns()->sum('sold');
            
            // Add calculated attributes
            $product->current_stock = $currentStock;
            $product->brand_name = $product->brand ? $product->brand->brand_name : 'N/A';
            $product->category_name = $product->category ? $product->category->category_name : 'N/A';
            $product->branch_name = $product->branch ? $product->branch->branch_name : 'Main Branch';
            $product->unit_price = 0; // Default value - you may need to adjust this based on your pricing logic
            $product->last_stock_update = $product->updated_at;
            
            return $product;
        });
        
        // Get filter options
        $categories = Category::orderBy('category_name')->pluck('category_name', 'category_name');
        $suppliers = Supplier::orderBy('supplier_name')->pluck('supplier_name', 'id');
        $branches = \App\Models\Branch::orderBy('branch_name')->get();
        
        // Calculate stock statistics
        $stockStats = $this->calculateStockStatistics();
        
        return view('superadmin.inventory.stock-management', compact(
            'products', 
            'categories', 
            'suppliers', 
            'branches',
            'stockStats'
        ));
    }
    
    /**
     * Apply stock level filters to query
     */
    private function applyStockLevelFilters($query, $stockLevels)
    {
        return $query->where(function($q) use ($stockLevels) {
            foreach ($stockLevels as $level) {
                switch ($level) {
                    case 'out_of_stock':
                        $q->orWhereHas('stockIns', function($stockQuery) {
                            $stockQuery->selectRaw('SUM(quantity) - SUM(sold) as current_stock')
                                     ->havingRaw('SUM(quantity) - SUM(sold) <= 0');
                        });
                        break;
                    case 'low_stock':
                        $q->orWhereHas('stockIns', function($stockQuery) {
                            $stockQuery->selectRaw('SUM(quantity) - SUM(sold) as current_stock')
                                     ->havingRaw('SUM(quantity) - SUM(sold) > 0')
                                     ->havingRaw('SUM(quantity) - SUM(sold) <= COALESCE(min_stock_level, 5)');
                        });
                        break;
                    case 'critical_stock':
                        $q->orWhereHas('stockIns', function($stockQuery) {
                            $stockQuery->selectRaw('SUM(quantity) - SUM(sold) as current_stock')
                                     ->havingRaw('SUM(quantity) - SUM(sold) > 0')
                                     ->havingRaw('SUM(quantity) - SUM(sold) <= 3');
                        });
                        break;
                    case 'in_stock':
                        $q->orWhereHas('stockIns', function($stockQuery) {
                            $stockQuery->selectRaw('SUM(quantity) - SUM(sold) as current_stock')
                                     ->havingRaw('SUM(quantity) - SUM(sold) > 0');
                        });
                        break;
                    case 'overstock':
                        $q->orWhereHas('stockIns', function($stockQuery) {
                            $stockQuery->selectRaw('SUM(quantity) - SUM(sold) as current_stock')
                                     ->havingRaw('SUM(quantity) - SUM(sold) > COALESCE(max_stock_level, 100)');
                        });
                        break;
                }
            }
        });
    }
    
    /**
     * Apply date range filter
     */
    private function applyDateRangeFilter($query, $dateRange)
    {
        switch ($dateRange) {
            case 'today':
                return $query->whereDate('created_at', today());
            case 'week':
                return $query->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ]);
            case 'month':
                return $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
            case 'quarter':
                return $query->whereBetween('created_at', [
                    now()->startOfQuarter(),
                    now()->endOfQuarter()
                ]);
            case 'year':
                return $query->whereYear('created_at', now()->year);
            default:
                return $query;
        }
    }
    
    /**
     * Apply movement filter
     */
    private function applyMovementFilter($query, $movement)
    {
        switch ($movement) {
            case 'recently_restocked':
                return $query->whereDate('updated_at', '>=', now()->subDays(7));
            case 'no_movement':
                return $query->whereDate('updated_at', '<=', now()->subDays(30));
            case 'fast_moving':
                return $query->where('quantity', '>', 0)
                          ->where('created_at', '<=', now()->subDays(30));
            case 'slow_moving':
                return $query->where('quantity', '>', 0)
                          ->where('created_at', '>', now()->subDays(90));
            default:
                return $query;
        }
    }
    
    /**
     * Apply sorting to query
     */
    private function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'name_asc':
                return $query->orderBy('product_name', 'asc');
            case 'name_desc':
                return $query->orderBy('product_name', 'desc');
            case 'quantity_asc':
                return $query->selectRaw('products.*, (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) as current_stock')
                          ->orderBy('current_stock', 'asc');
            case 'quantity_desc':
                return $query->selectRaw('products.*, (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) as current_stock')
                          ->orderBy('current_stock', 'desc');
            case 'updated_desc':
                return $query->orderBy('updated_at', 'desc');
            case 'status_asc':
                return $query->selectRaw('products.*, (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) as current_stock')
                          ->orderByRaw('
                            CASE 
                                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) <= 0 THEN 1
                                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) <= 3 THEN 2
                                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) <= COALESCE(min_stock_level, 5) THEN 3
                                ELSE 4
                            END
                        ');
            default:
                return $query->orderBy('product_name', 'asc');
        }
    }
    
    /**
     * Calculate stock statistics
     */
    private function calculateStockStatistics()
    {
        $stats = Product::selectRaw('
            COUNT(*) as total_products,
            SUM(CASE 
                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) <= 0 THEN 1 
                ELSE 0 
            END) as out_of_stock_count,
            SUM(CASE 
                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) > 0 
                AND (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) <= COALESCE(products.min_stock_level, 5) THEN 1 
                ELSE 0 
            END) as low_stock_count,
            SUM(CASE 
                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) > 0 
                AND (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) <= 3 THEN 1 
                ELSE 0 
            END) as critical_stock_count,
            SUM(CASE 
                WHEN (SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id) > COALESCE(products.max_stock_level, 100) THEN 1 
                ELSE 0 
            END) as overstock_count,
            SUM((SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id)) as total_quantity,
            AVG((SELECT SUM(quantity) - SUM(sold) FROM stock_ins WHERE product_id = products.id)) as average_quantity
        ')->first();
        
        return [
            'total_products' => $stats->total_products ?? 0,
            'out_of_stock' => $stats->out_of_stock_count ?? 0,
            'low_stock' => $stats->low_stock_count ?? 0,
            'critical_stock' => $stats->critical_stock_count ?? 0,
            'overstock' => $stats->overstock_count ?? 0,
            'total_quantity' => $stats->total_quantity ?? 0,
            'average_quantity' => round($stats->average_quantity ?? 0, 2)
        ];
    }
    
    /**
     * API endpoint for suppliers
     */
    public function getSuppliers()
    {
        $suppliers = Supplier::select('id', 'supplier_name')
                           ->orderBy('supplier_name')
                           ->get();
        
        return response()->json($suppliers);
    }
    
    /**
     * API endpoint for filtered products
     */
    public function getFilteredProducts(Request $request)
    {
        $query = Product::with(['category', 'supplier']);
        
        // Apply all filters (same logic as index method)
        if ($request->has('stock_levels') && is_array($request->stock_levels)) {
            $query = $this->applyStockLevelFilters($query, $request->stock_levels);
        }
        
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('category_name', $request->category);
            });
        }
        
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('product_name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('barcode', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('model_number', 'LIKE', "%{$searchTerm}%");
            });
        }
        
        $sortBy = $request->get('sort_by', 'name_asc');
        $query = $this->applySorting($query, $sortBy);
        
        $products = $query->limit(100)->get();
        
        return response()->json([
            'products' => $products,
            'total' => $products->count()
        ]);
    }
    
    /**
     * Get product details for modal
     */
    public function getProductDetails($id)
    {
        $product = Product::with(['category', 'supplier', 'brand'])
                          ->findOrFail($id);
        
        // Calculate current stock
        $currentStock = $product->stockIns()->sum('quantity') - $product->stockIns()->sum('sold');
        
        return response()->json([
            'id' => $product->id,
            'product_name' => $product->product_name,
            'barcode' => $product->barcode,
            'model_number' => $product->model_number,
            'description' => $product->description ?? 'N/A',
            'category' => $product->category ? $product->category->category_name : 'N/A',
            'supplier' => $product->supplier ? $product->supplier->supplier_name : 'N/A',
            'brand' => $product->brand ? $product->brand->brand_name : 'N/A',
            'quantity' => $currentStock,
            'min_stock_level' => $product->min_stock_level ?? 5,
            'max_stock_level' => $product->max_stock_level ?? 100,
            'updated_at' => $product->updated_at
        ]);
    }
    
    /**
     * Get stock history for a product
     */
    public function getStockHistory($id)
    {
        $product = Product::findOrFail($id);
        
        // Get stock movements
        $stockIns = $product->stockIns()
                           ->with(['user'])
                           ->orderBy('created_at', 'desc')
                           ->get()
                           ->map(function($stockIn) {
                               return [
                                   'type' => 'in',
                                   'quantity' => $stockIn->quantity,
                                   'reference' => $stockIn->reference_number ?? 'N/A',
                                   'user' => $stockIn->user ? $stockIn->user->name : 'System',
                                   'created_at' => $stockIn->created_at
                               ];
                           });
        
        // Get stock outs (from sales)
        $stockOuts = $product->stockIns()
                            ->where('sold', '>', 0)
                            ->with(['user'])
                            ->orderBy('created_at', 'desc')
                            ->get()
                            ->map(function($stockOut) {
                                return [
                                    'type' => 'out',
                                    'quantity' => $stockOut->sold,
                                    'reference' => 'Sale',
                                    'user' => $stockOut->user ? $stockOut->user->name : 'System',
                                    'created_at' => $stockOut->created_at
                                ];
                            });
        
        // Combine and sort by date
        $allMovements = $stockIns->concat($stockOuts)
                                ->sortByDesc('created_at')
                                ->values();
        
        return response()->json($allMovements);
    }
}
