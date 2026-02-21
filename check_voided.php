<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

echo "Checking voided sales directly:\n";

// Check if voided column exists
$sales = DB::table('sales')->where('voided', true)->get(['id', 'voided', 'created_at', 'total_amount']);

echo "Found " . $sales->count() . " voided sales:\n";

foreach ($sales as $sale) {
    echo "ID: {$sale->id}, voided: {$sale->voided}, date: {$sale->created_at}, amount: {$sale->total_amount}\n";
}

// Check all sales
$allSales = DB::table('sales')->count();
echo "\nTotal sales in database: {$allSales}\n";

// Check distinct voided values
$voidedValues = DB::table('sales')->select('voided', DB::raw('COUNT(*) as count'))->groupBy('voided')->get();
echo "\nVoided column values:\n";
foreach ($voidedValues as $value) {
    echo "voided = {$value->voided}: {$value->count} records\n";
}
