<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PosAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SuperAdmin\ProductController as SuperAdminProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\AccessPermissionController;
use App\Http\Controllers\SuperAdmin\PurchaseController;
use App\Http\Controllers\SuperAdmin\InventoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Cashier\CashierDashboardController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();
        
        // Get authenticated user
        $user = Auth::user();
        
        // Update last login timestamp
        $user->last_login_at = now();
        $user->save();
        
        // Check if user has a user type assigned
        if (!$user->userType) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withInput()->with('error', 'Your account is not properly configured. Please contact your administrator.');
        }
        
        // Check if cashier has branch assignment
        if ($user->userType->name === 'Cashier') {
            if (!$user->branch_id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withInput()->with('error', 'Your cashier account is not assigned to any branch. Please contact your administrator.');
            }
            
            // Check if branch exists and is active
            $branch = \App\Models\Branch::find($user->branch_id);
            if (!$branch) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withInput()->with('error', 'Your assigned branch is not found in the system. Please contact your administrator.');
            }
            
            if ($branch->status !== 'active') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withInput()->with('error', 'Your assigned branch is currently inactive. Please contact your administrator.');
            }
            
            return redirect()->route('cashier.dashboard');
        }
        
        // For non-cashier users, check if they have active status
        if (isset($user->status) && $user->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withInput()->with('error', 'Your account is currently inactive. Please contact your administrator.');
        }
        
        return redirect()->route('dashboard');
    }

    return back()->withInput()->with('error', 'Incorrect email or password');
})->name('login.post');

Route::get('/dashboard', function () {
    return view('SuperAdmin.dashboard');
})->middleware('auth')->name('dashboard');

// Cashier Dashboard
Route::get('/cashier/dashboard', [CashierDashboardController::class, 'index'])->middleware('auth')->name('cashier.dashboard');
Route::get('/cashier/dashboard/chart', [CashierDashboardController::class, 'chartData'])->middleware('auth')->name('cashier.dashboard.chart');
Route::get('/cashier/dashboard/low-stock', [CashierDashboardController::class, 'getLowStock'])->middleware('auth')->name('cashier.dashboard.low-stock');

// Cashier Products
Route::get('/cashier/products', [CashierDashboardController::class, 'products'])->middleware('auth')->name('cashier.products.index');
Route::get('/cashier/products/create', [CashierDashboardController::class, 'createProduct'])->middleware('auth')->name('cashier.products.create');
Route::post('/cashier/products', [CashierDashboardController::class, 'storeProduct'])->middleware('auth')->name('cashier.products.store');
Route::get('/cashier/products/{product}', [CashierDashboardController::class, 'showProduct'])->middleware('auth')->name('cashier.products.show');
Route::get('/cashier/products/{product}/edit', [CashierDashboardController::class, 'editProduct'])->middleware('auth')->name('cashier.products.edit');
Route::put('/cashier/products/{product}', [CashierDashboardController::class, 'updateProduct'])->middleware('auth')->name('cashier.products.update');
Route::delete('/cashier/products/{product}', [CashierDashboardController::class, 'destroyProduct'])->middleware('auth')->name('cashier.products.destroy');

// Cashier Product Categories
Route::get('/cashier/categories', [CashierDashboardController::class, 'categories'])->middleware('auth')->name('cashier.categories.index');
Route::get('/cashier/categories/create', [CashierDashboardController::class, 'createCategory'])->middleware('auth')->name('cashier.categories.create');
Route::post('/cashier/categories', [CashierDashboardController::class, 'storeCategory'])->middleware('auth')->name('cashier.categories.store');
Route::get('/cashier/categories/{category}/edit', [CashierDashboardController::class, 'editCategory'])->middleware('auth')->name('cashier.categories.edit');
Route::put('/cashier/categories/{category}', [CashierDashboardController::class, 'updateCategory'])->middleware('auth')->name('cashier.categories.update');
Route::delete('/cashier/categories/{category}', [CashierDashboardController::class, 'destroyCategory'])->middleware('auth')->name('cashier.categories.destroy');
Route::delete('/cashier/categories/bulk-delete', [CashierDashboardController::class, 'bulkDeleteCategories'])->middleware('auth')->name('cashier.categories.deleteMultiple');

// Cashier Purchases
Route::get('/cashier/purchases', [CashierDashboardController::class, 'purchasesIndex'])->middleware('auth')->name('cashier.purchases.index');
Route::get('/cashier/purchases/create', [CashierDashboardController::class, 'purchasesCreate'])->middleware('auth')->name('cashier.purchases.create');
Route::post('/cashier/purchases', [CashierDashboardController::class, 'purchasesStore'])->middleware('auth')->name('cashier.purchases.store');
Route::get('/cashier/purchases/{purchase}', [CashierDashboardController::class, 'purchasesShow'])->middleware('auth')->name('cashier.purchases.show');
Route::post('/cashier/purchases/ocr-product-match', [CashierDashboardController::class, 'purchasesMatchProduct'])->middleware('auth')->name('cashier.purchases.ocr-product-match');

// Cashier Inventory
Route::get('/cashier/inventory', [CashierDashboardController::class, 'inventoryIndex'])->middleware('auth')->name('cashier.inventory.index');

// Cashier Stock In (under Inventory)
Route::get('/cashier/stockin', [CashierDashboardController::class, 'stockInIndex'])->middleware('auth')->name('cashier.stockin.index');
Route::get('/cashier/stockin/create', [CashierDashboardController::class, 'stockInCreate'])->middleware('auth')->name('cashier.stockin.create');
Route::post('/cashier/stockin', [CashierDashboardController::class, 'stockInStore'])->middleware('auth')->name('cashier.stockin.store');
Route::get('/cashier/stockin/products-by-purchase/{purchase}', [CashierDashboardController::class, 'stockInProductsByPurchase'])->middleware('auth')->name('cashier.stockin.products-by-purchase');

