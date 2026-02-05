<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Http\Requests\Admin\UpdateExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with('category', 'supplier');

        // Date range filter
        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->where('expense_category_id', $request->category_filter);
        }

        // Search filter
        if ($request->filled('search_input')) {
            $search = $request->search_input;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $expenses = $query->latest()->paginate(15);
        $categories = ExpenseCategory::all();

        // Calculate summary data
        $todayExpenses = Expense::whereDate('expense_date', now()->today())->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)
                                 ->whereYear('expense_date', now()->year)
                                 ->sum('amount');
        $totalExpenses = Expense::sum('amount');

        return view('Admin.expenses.index', compact('expenses', 'categories', 'todayExpenses', 'monthlyExpenses', 'totalExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::where('name', '!=', 'Purchases')->get();
        $suppliers = \App\Models\Supplier::all(); // Assuming a Supplier model exists
        return view('Admin.expenses.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('superadmin.admin.expenses.index')->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        $expense->load('category', 'supplier');
        return view('Admin.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        $this->authorize('update-expense', $expense);
        $categories = ExpenseCategory::where('name', '!=', 'Purchases')->get();
        $suppliers = \App\Models\Supplier::all();
        return view('Admin.expenses.edit', compact('expense', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update-expense', $expense);
        $data = $request->validated();

        if ($request->hasFile('receipt')) {
            // Delete old receipt if it exists
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update($data);

        return redirect()->route('superadmin.admin.expenses.index')->with('success', 'Expense updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete-expense', $expense);

        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();

        return redirect()->route('superadmin.admin.expenses.index')->with('success', 'Expense deleted successfully.');
    }
    
    public function getTodaysExpenses()
    {
        $today = Carbon::today();
        
        $expenses = Expense::with(['category'])
            ->whereDate('expense_date', $today)
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $expensesData = $expenses->map(function ($expense) {
            return [
                'id' => $expense->id,
                'created_at' => $expense->created_at,
                'description' => $expense->description,
                'amount' => $expense->amount,
                'category' => $expense->category ? $expense->category->name : 'N/A'
            ];
        });
        
        return response()->json(['expenses' => $expensesData]);
    }
    
    public function getThisMonthExpenses()
    {
        $thisMonth = Carbon::now()->startOfMonth();
        $thisMonthEnd = Carbon::now()->endOfMonth();
        
        $expenses = Expense::with(['category'])
            ->whereBetween('expense_date', [$thisMonth, $thisMonthEnd])
            ->orderBy('expense_date', 'desc')
            ->get();
        
        $expensesData = $expenses->map(function ($expense) {
            return [
                'id' => $expense->id,
                'created_at' => $expense->created_at,
                'description' => $expense->description,
                'amount' => $expense->amount,
                'category' => $expense->category ? $expense->category->name : 'N/A'
            ];
        });
        
        return response()->json(['expenses' => $expensesData]);
    }
}
