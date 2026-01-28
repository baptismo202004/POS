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
}
