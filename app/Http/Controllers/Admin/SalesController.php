<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function getSaleItems(Sale $sale)
    {
        $items = $sale->saleItems()->with(['product', 'refunds'])->get();
        
        // Calculate total refunds for this sale
        $totalRefunds = $sale->refunds()->where('status', 'approved')->sum('refund_amount');
        
        $itemsData = $items->map(function ($item) {
            $refundedQuantity = $item->refunds()->where('status', 'approved')->sum('quantity_refunded');
            
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'quantity' => $item->quantity,
                'refunded' => $refundedQuantity,
                'unit_price' => $item->unit_price,
                'available_for_refund' => $item->quantity - $refundedQuantity
            ];
        });
        
        return response()->json([
            'items' => $itemsData,
            'sale_info' => [
                'original_total' => $sale->total_amount + $totalRefunds, // Original amount before refunds
                'current_total' => $sale->total_amount, // Current amount after refunds
                'total_refunded' => $totalRefunds
            ]
        ]);
    }

    public function index(Request $request)
    {
        // Get date from request or default to today
        $selectedDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        // Get filter type from request
        $filter = $request->get('filter', 'all');
        
        // Get sales data for selected date
        $todaySales = Sale::whereDate('created_at', $selectedDate)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get sales items count for selected date
        $todayItems = SaleItem::whereHas('sale', function($query) use ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        })->sum('quantity');
        
        // Get this month's sales
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlySales = Sale::whereDate('created_at', '>=', $thisMonth)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get recent sales for table (showing yesterday, today, and tomorrow)
        $recentSalesQuery = Sale::with(['saleItems.product', 'cashier'])
            ->orderBy('created_at', 'desc');
            
        // Default: show sales from yesterday, today, and tomorrow
        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::tomorrow()->endOfDay();
        
        if ($request->get('date')) {
            // If date is specified, show sales for that date
            $selectedDate = Carbon::parse($request->get('date'));
            $startDate = $selectedDate->copy()->startOfDay();
            $endDate = $selectedDate->copy()->endOfDay();
        }
        
        $recentSalesQuery->whereBetween('created_at', [$startDate, $endDate]);
        
        // Apply filter based on alert type
        if ($filter !== 'all') {
            switch ($filter) {
                case 'below-price':
                    $recentSalesQuery->whereHas('saleItems', function($query) {
                        $query->whereRaw('(sale_items.unit_price * sale_items.quantity) < (SELECT AVG(stock_ins.price) FROM stock_ins JOIN sale_items ON stock_ins.product_id = sale_items.product_id WHERE stock_ins.created_at <= sales.created_at LIMIT 1)');
                    });
                    break;
                case 'below-cost':
                    $recentSalesQuery->whereHas('saleItems', function($query) {
                        $query->whereRaw('(sale_items.unit_price * sale_items.quantity) < (SELECT AVG(stock_ins.price) FROM stock_ins JOIN sale_items ON stock_ins.product_id = sale_items.product_id WHERE stock_ins.created_at <= sales.created_at LIMIT 1)');
                    });
                    break;
                case 'voided':
                    $recentSalesQuery->where('voided', true);
                    break;
                case 'high-discount':
                    // Skip high-discount filter as column doesn't exist
                    break;
            }
        }
        
        $recentSales = $recentSalesQuery->get()
            ->map(function ($sale) {
                // Ensure product names are properly loaded
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
            ->whereHas('sale', function($query) use ($today) {
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
                    'created_at' => $item->created_at
                ];
            });
        
        return response()->json(['items' => $items]);
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
                'branch_name' => $sale->branch ? $sale->branch->branch_name : 'N/A'
            ];
        });
        
        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'sales' => $salesData
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
        
        // Group by date for daily breakdown
        $dailySales = $sales->groupBy(function ($sale) {
            return $sale->created_at->format('Y-m-d');
        })->map(function ($daySales, $date) {
            $dayTotal = $daySales->sum('total_amount');
            $dayTransactions = $daySales->count();
            $dayAverage = $dayTransactions > 0 ? $dayTotal / $dayTransactions : 0;
            
            return [
                'date' => $date,
                'total_sales' => $dayTotal,
                'transactions' => $dayTransactions,
                'average' => $dayAverage
            ];
        })->sortBy('date')->values();
        
        return response()->json([
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'average_sale' => $averageSale,
            'daily_sales' => $dailySales
        ]);
    }
    
    public function management(Request $request)
    {
        // Get date from request or default to today
        $selectedDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        // Get sales data for selected date
        $todaySales = Sale::whereDate('created_at', $selectedDate)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get sales items count for selected date
        $todayItems = SaleItem::whereHas('sale', function($query) use ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        })->sum('quantity');
        
        // Get this month's sales
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlySales = Sale::whereDate('created_at', '>=', $thisMonth)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get recent sales for table (showing yesterday, today, and tomorrow)
        $recentSalesQuery = Sale::with(['saleItems.product', 'cashier'])
            ->orderBy('created_at', 'desc');
            
        // Default: show sales from yesterday, today, and tomorrow
        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::tomorrow()->endOfDay();
        
        if ($request->get('date')) {
            // If date is specified, show sales for that date
            $selectedDate = Carbon::parse($request->get('date'));
            $startDate = $selectedDate->copy()->startOfDay();
            $endDate = $selectedDate->copy()->endOfDay();
        }
        
        $recentSalesQuery->whereBetween('created_at', [$startDate, $endDate]);
        
        $recentSales = $recentSalesQuery->get()
            ->map(function ($sale) {
                // Ensure product names are properly loaded
                $productNames = $sale->saleItems->map(function ($item) {
                    return $item->product ? $item->product->product_name : 'Unknown Product';
                })->filter();
                
                $sale->product_names = $productNames->isNotEmpty() ? $productNames->join(', ') : 'No products';
                return $sale;
            });
        
        // Get all branches today's sales
        $allBranchesTodaySales = Sale::whereDate('created_at', Carbon::today())
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        return view('Admin.sales.management', compact(
            'todaySales',
            'todayItems', 
            'monthlySales',
            'allBranchesTodaySales',
            'recentSales',
            'selectedDate'
        ));
    }
    
    public function voidSale(Sale $sale)
    {
        $sale->voided = true;
        $sale->save();
        
        return redirect()->back()->with('success', 'Sale has been voided successfully.');
    }
    
    public function voidedSales(Request $request)
    {
        // Get date range from request
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::today();
        $endDate = Carbon::today()->endOfDay(); // Always go to today
        
        // Get voided sales with relationships
        $voidedSales = Sale::with(['saleItems.product', 'cashier', 'branch'])
            ->where('voided', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Debug: Log what sales we found
        \Log::info("Voided sales query returned: " . $voidedSales->count() . " sales");
        foreach ($voidedSales as $sale) {
            \Log::info("Sale ID: {$sale->id}, Items count: " . $sale->saleItems->count());
        }
        
        // For voided sales, also get original items count
        $voidedSales->getCollection()->each(function ($sale) {
            $originalItemsCount = DB::table('sale_items')
                ->where('sale_id', $sale->id)
                ->sum('quantity');
            $sale->original_items_count = $originalItemsCount;
            
            // Debug logging
            \Log::info("Sale ID {$sale->id}: Original items count = {$originalItemsCount}");
        });
        
        // Calculate totals
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
}
