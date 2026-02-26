<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Credit;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\UnitType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CashierDashboardController extends Controller
{
    /**
     * Display the cashier dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Check if branch exists and is active
        $branch = DB::table('branches')->find($branchId);
        if (! $branch || $branch->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withInput()->with('error', 'Your assigned branch is currently inactive. Please contact your administrator.');
        }

        // Get today's sales data for the current branch
        $today = Carbon::today();
        $todaySales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as total_sales, SUM(total_amount) as total_revenue')
            ->first();

        // Get today's expenses for the current branch
        $todayExpenses = \App\Models\Expense::where('branch_id', $branchId)
            ->whereDate('expense_date', $today)
            ->sum('amount');

        // Get today's refunds data (branch-scoped through sales)
        $todayRefunds = \App\Models\Refund::whereDate('created_at', $today)
            ->whereHas('sale', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_refunds, COALESCE(SUM(refund_amount), 0) as total_refund_amount, COALESCE(SUM(quantity_refunded), 0) as total_items')
            ->first();

        // Get low stock items count for the current branch
        $lowStockCount = Product::whereHas('stockIns', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId)
                ->whereRaw('stock_ins.quantity < products.low_stock_threshold');
        })
            ->where('branch_id', $branchId)
            ->count();

        // Get top products for the current branch
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('stock_ins', function ($join) use ($branchId) {
                $join->on('sale_items.product_id', '=', 'stock_ins.product_id')
                    ->where('stock_ins.branch_id', $branchId);
            })
            ->where('sales.branch_id', $branchId)
            ->whereDate('sales.created_at', $today)
            ->selectRaw('products.product_name, SUM(sale_items.quantity) as total_sold, SUM(sale_items.quantity * sale_items.unit_price) as revenue')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        // Get recent sales for the current branch
        $recentSales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Prepare data for the view
        $modules = [
            'products' => ['label' => 'Products', 'icon' => 'box'],
            'product_category' => ['label' => 'Product Category', 'icon' => 'tags'],
            'purchases' => ['label' => 'Purchases', 'icon' => 'shopping-bag'],
            'inventory' => ['label' => 'Inventory', 'icon' => 'warehouse'],
            'stock_in' => ['label' => 'Stock In', 'icon' => 'sign-in-alt'],
            'stock_transfer' => ['label' => 'Stock Transfer', 'icon' => 'exchange-alt'],
            'customer' => ['label' => 'Customers', 'icon' => 'users'],
            'refund_return' => ['label' => 'Refund/Return', 'icon' => 'undo'],
            'sales' => ['label' => 'Sales', 'icon' => 'shopping-cart'],
            'sales_report' => ['label' => 'Sales Reports', 'icon' => 'file-invoice-dollar'],
            'expenses' => ['label' => 'Expenses', 'icon' => 'file-invoice'],
            'credit' => ['label' => 'Credit', 'icon' => 'credit-card'],
        ];

        // Permissions are handled by @canAccess directive in views

        // Prepare chart data
        $labels = [];
        $salesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $sales = DB::table('sales')
                ->where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->sum('total_amount');

            $labels[] = Carbon::today()->subDays($i)->format('M j');
            $salesData[] = $sales ?: 0;
        }

        return view('cashier.dashboard', compact(
            'todaySales',
            'todayExpenses',
            'todayRefunds',
            'lowStockCount',
            'topProducts',
            'recentSales',
            'modules',
            'labels',
            'salesData',
            'branch'
        ));
    }

    /**
     * Get chart data for dashboard.
     */
    public function chartData()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Get sales data for the last 7 days
        $labels = [];
        $salesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $sales = DB::table('sales')
                ->where('branch_id', $branchId)
                ->whereDate('created_at', $date)
                ->sum('total_amount');

            $labels[] = Carbon::today()->subDays($i)->format('M j');
            $salesData[] = $sales ?: 0;
        }

        return response()->json([
            'labels' => $labels,
            'salesData' => $salesData,
        ]);
    }

    /**
     * Get low stock items count for dashboard.
     */
    public function getLowStock()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $lowStockItems = DB::table('products')
            ->whereHas('stockIns', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->whereRaw('stock_ins.quantity < products.reorder_level');
            })
            ->where('branch_id', $branchId)
            ->select('products.*')
            ->get();

        return response()->json([
            'low_stock_items' => $lowStockItems,
        ]);
    }

    // SALES METHODS
    public function salesIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $query = Sale::where('branch_id', $branchId)
            ->with(['saleItems.product', 'customer']);

        // Apply sorting
        $allowedSorts = ['id', 'total_amount', 'created_at', 'payment_method'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $sales = $query->paginate(15);

        return view('cashier.sales.index', compact('sales', 'sortBy', 'sortDirection'));
    }

    public function salesCreate()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        return view('cashier.sales.create', compact('branchId'));
    }

    public function salesReports(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Get report data
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $todaySales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $monthlySales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$startOfMonth, $today->copy()->endOfDay()])
            ->sum('total_amount');

        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.branch_id', $branchId)
            ->whereBetween('sales.created_at', [$startOfMonth, $today->copy()->endOfDay()])
            ->selectRaw('products.product_name, SUM(sale_items.quantity) as total_sold, SUM(sale_items.quantity * sale_items.unit_price) as revenue')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        return view('cashier.sales.reports', compact('todaySales', 'monthlySales', 'topProducts'));
    }

    // PRODUCTS METHODS
    public function products(Request $request)
    {
        return $this->productsIndex($request);
    }

    public function productsIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        $query = Product::query();

        // Apply search if provided
        if ($search) {
            $query->where('product_name', 'like', '%'.$search.'%')
                ->orWhere('barcode', 'like', '%'.$search.'%');
        }

        // Apply branch filtering
        $query->whereHas('stockIns', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        });

        // Apply sorting
        $allowedSorts = ['id', 'product_name', 'selling_price', 'current_stock'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $products = $query->with(['brand', 'category', 'unitTypes'])
            ->orderBy($sortBy, $sortDirection)
            ->paginate(15);

        return view('cashier.products.index', compact('products', 'sortBy', 'sortDirection'));
    }

    public function createProduct()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $brands = Brand::all();
        $categories = Category::all();
        $productTypes = ProductType::all();
        $unitTypes = UnitType::all();
        $userBranch = Branch::find($branchId);
        $branches = Branch::all();

        return view('cashier.products.create', compact('brands', 'categories', 'productTypes', 'unitTypes', 'userBranch', 'branches'));
    }

    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'status' => 'required|in:active,inactive',
        ]);

        Product::create([
            'product_name' => $validated['product_name'],
            'barcode' => $validated['barcode'],
            'description' => $validated['description'],
            'selling_price' => $validated['selling_price'],
            'cost_price' => $validated['cost_price'],
            'reorder_level' => $validated['reorder_level'],
            'brand_id' => $validated['brand_id'],
            'category_id' => $validated['category_id'],
            'product_type_id' => $validated['product_type_id'],
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cashier.products.index')->with('success', 'Product created successfully');
    }

    public function showProduct($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $product = Product::findOrFail($id);

        return view('cashier.products.show', compact('product'));
    }

    public function editProduct($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $product = Product::findOrFail($id);
        $brands = Brand::all();
        $categories = Category::all();
        $productTypes = ProductType::all();
        $unitTypes = UnitType::all();

        return view('cashier.products.edit', compact('product', 'brands', 'categories', 'productTypes', 'unitTypes'));
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,'.$id,
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'status' => 'required|in:active,inactive',
        ]);

        $product->update([
            'product_name' => $validated['product_name'],
            'barcode' => $validated['barcode'],
            'description' => $validated['description'],
            'selling_price' => $validated['selling_price'],
            'cost_price' => $validated['cost_price'],
            'reorder_level' => $validated['reorder_level'],
            'brand_id' => $validated['brand_id'],
            'category_id' => $validated['category_id'],
            'product_type_id' => $validated['product_type_id'],
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        return redirect()->route('cashier.products.index')->with('success', 'Product updated successfully');
    }

    public function destroyProduct($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('cashier.products.index')->with('success', 'Product deleted successfully');
    }

    // PRODUCT CATEGORIES METHODS
    public function categories(Request $request)
    {
        return $this->categoriesIndex($request);
    }

    public function categoriesIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        $query = Category::query();

        if ($search) {
            $query->where('category_name', 'like', '%'.$search.'%');
        }

        $categories = $query->orderBy($sortBy, $sortDirection)->paginate(15);

        return view('cashier.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        return view('cashier.categories.create', compact('branchId'));
    }

    public function storeCategory(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'status' => 'required|in:active,inactive',
        ]);

        $category = Category::create([
            'category_name' => $validated['category_name'],
            'status' => $validated['status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category
            ]);
        }

        return redirect()->route('cashier.categories.index')->with('success', 'Category created successfully');
    }

    public function editCategory($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $category = Category::findOrFail($id);

        return view('cashier.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,'.$id,
            'status' => 'required|in:active,inactive',
        ]);

        $category->update([
            'category_name' => $validated['category_name'],
            'status' => $validated['status'],
            'updated_at' => now(),
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
        }

        return redirect()->route('cashier.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroyCategory($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('cashier.categories.index')->with('success', 'Category deleted successfully');
    }

    public function bulkDeleteCategories(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'required|exists:categories,id',
        ]);

        Category::whereIn('id', $validated['category_ids'])->delete();

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Categories deleted successfully'
            ]);
        }

        return redirect()->route('cashier.categories.index')->with('success', 'Categories deleted successfully');
    }

    // PURCHASES METHODS
    public function purchasesIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = DB::table('purchases')
            ->where('branch_id', $branchId);

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', '%'.$search.'%')
                    ->orWhere('supplier_name', 'like', '%'.$search.'%');
            });
        }

        // Apply date filters
        if ($dateFrom) {
            $query->whereDate('purchase_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('purchase_date', '<=', $dateTo);
        }

        // Apply sorting
        $allowedSorts = ['created_at', 'reference_number', 'supplier_name', 'total_cost'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $purchases = $query->paginate(15);

        return view('cashier.purchase.index', compact('purchases', 'sortBy', 'sortDirection'));
    }

    public function purchasesCreate(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $suppliers = Supplier::all();
        $products = Product::all();
        $unit_types = UnitType::all();

        return view('cashier.purchase.create', compact('suppliers', 'branchId', 'products', 'unit_types'));
    }

    public function purchasesOcrProductMatch(Request $request)
    {
        try {
            Log::info('OCR method called', ['request_data' => $request->all()]);
            
            $text = (string) $request->input('text', '');
            Log::info('OCR: Text received', ['text_length' => strlen($text), 'text_preview' => substr($text, 0, 200)]);
            
            if (empty($text)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No text provided'
                ], 400);
            }
            
            // Simple test - just return success with the text
            return response()->json([
                'success' => true,
                'message' => 'OCR processing complete',
                'reference_number' => 'TEST-REF',
                'matched_products' => [
                    [
                        'id' => 1,
                        'product_name' => 'Test Product 1',
                        'detected_quantity' => 2,
                        'detected_cost' => 50.00
                    ],
                    [
                        'id' => 2,
                        'product_name' => 'Test Product 2', 
                        'detected_quantity' => 1,
                        'detected_cost' => 25.00
                    ]
                ],
                'unmatched_products' => []
            ]);
            
        } catch (\Throwable $e) {
            Log::error('OCR method failed completely', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'OCR processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testOcr(Request $request)
    {
        try {
            Log::info('Test OCR method called', ['request_data' => $request->all()]);
            
            return response()->json([
                'success' => true,
                'message' => 'Test OCR working',
                'data' => $request->all()
            ]);
        } catch (\Throwable $e) {
            Log::error('Test OCR failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function purchasesStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'payment_status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        $purchaseId = DB::table('purchases')->insertGetId([
            'supplier_id' => $validated['supplier_id'],
            'branch_id' => $branchId,
            'reference_number' => $validated['reference_number'],
            'purchase_date' => $validated['purchase_date'],
            'payment_status' => $validated['payment_status'],
            'notes' => $validated['notes'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cashier.purchases.show', ['purchase_id' => $purchaseId])->with('success', 'Purchase created successfully');
    }

    public function purchasesShow($purchase)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $purchase = DB::table('purchases')
            ->where('id', $purchase)
            ->where('branch_id', $branchId)
            ->with(['supplier', 'items'])
            ->first();

        if (! $purchase) {
            abort(404, 'Purchase not found');
        }

        return view('cashier.purchase.show', compact('purchase'));
    }

    public function purchasesMatchProduct(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $barcode = $request->input('barcode');
        $productName = $request->input('product_name');

        $query = Product::query();

        if ($barcode) {
            $query->where('barcode', $barcode);
        }

        if ($productName) {
            $query->where('product_name', 'like', '%'.$productName.'%');
        }

        // Apply branch filtering
        $query->whereHas('stockIns', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });

        $products = $query->limit(10)->get(['id', 'product_name', 'barcode', 'selling_price']);

        return response()->json($products);
    }

    // INVENTORY METHODS
    public function inventoryIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        $query = Product::query();

        // Apply search
        if ($search) {
            $query->where('product_name', 'like', '%'.$search.'%');
        }

        // Apply branch filtering
        $query->whereHas('stockIns', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });

        // Get stock data for each product
        $products = $query->with(['brand', 'category'])
            ->get()
            ->map(function ($product) use ($branchId) {
                $totalStock = DB::table('stock_ins')
                    ->where('product_id', $product->id)
                    ->where('branch_id', $branchId)
                    ->sum('quantity');

                $totalSold = DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sale_items.product_id', $product->id)
                    ->where('sales.branch_id', $branchId)
                    ->sum('quantity');

                $currentStock = $totalStock - $totalSold;

                return (object) [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'brand' => $product->brand->brand_name ?? 'N/A',
                    'category' => $product->category->category_name ?? 'N/A',
                    'current_stock' => $currentStock,
                    'total_sold' => $totalSold,
                    'selling_price' => $product->selling_price,
                    'total_revenue' => $totalSold * $product->selling_price,
                ];
            });

        // Apply sorting
        $sortedProducts = $search ? $products : $products->sortBy([$sortBy => $sortDirection]);

        return view('cashier.inventory.index', compact('sortedProducts', 'sortBy', 'sortDirection'));
    }

    // STOCK IN METHODS
    public function stockInIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $stockIns = StockIn::with(['product', 'purchase'])
            ->where('branch_id', $branchId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cashier.stockin.index', compact('stockIns'));
    }

    public function stockInCreate(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Get purchases for current branch that have available stock
        $purchases = DB::table('purchases')
            ->where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->leftJoin('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select('purchases.*', 'suppliers.supplier_name')
            ->orderBy('purchase_date', 'desc')
            ->get();

        return view('cashier.stockin.create', compact('purchases', 'branchId'));
    }

    public function stockInStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'expiry_date' => 'nullable|date|after:today',
        ]);

        StockIn::create([
            'purchase_id' => $validated['purchase_id'],
            'product_id' => $validated['product_id'],
            'branch_id' => $branchId,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'expiry_date' => $validated['expiry_date'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('cashier.stockin.index')->with('success', 'Stock added successfully');
    }

    public function stockInProductsByPurchase($purchase)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $stockIns = DB::table('stock_ins')
            ->where('purchase_id', $purchase)
            ->where('branch_id', $branchId)
            ->with('product')
            ->get();

        return response()->json($stockIns);
    }

    /**
     * Display refunds for the cashier's branch.
     */
    public function refundsIndex()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Get today's refunds data for the current branch
        $today = Carbon::today();
        $todayRefunds = Refund::whereDate('created_at', $today)
            ->whereHas('sale', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_refunds, COALESCE(SUM(refund_amount), 0) as total_refund_amount, COALESCE(SUM(quantity_refunded), 0) as total_items')
            ->first();

        // Get this month's refunds for the current branch
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlyRefunds = Refund::whereDate('created_at', '>=', $thisMonth)
            ->whereHas('sale', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_refunds, COALESCE(SUM(refund_amount), 0) as total_refund_amount, COALESCE(SUM(quantity_refunded), 0) as total_items')
            ->first();

        // Get recent refunds for the current branch
        $refunds = Refund::with(['sale', 'saleItem.product', 'product', 'cashier'])
            ->whereHas('sale', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cashier.refunds.index', compact(
            'refunds',
            'todayRefunds',
            'monthlyRefunds'
        ));
    }

    /**
     * Store a newly created refund.
     */
    public function refundsStore(Request $request)
    {
        try {
            $user = Auth::user();
            $branchId = $user->branch_id;

            if (! $branchId) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier'], 403);
                }

                return back()->with('error', 'No branch assigned to this cashier.');
            }

            // Debug: Log incoming request data
            \Log::info('Cashier refund request data: '.json_encode($request->all()));

            $validator = Validator::make($request->all(), [
                'sale_id' => 'required|exists:sales,id',
                'sale_item_id' => 'required|exists:sale_items,id',
                'product_id' => 'required|exists:products,id',
                'quantity_refunded' => 'required|integer|min:1',
                'refund_amount' => 'required|numeric|min:0',
                'reason' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                \Log::error('Cashier refund validation failed: '.json_encode($validator->errors()->toArray()));
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
                }

                return back()->withErrors($validator)->withInput();
            }

            // Verify the sale belongs to the cashier's branch
            $sale = \App\Models\Sale::find($request->sale_id);
            if (! $sale || $sale->branch_id != $branchId) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Sale not found or not authorized for this branch'], 403);
                }

                return back()->with('error', 'Sale not found or not authorized for this branch.');
            }

            \Log::info('Validation passed, starting transaction');

            DB::transaction(function () use ($request) {
                \Log::info('Finding sale item: '.$request->sale_item_id);
                $saleItem = \App\Models\SaleItem::find($request->sale_item_id);

                if (! $saleItem) {
                    \Log::error('Sale item not found: '.$request->sale_item_id);
                    throw new \Exception('Sale item not found');
                }

                \Log::info('Sale item found, checking existing refunds');
                // Validate that refund quantity doesn't exceed sold quantity
                $totalRefunded = Refund::where('sale_item_id', $request->sale_item_id)
                    ->where('status', 'approved')
                    ->sum('quantity_refunded');

                \Log::info('Total refunded: '.$totalRefunded.', requested: '.$request->quantity_refunded.', sold: '.$saleItem->quantity);

                if ($totalRefunded + $request->quantity_refunded > $saleItem->quantity) {
                    throw new \Exception('Cannot refund more items than were sold');
                }

                \Log::info('Creating refund record');
                // Create refund record
                $refund = Refund::create([
                    'sale_id' => $request->sale_id,
                    'sale_item_id' => $request->sale_item_id,
                    'product_id' => $request->product_id,
                    'cashier_id' => auth()->id(),
                    'quantity_refunded' => $request->quantity_refunded,
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->reason,
                    'status' => 'approved', // Auto-approve for cashier refunds
                    'notes' => $request->notes,
                ]);

                \Log::info('Refund created with ID: '.$refund->id);

                // Update sale total amount by deducting refund amount
                $sale = \App\Models\Sale::find($request->sale_id);
                if ($sale) {
                    $currentTotal = $sale->total_amount;
                    $newTotal = $currentTotal - $request->refund_amount;

                    $sale->total_amount = max(0, $newTotal);
                    $sale->save();

                    \Log::info('Sale total updated: '.$currentTotal.' -> '.$sale->total_amount.' (Refund: '.$request->refund_amount.')');
                }

                // Update inventory - add back refunded items
                $product = Product::find($request->product_id);
                if ($product) {
                    \Log::info('Updating inventory for product: '.$product->id);

                    // Try to find and update StockIn record (primary method)
                    $stockIn = StockIn::where('product_id', $product->id)
                        ->where('branch_id', auth()->user()->branch_id)
                        ->first();

                    if ($stockIn) {
                        // Reduce sold count (add back to inventory)
                        $newSold = max(0, $stockIn->sold - $request->quantity_refunded);
                        $stockIn->sold = $newSold;
                        $stockIn->save();
                        \Log::info('Updated StockIn sold count: '.$stockIn->sold.' (reduced by '.$request->quantity_refunded.')');
                    } else {
                        \Log::warning('No StockIn record found for product: '.$product->id.', creating new record');

                        // Create new StockIn record if none exists
                        StockIn::create([
                            'product_id' => $product->id,
                            'branch_id' => auth()->user()->branch_id,
                            'quantity' => $request->quantity_refunded,
                            'sold' => 0,
                            'price' => $request->refund_amount / $request->quantity_refunded,
                        ]);
                        \Log::info('Created new StockIn record for refunded items');
                    }

                    // Try to update StockOut record (secondary method)
                    $stockOut = \App\Models\StockOut::where('sale_id', $request->sale_id)
                        ->where('product_id', $request->product_id)
                        ->first();

                    if ($stockOut) {
                        \Log::info('Found stock out record, updating quantity');
                        $newQuantity = max(0, $stockOut->quantity - $request->quantity_refunded);

                        if ($newQuantity <= 0) {
                            $stockOut->delete();
                            \Log::info('StockOut record deleted (fully refunded)');
                        } else {
                            $stockOut->quantity = $newQuantity;
                            $stockOut->save();
                            \Log::info('StockOut quantity updated to: '.$stockOut->quantity);
                        }
                    } else {
                        \Log::info('No StockOut record found - this is normal for some sales');
                    }
                } else {
                    \Log::warning('Product not found: '.$request->product_id);
                }

                \Log::info('Cashier refund transaction completed successfully');
            });

            \Log::info('Returning success response');
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund processed successfully']);
            }

            return redirect()
                ->route('cashier.refunds.index')
                ->with('success', 'Refund processed successfully.');

        } catch (\Exception $e) {
            \Log::error('Cashier refund processing error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
            \Log::error('Stack trace: '.$e->getTraceAsString());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error processing refund: '.$e->getMessage()], 500);
            }

            return back()->with('error', 'Error processing refund: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display credits for the cashier's branch.
     */
    public function creditIndex()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        try {
            // Get today's credits data for the current branch
            $today = Carbon::today();
            $todayCredits = Credit::whereDate('created_at', $today)
                ->where('branch_id', $branchId)
                ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
                ->first();

            // Get this month's credits for the current branch
            $thisMonth = Carbon::now()->startOfMonth();
            $monthlyCredits = Credit::whereDate('created_at', '>=', $thisMonth)
                ->where('branch_id', $branchId)
                ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_credit_amount, COALESCE(SUM(remaining_balance), 0) as total_outstanding')
                ->first();

            // Get recent credits for the current branch
            $credits = Credit::with(['customer', 'cashier'])
                ->where('branch_id', $branchId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('cashier.credit.index', compact(
                'credits',
                'todayCredits',
                'monthlyCredits'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading cashier credits: '.$e->getMessage());

            return back()->with('error', 'Error loading credits: '.$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new credit.
     */
    public function creditCreate()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Get active customers (no branch column on customers table)
        $customers = Customer::where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('cashier.credit.create', compact('customers'));
    }

    /**
     * Store a newly created credit.
     */
    public function creditStore(Request $request)
    {
        try {
            $user = Auth::user();
            $branchId = $user->branch_id;

            if (! $branchId) {
                return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier'], 403);
            }

            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'credit_amount' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Verify customer belongs to the cashier's branch
            $customer = Customer::find($request->customer_id);
            if (! $customer || $customer->branch_id != $branchId) {
                return response()->json(['success' => false, 'message' => 'Customer not found or not authorized for this branch'], 403);
            }

            $credit = Credit::create([
                'customer_id' => $request->customer_id,
                'branch_id' => $branchId,
                'cashier_id' => $user->id,
                'credit_amount' => $request->credit_amount,
                'remaining_balance' => $request->credit_amount,
                'description' => $request->description,
                'status' => 'active',
                'notes' => $request->notes,
            ]);

            return response()->json(['success' => true, 'message' => 'Credit created successfully', 'credit' => $credit]);

        } catch (\Exception $e) {
            \Log::error('Cashier credit creation error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error creating credit: '.$e->getMessage()], 500);
        }
    }

    /**
     * Display expenses for the cashier's branch.
     */
    public function expensesIndex()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        try {
            // Get today's expenses data for the current branch
            $today = Carbon::today();
            $todayExpenses = Expense::whereDate('expense_date', $today)
                ->where('branch_id', $branchId)
                ->selectRaw('COUNT(*) as total_expenses, COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            // Get this month's expenses for the current branch
            $thisMonth = Carbon::now()->startOfMonth();
            $monthlyExpenses = Expense::whereDate('expense_date', '>=', $thisMonth)
                ->where('branch_id', $branchId)
                ->selectRaw('COUNT(*) as total_expenses, COALESCE(SUM(amount), 0) as total_amount')
                ->first();

            // Get recent expenses for the current branch
            $expenses = Expense::with(['category', 'supplier'])
                ->where('branch_id', $branchId)
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Expense categories and suppliers are global (no branch_id columns)
            $categories = ExpenseCategory::query()->orderBy('name')->get();
            $suppliers = Supplier::query()->where('status', 'active')->orderBy('supplier_name')->get();

            return view('cashier.expenses.index', compact(
                'expenses',
                'categories',
                'suppliers',
                'todayExpenses',
                'monthlyExpenses'
            ));
        } catch (\Exception $e) {
            \Log::error('Error loading cashier expenses: '.$e->getMessage());

            return back()->with('error', 'Error loading expenses: '.$e->getMessage());
        }
    }

    /**
     * Show the form for creating a new expense.
     */
    public function expensesCreate()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Expense categories and suppliers are global (no branch_id columns)
        $categories = ExpenseCategory::query()->orderBy('name')->get();
        $suppliers = Supplier::query()->where('status', 'active')->orderBy('supplier_name')->get();

        return view('cashier.expenses.create', compact('categories', 'suppliers'));
    }

    public function expensesEdit(Expense $expense)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        abort_unless((int) $expense->branch_id === (int) $branchId, 403, 'Not authorized for this branch');

        $categories = ExpenseCategory::query()->orderBy('name')->get();
        $suppliers = Supplier::query()->where('status', 'active')->orderBy('supplier_name')->get();

        return view('cashier.expenses.edit', compact('expense', 'categories', 'suppliers'));
    }

    /**
     * Store a newly created expense.
     */
    public function expensesStore(Request $request)
    {
        try {
            $user = Auth::user();
            $branchId = $user->branch_id;

            if (! $branchId) {
                return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier'], 403);
            }

            $validator = Validator::make($request->all(), [
                'expense_category_id' => 'required|exists:expense_categories,id',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'amount' => 'required|numeric|min:0',
                'expense_date' => 'required|date',
                'payment_method' => 'required|string|max:50',
                'description' => 'nullable|string|max:255',
                'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Expense categories and suppliers are global (no branch restrictions here)

            $expenseData = [
                'branch_id' => $branchId,
                'expense_category_id' => $request->expense_category_id,
                'supplier_id' => $request->supplier_id,
                'amount' => $request->amount,
                'expense_date' => $request->expense_date,
                'payment_method' => $request->payment_method,
                'description' => $request->description,
            ];

            // Handle receipt upload if provided
            if ($request->hasFile('receipt')) {
                $receiptPath = $request->file('receipt')->store('expenses/receipts', 'public');
                $expenseData['receipt_path'] = $receiptPath;
            }

            $expense = Expense::create($expenseData);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Expense created successfully', 'expense' => $expense]);
            }

            return redirect()->route('cashier.expenses.index')->with('success', 'Expense created successfully.');

        } catch (\Exception $e) {
            \Log::error('Cashier expense creation error: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error creating expense: '.$e->getMessage()], 500);
            }

            return back()->with('error', 'Error creating expense: '.$e->getMessage())->withInput();
        }
    }

    public function expensesUpdate(Request $request, Expense $expense)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        abort_unless((int) $expense->branch_id === (int) $branchId, 403, 'Not authorized for this branch');

        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('cashier.expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function expensesDestroy(Expense $expense)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        abort_unless((int) $expense->branch_id === (int) $branchId, 403, 'Not authorized for this branch');

        $expense->delete();

        return redirect()->route('cashier.expenses.index')->with('success', 'Expense deleted successfully.');
    }

    /**
     * Display customers for cashier's branch.
     */
    public function customersIndex()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        try {
            $search = request('search');

            $customers = Customer::query()
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
                })
                ->orderBy('full_name')
                ->paginate(20)
                ->withQueryString();

            return view('cashier.customers.index', compact('customers'));
        } catch (\Exception $e) {
            \Log::error('Error loading cashier customers: '.$e->getMessage());

            return back()->with('error', 'Error loading customers: '.$e->getMessage());
        }
    }

    /**
     * Show form for creating a new customer.
     */
    public function customersCreate()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        return view('cashier.customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function customersStore(Request $request)
    {
        try {
            $user = Auth::user();
            $branchId = $user->branch_id;

            if (! $branchId) {
                return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier'], 403);
            }

            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'email' => 'nullable|email|unique:customers,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'max_credit_limit' => 'nullable|numeric|min:0',
                'status' => 'required|in:active,blocked',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $customer = Customer::create([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'max_credit_limit' => $request->max_credit_limit ?? 0,
                'status' => $request->status,
                'created_by' => $user->id,
            ]);

            return response()->json(['success' => true, 'message' => 'Customer created successfully', 'customer' => $customer]);

        } catch (\Exception $e) {
            \Log::error('Cashier customer creation error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error creating customer: '.$e->getMessage()], 500);
        }
    }

    public function customersShow(Customer $customer)
    {
        return view('cashier.customers.show', compact('customer'));
    }

    public function customersEdit(Customer $customer)
    {
        return view('cashier.customers.edit', compact('customer'));
    }

    public function customersUpdate(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email,'.$customer->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'max_credit_limit' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $customer->update([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'max_credit_limit' => $request->max_credit_limit ?? 0,
        ]);

        return response()->json(['success' => true, 'message' => 'Customer updated successfully', 'customer' => $customer]);
    }

    public function customersDestroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
    }

    // POS Methods for Cashier
    public function posLookup(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['error' => 'No branch assigned to this cashier']);
        }

        // Force use MCS branch (ID 3) for product display
        $mcsBranchId = 3;

        $keyword = $request->input('keyword', $request->input('barcode'));
        $mode = $request->input('mode', 'list');

        if ($mode === 'list' && empty($keyword)) {
            // Return all products for MCS branch
            $products = Product::whereHas('stockIns', function ($query) use ($mcsBranchId) {
                $query->where('branch_id', $mcsBranchId);
            })
                ->with(['unitTypes', 'stockIns' => function ($query) use ($mcsBranchId) {
                    $query->where('branch_id', $mcsBranchId);
                }])
                ->get()
                ->map(function ($product) use ($mcsBranchId) {
                    $stockIns = $product->stockIns->where('branch_id', $mcsBranchId);
                    $totalStock = $stockIns->sum('quantity');
                    $price = $stockIns->first()->price ?? 0; // Get price from StockIn

                    $branches = collect([
                        [
                            'branch_id' => $mcsBranchId,
                            'branch_name' => 'MCS',
                            'stock' => $totalStock,
                        ],
                    ]);

                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'barcode' => $product->barcode,
                        'model_number' => $product->model_number,
                        'selling_price' => $price, // Use price from StockIn
                        'total_stock' => $totalStock,
                        'branches' => $branches->toArray(),
                    ];
                });

            return response()->json(['success' => true, 'products' => $products]);
        }

        // Search by barcode or name in MCS branch
        $products = Product::where(function ($query) use ($keyword) {
            $query->where('barcode', $keyword)
                ->orWhere('product_name', 'like', '%'.$keyword.'%')
                ->orWhere('model_number', 'like', '%'.$keyword.'%');
        })
            ->whereHas('stockIns', function ($query) use ($mcsBranchId) {
                $query->where('branch_id', $mcsBranchId);
            })
            ->with(['unitTypes', 'stockIns' => function ($query) use ($mcsBranchId) {
                $query->where('branch_id', $mcsBranchId);
            }])
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['error' => 'Product not found']);
        }

        $product = $products->first();
        $stockIns = $product->stockIns->where('branch_id', $mcsBranchId);
        $totalStock = $stockIns->sum('quantity');
        $price = $stockIns->first()->price ?? 0; // Get price from StockIn

        $branches = collect([
            [
                'branch_id' => $mcsBranchId,
                'branch_name' => 'MCS',
                'stock' => $totalStock,
            ],
        ]);

        return response()->json([
            'success' => true,
            'products' => [[
                'id' => $product->id,
                'product_name' => $product->product_name,
                'barcode' => $product->barcode,
                'model_number' => $product->model_number,
                'selling_price' => $price, // Use price from StockIn
                'total_stock' => $totalStock,
                'branches' => $branches->toArray(),
            ]]
        ]);
    }

    public function posStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier']);
        }

        try {
            DB::beginTransaction();

            $total = $request->input('total');
            $paymentMethod = $request->input('payment_method');
            $customerName = $request->input('customer_name');
            $creditDueDate = $request->input('credit_due_date');
            $creditNotes = $request->input('credit_notes');
            $items = $request->input('products');

            if (empty($items) || !is_array($items)) {
                return response()->json(['success' => false, 'message' => 'No items provided for sale']);
            }

            // Create sale record
            $sale = Sale::create([
                'branch_id' => $branchId,
                'cashier_id' => $user->id,
                'total_amount' => $total,
                'payment_method' => $paymentMethod,
                'customer_name' => $customerName,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create sale items and update stock
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Deduct stock
                $stockIn = StockIn::where('product_id', $item['id'])
                    ->where('branch_id', 3) // MCS branch
                    ->where('quantity', '>', 0)
                    ->first();

                if ($stockIn) {
                    $stockIn->quantity -= $item['quantity'];
                    $stockIn->save();
                }
            }

            // Create credit record if needed
            if ($paymentMethod === 'credit' && $customerName) {
                Credit::create([
                    'branch_id' => $branchId,
                    'customer_name' => $customerName,
                    'sale_id' => $sale->id,
                    'amount' => $total,
                    'due_date' => $creditDueDate,
                    'status' => 'pending',
                    'notes' => $creditNotes,
                ]);
            }

            DB::commit();

            // Generate receipt URL if cash payment
            $receiptUrl = null;
            $autoReceipt = $paymentMethod === 'cash';

            if ($autoReceipt) {
                $receiptUrl = route('cashier.pos.receipt', $sale->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id,
                'auto_receipt' => $autoReceipt,
                'receipt_url' => $receiptUrl,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cashier POS store error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error processing sale: '.$e->getMessage()]);
        }
    }

    /**
     * Get credit limits data for the cashier's branch.
     */
    public function creditLimitsData()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'No branch assigned']);
        }

        try {
            // Get credit data grouped by customer for the current branch
            $creditsByCustomer = DB::table('credits as c')
                ->join('customers as cust', 'c.customer_id', '=', 'cust.id')
                ->where('c.branch_id', $branchId)
                ->selectRaw('
                    cust.id as customer_id,
                    cust.full_name as customer_name,
                    COUNT(c.id) as total_credits,
                    COALESCE(SUM(c.credit_amount), 0) as total_credit_limit,
                    COALESCE(SUM(c.paid_amount), 0) as total_paid,
                    COALESCE(SUM(c.remaining_balance), 0) as total_remaining,
                    COALESCE(cust.max_credit_limit, 0) as max_credit_limit
                ')
                ->groupBy('cust.id', 'cust.full_name', 'cust.max_credit_limit')
                ->get();

            return response()->json([
                'success' => true,
                'creditsByCustomer' => $creditsByCustomer
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading credit limits data: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading credit limits data']);
        }
    }

    /**
     * Update credit limits for customers.
     */
    public function updateCreditLimits(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'No branch assigned']);
        }

        try {
            $validated = $request->validate([
                'customers' => 'required|array',
                'customers.*.customer_id' => 'required|exists:customers,id',
                'customers.*.max_credit_limit' => 'required|numeric|min:0'
            ]);

            DB::beginTransaction();

            foreach ($validated['customers'] as $customerData) {
                // Update max_credit_limit in customers table
                DB::table('customers')
                    ->where('id', $customerData['customer_id'])
                    ->update(['max_credit_limit' => $customerData['max_credit_limit']]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Credit limits updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating credit limits: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error updating credit limits']);
        }
    }

    public function receipt($saleId)
    {
        $sale = Sale::with('saleItems.product')->findOrFail($saleId);
        
        // Simple barcode generation using the sale ID
        $barcode = $sale->id;
        
        // For cashier receipts, no group ID (null) and no related sales
        $receiptGroupId = null;
        $relatedSales = collect(); // Empty collection for individual receipts
        
        return view('cashier.sales.receipt', compact('sale', 'barcode', 'receiptGroupId', 'relatedSales'));
    }

    /**
     * Store a new supplier.
     */
    public function supplierStore(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255|unique:suppliers,supplier_name',
            'contact_person' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        try {
            $supplier = Supplier::create([
                'supplier_name' => $validated['supplier_name'],
                'contact_person' => $validated['contact_person'] ?? null,
                'phone' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'active',
            ]);

            return response()->json([
                'id' => $supplier->id,
                'supplier_name' => $supplier->supplier_name,
                'message' => 'Supplier added successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating supplier: '.$e->getMessage());
            return response()->json([
                'message' => 'Error creating supplier. Please try again.'
            ], 500);
        }
    }
}
