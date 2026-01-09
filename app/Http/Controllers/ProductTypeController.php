<?php

namespace App\Http\Controllers\SuperAdmin;

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
            'name' => 'required|unique:product_types,name',
            'is_electronic' => 'boolean'
        ]);

        ProductType::create([
            'name' => $request->name,
            'is_electronic' => $request->is_electronic ?? false
        ]);

        return back()->with('success', 'Product type added');
    }
}
