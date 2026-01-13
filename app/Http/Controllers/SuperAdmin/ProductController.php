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
            $b = Brand::firstOrCreate(
                ['brand_name' => $request->input('brand_id')],
                ['status' => 'active']
            );
            if (!$b->wasRecentlyCreated) {
                session()->flash('notice', "Brand '" . $b->brand_name . "' already exists. Selected it automatically.");
            }
            $validated['brand_id'] = $b->id;
        }

        // Category: create or select existing; update is_electronic if different
        if (!empty($request->input('category_id')) && !is_numeric($request->input('category_id'))) {
            $requestedElectronic = (bool) $request->input('is_electronic', false);
            $c = Category::firstOrCreate(
                ['category_name' => $request->input('category_id')],
                ['is_electronic' => $requestedElectronic, 'status' => 'active']
            );
            if (!$c->wasRecentlyCreated) {
                if ((bool) $c->is_electronic !== $requestedElectronic) {
                    $c->is_electronic = $requestedElectronic;
                    $c->save();
                    session()->flash('notice', "Category '" . $c->category_name . "' already exists and its electronic setting was updated to match your selection.");
                } else {
                    session()->flash('notice', "Category '" . $c->category_name . "' already exists. Selected it automatically.");
                }
            }
            $validated['category_id'] = $c->id;
        }

        if (!empty($request->input('unit_type_id')) && !is_numeric($request->input('unit_type_id'))) {
            $ut = UnitType::firstOrCreate(['unit_name' => $request->input('unit_type_id')]);
            if (!$ut->wasRecentlyCreated) {
                session()->flash('notice', "Unit type '" . $ut->unit_name . "' already exists. Selected it automatically.");
            }
            $validated['unit_type_id'] = $ut->id;
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
            $b = Brand::firstOrCreate(
                ['brand_name' => $request->input('brand_id')],
                ['status' => 'active']
            );
            if (!$b->wasRecentlyCreated) {
                session()->flash('notice', "Brand '" . $b->brand_name . "' already exists. Selected it automatically.");
            }
            $validated['brand_id'] = $b->id;
        }

        // Category: create or select existing; update is_electronic if different
        if (!empty($request->input('category_id')) && !is_numeric($request->input('category_id'))) {
            $requestedElectronic = (bool) $request->input('is_electronic', false);
            $c = Category::firstOrCreate(
                ['category_name' => $request->input('category_id')],
                ['is_electronic' => $requestedElectronic, 'status' => 'active']
            );
            if (!$c->wasRecentlyCreated) {
                if ((bool) $c->is_electronic !== $requestedElectronic) {
                    $c->is_electronic = $requestedElectronic;
                    $c->save();
                    session()->flash('notice', "Category '" . $c->category_name . "' already exists and its electronic setting was updated to match your selection.");
                } else {
                    session()->flash('notice', "Category '" . $c->category_name . "' already exists. Selected it automatically.");
                }
            }
            $validated['category_id'] = $c->id;
        }

        if (!empty($request->input('unit_type_id')) && !is_numeric($request->input('unit_type_id'))) {
            $ut = UnitType::firstOrCreate(['unit_name' => $request->input('unit_type_id')]);
            if (!$ut->wasRecentlyCreated) {
                session()->flash('notice', "Unit type '" . $ut->unit_name . "' already exists. Selected it automatically.");
            }
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

    /**
     * AJAX: Check for similar product names and existence
     * GET /superadmin/products/check-name?name=...
     */
    public function checkName(Request $request)
    {
        $name = trim((string) $request->query('name', ''));
        if ($name === '') {
            return response()->json(['ok' => true, 'exists' => false, 'suggestions' => []]);
        }

        $query = Product::query();
        $query->where(function ($q) use ($name) {
            $q->where('product_name', $name)
              ->orWhere('product_name', 'like', '%' . str_replace(['%', '_'], ['\\%', '\\_'], $name) . '%');
        });

        $matches = $query->limit(8)->pluck('product_name');
        $existsExact = $matches->contains(function ($v) use ($name) { return mb_strtolower($v) === mb_strtolower($name); });

        return response()->json([
            'ok' => true,
            'exists' => $existsExact,
            'suggestions' => $matches,
        ]);
    }

    /**
     * AJAX: Check if a barcode already exists
     * GET /superadmin/products/check-barcode?barcode=...&ignore_id=...
     */
    public function checkBarcode(Request $request)
    {
        $barcode = trim((string) $request->query('barcode', ''));
        if ($barcode === '') {
            return response()->json(['ok' => true, 'duplicate' => false]);
        }

        $ignoreId = $request->query('ignore_id');
        $query = Product::where('barcode', $barcode);
        if (!empty($ignoreId)) {
            $query->where('id', '!=', $ignoreId);
        }
        $exists = $query->exists();

        return response()->json([
            'ok' => true,
            'duplicate' => $exists,
        ]);
    }
}
