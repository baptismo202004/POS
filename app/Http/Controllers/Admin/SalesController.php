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
        
        return response()->json(['items' => $itemsData]);
    }

    public function index()
    {
        // Get today's sales data
        $today = Carbon::today();
        
        $todaySales = Sale::whereDate('created_at', $today)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get today's sales items count
        $todayItems = SaleItem::whereHas('sale', function($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->sum('quantity');
        
        // Get this month's sales
        $thisMonth = Carbon::now()->startOfMonth();
        $monthlySales = Sale::whereDate('created_at', '>=', $thisMonth)
            ->selectRaw('COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue')
            ->first();
        
        // Get recent sales for the table
        $recentSales = Sale::with(['saleItems.product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
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
            'recentSales'
        ));
    }
}
