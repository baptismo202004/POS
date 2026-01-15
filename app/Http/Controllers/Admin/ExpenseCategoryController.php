<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $categories = ExpenseCategory::where('name', 'LIKE', "%{$search}%")
            ->where('name', '!=', 'Purchases')
            ->limit(10)
            ->get(['id', 'name as text']);

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:expense_categories,name',
        ]);

        $category = ExpenseCategory::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'id' => $category->id,
            'text' => $category->name,
        ]);
    }
}