// Dashboard chart data (JSON)
Route::get('/dashboard/chart', [DashboardController::class, 'chartData'])->middleware('auth')->name('dashboard.chart');
Route::get('/dashboard/monthly-sales', [DashboardController::class, 'monthlySales'])->middleware('auth')->name('dashboard.monthly-sales');
Route::get('/dashboard/monthly-sales-breakdown', [DashboardController::class, 'monthlySalesBreakdown'])->middleware('auth')->name('dashboard.monthly-sales-breakdown');
Route::get('/dashboard/monthly-expenses', [DashboardController::class, 'monthlyExpenses'])->middleware('auth')->name('dashboard.monthly-expenses');
Route::get('/dashboard/monthly-expenses-breakdown', [DashboardController::class, 'monthlyExpensesBreakdown'])->middleware('auth')->name('dashboard.monthly-expenses-breakdown');
Route::get('/dashboard/monthly-profit', [DashboardController::class, 'monthlyProfit'])->middleware('auth')->name('dashboard.monthly-profit');
Route::get('/dashboard/monthly-profit-breakdown', [DashboardController::class, 'monthlyProfitBreakdown'])->middleware('auth')->name('dashboard.monthly-profit-breakdown');
Route::get('/dashboard/monthly-returns', [DashboardController::class, 'monthlyReturns'])->middleware('auth')->name('dashboard.monthly-returns');
Route::get('/dashboard/monthly-returns-breakdown', [DashboardController::class, 'monthlyReturnsBreakdown'])->middleware('auth')->name('dashboard.monthly-returns-breakdown');
Route::get('/dashboard/branch-sales-today', [DashboardController::class, 'branchSalesToday'])->middleware('auth')->name('dashboard.branch-sales-today');
Route::get('/dashboard/expenses-today', [DashboardController::class, 'expensesToday'])->middleware('auth')->name('dashboard.expenses-today');
Route::get('/debug/expenses-table', function () {
    try {
        $columns = DB::select('DESCRIBE expenses');
        $sample = DB::table('expenses')->limit(3)->get();
        
        return response()->json([
            'columns' => $columns,
            'sample_data' => $sample,
            'table_exists' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'table_exists' => false
        ]);
    }
})->middleware('auth');
Route::get('/debug/expenses-simple', function () {
    try {
        $today = \Carbon\Carbon::today('Asia/Manila');
        
        // Simple test query
        $allExpenses = DB::table('expenses')
            ->whereDate('expense_date', $today)
            ->get();
            
        $total = $allExpenses->sum('amount');
        
        return response()->json([
            'date' => $today->format('Y-m-d'),
            'total_expenses' => $total,
            'count' => $allExpenses->count(),
            'sample_expenses' => $allExpenses->take(3),
            'success' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'success' => false
        ]);
    }
})->middleware('auth');
Route::get('/debug/view-logs', function () {
    try {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            $lines = array_slice(explode("\n", $logs), -50); // Last 50 lines
            return response()->json([
                'success' => true,
                'logs' => $lines,
                'file_path' => $logFile
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Log file not found',
                'file_path' => $logFile
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
})->middleware('auth');
Route::get('/dashboard/chart-data', function (Illuminate\Http\Request $request) {
    $end = $request->query('end') ? Carbon::parse($request->query('end')) : Carbon::today();
    $start = $request->query('start') ? Carbon::parse($request->query('start')) : $end->copy()->subDays(6);
    $days = $start->diffInDays($end) + 1;
    $prevEnd = $start->copy()->subDay();
    $prevStart = $prevEnd->copy()->subDays($days - 1);

    // Build label range
    $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
    $labels = [];
    $keys = [];
    foreach ($period as $d) {
        $labels[] = $d->format('D');
        $keys[] = $d->format('Y-m-d');
    }

    // Current period
    $salesRows = DB::table('sales')
        ->selectRaw('DATE(created_at) as d, SUM(total_amount) as s')
        ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
        ->groupBy('d')
        ->pluck('s', 'd');
    $expenseRows = DB::table('expenses')
        ->selectRaw('expense_date as d, SUM(amount) as s')
        ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
        ->groupBy('d')
        ->pluck('s', 'd');

    // Previous period
    $prevSalesRows = DB::table('sales')
        ->selectRaw('DATE(created_at) as d, SUM(total_amount) as s')
        ->whereBetween('created_at', [$prevStart->copy()->startOfDay(), $prevEnd->copy()->endOfDay()])
        ->groupBy('d')
        ->pluck('s', 'd');
    $prevExpenseRows = DB::table('expenses')
        ->selectRaw('expense_date as d, SUM(amount) as s')
        ->whereBetween('expense_date', [$prevStart->toDateString(), $prevEnd->toDateString()])
        ->groupBy('d')
        ->pluck('s', 'd');

    $sales = [];
    $expenses = [];
    foreach ($keys as $k) {
        $sales[] = (float) ($salesRows[$k] ?? 0);
        $expenses[] = (float) ($expenseRows[$k] ?? 0);
    }

    // Totals and comparison
    $salesTotal = (float) $salesRows->sum();
    $expensesTotal = (float) $expenseRows->sum();
    $prevSalesTotal = (float) $prevSalesRows->sum();
    $prevExpensesTotal = (float) $prevExpenseRows->sum();
    $profitTotal = $salesTotal - $expensesTotal;
    $prevProfitTotal = $prevSalesTotal - $prevExpensesTotal;

    $salesPct = $prevSalesTotal ? round((($salesTotal - $prevSalesTotal) / $prevSalesTotal) * 100, 1) : 0;
    $expensesPct = $prevExpensesTotal ? round((($expensesTotal - $prevExpensesTotal) / $prevExpensesTotal) * 100, 1) : 0;
    $profitPct = $prevProfitTotal ? round((($profitTotal - $prevProfitTotal) / (abs($prevProfitTotal) ?: 1)) * 100, 1) : 0;

    return response()->json([
        'labels' => $labels,
        'sales' => $sales,
        'expenses' => $expenses,
        'totals' => [
            'sales' => $salesTotal,
            'expenses' => $expensesTotal,
            'profit' => $profitTotal,
        ],
        'comparison' => [
            'sales' => $salesPct,
            'expenses' => $expensesPct,
            'profit' => $profitPct,
        ],
        'start' => $start->toDateString(),
        'end' => $end->toDateString(),
    ]);
})->middleware('auth')->name('dashboard.chart-data');

// Dashboard layout management routes
Route::get('/dashboard/layout', function (Request $request) {
    $user = $request->user();
    $layout = $user->dashboard_layout ?? null;
    
    return response()->json([
        'layout' => $layout ? json_decode($layout, true) : null
    ]);
})->middleware('auth')->name('dashboard.layout.get');

Route::post('/dashboard/layout', function (Request $request) {
    $user = $request->user();
    $layoutData = $request->json('layout');
    $reset = $request->json('reset', false);
    
    if ($reset) {
        // Reset to default (clear user layout)
        $user->dashboard_layout = null;
    } else {
        // Save user layout
        $user->dashboard_layout = json_encode($layoutData);
    }
    
    $user->save();
    
    return response()->json([
        'success' => true,
        'message' => $reset ? 'Layout reset to default' : 'Layout saved successfully'
    ]);
})->middleware('auth')->name('dashboard.layout.save');

// Debug route for refund testing
Route::get('/debug/refund-test', function (Request $request) {
    try {
        // Test basic database connections
        $tests = [];
        
        // Test 1: Check if tables exist
        $tests['tables_exist'] = [
            'sales' => DB::getSchemaBuilder()->hasTable('sales'),
            'sale_items' => DB::getSchemaBuilder()->hasTable('sale_items'),
            'refunds' => DB::getSchemaBuilder()->hasTable('refunds'),
            'products' => DB::getSchemaBuilder()->hasTable('products'),
            'stock_ins' => DB::getSchemaBuilder()->hasTable('stock_ins'),
            'stock_outs' => DB::getSchemaBuilder()->hasTable('stock_outs')
        ];
        
        // Test 2: Check sample data
        $tests['sample_data'] = [
            'sales_count' => DB::table('sales')->count(),
            'sale_items_count' => DB::table('sale_items')->count(),
            'products_count' => DB::table('products')->count(),
            'refunds_count' => DB::table('refunds')->count(),
            'stock_ins_count' => DB::table('stock_ins')->count(),
            'stock_outs_count' => DB::table('stock_outs')->count()
        ];
        
        // Test 3: Check recent sale item
        $recentSaleItem = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(
                'sale_items.id as sale_item_id',
                'sale_items.sale_id',
                'sale_items.product_id',
                'sale_items.quantity',
                'sale_items.unit_price',
                'products.product_name',
                'sales.total_amount'
            )
            ->first();
            
        
        $tests['recent_sale_item'] = $recentSaleItem;
        
        // Test 4: Check stock for this product
        if ($recentSaleItem) {
            $stockIn = DB::table('stock_ins')
                ->where('product_id', $recentSaleItem->product_id)
                ->first();
                
            $stockOut = DB::table('stock_outs')
                ->where('sale_id', $recentSaleItem->sale_id)
                ->where('product_id', $recentSaleItem->product_id)
                ->first();
            
            $tests['stock_info'] = [
                'stock_in' => $stockIn,
                'stock_out' => $stockOut
            ];
        }
        
        // Test 5: Check if refund can be created
        if ($recentSaleItem) {
            try {
                // Test refund data
                $refundData = [
                    'sale_id' => $recentSaleItem->sale_id,
                    'sale_item_id' => $recentSaleItem->sale_item_id,
                    'product_id' => $recentSaleItem->product_id,
                    'quantity_refunded' => 1,
                    'refund_amount' => $recentSaleItem->unit_price,
                    'reason' => 'Test refund',
                    'status' => 'approved'
                ];
                
                $tests['test_refund_data'] = $refundData;
                $tests['can_create_refund'] = true;
                
            } catch (\Exception $e) {
                $tests['refund_error'] = $e->getMessage();
                $tests['can_create_refund'] = false;
            }
        }
        
        return response()->json([
            'success' => true,
            'tests' => $tests,
            'timestamp' => now()->toDateTimeString()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->middleware('auth');

// Debug route to check alerts
Route::get('/debug/alerts', function (Request $request) {
    try {
        // First, let's see what's in stock_ins table
        $stockIns = DB::table('stock_ins')
            ->join('products', 'stock_ins.product_id', '=', 'products.id')
            ->select(
                'products.product_name',
                'stock_ins.quantity',
                'stock_ins.created_at'
            )
            ->orderBy('stock_ins.quantity', 'asc')
            ->get();
        
        // Check stock_outs
        $stockOuts = DB::table('stock_outs')
            ->join('products', 'stock_outs.product_id', '=', 'products.id')
            ->select(
                'products.product_name',
                'stock_outs.quantity',
                'stock_outs.created_at'
            )
            ->orderBy('stock_outs.quantity', 'asc')
            ->get();
        
        // Simple stock calculation per product
        $productStocks = [];
        
        // Get all products
        $products = DB::table('products')->get();
        
        foreach ($products as $product) {
            // Calculate stock the same way the refund system does
            $totalIn = DB::table('stock_ins')
                ->where('product_id', $product->id)
                ->sum('quantity') ?? 0;
            
            $totalSold = DB::table('stock_ins')
                ->where('product_id', $product->id)
                ->sum('sold') ?? 0;
            
            $currentStock = $totalIn - $totalSold;
            
            $productStocks[] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'stock_in' => $totalIn,
                'stock_sold' => $totalSold,
                'current_stock' => $currentStock
            ];
        }
        
        // Sort by current stock
        usort($productStocks, function($a, $b) {
            return $a['current_stock'] - $b['current_stock'];
        });
        
        // Filter for alerts
        $criticalItems = array_filter($productStocks, function($item) {
            return $item['current_stock'] <= 10 && $item['current_stock'] > 0;
        });
        
        $outOfStockItems = array_filter($productStocks, function($item) {
            return $item['current_stock'] == 0;
        });
        
        return response()->json([
            'critical_stock_count' => count($criticalItems),
            'out_of_stock_count' => count($outOfStockItems),
            'critical_items' => array_values($criticalItems),
            'out_of_stock_items' => array_values($outOfStockItems),
            'all_products_stock' => $productStocks,
            'raw_stock_ins' => $stockIns,
            'raw_stock_outs' => $stockOuts,
            'debug_info' => [
                'total_products' => count($products),
                'total_stock_ins_records' => DB::table('stock_ins')->count(),
                'total_stock_outs_records' => DB::table('stock_outs')->count(),
                'products_with_stock_ins' => DB::table('stock_ins')->distinct('product_id')->count(),
                'products_with_stock_outs' => DB::table('stock_outs')->distinct('product_id')->count()
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->middleware('auth');

// Dashboard widgets data (JSON) - Complete Dashboard Layout
Route::get('/dashboard/widgets', function (Request $request) {
    try {
        Log::info('Starting dashboard widgets data fetch');
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfMonth = Carbon::now()->startOfMonth();
        
        // ROW 1: TODAY'S KPIs
        
        // 1. Today's Sales
        try {
            $todaySales = DB::table('sales')
                ->whereDate('created_at', $today)
                ->sum('total_amount') ?? 0;
            
            $yesterdaySales = DB::table('sales')
                ->whereDate('created_at', $yesterday)
                ->sum('total_amount') ?? 0;
            
            $todayTransactions = DB::table('sales')
                ->whereDate('created_at', $today)
                ->count() ?? 0;
            
            $salesChange = $yesterdaySales > 0 ? round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1) : 0;
            
            Log::info('Today sales calculated', ['todaySales' => $todaySales, 'yesterdaySales' => $yesterdaySales]);
        } catch (\Exception $e) {
            Log::error('Error calculating today sales: ' . $e->getMessage());
            $todaySales = 0;
            $yesterdaySales = 0;
            $todayTransactions = 0;
            $salesChange = 0;
        }
        
        // 2. Today's Profit (Sales - Cost of Goods - Expenses)
        try {
            $todayCostOfGoods = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('stock_ins', function($join) {
                    $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                         ->on('sales.branch_id', '=', 'stock_ins.branch_id');
                })
                ->whereDate('sales.created_at', $today)
                ->selectRaw('SUM(sale_items.quantity * stock_ins.price) as cost')
                ->value('cost') ?? 0;
            
            $todayExpenses = DB::table('expenses')
                ->whereDate('expense_date', $today)
                ->sum('amount') ?? 0;
            
            $todayProfit = $todaySales - $todayCostOfGoods - $todayExpenses;
            
            Log::info('Today profit calculated', ['costOfGoods' => $todayCostOfGoods, 'expenses' => $todayExpenses, 'profit' => $todayProfit]);
        } catch (\Exception $e) {
            Log::error('Error calculating today profit: ' . $e->getMessage());
            $todayCostOfGoods = 0;
            $todayExpenses = 0;
            $todayProfit = 0;
        }
    
    // 3. Today's Expenses with biggest category
    try {
        $biggestExpenseCategory = DB::table('expenses')
            ->join('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->whereDate('expenses.expense_date', $today)
            ->select('expense_categories.name', DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('expense_categories.id', 'expense_categories.name')
            ->orderByDesc('total')
            ->first();
    } catch (\Exception $e) {
        Log::error('Error fetching biggest expense category: ' . $e->getMessage());
        $biggestExpenseCategory = (object) ['name' => 'N/A', 'total' => 0];
    }
    
    // 4. Low Stock / Critical Stock Count
    try {
        $subquery = DB::table('products')
            ->leftJoinSub(
                DB::table('stock_ins')
                    ->select('product_id', DB::raw('SUM(quantity) as total_in'))
                    ->groupBy('product_id'),
                'stock_in_totals',
                'products.id',
                '=',
                'stock_in_totals.product_id'
            )
            ->leftJoinSub(
                DB::table('stock_outs')
                    ->select('product_id', DB::raw('SUM(quantity) as total_out'))
                    ->groupBy('product_id'),
                'stock_out_totals',
                'products.id',
                '=',
                'stock_out_totals.product_id'
            )
            ->select(
                'products.id',
                'products.product_name',
                DB::raw('(COALESCE(stock_in_totals.total_in, 0) - COALESCE(stock_out_totals.total_out, 0)) as current_stock')
            );
        
        $criticalStock = DB::table(DB::raw('(' . $subquery->toSql() . ') as product_stocks'))
            ->whereRaw('current_stock <= 15')
            ->count();
        
        Log::info('Critical stock calculated', ['count' => $criticalStock]);
    } catch (\Exception $e) {
        Log::error('Error calculating critical stock: ' . $e->getMessage());
        $criticalStock = 0;
    }
    
    // 5. Cash on Hand / Expected Cash (Yearly)
    try {
        // Get cash on hand for the entire year
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();
        
        $yearlyCashSales = DB::table('sales')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->where('payment_method', 'cash')
            ->sum('total_amount') ?? 0;
        
        Log::info('Yearly cash sales calculated', ['amount' => $yearlyCashSales]);
    } catch (\Exception $e) {
        Log::error('Error calculating yearly cash sales: ' . $e->getMessage());
        $yearlyCashSales = 0;
    }
    
    // ROW 2: PERFORMANCE & ALERTS
    
    // 6. Sales Trend data (already exists in chart-data endpoint)
    // 7. Top 5 Products by Revenue (last 30 days)
    try {
        $topProductsByRevenue = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [Carbon::now()->subDays(30), $today->copy()->endOfDay()])
            ->select(
                'products.product_name',
                DB::raw('SUM(sale_items.subtotal) as revenue'),
                DB::raw('SUM(sale_items.quantity) as quantity')
            )
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        
        Log::info('Top products calculated', ['count' => $topProductsByRevenue->count()]);
    } catch (\Exception $e) {
        Log::error('Error calculating top products: ' . $e->getMessage());
        $topProductsByRevenue = collect([]);
    }
    
    $totalRevenue30Days = $topProductsByRevenue->sum('revenue');
    $topProductsByRevenue->each(function($product) use ($totalRevenue30Days) {
        $product->contribution_percent = $totalRevenue30Days > 0 ? round(($product->revenue / $totalRevenue30Days) * 100, 1) : 0;
    });
    
    // 8. Top Branches by Revenue and Profit Margin
    try {
        $topBranches = DB::table('sales')
            ->join('branches', 'sales.branch_id', '=', 'branches.id')
            ->whereBetween('sales.created_at', [Carbon::now()->subDays(30), $today->copy()->endOfDay()])
            ->select(
                'branches.branch_name',
                'branches.id as branch_id',
                DB::raw('SUM(sales.total_amount) as revenue'),
                DB::raw('COUNT(sales.id) as transaction_count')
            )
            ->groupBy('branches.id', 'branches.branch_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
        
        // Calculate profit margin for each branch
        $topBranches->each(function($branch) use ($today) {
            try {
                $branchCost = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->join('stock_ins', function($join) {
                        $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                             ->on('sales.branch_id', '=', 'stock_ins.branch_id');
                    })
                    ->where('sales.branch_id', $branch->branch_id)
                    ->whereBetween('sales.created_at', [Carbon::now()->subDays(30), $today->copy()->endOfDay()])
                    ->selectRaw('SUM(sale_items.quantity * stock_ins.price) as cost')
                    ->value('cost') ?? 0;
                
                $branch->profit_margin = $branch->revenue > 0 ? round((($branch->revenue - $branchCost) / $branch->revenue) * 100, 1) : 0;
            } catch (\Exception $e) {
                Log::error('Error calculating branch profit margin: ' . $e->getMessage());
                $branch->profit_margin = 0;
            }
        });
        
        Log::info('Top branches calculated', ['count' => $topBranches->count()]);
    } catch (\Exception $e) {
        Log::error('Error calculating top branches: ' . $e->getMessage());
        $topBranches = collect([]);
    }
    
    // 9. Alerts Panel - Out of Stock Items
    try {
        $subquery = DB::table('products')
            ->leftJoinSub(
                DB::table('stock_ins')
                    ->select('product_id', DB::raw('SUM(quantity) as total_in'))
                    ->groupBy('product_id'),
                'stock_in_totals',
                'products.id',
                '=',
                'stock_in_totals.product_id'
            )
            ->leftJoinSub(
                DB::table('stock_outs')
                    ->select('product_id', DB::raw('SUM(quantity) as total_out'))
                    ->groupBy('product_id'),
                'stock_out_totals',
                'products.id',
                '=',
                'stock_out_totals.product_id'
            )
            ->select(
                'products.id',
                'products.product_name',
                DB::raw('(COALESCE(stock_in_totals.total_in, 0) - COALESCE(stock_out_totals.total_out, 0)) as current_stock')
            );
        
        $outOfStockItems = DB::table(DB::raw('(' . $subquery->toSql() . ') as product_stocks'))
            ->whereRaw('current_stock <= 15')
            ->count();
        
        Log::info('Out of stock items calculated', ['count' => $outOfStockItems]);
    } catch (\Exception $e) {
        Log::error('Error calculating out of stock items: ' . $e->getMessage());
        $outOfStockItems = 0;
    }
    
    // 10. Negative Profit Items (items sold below cost)
    try {
        $negativeProfitItems = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('stock_ins', function($join) {
                $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                     ->on('sales.branch_id', '=', 'stock_ins.branch_id');
            })
            ->whereDate('sales.created_at', $today)
            ->whereRaw('sale_items.quantity * stock_ins.price > sale_items.subtotal')
            ->count();
        
        Log::info('Negative profit items calculated', ['count' => $negativeProfitItems]);
    } catch (\Exception $e) {
        Log::error('Error calculating negative profit items: ' . $e->getMessage());
        $negativeProfitItems = 0;
    }
    
    // 14. Voided Sales Today
    try {
        $voidedSalesToday = DB::table('sales')
            ->whereDate('created_at', '=', $today)
            ->where('voided', true)
            ->count();
        
        Log::info('Voided sales calculated', ['count' => $voidedSalesToday]);
    } catch (\Exception $e) {
        Log::error('Error calculating voided sales: ' . $e->getMessage());
        $voidedSalesToday = 0;
    }
    
    // 11. Below Cost Sales (items sold below cost)
    try {
        $belowCostSales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('stock_ins', function($join) {
                $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                     ->on('sales.branch_id', '=', 'stock_ins.branch_id');
            })
            ->whereDate('sales.created_at', $today)
            ->whereRaw('sale_items.quantity * stock_ins.price > sale_items.subtotal')
            ->count();
        
        Log::info('Below cost sales calculated', ['count' => $belowCostSales]);
    } catch (\Exception $e) {
        Log::error('Error calculating below cost sales: ' . $e->getMessage());
        $belowCostSales = 0;
    }
    
    // 12. High Discount Usage (transactions with high discounts)
    try {
        $highDiscountUsage = DB::table('sales')
            ->whereDate('created_at', $today)
            ->where('discount_percentage', '>', 15) // More reasonable threshold
            ->count();
        
        Log::info('High discount usage calculated', ['count' => $highDiscountUsage]);
    } catch (\Exception $e) {
        Log::error('Error calculating high discount usage: ' . $e->getMessage());
        $highDiscountUsage = 0;
    }
    
    // 13. Refunds Today
    try {
        $refundsToday = DB::table('refunds')
            ->whereDate('created_at', $today)
            ->count();
        
        Log::info('Refunds calculated', ['count' => $refundsToday]);
    } catch (\Exception $e) {
        Log::error('Error calculating refunds: ' . $e->getMessage());
        $refundsToday = 0;
    }
    
    // ROW 3: OPERATIONS SNAPSHOT
    
    // 11. Cashier Performance
    try {
        $cashierPerformance = DB::table('sales')
            ->join('users', 'sales.cashier_id', '=', 'users.id')
            ->whereDate('sales.created_at', $today)
            ->select(
                'users.id',
                'users.name',
                DB::raw('SUM(sales.total_amount) as total_sales'),
            DB::raw('COUNT(sales.id) as transaction_count'),
            DB::raw('AVG(sales.total_amount) as avg_transaction')
        )
        ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
        
        Log::info('Cashier performance calculated', ['count' => $cashierPerformance->count()]);
    } catch (\Exception $e) {
        Log::error('Error calculating cashier performance: ' . $e->getMessage());
        $cashierPerformance = collect([]);
    }
    
    // 12. Transaction Summary
    try {
        $totalTransactions = DB::table('sales')
        ->whereDate('created_at', $today)
            ->count();
        
        $avgTransactionValue = DB::table('sales')
            ->whereDate('created_at', $today)
            ->avg('total_amount') ?? 0;
        
        $highestSaleToday = DB::table('sales')
            ->whereDate('created_at', $today)
            ->max('total_amount') ?? 0;
        
        Log::info('Transaction summary calculated', ['total' => $totalTransactions, 'avg' => $avgTransactionValue]);
    } catch (\Exception $e) {
        Log::error('Error calculating transaction summary: ' . $e->getMessage());
        $totalTransactions = 0;
        $avgTransactionValue = 0;
        $highestSaleToday = 0;
    }
    
    return response()->json([
        // Row 1: Today's KPIs
        'todayKPIs' => [
            'sales' => [
                'amount' => (float) $todaySales,
                'change' => $salesChange,
                'transactions' => $todayTransactions
            ],
            'profit' => [
                'amount' => (float) $todayProfit,
                'isPositive' => $todayProfit >= 0
            ],
            'expenses' => [
                'amount' => (float) $todayExpenses,
                'biggestCategory' => $biggestExpenseCategory
            ],
            'criticalStock' => [
                'count' => $criticalStock
            ],
            'cashOnHand' => [
                'amount' => (float) $yearlyCashSales
            ]
        ],
        
        // Row 2: Performance & Alerts
        'performance' => [
            'topProductsByRevenue' => $topProductsByRevenue,
            'topBranches' => $topBranches
        ],
        
        'alerts' => [
            'outOfStock' => $outOfStockItems,
            'negativeProfit' => $negativeProfitItems,
            'voidedSales' => $voidedSalesToday,
            'belowCostSales' => $belowCostSales,
            'highDiscountUsage' => $highDiscountUsage,
            'refunds' => $refundsToday
        ],
        
        // Row 3: Operations
        'operations' => [
            'cashierPerformance' => $cashierPerformance,
            'transactionSummary' => [
                'totalTransactions' => $totalTransactions,
                'avgTransactionValue' => (float) $avgTransactionValue,
                'highestSaleToday' => (float) $highestSaleToday
            ]
        ],
        
        'date' => $today->toDateString()
    ]);
    
    } catch (\Exception $e) {
        Log::error('Critical error in dashboard widgets: ' . $e->getMessage());
        
        // Return safe default values
        return response()->json([
            'todayKPIs' => [
                'sales' => ['amount' => 0, 'change' => 0, 'transactions' => 0],
                'profit' => ['amount' => 0, 'isPositive' => true],
                'expenses' => ['amount' => 0, 'biggestCategory' => (object) ['name' => 'N/A', 'total' => 0]],
                'criticalStock' => 0,
                'cashOnHand' => 0
            ],
            'performance' => [
                'topProductsByRevenue' => [],
                'topBranches' => []
            ],
            'alerts' => [
                'outOfStock' => 0,
                'negativeProfit' => 0,
                'voidedSales' => 0,
                'belowCostSales' => 0,
                'highDiscountUsage' => 0,
                'refunds' => 0
            ],
            'operations' => [
                'cashierPerformance' => [],
                'transactionSummary' => [
                    'totalTransactions' => 0,
                    'avgTransactionValue' => 0,
                    'highestSaleToday' => 0
                ]
            ],
            'date' => Carbon::today()->toDateString(),
            'error' => 'Dashboard data temporarily unavailable'
        ]);
    }
    
})->middleware('auth')->name('dashboard.widgets');

// POS Route
Route::get('/pos', [PosAdminController::class, 'index'])->name('pos.index')->middleware('auth');
Route::post('/pos', [PosAdminController::class, 'store'])->name('pos.store')->middleware('auth');
Route::post('/pos/lookup', [PosAdminController::class, 'lookup'])->name('pos.lookup')->middleware('auth');
Route::post('/pos/cashier/validate', [PosAdminController::class, 'validateCashier'])->name('pos.cashier.validate')->middleware('auth');
Route::post('/admin/pos/checkout', [\App\Http\Controllers\Admin\PosController::class, 'checkout'])->name('admin.pos.checkout')->middleware('auth');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    Route::prefix('superadmin')->name('superadmin.')->group(function () {
        // Product routes
        Route::get('/products', [SuperAdminProductController::class, 'index'])->middleware('ability:products,view')->name('products.index');
        Route::get('/products/create', [SuperAdminProductController::class, 'create'])->middleware('ability:products,edit')->name('products.create');
        Route::post('/products', [SuperAdminProductController::class, 'store'])->middleware('ability:products,edit')->name('products.store');
        Route::get('/products/{product}', [SuperAdminProductController::class, 'show'])->middleware('ability:products,view')->name('products.show');
        Route::get('/products/{product}/edit', [SuperAdminProductController::class, 'edit'])->middleware('ability:products,edit')->name('products.edit');
        Route::put('/products/{product}', [SuperAdminProductController::class, 'update'])->middleware('ability:products,edit')->name('products.update');
        Route::post('/products/{product}/update-image', [SuperAdminProductController::class, 'updateImage'])->middleware('ability:products,edit')->name('products.updateImage');
        Route::delete('/products/{product}', [SuperAdminProductController::class, 'destroy'])->middleware('ability:products,full')->name('products.destroy');

        // Categories routes (part of products module)
        Route::get('/categories', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'index'])->middleware('ability:products,view')->name('categories.index');
        Route::post('/categories', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'store'])->middleware('ability:products,edit')->name('categories.store');
        Route::get('/categories/create', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'create'])->middleware('ability:products,edit')->name('categories.create');
        Route::get('/categories/{category}/edit', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'edit'])->middleware('ability:products,edit')->name('categories.edit');
        Route::put('/categories/{category}', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'update'])->middleware('ability:products,edit')->name('categories.update');
        Route::put('/categories/bulk-update', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'bulkUpdate'])->middleware('ability:products,edit')->name('categories.bulkUpdate');
        Route::delete('/categories/bulk-delete', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'bulkDestroy'])->middleware('ability:products,edit')->name('categories.bulkDestroy');
        Route::delete('/categories/{category}', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'destroy'])->middleware('ability:products,full')->name('categories.destroy');

        // Inventory routes
        Route::get('/inventory', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/out-of-stock', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'outOfStock'])->name('inventory.out-of-stock');
        Route::post('/inventory/{product}/adjust', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('/inventory/{product}/stock-in', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'stockIn'])->name('inventory.stock-in');
        Route::get('/inventory/out-of-stock/export', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'exportOutOfStockPDF'])->name('inventory.out-of-stock.export');

        // API routes for out-of-stock functionality
        Route::get('/api/branches', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getBranches']);
        
        Route::get('/inventory/product-stock/{productId}', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getProductStock']);
        
        Route::get('/purchases/by-product/{productId}', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getProductPurchases']);
        
        Route::get('/inventory/product-sales/{productId}', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getProductSales']);

        // Sales routes
        Route::get('/sales', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/management', [\App\Http\Controllers\Admin\SalesController::class, 'management'])->name('sales.management.index');
        Route::get('/sales/voided', [\App\Http\Controllers\Admin\SalesController::class, 'voidedSales'])->name('sales.voided');
        Route::get('/sales/items-today', [\App\Http\Controllers\Admin\SalesController::class, 'getItemsSoldToday'])->name('sales.items-today');
        Route::get('/sales/todays-revenue', [\App\Http\Controllers\Admin\SalesController::class, 'getTodaysRevenue'])->name('sales.todays-revenue');
        Route::get('/sales/this-month-sales', [\App\Http\Controllers\Admin\SalesController::class, 'getThisMonthSales'])->name('sales.this-month-sales');
        Route::get('/sales/{sale}', [\App\Http\Controllers\Admin\SaleController::class, 'show'])->name('sales.show');
        Route::get('/sales/{sale}/receipt', [\App\Http\Controllers\Admin\SaleController::class, 'receipt'])->name('sales.receipt');
        Route::get('/sales/{sale}/items', [\App\Http\Controllers\Admin\SalesController::class, 'getSaleItems'])->name('sales.items');
        Route::post('/sales/{sale}/void', [\App\Http\Controllers\Admin\SalesController::class, 'voidSale'])->name('sales.void');

        // Purchase routes
        Route::get('/purchases', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'show'])->name('purchases.show');
        Route::post('/purchases/ocr-product-match', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'matchProduct'])->name('purchases.ocr-product-match');

        Route::post('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->middleware('ability:inventory,edit')->name('inventory.adjust');
    });

    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        // Stock In routes
        Route::get('stockin', [\App\Http\Controllers\Admin\PosAdminController::class, 'stockInIndex'])->name('stockin.index');
        Route::get('stockin/create', [\App\Http\Controllers\Admin\PosAdminController::class, 'stockInCreate'])->name('stockin.create');
        Route::post('stockin', [\App\Http\Controllers\Admin\PosAdminController::class, 'stockInStore'])->name('stockin.store');
        Route::get('stockin/products-by-purchase/{purchase}', [\App\Http\Controllers\Admin\PosAdminController::class, 'stockInProductsByPurchase'])->name('stockin.products-by-purchase');

        // Sales route
        Route::get('sales', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales.index');
        Route::get('sales/management', [\App\Http\Controllers\Admin\SalesController::class, 'management'])->name('sales.management.index');
        Route::get('sales/{sale}', [\App\Http\Controllers\Admin\SaleController::class, 'show'])->name('sales.show');
        Route::get('sales/{sale}/receipt', [\App\Http\Controllers\Admin\SaleController::class, 'receipt'])->name('sales.receipt');
        Route::get('sales/{sale}/items', [\App\Http\Controllers\Admin\SalesController::class, 'getSaleItems'])->name('sales.items');
        Route::get('sales/items-today', [\App\Http\Controllers\Admin\SalesController::class, 'getItemsSoldToday'])->name('sales.items-today');
        Route::get('sales/todays-revenue', [\App\Http\Controllers\Admin\SalesController::class, 'getTodaysRevenue'])->name('sales.todays-revenue');
        Route::get('sales/this-month-sales', [\App\Http\Controllers\Admin\SalesController::class, 'getThisMonthSales'])->name('sales.this-month-sales');

        // Reports routes - unified in index page
        Route::get('reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
        Route::post('reports/filter', [\App\Http\Controllers\Admin\ReportsController::class, 'filter'])->name('reports.filter');
        Route::post('reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');
    });

    // Stock Transfer routes
    Route::get('/stocktransfer', [\App\Http\Controllers\SuperAdmin\StockTransferController::class, 'index'])->middleware('ability:inventory,view')->name('stocktransfer.index');
    Route::post('/stocktransfer', [\App\Http\Controllers\SuperAdmin\StockTransferController::class, 'store'])->middleware('ability:inventory,edit')->name('stocktransfer.store');
    Route::put('/stocktransfer/{stockTransfer}', [\App\Http\Controllers\SuperAdmin\StockTransferController::class, 'update'])->middleware('ability:inventory,edit')->name('stocktransfer.update');

    // Settings routes (guard at least with view-level ability)
    Route::middleware('ability:settings,view')->group(function () {
        Route::resource('brands', \App\Http\Controllers\SuperAdmin\BrandController::class);
        Route::resource('product-types', \App\Http\Controllers\ProductTypeController::class);
        Route::resource('unit-types', \App\Http\Controllers\UnitTypeController::class);
        Route::resource('branches', \App\Http\Controllers\SuperAdmin\BranchController::class);
        Route::resource('sales', \App\Http\Controllers\Admin\SalesController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('taxes', \App\Http\Controllers\Admin\TaxController::class);
        Route::resource('receipt-templates', \App\Http\Controllers\Admin\ReceiptTemplateController::class);
            // Custom receipt template routes
            Route::get('receipt-templates/{receiptTemplate}/preview', [\App\Http\Controllers\Admin\ReceiptTemplateController::class, 'preview'])->name('receipt-templates.preview');
            Route::post('receipt-templates/{receiptTemplate}/set-default', [\App\Http\Controllers\Admin\ReceiptTemplateController::class, 'setDefault'])->name('receipt-templates.set-default');
        });

        // Separate Suppliers routes with proper abilities
        Route::resource('suppliers', \App\Http\Controllers\SuperAdmin\SupplierController::class);
        
        // Test route for debugging
        Route::get('test-supplier', function() {
            try {
                \App\Models\Supplier::create([
                    'supplier_name' => 'Test Supplier ' . date('Y-m-d H:i:s'),
                    'contact_person' => 'Test Contact',
                    'email' => 'test' . time() . '@example.com',
                    'phone' => '123456789',
                    'address' => 'Test Address',
                    'status' => 'active'
                ]);
                return 'Supplier created successfully! Count: ' . \App\Models\Supplier::count();
            } catch (\Exception $e) {
                return 'Error creating supplier: ' . $e->getMessage();
            }
        });
        
        // Test POST route
        Route::post('test-supplier-post', function(\Illuminate\Http\Request $request) {
            \Illuminate\Support\Facades\Log::info('=== TEST POST ROUTE STARTED ===');
            \Illuminate\Support\Facades\Log::info('Request data:', $request->all());
            
            try {
                $supplier = \App\Models\Supplier::create([
                    'supplier_name' => $request->supplier_name,
                    'contact_person' => $request->contact_person ?? null,
                    'email' => $request->email ?? null,
                    'phone' => $request->phone ?? null,
                    'address' => $request->address ?? null,
                    'status' => 'active'
                ]);
                
                \Illuminate\Support\Facades\Log::info('✅ Test supplier created successfully');
                
                return response()->json([
                    'success' => true,
                    'supplier' => $supplier,
                    'message' => 'Supplier created successfully!'
                ]);
                
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('❌ Test route error: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 500);
            }
        });
        
        // API routes for out-of-stock functionality
        Route::get('/api/branches', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getBranches']);
        
        Route::get('/inventory/product-stock/{productId}', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getProductStock']);
        
        Route::get('/purchases/by-product/{productId}', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getProductPurchases']);
        
        Route::get('/inventory/product-sales/{productId}', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'getProductSales']);

        // Admin-only user management (account creation, access control)
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
            Route::post('/users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');

            // Role-based access configuration UI
            Route::get('/access', [\App\Http\Controllers\Admin\AccessController::class, 'index'])->name('access.index');
            Route::get('/access/logs', [\App\Http\Controllers\Admin\AccessController::class, 'accessLogs'])->name('access.logs');
            Route::post('/access', [\App\Http\Controllers\Admin\AccessController::class, 'store'])->name('access.store');
            Route::post('/roles', [\App\Http\Controllers\Admin\AccessController::class, 'storeRole'])->name('roles.store');
            Route::put('/roles/{role}', [\App\Http\Controllers\Admin\AccessController::class, 'updateRole'])->name('roles.update');
            Route::delete('/roles/{role}', [\App\Http\Controllers\Admin\AccessController::class, 'destroyRole'])->name('roles.destroy');

            // User management for access control
            Route::get('/access/users/{id}', [\App\Http\Controllers\Admin\AccessController::class, 'editUser'])->name('access.users.edit');
            Route::put('/access/users/{id}', [\App\Http\Controllers\Admin\AccessController::class, 'updateUser'])->name('access.users.update');
            Route::delete('/access/users/{id}', [\App\Http\Controllers\Admin\AccessController::class, 'deleteUser'])->name('access.users.delete');

            // Permission management
            Route::post('/access/permissions/update', [\App\Http\Controllers\Admin\AccessPermissionController::class, 'updatePermission'])->name('access.permissions.update');
            Route::get('/access/permissions/{roleId}', [\App\Http\Controllers\Admin\AccessPermissionController::class, 'getPermissions'])->name('access.permissions.get');

            // Reports routes - unified in index page
            Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
            Route::post('/reports/filter', [\App\Http\Controllers\Admin\ReportsController::class, 'filter'])->name('reports.filter');
            Route::post('/reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');

            // Routes for Select2 expense category search and creation
            Route::get('expense-categories-search', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'index'])->name('expense-categories.search');
            Route::post('expense-categories', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'store'])->name('expense-categories.store');

            // Customer routes
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
            Route::put('customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');
            Route::post('customers/make-payment', [CustomerController::class, 'makePayment'])->name('customers.make-payment');
            Route::get('customers/credit-limits', [CustomerController::class, 'creditLimits'])->name('customers.credit-limits');
            Route::get('customers/payment-history', [CustomerController::class, 'paymentHistory'])->name('customers.payment-history');

            // Sales route
            Route::get('sales', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales.index');
            Route::get('sales/{sale}', [\App\Http\Controllers\Admin\SaleController::class, 'show'])->name('sales.show');
            Route::get('sales/{sale}/receipt', [\App\Http\Controllers\Admin\SaleController::class, 'receipt'])->name('sales.receipt');
            Route::get('sales/{sale}/items', [\App\Http\Controllers\Admin\SalesController::class, 'getSaleItems'])->name('sales.items');
            Route::get('sales/items-today', [\App\Http\Controllers\Admin\SalesController::class, 'getItemsSoldToday'])->name('sales.items-today');
            Route::get('sales/todays-revenue', [\App\Http\Controllers\Admin\SalesController::class, 'getTodaysRevenue'])->name('sales.todays-revenue');
            Route::get('sales/this-month-sales', [\App\Http\Controllers\Admin\SalesController::class, 'getThisMonthSales'])->name('sales.this-month-sales');

            // Expenses routes
            Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);
            Route::get('expenses/todays-expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'getTodaysExpenses'])->name('expenses.todays-expenses');
            Route::get('expenses/this-month-expenses', [\App\Http\Controllers\Admin\ExpenseController::class, 'getThisMonthExpenses'])->name('expenses.this-month-expenses');

            // Refund routes
            Route::get('refunds', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('refunds.index');
            Route::post('refunds', [\App\Http\Controllers\Admin\RefundController::class, 'store'])->name('refunds.store');
            Route::get('refunds/create', [\App\Http\Controllers\Admin\RefundController::class, 'create'])->name('refunds.create');
            Route::post('refunds/{refund}/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('refunds.approve');
            Route::post('refunds/{refund}/reject', [\App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('refunds.reject');

            // Credit routes
            Route::get('credits', [\App\Http\Controllers\Admin\CreditController::class, 'index'])->name('credits.index');
            Route::get('credits/create', [\App\Http\Controllers\Admin\CreditController::class, 'create'])->name('credits.create');
            Route::post('credits', [\App\Http\Controllers\Admin\CreditController::class, 'store'])->name('credits.store');
            Route::get('credits/credit-limits-data', [\App\Http\Controllers\Admin\CreditController::class, 'creditLimitsData'])->name('credits.credit-limits-data');
            Route::post('credits/update-credit-limit', [\App\Http\Controllers\Admin\CreditController::class, 'updateCreditLimit'])->name('credits.update-credit-limit');
            Route::get('credits/customer/{customerId}/full-history', [\App\Http\Controllers\Admin\CreditController::class, 'fullCreditHistory'])->name('credits.full-history');
            Route::get('credits/customer/{customerId}', [\App\Http\Controllers\Admin\CreditController::class, 'customerCreditDetails'])->name('credits.customer')->where('customerId', '[0-9]+');
            Route::get('credits/{credit}', [\App\Http\Controllers\Admin\CreditController::class, 'show'])->name('credits.show');
            Route::post('credits/{credit}/make-payment', [\App\Http\Controllers\Admin\CreditController::class, 'makePayment'])->name('credits.make-payment');
            Route::post('credits/{credit}/payment', [\App\Http\Controllers\Admin\CreditController::class, 'makePayment'])->name('credits.payment');
            Route::post('credits/multi-payment', [\App\Http\Controllers\Admin\CreditController::class, 'makeMultiCreditPayment'])->name('credits.multi-payment');
            Route::get('credits/customer/{customerId}/details', [\App\Http\Controllers\Admin\CreditController::class, 'getCustomerCreditDetails'])->name('credits.customer.details');
            Route::post('credits/{credit}/status', [\App\Http\Controllers\Admin\CreditController::class, 'updateStatus'])->name('credits.status');
            Route::post('credits/{credit}/update-name', [\App\Http\Controllers\Admin\CreditController::class, 'updateCustomerName'])->name('credits.update-name');
            Route::post('credits/{credit}/update-customer', [\App\Http\Controllers\Admin\CreditController::class, 'updateWalkInCustomer'])->name('credits.update-customer');

            // Customer routes
            Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/credit-limits', [\App\Http\Controllers\Admin\CustomerController::class, 'creditLimits'])->name('customers.credit-limits');
            Route::get('customers/payment-history', [\App\Http\Controllers\Admin\CustomerController::class, 'paymentHistory'])->name('customers.payment-history');
        });
    });

    Route::post('/ui/sidebar/user-mgmt', [\App\Http\Controllers\UiStateController::class, 'setSidebarUserMgmt'])->name('ui.sidebar.user-mgmt');

    Route::get('/password/reset', function () {return view('auth.passwords.email');})->name('password.request');

    Route::post('/password/email', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $user = \App\Models\User::where('email', $request->input('email'))->first();
        if ($user) {
            // dispatch reset email here if implemented
        }
        return redirect()->route('login')->with('success', 'If an account exists for that email, a password reset link has been sent.');
    })->name('password.email');

