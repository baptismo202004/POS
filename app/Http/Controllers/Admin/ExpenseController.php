<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExpenseRequest;
use App\Http\Requests\Admin\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Traits\ScopesByBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    use ScopesByBranch;

    public function index(Request $request)
    {
        $branchIds = $this->accessibleBranchIds();
        $query = Expense::with('category', 'supplier')
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds));

        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }
        if ($request->filled('category_filter')) {
            $query->where('expense_category_id', $request->category_filter);
        }
        if ($request->filled('search_input')) {
            $search = $request->search_input;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $expenses = $query->latest()->paginate(15);
        $categories = ExpenseCategory::all();

        $todayExpenses = Expense::whereDate('expense_date', now()->today())
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');
        $monthlyExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');
        $totalExpenses = Expense::when(! empty($branchIds), fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->sum('amount');

        return view('Admin.expenses.index', compact('expenses', 'categories', 'todayExpenses', 'monthlyExpenses', 'totalExpenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = ExpenseCategory::where('name', '!=', 'Purchases')->get();
        $suppliers = \App\Models\Supplier::all();

        return view('Admin.expenses.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $data = $request->validated();

        // Auto-create category if a new name was typed (value starts with "new:")
        if (str_starts_with((string) $data['expense_category_id'], 'new:')) {
            $newName = trim(substr($data['expense_category_id'], 4));
            $category = \App\Models\ExpenseCategory::firstOrCreate(['name' => $newName]);
            $data['expense_category_id'] = $category->id;
        }

        // supplier_id can be a numeric ID (existing) or a typed name string (new via select2 tags)
        if (! empty($data['supplier_id'])) {
            if (! is_numeric($data['supplier_id'])) {
                $supplier = \App\Models\Supplier::firstOrCreate([
                    'supplier_name' => trim($data['supplier_id']),
                ]);
                $data['supplier_id'] = $supplier->id;
            } else {
                $data['supplier_id'] = (int) $data['supplier_id'];
            }
        } else {
            $data['supplier_id'] = null;
        }

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('admin.expenses.index')->with('success', 'Expense created successfully.');
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
                'category' => $expense->category ? $expense->category->name : 'N/A',
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
                'category' => $expense->category ? $expense->category->name : 'N/A',
            ];
        });

        return response()->json(['expenses' => $expensesData]);
    }
}
