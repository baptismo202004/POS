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
            'initial_quantity' => $request->quantity, // Save original quantity
            'branch_id' => $request->branch_id,
            'reason' => 'Stock In'
        ]);

        return back()->with('success', 'Stock added successfully.');
    }

    public function adjust(Request $request, Product $product)
    {
        $adjustmentType = $request->input('adjustment_type');
        
        try {
            if ($adjustmentType === 'purchase') {
                return $this->adjustFromPurchase($request, $product);
            } elseif ($adjustmentType === 'transfer') {
                return $this->adjustFromTransfer($request, $product);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid adjustment type'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error adjusting stock: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to adjust stock'], 500);
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
            Log::error('Error fetching product stock: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch stock data'], 500);
        }
    }

    public function getProductPurchases($productId)
    {
        try {
            // Get purchases that contain this product through the relationship
            $purchases = \App\Models\Purchase::with('items')
                ->whereHas('items', function($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->get();

            // Format the response
            $purchaseData = $purchases->map(function($purchase) {
                return [
                    'id' => $purchase->id,
                    'purchase_date' => $purchase->purchase_date,
                    'quantity' => $purchase->items->sum('quantity')
                ];
            });

            return response()->json($purchaseData);
        } catch (\Exception $e) {
            Log::error('Error fetching product purchases: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch purchase data'], 500);
        }
    }

    public function getBranches()
    {
        try {
            $branches = \App\Models\Branch::all();
            return response()->json($branches);
        } catch (\Exception $e) {
            Log::error('Error fetching branches: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch branches'], 500);
        }
    }

    public function getProductSales($productId)
    {
        try {
            // Get sales data for this product over the last 30 days, grouped by branch
            $salesData = DB::table('sales')
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->join('branches', 'sales.branch_id', '=', 'branches.id')
                ->where('sale_items.product_id', $productId)
                ->where('sales.created_at', '>=', now()->subDays(30))
                ->select(
                    'branches.branch_name',
                    DB::raw('DATE(sales.created_at) as date'),
                    DB::raw('SUM(sale_items.quantity) as quantity')
                )
                ->groupBy('branches.branch_name', DB::raw('DATE(sales.created_at)'))
                ->orderBy('date', 'asc')
                ->get();

            // Group data by branch for easier processing in JavaScript
            $groupedData = [];
            foreach ($salesData as $sale) {
                $branchName = $sale->branch_name;
                if (!isset($groupedData[$branchName])) {
                    $groupedData[$branchName] = [];
                }
                $groupedData[$branchName][] = [
                    'date' => $sale->date,
                    'quantity' => $sale->quantity
                ];
            }

            return response()->json($groupedData);
        } catch (\Exception $e) {
            Log::error('Error fetching product sales: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch sales data'], 500);
        }
    }

    private function adjustFromPurchase(Request $request, Product $product)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'purchase_quantity' => 'required|integer|min:1'
        ]);

        $purchaseId = $request->input('purchase_id');
        $quantity = $request->input('purchase_quantity');

        // Validation: Check remaining quantity from purchase
        $purchasedQty = \App\Models\PurchaseItem::where('purchase_id', $purchaseId)
            ->where('product_id', $product->id)
            ->value('quantity');

        $alreadyStocked = \App\Models\StockIn::where('purchase_id', $purchaseId)
            ->where('product_id', $product->id)
            ->sum('initial_quantity');

        $remaining = $purchasedQty - $alreadyStocked;

        if ($quantity > $remaining) {
            return response()->json([
                'success' => false, 
                'message' => "Cannot stock more than purchased quantity. Remaining: {$remaining} units from Purchase #{$purchaseId}"
            ], 400);
        }

        // Create new stock_in record
        $product->stockIns()->create([
            'quantity' => $quantity,
            'initial_quantity' => $quantity, // Save original quantity
            'sold' => 0,
            'branch_id' => $product->branch_id ?? 1, // Use product's branch or default
            'price' => 0,
            'purchase_id' => $purchaseId
        ]);

        // Calculate new stock
        $currentStock = $product->current_stock;
        $newStock = $currentStock + $quantity;

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
            'message' => "Stock adjusted successfully. Added {$quantity} units from purchase #{$purchaseId}. Remaining: {$remaining} units.",
            'outOfStockCount' => $outOfStockCount
        ]);
    }

    private function adjustFromTransfer(Request $request, Product $product)
    {
        $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'transfer_quantity' => 'required|integer|min:1'
        ]);

        $fromBranchId = $request->input('from_branch');
        $quantity = $request->input('transfer_quantity');

        // Validate transfer amount
        $currentStock = $product->current_stock;
        if ($quantity > $currentStock) {
            return response()->json(['success' => false, 'message' => 'Cannot transfer more units than currently available'], 400);
        }

        // Find stock_in record from source branch
        $sourceStock = $product->stockIns()
            ->where('branch_id', $fromBranchId)
            ->where('quantity', '>', 'sold') // Has available stock
            ->first();

        if (!$sourceStock) {
            return response()->json(['success' => false, 'message' => 'Source branch has insufficient stock for transfer'], 400);
        }

        $availableStock = $sourceStock->quantity - $sourceStock->sold;
        $transferAmount = min($quantity, $availableStock);

        // Update source branch (reduce stock)
        $sourceStock->increment('sold', $transferAmount);

        // Create new stock_in record for destination branch (current branch)
        $product->stockIns()->create([
            'quantity' => $transferAmount,
            'initial_quantity' => $transferAmount, // Save original quantity
            'sold' => 0,
            'branch_id' => $product->current_branch_id ?? 1,
            'price' => 0,
            'reason' => 'Stock Transfer'
        ]);

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
            'message' => "Successfully transferred {$transferAmount} units from branch to current branch.",
            'outOfStockCount' => $outOfStockCount
        ]);
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
        
        // Set current_branch_id for each product
        $productIds = $products->getCollection()->pluck('id');
        \App\Models\Product::whereIn('id', $productIds)->get()->each(function($productModel) use ($products) {
            $collectionProduct = $products->firstWhere('id', $productModel->id);
            if ($collectionProduct) {
                $productModel->setAttribute('current_branch_id', $collectionProduct->branch_id);
            }
        });
        
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
