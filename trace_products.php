<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "=== PRODUCTS RESTORED FROM AUTO-VOIDED CREDIT SALES ===\n\n";

// Get all stock_ins with price = 0 (indicating auto-voided sales)
$restoredItems = DB::table('stock_ins')
    ->where('price', 0)
    ->orderBy('created_at', 'desc')
    ->get();

echo "Total restored items: " . $restoredItems->count() . "\n\n";

foreach ($restoredItems as $item) {
    // Get product name
    $product = DB::table('products')->find($item->product_id);
    $productName = $product ? $product->product_name : 'Unknown Product';
    
    // Get branch name
    $branch = DB::table('branches')->find($item->branch_id);
    $branchName = $branch ? $branch->branch_name : 'Unknown Branch';
    
    echo "📦 Product: {$productName}\n";
    echo "   Quantity: +{$item->quantity} units\n";
    echo "   Branch: {$branchName}\n";
    echo "   Restored at: {$item->created_at}\n";
    echo "   Stock ID: {$item->id}\n";
    echo "----------------------------------------\n";
}

// Show summary
echo "\n=== SUMMARY BY PRODUCT ===\n";
$summary = DB::table('stock_ins')
    ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('COUNT(*) as restoration_count'))
    ->where('price', 0)
    ->groupBy('product_id')
    ->orderBy('total_quantity', 'desc')
    ->get();

foreach ($summary as $item) {
    $product = DB::table('products')->find($item->product_id);
    $productName = $product ? $product->product_name : 'Unknown Product';
    
    echo "{$productName}: +{$item->total_quantity} units (restored {$item->restoration_count} times)\n";
}
