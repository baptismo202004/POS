<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSerial;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Traits\ScopesByBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    use ScopesByBranch;

    public function getSaleItems(Sale $sale)
    {
        $sale->load(['cashier', 'branch', 'customer']);
        $items = $sale->saleItems()->with(['product', 'refunds'])->get();

        $serialsBySaleItemId = ProductSerial::whereIn('sale_item_id', $items->pluck('id')->filter())
            ->get()
            ->keyBy('sale_item_id');

        $totalRefunds = $sale->refunds()->where('status', 'approved')->sum('refund_amount');

        $itemsData = $items->map(function ($item) use ($serialsBySaleItemId) {
            $refundedQuantity = $item->refunds()->where('status', 'approved')->sum('quantity_refunded');

            $serial = $serialsBySaleItemId[$item->id] ?? null;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'quantity' => $item->quantity,
                'refunded' => $refundedQuantity,
                'unit_price' => $item->unit_price,
                'available_for_refund' => $item->quantity - $refundedQuantity,
                'warranty_months' => $item->warranty_months ?? null,
                'warranty_start' => $serial && $serial->sold_at ? Carbon::parse((string) $serial->sold_at)->toIso8601String() : null,
                'warranty_expiry_date' => $serial && $serial->warranty_expiry_date ? Carbon::parse((string) $serial->warranty_expiry_date)->toIso8601String() : null,
            ];
        });

        return response()->json([
            'items' => $itemsData,
            'sale' => [
                'id' => $sale->id,
                'reference_number' => $sale->reference_number,
                'created_at' => optional($sale->created_at)->toIso8601String(),
                'status' => $sale->status,
                'payment_method' => $sale->payment_method,
                'total_amount' => $sale->total_amount,
                'branch_name' => optional($sale->branch)->branch_name,
                'cashier_name' => optional($sale->cashier)->name,
                'customer' => $sale->customer ? [
                    'full_name' => $sale->customer->full_name,
                    'company_school_name' => $sale->customer->company_school_name,
                    'phone' => $sale->customer->phone,
                    'email' => $sale->customer->email,
                    'facebook' => $sale->customer->facebook,
                    'address' => $sale->customer->address,
                ] : null,
            ],
            'sale_info' => [
                'original_total' => $sale->total_amount + $totalRefunds,
                'current_total' => $sale->total_amount,
                'total_refunded' => $totalRefunds,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $selectedDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        $filter = $request->get('filter', 'all');
        $excludeVoided = $filter !== 'voided';
        $branchIds = $this->accessibleBranchIds();

        $todaySalesQuery = Sale::whereDate('created_at', $selectedDate)
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds));
        if ($excludeVoided) {
            $todaySalesQuery->where('status', '!=', 'voided');
        }
        $todaySales = $todaySalesQuery
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $todayItems = SaleItem::whereHas('sale', function ($query) use ($selectedDate, $excludeVoided, $branchIds) {
            $query->whereDate('created_at', $selectedDate)
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds));
            if ($excludeVoided) {
                $query->where('status', '!=', 'voided');
            }
        })->sum('quantity');

        $thisMonth = Carbon::now()->startOfMonth();
        $monthlySalesQuery = Sale::whereDate('created_at', '>=', $thisMonth)
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds));
        if ($excludeVoided) {
            $monthlySalesQuery->where('status', '!=', 'voided');
        }
        $monthlySales = $monthlySalesQuery
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $recentSalesQuery = Sale::with(['saleItems.product', 'cashier'])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->orderBy('created_at', 'desc');

        if ($excludeVoided) {
            $recentSalesQuery->where('status', '!=', 'voided');
        }

        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::tomorrow()->endOfDay();

        if ($request->get('date')) {
            $selectedDate = Carbon::parse($request->get('date'));
            $startDate = $selectedDate->copy()->startOfDay();
            $endDate = $selectedDate->copy()->endOfDay();
        }

        $recentSalesQuery->whereBetween('created_at', [$startDate, $endDate]);

        if ($filter !== 'all') {
            switch ($filter) {
                case 'below-price':
                case 'below-cost':
                    $recentSalesQuery->whereHas('saleItems', function ($query) {
                        $query->whereRaw(
                            '(sale_items.unit_price * sale_items.quantity) < (sale_items.quantity * COALESCE((
                                SELECT pi.unit_cost
                                FROM purchase_items pi
                                JOIN purchases p ON p.id = pi.purchase_id
                                WHERE pi.product_id = sale_items.product_id
                                  AND p.branch_id = sales.branch_id
                                  AND p.created_at <= sales.created_at
                                ORDER BY p.created_at DESC, pi.id DESC
                                LIMIT 1
                            ), 0))'
                        );
                    });
                    break;
                case 'voided':
                    $recentSalesQuery->where('status', 'voided');
                    break;
            }
        }

        $recentSales = $recentSalesQuery->get()
            ->map(function ($sale) {
                $sale->product_names = $sale->saleItems->map(function ($item) {
                    return $item->product ? $item->product->product_name : 'Unknown Product';
                })->filter()->join(', ');

                return $sale;
            });

        return view('Admin.sales.index', compact(
            'todaySales',
            'todayItems',
            'monthlySales',
            'recentSales',
            'selectedDate',
            'filter'
        ));
    }

    public function getItemsSoldToday()
    {
        $today = Carbon::today();

        $items = SaleItem::with(['product', 'sale.branch'])
            ->whereHas('sale', function ($query) use ($today) {
                $query->whereDate('created_at', $today);
            })
            ->get()
            ->map(function ($item) {
                return [
                    'product_name' => $item->product ? $item->product->product_name : 'Unknown Product',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->quantity * $item->unit_price,
                    'branch_name' => $item->sale && $item->sale->branch ? $item->sale->branch->branch_name : 'N/A',
                    'created_at' => $item->created_at,
                ];
            });

        return response()->json(['items' => $items]);
    }

    public function getGraphData()
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(14);

        $salesData = Sale::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_sales, COUNT(*) as total_orders')
            ->groupBy('DATE(created_at)')
            ->orderBy('date')
            ->get();

        $completeData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayData = $salesData->firstWhere('date', $dateStr);

            $completeData[] = [
                'date' => $currentDate->format('M j'),
                'sales' => $dayData ? (float) $dayData->total_sales : 0,
                'orders' => $dayData ? (int) $dayData->total_orders : 0,
            ];

            $currentDate->addDay();
        }

        return response()->json($completeData);
    }

    public function getTodaysRevenue()
    {
        $today = Carbon::today();

        $sales = Sale::with(['branch'])
            ->whereDate('created_at', $today)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalTransactions = $sales->count();

        $salesData = $sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'created_at' => $sale->created_at,
                'total_amount' => $sale->total_amount,
                'branch_name' => $sale->branch ? $sale->branch->branch_name : 'N/A',
            ];
        });

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'sales' => $salesData,
        ]);
    }

    public function getThisMonthSales()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $today = Carbon::today();

        $sales = Sale::whereDate('created_at', '>=', $thisMonth)
            ->whereDate('created_at', '<=', $today)
            ->get();

        $totalRevenue = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $averageSale = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        $dailySales = $sales->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m-d');
        })->map(function ($daySales, $date) {
            $dayTotal = $daySales->sum('total_amount');
            $dayTransactions = $daySales->count();

            return [
                'date' => $date,
                'total_sales' => $dayTotal,
                'transactions' => $dayTransactions,
                'average' => $dayTransactions > 0 ? $dayTotal / $dayTransactions : 0,
            ];
        })->sortBy('date')->values();

        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_sale' => $averageSale,
            'daily_sales' => $dailySales,
        ]);
    }

    public function management(Request $request)
    {
        $selectedDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();

        $user = auth()->user();
        $branchIds = $user->accessibleBranchIds();

        $branchScope = function ($q) use ($branchIds) {
            if (! empty($branchIds)) {
                $q->whereIn('branch_id', $branchIds);
            }
        };

        $todaySales = Sale::whereDate('created_at', $selectedDate)
            ->where('payment_method', '!=', 'credit')
            ->where('status', '!=', 'voided')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $todayItems = SaleItem::whereHas('sale', function ($query) use ($selectedDate, $branchIds) {
            $query->whereDate('created_at', $selectedDate)
                ->where('status', '!=', 'voided')
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds));
        })->sum('quantity');

        $thisMonth = Carbon::now()->startOfMonth();
        $monthlySales = Sale::whereDate('created_at', '>=', $thisMonth)
            ->where('status', '!=', 'voided')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $recentSalesQuery = Sale::with(['saleItems.product', 'cashier'])
            ->where('status', '!=', 'voided')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->orderBy('created_at', 'desc');

        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::tomorrow()->endOfDay();

        if ($request->get('date')) {
            $selectedDate = Carbon::parse($request->get('date'));
            $startDate = $selectedDate->copy()->startOfDay();
            $endDate = $selectedDate->copy()->endOfDay();
        }

        $recentSalesQuery->whereBetween('created_at', [$startDate, $endDate]);

        $recentSales = $recentSalesQuery->get()
            ->map(function ($sale) {
                $productNames = $sale->saleItems->map(function ($item) {
                    return $item->product ? $item->product->product_name : 'Unknown Product';
                })->filter();

                $sale->product_names = $productNames->isNotEmpty() ? $productNames->join(', ') : 'No products';

                return $sale;
            });

        $allBranchesTodaySales = Sale::whereDate('created_at', Carbon::today())
            ->where('payment_method', '!=', 'credit')
            ->where('status', '!=', 'voided')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();

        $todayCredits = \App\Models\Credit::whereDate('created_at', Carbon::today())
            ->selectRaw('COUNT(*) as total_credits, COALESCE(SUM(credit_amount), 0) as total_amount')
            ->first();

        return view('Admin.sales.management', compact(
            'todaySales',
            'todayItems',
            'monthlySales',
            'allBranchesTodaySales',
            'todayCredits',
            'recentSales',
            'selectedDate'
        ));
    }

    public function voidSale(Sale $sale)
    {
        $sale->status = 'voided';
        $sale->voided_at = now();
        $sale->save();

        return redirect()->back()->with('success', 'Sale has been voided successfully.');
    }

    public function voidedSales(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::today();
        $endDate = Carbon::today()->endOfDay();

        $voidedSales = Sale::with(['saleItems.product', 'cashier', 'branch'])
            ->where('status', 'voided')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $voidedSales->getCollection()->each(function ($sale) {
            $sale->original_items_count = DB::table('sale_items')
                ->where('sale_id', $sale->id)
                ->sum('quantity');
        });

        $totalVoidedAmount = $voidedSales->sum('total_amount');
        $totalVoidedCount = $voidedSales->total();

        return view('Admin.sales.voided', compact(
            'voidedSales',
            'totalVoidedAmount',
            'totalVoidedCount',
            'startDate',
            'endDate'
        ));
    }

    public function belowCostSalesReport(Request $request)
    {
        $start = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $end = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::today()->endOfDay();

        $items = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('branches', 'sales.branch_id', '=', 'branches.id')
            ->leftJoin('users', 'sales.cashier_id', '=', 'users.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->select([
                'sale_items.id as sale_item_id',
                'sales.id as sale_id',
                'sales.reference_number',
                'sales.created_at',
                'products.product_name',
                'branches.branch_name',
                'users.name as cashier_name',
                'sale_items.quantity',
                'sale_items.unit_price as sold_unit_price',
                'sale_items.subtotal as sold_total',
                DB::raw('COALESCE((
                    SELECT pi.unit_cost
                    FROM purchase_items pi
                    JOIN purchases p ON p.id = pi.purchase_id
                    WHERE pi.product_id = sale_items.product_id
                      AND p.branch_id = sales.branch_id
                      AND p.created_at <= sales.created_at
                    ORDER BY p.created_at DESC, pi.id DESC
                    LIMIT 1
                ), 0) as purchase_price'),
                DB::raw('sale_items.quantity * COALESCE((
                    SELECT pi.unit_cost
                    FROM purchase_items pi
                    JOIN purchases p ON p.id = pi.purchase_id
                    WHERE pi.product_id = sale_items.product_id
                      AND p.branch_id = sales.branch_id
                      AND p.created_at <= sales.created_at
                    ORDER BY p.created_at DESC, pi.id DESC
                    LIMIT 1
                ), 0) as purchase_total'),
            ])
            ->whereRaw(
                'sale_items.quantity * COALESCE((
                    SELECT pi.unit_cost
                    FROM purchase_items pi
                    JOIN purchases p ON p.id = pi.purchase_id
                    WHERE pi.product_id = sale_items.product_id
                      AND p.branch_id = sales.branch_id
                      AND p.created_at <= sales.created_at
                    ORDER BY p.created_at DESC, pi.id DESC
                    LIMIT 1
                ), 0) > sale_items.subtotal'
            )
            ->orderByDesc('sales.created_at')
            ->paginate(50);

        return view('Admin.sales.below-cost', [
            'items' => $items,
            'startDate' => $start,
            'endDate' => $end,
        ]);
    }
}
