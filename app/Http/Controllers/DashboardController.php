<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DatePeriod;
use DateInterval;

class DashboardController extends Controller
{
    /**
     * Chart data endpoint returning labels, sales, and profit arrays.
     * Profit is approximated as sales - expenses per day.
     */
    public function chartData(Request $request)
    {
        $type = $request->query('type', 'sales'); // currently unused; both arrays returned

        $end = Carbon::today();
        $start = $end->copy()->subDays(6);

        // Build daily periods and keys
        $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
        $labels = [];
        $keys = [];
        foreach ($period as $d) {
            $labels[] = $d->format('D');
            $keys[] = $d->format('Y-m-d');
        }

        // Aggregate sales and expenses by day
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

        $sales = [];
        $profit = [];
        foreach ($keys as $k) {
            $daySales = (float) ($salesRows[$k] ?? 0);
            $dayExpenses = (float) ($expenseRows[$k] ?? 0);
            $sales[] = $daySales;
            $profit[] = $daySales - $dayExpenses;
        }

        return response()->json([
            'labels' => $labels,
            'sales' => $sales,
            'profit' => $profit,
        ]);
    }

    /**
     * Get monthly sales data for the current month
     */
    public function monthlySales()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // Get total sales for current month
        $totalSales = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        // Get total expenses for current month
        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        // Get Cost of Goods Sold (COGS) for current month
        $cogs = DB::table('sale_items')
            ->join('stock_ins', 'sale_items.product_id', '=', 'stock_ins.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->sum(DB::raw('stock_ins.price * sale_items.quantity'));

        // Get transaction count for current month
        $transactionCount = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Calculate profit with COGS
        $grossProfit = $totalSales - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        // Get previous month data for comparison
        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousSales = DB::table('sales')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total_amount');

        // Calculate percentage change
        $salesChange = $previousSales > 0 ? (($totalSales - $previousSales) / $previousSales) * 100 : 0;

        return response()->json([
            'total_sales' => (float) $totalSales,
            'total_expenses' => (float) $totalExpenses,
            'cogs' => (float) $cogs,
            'gross_profit' => (float) $grossProfit,
            'net_profit' => (float) $netProfit,
            'transaction_count' => $transactionCount,
            'sales_change_percentage' => round($salesChange, 2),
            'month' => Carbon::now()->format('F Y'),
        ]);
    }

    /**
     * Get monthly returns data for the current month
     */
    public function monthlyReturns()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // Debug: Check if refunds table has data
        $allRefunds = DB::table('refunds')->get();
        \Log::info('All refunds in database: ' . $allRefunds->count());
        
        // Debug: Check current month refunds
        $currentMonthRefunds = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->get();
        \Log::info('Current month refunds: ' . $currentMonthRefunds->count());
        
        // Get total returns/refunds for current month
        $totalReturns = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->sum('refund_amount');

        // Get total items returned for current month
        $totalItemsReturned = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->sum('quantity_refunded');

        // Get return count for current month
        $returnCount = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->count();

        // Get previous month data for comparison
        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousReturns = DB::table('refunds')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('status', 'approved')
            ->sum('refund_amount');

        // Calculate percentage change
        $returnsChange = $previousReturns > 0 ? (($totalReturns - $previousReturns) / $previousReturns) * 100 : 0;

        \Log::info('Monthly returns calculation:', [
            'total_returns' => $totalReturns,
            'total_items_returned' => $totalItemsReturned,
            'return_count' => $returnCount,
            'returns_change_percentage' => $returnsChange
        ]);

        return response()->json([
            'total_returns' => (float) $totalReturns,
            'total_items_returned' => $totalItemsReturned,
            'return_count' => $returnCount,
            'returns_change_percentage' => round($returnsChange, 2),
            'month' => Carbon::now()->format('F Y'),
        ]);
    }

    /**
     * Get monthly profit data for the current month
     */
    public function monthlyProfit()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // Get total sales for current month
        $totalSales = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        // Get total expenses for current month
        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        // Get Cost of Goods Sold (COGS) for current month
        $cogs = DB::table('sale_items')
            ->join('stock_ins', 'sale_items.product_id', '=', 'stock_ins.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->sum(DB::raw('stock_ins.price * sale_items.quantity'));

        // Calculate profit with COGS
        $grossProfit = $totalSales - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        // Get previous month data for comparison
        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        // Get previous month COGS
        $previousCogs = DB::table('sale_items')
            ->join('stock_ins', 'sale_items.product_id', '=', 'stock_ins.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$previousStart, $previousEnd])
            ->sum(DB::raw('stock_ins.price * sale_items.quantity'));

        $previousExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->sum('amount');

        $previousSales = DB::table('sales')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total_amount');

        $previousGrossProfit = $previousSales - $previousCogs;
        $previousNetProfit = $previousGrossProfit - $previousExpenses;

        // Calculate percentage change
        $profitChange = $previousNetProfit > 0 ? (($netProfit - $previousNetProfit) / $previousNetProfit) * 100 : 0;

        return response()->json([
            'total_sales' => (float) $totalSales,
            'total_expenses' => (float) $totalExpenses,
            'cogs' => (float) $cogs,
            'gross_profit' => (float) $grossProfit,
            'net_profit' => (float) $netProfit,
            'profit_change_percentage' => round($profitChange, 2),
            'month' => Carbon::now()->format('F Y'),
        ]);
    }

    /**
     * Get monthly expenses data for the current month
     */
    public function monthlyExpenses()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        // Get total expenses for current month
        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        // Get previous month data for comparison
        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->sum('amount');

        // Calculate percentage change
        $expensesChange = $previousExpenses > 0 ? (($totalExpenses - $previousExpenses) / $previousExpenses) * 100 : 0;

        return response()->json([
            'total_expenses' => (float) $totalExpenses,
            'expenses_change_percentage' => round($expensesChange, 2),
            'month' => Carbon::now()->format('F Y'),
        ]);
    }
}
