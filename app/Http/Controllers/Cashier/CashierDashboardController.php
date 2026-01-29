<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
        $lowStockCount = DB::table('stock_ins as si')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->where('si.branch_id', $branchId)
            ->whereRaw('(si.quantity - si.sold) <= 10') // Assuming 10 as low stock threshold
            ->count();

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

        return view('cashier.dashboard', compact(
            'branch',
            'todaySales',
            'todayTransactions',
            'todayExpenses',
            'lowStockCount',
            'recentSales',
            'topProducts',
            'labels',
            'salesData'
        ));
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
}
