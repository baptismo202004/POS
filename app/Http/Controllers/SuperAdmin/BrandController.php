<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->get();
        return view('SuperAdmin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|unique:brands,brand_name',
            'status' => 'required|in:active,inactive'
        ]);

        Brand::create([
            'brand_name' => $request->brand_name,
            'status' => $request->status
        ]);

        return back()->with('success', 'Brand added successfully');
    }

    public function create()
    {
        return view('SuperAdmin.brands.index');
    }

    public function edit(Brand $brand)
    {
        return view('SuperAdmin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'brand_name' => 'required|unique:brands,brand_name,' . $brand->id,
            'status' => 'required|in:active,inactive'
        ]);

        $brand->update([
            'brand_name' => $request->brand_name,
            'status' => $request->status
        ]);

        return redirect()->route('superadmin.brands.index')->with('success', 'Brand updated successfully');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return back()->with('success', 'Brand deleted successfully');
    }
}
