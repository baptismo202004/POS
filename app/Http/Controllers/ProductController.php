<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\UnitType;
use App\Models\Branch;

class ProductController extends Controller
{
    /**
     * Display product list
     * GET /products
     */
    public function index()
    {
        $products = Product::with([
            'brand',
            'category',
            'productType',
            'unitType'
        ])->latest()->paginate(15);

        return view('SuperAdmin.products.productIndex', compact('products'));
    }

    /**
     * Show create product form
     * GET /products/create
     */
    public function create()
    {
        return view('SuperAdmin.products.productList', [
            'brands'       => Brand::where('status', 'active')->get(),
            'categories'   => Category::where('status', 'active')->get(),
            'productTypes' => ProductType::all(),
            'unitTypes'    => UnitType::all(),
            'branches'     => Branch::all(),
        ]);
    }

    /**
     * Store new product
     * POST /products
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',

            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'unit_type_id' => 'nullable|exists:unit_types,id',

            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',

            'tracking_type' => 'required|in:none,serial,imei',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',

            // serials (only for electronic)
            'serials' => 'nullable|array',
            'serials.*.branch_id' => 'required_with:serials|exists:branches,id',
            'serials.*.serial_number' => 'required_with:serials|string|distinct',
            'serials.*.status' => 'required_with:serials|in:in_stock,sold,returned,defective,lost',
            'serials.*.warranty_expiry_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $validated) {

            // Upload image
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')
                    ->store('products', 'public');
            }

            // Create product
            $product = Product::create($validated);

            // Save serials (if electronic)
            if ($request->filled('serials')) {
                foreach ($request->serials as $serial) {
                    ProductSerial::create([
                        'product_id' => $product->id,
                        'branch_id' => $serial['branch_id'],
                        'serial_number' => $serial['serial_number'],
                        'status' => $serial['status'],
                        'warranty_expiry_date' => $serial['warranty_expiry_date'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('superadmin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show edit product form
     * GET /products/{product}/edit
     */
    public function edit(Product $product)
    {
        $product->load('serials');

        return view('SuperAdmin.products.productList', [
            'product'      => $product,
            'brands'       => Brand::where('status', 'active')->get(),
            'categories'   => Category::where('status', 'active')->get(),
            'productTypes' => ProductType::all(),
            'unitTypes'    => UnitType::all(),
            'branches'     => Branch::all(),
        ]);
    }

    /**
     * Update product
     * PUT /products/{product}
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode,' . $product->id,

            'brand_id' => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_type_id' => 'nullable|exists:product_types,id',
            'unit_type_id' => 'nullable|exists:unit_types,id',

            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',

            'tracking_type' => 'required|in:none,serial,imei',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',

            'serials' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $product, $validated) {

            // Image replace
            if ($request->hasFile('image')) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $validated['image'] = $request->file('image')
                    ->store('products', 'public');
            }

            $product->update($validated);

            // Replace serials (simple & safe)
            if ($request->filled('serials')) {
                $product->serials()->delete();

                foreach ($request->serials as $serial) {
                    ProductSerial::create([
                        'product_id' => $product->id,
                        'branch_id' => $serial['branch_id'],
                        'serial_number' => $serial['serial_number'],
                        'status' => $serial['status'],
                        'warranty_expiry_date' => $serial['warranty_expiry_date'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('superadmin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete product
     * DELETE /products/{product}
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('superadmin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
