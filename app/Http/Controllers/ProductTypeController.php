<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index()
    {
        $productTypes = ProductType::latest()->get();
        return view('SuperAdmin.productTypes.index', compact('productTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|unique:product_types,type_name',
            'is_electronic' => 'boolean'
        ]);

        ProductType::create([
            'type_name' => $request->type_name,
            'is_electronic' => $request->is_electronic ?? false
        ]);

        return back()->with('success', 'Product type added');
    }

    public function create()
    {
        return view('SuperAdmin.productTypes.create');
    }

    public function edit(ProductType $productType)
    {
        return view('SuperAdmin.productTypes.edit', compact('productType'));
    }

    public function update(Request $request, ProductType $productType)
    {
        $request->validate([
            'type_name' => 'required|unique:product_types,type_name,' . $productType->id,
            'is_electronic' => 'boolean'
        ]);

        $productType->update([
            'type_name' => $request->type_name,
            'is_electronic' => $request->is_electronic ?? false
        ]);

        return redirect()->route('superadmin.product-types.index')->with('success', 'Product type updated successfully');
    }

    public function destroy(ProductType $productType)
    {
        $productType->delete();
        return back()->with('success', 'Product type deleted successfully');
    }
}
