<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        // Get the date from request or default to today
        $selectedDate = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
        
        // Get sales data for the selected date
        $todaySales = Sale::whereDate('created_at', $selectedDate)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get sales items count for the selected date
        $todayItems = SaleItem::whereHas('sale', function($query) use ($selectedDate) {
            $query->whereDate('created_at', $selectedDate);
        })->sum('quantity');
        
        // Get this month's sales
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlySales = Sale::whereDate('created_at', '>=', $thisMonth)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get recent sales for the table (showing yesterday, today, and tomorrow)
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
            'selectedDate'
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
}
