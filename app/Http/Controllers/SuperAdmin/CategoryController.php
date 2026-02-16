<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'asc')->get();
        return view('SuperAdmin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name',
            'status' => 'required|in:active,inactive'
        ]);

        Category::create(['category_name' => $request->category_name, 'status' => $request->status]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category added successfully']);
        }

        return back()->with('success', 'Category added');
    }

    public function create()
    {
        return view('SuperAdmin.categories.index');
    }

    public function edit(Category $category)
    {
        return view('SuperAdmin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name,' . $category->id,
            'status' => 'required|in:active,inactive'
        ]);

        $category->update(['category_name' => $request->category_name, 'status' => $request->status]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category updated successfully']);
        }

        return redirect()->route('superadmin.categories.index')->with('success', 'Category updated successfully');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return back()->with('success', 'Category deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
        ]);

        Category::whereIn('id', $request->ids)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Categories deleted successfully']);
        }

        return back()->with('success', 'Selected categories deleted successfully');
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        Category::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return back()->with('success', 'Selected categories updated successfully');
    }
}
