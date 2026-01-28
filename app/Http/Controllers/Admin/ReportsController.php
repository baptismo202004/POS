<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\SaleItem;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        return view('Admin.reports.index');
    }

    public function filter(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        
        // Validate dates
        if (!$fromDate || !$toDate) {
            return response()->json(['error' => 'Please provide both from and to dates'], 400);
        }
        
        try {
            $fromDate = Carbon::parse($fromDate)->startOfDay();
            $toDate = Carbon::parse($toDate)->endOfDay();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }
        
        // Get filtered sales
        $sales = Sale::with(['user', 'saleItems.product'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get filtered expenses
        $expenses = Expense::with('category')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Combine and sort by date
        $allTransactions = $sales->concat($expenses)
            ->sortByDesc('created_at')
            ->values();
        
        // Calculate summaries
        $totalSales = $sales->sum('total_amount');
        $totalExpenses = $expenses->sum('amount');
        $netTotal = $totalSales - $totalExpenses;
        
        // Get sales by product
        $salesByProduct = [];
        foreach ($sales as $sale) {
            foreach ($sale->saleItems as $item) {
                $productName = $item->product ? $item->product->product_name : 'Unknown Product';
                if (!isset($salesByProduct[$productName])) {
                    $salesByProduct[$productName] = [
                        'quantity' => 0,
                        'revenue' => 0,
                        'price' => $item->unit_price
                    ];
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
                'sales_count' => $sales->count(),
                'expense_count' => $expenses->count()
            ],
            'sales_by_product' => $salesByProduct
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
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Generate CSV
        $filename = "sales_report_{$fromDate->format('Y-m-d')}_to_{$toDate->format('Y-m-d')}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($sales, $expenses) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, [
                'Date & Time',
                'Type',
                'Description',
                'Amount',
                'User',
                'Status'
            ]);
            
            // Add sales
            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->created_at->format('Y-m-d H:i:s'),
                    'SALE',
                    "Sale Transaction ({$sale->saleItems->count()} items)",
                    $sale->total_amount,
                    $sale->user->name ?? 'N/A',
                    'Completed'
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
                    'Processed'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
