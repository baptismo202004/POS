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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'brand_name' => 'required|unique:brands,brand_name',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $brand = Brand::create([
            'brand_name' => $request->brand_name,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Brand added successfully.', 'brand' => $brand]);
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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'brand_name' => 'required|unique:brands,brand_name,'.$brand->id,
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $brand->update([
            'brand_name' => $request->brand_name,
            'status' => $request->status,
        ]);

        return response()->json(['success' => true, 'message' => 'Brand updated successfully.', 'brand' => $brand->fresh()]);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return back()->with('success', 'Brand deleted successfully');
    }
}
