<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::orderBy('name')->paginate(15);
        return view('Admin.taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:taxes,code',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        Tax::create($request->all());

        return redirect()->route('superadmin.admin.taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tax $tax)
    {
        return view('Admin.taxes.show', compact('tax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tax $tax)
    {
        return view('Admin.taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:taxes,code,' . $tax->id,
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $tax->update($request->all());

        return redirect()->route('superadmin.admin.taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tax $tax)
    {
        $tax->delete();

        return redirect()->route('superadmin.admin.taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
