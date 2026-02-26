<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $expenses = Expense::with(['category', 'cashier'])
            ->where('branch_id', $branchId)
            ->orderBy('expense_date', 'desc')
            ->get();

        return view('cashier.expenses.index', compact('expenses'));
    }

    public function create()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $categories = ExpenseCategory::orderBy('name')->get();

        return view('cashier.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'expense_date' => 'required|date|before_or_equal:today',
            'receipt_number' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::create([
                'expense_category_id' => $request->expense_category_id,
                'branch_id' => $branchId,
                'amount' => $request->amount,
                'description' => $request->description,
                'expense_date' => $request->expense_date,
                'receipt_number' => $request->receipt_number,
                'created_by' => $user->id,
                'status' => 'approved',
            ]);

            DB::commit();

            return redirect()->route('cashier.expenses.index')
                ->with('success', 'Expense recorded successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error recording expense: '.$e->getMessage());
        }
    }

    public function show(Expense $expense)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($expense->branch_id !== $branchId) {
            abort(403, 'Unauthorized access to this expense');
        }

        $expense->load(['category', 'creator']);

        return view('cashier.expenses.show', compact('expense'));
    }
}
