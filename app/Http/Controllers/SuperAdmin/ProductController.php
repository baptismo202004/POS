<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        if (!in_array($sortBy, ['id', 'product_name', 'status'])) {
            $sortBy = 'id';
        }

        $productsQuery = Product::with(['brand', 'category', 'productType', 'unitType']);

        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%")
                      ->orWhereHas('brand', function ($q) use ($search) {
                          $q->where('brand_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('category_name', 'like', "%{$search}%");
                      });
            });
        }

        $products = $productsQuery->orderBy($sortBy, $sortDirection)->paginate(15);

        if ($request->ajax()) {
            return view('SuperAdmin.products._product_table', compact('products'))->render();
        }

        return view('SuperAdmin.products.productIndex', [
            'products' => $products->appends($request->query()),
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection
        ]);
    }

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

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'product_name' => 'required|string|max:255',
                'barcode' => 'required|string|unique:products,barcode',
                'brand_id' => 'nullable|exists:brands,id',
                'category_id' => 'nullable|exists:categories,id',
                'tracking_type' => 'required|in:none,serial,imei',
                'warranty_type' => 'required|in:none,shop,manufacturer',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()]);
            }

            $product = Product::create($validator->validated());

            return response()->json(['success' => true, 'product' => $product]);
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'product_type_id' => 'nullable',
            'unit_type_id' => 'nullable',
            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'tracking_type' => 'required|in:none,serial,imei',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'serials' => 'nullable|array',
            'serials.*.branch_id' => 'required_with:serials|exists:branches,id',
            'serials.*.serial_number' => 'required_with:serials|string|distinct',
            'serials.*.status' => 'required_with:serials|in:in_stock,sold,returned,defective,lost',
            'serials.*.warranty_expiry_date' => 'nullable|date',
        ]);

        if (!empty($request->input('brand_id')) && !is_numeric($request->input('brand_id'))) {
            $b = Brand::create(['brand_name' => $request->input('brand_id'), 'status' => 'active']);
            $validated['brand_id'] = $b->id;
        }

        if (!empty($request->input('category_id')) && !is_numeric($request->input('category_id'))) {
            $c = Category::create(['category_name' => $request->input('category_id'), 'status' => 'active']);
            $validated['category_id'] = $c->id;
        }

        DB::transaction(function () use ($request, $validated) {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('products', 'public');
            }

            $product = Product::create($validated);

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

        return redirect()->route('superadmin.products.index')->with('success', 'Product created successfully.');
    }


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

            // allow ids OR free-text values (we'll resolve/create below)
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'product_type_id' => 'nullable',
            'unit_type_id' => 'nullable',

            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',

            'tracking_type' => 'required|in:none,serial,imei',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',

            'serials' => 'nullable|array',
        ]);

        // Resolve possible string inputs (from select2 "tags") into actual IDs for update
        if (!empty($request->input('brand_id')) && !is_numeric($request->input('brand_id'))) {
            $b = new Brand();
            $b->brand_name = $request->input('brand_id');
            $b->status = 'active';
            $b->save();
            $validated['brand_id'] = $b->id;
        }

        if (!empty($request->input('category_id')) && !is_numeric($request->input('category_id'))) {
            $c = new Category();
            $c->category_name = $request->input('category_id');
            $c->status = 'active';
            $c->save();
            $validated['category_id'] = $c->id;
        }

        if (!empty($request->input('product_type_id')) && !is_numeric($request->input('product_type_id'))) {
            $pt = new ProductType();
            $pt->type_name = $request->input('product_type_id');
            $pt->is_electronic = false;
            $pt->save();
            $validated['product_type_id'] = $pt->id;
        }

        if (!empty($request->input('unit_type_id')) && !is_numeric($request->input('unit_type_id'))) {
            $ut = new UnitType();
            $ut->unit_name = $request->input('unit_type_id');
            $ut->save();
            $validated['unit_type_id'] = $ut->id;
        }

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
