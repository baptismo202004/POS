<?php
// Simple test to check if PDF generation works
require_once 'vendor/autoload.php';

use Illuminate\Http\Request;

// Mock request
$request = new Request();

// Test the same query as in controller
$branches = \App\Models\Branch::all();
$sortBy = 'product_name';
$sortDirection = 'asc';
$search = null;
$branchId = null;

// Build base query with stock calculations
$productsQuery = \Illuminate\Support\Facades\DB::table('products')
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
        \Illuminate\Support\Facades\DB::raw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) as current_stock'),
        \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold'),
        \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(sale_items.subtotal), 0) as total_revenue')
    )
    ->groupBy('products.id', 'products.product_name', 'brands.brand_name', 'categories.category_name', 'branches.id', 'branches.branch_name');

// Filter for out-of-stock items (≤ 15 units)
$productsQuery->havingRaw('(COALESCE(SUM(stock_ins.quantity), 0) - COALESCE(SUM(stock_ins.sold), 0)) <= 15');

$products = $productsQuery->get();

echo "Found " . $products->count() . " products that need purchasing:\n";
foreach($products as $product) {
    echo "- " . $product->product_name . " (Stock: " . $product->current_stock . ", Branch: " . ($product->branch_name ?? 'N/A') . ")\n";
}
?>
