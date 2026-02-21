<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cashier\SalesController;

/*
|--------------------------------------------------------------------------
| Test Multi-Branch Sales
|--------------------------------------------------------------------------
|
| This file contains test routes to verify multi-branch sales functionality
|
*/

// Test route to simulate multi-branch sale creation
Route::get('/test-multibranch-sale', function() {
    // This is just a test route to demonstrate the data structure needed
    $testData = [
        'payment_method' => 'cash',
        'customer_name' => 'Test Customer',
        'customer_phone' => '123-456-7890',
        'items' => [
            [
                'product_id' => 1, // Product from Branch 1
                'quantity' => 2,
                'unit_price' => 100.00,
                'unit_type_id' => 1,
                'branch_id' => 1 // Specify branch for each item
            ],
            [
                'product_id' => 2, // Product from Branch 2  
                'quantity' => 1,
                'unit_price' => 150.00,
                'unit_type_id' => 1,
                'branch_id' => 2 // Different branch
            ],
            [
                'product_id' => 3, // Another product from Branch 1
                'quantity' => 3,
                'unit_price' => 50.00,
                'unit_type_id' => 1,
                'branch_id' => 1 // Same as first item
            ]
        ]
    ];
    
    return response()->json([
        'message' => 'Test data structure for multi-branch sale',
        'data' => $testData,
        'explanation' => 'This will create 2 sales records: one for branch 1 (items 1 & 3) and one for branch 2 (item 2), grouped by receipt_group_id'
    ]);
});

// Test route to check database structure
Route::get('/test-sales-structure', function() {
    $sales = \App\Models\Sale::with(['items.product', 'branch'])
        ->whereNotNull('receipt_group_id')
        ->limit(5)
        ->get();
    
    return response()->json([
        'multi_branch_sales' => $sales,
        'total_sales' => \App\Models\Sale::count(),
        'multi_branch_count' => \App\Models\Sale::whereNotNull('receipt_group_id')->count()
    ]);
});
