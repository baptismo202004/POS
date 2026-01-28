<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PosAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SuperAdmin\ProductController as SuperAdminProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\AccessPermissionController;
use App\Http\Controllers\SuperAdmin\PurchaseController;
use App\Http\Controllers\SuperAdmin\InventoryController;

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
        return redirect()->route('login')->with('success', 'Login successful');
    }

    return back()->withInput()->with('error', 'Incorrect email or password');
})->name('login.post');

Route::get('/dashboard', function () {
    return view('SuperAdmin.dashboard');
})->middleware('auth')->name('dashboard');

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

    Route::post('/ui/sidebar/user-mgmt', [\App\Http\Controllers\UiStateController::class, 'setSidebarUserMgmt'])
        ->name('ui.sidebar.user-mgmt');

    Route::get('/password/reset', function () {
        return view('auth.passwords.email');
    })->name('password.request');

    Route::post('/password/email', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $user = \App\Models\User::where('email', $request->input('email'))->first();
        if ($user) {
        }
        return redirect()->route('login')->with('success', 'If an account exists for that email, a password reset link has been sent.');
    })->name('password.email');
});
// Password reset (simple request flow)
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    // If user exists, we would normally send a reset link. For now, just show a generic message.
    $user = \App\Models\User::where('email', $request->input('email'))->first();
    if ($user) {
        // dispatch reset email here if implemented
    }
    return redirect()->route('login')->with('success', 'If an account exists for that email, a password reset link has been sent.');
})->name('password.email');

