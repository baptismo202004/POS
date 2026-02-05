<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\UnitType;
use App\Models\Branch;
use App\Models\ProductSerial;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    
    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        if (!in_array($sortBy, ['id', 'product_name', 'status'])) {
            $sortBy = 'id';
        }

        $productsQuery = Product::with(['brand', 'category', 'productType', 'unitTypes']);

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
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'barcode' => 'required|string|unique:products,barcode',
            'unit_type_ids' => 'required|array|min:1',
            'unit_type_ids.*' => 'exists:unit_types,id',
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'product_type_id' => 'nullable',
            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'serial_number' => 'required_if:product_type_id,1|string|unique:product_serials,serial_number',
            'branch_id' => 'required_if:product_type_id,1|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($request, $validated) {
                // Check if product type is electronic
                $isElectronic = false;
                if (!empty($validated['product_type_id'])) {
                    $productType = ProductType::find($validated['product_type_id']);
                    $isElectronic = $productType && $productType->is_electronic;

                    Log::info('Electronic product check', [
                        'product_type_id' => $validated['product_type_id'],
                        'product_type_found' => !!$productType,
                        'is_electronic_flag' => $productType ? $productType->is_electronic : 'N/A',
                        'result' => $isElectronic
                    ]);
                }

                if ($isElectronic) {
                    // For electronic products, store in product_serials table
                    // First create the base product record
                    $productData = [
                        'product_name' => $validated['product_name'],
                        'barcode' => $validated['barcode'],
                        'brand_id' => $validated['brand_id'] ?? null,
                        'category_id' => $validated['category_id'] ?? null,
                        'product_type_id' => $validated['product_type_id'],
                        'model_number' => $validated['model_number'] ?? null,
                        'warranty_type' => $validated['warranty_type'],
                        'warranty_coverage_months' => $validated['warranty_coverage_months'] ?? null,
                        'voltage_specs' => $validated['voltage_specs'] ?? null,
                        'status' => $validated['status'],
                    ];

                    if (!empty($validated['brand_id']) && !is_numeric($validated['brand_id'])) {
                        $b = Brand::create(['brand_name' => $validated['brand_id'], 'status' => 'active']);
                        $productData['brand_id'] = $b->id;
                    }

                    if (!empty($validated['category_id']) && !is_numeric($validated['category_id'])) {
                        $c = Category::create(['category_name' => $validated['category_id'], 'status' => 'active']);
                        $productData['category_id'] = $c->id;
                    }

                    if ($request->hasFile('image')) {
                        $productData['image'] = $request->file('image')->store('products', 'public');
                    }

                    $product = Product::create($productData);
                    $product->unitTypes()->sync($validated['unit_type_ids']);

                    // Create product serial record
                    ProductSerial::create([
                        'product_id' => $product->id,
                        'branch_id' => $validated['branch_id'],
                        'serial_number' => $validated['serial_number'],
                        'status' => 'in_stock',
                        'warranty_expiry_date' => $this->calculateWarrantyExpiry($validated['warranty_type'], $validated['warranty_coverage_months'] ?? null),
                    ]);

                } else {
                    // For non-electronic products, store normally in products table
                    if (!empty($request->input('brand_id')) && !is_numeric($request->input('brand_id'))) {
                        $b = Brand::create(['brand_name' => $request->input('brand_id'), 'status' => 'active']);
                        $validated['brand_id'] = $b->id;
                    }

                    if (!empty($request->input('category_id')) && !is_numeric($request->input('category_id'))) {
                        $c = Category::create(['category_name' => $request->input('category_id'), 'status' => 'active']);
                        $validated['category_id'] = $c->id;
                    }

                    if ($request->hasFile('image')) {
                        $validated['image'] = $request->file('image')->store('products', 'public');
                    }

                    $product = Product::create($validated);
                    $product->unitTypes()->sync($validated['unit_type_ids']);
                }
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function calculateWarrantyExpiry($warrantyType, $coverageMonths)
    {
        if ($warrantyType === 'none' || !$coverageMonths) {
            return null;
        }
        
        return now()->addMonths($coverageMonths)->format('Y-m-d');
    }


    public function show(Product $product)
    {
        $product->load('brand', 'category', 'productType', 'unitTypes');
        return view('SuperAdmin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load('unitTypes');

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
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255|unique:products,product_name,' . $product->id,
            'barcode' => 'required|string|unique:products,barcode,' . $product->id,
            'unit_type_ids' => 'required|array|min:1',
            'unit_type_ids.*' => 'exists:unit_types,id',
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'product_type_id' => 'nullable',
            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'warranty_type' => 'required|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($request, $product, $validated) {
                if (!empty($request->input('brand_id')) && !is_numeric($request->input('brand_id'))) {
                    $b = Brand::create(['brand_name' => $request->input('brand_id'), 'status' => 'active']);
                    $validated['brand_id'] = $b->id;
                }

                if (!empty($request->input('category_id')) && !is_numeric($request->input('category_id'))) {
                    $c = Category::create(['category_name' => $request->input('category_id'), 'status' => 'active']);
                    $validated['category_id'] = $c->id;
                }

                if ($request->hasFile('image')) {
                    if ($product->image) {
                        Storage::disk('public')->delete($product->image);
                    }
                    $validated['image'] = $request->file('image')->store('products', 'public');
                }

                $product->update($validated);
                $product->unitTypes()->sync($validated['unit_type_ids']);
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function updateImage(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        try {
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                // Store new image
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
                $product->save();
            }

            return response()->json(['success' => true, 'message' => 'Image uploaded successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

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
