<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter', 'all');
        
        if (!in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
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
            [$sortBy, $sortDirection]
        ]);
        
        // Apply out-of-stock filter if needed
        if ($filter === 'out-of-stock') {
            $products = $products->filter(function($product) {
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
            'filter' => $filter
        ])->with('branchesJson', $branches->toJson());
    }

    public function stockIn(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $product->stockIns()->create([
            'quantity' => $request->quantity,
            'branch_id' => $request->branch_id,
            'reason' => 'Stock In'
        ]);

        return back()->with('success', 'Stock added successfully.');
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $currentStock = $product->current_stock;
        $newStock = $request->quantity;
        $quantityAdded = max(0, $newStock - $currentStock);
        $quantityRemoved = max(0, $currentStock - $newStock);

        // Only process if stock actually changes
        if ($newStock != $currentStock) {
            if ($newStock > $currentStock) {
                // Create new stock_in entry for additional stock
                $product->stockIns()->create([
                    'quantity' => $quantityAdded,
                    'sold' => 0, // Initialize sold to 0 for new stock entry
                    'branch_id' => $request->branch_id,
                    'price' => 0, // Default price for stock adjustments
                ]);
                
                Log::info("Stock added for product {$product->product_name}: {$quantityAdded} units to branch {$request->branch_id}");
                
            } elseif ($newStock < $currentStock) {
                // Update sold column in most recent stock_in for this branch
                $stockIn = $product->stockIns()
                    ->where('branch_id', $request->branch_id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($stockIn) {
                    $stockIn->increment('sold', $quantityRemoved);
                    Log::info("Stock removed for product {$product->product_name}: {$quantityRemoved} units from branch {$request->branch_id}");
                } else {
                    // If no stock_in exists, create one with sold quantity
                    $product->stockIns()->create([
                        'quantity' => 0,
                        'sold' => $quantityRemoved,
                        'branch_id' => $request->branch_id,
                        'price' => 0,
                    ]);
                    Log::warning("Created stock_in record for product {$product->product_name} with only sold quantity: {$quantityRemoved}");
                }
            }
        }

        // Simple out-of-stock count calculation
        $outOfStockCount = \App\Models\Product::leftJoin('stock_ins', 'products.id', '=', 'stock_ins.product_id')
            ->selectRaw('COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0) as current_stock')
            ->groupBy('products.id')
            ->havingRaw('current_stock <= 15')
            ->count();

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $newStock > $currentStock ? 
                    "Stock added successfully. {$quantityAdded} units added to {$product->product_name}." : 
                    "Stock adjusted successfully. {$quantityRemoved} units removed from {$product->product_name}.",
                'outOfStockCount' => $outOfStockCount,
                'newStock' => $newStock,
                'previousStock' => $currentStock
            ]);
        }

        return back()->with('success', 'Stock adjusted successfully.');
    }

    public function outOfStock(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $branchId = $request->query('branch_id');
        
        if (!in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
            $sortBy = 'product_name';
        }
        
        // Build base query with stock calculations
        $productsQuery = DB::table('products')
            ->leftJoin('stock_ins', function($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->leftJoin('branches', function($join) {
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
        
        $totalOutOfStock = $productsQuery->count();
        
        // Store the count and total in session for sidebar display
        session([
            'out_of_stock_count' => $totalOutOfStock,
            'out_of_stock_total' => $totalOutOfStock
        ]);
        
        return view('SuperAdmin.inventory.out-of-stock', [
            'products' => $products->appends($request->query()),
            'branches' => $branches,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'selectedBranchId' => $branchId
        ])->with('branchesJson', $branches->toJson());
    }

    public function exportOutOfStockPDF(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $branchId = $request->query('branch_id');
        
        if (!in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
            $sortBy = 'product_name';
        }
        
        // Build base query with stock calculations
        $productsQuery = DB::table('products')
            ->leftJoin('stock_ins', function($join) {
                $join->on('products.id', '=', 'stock_ins.product_id');
            })
            ->leftJoin('sale_items', function($join) {
                $join->on('products.id', '=', 'sale_items.product_id');
            })
            ->leftJoin('branches', function($join) {
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
            'bindings' => $productsQuery->getBindings()
        ]);
        
        // Generate PDF with simplified approach
        try {
            // Configure DomPDF settings
            $options = new \Dompdf\Options();
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
    <p>Generated: ' . now()->format('F j, Y h:i A') . '</p>
    <p>Total products needing purchase: ' . $products->count() . '</p>
    
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
                    <td>' . htmlspecialchars($product->product_name) . '</td>
                    <td>' . $product->current_stock . '</td>
                    <td>' . htmlspecialchars($product->branch_name ?? 'All Branches') . '</td>
                    <td>' . ($product->current_stock <= 5 ? 'Critical' : 'Low Stock') . '</td>
                </tr>';
            }
            
            $html .= '
        </tbody>
    </table>
</body>
</html>';
            
            Log::info('Simple HTML generated', [
                'html_length' => strlen($html),
                'products_count' => $products->count()
            ]);
            
            $pdf->loadHtml($html);
            $pdfOutput = $pdf->output();
            
            Log::info('PDF generated successfully', [
                'output_length' => strlen($pdfOutput)
            ]);
            
            // Download PDF
            return response($pdfOutput)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="out-of-stock-' . date('Y-m-d') . '.pdf"')
                ->header('Content-Length', strlen($pdfOutput));
                
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF generation failed: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'PDF generation failed: ' . $e->getMessage());
        }
    }
}
