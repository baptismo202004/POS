<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Support\Access;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\UnitType;
use App\Models\Supplier;

class CashierDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $today = Carbon::today();
        $start = $today->copy()->subDays(6);

        // Get branch info
        $branch = DB::table('branches')->where('id', $branchId)->first();

        // Today's KPIs (branch-scoped)
        $todaySales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $todayTransactions = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->count();

        $todayExpenses = DB::table('expenses')
            ->where('branch_id', $branchId)
            ->whereDate('expense_date', $today)
            ->sum('amount');

        // Low stock alerts for this branch
        $lowStockQuery = "
            SELECT DISTINCT p.id
            FROM products p
            LEFT JOIN (
                SELECT product_id, branch_id, SUM(quantity) as total_in
                FROM stock_ins
                GROUP BY product_id, branch_id
            ) si ON p.id = si.product_id
            WHERE COALESCE(si.total_in, 0) <= COALESCE(p.low_stock_threshold, 10) AND COALESCE(si.total_in, 0) > 0
        ";
        $lowStockCount = count(DB::select($lowStockQuery));

        // Recent sales (last 5)
        $recentSales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'total_amount', 'created_at']);

        // Top products today (branch-scoped)
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.branch_id', $branchId)
            ->whereDate('sales.created_at', $today)
            ->selectRaw('products.product_name, SUM(sale_items.quantity) as total_qty, SUM(sale_items.subtotal) as revenue')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();

        // Chart data for last 7 days (branch-scoped)
        $labels = [];
        $salesData = [];
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $today->copy()->addDay());
        
        foreach ($period as $day) {
            $dateStr = $day->format('Y-m-d');
            $labels[] = $day->format('D');
            
            $daySales = DB::table('sales')
                ->where('branch_id', $branchId)
                ->whereDate('created_at', $dateStr)
                ->sum('total_amount');
            
            $salesData[] = (float) $daySales;
        }

        $permissions = Access::getPermissions($user);
        $modules = Config::get('rbac.modules');

        return view('cashier.dashboard', compact(
            'branch',
            'todaySales',
            'todayTransactions',
            'todayExpenses',
            'lowStockCount',
            'recentSales',
            'topProducts',
            'labels',
            'salesData',
            'permissions',
            'modules'
        ));
    }

    public function products(Request $request)
    {
        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        if (!in_array($sortBy, ['id', 'product_name', 'status'])) {
            $sortBy = 'id';
        }

        $productsQuery = \App\Models\Product::with(['brand', 'category', 'productType', 'unitTypes']);

        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%")
                      ->orWhereHas('brand', function ($q) use ($search) {
                          $q->where('brand_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('category_name', 'like', "%{$search}%");
                      });
            });
        }

        $products = $productsQuery->orderBy($sortBy, $sortDirection)->paginate(15);

        if ($request->ajax()) {
            return view('SuperAdmin.products._product_table', compact('products'))->render();
        }

        return view('cashier.products.index', [
            'products' => $products->appends($request->query()),
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
    }

    public function createProduct()
    {
        return view('SuperAdmin.products.productList', [
            'brands'       => \App\Models\Brand::where('status', 'active')->get(),
            'categories'   => \App\Models\Category::where('status', 'active')->get(),
            'productTypes' => \App\Models\ProductType::all(),
            'unitTypes'    => \App\Models\UnitType::all(),
            'branches'     => \App\Models\Branch::all(),
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'barcode' => 'required|string|unique:products,barcode',
            'unit_type_ids' => 'required|array|min:1',
            'unit_type_ids.*' => 'exists:unit_types,id',
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'product_type_id' => 'nullable',
            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'serial_number' => 'required_if:product_type_id,1|string|unique:product_serials,serial_number',
            'branch_id' => 'required_if:product_type_id,1|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($request, $validated) {
                // Check if product type is electronic
                $isElectronic = false;
                if (!empty($validated['product_type_id'])) {
                    $productType = \App\Models\ProductType::find($validated['product_type_id']);
                    $isElectronic = $productType && $productType->is_electronic;
                }

                if ($isElectronic) {
                    // For electronic products, store in product_serials table
                    $productData = [
                        'product_name' => $validated['product_name'],
                        'barcode' => $validated['barcode'],
                        'brand_id' => $validated['brand_id'] ?? null,
                        'category_id' => $validated['category_id'] ?? null,
                        'product_type_id' => $validated['product_type_id'],
                        'model_number' => $validated['model_number'] ?? null,
                        'warranty_type' => $validated['warranty_type'],
                        'warranty_coverage_months' => $validated['warranty_coverage_months'] ?? null,
                        'voltage_specs' => $validated['voltage_specs'] ?? null,
                        'status' => $validated['status'],
                    ];

                    if (!empty($validated['brand_id']) && !is_numeric($validated['brand_id'])) {
                        $b = \App\Models\Brand::create(['brand_name' => $validated['brand_id'], 'status' => 'active']);
                        $productData['brand_id'] = $b->id;
                    }

                    if (!empty($validated['category_id']) && !is_numeric($validated['category_id'])) {
                        $c = \App\Models\Category::create(['category_name' => $validated['category_id'], 'status' => 'active']);
                        $productData['category_id'] = $c->id;
                    }

                    if ($request->hasFile('image')) {
                        $productData['image'] = $request->file('image')->store('products', 'public');
                    }

                    $product = \App\Models\Product::create($productData);
                    $product->unitTypes()->sync($validated['unit_type_ids']);

                    // Create product serial record
                    \App\Models\ProductSerial::create([
                        'product_id' => $product->id,
                        'branch_id' => $validated['branch_id'],
                        'serial_number' => $validated['serial_number'],
                        'status' => 'in_stock',
                        'warranty_expiry_date' => $this->calculateWarrantyExpiry($validated['warranty_type'], $validated['warranty_coverage_months'] ?? null),
                    ]);

                } else {
                    // For non-electronic products, store normally
                    $productData = [
                        'product_name' => $validated['product_name'],
                        'barcode' => $validated['barcode'],
                        'brand_id' => $validated['brand_id'] ?? null,
                        'category_id' => $validated['category_id'] ?? null,
                        'product_type_id' => $validated['product_type_id'] ?? null,
                        'model_number' => $validated['model_number'] ?? null,
                        'warranty_type' => $validated['warranty_type'],
                        'warranty_coverage_months' => $validated['warranty_coverage_months'] ?? null,
                        'voltage_specs' => $validated['voltage_specs'] ?? null,
                        'status' => $validated['status'],
                    ];

                    if (!empty($validated['brand_id']) && !is_numeric($validated['brand_id'])) {
                        $b = \App\Models\Brand::create(['brand_name' => $validated['brand_id'], 'status' => 'active']);
                        $productData['brand_id'] = $b->id;
                    }

                    if (!empty($validated['category_id']) && !is_numeric($validated['category_id'])) {
                        $c = \App\Models\Category::create(['category_name' => $validated['category_id'], 'status' => 'active']);
                        $productData['category_id'] = $c->id;
                    }

                    if ($request->hasFile('image')) {
                        $productData['image'] = $request->file('image')->store('products', 'public');
                    }

                    $product = \App\Models\Product::create($productData);
                    $product->unitTypes()->sync($validated['unit_type_ids']);
                }
            });

            return response()->json(['success' => true, 'message' => 'Product created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating product: ' . $e->getMessage()]);
        }
    }

    private function calculateWarrantyExpiry($warrantyType, $coverageMonths)
    {
        if ($warrantyType === 'none' || !$coverageMonths) {
            return null;
        }
        return Carbon::now()->addMonths($coverageMonths);
    }

    public function showProduct($id)
    {
        $product = \App\Models\Product::with(['brand', 'category', 'productType', 'unitTypes'])->findOrFail($id);
        return response()->json(['product' => $product]);
    }

    public function editProduct($id)
    {
        $product = \App\Models\Product::with(['brand', 'category', 'productType', 'unitTypes'])->findOrFail($id);
        
        return view('SuperAdmin.products.productList', [
            'product'      => $product,
            'brands'       => \App\Models\Brand::where('status', 'active')->get(),
            'categories'   => \App\Models\Category::where('status', 'active')->get(),
            'productTypes' => \App\Models\ProductType::all(),
            'unitTypes'    => \App\Models\UnitType::all(),
            'branches'     => \App\Models\Branch::all(),
        ]);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = \App\Models\Product::findOrFail($id);
        
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_name' => 'required|string|max:255|unique:products,product_name,' . $id,
            'barcode' => 'required|string|unique:products,barcode,' . $id,
            'unit_type_ids' => 'required|array|min:1',
            'unit_type_ids.*' => 'exists:unit_types,id',
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'product_type_id' => 'nullable',
            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($request, $validated, $product) {
                $productData = [
                    'product_name' => $validated['product_name'],
                    'barcode' => $validated['barcode'],
                    'brand_id' => $validated['brand_id'] ?? null,
                    'category_id' => $validated['category_id'] ?? null,
                    'product_type_id' => $validated['product_type_id'] ?? null,
                    'model_number' => $validated['model_number'] ?? null,
                    'warranty_type' => $validated['warranty_type'],
                    'warranty_coverage_months' => $validated['warranty_coverage_months'] ?? null,
                    'voltage_specs' => $validated['voltage_specs'] ?? null,
                    'status' => $validated['status'],
                ];

                if (!empty($validated['brand_id']) && !is_numeric($validated['brand_id'])) {
                    $b = \App\Models\Brand::create(['brand_name' => $validated['brand_id'], 'status' => 'active']);
                    $productData['brand_id'] = $b->id;
                }

                if (!empty($validated['category_id']) && !is_numeric($validated['category_id'])) {
                    $c = \App\Models\Category::create(['category_name' => $validated['category_id'], 'status' => 'active']);
                    $productData['category_id'] = $c->id;
                }

                if ($request->hasFile('image')) {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $productData['image'] = $request->file('image')->store('products', 'public');
                }

                $product->update($productData);
                $product->unitTypes()->sync($validated['unit_type_ids']);
            });

            return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating product: ' . $e->getMessage()]);
        }
    }

    public function destroyProduct($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        
        try {
            DB::transaction(function () use ($product) {
                // Delete related records
                $product->unitTypes()->detach();
                
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $product->delete();
            });

            return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting product: ' . $e->getMessage()]);
        }
    }

    // Product Category Methods
    public function categories()
    {
        $categories = \App\Models\Category::latest()->get();
        return view('SuperAdmin.categories.index', compact('categories'));
    }

    public function purchasesIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $purchases = Purchase::with(['items.product'])
            ->where('branch_id', $branchId)
            ->latest('purchase_date')
            ->paginate(15);

        return view('cashier.purchase.index', compact('purchases'));
    }

    public function purchasesCreate(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $products = Product::where('status', 'active')->get();
        $unit_types = UnitType::all();
        $suppliers = Supplier::where('status', 'active')->get();

        return view('cashier.purchase.create', compact('products', 'unit_types', 'suppliers'));
    }

    public function purchasesStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.is_new' => 'nullable|boolean',
            'items.*.product_id' => 'required_if:items.*.is_new,null|exists:products,id',
            'items.*.product_name' => 'required_if:items.*.is_new,1|string|max:255|unique:products,product_name',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_type_id' => 'required|exists:unit_types,id',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $branchId) {
            $totalCost = 0;
            $purchaseItemsData = [];

            foreach ($validated['items'] as $item) {
                $productId = null;
                if (!empty($item['is_new'])) {
                    $newProduct = Product::create([
                        'product_name' => $item['product_name'],
                        'barcode' => 'BC-' . uniqid(),
                        'status' => 'active',
                        'tracking_type' => 'none',
                        'warranty_type' => 'none',
                    ]);
                    $newProduct->unitTypes()->sync([$item['unit_type_id']]);
                    $productId = $newProduct->id;
                } else {
                    $productId = $item['product_id'];
                }

                $subtotal = $item['quantity'] * $item['cost'];
                $totalCost += $subtotal;

                $purchaseItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'unit_type_id' => $item['unit_type_id'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $subtotal,
                ];
            }

            $purchase = Purchase::create([
                'branch_id' => $branchId,
                'purchase_date' => $validated['purchase_date'],
                'supplier_id' => $validated['supplier_id'],
                'total_cost' => $totalCost,
                'payment_status' => $validated['payment_status'],
                'reference_number' => $validated['reference_number'] ?? null,
            ]);

            $purchase->items()->createMany($purchaseItemsData);
        });

        return redirect()->route('cashier.purchases.index')->with('success', 'Purchase created successfully.');
    }

    public function purchasesShow(Purchase $purchase)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ((int) $purchase->branch_id !== (int) $branchId) {
            abort(403, 'Unauthorized purchase access');
        }

        $purchase->load('items.product', 'items.unitType');
        return view('cashier.purchase.show', compact('purchase'));
    }

    public function purchasesMatchProduct(Request $request)
    {
        $text = $request->input('text', '');
        $lines = explode("\n", $text);
        $matchedProducts = [];
        $unmatchedProducts = [];
        $referenceNumber = null;

        foreach ($lines as $line) {
            if (preg_match('/(?:REFERENCE NO|REF NO|REFERENCE):\s*([A-Z0-9-]+)/i', $line, $matches)) {
                $referenceNumber = trim($matches[1]);
                break;
            }
        }

        foreach ($lines as $line) {
            if (preg_match('/^(\d+)\s+(.*?)\s+(\d+\.\d{2})\s+(\d+\.\d{2})/', $line, $matches)) {
                $quantity = (int) $matches[1];
                $productName = trim($matches[2]);
                $cost = (float) str_replace(',', '', $matches[3]);

                $product = Product::where('product_name', 'like', '%' . $productName . '%')->first();

                if ($product) {
                    $matchedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                } else {
                    $unmatchedProducts[] = [
                        'name' => $productName,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                }
            }
        }

        return response()->json([
            'reference_number' => $referenceNumber,
            'products' => $matchedProducts,
            'unmatched_products' => $unmatchedProducts,
        ]);
    }

    public function inventoryIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        if (!in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'], true)) {
            $sortBy = 'product_name';
        }

        $productsQuery = Product::with(['brand', 'category']);

        if ($search) {
            $productsQuery->where('product_name', 'like', "%{$search}%")
                ->orWhereHas('brand', function ($q) use ($search) {
                    $q->where('brand_name', 'like', "%{$search}%");
                })
                ->orWhereHas('category', function ($q) use ($search) {
                    $q->where('category_name', 'like', "%{$search}%");
                });
        }

        $products = $productsQuery->get();

        $stockInByProduct = DB::table('stock_ins')
            ->where('branch_id', $branchId)
            ->groupBy('product_id')
            ->selectRaw('product_id, COALESCE(SUM(quantity),0) as total_in')
            ->pluck('total_in', 'product_id');

        $stockOutByProduct = DB::table('stock_outs')
            ->where('branch_id', $branchId)
            ->groupBy('product_id')
            ->selectRaw('product_id, COALESCE(SUM(quantity),0) as total_out')
            ->pluck('total_out', 'product_id');

        $salesByProduct = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.branch_id', $branchId)
            ->groupBy('sale_items.product_id')
            ->selectRaw('sale_items.product_id, COALESCE(SUM(sale_items.quantity),0) as total_sold, COALESCE(SUM(sale_items.subtotal),0) as total_revenue')
            ->get()
            ->keyBy('product_id');

        foreach ($products as $product) {
            $in = (float) ($stockInByProduct[$product->id] ?? 0);
            $out = (float) ($stockOutByProduct[$product->id] ?? 0);
            $product->current_stock = $in - $out;

            $sales = $salesByProduct->get($product->id);
            $product->total_sold = (int) ($sales->total_sold ?? 0);
            $product->total_revenue = (float) ($sales->total_revenue ?? 0);
        }

        $products = $products->sortBy([
            [$sortBy, $sortDirection],
        ]);

        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 15),
            $products->count(),
            15,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return view('cashier.inventory.index', [
            'products' => $products->appends($request->query()),
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function stockInIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $query = StockIn::with(['product', 'purchase'])
            ->where('branch_id', $branchId);

        if ($request->has('sort')) {
            $direction = $request->get('direction', 'asc');
            switch ($request->get('sort')) {
                case 'product':
                    $query->join('products', 'stock_ins.product_id', '=', 'products.id')
                        ->where('stock_ins.branch_id', $branchId)
                        ->orderBy('products.product_name', $direction);
                    break;
                default:
                    $query->orderBy('stock_ins.created_at', 'desc');
            }
        } else {
            $query->orderBy('stock_ins.created_at', 'desc');
        }

        $stockIns = $query->paginate(15);

        return view('cashier.stockin.index', compact('stockIns'));
    }

    public function stockInCreate(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $purchases = Purchase::with('items.product.unitTypes')
            ->where('branch_id', $branchId)
            ->orderByDesc('purchase_date')
            ->get();

        return view('cashier.stockin.create', compact('purchases'));
    }

    public function stockInProductsByPurchase(Purchase $purchase)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ((int) $purchase->branch_id !== (int) $branchId) {
            abort(403, 'Unauthorized purchase access');
        }

        $items = $purchase->items()->with('product.unitTypes')->get();
        return response()->json($items);
    }

    public function stockInStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_type_id' => 'nullable|exists:unit_types,id',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $purchase = Purchase::with('items.product')
            ->where('id', $validated['purchase_id'])
            ->where('branch_id', $branchId)
            ->firstOrFail();

        $stockInCount = 0;
        $errorMessages = [];

        $groupedItems = collect($validated['items'])->groupBy('product_id');

        foreach ($groupedItems as $productId => $items) {
            $purchaseItem = $purchase->items->firstWhere('product_id', (int) $productId);

            if (!$purchaseItem) {
                return back()->withInput()->with('error', 'Invalid product found in the stock-in request.');
            }

            $totalStockedIn = StockIn::where('purchase_id', $validated['purchase_id'])
                ->where('product_id', $productId)
                ->where('branch_id', $branchId)
                ->sum('quantity');

            $availableQuantity = $purchaseItem->quantity - $totalStockedIn;
            $currentStockInQuantity = $items->sum('quantity');

            if ($currentStockInQuantity > $availableQuantity) {
                $productName = $purchaseItem->product->product_name;
                $errorMessages[] = "Cannot stock in {$currentStockInQuantity} for {$productName}. Only {$availableQuantity} remaining.";
                continue;
            }

            foreach ($items as $item) {
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                StockIn::create([
                    'product_id' => $item['product_id'],
                    'branch_id' => $branchId,
                    'purchase_id' => $validated['purchase_id'],
                    'unit_type_id' => !empty($item['unit_type_id']) ? $item['unit_type_id'] : null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $stockInCount++;
            }
        }

        if (!empty($errorMessages)) {
            return back()->withInput()->with('error', implode('<br>', $errorMessages));
        }

        if ($stockInCount > 0) {
            return redirect()->route('cashier.stockin.index')
                ->with('success', $stockInCount . ' item(s) have been successfully stocked in.');
        }

        return back()->withInput()->with('error', 'No items were stocked in. Please provide a quantity for at least one item.');
    }

    public function createCategory()
    {
        return view('SuperAdmin.categories.index');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name',
            'status' => 'required|in:active,inactive'
        ]);

        \App\Models\Category::create(['category_name' => $request->category_name, 'status' => $request->status]);

        return back()->with('success', 'Category added');
    }

    public function editCategory(\App\Models\Category $category)
    {
        return view('SuperAdmin.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, \App\Models\Category $category)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name,' . $category->id,
            'status' => 'required|in:active,inactive'
        ]);

        $category->update(['category_name' => $request->category_name, 'status' => $request->status]);

        return redirect()->route('cashier.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroyCategory(\App\Models\Category $category)
    {
        try {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting category: ' . $e->getMessage()]);
        }
    }

    public function bulkDeleteCategories(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id'
        ]);

        try {
            \App\Models\Category::whereIn('id', $request->ids)->delete();
            return response()->json(['success' => true, 'message' => 'Categories deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting categories: ' . $e->getMessage()]);
        }
    }

    public function chartData(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            return response()->json(['error' => 'No branch assigned'], 403);
        }

        $type = $request->query('type', 'sales');
        $end = Carbon::today();
        $start = $end->copy()->subDays(6);

        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end->copy()->addDay());
        $labels = [];
        $sales = [];
        
        foreach ($period as $day) {
            $dateStr = $day->format('Y-m-d');
            $labels[] = $day->format('D');
            
            $daySales = DB::table('sales')
                ->where('branch_id', $branchId)
                ->whereDate('created_at', $dateStr)
                ->sum('total_amount');
            
            $sales[] = (float) $daySales;
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $sales,
            'data' => $sales, // for compatibility
        ]);
    }

    public function getLowStock(Request $request)
    {
        ob_start(); // Prevent any output
        
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (!$branchId) {
            ob_end_clean();
            return response()->json(['error' => 'No branch assigned'], 403);
        }

        try {
            \Log::info('getLowStock method started.');
            // Get products with low stock (<= 10 items)
            $query = "
                SELECT
                    p.product_name,
                    COALESCE(si.total_in, 0) as current_stock,
                    b.branch_name,
                    'units' as unit_name
                FROM
                    products p
                LEFT JOIN
                    (SELECT product_id, branch_id, SUM(quantity) as total_in FROM stock_ins GROUP BY product_id, branch_id) as si ON p.id = si.product_id
                LEFT JOIN branches b ON si.branch_id = b.id
                WHERE
                    COALESCE(si.total_in, 0) <= COALESCE(p.low_stock_threshold, 10) AND COALESCE(si.total_in, 0) > 0
                ORDER BY
                    si.total_in ASC
            ";

            \Log::info('Executing low stock items query.', ['branch_id' => $branchId]);
            $lowStockItems = DB::select($query);
            \Log::info('Low stock items query executed successfully.', ['count' => count($lowStockItems)]);

            ob_end_clean();
            return response()->json([
                'success' => true,
                'lowStockItems' => $lowStockItems
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in getLowStock', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            ob_end_clean();
            \Log::error('Error in getLowStock: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch low stock items'
            ], 500);
        }
    }
}
