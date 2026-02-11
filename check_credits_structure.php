<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Credit;

echo "Checking credits table structure and data...\n";

try {
    // Check table structure
    $columns = DB::select("DESCRIBE credits");
    echo "Credits Table Columns:\n";
    foreach ($columns as $column) {
        echo "  - {$column->Field} ({$column->Type})\n";
    }
    
    echo "\nChecking credit types and sources...\n";
    
    // Check current credit data
    $credits = DB::table('credits')
        ->select('id', 'credit_type', 'sale_id', 'notes', 'created_at')
        ->limit(5)
        ->get();
    
    foreach ($credits as $credit) {
        echo "Credit ID: {$credit->id}\n";
        echo "  - Type: {$credit->credit_type}\n";
        echo "  - Sale ID: " . ($credit->sale_id ?? 'None') . "\n";
        echo "  - Notes: " . ($credit->notes ?? 'None') . "\n";
        echo "  - Created: {$credit->created_at}\n";
        echo "  ---\n";
    }
    
    echo "\nCredit Type Distribution:\n";
    $typeStats = DB::table('credits')
        ->select('credit_type', DB::raw('COUNT(*) as count'))
        ->groupBy('credit_type')
        ->get();
    
    foreach ($typeStats as $stat) {
        echo "  - {$stat->credit_type}: {$stat->count} credits\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
