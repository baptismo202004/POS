<?php

namespace App\Http\Controllers;

use App\Traits\ScopesByBranch;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    use ScopesByBranch;

    /**
     * Chart data endpoint returning labels, sales, and profit arrays.
     * Profit is approximated as sales - expenses per day.
     */
    public function chartData(Request $request)
    {
        $type = $request->query('type', 'sales');
        $branchIds = $this->accessibleBranchIds();

        $end = Carbon::today();
        $start = $end->copy()->subDays(6);

        $period = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
        $labels = [];
        $keys = [];
        foreach ($period as $d) {
            $labels[] = $d->format('D');
            $keys[] = $d->format('Y-m-d');
        }

        $salesRows = DB::table('sales')
            ->selectRaw('DATE(created_at) as d, SUM(total_amount) as s')
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->groupBy('d')
            ->pluck('s', 'd');

        $expenseRows = DB::table('expenses')
            ->selectRaw('expense_date as d, SUM(amount) as s')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
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
        $branchIds = $this->accessibleBranchIds();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalSales = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('total_amount');

        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');

        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoinSub(
                DB::table('purchase_items')->selectRaw('product_id, MAX(id) as last_purchase_item_id')->groupBy('product_id'),
                'last_purchase',
                fn ($join) => $join->on('sale_items.product_id', '=', 'last_purchase.product_id')
            )
            ->leftJoin('purchase_items as pi', 'pi.id', '=', 'last_purchase.last_purchase_item_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('sales.branch_id', $branchIds))
            ->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

        $transactionCount = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->count();

        $grossProfit = $totalSales - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousSales = DB::table('sales')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('total_amount');

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
        $branchIds = $this->accessibleBranchIds();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalReturns = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('sale_id', DB::table('sales')->whereIn('branch_id', $branchIds)->pluck('id')))
            ->sum('refund_amount');

        $totalItemsReturned = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('sale_id', DB::table('sales')->whereIn('branch_id', $branchIds)->pluck('id')))
            ->sum('quantity_refunded');

        $returnCount = DB::table('refunds')
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'approved')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('sale_id', DB::table('sales')->whereIn('branch_id', $branchIds)->pluck('id')))
            ->count();

        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousReturns = DB::table('refunds')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->where('status', 'approved')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('sale_id', DB::table('sales')->whereIn('branch_id', $branchIds)->pluck('id')))
            ->sum('refund_amount');

        $returnsChange = $previousReturns > 0 ? (($totalReturns - $previousReturns) / $previousReturns) * 100 : 0;

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
        $branchIds = $this->accessibleBranchIds();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalSales = DB::table('sales')
            ->whereBetween('created_at', [$start, $end])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('total_amount');

        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');

        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->leftJoinSub(DB::table('purchase_items')->selectRaw('product_id, MAX(id) as last_purchase_item_id')->groupBy('product_id'), 'last_purchase', fn ($j) => $j->on('sale_items.product_id', '=', 'last_purchase.product_id'))
            ->leftJoin('purchase_items as pi', 'pi.id', '=', 'last_purchase.last_purchase_item_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('sales.branch_id', $branchIds))
            ->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

        $grossProfit = $totalSales - $cogs;
        $netProfit = $grossProfit - $totalExpenses;

        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousSales = DB::table('sales')->whereBetween('created_at', [$previousStart, $previousEnd])->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))->sum('total_amount');
        $previousExpenses = DB::table('expenses')->whereBetween('expense_date', [$previousStart->toDateString(), $previousEnd->toDateString()])->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))->sum('amount');
        $previousCogs = DB::table('sale_items')->join('sales', 'sale_items.sale_id', '=', 'sales.id')->leftJoinSub(DB::table('purchase_items')->selectRaw('product_id, MAX(id) as last_purchase_item_id')->groupBy('product_id'), 'last_purchase', fn ($j) => $j->on('sale_items.product_id', '=', 'last_purchase.product_id'))->leftJoin('purchase_items as pi', 'pi.id', '=', 'last_purchase.last_purchase_item_id')->whereBetween('sales.created_at', [$previousStart, $previousEnd])->when(! empty($branchIds), fn ($q) => $q->whereIn('sales.branch_id', $branchIds))->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

        $previousNetProfit = ($previousSales - $previousCogs) - $previousExpenses;
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

    public function monthlyExpenses()
    {
        $branchIds = $this->accessibleBranchIds();
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $totalExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');

        // Get previous month data for comparison
        $previousStart = Carbon::now()->subMonth()->startOfMonth();
        $previousEnd = Carbon::now()->subMonth()->endOfMonth();

        $previousExpenses = DB::table('expenses')
            ->whereBetween('expense_date', [$previousStart->toDateString(), $previousEnd->toDateString()])
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');

        $expensesChange = $previousExpenses > 0 ? (($totalExpenses - $previousExpenses) / $previousExpenses) * 100 : 0;

        return response()->json([
            'total_expenses' => (float) $totalExpenses,
            'expenses_change_percentage' => round($expensesChange, 2),
            'month' => Carbon::now()->format('F Y'),
        ]);
    }

    /**
     * Get today's sales by branch for modal display
     */
    public function branchSalesToday(Request $request)
    {
        try {
            $branchIds = $this->accessibleBranchIds();
            $today = Carbon::today('Asia/Manila');

            $branchSales = DB::table('sales')
                ->join('branches', 'sales.branch_id', '=', 'branches.id')
                ->whereDate('sales.created_at', $today)
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('sales.branch_id', $branchIds))
                ->selectRaw('branches.id as branch_id, branches.branch_name, SUM(sales.total_amount) as total_sales, COUNT(sales.id) as transaction_count')
                ->groupBy('branches.id', 'branches.branch_name')
                ->orderBy('total_sales', 'desc')
                ->get();

            // Calculate totals
            $totalSales = $branchSales->sum('total_sales');
            $totalTransactions = $branchSales->sum('transaction_count');
            $branchCount = $branchSales->count();

            return response()->json([
                'branches' => $branchSales,
                'total_sales' => (float) $totalSales,
                'total_transactions' => $totalTransactions,
                'branch_count' => $branchCount,
                'date' => $today->format('Y-m-d'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching branch sales today: '.$e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch branch sales data',
                'branches' => [],
                'total_sales' => 0,
                'total_transactions' => 0,
                'branch_count' => 0,
            ], 500);
        }
    }

    public function branchProfitToday(): \Illuminate\Http\JsonResponse
    {
        try {
            $user = auth()->user();
            $user?->loadMissing('userType');
            $branchIds = $this->accessibleBranchIds();

            $today = Carbon::today('Asia/Manila');
            $month = Carbon::now('Asia/Manila');

            $branches = DB::table('branches')
                ->where('status', 'active')
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('id', $branchIds))
                ->get(['id', 'branch_name']);

            // Reusable COGS subquery: latest purchase unit_cost per product
            $latestCostSub = DB::table('purchase_items')
                ->selectRaw('product_id, MAX(id) as last_id')
                ->groupBy('product_id');

            $result = $branches->map(function ($branch) use ($today, $month, $latestCostSub) {
                // Today's sales
                $todaySales = (float) DB::table('sales')
                    ->where('branch_id', $branch->id)
                    ->where('status', '!=', 'voided')
                    ->whereDate('created_at', $today)
                    ->sum('total_amount');

                // Today's COGS
                $todayCogs = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->leftJoinSub($latestCostSub, 'lp', fn ($j) => $j->on('sale_items.product_id', '=', 'lp.product_id'))
                    ->leftJoin('purchase_items as pi', 'pi.id', '=', 'lp.last_id')
                    ->where('sales.branch_id', $branch->id)
                    ->where('sales.status', '!=', 'voided')
                    ->whereDate('sales.created_at', $today)
                    ->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

                // Monthly sales
                $monthlySales = (float) DB::table('sales')
                    ->where('branch_id', $branch->id)
                    ->where('status', '!=', 'voided')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('total_amount');

                // Monthly COGS
                $monthlyCogs = (float) DB::table('sale_items')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->leftJoinSub($latestCostSub, 'lp', fn ($j) => $j->on('sale_items.product_id', '=', 'lp.product_id'))
                    ->leftJoin('purchase_items as pi', 'pi.id', '=', 'lp.last_id')
                    ->where('sales.branch_id', $branch->id)
                    ->where('sales.status', '!=', 'voided')
                    ->whereMonth('sales.created_at', $month->month)
                    ->whereYear('sales.created_at', $month->year)
                    ->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

                return [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->branch_name,
                    'today_sales' => $todaySales,
                    'today_cogs' => $todayCogs,
                    'today_profit' => $todaySales - $todayCogs,
                    'monthly_sales' => $monthlySales,
                    'monthly_cogs' => $monthlyCogs,
                    'monthly_profit' => $monthlySales - $monthlyCogs,
                ];
            })->sortByDesc('monthly_profit')->values();

            return response()->json(['branches' => $result]);

        } catch (\Exception $e) {
            Log::error('Error fetching branch profit: '.$e->getMessage());

            return response()->json(['error' => 'Failed to fetch branch profit data', 'branches' => []], 500);
        }
    }

    /**
     * Get monthly sales breakdown data for modal display
     */
    public function monthlySalesBreakdown(Request $request)
    {
        try {
            $branchIds = $this->accessibleBranchIds();
            $currentYear = Carbon::now()->year;
            $start = Carbon::createFromDate($currentYear, 1, 1)->startOfYear();
            $end = Carbon::createFromDate($currentYear, 12, 31)->endOfYear();

            $monthlySales = DB::table('sales')
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, DATE_FORMAT(created_at, "%M %Y") as month_name, SUM(total_amount) as total_sales, COUNT(id) as transaction_count')
                ->whereBetween('created_at', [$start, $end])
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->groupBy('month', 'month_name')
                ->orderBy('month', 'asc')
                ->get();

            // Fill in all months of current year with zero values
            $completeMonthlyData = [];
            $current = $start->copy();

            while ($current <= $end) {
                $monthKey = $current->format('Y-m');
                $monthName = $current->format('F Y');

                $monthData = $monthlySales->firstWhere('month', $monthKey);

                $completeMonthlyData[] = [
                    'month' => $monthKey,
                    'month_name' => $monthName,
                    'total_sales' => $monthData ? (float) $monthData->total_sales : 0,
                    'transaction_count' => $monthData ? $monthData->transaction_count : 0,
                ];

                $current->addMonth();
            }

            // Calculate summary statistics
            $totalSales = array_sum(array_column($completeMonthlyData, 'total_sales'));
            $monthsWithData = array_filter($completeMonthlyData, function ($month) {
                return $month['total_sales'] > 0;
            });
            $averageMonthlySales = count($monthsWithData) > 0 ? $totalSales / count($monthsWithData) : 0;
            $bestMonthData = collect($completeMonthlyData)->sortByDesc('total_sales')->first();

            return response()->json([
                'monthly_sales' => $completeMonthlyData,
                'total_sales' => $totalSales,
                'average_monthly_sales' => $averageMonthlySales,
                'best_month_sales' => $bestMonthData['total_sales'],
                'period' => $start->format('F Y').' - '.$end->format('F Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching monthly sales breakdown: '.$e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch monthly sales data',
                'monthly_sales' => [],
                'total_sales' => 0,
                'average_monthly_sales' => 0,
                'best_month_sales' => 0,
            ], 500);
        }
    }

    /**
            return response()->json([
                'error' => 'Expenses table not found',
                'categories' => [],
                'highest_month_expenses' => $highestMonthData['total_expenses'],
                'period' => $start->format('F Y') . ' - ' . $end->format('F Y')
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching monthly expenses breakdown: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch monthly expenses data',
                'monthly_expenses' => [],
                'total_expenses' => 0,
                'average_monthly_expenses' => 0,
                'highest_month_expenses' => 0
            ], 500);
        }
    }

    /**
     * Get monthly expenses breakdown data for modal display
     */
    public function monthlyExpensesBreakdown(Request $request)
    {
        try {
            $branchIds = $this->accessibleBranchIds();
            $currentYear = Carbon::now()->year;
            $start = Carbon::createFromDate($currentYear, 1, 1)->startOfYear();
            $end = Carbon::createFromDate($currentYear, 12, 31)->endOfYear();

            $monthlyExpenses = DB::table('expenses')
                ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, DATE_FORMAT(expense_date, "%M %Y") as month_name, SUM(amount) as total_expenses, COUNT(id) as transaction_count')
                ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->groupBy('month', 'month_name')
                ->orderBy('month', 'asc')
                ->get();

            // Fill in all months of current year with zero values
            $completeMonthlyData = [];
            $current = $start->copy();

            while ($current <= $end) {
                $monthKey = $current->format('Y-m');
                $monthName = $current->format('F Y');

                $monthData = $monthlyExpenses->firstWhere('month', $monthKey);

                $completeMonthlyData[] = [
                    'month' => $monthKey,
                    'month_name' => $monthName,
                    'total_expenses' => $monthData ? (float) $monthData->total_expenses : 0,
                    'transaction_count' => $monthData ? $monthData->transaction_count : 0,
                ];

                $current->addMonth();
            }

            // Calculate summary statistics
            $totalExpenses = array_sum(array_column($completeMonthlyData, 'total_expenses'));
            $monthsWithData = array_filter($completeMonthlyData, function ($month) {
                return $month['total_expenses'] > 0;
            });
            $averageMonthlyExpenses = count($monthsWithData) > 0 ? $totalExpenses / count($monthsWithData) : 0;
            $highestMonthData = collect($completeMonthlyData)->sortByDesc('total_expenses')->first();

            return response()->json([
                'monthly_expenses' => $completeMonthlyData,
                'total_expenses' => $totalExpenses,
                'average_monthly_expenses' => $averageMonthlyExpenses,
                'highest_month_expenses' => $highestMonthData['total_expenses'],
                'period' => $start->format('F Y').' - '.$end->format('F Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching monthly expenses breakdown: '.$e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch monthly expenses data',
                'monthly_expenses' => [],
                'total_expenses' => 0,
                'average_monthly_expenses' => 0,
                'highest_month_expenses' => 0,
            ], 500);
        }
    }

    /**
     * Get monthly returns breakdown data for modal display
     */
    public function monthlyReturnsBreakdown(Request $request)
    {
        try {
            $branchIds = $this->accessibleBranchIds();
            $currentYear = Carbon::now()->year;
            $start = Carbon::createFromDate($currentYear, 1, 1)->startOfYear();
            $end = Carbon::createFromDate($currentYear, 12, 31)->endOfYear();

            $saleIds = ! empty($branchIds)
                ? DB::table('sales')->whereIn('branch_id', $branchIds)->pluck('id')
                : null;

            $monthlyReturns = DB::table('refunds')
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, DATE_FORMAT(created_at, "%M %Y") as month_name, SUM(refund_amount) as total_returns, COUNT(id) as return_count')
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 'approved')
                ->when($saleIds !== null, fn ($q) => $q->whereIn('sale_id', $saleIds))
                ->groupBy('month', 'month_name')
                ->orderBy('month', 'asc')
                ->get();

            // Fill in all months of current year with zero values
            $completeMonthlyData = [];
            $current = $start->copy();

            while ($current <= $end) {
                $monthKey = $current->format('Y-m');
                $monthName = $current->format('F Y');

                $monthData = $monthlyReturns->firstWhere('month', $monthKey);

                $completeMonthlyData[] = [
                    'month' => $monthKey,
                    'month_name' => $monthName,
                    'total_returns' => $monthData ? (float) $monthData->total_returns : 0,
                    'return_count' => $monthData ? $monthData->return_count : 0,
                ];

                $current->addMonth();
            }

            // Calculate summary statistics
            $totalReturns = array_sum(array_column($completeMonthlyData, 'total_returns'));
            $monthsWithData = array_filter($completeMonthlyData, function ($month) {
                return $month['total_returns'] > 0;
            });
            $averageMonthlyReturns = count($monthsWithData) > 0 ? $totalReturns / count($monthsWithData) : 0;
            $highestMonthData = collect($completeMonthlyData)->sortByDesc('total_returns')->first();

            return response()->json([
                'monthly_returns' => $completeMonthlyData,
                'total_returns' => $totalReturns,
                'average_monthly_returns' => $averageMonthlyReturns,
                'highest_month_returns' => $highestMonthData['total_returns'],
                'period' => $start->format('F Y').' - '.$end->format('F Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching monthly returns breakdown: '.$e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch monthly returns data',
                'monthly_returns' => [],
                'total_returns' => 0,
                'average_monthly_returns' => 0,
                'highest_month_returns' => 0,
            ], 500);
        }
    }

    /**
     * Get monthly profit breakdown data for modal display
     */
    public function monthlyProfitBreakdown(Request $request)
    {
        try {
            $branchIds = $this->accessibleBranchIds();
            $currentYear = Carbon::now()->year;
            $start = Carbon::createFromDate($currentYear, 1, 1)->startOfYear();
            $end = Carbon::createFromDate($currentYear, 12, 31)->endOfYear();

            $monthlyProfit = DB::table('sales')
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, DATE_FORMAT(created_at, "%M %Y") as month_name, SUM(total_amount) as total_sales, COUNT(id) as transaction_count')
                ->whereBetween('created_at', [$start, $end])
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->groupBy('month', 'month_name')
                ->orderBy('month', 'asc')
                ->get();

            $monthlyCogs = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->leftJoinSub(DB::table('purchase_items')->selectRaw('product_id, MAX(id) as last_purchase_item_id')->groupBy('product_id'), 'last_purchase', fn ($j) => $j->on('sale_items.product_id', '=', 'last_purchase.product_id'))
                ->leftJoin('purchase_items as pi', 'pi.id', '=', 'last_purchase.last_purchase_item_id')
                ->selectRaw('DATE_FORMAT(sales.created_at, "%Y-%m") as month, SUM(COALESCE(pi.unit_cost, 0) * sale_items.quantity) as total_cogs')
                ->whereBetween('sales.created_at', [$start, $end])
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('sales.branch_id', $branchIds))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $monthlyExpenses = DB::table('expenses')
                ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total_expenses')
                ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            // Combine data and calculate profit
            $completeMonthlyData = [];
            $current = $start->copy();

            while ($current <= $end) {
                $monthKey = $current->format('Y-m');
                $monthName = $current->format('F Y');

                $salesData = $monthlyProfit->firstWhere('month', $monthKey);
                $cogsData = $monthlyCogs->firstWhere('month', $monthKey);
                $expensesData = $monthlyExpenses->firstWhere('month', $monthKey);

                $totalSales = $salesData ? (float) $salesData->total_sales : 0;
                $totalCogs = $cogsData ? (float) $cogsData->total_cogs : 0;
                $totalExpenses = $expensesData ? (float) $expensesData->total_expenses : 0;
                $grossProfit = $totalSales - $totalCogs;
                $netProfit = $grossProfit - $totalExpenses;

                $completeMonthlyData[] = [
                    'month' => $monthKey,
                    'month_name' => $monthName,
                    'total_sales' => $totalSales,
                    'total_cogs' => $totalCogs,
                    'total_expenses' => $totalExpenses,
                    'gross_profit' => $grossProfit,
                    'net_profit' => $netProfit,
                    'transaction_count' => $salesData ? $salesData->transaction_count : 0,
                ];

                $current->addMonth();
            }

            // Calculate summary statistics
            $totalProfit = array_sum(array_column($completeMonthlyData, 'net_profit'));
            $monthsWithData = array_filter($completeMonthlyData, function ($month) {
                return $month['net_profit'] != 0;
            });
            $averageMonthlyProfit = count($monthsWithData) > 0 ? $totalProfit / count($monthsWithData) : 0;
            $bestMonthData = collect($completeMonthlyData)->sortByDesc('net_profit')->first();

            return response()->json([
                'monthly_profit' => $completeMonthlyData,
                'total_profit' => $totalProfit,
                'average_monthly_profit' => $averageMonthlyProfit,
                'best_month_profit' => $bestMonthData['net_profit'],
                'period' => $start->format('F Y').' - '.$end->format('F Y'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching monthly profit breakdown: '.$e->getMessage());

            return response()->json([
                'error' => 'Failed to fetch monthly profit data',
                'monthly_profit' => [],
                'total_profit' => 0,
                'average_monthly_profit' => 0,
                'best_month_profit' => 0,
            ], 500);
        }
    }

    /**
     * Get today's expenses by category for modal display
     */
    public function expensesToday(Request $request)
    {
        try {
            $branchIds = $this->accessibleBranchIds();
            $today = Carbon::today('Asia/Manila');

            if (! DB::getSchemaBuilder()->hasTable('expenses')) {
                return response()->json(['error' => 'Expenses table not found', 'categories' => [], 'total_expenses' => 0, 'transaction_count' => 0, 'category_count' => 0], 404);
            }

            $allExpenses = DB::table('expenses')
                ->whereDate('expense_date', $today)
                ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
                ->get();

            if ($allExpenses->isEmpty()) {
                Log::info('No expenses found for today');

                return response()->json([
                    'categories' => [],
                    'total_expenses' => 0,
                    'transaction_count' => 0,
                    'category_count' => 0,
                    'date' => $today->format('Y-m-d'),
                ]);
            }

            // Check first expense to understand table structure
            $firstExpense = $allExpenses->first();
            Log::info('First expense structure:', (array) $firstExpense);

            // Initialize result array
            $expensesByCategory = [];

            // Try different approaches based on table structure
            try {
                // Check if category column exists
                if (isset($firstExpense->category)) {
                    Log::info('Category column found, grouping by category');

                    $expensesByCategory = DB::table('expenses')
                        ->whereDate('expense_date', $today)
                        ->selectRaw('
                            category,
                            SUM(amount) as total_amount,
                            COUNT(id) as transaction_count
                        ')
                        ->groupBy('category')
                        ->orderBy('total_amount', 'desc')
                        ->get();
                }
                // Check if description column exists (alternative to category)
                elseif (isset($firstExpense->description)) {
                    Log::info('Description column found, grouping by description');

                    $expensesByCategory = DB::table('expenses')
                        ->whereDate('expense_date', $today)
                        ->selectRaw('
                            description as category_name,
                            SUM(amount) as total_amount,
                            COUNT(id) as transaction_count
                        ')
                        ->groupBy('description')
                        ->orderBy('total_amount', 'desc')
                        ->get();
                }
                // Fallback: use a single uncategorized entry
                else {
                    Log::warning('No category or description column found, using uncategorized total');

                    $totalAmount = $allExpenses->sum('amount');
                    $totalCount = $allExpenses->count();

                    $expensesByCategory = [collect([
                        'category_name' => 'Uncategorized',
                        'total_amount' => $totalAmount,
                        'transaction_count' => $totalCount,
                    ])];
                }

            } catch (\Exception $e) {
                Log::error('Error in expense processing: '.$e->getMessage());

                // Ultimate fallback: return uncategorized total
                $totalAmount = $allExpenses->sum('amount');
                $totalCount = $allExpenses->count();

                $expensesByCategory = [collect([
                    'category_name' => 'Uncategorized',
                    'total_amount' => $totalAmount,
                    'transaction_count' => $totalCount,
                ])];
            }

            Log::info('Final expenses result:', ['count' => $expensesByCategory->count()]);

            // Calculate totals
            $totalExpenses = $expensesByCategory->sum('total_amount');
            $totalTransactions = $expensesByCategory->sum('transaction_count');
            $categoryCount = $expensesByCategory->count();

            return response()->json([
                'categories' => $expensesByCategory,
                'total_expenses' => (float) $totalExpenses,
                'transaction_count' => $totalTransactions,
                'category_count' => $categoryCount,
                'date' => $today->format('Y-m-d'),
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching expenses today:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to fetch expense data',
                'message' => $e->getMessage(),
                'debug_info' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ], 500);
        }
    }
}
