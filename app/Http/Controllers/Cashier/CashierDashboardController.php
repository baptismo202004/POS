<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\ProductType;
use App\Models\Purchase;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockIn;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\UnitType;
use App\Services\InventoryService;
use App\Support\Access;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CashierDashboardController extends Controller
{
    /**
     * Display the cashier dashboard.
     */
    public function index(Request $request)
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

        // Get today's credit payments (revenue from credit) for the current branch
        $todayCreditPaymentsQuery = CreditPayment::with(['credit.customer'])
            ->whereDate('created_at', $today)
            ->whereHas('credit', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->orderBy('created_at', 'desc');

        $todayCreditRevenue = (clone $todayCreditPaymentsQuery)->sum('payment_amount');
        $todayCreditPayments = $todayCreditPaymentsQuery->take(10)->get();

        // Cash on hand today: total cash sales for today (excluding credit)
        $cashOnHandToday = DB::table('sales')
            ->where('branch_id', $branchId)
            ->whereDate('created_at', $today)
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        // Get top products for the current branch
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
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
            'stock_management' => ['label' => 'Stock Management', 'icon' => 'warehouse'],
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
            'todayCreditRevenue',
            'todayCreditPayments',
            'cashOnHandToday',
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
            return response()->json([
                'success' => false,
                'message' => 'No branch assigned to this cashier',
            ], 403);
        }

        try {
            $products = Product::query()
                ->whereNotNull('low_stock_threshold')
                ->where('low_stock_threshold', '>', 0)
                ->whereExists(function ($exists) use ($branchId) {
                    $exists->select(DB::raw(1))
                        ->from('branch_stocks')
                        ->whereColumn('branch_stocks.product_id', 'products.id')
                        ->where('branch_stocks.branch_id', (int) $branchId);
                })
                ->get(['id', 'product_name', 'low_stock_threshold']);

            $inventory = app(InventoryService::class);

            // Map to structure expected by dashboard JS: product_name, branch_name, current_stock, unit_name
            $lowStockItems = $products->map(function ($product) use ($branchId, $inventory) {
                $current = (float) $inventory->availableStockBase((int) $product->id, (int) $branchId);
                if ($current > (float) ($product->low_stock_threshold ?? 0)) {
                    return null;
                }

                return [
                    'product_name' => $product->product_name,
                    'branch_name' => (string) (DB::table('branches')->where('id', (int) $branchId)->value('branch_name') ?? 'Branch'),
                    'current_stock' => $current,
                    'unit_name' => 'unit',
                ];
            })->filter()->values();

            return response()->json([
                'success' => true,
                'lowStockItems' => $lowStockItems,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading low stock items for cashier dashboard: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load low stock items',
            ], 500);
        }
    }

    /**
     * Dashboard alerts JSON — branch-scoped counts for the cashier's branch.
     */
    public function dashboardAlerts(): \Illuminate\Http\JsonResponse
    {
        $branchId = (int) Auth::user()->branch_id;
        $today = Carbon::today();

        $outOfStock = DB::table('branch_stocks')
            ->where('branch_id', $branchId)
            ->where('quantity_base', '<=', 0)
            ->count();

        $lowStock = DB::table('branch_stocks')
            ->join('products', 'branch_stocks.product_id', '=', 'products.id')
            ->where('branch_stocks.branch_id', $branchId)
            ->where('branch_stocks.quantity_base', '>', 0)
            ->whereRaw('branch_stocks.quantity_base <= COALESCE(NULLIF(products.low_stock_threshold,0), 10)')
            ->count();

        $pendingRefunds = DB::table('refunds')
            ->where('status', 'pending')
            ->whereExists(fn ($q) => $q->from('sales')
                ->whereColumn('sales.id', 'refunds.sale_id')
                ->where('sales.branch_id', $branchId))
            ->count();

        $pendingSales = DB::table('sales')
            ->where('branch_id', $branchId)
            ->where('status', 'pending')
            ->count();

        $procurementNeeds = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.branch_id', $branchId)
            ->where('sale_items.is_for_procurement', true)
            ->where('sale_items.pending_qty', '>', 0)
            ->distinct('sale_items.product_id')
            ->count('sale_items.product_id');

        return response()->json([
            'outOfStock' => $outOfStock,
            'lowStock' => $lowStock,
            'pendingRefunds' => $pendingRefunds,
            'pendingSales' => $pendingSales,
            'procurementNeeds' => $procurementNeeds,
            'total' => $outOfStock + $lowStock + $pendingRefunds + $pendingSales + $procurementNeeds,
        ]);
    }

    /**
     * Procurement needs — items ordered but unfulfilled, scoped to cashier's branch.
     */
    public function procurement(Request $request): \Illuminate\View\View
    {
        $branchId = (int) Auth::user()->branch_id;
        $search = $request->query('search');

        $latestSupplier = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->selectRaw('purchase_items.product_id, suppliers.supplier_name, MAX(purchases.id) as max_purchase_id')
            ->groupBy('purchase_items.product_id', 'suppliers.supplier_name');

        $pendingQuery = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoinSub($latestSupplier, 'latest_supplier', 'latest_supplier.product_id', '=', 'products.id')
            ->where('sales.branch_id', $branchId)
            ->where('sale_items.is_for_procurement', true)
            ->where('sale_items.pending_qty', '>', 0)
            ->selectRaw('
                products.id AS product_id,
                products.product_name, products.barcode, products.model_number,
                COALESCE(brands.brand_name,"—") AS brand_name,
                COALESCE(categories.category_name,"—") AS category_name,
                COALESCE(latest_supplier.supplier_name,"—") AS supplier_name,
                sales.branch_id,
                COALESCE(branches.branch_name,"—") AS branch_name,
                SUM(sale_items.pending_qty) AS total_pending_qty,
                COUNT(DISTINCT sale_items.sale_id) AS order_count,
                MIN(sales.created_at) AS oldest_order_date,
                MAX(sales.created_at) AS latest_order_date
            ')
            ->groupBy(
                'products.id', 'products.product_name', 'products.barcode',
                'products.model_number', 'brands.brand_name', 'categories.category_name',
                'latest_supplier.supplier_name', 'sales.branch_id', 'branches.branch_name'
            );

        if ($search) {
            $pendingQuery->where(fn ($q) => $q
                ->where('products.product_name', 'like', "%{$search}%")
                ->orWhere('products.barcode', 'like', "%{$search}%")
                ->orWhere('latest_supplier.supplier_name', 'like', "%{$search}%"));
        }

        $pendingItems = $pendingQuery->orderByDesc('total_pending_qty')->get();

        $stockMap = DB::table('branch_stocks')
            ->where('branch_id', $branchId)
            ->whereIn('product_id', $pendingItems->pluck('product_id')->unique())
            ->get()->keyBy('product_id');

        $pendingItems = $pendingItems->map(function ($row) use ($stockMap) {
            $row->current_stock = (float) ($stockMap[$row->product_id]->quantity_base ?? 0);
            $row->still_needed = max(0, $row->total_pending_qty - $row->current_stock);
            $row->can_fulfill = min($row->total_pending_qty, $row->current_stock);

            return $row;
        });

        $totalPendingProducts = $pendingItems->count();
        $totalPendingUnits = $pendingItems->sum('total_pending_qty');
        $totalStillNeeded = $pendingItems->sum('still_needed');
        $totalOrders = $pendingItems->sum('order_count');
        $fullyBlocked = $pendingItems->where('current_stock', '<=', 0)->count();
        $partiallyFulfillable = $pendingItems->where('current_stock', '>', 0)->where('still_needed', '>', 0)->count();

        // Reuse stock stats scoped to this branch
        $stockStats = app(\App\Http\Controllers\SuperAdmin\StockManagementController::class)
            ->calculateStockStatisticsPublic($branchId);

        return view('cashier.procurement', compact(
            'pendingItems', 'search',
            'totalPendingProducts', 'totalPendingUnits', 'totalStillNeeded',
            'totalOrders', 'fullyBlocked', 'partiallyFulfillable',
            'stockStats'
        ));
    }

    /**
     * Product lifecycle — reuses SuperAdmin lifecycle scoped to cashier's branch.
     */
    public function productLifecycle(\App\Models\Product $product): \Illuminate\View\View
    {
        view()->share('backIndexRoute', route('cashier.products.index'));
        view()->share('backShowRoute', route('cashier.products.show', $product));

        return app(\App\Http\Controllers\SuperAdmin\ProductController::class)->lifecycle($product);
    }

    /**
     * Purchase lifecycle — reuses SuperAdmin lifecycle.
     */
    public function purchaseLifecycle(\App\Models\Purchase $purchase): \Illuminate\View\View
    {
        view()->share('backIndexRoute', route('cashier.purchases.index'));
        view()->share('backShowRoute', route('cashier.purchases.show', $purchase));

        return app(\App\Http\Controllers\SuperAdmin\PurchaseController::class)->lifecycle($purchase);
    }

    // SALES METHODS
    public function salesIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Use same parameter names as the Blade view and default to oldest first
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');
        $dateFrom = $request->get('date_from');
        $search = $request->get('search');

        $query = Sale::where('branch_id', $branchId)
            ->with(['saleItems.product', 'customer']);

        // If a date is selected, show only sales from that specific date
        if (! empty($dateFrom)) {
            $query->whereDate('created_at', $dateFrom);
        }

        // If a search term is provided, filter by receipt # (id) or customer name
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $allowedSorts = ['id', 'total_amount', 'created_at', 'payment_method'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $sales = $query->paginate(15);

        // Preserve current query params in pagination links
        $sales->appends($request->query());

        return view('cashier.sales.index', compact('sales', 'sortBy', 'sortDirection'));
    }

    public function salesShow(\App\Models\Sale $sale): \Illuminate\View\View
    {
        $user = Auth::user();

        if ($sale->branch_id !== $user->branch_id) {
            abort(403);
        }

        $sale->load(['saleItems.product', 'saleItems.unitType', 'branch', 'cashier', 'customer']);

        $serialsBySaleItemId = \App\Models\ProductSerial::whereIn('sale_item_id', $sale->saleItems->pluck('id')->filter())
            ->get()
            ->keyBy('sale_item_id');

        return view('cashier.sales.show', compact('sale', 'serialsBySaleItemId'));
    }

    public function salesMarkCompleted(Request $request, \App\Models\Sale $sale): \Illuminate\Http\RedirectResponse
    {
        if ($sale->branch_id !== Auth::user()->branch_id) {
            abort(403);
        }

        if ($sale->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be marked as completed.');
        }

        $sale->load(['saleItems.product', 'saleItems.unitType', 'branch']);
        $serials = $request->input('serials', []);
        if (! is_array($serials)) {
            $serials = [];
        }

        $usedSerials = [];
        foreach ($sale->saleItems as $saleItem) {
            $trackingType = (string) ($saleItem->product->tracking_type ?? 'none');
            if ($trackingType === 'none') {
                continue;
            }
            $value = trim((string) ($serials[(string) $saleItem->id] ?? ''));
            if ($value === '') {
                return redirect()->back()->with('error', 'Serial number is required for all serialized items before completing.');
            }
            $key = strtolower($value);
            if (isset($usedSerials[$key])) {
                return redirect()->back()->with('error', 'Duplicate serial number entered: '.$value);
            }
            $usedSerials[$key] = true;
        }

        try {
            DB::beginTransaction();

            foreach ($sale->saleItems as $saleItem) {
                $trackingType = (string) ($saleItem->product->tracking_type ?? 'none');
                $serialNumber = trim((string) ($serials[(string) $saleItem->id] ?? ''));
                $productId = (int) $saleItem->product_id;
                $branchId = (int) $sale->branch_id;

                if ($trackingType !== 'none') {
                    $serial = \App\Models\ProductSerial::where('serial_number', $serialNumber)->first();
                    if (! $serial) {
                        DB::rollBack();

                        return redirect()->back()->with('error', 'Invalid serial number: '.$serialNumber);
                    }
                    if ((int) $serial->product_id !== $productId) {
                        DB::rollBack();

                        return redirect()->back()->with('error', 'Serial does not match product: '.$serialNumber);
                    }
                    if ($serial->status === 'sold') {
                        DB::rollBack();

                        return redirect()->back()->with('error', 'Serial is already sold: '.$serialNumber);
                    }
                    if (! in_array((string) $serial->status, ['purchased', 'in_stock', 'assigned'], true)) {
                        DB::rollBack();

                        return redirect()->back()->with('error', 'Serial is not eligible for sale: '.$serialNumber);
                    }

                    $warrantyMonths = (int) ($saleItem->warranty_months ?? 0);
                    $serial->status = 'sold';
                    $serial->sold_at = Carbon::now();
                    $serial->branch_id = $branchId;
                    $serial->sale_item_id = $saleItem->id;
                    $serial->warranty_expiry_date = $warrantyMonths > 0
                        ? Carbon::now()->addMonths($warrantyMonths)->toDateString()
                        : null;
                    $serial->save();

                    continue;
                }

                $unitTypeId = (int) $saleItem->unit_type_id;
                $quantity = (float) $saleItem->quantity;
                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', $productId)
                    ->where('unit_type_id', $unitTypeId)
                    ->value('conversion_factor') ?? 1);
                if ($factor <= 0) {
                    $factor = 1.0;
                }

                $remaining = $quantity * $factor;
                $stocks = \App\Models\StockIn::where('product_id', $productId)
                    ->where('branch_id', $branchId)
                    ->where('quantity', '>', DB::raw('sold'))
                    ->orderBy('id')
                    ->get();

                foreach ($stocks as $stock) {
                    if ($remaining <= 0) {
                        break;
                    }
                    $available = $stock->quantity - $stock->sold;
                    $deduct = min($remaining, $available);
                    $stock->sold += $deduct;
                    $stock->save();
                    $remaining -= $deduct;
                }

                if ($remaining > 0) {
                    DB::rollBack();

                    return redirect()->back()->with('error', 'Insufficient stock to complete this order.');
                }
            }

            $sale->status = 'completed';
            $sale->save();

            DB::commit();

            return redirect()->route('cashier.sales.show', $sale)->with('success', 'Order marked as completed.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Failed to complete order: '.$e->getMessage());
        }
    }

    /**
     * Perform a quick full refund for a sale from the cashier sales list.
     */
    public function quickRefund(Request $request, Sale $sale)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId || $sale->branch_id !== $branchId) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to refund this sale.',
            ], 403);
        }

        // Disallow refunds for credit sales
        if (strtolower($sale->payment_method) === 'credit') {
            return response()->json([
                'success' => false,
                'message' => 'Refunds are not allowed for credit sales.',
            ], 422);
        }

        // Enforce 3-day window
        $saleDate = $sale->created_at->startOfDay();
        $today = Carbon::today();
        $diffDays = $today->diffInDays($saleDate);

        if ($diffDays > 3) {
            return response()->json([
                'success' => false,
                'message' => 'Refund/Return is only allowed within 2-3 days from the purchase date.',
            ], 422);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($sale, $user, $validated) {
                $saleItems = $sale->saleItems()->with('product')->get();

                $service = app(\App\Services\InventoryService::class);

                $quantity = $validated['quantity'] ?? null;

                // If a specific quantity is provided and there is exactly one item in the sale,
                // perform a partial refund for that item only.
                if ($quantity !== null && $saleItems->count() === 1) {
                    $item = $saleItems->first();
                    $qtyToRefund = min($quantity, $item->quantity);

                    if ($qtyToRefund <= 0) {
                        throw new \InvalidArgumentException('Invalid refund quantity.');
                    }

                    $unitPrice = $item->quantity > 0 ? ($item->subtotal / $item->quantity) : 0;
                    $refundAmount = $unitPrice * $qtyToRefund;

                    $refund = Refund::create([
                        'sale_id' => $sale->id,
                        'sale_item_id' => $item->id,
                        'product_id' => $item->product_id,
                        'cashier_id' => $user->id,
                        'quantity_refunded' => $qtyToRefund,
                        'refund_amount' => $refundAmount,
                        'reason' => $validated['reason'],
                        'status' => 'approved',
                        'notes' => null,
                    ]);

                    $unitTypeId = (int) ($item->unit_type_id ?? 0);
                    if ($unitTypeId <= 0) {
                        throw new \RuntimeException('Cannot restore inventory: sale item unit not found.');
                    }

                    $baseQty = $service->convertToBaseQuantity((int) $item->product_id, $unitTypeId, (float) $qtyToRefund);
                    $service->increaseStock((int) $sale->branch_id, (int) $item->product_id, (float) $baseQty, 'adjustment', 'refunds', (int) $refund->id, now());

                    // Reduce sale total by the refunded amount; leave status column unchanged
                    $sale->total_amount = max(0, $sale->total_amount - $refundAmount);
                    $sale->save();
                } else {
                    // Fallback: full-sale refund (existing behavior)
                    foreach ($saleItems as $item) {
                        $refund = Refund::create([
                            'sale_id' => $sale->id,
                            'sale_item_id' => $item->id,
                            'product_id' => $item->product_id,
                            'cashier_id' => $user->id,
                            'quantity_refunded' => $item->quantity,
                            'refund_amount' => $item->subtotal,
                            'reason' => $validated['reason'],
                            'status' => 'approved',
                            'notes' => null,
                        ]);

                        $unitTypeId = (int) ($item->unit_type_id ?? 0);
                        if ($unitTypeId <= 0) {
                            throw new \RuntimeException('Cannot restore inventory: sale item unit not found.');
                        }

                        $baseQty = $service->convertToBaseQuantity((int) $item->product_id, $unitTypeId, (float) $item->quantity);
                        $service->increaseStock((int) $sale->branch_id, (int) $item->product_id, (float) $baseQty, 'adjustment', 'refunds', (int) $refund->id, now());
                    }

                    // Full refund: set total to zero; leave status column unchanged
                    $sale->total_amount = 0;
                    $sale->save();
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Sale refunded successfully.',
                // For UI purposes, report status as 'refunded' even if the DB column is numeric/enum
                'status' => 'refunded',
            ]);
        } catch (\Exception $e) {
            Log::error('Quick refund error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error processing refund: '.$e->getMessage(),
            ], 500);
        }
    }

    public function salesCreate()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Check if the user has only 'view' permission for the 'products' module
        if (Access::hasViewOnlyPermission($user, 'products')) {
            return redirect()->route('cashier.products.index')->with('error', 'You do not have permission to add new products.');
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

        if (! Access::can($user, 'products', 'view')) {
            abort(403);
        }

        $isViewOnlyProducts = Access::hasViewOnlyPermission($user, 'products');
        $canCreateProducts = Access::can($user, 'products', 'create');
        $canEditProducts = Access::can($user, 'products', 'edit');
        $canDeleteProducts = Access::can($user, 'products', 'delete');

        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        $query = Product::query();

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', '%'.$search.'%')
                    ->orWhere('barcode', 'like', '%'.$search.'%');
            });
        }

        // Apply branch filtering - show products that are:
        // 1. Associated with the branch in product_branch, OR
        // 2. Have stock in the branch, OR
        // 3. Have been purchased (exist in purchase_items)
        $query->where(function ($q) use ($branchId) {
            $q->whereExists(function ($exists) use ($branchId) {
                $exists->select(DB::raw(1))
                    ->from('product_branch')
                    ->whereColumn('product_branch.product_id', 'products.id')
                    ->where('product_branch.branch_id', (int) $branchId);
            })
                ->orWhereExists(function ($exists) use ($branchId) {
                    $exists->select(DB::raw(1))
                        ->from('branch_stocks')
                        ->whereColumn('branch_stocks.product_id', 'products.id')
                        ->where('branch_stocks.branch_id', (int) $branchId);
                })
                ->orWhereExists(function ($exists) {
                    $exists->select(DB::raw(1))
                        ->from('purchase_items')
                        ->whereColumn('purchase_items.product_id', 'products.id');
                });
        });

        // Apply sorting
        $allowedSorts = ['id', 'product_name', 'selling_price', 'current_stock'];
        if (! in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        $products = $query->with(['brand', 'category', 'unitTypes'])
            ->orderBy($sortBy, $sortDirection)
            ->paginate(15);

        if ($request->ajax()) {
            return view('cashier.products._product_table', compact('products'));
        }

        return view('cashier.products.index', compact(
            'products',
            'sortBy',
            'sortDirection',
            'isViewOnlyProducts',
            'canCreateProducts',
            'canEditProducts',
            'canDeleteProducts'
        ));
    }

    public function createProduct()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if (! Access::can($user, 'products', 'create')) {
            return redirect()->route('cashier.products.index')
                ->with('error', "You don't have permission to add, edit, or delete products.");
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

        if (! Access::can($user, 'products', 'create')) {
            return redirect()->route('cashier.products.index')
                ->with('error', "You don't have permission to add, edit, or delete products.");
        }

        // Delegate to the SuperAdmin ProductController which has the full validation
        // and creation logic (warranty, voltage specs, unit conversions, etc.)
        $response = app(\App\Http\Controllers\SuperAdmin\ProductController::class)->store($request);

        // store() returns a JSON response — decode it and redirect accordingly
        $data = json_decode($response->getContent(), true);

        if (! empty($data['success'])) {
            // Ensure the new product is linked to the cashier's branch
            if (! empty($data['product_id'])) {
                $newProduct = Product::find($data['product_id']);
                if ($newProduct) {
                    $newProduct->branches()->syncWithoutDetaching([$branchId]);
                }
            }

            return redirect()->route('cashier.products.index')
                ->with('success', 'Product created successfully.');
        }

        // Validation errors — send back with errors
        if (! empty($data['errors'])) {
            return back()->withInput()->withErrors($data['errors']);
        }

        return back()->withInput()->with('error', $data['message'] ?? 'Failed to create product.');
    }

    public function showProduct($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $product = Product::with(['brand', 'category', 'unitTypes'])->findOrFail($id);

        return view('cashier.products.show', compact('product'));
    }

    public function updateProductImage(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'image' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            if ($request->hasFile('image')) {
                if ($product->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
                }

                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
                $product->save();
            }

            return response()->json(['success' => true, 'message' => 'Image uploaded successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: '.$e->getMessage()], 500);
        }
    }

    public function destroyProduct(Product $product): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if (! Access::can($user, 'products', 'delete')) {
            return response()->json(['success' => false, 'message' => 'You do not have permission to delete products.'], 403);
        }

        // Only allow deletion if the product has zero stock across all branches
        $totalStock = \App\Models\StockIn::where('product_id', $product->id)
            ->selectRaw('COALESCE(SUM(quantity - sold), 0) as total')
            ->value('total');

        if ((float) $totalStock > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with existing stock. Remove all stock first.',
            ], 422);
        }

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
    }

    public function editProduct($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if (! Access::can($user, 'products', 'edit')) {
            return redirect()->route('cashier.products.index')
                ->with('error', "You don't have permission to edit and delete products.");
        }

        $product = Product::findOrFail($id);
        $brands = Brand::all();
        $categories = Category::all();
        $productTypes = ProductType::all();
        $unitTypes = UnitType::all();
        $branches = Branch::all();
        $userBranch = Branch::find($branchId);

        return view('SuperAdmin.products.productList', compact('product', 'brands', 'categories', 'productTypes', 'unitTypes', 'branches', 'userBranch'));
    }

    public function updateProduct(Request $request, $id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if (! Access::can($user, 'products', 'edit')) {
            return redirect()->route('cashier.products.index')
                ->with('error', "You don't have permission to add, edit, or delete products.");
        }

        $product = Product::findOrFail($id);

        // Delegate to the SuperAdmin ProductController which has the full update logic
        $response = app(\App\Http\Controllers\SuperAdmin\ProductController::class)->update($request, $product);

        $data = json_decode($response->getContent(), true);

        if (! empty($data['success'])) {
            return redirect()->route('cashier.products.index')
                ->with('success', 'Product updated successfully.');
        }

        if (! empty($data['errors'])) {
            return back()->withInput()->withErrors($data['errors']);
        }

        return back()->withInput()->with('error', $data['message'] ?? 'Failed to update product.');
    }

    public function destroyProduct($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if (! Access::can($user, 'products', 'delete')) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to delete products.",
                ], 403);
            }

            return redirect()->route('cashier.products.index')
                ->with('error', "You don't have permission to delete products.");
        }

        $product = Product::findOrFail($id);

        try {
            $product->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('cashier.products.index')->with('success', 'Product deleted successfully');
        } catch (\Throwable $e) {
            $message = 'Failed to delete product.';

            if ($e instanceof \Illuminate\Database\QueryException) {
                $sqlState = $e->errorInfo[0] ?? null;
                if ($sqlState === '23000') {
                    $message = 'Cannot delete this product because it has related records (sales/stock/etc.).';
                }
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 409);
            }

            return redirect()->route('cashier.products.index')->with('error', $message);
        }
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

        if (! Access::can($user, 'product_category', 'view')) {
            abort(403);
        }

        $isViewOnlyCategories = Access::hasViewOnlyPermission($user, 'product_category');
        $canCreateCategories = Access::can($user, 'product_category', 'create');
        $canEditCategories = Access::can($user, 'product_category', 'edit');
        $canDeleteCategories = Access::can($user, 'product_category', 'delete');

        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        $query = Category::query();

        if ($search) {
            $query->where('category_name', 'like', '%'.$search.'%');
        }

        $categories = $query->orderBy($sortBy, $sortDirection)->paginate(15);

        return view('cashier.categories.index', compact(
            'categories',
            'isViewOnlyCategories',
            'canCreateCategories',
            'canEditCategories',
            'canDeleteCategories'
        ));
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
            'category_type' => 'required|in:non_electronic,electronic_without_serial,electronic_with_serial',
        ]);

        $category = Category::create([
            'category_name' => $validated['category_name'],
            'status' => $validated['status'],
            'category_type' => $validated['category_type'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'category' => $category,
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
            'category_type' => 'required|in:non_electronic,electronic_without_serial,electronic_with_serial',
        ]);

        $category->update([
            'category_name' => $validated['category_name'],
            'status' => $validated['status'],
            'category_type' => $validated['category_type'],
            'updated_at' => now(),
        ]);

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'category' => $category,
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
                'message' => 'Categories deleted successfully',
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

        if (! Access::can($user, 'purchases', 'view')) {
            abort(403);
        }

        $isViewOnlyPurchases = Access::hasViewOnlyPermission($user, 'purchases');
        $canCreatePurchases = Access::can($user, 'purchases', 'create');

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = \App\Models\Purchase::query()
            ->where('branch_id', $branchId)
            ->withCount('items');

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

        return view('cashier.purchase.index', compact(
            'purchases',
            'sortBy',
            'sortDirection',
            'isViewOnlyPurchases',
            'canCreatePurchases'
        ));
    }

    public function purchasesCreate(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if (! Access::can($user, 'purchases', 'create')) {
            return redirect()->route('cashier.purchases.index')
                ->with('error', "You don't have permission to add purchases.");
        }

        $suppliers = Supplier::all();
        $products = Product::all();
        $unit_types = UnitType::all();

        return view('cashier.purchase.create', compact('suppliers', 'branchId', 'products', 'unit_types'));
    }

    public function purchasesStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if (! Access::can($user, 'purchases', 'create')) {
            return redirect()->route('cashier.purchases.index')
                ->with('error', "You don't have permission to add purchases.");
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.primary_quantity' => 'required|numeric|min:1',
            'items.*.unit_type_id' => 'required|exists:unit_types,id',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.serials' => 'nullable|array',
            'items.*.serials.*.serial_number' => [
                'required_with:items.*.serials',
                'string',
                'distinct',
                function ($attribute, $value, $fail) {
                    if (\App\Models\ProductSerial::where('serial_number', $value)->exists()) {
                        $fail('Duplicate serial number: '.$value);
                    }
                },
            ],
            'items.*.serials.*.warranty_expiry' => 'nullable|date',
        ]);

        DB::transaction(function () use ($validated, $branchId, &$purchaseId) {
            $totalCost = 0;
            $purchaseId = DB::table('purchases')->insertGetId([
                'supplier_id' => $validated['supplier_id'],
                'branch_id' => $branchId,
                'cashier_id' => Auth::id(),
                'reference_number' => $validated['reference_number'],
                'purchase_date' => $validated['purchase_date'],
                'payment_status' => $validated['payment_status'],
                'total_cost' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $itemsToInsert = [];
            $serialsByItemIndex = [];

            foreach ($validated['items'] as $index => $item) {
                $primaryQty = (float) $item['primary_quantity'];
                $cost = (float) $item['cost'];
                $subtotal = $primaryQty * $cost;
                $totalCost += $subtotal;

                $itemsToInsert[] = [
                    'purchase_id' => $purchaseId,
                    'product_id' => $item['product_id'],
                    'quantity' => $primaryQty,
                    'unit_type_id' => $item['unit_type_id'],
                    'unit_cost' => $cost,
                    'subtotal' => $subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $serialsByItemIndex[$index] = $item['serials'] ?? [];
            }

            if (! empty($itemsToInsert)) {
                DB::table('purchase_items')->insert($itemsToInsert);
            }

            DB::table('purchases')->where('id', $purchaseId)->update([
                'total_cost' => $totalCost,
                'updated_at' => now(),
            ]);

            // Save serial numbers to product_serials
            foreach ($validated['items'] as $index => $item) {
                $serials = $serialsByItemIndex[$index] ?? [];
                foreach ($serials as $s) {
                    if (empty($s['serial_number'])) {
                        continue;
                    }
                    \App\Models\ProductSerial::create([
                        'product_id' => $item['product_id'],
                        'purchase_id' => $purchaseId,
                        'branch_id' => $branchId,
                        'serial_number' => $s['serial_number'],
                        'status' => 'purchased',
                        'warranty_expiry_date' => $s['warranty_expiry'] ?? null,
                        'sale_item_id' => null,
                    ]);
                }
            }
        });

        return redirect()->route('cashier.purchases.show', ['purchase' => $purchaseId])
            ->with('success', 'Purchase created successfully')
            ->with('prompt_stockin', true);
    }

    public function getProductUnitTypes(Product $product)
    {
        $productUnits = $product->unitTypes()
            ->select('unit_types.id', 'unit_types.unit_name')
            ->withPivot('conversion_factor', 'is_base')
            ->get();

        if ($productUnits->isEmpty()) {
            $units = UnitType::orderBy('unit_name')->get()->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->unit_name,
                    'conversion_factor' => 1.0,
                    'is_base' => false,
                ];
            });
        } else {
            $units = $productUnits->map(function ($unit) {
                return [
                    'id' => $unit->id,
                    'name' => $unit->unit_name,
                    'conversion_factor' => isset($unit->pivot->conversion_factor) ? (float) $unit->pivot->conversion_factor : 1.0,
                    'is_base' => isset($unit->pivot->is_base) ? (bool) $unit->pivot->is_base : false,
                ];
            });
        }

        return response()->json([
            'units' => $units,
        ]);
    }

    public function purchasesShow($purchase)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $purchase = \App\Models\Purchase::with(['items.product', 'items.unitType'])
            ->where('id', $purchase)
            ->where('branch_id', $branchId)
            ->first();

        if (! $purchase) {
            abort(404, 'Purchase not found');
        }

        // Load supplier manually since relationship not defined
        $purchase->supplier = \App\Models\Supplier::find($purchase->supplier_id);

        return view('cashier.purchase.show', compact('purchase'));
    }

    public function purchasesMarkPaid($purchase)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $purchaseModel = \App\Models\Purchase::query()
            ->where('id', (int) $purchase)
            ->where('branch_id', (int) $branchId)
            ->first();

        if (! $purchaseModel) {
            abort(404, 'Purchase not found');
        }

        if ($purchaseModel->payment_status === 'paid') {
            return redirect()
                ->route('cashier.purchases.show', ['purchase' => $purchaseModel->id])
                ->with('success', 'Purchase is already marked as paid.');
        }

        $purchaseModel->update([
            'payment_status' => 'paid',
        ]);

        return redirect()
            ->route('cashier.purchases.show', ['purchase' => $purchaseModel->id])
            ->with('success', 'Purchase marked as paid successfully.');
    }

    public function purchasesCheckSerials(Request $request): \Illuminate\Http\JsonResponse
    {
        $serialNumbers = $request->input('serial_numbers', []);

        if (! is_array($serialNumbers) || empty($serialNumbers)) {
            return response()->json(['duplicates' => []]);
        }

        $existing = \App\Models\ProductSerial::whereIn('serial_number', $serialNumbers)
            ->pluck('serial_number')
            ->toArray();

        return response()->json([
            'duplicates' => $existing,
            'total_checked' => count($serialNumbers),
            'duplicates_found' => count($existing),
        ]);
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
        $query->whereExists(function ($exists) use ($branchId) {
            $exists->select(DB::raw(1))
                ->from('branch_stocks')
                ->whereColumn('branch_stocks.product_id', 'products.id')
                ->where('branch_stocks.branch_id', (int) $branchId);
        });

        $products = $query->limit(10)->get(['id', 'product_name', 'barcode', 'selling_price']);

        return response()->json($products);
    }

    public function creditFullHistory(Customer $customer)
    {
        try {
            $user = Auth::user();
            $branchId = (int) ($user->branch_id ?? 0);

            if ($branchId <= 0) {
                abort(403, 'No branch assigned to this cashier');
            }

            $lifetimeSummary = DB::table('credits')
                ->where('customer_id', (int) $customer->id)
                ->where('branch_id', $branchId)
                ->selectRaw('
                    COUNT(*) as total_credits_all_time,
                    COALESCE(SUM(credit_amount), 0) as lifetime_credit_amount,
                    COALESCE(SUM(paid_amount), 0) as lifetime_paid_amount,
                    COALESCE(SUM(remaining_balance), 0) as lifetime_outstanding_balance
                ')
                ->first();

            $allCredits = Credit::query()
                ->where('customer_id', (int) $customer->id)
                ->where('branch_id', $branchId)
                ->with(['cashier', 'payments' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            $filters = request()->only(['date_from', 'date_to', 'status', 'credit_id', 'created_by']);

            if (! empty(array_filter($filters, fn ($v) => $v !== null && $v !== ''))) {
                $query = Credit::query()
                    ->where('customer_id', (int) $customer->id)
                    ->where('branch_id', $branchId)
                    ->with(['cashier', 'payments' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }]);

                if (! empty($filters['date_from'])) {
                    $query->whereDate('created_at', '>=', $filters['date_from']);
                }
                if (! empty($filters['date_to'])) {
                    $query->whereDate('created_at', '<=', $filters['date_to']);
                }

                if (! empty($filters['status'])) {
                    if ($filters['status'] === 'active') {
                        $query->where('status', 'active');
                    } elseif ($filters['status'] === 'partial') {
                        $query->where('status', 'partial');
                    } elseif ($filters['status'] === 'paid') {
                        $query->where('status', 'paid');
                    }
                }

                if (! empty($filters['credit_id'])) {
                    $query->where('id', $filters['credit_id']);
                }

                if (! empty($filters['created_by'])) {
                    $query->whereHas('cashier', function ($q) use ($filters) {
                        $q->where('name', 'like', '%'.$filters['created_by'].'%');
                    });
                }

                $filteredCredits = $query->orderBy('created_at', 'desc')->get();
            } else {
                $filteredCredits = $allCredits;
            }

            $groupedCredits = $filteredCredits->groupBy(function ($credit) {
                return \Carbon\Carbon::parse($credit->created_at)->format('Y-m-d');
            });

            $cashiers = \App\Models\User::join('credits', 'users.id', '=', 'credits.cashier_id')
                ->where('credits.customer_id', (int) $customer->id)
                ->where('credits.branch_id', $branchId)
                ->pluck('users.name')->unique()->sort();

            $returnTo = request('return_to');

            return view('cashier.credit.full-credit-history', compact(
                'customer',
                'lifetimeSummary',
                'groupedCredits',
                'filters',
                'cashiers',
                'allCredits',
                'returnTo'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading cashier full credit history: '.$e->getMessage());

            return back()->with('error', 'Unable to load credit history');
        }
    }

    // INVENTORY METHODS
    public function inventoryIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $tab = $request->query('tab', 'stock');
        $search = $request->query('search');
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $perPage = 25;
        $now = now();
        $today = $now->copy()->startOfDay();

        // ── Stock table ────────────────────────────────────────────────────────
        $inventory = app(InventoryService::class);
        $stockQuery = Product::with(['brand', 'category'])
            ->whereExists(fn ($q) => $q->select(DB::raw(1))
                ->from('branch_stocks')
                ->whereColumn('branch_stocks.product_id', 'products.id')
                ->where('branch_stocks.branch_id', (int) $branchId));

        if ($search) {
            $stockQuery->where('product_name', 'like', '%'.$search.'%');
        }

        $allProducts = $stockQuery->get()->map(function ($product) use ($branchId, $inventory) {
            $currentStock = (float) $inventory->availableStockBase((int) $product->id, (int) $branchId);
            $totalSold = Schema::hasTable('stock_ins')
                ? (float) DB::table('stock_ins')->where('product_id', $product->id)->where('branch_id', $branchId)->sum('sold')
                : 0;

            return (object) [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'brand' => $product->brand->brand_name ?? 'N/A',
                'category' => $product->category->category_name ?? 'N/A',
                'current_stock' => $currentStock,
                'total_sold' => $totalSold,
            ];
        });

        $sortedProducts = $allProducts->sortBy($sortBy, SORT_REGULAR, $sortDirection === 'desc');

        // ── Paginated tabs ─────────────────────────────────────────────────────
        $stockInsQuery = StockIn::with(['product', 'branch'])
            ->where('branch_id', $branchId)->latest();
        if ($search) {
            $stockInsQuery->whereHas('product', fn ($q) => $q->where('product_name', 'like', "%{$search}%"));
        }
        $stockIns = $stockInsQuery->paginate($perPage, ['*'], 'stockins_page')->withQueryString();

        $movementsQuery = StockMovement::with(['product', 'branch'])
            ->where('branch_id', $branchId)->orderByDesc('created_at');
        if ($search) {
            $movementsQuery->whereHas('product', fn ($q) => $q->where('product_name', 'like', "%{$search}%"));
        }
        $movements = $movementsQuery->paginate($perPage, ['*'], 'movements_page')->withQueryString();

        // ── KPIs ───────────────────────────────────────────────────────────────
        $todaySales = (float) Sale::where('branch_id', $branchId)
            ->where('status', 'completed')->where('created_at', '>=', $today)->sum('total_amount');
        $totalStockValue = $allProducts->sum(fn ($p) => $p->current_stock);
        $lowStockCount = $allProducts->filter(fn ($p) => $p->current_stock < 10)->count();
        $totalStockIns = $stockIns->total();
        $totalMovements = $movements->total();

        // ── Top 5 products by sold ─────────────────────────────────────────────
        $topProducts = $allProducts->sortByDesc('total_sold')->take(5)->values();

        // ── 30-day chart (sales, purchases, stock-ins) ────────────────────────
        $chartDays = collect(range(29, 0))->map(fn ($d) => $now->copy()->subDays($d)->format('Y-m-d'));

        $salesByDay = DB::table('sales')
            ->selectRaw('DATE(created_at) as d, SUM(total_amount) as total')
            ->where('branch_id', $branchId)->where('status', 'completed')
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy('d')->pluck('total', 'd');

        $purchasesByDay = DB::table('purchases')
            ->selectRaw('DATE(purchase_date) as d, SUM(total_cost) as total')
            ->where('branch_id', $branchId)
            ->where('purchase_date', '>=', $now->copy()->subDays(29)->toDateString())
            ->groupBy('d')->pluck('total', 'd');

        $stockInsByDay = DB::table('stock_ins')
            ->selectRaw('DATE(created_at) as d, SUM(quantity) as total')
            ->where('branch_id', $branchId)
            ->where('created_at', '>=', $now->copy()->subDays(29)->startOfDay())
            ->groupBy('d')->pluck('total', 'd');

        $chartLabels = $chartDays->map(fn ($d) => date('M d', strtotime($d)))->values()->toArray();
        $chartSales = $chartDays->map(fn ($d) => (float) ($salesByDay[$d] ?? 0))->values()->toArray();
        $chartPurchases = $chartDays->map(fn ($d) => (float) ($purchasesByDay[$d] ?? 0))->values()->toArray();
        $chartStockIns = $chartDays->map(fn ($d) => (float) ($stockInsByDay[$d] ?? 0))->values()->toArray();

        // ── Live activity feed ─────────────────────────────────────────────────
        $feed = collect();
        Sale::where('branch_id', $branchId)->latest()->limit(5)->get()->each(fn ($s) => $feed->push([
            'time' => $s->created_at, 'icon' => 'fa-cash-register', 'color' => '#10b981',
            'text' => 'Sale '.($s->reference_number ?? '#'.$s->id).' — ₱'.number_format($s->total_amount, 2),
        ]));
        Purchase::where('branch_id', $branchId)->latest()->limit(5)->get()->each(fn ($p) => $feed->push([
            'time' => $p->created_at, 'icon' => 'fa-shopping-cart', 'color' => '#1976D2',
            'text' => 'Purchase '.($p->reference_number ?? '#'.$p->id).' — ₱'.number_format($p->total_cost, 2),
        ]));
        Expense::where('branch_id', $branchId)->latest()->limit(4)->get()->each(fn ($e) => $feed->push([
            'time' => $e->created_at, 'icon' => 'fa-receipt', 'color' => '#ef4444',
            'text' => 'Expense: '.($e->description ?? 'N/A').' — ₱'.number_format($e->amount, 2),
        ]));
        Refund::whereHas('sale', fn ($q) => $q->where('branch_id', $branchId))->latest()->limit(3)->get()->each(fn ($r) => $feed->push([
            'time' => $r->created_at, 'icon' => 'fa-undo', 'color' => '#8b5cf6',
            'text' => 'Refund on Sale #'.$r->sale_id.' — ₱'.number_format($r->refund_amount, 2),
        ]));
        $feed = $feed->sortByDesc('time')->take(20)->values();

        // ── Alerts ─────────────────────────────────────────────────────────────
        $alerts = collect();
        if ($lowStockCount > 0) {
            $alerts->push(['type' => 'warning', 'icon' => 'fa-box-open',
                'text' => "{$lowStockCount} product(s) critically low on stock (< 10)"]);
        }
        $pendingSales = Sale::where('branch_id', $branchId)->where('status', 'pending')->count();
        if ($pendingSales > 0) {
            $alerts->push(['type' => 'warning', 'icon' => 'fa-clock',
                'text' => "{$pendingSales} pending sale(s) awaiting fulfillment"]);
        }

        return view('cashier.inventory.index', compact(
            'sortedProducts', 'sortBy', 'sortDirection', 'search', 'tab',
            'stockIns', 'movements',
            'todaySales', 'totalStockValue', 'lowStockCount',
            'totalStockIns', 'totalMovements',
            'topProducts', 'alerts',
            'chartLabels', 'chartSales', 'chartPurchases', 'chartStockIns',
            'feed'
        ));
    }

    // STOCK IN METHODS
    public function stockInIndex(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $stockIns = DB::table('stock_movements')
            ->join('products', 'stock_movements.product_id', '=', 'products.id')
            ->leftJoin('purchases', function ($join) {
                $join->on('stock_movements.source_id', '=', 'purchases.id')
                    ->where(function ($q) {
                        $q->where('stock_movements.source_type', '=', 'purchases')
                            ->orWhereNull('stock_movements.source_type');
                    });
            })
            ->leftJoin('purchase_items', function ($join) {
                $join->on('purchase_items.purchase_id', '=', 'purchases.id')
                    ->on('purchase_items.product_id', '=', 'stock_movements.product_id');
            })
            ->where('stock_movements.branch_id', (int) $branchId)
            ->where('stock_movements.movement_type', 'purchase')
            ->where(function ($q) use ($branchId) {
                $q->whereExists(function ($exists) use ($branchId) {
                    $exists->select(DB::raw(1))
                        ->from('product_branch')
                        ->whereColumn('product_branch.product_id', 'products.id')
                        ->where('product_branch.branch_id', (int) $branchId);
                })
                    ->orWhereExists(function ($exists) use ($branchId) {
                        $exists->select(DB::raw(1))
                            ->from('branch_stocks')
                            ->whereColumn('branch_stocks.product_id', 'products.id')
                            ->where('branch_stocks.branch_id', (int) $branchId);
                    });
            })
            ->orderBy('products.product_name', 'asc')
            ->select([
                'stock_movements.*',
                'products.product_name as product_name',
                'purchases.reference_number as purchase_reference_number',
                DB::raw('COALESCE(purchase_items.unit_cost, 0) as price'),
                DB::raw('COALESCE(NULLIF(stock_movements.quantity, 0), stock_movements.quantity_base, 0) as display_quantity'),
                DB::raw('(SELECT sih.id FROM stock_ins_head sih WHERE sih.purchase_id = stock_movements.source_id AND sih.branch_id = stock_movements.branch_id LIMIT 1) as stock_in_head_id'),
            ])
            ->paginate(20);

        return view('cashier.stockin.index', compact('stockIns'));
    }

    public function stockInTransaction(int $stockInHeadId): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        try {
            $stockInHead = \App\Models\StockInsHead::with([
                'branch',
                'purchase.supplier',
                'creator',
            ])->findOrFail($stockInHeadId);

            $stockItems = DB::table('stock_ins')
                ->join('products', 'stock_ins.product_id', '=', 'products.id')
                ->leftJoin('stock_in_unit_prices', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                ->leftJoin('unit_types', 'stock_in_unit_prices.unit_type_id', '=', 'unit_types.id')
                ->where('stock_ins.purchase_id', $stockInHead->purchase_id)
                ->where('stock_ins.branch_id', $stockInHead->branch_id)
                ->select([
                    'stock_ins.id',
                    'stock_ins.product_id',
                    'stock_ins.quantity',
                    'stock_ins.price',
                    'stock_ins.created_at',
                    'products.product_name',
                    'unit_types.unit_name',
                    'stock_in_unit_prices.price as unit_price',
                ])
                ->orderBy('stock_ins.created_at', 'desc')
                ->get();

            $formattedMovements = $stockItems->groupBy('product_id')->map(function ($items) {
                $firstItem = $items->first();
                $unitPrices = $items->filter(fn ($i) => ! is_null($i->unit_name) && ! is_null($i->unit_price))
                    ->mapWithKeys(fn ($i) => [$i->unit_name => number_format($i->unit_price, 2)]);

                return [
                    'id' => $firstItem->id,
                    'product_name' => $firstItem->product_name,
                    'quantity' => $items->sum('quantity'),
                    'unit_prices' => $unitPrices,
                    'created_at' => $firstItem->created_at,
                ];
            });

            return view('cashier.stockin.transaction', compact('stockInHead', 'stockItems', 'formattedMovements'));
        } catch (\Exception $e) {
            return redirect()->route('cashier.stockin.index')
                ->with('error', 'Error loading transaction details: '.$e->getMessage());
        }
    }

    public function stockInCreate(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        // Get purchases for current branch
        $purchasedSub = DB::table('purchase_items')
            ->select('purchase_id', DB::raw('SUM(quantity) as total_purchased'))
            ->groupBy('purchase_id');

        $stockedInSub = DB::table('stock_ins')
            ->select('purchase_id', DB::raw('SUM(quantity) as total_stocked'))
            ->whereNotNull('purchase_id')
            ->groupBy('purchase_id');

        $purchases = DB::table('purchases')
            ->where('purchases.branch_id', $branchId)
            ->join('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->joinSub($purchasedSub, 'pi_totals', 'pi_totals.purchase_id', '=', 'purchases.id')
            ->leftJoinSub($stockedInSub, 'si_totals', 'si_totals.purchase_id', '=', 'purchases.id')
            ->select(
                'purchases.id',
                'purchases.reference_number',
                'purchases.purchase_date',
                'suppliers.supplier_name',
                DB::raw('pi_totals.total_purchased - COALESCE(si_totals.total_stocked, 0) as remaining_quantity')
            )
            ->orderBy('purchases.purchase_date', 'desc')
            ->get();

        $cashierBranchName = (string) (DB::table('branches')->where('id', $branchId)->value('branch_name') ?? '');

        // Get branches for dropdown (admin allows branch selection per item)
        $branches = \App\Models\Branch::where('status', 'active')->get();

        return view('cashier.stockin.create', compact('purchases', 'branchId', 'cashierBranchName', 'branches'));
    }

    public function stockInStore(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        try {
            $data = $request->validate([
                'purchase_id' => 'required|exists:purchases,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.unit_type_id' => 'required|exists:unit_types,id',
                'items.*.quantity' => 'required|numeric|min:0.0001',
                'items.*.new_price' => 'required|numeric|min:0',
                'items.*.unit_quantities' => 'nullable|array',
                'items.*.unit_quantities.*' => 'nullable|numeric|min:0',
                'items.*.branch_id' => 'required|exists:branches,id',
                'items.*.serial_ids' => 'nullable|array',
                'items.*.serial_ids.*' => 'integer|exists:product_serials,id',
            ]);

            $purchaseId = $data['purchase_id'];
            $items = $data['items'];

            $purchase = \App\Models\Purchase::where('id', $purchaseId)
                ->where('branch_id', $branchId)
                ->first();

            if (! $purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase not found for this branch',
                ], 403);
            }

            // Sum requested quantities per product (base units)
            $requestedByProduct = [];
            foreach ($items as $item) {
                $pid = (int) $item['product_id'];
                $unitQuantities = $item['unit_quantities'] ?? [];

                if (is_array($unitQuantities) && count($unitQuantities) > 0) {
                    $sumBase = 0.0;
                    foreach ($unitQuantities as $unitTypeId => $enteredQty) {
                        $enteredQty = (float) $enteredQty;
                        $unitTypeId = (int) $unitTypeId;
                        if ($enteredQty <= 0) {
                            continue;
                        }

                        $factor = (float) (\Illuminate\Support\Facades\DB::table('product_unit_type')
                            ->where('product_id', $pid)
                            ->where('unit_type_id', $unitTypeId)
                            ->value('conversion_factor') ?? 1);
                        if ($factor <= 0) {
                            $factor = 1;
                        }
                        $sumBase += ($enteredQty * $factor);
                    }

                    $requestedByProduct[$pid] = ($requestedByProduct[$pid] ?? 0) + $sumBase;

                    continue;
                }

                $qtyBase = (float) ($item['quantity'] ?? 0);
                $requestedByProduct[$pid] = ($requestedByProduct[$pid] ?? 0) + $qtyBase;
            }

            // Validate remaining quantity per product
            $purchaseItems = $purchase->items()->whereIn('product_id', array_keys($requestedByProduct))->get();
            foreach ($requestedByProduct as $pid => $requestedQty) {
                $pi = $purchaseItems->firstWhere('product_id', $pid);
                $purchasedQty = $pi ? (float) ($pi->quantity ?? 0) : 0;

                $alreadyStockedBase = (float) DB::table('stock_movements')
                    ->where('source_type', 'purchases')
                    ->where('source_id', (int) $purchaseId)
                    ->where('product_id', (int) $pid)
                    ->where('movement_type', 'purchase')
                    ->sum('quantity_base');

                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $pid)
                    ->where('unit_type_id', (int) ($pi?->unit_type_id ?? 0))
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;

                $purchasedBase = (float) $purchasedQty * $purchaseFactor;
                $remaining = (float) $purchasedBase - (float) $alreadyStockedBase;
                if ($remaining < 0) {
                    $remaining = 0;
                }

                if ($requestedQty > $remaining) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock-in quantity exceeds remaining for one or more products.',
                    ], 422);
                }
            }

            $inventory = app(InventoryService::class);
            DB::transaction(function () use ($items, $purchaseId, $inventory) {
                foreach ($items as $item) {
                    $unitQuantities = $item['unit_quantities'] ?? [];
                    $newPrice = (float) ($item['new_price'] ?? 0);

                    if (is_array($unitQuantities) && count($unitQuantities) > 0) {
                        foreach ($unitQuantities as $unitTypeId => $enteredQty) {
                            $enteredQty = (float) $enteredQty;
                            $unitTypeId = (int) $unitTypeId;
                            if ($enteredQty <= 0) {
                                continue;
                            }

                            $qtyBase = $inventory->convertToBaseQuantity((int) $item['product_id'], (int) $unitTypeId, (float) $enteredQty);
                            $inventory->increaseStock((int) $item['branch_id'], (int) $item['product_id'], (float) $qtyBase, 'purchase', 'purchases', (int) $purchaseId, now());

                            if (Schema::hasTable('stock_ins')) {
                                $stockInPayload = [
                                    'product_id' => (int) $item['product_id'],
                                    'branch_id' => (int) $item['branch_id'],
                                    'purchase_id' => (int) $purchaseId,
                                    'unit_type_id' => (int) $unitTypeId,
                                    'quantity' => (int) round($enteredQty),
                                    'price' => $newPrice,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];

                                if (Schema::hasColumn('stock_ins', 'initial_quantity')) {
                                    $stockInPayload['initial_quantity'] = (int) round($enteredQty);
                                }

                                $stockInId = DB::table('stock_ins')->insertGetId($stockInPayload);

                                // Insert into stock_in_unit_prices if table exists
                                if (Schema::hasTable('stock_in_unit_prices')) {
                                    DB::table('stock_in_unit_prices')->upsert(
                                        [
                                            'stock_in_id' => $stockInId,
                                            'unit_type_id' => (int) $unitTypeId,
                                            'price' => $newPrice,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ],
                                        ['stock_in_id', 'unit_type_id'],
                                        ['price', 'updated_at']
                                    );
                                }
                            }

                            if (Schema::hasTable('product_unit_type') && Schema::hasColumn('product_unit_type', 'price')) {
                                DB::table('product_unit_type')
                                    ->where('product_id', (int) $item['product_id'])
                                    ->where('unit_type_id', (int) $unitTypeId)
                                    ->update(['price' => $newPrice, 'updated_at' => now()]);
                            }
                        }

                        // Update selected serials to in_stock
                        $serialIds = $item['serial_ids'] ?? [];
                        if (is_array($serialIds) && count($serialIds) > 0) {
                            \App\Models\ProductSerial::query()
                                ->whereIn('id', array_map('intval', $serialIds))
                                ->where('purchase_id', (int) $purchaseId)
                                ->where('product_id', (int) $item['product_id'])
                                ->where('status', 'purchased')
                                ->update([
                                    'branch_id' => (int) $item['branch_id'],
                                    'status' => 'in_stock',
                                ]);
                        }

                        continue;
                    }

                    $qtyBase = $inventory->convertToBaseQuantity((int) $item['product_id'], (int) $item['unit_type_id'], (float) ($item['quantity'] ?? 0));
                    $inventory->increaseStock((int) $item['branch_id'], (int) $item['product_id'], (float) $qtyBase, 'purchase', 'purchases', (int) $purchaseId, now());

                    if (Schema::hasTable('stock_ins')) {
                        $enteredQty = (float) ($item['quantity'] ?? 0);
                        $unitTypeId = (int) ($item['unit_type_id'] ?? 0);

                        $stockInPayload = [
                            'product_id' => (int) $item['product_id'],
                            'branch_id' => (int) $item['branch_id'],
                            'purchase_id' => (int) $purchaseId,
                            'unit_type_id' => $unitTypeId ?: null,
                            'quantity' => (int) round($enteredQty),
                            'price' => $newPrice,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        if (Schema::hasColumn('stock_ins', 'initial_quantity')) {
                            $stockInPayload['initial_quantity'] = (int) round($enteredQty);
                        }

                        $stockInId = DB::table('stock_ins')->insertGetId($stockInPayload);

                        // Insert into stock_in_unit_prices if table exists
                        if (Schema::hasTable('stock_in_unit_prices') && $unitTypeId) {
                            DB::table('stock_in_unit_prices')->upsert(
                                [
                                    'stock_in_id' => $stockInId,
                                    'unit_type_id' => $unitTypeId,
                                    'price' => $newPrice,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ],
                                ['stock_in_id', 'unit_type_id'],
                                ['price', 'updated_at']
                            );
                        }
                    }

                    if (Schema::hasTable('product_unit_type') && Schema::hasColumn('product_unit_type', 'price')) {
                        DB::table('product_unit_type')
                            ->where('product_id', (int) $item['product_id'])
                            ->where('unit_type_id', (int) $item['unit_type_id'])
                            ->update(['price' => $newPrice, 'updated_at' => now()]);
                    }

                    // Update selected serials to in_stock
                    $serialIds = $item['serial_ids'] ?? [];
                    if (is_array($serialIds) && count($serialIds) > 0) {
                        \App\Models\ProductSerial::query()
                            ->whereIn('id', array_map('intval', $serialIds))
                            ->where('purchase_id', (int) $purchaseId)
                            ->where('product_id', (int) $item['product_id'])
                            ->where('status', 'purchased')
                            ->update([
                                'branch_id' => (int) $item['branch_id'],
                                'status' => 'in_stock',
                            ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => count($items).' items added successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding stock: '.$e->getMessage(),
            ], 500);
        }
    }

    public function stockInProductsByPurchase($purchase): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        try {
            $purchaseModel = \App\Models\Purchase::findOrFail($purchase);

            if ($purchaseModel->branch_id != $branchId) {
                abort(403, 'Purchase not found for this branch');
            }

            $purchaseItems = $purchaseModel->items()->with([
                'product.unitTypes' => function ($q) {
                    $q->withPivot('conversion_factor', 'is_base');
                },
                'product.category',
                'unitType',
            ])->get();

            $items = $purchaseItems->map(function ($item) {
                $purchaseUnitTypeId = (int) ($item->unit_type_id ?? 0);
                $purchaseUnitName = $item->unitType?->unit_name;

                $purchaseFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $item->product_id)
                    ->where('unit_type_id', $purchaseUnitTypeId)
                    ->value('conversion_factor') ?? 1);
                $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;

                $alreadyStockedBase = (float) DB::table('stock_ins')
                    ->where('purchase_id', (int) $item->purchase_id)
                    ->where('product_id', (int) $item->product_id)
                    ->sum('quantity');

                $purchasedQty = (float) ($item->quantity ?? 0);
                $purchasedBase = $purchasedQty * $purchaseFactor;
                $remainingBase = max(0, $purchasedBase - $alreadyStockedBase);

                $unitTypes = $item->product->unitTypes ?? collect();

                if ($unitTypes instanceof \Illuminate\Support\Collection && $unitTypes->isEmpty()) {
                    $unitTypes = \App\Models\UnitType::orderBy('unit_name')->get();
                }

                $unitTypesPayload = collect($unitTypes)->map(function ($ut) {
                    return [
                        'id' => $ut->id,
                        'unit_name' => $ut->unit_name,
                        'conversion_factor' => isset($ut->pivot->conversion_factor) ? (float) $ut->pivot->conversion_factor : 1.0,
                        'is_base' => isset($ut->pivot->is_base) ? (bool) $ut->pivot->is_base : false,
                    ];
                })->values();

                $baseUnit = collect($unitTypes)->firstWhere('pivot.is_base', true);
                $baseUnitName = $baseUnit?->unit_name;

                $conversionParts = [];
                if ($baseUnitName) {
                    foreach ($unitTypesPayload as $u) {
                        if (! empty($u['is_base'])) {
                            continue;
                        }
                        $f = (float) ($u['conversion_factor'] ?? 0);
                        if ($f > 0) {
                            $fText = rtrim(rtrim(number_format($f, 6, '.', ''), '0'), '.');
                            $conversionParts[] = '1 '.$u['unit_name'].' × '.$fText.' '.$baseUnitName;
                        }
                    }
                }

                return [
                    'product_id' => $item->product_id,
                    'product' => $item->product,
                    'category_type' => $item->product?->category?->category_type ?? 'non_electronic',
                    'quantity' => $remainingBase,
                    'purchased_qty' => $purchasedQty,
                    'purchased_base' => $purchasedBase,
                    'remaining_base' => $remainingBase,
                    'purchase_unit_name' => $purchaseUnitName,
                    'purchase_factor' => $purchaseFactor,
                    'base_unit_name' => $baseUnitName,
                    'conversion_summary' => implode(', ', $conversionParts),
                    'unit_price' => $item->unit_cost,
                    'unit_types' => $unitTypesPayload,
                    'unit_type' => $item->unitType,
                ];
            })->values();

            return response()->json(['items' => $items]);

        } catch (\Exception $e) {
            return response()->json(['items' => [], 'error' => $e->getMessage()]);
        }
    }

    public function stockInPurchaseProductSerials(\App\Models\Purchase $purchase, \App\Models\Product $product): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if ($purchase->branch_id !== $user->branch_id) {
            abort(403);
        }

        $serials = \App\Models\ProductSerial::query()
            ->where('purchase_id', $purchase->id)
            ->where('product_id', $product->id)
            ->where('status', 'purchased')
            ->orderBy('id')
            ->get(['id', 'serial_number', 'warranty_expiry_date']);

        return response()->json([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'serials' => $serials,
        ]);
    }

    public function autoStockInCheck($purchase): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'No branch assigned.'], 403);
        }

        $purchaseModel = \App\Models\Purchase::with(['items.product', 'items.unitType'])
            ->where('id', $purchase)
            ->where('branch_id', $branchId)
            ->first();

        if (! $purchaseModel) {
            return response()->json(['success' => false, 'message' => 'Purchase not found.'], 404);
        }

        $pricingItems = [];

        foreach ($purchaseModel->items as $item) {
            $productId = (int) $item->product_id;
            $unitTypeId = (int) ($item->unit_type_id ?? 0);

            if ($unitTypeId <= 0) {
                continue;
            }

            // Get the latest existing selling price for this product+unit+branch
            $existingPrice = DB::table('stock_in_unit_prices')
                ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                ->where('stock_ins.product_id', $productId)
                ->where('stock_ins.branch_id', $branchId)
                ->where('stock_in_unit_prices.unit_type_id', $unitTypeId)
                ->orderByDesc('stock_in_unit_prices.id')
                ->value('stock_in_unit_prices.price');

            $pricingItems[] = [
                'purchase_item_id' => $item->id,
                'product_id' => $productId,
                'unit_type_id' => $unitTypeId,
                'product_name' => $item->product->product_name ?? 'Unknown',
                'unit_name' => $item->unitType->unit_name ?? 'Unknown',
                'unit_cost' => (float) ($item->unit_cost ?? 0),
                'existing_price' => $existingPrice !== null ? (float) $existingPrice : null,
                'is_new' => $existingPrice === null,
            ];
        }

        return response()->json([
            'success' => true,
            'pricing_items' => $pricingItems,
        ]);
    }

    public function autoStockIn($purchase): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['success' => false, 'message' => 'No branch assigned.'], 403);
        }

        $purchaseModel = \App\Models\Purchase::with(['items.unitType'])
            ->where('id', $purchase)
            ->where('branch_id', $branchId)
            ->first();

        if (! $purchaseModel) {
            return response()->json(['success' => false, 'message' => 'Purchase not found.'], 404);
        }

        // selling_prices keyed by "productId_unitTypeId" => price
        $sellingPrices = request()->input('selling_prices', []);

        $inventory = app(InventoryService::class);

        try {
            DB::transaction(function () use ($purchaseModel, $branchId, $inventory, $sellingPrices) {
                foreach ($purchaseModel->items as $item) {
                    $productId = (int) $item->product_id;
                    $unitTypeId = (int) ($item->unit_type_id ?? 0);
                    $qty = (float) ($item->quantity ?? 0);
                    $unitCost = (float) ($item->unit_cost ?? 0);

                    if ($qty <= 0) {
                        continue;
                    }

                    $qtyBase = $inventory->convertToBaseQuantity($productId, $unitTypeId, $qty);
                    $inventory->increaseStock($branchId, $productId, $qtyBase, 'purchase', 'purchases', (int) $purchaseModel->id, now());

                    $stockInPayload = [
                        'product_id' => $productId,
                        'branch_id' => $branchId,
                        'purchase_id' => (int) $purchaseModel->id,
                        'unit_type_id' => $unitTypeId ?: null,
                        'quantity' => (int) round($qty),
                        'price' => $unitCost,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (Schema::hasColumn('stock_ins', 'initial_quantity')) {
                        $stockInPayload['initial_quantity'] = (int) round($qty);
                    }

                    $stockInId = DB::table('stock_ins')->insertGetId($stockInPayload);

                    if (Schema::hasTable('stock_in_unit_prices') && $unitTypeId) {
                        $key = $productId.'_'.$unitTypeId;
                        $sellingPrice = $sellingPrices[$key] ?? null;

                        // If no new price submitted, fall back to the existing price
                        if ($sellingPrice === null) {
                            $sellingPrice = DB::table('stock_in_unit_prices')
                                ->join('stock_ins', 'stock_ins.id', '=', 'stock_in_unit_prices.stock_in_id')
                                ->where('stock_ins.product_id', $productId)
                                ->where('stock_ins.branch_id', $branchId)
                                ->where('stock_in_unit_prices.unit_type_id', $unitTypeId)
                                ->where('stock_ins.id', '!=', $stockInId)
                                ->orderByDesc('stock_in_unit_prices.id')
                                ->value('stock_in_unit_prices.price');
                        }

                        if ($sellingPrice !== null) {
                            DB::table('stock_in_unit_prices')->insert([
                                'stock_in_id' => $stockInId,
                                'unit_type_id' => $unitTypeId,
                                'price' => (float) $sellingPrice,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'All items stocked in successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display refunds for the cashier's branch.
     */
    public function refundsIndex()
    {
        $user = Auth::user();

        if (! Access::can($user, 'refund_return', 'view')) {
            abort(403);
        }
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

            if (! Access::can($user, 'refund_return', 'create')) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }

                abort(403);
            }
            $branchId = $user->branch_id;

            if (! $branchId) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier'], 403);
                }

                return back()->with('error', 'No branch assigned to this cashier.');
            }

            // Debug: Log incoming request data
            Log::info('Cashier refund request data: '.json_encode($request->all()));

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
                Log::error('Cashier refund validation failed: '.json_encode($validator->errors()->toArray()));
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

            Log::info('Validation passed, starting transaction');

            DB::transaction(function () use ($request) {
                Log::info('Finding sale item: '.$request->sale_item_id);
                $saleItem = \App\Models\SaleItem::find($request->sale_item_id);

                if (! $saleItem) {
                    Log::error('Sale item not found: '.$request->sale_item_id);
                    throw new \Exception('Sale item not found');
                }

                Log::info('Sale item found, checking existing refunds');
                // Validate that refund quantity doesn't exceed sold quantity
                $totalRefunded = Refund::where('sale_item_id', $request->sale_item_id)
                    ->where('status', 'approved')
                    ->sum('quantity_refunded');

                Log::info('Total refunded: '.$totalRefunded.', requested: '.$request->quantity_refunded.', sold: '.$saleItem->quantity);

                if ($totalRefunded + $request->quantity_refunded > $saleItem->quantity) {
                    throw new \Exception('Cannot refund more items than were sold');
                }

                Log::info('Creating refund record');
                // Create refund record
                $refund = Refund::create([
                    'sale_id' => $request->sale_id,
                    'sale_item_id' => $request->sale_item_id,
                    'product_id' => $request->product_id,
                    'cashier_id' => Auth::id(),
                    'quantity_refunded' => $request->quantity_refunded,
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->reason,
                    'status' => 'approved', // Auto-approve for cashier refunds
                    'notes' => $request->notes,
                ]);

                Log::info('Refund created with ID: '.$refund->id);

                // Update sale total amount by deducting refund amount
                /** @var \App\Models\Sale|null $sale */
                $sale = \App\Models\Sale::find($request->sale_id);
                if ($sale) {
                    // Do not allow refunds for credit sales (defensive check)
                    if (strtolower($sale->payment_method) === 'credit') {
                        throw new \Exception('Refunds are not allowed for credit sales.');
                    }

                    $currentTotal = $sale->total_amount;
                    $newTotal = $currentTotal - $request->refund_amount;

                    $sale->total_amount = max(0, $newTotal);
                    $sale->status = 'refunded';
                    $sale->save();

                    Log::info('Sale total updated: '.$currentTotal.' -> '.$sale->total_amount.' (Refund: '.$request->refund_amount.')');
                }

                // Update inventory - add back refunded items
                $product = Product::find($request->product_id);
                if ($product) {
                    Log::info('Updating inventory for product: '.$product->id);

                    $branchId = (int) (Auth::user()?->branch_id ?? 1);
                    $service = app(\App\Services\InventoryService::class);

                    $saleItem = \App\Models\SaleItem::query()
                        ->where('sale_id', (int) $request->sale_id)
                        ->where('product_id', (int) $request->product_id)
                        ->first(['unit_type_id']);

                    $unitTypeId = (int) ($saleItem?->unit_type_id ?? 0);
                    if ($unitTypeId <= 0) {
                        throw new \RuntimeException('Cannot restore inventory: sale item unit not found.');
                    }

                    $baseQty = $service->convertToBaseQuantity((int) $product->id, $unitTypeId, (float) $request->quantity_refunded);
                    $service->increaseStock($branchId, (int) $product->id, $baseQty, 'adjustment', 'refunds', (int) $request->sale_id, now());
                } else {
                    Log::warning('Product not found: '.$request->product_id);
                }

                Log::info('Cashier refund transaction completed successfully');
            });

            Log::info('Returning success response');
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Refund processed successfully']);
            }

            return redirect()
                ->route('cashier.refunds.index')
                ->with('success', 'Refund processed successfully.');

        } catch (\Exception $e) {
            Log::error('Cashier refund processing error: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
            Log::error('Stack trace: '.$e->getTraceAsString());
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
            Log::error('Error loading cashier credits: '.$e->getMessage());

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

        // Branches dropdown based on branches table
        $branches = Branch::where('status', 'active')->orderBy('branch_name')->get();
        $userBranch = Branch::find($branchId);

        return view('cashier.credit.create', compact('customers', 'branches', 'userBranch'));
    }

    /**
     * Store a newly created credit.
     */
    public function creditStore(Request $request)
    {
        try {
            $user = Auth::user();
            $userBranchId = $user->branch_id;

            if (! $userBranchId) {
                return response()->json(['success' => false, 'message' => 'No branch assigned to this cashier'], 403);
            }

            // customer_id can be either an existing customer ID or a new name
            $validator = Validator::make($request->all(), [
                'branch_id' => 'required|exists:branches,id',
                'customer_id' => 'required',
                'credit_amount' => 'required|numeric|min:0',
                'credit_type' => 'required|in:cash,grocery,electronics',
                'sale_id' => 'required_if:credit_type,grocery,electronics|nullable|exists:sales,id',
                'phone_number' => 'nullable|string|max:20',
                'due_date' => 'required|date',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            // Use selected branch from form
            $branchId = (int) $userBranchId;

            // Generate a unique reference number for this credit
            $referenceNumber = 'CR-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));

            // Resolve or create customer
            $rawCustomer = $request->customer_id;

            if (is_numeric($rawCustomer)) {
                $customer = Customer::find($rawCustomer);
            } else {
                $typedName = trim((string) $rawCustomer);

                if ($typedName !== '') {
                    // Avoid double customer records: reuse existing customer if name matches.
                    $customer = Customer::query()
                        ->whereRaw('LOWER(TRIM(COALESCE(full_name, name))) = ?', [mb_strtolower($typedName)])
                        ->first();
                } else {
                    $customer = null;
                }
            }

            // If no existing customer, create a new one using typed name
            if (! $customer) {
                $safeName = trim((string) $rawCustomer);

                if ($safeName === '') {
                    return response()->json(['success' => false, 'message' => 'Customer name is required'], 422);
                }

                $customer = new Customer([
                    'full_name' => $safeName,
                    'status' => 'active',
                    'created_by' => $user->id,
                ]);

                // If customers table has branch_id, set it safely
                if (Schema::hasColumn('customers', 'branch_id')) {
                    $customer->branch_id = $branchId;
                }

                $customer->save();
            }

            // If customers table has branch_id, ensure it matches the selected branch
            if (isset($customer->branch_id) && $customer->branch_id != $branchId) {
                return response()->json(['success' => false, 'message' => 'Customer not authorized for this branch'], 403);
            }

            $credit = DB::transaction(function () use ($request, $customer, $branchId, $user, $referenceNumber) {
                $maxLimit = (float) ($customer->max_credit_limit ?? 0);
                $creditType = (string) $request->credit_type;
                $saleId = $request->sale_id;
                $amount = (float) $request->credit_amount;
                $creditDate = $request->due_date;

                if ($maxLimit > 0) {
                    $activeCredits = Credit::query()
                        ->where('branch_id', $branchId)
                        ->where('customer_id', $customer->id)
                        ->where('status', 'active')
                        ->lockForUpdate()
                        ->get(['remaining_balance']);

                    $outstanding = (float) $activeCredits->sum(function ($row) {
                        return (float) ($row->remaining_balance ?? 0);
                    });

                    if (($outstanding + $amount) > $maxLimit) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'credit_amount' => 'Credit limit reached. Outstanding balance would exceed the maximum credit limit.',
                        ]);
                    }
                }

                $existing = Credit::query()
                    ->where('branch_id', $branchId)
                    ->where('customer_id', $customer->id)
                    ->where('status', 'active')
                    ->where('credit_type', $creditType)
                    ->whereDate('date', '=', $creditDate)
                    ->when(in_array($creditType, ['grocery', 'electronics'], true), function ($q) use ($saleId) {
                        $q->where('sale_id', $saleId);
                    })
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $existing->credit_amount = (float) $existing->credit_amount + $amount;
                    $existing->remaining_balance = (float) $existing->remaining_balance + $amount;
                    $existing->date = $request->due_date;
                    $existing->cashier_id = $user->id;
                    $existing->notes = $request->phone_number;
                    $existing->save();

                    return $existing;
                }

                return Credit::create([
                    'reference_number' => $referenceNumber,
                    'customer_id' => $customer->id,
                    'branch_id' => $branchId,
                    'cashier_id' => $user->id,
                    'sale_id' => $saleId,
                    'credit_amount' => $amount,
                    'remaining_balance' => $amount,
                    'date' => $request->due_date,
                    'description' => $request->description,
                    'status' => 'active',
                    'notes' => $request->phone_number,
                    'credit_type' => $creditType,
                ]);
            });

            // Update customer phone number if provided
            if (! empty($request->phone_number)) {
                $customer->update(['phone' => $request->phone_number]);
            }

            return response()->json(['success' => true, 'message' => 'Credit created successfully', 'credit' => $credit]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Cashier credit creation error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error creating credit: '.$e->getMessage()], 500);
        }
    }

    /**
     * Record a payment for a specific credit (cashier side).
     */
    public function creditRecordPayment(Request $request, Credit $credit)
    {
        $user = Auth::user();

        // Ensure credit belongs to the cashier's branch if branch_id is set
        if (isset($credit->branch_id) && $credit->branch_id !== $user->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to record a payment for this credit.',
            ], 403);
        }

        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        // Do not allow overpayment
        if ($validated['payment_amount'] > $credit->remaining_balance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot be greater than remaining balance.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($credit, $user, $validated) {
                // Create payment record
                CreditPayment::create([
                    'credit_id' => $credit->id,
                    'cashier_id' => $user->id,
                    'payment_amount' => $validated['payment_amount'],
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Update credit balances
                $credit->paid_amount = ($credit->paid_amount ?? 0) + $validated['payment_amount'];
                $credit->remaining_balance = max(0, $credit->remaining_balance - $validated['payment_amount']);

                if ($credit->remaining_balance <= 0) {
                    $credit->status = 'paid';
                }

                $credit->save();
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'remaining_balance' => number_format($credit->fresh()->remaining_balance, 2),
                'status' => $credit->fresh()->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Error recording credit payment: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error recording payment. Please try again.',
            ], 500);
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
            Log::error('Error loading cashier expenses: '.$e->getMessage());

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
            Log::error('Cashier expense creation error: '.$e->getMessage());

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
            $branchName = (string) (DB::table('branches')->where('id', (int) $branchId)->value('branch_name') ?? 'Branch');

            $customers = Customer::query()
                ->with(['user'])
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'like', '%'.$search.'%')
                            ->orWhere('phone', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
                })
                ->addSelect([
                    'outstanding_balance' => Credit::query()
                        ->selectRaw('COALESCE(SUM(remaining_balance), 0)')
                        ->whereColumn('credits.customer_id', 'customers.id')
                        ->where('credits.branch_id', (int) $branchId),
                ])
                ->orderBy('full_name')
                ->paginate(20)
                ->withQueryString();

            return view('cashier.customers.index', compact('customers', 'branchName'));
        } catch (\Exception $e) {
            Log::error('Error loading cashier customers: '.$e->getMessage());

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
            Log::error('Cashier customer creation error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Error creating customer: '.$e->getMessage()], 500);
        }
    }

    public function customersShow(Customer $customer)
    {
        $user = Auth::user();
        $branchId = (int) ($user->branch_id ?? 0);

        if ($branchId <= 0) {
            abort(403, 'No branch assigned to this cashier');
        }

        $customerDetails = DB::table('customers')
            ->select([
                'customers.id as customer_id',
                'customers.full_name',
                'customers.phone',
                'customers.email',
                'customers.address',
                'customers.max_credit_limit',
                'customers.status',
                'customers.created_at',
                DB::raw('COALESCE(users.name, "Cashier") as created_by'),
                DB::raw('COUNT(credits.id) as total_credits'),
                DB::raw('COALESCE(SUM(credits.credit_amount), 0) as total_credit'),
                DB::raw('COALESCE(SUM(credits.paid_amount), 0) as total_paid'),
                DB::raw('COALESCE(SUM(credits.remaining_balance), 0) as outstanding_balance'),
                DB::raw('MAX(credits.created_at) as last_credit_date'),
                DB::raw('CASE
                    WHEN COALESCE(SUM(credits.remaining_balance), 0) <= 0 THEN "Fully Paid"
                    WHEN COALESCE(SUM(credits.paid_amount), 0) / NULLIF(COALESCE(SUM(credits.credit_amount), 0), 0) >= 0.8 THEN "Good Standing"
                    ELSE "Outstanding"
                END as credit_status'),
            ])
            ->leftJoin('credits', function ($join) use ($branchId) {
                $join->on('customers.id', '=', 'credits.customer_id')
                    ->where('credits.branch_id', '=', $branchId);
            })
            ->leftJoin('users', 'customers.created_by', '=', 'users.id')
            ->where('customers.id', (int) $customer->id)
            ->groupBy(
                'customers.id',
                'customers.full_name',
                'customers.phone',
                'customers.email',
                'customers.address',
                'customers.max_credit_limit',
                'customers.status',
                'customers.created_at',
                'users.name'
            )
            ->first();

        $recentCreditsRaw = Credit::query()
            ->where('customer_id', (int) $customer->id)
            ->where('branch_id', $branchId)
            ->with(['cashier'])
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $recentCredits = $recentCreditsRaw
            ->groupBy(function ($c) {
                try {
                    return \Carbon\Carbon::parse($c->date)->format('Y-m-d');
                } catch (\Exception $e) {
                    return (string) $c->date;
                }
            })
            ->map(function ($rows, $dateKey) {
                $totalAmount = (float) $rows->sum(fn ($r) => (float) ($r->credit_amount ?? 0));
                $totalPaid = (float) $rows->sum(fn ($r) => (float) ($r->paid_amount ?? 0));
                $totalRemaining = (float) $rows->sum(fn ($r) => (float) ($r->remaining_balance ?? 0));
                $last = $rows->sortByDesc('created_at')->first();

                return (object) [
                    'date_key' => $dateKey,
                    'date' => $last?->date,
                    'created_at' => $last?->created_at,
                    'cashier_name' => $last?->cashier?->name,
                    'credit_amount' => $totalAmount,
                    'paid_amount' => $totalPaid,
                    'remaining_balance' => $totalRemaining,
                    'count' => (int) $rows->count(),
                ];
            })
            ->sortByDesc('date_key')
            ->take(3)
            ->values();

        $activeCredits = Credit::query()
            ->where('customer_id', (int) $customer->id)
            ->where('branch_id', $branchId)
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->orderByDesc('created_at')
            ->get(['id', 'remaining_balance']);

        return view('cashier.customers.show', compact('customer', 'customerDetails', 'recentCredits', 'activeCredits'));
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
    public function posElectronics()
    {
        return view('cashier.pos.electronics');
    }

    public function posLookup(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            return response()->json(['error' => 'No branch assigned to this cashier']);
        }

        $posBranchId = (int) $branchId;

        $keyword = $request->input('keyword', $request->input('barcode'));
        $mode = $request->input('mode', 'list');
        $electronicsOnly = (bool) $request->boolean('electronics_only');
        $excludeElectronics = (bool) $request->boolean('exclude_electronics'); // New flag for regular POS

        if ($electronicsOnly) {
            Log::debug('POS electronics_only lookup', [
                'branch_id' => $posBranchId,
                'mode' => $mode,
                'keyword' => $keyword,
            ]);
        }
        if ($excludeElectronics) {
            Log::debug('POS exclude_electronics lookup', [
                'branch_id' => $posBranchId,
                'mode' => $mode,
                'keyword' => $keyword,
            ]);
        }

        if ($mode === 'list' && empty($keyword)) {
            // Return all products for cashier's branch
            $inventory = app(InventoryService::class);

            $productsQuery = Product::query();
            if ($electronicsOnly) {
                $productsQuery = $productsQuery
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                    ->where(function ($q) {
                        $q->whereRaw("LOWER(TRIM(categories.category_type)) LIKE 'electronic%'")
                            ->orWhere('product_types.is_electronic', true)
                            ->orWhere('product_types.type_name', 'LIKE', '%elect%');
                    });
            }
            if ($excludeElectronics) {
                $productsQuery = $productsQuery
                    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                    ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                    ->where(function ($q) {
                        $q->whereRaw("LOWER(TRIM(categories.category_type)) NOT LIKE 'electronic%'")
                            ->where(function ($inner) {
                                $inner->whereNull('product_types.is_electronic')
                                    ->orWhere('product_types.is_electronic', false);
                            })
                            ->where(function ($inner) {
                                $inner->whereNull('product_types.type_name')
                                    ->orWhere('product_types.type_name', 'NOT LIKE', '%elect%');
                            });
                    });
            }

            if ($electronicsOnly) {
                Log::debug('POS electronics_only SQL', ['sql' => $productsQuery->toSql()]);
            }

            $products = $productsQuery
                ->when(! $electronicsOnly, function ($q) use ($posBranchId) {
                    $q->whereExists(function ($exists) use ($posBranchId) {
                        $exists->select(DB::raw(1))
                            ->from('branch_stocks')
                            ->whereColumn('branch_stocks.product_id', 'products.id')
                            ->where('branch_stocks.branch_id', (int) $posBranchId)
                            ->where('branch_stocks.quantity_base', '>', 0);
                    });
                })
                ->with(['unitTypes', 'category'])
                ->get(['products.id', 'products.product_name', 'products.barcode', 'products.model_number'])
                ->map(function ($product) use ($posBranchId, $inventory) {
                    $totalStock = (float) $inventory->availableStockBase((int) $product->id, (int) $posBranchId);

                    $availableByUnitTypeId = [];
                    if (Schema::hasTable('stock_ins')) {
                        $availableByUnitTypeId = DB::table('stock_ins')
                            ->where('product_id', (int) $product->id)
                            ->where('branch_id', (int) $posBranchId)
                            ->whereNotNull('unit_type_id')
                            ->groupBy('unit_type_id')
                            ->select('unit_type_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(sold) as sold'))
                            ->get()
                            ->mapWithKeys(function ($r) {
                                $ut = (int) ($r->unit_type_id ?? 0);
                                $qty = (float) ($r->qty ?? 0);
                                $sold = (float) ($r->sold ?? 0);
                                $available = max(0, $qty - $sold);

                                return [$ut => $available];
                            })
                            ->toArray();
                    }

                    $unitRows = DB::table('product_unit_type')
                        ->join('unit_types', 'unit_types.id', '=', 'product_unit_type.unit_type_id')
                        ->where('product_unit_type.product_id', (int) $product->id)
                        ->select('unit_types.id as unit_type_id', 'unit_types.unit_name', 'product_unit_type.conversion_factor', 'product_unit_type.is_base')
                        ->orderByDesc('product_unit_type.is_base')
                        ->get();

                    $unitPriceById = [];
                    if (Schema::hasTable('product_unit_type') && Schema::hasColumn('product_unit_type', 'price')) {
                        $unitPriceById = DB::table('product_unit_type')
                            ->where('product_id', (int) $product->id)
                            ->pluck('price', 'unit_type_id')
                            ->map(fn ($v) => (float) $v)
                            ->toArray();
                    }

                    if (Schema::hasTable('stock_ins')) {
                        $stockInPrices = DB::table('stock_ins')
                            ->where('product_id', (int) $product->id)
                            ->where('branch_id', (int) $posBranchId)
                            ->whereNotNull('unit_type_id')
                            ->where('price', '>', 0)
                            ->orderByDesc('id')
                            ->get(['unit_type_id', 'price']);

                        foreach ($stockInPrices as $r) {
                            $ut = (int) $r->unit_type_id;
                            $p = (float) $r->price;
                            if ($ut <= 0 || $p <= 0) {
                                continue;
                            }
                            if (! isset($unitPriceById[$ut]) || (float) $unitPriceById[$ut] <= 0) {
                                $unitPriceById[$ut] = $p;
                            }
                        }
                    }

                    $stockUnits = $unitRows->map(function ($row) use ($totalStock, $unitPriceById, $availableByUnitTypeId) {
                        $factor = (float) ($row->conversion_factor ?? 1);
                        $factor = $factor > 0 ? $factor : 1;

                        $unitTypeId = (int) $row->unit_type_id;
                        $unitStock = 0.0;
                        if (array_key_exists($unitTypeId, $availableByUnitTypeId)) {
                            $unitStock = (float) $availableByUnitTypeId[$unitTypeId];
                        } else {
                            $unitStock = (float) $totalStock / $factor;
                        }

                        return [
                            'unit_type_id' => $unitTypeId,
                            'unit_name' => $row->unit_name,
                            'stock' => $unitStock,
                            'price' => (float) ($unitPriceById[$unitTypeId] ?? 0),
                        ];
                    })->values()->toArray();

                    $defaultPrice = 0.0;
                    if (! empty($unitPriceById)) {
                        // Prefer base unit price when available
                        $baseUnit = $unitRows->firstWhere('is_base', 1);
                        if ($baseUnit) {
                            $defaultPrice = (float) ($unitPriceById[(int) $baseUnit->unit_type_id] ?? 0);
                        }
                        if ($defaultPrice <= 0) {
                            $defaultPrice = (float) (collect($unitPriceById)->first() ?? 0);
                        }
                    }

                    $branches = [
                        [
                            'branch_id' => $posBranchId,
                            'branch_name' => (string) (DB::table('branches')->where('id', (int) $posBranchId)->value('branch_name') ?? 'Branch'),
                            'stock' => $totalStock,
                            'price' => $defaultPrice,
                            'stock_units' => $stockUnits,
                        ],
                    ];

                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'barcode' => $product->barcode,
                        'model_number' => $product->model_number,
                        'selling_price' => $defaultPrice,
                        'total_stock' => $totalStock,
                        'branches' => $branches,
                        'warranty_coverage_months' => (int) ($product->warranty_coverage_months ?? 0),
                        'warranty_type' => $product->warranty_type ?? 'none',
                        'category_type' => $product->category?->category_type ?? 'non_electronic',
                    ];
                });

            if ($electronicsOnly) {
                Log::debug('POS electronics_only result', ['count' => $products->count()]);
            }

            return response()->json(['success' => true, 'products' => $products]);
        }

        // Search by barcode or name in cashier's branch
        $productsQuery = Product::query();
        if ($electronicsOnly) {
            $productsQuery = $productsQuery
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                ->where(function ($q) {
                    $q->whereRaw("LOWER(TRIM(categories.category_type)) LIKE 'electronic%'")
                        ->orWhere('product_types.is_electronic', true)
                        ->orWhere('product_types.type_name', 'LIKE', '%elect%');
                });
        }
        if ($excludeElectronics) {
            $productsQuery = $productsQuery
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                ->where(function ($q) {
                    $q->whereRaw("LOWER(TRIM(categories.category_type)) NOT LIKE 'electronic%'")
                        ->where(function ($inner) {
                            $inner->whereNull('product_types.is_electronic')
                                ->orWhere('product_types.is_electronic', false);
                        })
                        ->where(function ($inner) {
                            $inner->whereNull('product_types.type_name')
                                ->orWhere('product_types.type_name', 'NOT LIKE', '%elect%');
                        });
                });
        }

        $products = $productsQuery->where(function ($query) use ($keyword) {
            $query->where('barcode', $keyword)
                ->orWhere('product_name', 'like', '%'.$keyword.'%')
                ->orWhere('model_number', 'like', '%'.$keyword.'%');
        })
            ->when(! $electronicsOnly, function ($q) use ($posBranchId) {
                $q->whereExists(function ($exists) use ($posBranchId) {
                    $exists->select(DB::raw(1))
                        ->from('branch_stocks')
                        ->whereColumn('branch_stocks.product_id', 'products.id')
                        ->where('branch_stocks.branch_id', (int) $posBranchId)
                        ->where('branch_stocks.quantity_base', '>', 0);
                });
            })
            ->with(['unitTypes', 'category'])
            ->get();

        if ($products->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No products found.',
            ]);
        }

        $product = $products->first();

        $inventory = app(InventoryService::class);
        $totalStock = $inventory->availableStockBase((int) $product->id, (int) $posBranchId);

        $availableByUnitTypeId = [];
        if (Schema::hasTable('stock_ins')) {
            $availableByUnitTypeId = DB::table('stock_ins')
                ->where('product_id', (int) $product->id)
                ->where('branch_id', (int) $posBranchId)
                ->whereNotNull('unit_type_id')
                ->groupBy('unit_type_id')
                ->select('unit_type_id', DB::raw('SUM(quantity) as qty'), DB::raw('SUM(sold) as sold'))
                ->get()
                ->mapWithKeys(function ($r) {
                    $ut = (int) ($r->unit_type_id ?? 0);
                    $qty = (float) ($r->qty ?? 0);
                    $sold = (float) ($r->sold ?? 0);
                    $available = max(0, $qty - $sold);

                    return [$ut => $available];
                })
                ->toArray();
        }

        $unitRows = DB::table('product_unit_type')
            ->join('unit_types', 'unit_types.id', '=', 'product_unit_type.unit_type_id')
            ->where('product_unit_type.product_id', (int) $product->id)
            ->select('unit_types.id as unit_type_id', 'unit_types.unit_name', 'product_unit_type.conversion_factor', 'product_unit_type.is_base')
            ->orderByDesc('product_unit_type.is_base')
            ->get();

        $unitPriceById = [];
        if (Schema::hasTable('product_unit_type') && Schema::hasColumn('product_unit_type', 'price')) {
            $unitPriceById = DB::table('product_unit_type')
                ->where('product_id', (int) $product->id)
                ->pluck('price', 'unit_type_id')
                ->map(fn ($v) => (float) $v)
                ->toArray();
        }

        if (Schema::hasTable('stock_ins')) {
            $stockInPrices = DB::table('stock_ins')
                ->where('product_id', (int) $product->id)
                ->where('branch_id', (int) $posBranchId)
                ->whereNotNull('unit_type_id')
                ->where('price', '>', 0)
                ->orderByDesc('id')
                ->get(['unit_type_id', 'price']);

            foreach ($stockInPrices as $r) {
                $ut = (int) $r->unit_type_id;
                $p = (float) $r->price;
                if ($ut <= 0 || $p <= 0) {
                    continue;
                }
                if (! isset($unitPriceById[$ut]) || (float) $unitPriceById[$ut] <= 0) {
                    $unitPriceById[$ut] = $p;
                }
            }
        }

        $stockUnits = $unitRows->map(function ($row) use ($totalStock, $unitPriceById, $availableByUnitTypeId) {
            $factor = (float) ($row->conversion_factor ?? 1);
            $factor = $factor > 0 ? $factor : 1;

            $unitTypeId = (int) $row->unit_type_id;
            $unitStock = 0.0;
            if (array_key_exists($unitTypeId, $availableByUnitTypeId)) {
                $unitStock = (float) $availableByUnitTypeId[$unitTypeId];
            } else {
                $unitStock = (float) $totalStock / $factor;
            }

            return [
                'unit_type_id' => $unitTypeId,
                'unit_name' => $row->unit_name,
                'stock' => $unitStock,
                'price' => (float) ($unitPriceById[$unitTypeId] ?? 0),
            ];
        })->values()->toArray();

        $price = 0.0;
        if (! empty($unitPriceById)) {
            $baseUnit = $unitRows->firstWhere('is_base', 1);
            if ($baseUnit) {
                $price = (float) ($unitPriceById[(int) $baseUnit->unit_type_id] ?? 0);
            }
            if ($price <= 0) {
                $price = (float) (collect($unitPriceById)->first() ?? 0);
            }
        }

        $branches = [
            [
                'branch_id' => $posBranchId,
                'branch_name' => (string) (DB::table('branches')->where('id', (int) $posBranchId)->value('branch_name') ?? 'Branch'),
                'stock' => $totalStock,
                'price' => $price,
                'stock_units' => $stockUnits,
            ],
        ];

        return response()->json([
            'success' => true,
            'products' => [[
                'id' => $product->id,
                'product_name' => $product->product_name,
                'barcode' => $product->barcode,
                'model_number' => $product->model_number,
                'selling_price' => $price, // Use price from StockIn
                'total_stock' => $totalStock,
                'branches' => $branches,
                'warranty_coverage_months' => (int) ($product->warranty_coverage_months ?? 0),
                'warranty_type' => $product->warranty_type ?? 'none',
                'category_type' => $product->category?->category_type ?? 'non_electronic',
            ]],
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

            $inventory = app(InventoryService::class);

            $total = $request->input('total');
            $paymentMethod = $request->input('payment_method');
            $customerName = $request->input('customer_name');
            $requestedOrderStatus = $request->input('order_status', 'completed');
            $notes = $request->filled('notes') ? trim((string) $request->input('notes')) : null;
            $creditDueDate = $request->input('credit_due_date');
            $creditNotes = $request->input('credit_notes');
            $items = $request->input('products');

            if (empty($items) || ! is_array($items)) {
                return response()->json(['success' => false, 'message' => 'No items provided for sale']);
            }

            $finalStatus = in_array($requestedOrderStatus, ['completed', 'pending'], true) ? $requestedOrderStatus : 'completed';

            // Create sale record
            $salePayload = [
                'branch_id' => $branchId,
                'cashier_id' => $user->id,
                'total_amount' => $total,
                'payment_method' => $paymentMethod,
                'customer_name' => $customerName,
                'status' => $finalStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($notes !== null && $notes !== '' && Schema::hasColumn('sales', 'notes')) {
                $salePayload['notes'] = $notes;
            }

            $sale = Sale::create($salePayload);

            $posBranchId = (int) $branchId;

            // Create sale items and update stock per the three-mode rules
            foreach ($items as $item) {
                $productId = (int) ($item['id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                $price = (float) ($item['price'] ?? 0);
                $unitTypeId = isset($item['unit_type_id']) ? (int) $item['unit_type_id'] : null;

                if (! $productId || $quantity <= 0) {
                    DB::rollBack();

                    return response()->json(['success' => false, 'message' => 'Invalid item payload.']);
                }

                // If no unit_type_id provided, try to resolve from stock_ins for this product/branch
                if (empty($unitTypeId)) {
                    $fallbackUnitTypeId = DB::table('stock_ins')
                        ->where('product_id', $productId)
                        ->where('branch_id', $posBranchId)
                        ->whereNotNull('unit_type_id')
                        ->orderBy('id')
                        ->value('unit_type_id');

                    if ($fallbackUnitTypeId) {
                        $unitTypeId = (int) $fallbackUnitTypeId;
                    } else {
                        DB::rollBack();

                        return response()->json(['success' => false, 'message' => 'Unit type is required for each item.'], 422);
                    }
                }

                $serialNumber = isset($item['serial_number']) ? trim((string) $item['serial_number']) : null;
                $warrantyMonths = isset($item['warranty_months']) ? (int) $item['warranty_months'] : 0;

                $subtotal = $quantity * $price;

                $saleItemPayload = [
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ];
                if (Schema::hasColumn('sale_items', 'unit_type_id')) {
                    $saleItemPayload['unit_type_id'] = $unitTypeId;
                }
                if (Schema::hasColumn('sale_items', 'warranty_months')) {
                    $saleItemPayload['warranty_months'] = $warrantyMonths;
                }

                $saleItem = SaleItem::create($saleItemPayload);

                // PENDING MODE — no stock/serial processing
                if ($finalStatus === 'pending') {
                    continue;
                }

                // COMPLETED MODE — determine available stock for this item
                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) $unitTypeId)
                    ->value('conversion_factor') ?? 1);
                $factor = $factor > 0 ? $factor : 1;

                $baseQty = (float) $quantity * $factor;
                $availableBase = (float) (DB::table('branch_stocks')
                    ->where('product_id', (int) $productId)
                    ->where('branch_id', (int) $posBranchId)
                    ->value('quantity_base') ?? 0);
                $fulfillBase = min($baseQty, $availableBase);

                // Deduct from branch_stocks and record stock movement
                if ($fulfillBase > 0) {
                    DB::table('branch_stocks')
                        ->where('product_id', (int) $productId)
                        ->where('branch_id', (int) $posBranchId)
                        ->decrement('quantity_base', $fulfillBase);

                    DB::table('stock_movements')->insert([
                        'product_id' => (int) $productId,
                        'branch_id' => (int) $posBranchId,
                        'movement_type' => 'sale',
                        'source_type' => 'sales',
                        'source_id' => (int) $sale->id,
                        'quantity_base' => -$fulfillBase,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Update sold count on stock_ins rows (FIFO)
                    $fulfillQty = $fulfillBase / $factor;
                    $remaining = $fulfillQty;

                    $stockInRows = DB::table('stock_ins')
                        ->where('product_id', (int) $productId)
                        ->where('branch_id', (int) $posBranchId)
                        ->whereRaw('(quantity - COALESCE(sold, 0)) > 0')
                        ->orderBy('id')
                        ->lockForUpdate()
                        ->get(['id', 'quantity', 'sold']);

                    foreach ($stockInRows as $row) {
                        if ($remaining <= 0) {
                            break;
                        }
                        $available = (float) $row->quantity - (float) ($row->sold ?? 0);
                        $consumeNow = min($available, $remaining);
                        DB::table('stock_ins')->where('id', $row->id)
                            ->update(['sold' => (float) ($row->sold ?? 0) + $consumeNow, 'updated_at' => now()]);
                        $remaining -= $consumeNow;
                    }
                }

                // Serial processing — only if serial provided and stock was available
                if (! empty($serialNumber) && $fulfillBase > 0) {
                    $serial = ProductSerial::where('serial_number', $serialNumber)->first();
                    if (! $serial) {
                        DB::rollBack();

                        return response()->json(['success' => false, 'message' => 'Invalid serial number (not found in inventory).'], 422);
                    }
                    if ((int) $serial->product_id !== $productId) {
                        DB::rollBack();

                        return response()->json(['success' => false, 'message' => 'Serial number does not match the selected product.'], 422);
                    }
                    if (! in_array($serial->status, ['in_stock', 'purchased'], true)) {
                        DB::rollBack();

                        return response()->json(['success' => false, 'message' => 'Serial number is not available (already sold/invalid).'], 422);
                    }

                    $serial->status = 'sold';
                    $serial->sold_at = now();
                    $serial->sale_item_id = $saleItem->id;
                    $serial->branch_id = $branchId;
                    $serial->warranty_expiry_date = $warrantyMonths > 0
                        ? Carbon::now()->addMonths($warrantyMonths)->toDateString()
                        : null;
                    $serial->save();

                    // Activate warranty record for this serial
                    $customerId = $sale->customer_id ?? null;
                    app(\App\Services\WarrantyService::class)->activateForSale($saleItem, (int) $branchId, $customerId, $serial);
                } elseif ($fulfillBase > 0) {
                    // Non-serial item with stock fulfilled — create warranty record at sale time
                    $customerId = $sale->customer_id ?? null;
                    app(\App\Services\WarrantyService::class)->activateForSale($saleItem, (int) $branchId, $customerId, null);
                }
            }

            // Create credit record if needed
            if ($paymentMethod === 'credit' && $customerName) {
                // Use CustomerService to create/find customer and credit
                \App\Services\CustomerService::createCredit([
                    'customer' => [
                        'full_name' => $customerName,
                        'phone' => null, // Can be enhanced to capture phone from POS
                        'email' => null,
                        'address' => null,
                    ],
                    'credit_amount' => $total,
                    'sale_id' => $sale->id,
                    'status' => 'active',
                    'date' => $creditDueDate ?? now()->addDays(30),
                    'description' => $creditNotes ?? 'Credit from POS Sale #'.$sale->id,
                    'credit_type' => 'sales',
                ], $branchId, $user->id);
            }

            DB::commit();

            // Generate receipt URL if cash payment
            $receiptUrl = null;
            $autoReceipt = $paymentMethod === 'cash';

            $receiptPdfUrl = route('cashier.pos.receipt.pdf', $sale->id);

            if ($autoReceipt) {
                $receiptUrl = route('cashier.pos.receipt', $sale->id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id,
                'auto_receipt' => $autoReceipt,
                'receipt_url' => $receiptUrl,
                'receipt_pdf_url' => $receiptPdfUrl,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Cashier POS store error: '.$e->getMessage());

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
                'creditsByCustomer' => $creditsByCustomer,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading credit limits data: '.$e->getMessage());

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
                'customers.*.max_credit_limit' => 'required|numeric|min:0',
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
            Log::error('Error updating credit limits: '.$e->getMessage());

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

    public function receiptPdf($saleId)
    {
        $sale = Sale::with(['saleItems.product', 'saleItems.unitType', 'cashier', 'branch', 'customer'])->findOrFail($saleId);

        $serialsBySaleItemId = ProductSerial::whereIn('sale_item_id', $sale->saleItems->pluck('id')->filter())
            ->get()
            ->keyBy('sale_item_id');

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('cashier.sales.receipt_pdf', compact('sale', 'serialsBySaleItemId'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'receipt-'.($sale->reference_number ?: $sale->id).'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
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
                'message' => 'Supplier added successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating supplier: '.$e->getMessage());

            return response()->json([
                'message' => 'Error creating supplier. Please try again.',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified credit.
     */
    public function creditEdit($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $credit = Credit::where('id', $id)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        // Get all customers for the dropdown
        $customers = \App\Models\Customer::where('status', 'active')
            ->orderBy('full_name', 'asc')
            ->get();

        return view('cashier.credit.edit', compact('credit', 'customers'));
    }

    /**
     * Update the specified credit in storage.
     */
    public function creditUpdate(Request $request, $id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $credit = Credit::where('id', $id)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        // New validation to match the updated form (customer + contact info)
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($credit, $validated) {
                // Update which customer this credit is linked to
                $credit->update([
                    'customer_id' => $validated['customer_id'],
                ]);

                // Update customer contact information if we have a related customer
                if ($credit->customer) {
                    $customerData = [];

                    if (! empty($validated['email'])) {
                        $customerData['email'] = $validated['email'];
                    }

                    if (! empty($validated['phone_number'])) {
                        $customerData['phone'] = $validated['phone_number'];
                    }

                    if (! empty($validated['address'])) {
                        $customerData['address'] = $validated['address'];
                    }

                    if (! empty($customerData)) {
                        $credit->customer->update($customerData);
                    }
                }
            });

            // Check if it's an AJAX request (multiple methods)
            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

            // Debug logging
            Log::info('Credit Update Request:', [
                'ajax' => $request->ajax(),
                'wantsJson' => $request->wantsJson(),
                'xRequestedWith' => $request->header('X-Requested-With'),
                'isAjax' => $isAjax,
                'headers' => $request->headers->all(),
            ]);

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Credit updated successfully!',
                ]);
            }

            return redirect()->route('cashier.credit.index')
                ->with('success', 'Credit updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating credit: '.$e->getMessage());

            // Check if it's an AJAX request (multiple methods)
            $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating credit. Please try again.',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error updating credit. Please try again.');
        }
    }

    /**
     * Remove the specified credit from storage.
     */
    public function creditDestroy($id)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $credit = Credit::where('id', $id)
            ->where('branch_id', $branchId)
            ->firstOrFail();

        try {
            $credit->delete();

            return redirect()->route('cashier.credit.index')
                ->with('success', 'Credit deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting credit: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Error deleting credit. Please try again.');
        }
    }
}
