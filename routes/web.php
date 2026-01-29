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

// Dashboard chart data (JSON)
Route::get('/dashboard/chart', [DashboardController::class, 'chartData'])->middleware('auth')->name('dashboard.chart');
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
        $criticalStock = DB::table('products')
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
                'products.product_name',
                DB::raw('(COALESCE(stock_in_totals.total_in, 0) - COALESCE(stock_out_totals.total_out, 0)) as current_stock')
            )
            ->havingRaw('(COALESCE(stock_in_totals.total_in, 0) - COALESCE(stock_out_totals.total_out, 0)) <= 5')
            ->count();
        
        Log::info('Critical stock calculated', ['count' => $criticalStock]);
    } catch (\Exception $e) {
        Log::error('Error calculating critical stock: ' . $e->getMessage());
        $criticalStock = 0;
    }
    
    // 5. Cash on Hand / Expected Cash
    try {
        $todayCashSales = DB::table('sales')
            ->whereDate('created_at', $today)
            ->where('payment_method', 'cash')
            ->sum('total_amount') ?? 0;
        
        Log::info('Cash sales calculated', ['amount' => $todayCashSales]);
    } catch (\Exception $e) {
        Log::error('Error calculating cash sales: ' . $e->getMessage());
        $todayCashSales = 0;
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
    
    // 9. Alerts Panel
    try {
        $outOfStockItems = DB::table('products')
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
                'products.product_name',
                DB::raw('(COALESCE(stock_in_totals.total_in, 0) - COALESCE(stock_out_totals.total_out, 0)) as current_stock')
            )
            ->havingRaw('(COALESCE(stock_in_totals.total_in, 0) - COALESCE(stock_out_totals.total_out, 0)) = 0')
            ->count();
        
        Log::info('Out of stock items calculated', ['count' => $outOfStockItems]);
    } catch (\Exception $e) {
        Log::error('Error calculating out of stock items: ' . $e->getMessage());
        $outOfStockItems = 0;
    }
    
    try {
        $negativeProfitItems = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('stock_ins', function($join) {
                $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                     ->on('sales.branch_id', '=', 'stock_ins.branch_id');
            })
            ->whereDate('sales.created_at', $today)
            ->havingRaw('sale_items.unit_price <= stock_ins.price')
            ->count();
        
        Log::info('Negative profit items calculated', ['count' => $negativeProfitItems]);
    } catch (\Exception $e) {
        Log::error('Error calculating negative profit items: ' . $e->getMessage());
        $negativeProfitItems = 0;
    }
    
    try {
        $voidedSalesToday = DB::table('sales')
            ->whereDate('created_at', $today)
            ->where('status', 'voided') // Assuming there's a status field
            ->count();
        
        Log::info('Voided sales calculated', ['count' => $voidedSalesToday]);
    } catch (\Exception $e) {
        Log::error('Error calculating voided sales: ' . $e->getMessage());
        $voidedSalesToday = 0;
    }
    
    // 10. Unusual Activity
    try {
        $belowCostSales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('stock_ins', function($join) {
                $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                     ->on('sales.branch_id', '=', 'stock_ins.branch_id');
            })
            ->whereDate('sales.created_at', $today)
            ->whereRaw('sale_items.unit_price < stock_ins.price')
            ->count();
        
        Log::info('Below cost sales calculated', ['count' => $belowCostSales]);
    } catch (\Exception $e) {
        Log::error('Error calculating below cost sales: ' . $e->getMessage());
        $belowCostSales = 0;
    }
    
    try {
        $highDiscountUsage = DB::table('sales')
            ->whereDate('created_at', $today)
            ->where('discount_percentage', '>', 20) // Assuming discount field
            ->count();
        
        Log::info('High discount usage calculated', ['count' => $highDiscountUsage]);
    } catch (\Exception $e) {
        Log::error('Error calculating high discount usage: ' . $e->getMessage());
        $highDiscountUsage = 0;
    }
    
    // ROW 3: OPERATIONS SNAPSHOT
    
    // 11. Cashier Performance
    try {
        $cashierPerformance = DB::table('sales')
            ->join('users', 'sales.cashier_id', '=', 'users.id')
            ->whereDate('sales.created_at', $today)
            ->select(
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
                'amount' => (float) $todayCashSales
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
            'highDiscountUsage' => $highDiscountUsage
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
                'highDiscountUsage' => 0
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
        Route::delete('/categories/bulk-delete', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'bulkDestroy'])->middleware('ability:products,full')->name('categories.bulkDestroy');
        Route::delete('/categories/{category}', [\App\Http\Controllers\SuperAdmin\CategoryController::class, 'destroy'])->middleware('ability:products,full')->name('categories.destroy');

        Route::post('/purchases/ocr-product-match', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'matchProduct'])->middleware('ability:purchases,edit')->name('purchases.ocr-product-match');
        Route::get('/purchases', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'index'])->middleware('ability:purchases,view')->name('purchases.index');
        Route::get('/purchases/create', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'create'])->middleware('ability:purchases,edit')->name('purchases.create');
        Route::post('/purchases', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'store'])->middleware('ability:purchases,edit')->name('purchases.store');
        Route::get('/purchases/{purchase}', [\App\Http\Controllers\SuperAdmin\PurchaseController::class, 'show'])->middleware('ability:purchases,view')->name('purchases.show');

        // Stock In routes
        // DEBUG: Temporarily removed middleware to test for permissions issue.
        Route::get('/stockin', [\App\Http\Controllers\SuperAdmin\StockInController::class, 'index'])->name('stockin.index');
        Route::get('/stockin/create', [\App\Http\Controllers\SuperAdmin\StockInController::class, 'create'])->name('stockin.create');
        Route::post('/stockin', [\App\Http\Controllers\SuperAdmin\StockInController::class, 'store'])->name('stockin.store');
        Route::get('/stockin/products-by-purchase/{purchase}', [\App\Http\Controllers\SuperAdmin\StockInController::class, 'getProductsByPurchase'])->name('stockin.products-by-purchase');

        // Inventory routes
        Route::get('/inventory', [InventoryController::class, 'index'])->middleware('ability:inventory,view')->name('inventory.index');
        Route::post('/inventory/{product}/stock-in', [InventoryController::class, 'stockIn'])->middleware('ability:inventory,edit')->name('inventory.stock-in');
        Route::post('/inventory/{product}/adjust', [InventoryController::class, 'adjust'])->middleware('ability:inventory,edit')->name('inventory.adjust');

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
        });

        // Separate Suppliers routes with proper abilities
        Route::resource('suppliers', \App\Http\Controllers\SuperAdmin\SupplierController::class);

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

            // Permission management
            Route::post('/access/permissions/update', [\App\Http\Controllers\Admin\AccessPermissionController::class, 'updatePermission'])->name('access.permissions.update');
            Route::get('/access/permissions/{roleId}', [\App\Http\Controllers\Admin\AccessPermissionController::class, 'getPermissions'])->name('access.permissions.get');

            // Expenses routes
            Route::resource('expenses', \App\Http\Controllers\Admin\ExpenseController::class);

            // Reports routes - unified in index page
            Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports.index');
            Route::post('/reports/filter', [\App\Http\Controllers\Admin\ReportsController::class, 'filter'])->name('reports.filter');
            Route::post('/reports/export', [\App\Http\Controllers\Admin\ReportsController::class, 'export'])->name('reports.export');

            // Routes for Select2 expense category search and creation
            Route::get('expense-categories-search', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'index'])->name('expense-categories.search');
            Route::post('expense-categories', [\App\Http\Controllers\Admin\ExpenseCategoryController::class, 'store'])->name('expense-categories.store');

            // Sales route
            Route::get('sales', [\App\Http\Controllers\Admin\SalesController::class, 'index'])->name('sales.index');
            Route::get('sales/{sale}/items', [\App\Http\Controllers\Admin\SalesController::class, 'getSaleItems'])->name('sales.items');

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
            Route::get('credits/{credit}', [\App\Http\Controllers\Admin\CreditController::class, 'show'])->name('credits.show');
            Route::post('credits/{credit}/payment', [\App\Http\Controllers\Admin\CreditController::class, 'makePayment'])->name('credits.payment');
            Route::post('credits/{credit}/status', [\App\Http\Controllers\Admin\CreditController::class, 'updateStatus'])->name('credits.status');
        });
    });
});

Route::post('/ui/sidebar/user-mgmt', [\App\Http\Controllers\UiStateController::class, 'setSidebarUserMgmt'])
    ->name('ui.sidebar.user-mgmt');

Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $user = \App\Models\User::where('email', $request->input('email'))->first();
    if ($user) {
        // dispatch reset email here if implemented
    }
    return redirect()->route('login')->with('success', 'If an account exists for that email, a password reset link has been sent.');
})->name('password.email');

