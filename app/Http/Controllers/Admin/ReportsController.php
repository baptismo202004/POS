<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Sale;
use App\Traits\ScopesByBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    use ScopesByBranch;

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        return view('Admin.reports.index');
    }

    public function branchProfit(): \Illuminate\Http\JsonResponse
    {
        $branchIds = $this->accessibleBranchIds();
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $latestCostSub = DB::table('purchase_items')
            ->selectRaw('product_id, MAX(id) as last_id')
            ->groupBy('product_id');

        $branches = Branch::where('status', 'active')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('id', $branchIds))
            ->orderBy('branch_name')
            ->get(['id', 'branch_name']);

        $rows = $branches->map(function (Branch $branch) use ($today, $monthStart, $monthEnd, $latestCostSub): array {
            // Daily
            $dailySales = (float) DB::table('sales')
                ->where('branch_id', $branch->id)->where('status', '!=', 'voided')
                ->whereDate('created_at', $today)->sum('total_amount');

            $dailyCogs = (float) DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->leftJoinSub($latestCostSub, 'lp', fn ($j) => $j->on('sale_items.product_id', '=', 'lp.product_id'))
                ->leftJoin('purchase_items as pi', 'pi.id', '=', 'lp.last_id')
                ->where('sales.branch_id', $branch->id)->where('sales.status', '!=', 'voided')
                ->whereDate('sales.created_at', $today)
                ->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

            $dailyExpenses = (float) DB::table('expenses')
                ->where('branch_id', $branch->id)->whereDate('expense_date', $today)->sum('amount');

            // Monthly
            $monthlySales = (float) DB::table('sales')
                ->where('branch_id', $branch->id)->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');

            $monthlyCogs = (float) DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->leftJoinSub($latestCostSub, 'lp', fn ($j) => $j->on('sale_items.product_id', '=', 'lp.product_id'))
                ->leftJoin('purchase_items as pi', 'pi.id', '=', 'lp.last_id')
                ->where('sales.branch_id', $branch->id)->where('sales.status', '!=', 'voided')
                ->whereBetween('sales.created_at', [$monthStart, $monthEnd])
                ->sum(DB::raw('COALESCE(pi.unit_cost, 0) * sale_items.quantity'));

            $monthlyExpenses = (float) DB::table('expenses')
                ->where('branch_id', $branch->id)
                ->whereBetween('expense_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('amount');

            return [
                'branch_name' => $branch->branch_name,
                'daily_sales' => $dailySales,
                'daily_cogs' => $dailyCogs,
                'daily_expenses' => $dailyExpenses,
                'daily_profit' => $dailySales - $dailyCogs - $dailyExpenses,
                'monthly_sales' => $monthlySales,
                'monthly_cogs' => $monthlyCogs,
                'monthly_expenses' => $monthlyExpenses,
                'monthly_profit' => $monthlySales - $monthlyCogs - $monthlyExpenses,
            ];
        });

        return response()->json(['branches' => $rows, 'month' => $today->format('F Y'), 'today' => $today->toDateString()]);
    }

    public function filter(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $types = $request->input('types', ['sales', 'expenses']);

        if (! $fromDate || ! $toDate) {
            return response()->json(['error' => 'Please provide both from and to dates'], 400);
        }

        try {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        $sales = collect();
        $expenses = collect();

        if (in_array('sales', $types)) {
            $sales = Sale::with(['user', 'saleItems.product'])
                ->whereBetween('created_at', [$fromDate, $toDate])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if (in_array('expenses', $types)) {
            $expenses = Expense::with('category')
                ->whereBetween('expense_date', [$fromDate, $toDate])
                ->orderBy('expense_date', 'desc')
                ->get();
        }

        $allTransactions = $sales->map(fn ($s) => tap($s, fn ($s) => $s->transaction_date = $s->created_at))
            ->concat($expenses->map(fn ($e) => tap($e, fn ($e) => $e->transaction_date = $e->expense_date)))
            ->sortByDesc('transaction_date')
            ->values();

        $totalSales = $sales->sum('total_amount');
        $totalExpenses = $expenses->sum('amount');
        $netTotal = $totalSales - $totalExpenses;

        $totalPurchaseCost = 0;
        $salesByProduct = [];

        // Pre-fetch latest unit_cost per product from purchase_items
        $productIds = $sales->flatMap(fn ($s) => $s->saleItems->pluck('product_id'))->unique()->values()->all();
        $latestUnitCosts = [];
        if (! empty($productIds)) {
            $rows = \Illuminate\Support\Facades\DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->whereIn('purchase_items.product_id', $productIds)
                ->select('purchase_items.product_id', 'purchase_items.unit_cost', 'purchases.purchase_date')
                ->orderByDesc('purchases.purchase_date')
                ->get()
                ->groupBy('product_id')
                ->map(fn ($g) => (float) $g->first()->unit_cost);
            $latestUnitCosts = $rows->toArray();
        }

        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $item) {
                $unitCost = $latestUnitCosts[$item->product_id] ?? 0.0;
                $totalPurchaseCost += (float) $item->quantity * $unitCost;

                $productName = $item->product->product_name ?? 'Unknown Product';
                if (! isset($salesByProduct[$productName])) {
                    $salesByProduct[$productName] = ['quantity' => 0, 'revenue' => 0, 'price' => $item->unit_price];
                }
                $salesByProduct[$productName]['quantity'] += $item->quantity;
                $salesByProduct[$productName]['revenue'] += $item->quantity * $item->unit_price;
            }
        }

        return response()->json([
            'transactions' => $allTransactions,
            'summaries' => [
                'total_sales' => $totalSales,
                'total_expenses' => $totalExpenses,
                'net_total' => $netTotal,
                'total_purchase_cost' => $totalPurchaseCost,
                'gross_profit' => $totalSales - $totalPurchaseCost,
                'net_profit' => $totalSales - $totalExpenses - $totalPurchaseCost,
                'sales_count' => $sales->count(),
                'expense_count' => $expenses->count(),
            ],
            'sales_by_product' => $salesByProduct,
        ]);
    }

    public function export(Request $request)
    {
        $fromDate = $request->input('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->input('to_date', Carbon::now()->format('Y-m-d'));

        try {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Get data for export
        $sales = Sale::with(['user', 'saleItems.product'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->orderBy('expense_date', 'desc')
            ->get();

        // Generate CSV
        $filename = "sales_report_{$fromDate->format('Y-m-d')}_to_{$toDate->format('Y-m-d')}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($sales, $expenses) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'Date & Time',
                'Type',
                'Description',
                'Amount',
                'User',
                'Status',
            ]);

            // Add sales
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->created_at->format('Y-m-d H:i:s'),
                    'SALE',
                    "Sale Transaction ({$sale->saleItems->count()} items)",
                    $sale->total_amount,
                    $sale->user->name ?? 'N/A',
                    'Completed',
                ]);
            }

            // Add expenses
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->created_at->format('Y-m-d H:i:s'),
                    'EXPENSE',
                    $expense->description,
                    $expense->amount,
                    'System',
                    'Processed',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
