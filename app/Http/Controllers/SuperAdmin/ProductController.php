<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRepair;
use App\Models\ProductSerial;
use App\Models\ProductType;
use App\Models\PurchaseItem;
use App\Models\Refund;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private function normalizeUnitName(?string $name): string
    {
        $name = (string) $name;
        $name = trim(mb_strtolower($name));
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }

    private function unitScalar(string $unitName): ?array
    {
        $n = $this->normalizeUnitName($unitName);

        $count = [
            'pc' => 1.0,
            'pcs' => 1.0,
            'piece' => 1.0,
            'pieces' => 1.0,
            'can' => 1.0,
            'cans' => 1.0,
            'dozen' => 12.0,
        ];

        if (array_key_exists($n, $count)) {
            return ['count', (float) $count[$n]];
        }

        $mass = [
            'mg' => 0.001,
            'milligram' => 0.001,
            'milligrams' => 0.001,
            'g' => 1.0,
            'gram' => 1.0,
            'grams' => 1.0,
            'kg' => 1000.0,
            'kilogram' => 1000.0,
            'kilograms' => 1000.0,
        ];

        if (array_key_exists($n, $mass)) {
            return ['mass', (float) $mass[$n]];
        }

        $volume = [
            'ml' => 1.0,
            'milliliter' => 1.0,
            'milliliters' => 1.0,
            'l' => 1000.0,
            'liter' => 1000.0,
            'liters' => 1000.0,
            'kl' => 1000000.0,
            'kiloliter' => 1000000.0,
            'kiloliters' => 1000000.0,
        ];

        if (array_key_exists($n, $volume)) {
            return ['volume', (float) $volume[$n]];
        }

        return null;
    }

    private function computeConversionFactor(?string $unitName, ?string $baseUnitName): ?float
    {
        if (! $unitName || ! $baseUnitName) {
            return null;
        }

        $u = $this->unitScalar($unitName);
        $b = $this->unitScalar($baseUnitName);

        if (! $u || ! $b) {
            return null;
        }

        if ($u[0] !== $b[0]) {
            return null;
        }

        $baseScalar = (float) $b[1];
        $unitScalar = (float) $u[1];

        if ($baseScalar <= 0 || $unitScalar <= 0) {
            return null;
        }

        // conversion_factor means: how many of this unit equals 1 base unit
        // Example: base=Kilogram(1000), unit=Gram(1) => 1000/1 = 1000 grams per 1 kilogram
        return round($baseScalar / $unitScalar, 6);
    }

    private function buildUnitTypeSyncData(Request $request, array $unitTypeIds): array
    {
        $unitTypeIds = array_values(array_unique(array_map('intval', $unitTypeIds)));
        if (count($unitTypeIds) === 0) {
            return [];
        }

        $baseUnitTypeId = (int) ($request->input('base_unit_type_id') ?? 0);
        if ($baseUnitTypeId <= 0 || ! in_array($baseUnitTypeId, $unitTypeIds, true)) {
            $baseUnitTypeId = (int) $unitTypeIds[0];
        }

        $units = UnitType::whereIn('id', $unitTypeIds)->get()->keyBy('id');
        $baseUnitName = optional($units->get($baseUnitTypeId))->unit_name;

        $syncData = [];
        foreach ($unitTypeIds as $unitTypeId) {
            $unit = $units->get($unitTypeId);
            $unitName = $unit?->unit_name;

            $isBase = ((int) $unitTypeId === (int) $baseUnitTypeId);
            $requestedFactor = $request->input('conversion_factor.'.$unitTypeId);

            if ($isBase) {
                $factor = 1.0;
            } else {
                $factor = is_numeric($requestedFactor) ? (float) $requestedFactor : null;
                if (is_null($factor)) {
                    $factor = $this->computeConversionFactor($unitName, $baseUnitName);
                }
                if (is_null($factor) || $factor <= 0) {
                    $factor = 1.0;
                }
            }

            $syncData[$unitTypeId] = [
                'conversion_factor' => $factor,
                'is_base' => $isBase,
            ];
        }

        return $syncData;
    }

    public function index(Request $request)
    {
        $sortBy = $request->query('sort_by', 'id');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');

        if (! in_array($sortBy, ['id', 'product_name', 'status'])) {
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

        $products = $productsQuery->orderBy($sortBy, $sortDirection)->paginate(100);

        if ($request->ajax()) {
            return view('SuperAdmin.products._product_table', compact('products'))->render();
        }

        return view('SuperAdmin.products.productIndex', [
            'products' => $products->appends($request->query()),
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function create()
    {
        return view('SuperAdmin.products.productList', [
            'brands' => Brand::where('status', 'active')->get(),
            'categories' => Category::where('status', 'active')->get(),
            'productTypes' => ProductType::all(),
            'unitTypes' => UnitType::all(),
        ]);
    }

    public function store(Request $request)
    {
        // Log incoming request data for debugging
        Log::info('Product creation attempt', [
            'request_data' => $request->all(),
            'files' => $request->files->all(),
        ]);

        $rules = [
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'barcode' => 'required|string|unique:products,barcode',
            'unit_type_ids' => 'required|array|min:1',
            'unit_type_ids.*' => 'exists:unit_types,id',
            'brand_id' => 'nullable',
            'category_id' => 'nullable',
            'model_number' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'warranty_type' => 'nullable|in:none,shop,manufacturer',
            'warranty_coverage_months' => 'nullable|integer|min:0',
            'voltage_specs' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
            'selling_price' => 'nullable|numeric|min:0',
            'base_unit_type_id' => 'nullable|integer|exists:unit_types,id',
            'conversion_factor' => 'nullable|array',
            'conversion_factor.*' => 'nullable|numeric|gt:0',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::error('Product validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all(),
                'product_type' => $request->input('product_type_id'),
            ]);

            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $validated = $validator->validated();

        try {
            $debugInfo = [];
            $createdProductId = null;
            DB::transaction(function () use ($request, $validated, &$debugInfo, &$createdProductId) {
                $categoryId = $validated['category_id'] ?? null;
                if (! empty($categoryId) && ! is_numeric($categoryId)) {
                    $c = Category::create(['category_name' => $categoryId, 'status' => 'active']);
                    $categoryId = $c->id;
                }

                $categoryType = null;
                if (! empty($categoryId)) {
                    $categoryType = Category::where('id', (int) $categoryId)->value('category_type');
                }

                $isElectronic = in_array($categoryType, ['electronic_with_serial', 'electronic_without_serial'], true);

                $debugInfo = [
                    'category_type' => $categoryType,
                    'is_electronic' => $isElectronic,
                ];

                if ($isElectronic) {
                    // For electronic products, store in products table with electronic-specific fields
                    $productData = [
                        'product_name' => $validated['product_name'],
                        'barcode' => $validated['barcode'],
                        'brand_id' => $validated['brand_id'] ?? null,
                        'category_id' => $categoryId,
                        'model_number' => $validated['model_number'] ?? null,
                        'warranty_type' => $validated['warranty_type'] ?? 'none',
                        'warranty_coverage_months' => $validated['warranty_coverage_months'] ?? null,
                        'voltage_specs' => $validated['voltage_specs'] ?? null,
                        'status' => $validated['status'],
                    ];

                    if (! empty($validated['brand_id']) && ! is_numeric($validated['brand_id'])) {
                        $b = Brand::create(['brand_name' => $validated['brand_id'], 'status' => 'active']);
                        $productData['brand_id'] = $b->id;
                    }

                    $productData['category_id'] = $categoryId;

                    if ($request->hasFile('image')) {
                        $productData['image'] = $request->file('image')->store('products', 'public');
                    }

                    $product = Product::create($productData);
                    $product->unitTypes()->sync($this->buildUnitTypeSyncData($request, $validated['unit_type_ids']));
                    $createdProductId = $product->id;

                } else {
                    // For non-electronic products, store in products table
                    Log::info('Processing non-electronic product', [
                        'brand_id' => $validated['brand_id'] ?? 'null',
                        'category_id' => $categoryId ?? 'null',
                    ]);

                    // Handle brand_id - check if it's numeric or text
                    if (! empty($validated['brand_id'])) {
                        if (! is_numeric($validated['brand_id'])) {
                            Log::info('Creating new brand', ['brand_name' => $validated['brand_id']]);
                            $b = Brand::create(['brand_name' => $validated['brand_id'], 'status' => 'active']);
                            $validated['brand_id'] = $b->id;
                            Log::info('New brand created with ID', ['brand_id' => $b->id]);
                        }
                    } else {
                        $validated['brand_id'] = null;
                    }

                    $validated['category_id'] = $categoryId;

                    unset($validated['product_type_id']);

                    if ($request->hasFile('image')) {
                        $validated['image'] = $request->file('image')->store('products', 'public');
                        Log::info('Image stored', ['path' => $validated['image']]);
                    }

                    Log::info('Creating product with data', ['validated' => $validated]);
                    $product = Product::create($validated);
                    Log::info('Product created successfully', ['product_id' => $product->id]);
                    $product->unitTypes()->sync($this->buildUnitTypeSyncData($request, $validated['unit_type_ids']));
                    Log::info('Unit types synced');
                    $createdProductId = $product->id;
                }
            });

            return response()->json(['success' => true, 'debug' => $debugInfo, 'product_id' => $createdProductId]);

        } catch (\Exception $e) {
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'validated_data' => $validated ?? 'none',
            ]);

            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: '.$e->getMessage()], 500);
        }
    }

    private function calculateWarrantyExpiry($warrantyType, $coverageMonths)
    {
        if ($warrantyType === 'none' || ! $coverageMonths) {
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
        $product->load(['unitTypes']);

        return view('SuperAdmin.products.productList', [
            'product' => $product,
            'brands' => Brand::where('status', 'active')->get(),
            'categories' => Category::where('status', 'active')->get(),
            'productTypes' => ProductType::all(),
            'unitTypes' => UnitType::all(),
        ]);
    }

    /**
     * Update product
     * PUT /products/{product}
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255|unique:products,product_name,'.$product->id,
            'barcode' => 'required|string|unique:products,barcode,'.$product->id,
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
            'selling_price' => 'nullable|numeric|min:0',
            'base_unit_type_id' => 'nullable|integer|exists:unit_types,id',
            'conversion_factor' => 'nullable|array',
            'conversion_factor.*' => 'nullable|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($request, $product, $validated) {
                if (! empty($request->input('brand_id')) && ! is_numeric($request->input('brand_id'))) {
                    $b = Brand::create(['brand_name' => $request->input('brand_id'), 'status' => 'active']);
                    $validated['brand_id'] = $b->id;
                }

                if (! empty($request->input('category_id')) && ! is_numeric($request->input('category_id'))) {
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
                $product->unitTypes()->sync($this->buildUnitTypeSyncData($request, $validated['unit_type_ids']));
            });

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred: '.$e->getMessage()], 500);
        }
    }

    public function storeUnitConversion(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'unit_type_id' => 'required|integer|exists:unit_types,id',
            'reference_unit_type_id' => 'required|integer|exists:unit_types,id',
            'conversion_factor' => 'required|numeric|gt:0',
            'is_base' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Validation failed.');
        }

        $data = $validator->validated();

        $product->loadMissing('unitTypes');

        $referenceUnitTypeId = (int) $data['reference_unit_type_id'];
        $allowedReferenceIds = $product->unitTypes->pluck('id')->map(fn ($v) => (int) $v)->all();
        if (! in_array($referenceUnitTypeId, $allowedReferenceIds, true)) {
            return redirect()->back()->with('error', 'Invalid reference unit selected.');
        }

        DB::transaction(function () use ($product, $data, $referenceUnitTypeId) {
            $unitTypeId = (int) $data['unit_type_id'];
            $isBase = ! empty($data['is_base']);

            $inputFactor = (float) $data['conversion_factor'];
            $referenceFactor = (float) (DB::table('product_unit_type')
                ->where('product_id', $product->id)
                ->where('unit_type_id', $referenceUnitTypeId)
                ->value('conversion_factor') ?? 0);

            if ($referenceFactor <= 0) {
                $referenceFactor = 1.0;
            }

            $computedFactor = round($referenceFactor * $inputFactor, 6);

            if ($isBase) {
                // If this unit already exists, use its current factor as the divisor to
                // convert all other factors to be relative to this new base.
                $existingFactor = (float) (DB::table('product_unit_type')
                    ->where('product_id', $product->id)
                    ->where('unit_type_id', $unitTypeId)
                    ->value('conversion_factor') ?? 0);

                $divisor = $existingFactor > 0 ? $existingFactor : $computedFactor;

                if ($divisor > 0) {
                    DB::table('product_unit_type')
                        ->where('product_id', $product->id)
                        ->update([
                            'conversion_factor' => DB::raw('conversion_factor / '.$divisor),
                        ]);
                }

                DB::table('product_unit_type')
                    ->where('product_id', $product->id)
                    ->update(['is_base' => 0]);
            }

            $product->unitTypes()->syncWithoutDetaching([
                $unitTypeId => [
                    'conversion_factor' => $isBase ? 1.0 : $computedFactor,
                    'is_base' => $isBase,
                ],
            ]);
        });

        return redirect()->back()->with('success', 'Unit conversion saved.');
    }

    public function updateUnitConversion(Request $request, Product $product, UnitType $unitType)
    {
        $validator = Validator::make($request->all(), [
            'conversion_factor' => 'required|numeric|gt:0',
            'is_base' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Validation failed.');
        }

        $data = $validator->validated();

        DB::transaction(function () use ($product, $unitType, $data) {
            $isBase = ! empty($data['is_base']);

            if ($isBase) {
                $divisor = (float) (DB::table('product_unit_type')
                    ->where('product_id', $product->id)
                    ->where('unit_type_id', $unitType->id)
                    ->value('conversion_factor') ?? 0);

                if ($divisor > 0) {
                    DB::table('product_unit_type')
                        ->where('product_id', $product->id)
                        ->update([
                            'conversion_factor' => DB::raw('conversion_factor / '.$divisor),
                        ]);
                }

                DB::table('product_unit_type')
                    ->where('product_id', $product->id)
                    ->update(['is_base' => 0]);
            }

            DB::table('product_unit_type')
                ->where('product_id', $product->id)
                ->where('unit_type_id', $unitType->id)
                ->update([
                    'conversion_factor' => $isBase ? 1.0 : (float) $data['conversion_factor'],
                    'is_base' => $isBase,
                    'updated_at' => now(),
                ]);
        });

        return redirect()->back()->with('success', 'Unit conversion updated.');
    }

    public function destroyUnitConversion(Product $product, UnitType $unitType)
    {
        $row = DB::table('product_unit_type')
            ->where('product_id', $product->id)
            ->where('unit_type_id', $unitType->id)
            ->first();

        if ($row && ! empty($row->is_base)) {
            return redirect()->back()->with('error', 'You cannot delete the base unit. Set another base unit first.');
        }

        $product->unitTypes()->detach($unitType->id);

        return redirect()->back()->with('success', 'Unit conversion deleted.');
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
            return response()->json(['success' => false, 'message' => 'An error occurred: '.$e->getMessage()], 500);
        }
    }

    public function lifecycle(Product $product): \Illuminate\View\View
    {
        $product->load(['brand', 'category', 'unitTypes', 'serials.branch', 'serials.saleItem.sale.cashier', 'serials.repairs.handledBy', 'branchStocks.branch']);

        $isElectronic = str_contains(strtolower((string) ($product->category?->category_type ?? '')), 'electronic');

        // ── Purchase history ───────────────────────────────────────────────────
        $purchaseItems = PurchaseItem::with(['purchase.supplier', 'purchase.branch', 'unitType'])
            ->where('product_id', $product->id)
            ->latest('id')
            ->get();

        // ── Stock-in records ───────────────────────────────────────────────────
        $stockIns = \App\Models\StockIn::with(['branch', 'purchase.supplier'])
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        // ── Stock transfers ────────────────────────────────────────────────────
        $transfers = StockTransfer::with(['fromBranch', 'toBranch'])
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        // ── Sales ──────────────────────────────────────────────────────────────
        $saleItems = \App\Models\SaleItem::with(['sale.cashier', 'sale.branch', 'sale.customer', 'unitType'])
            ->where('product_id', $product->id)
            ->latest('id')
            ->get();

        // ── Refunds ────────────────────────────────────────────────────────────
        $refunds = Refund::with(['cashier', 'sale.branch'])
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        // ── Repairs (electronics) ──────────────────────────────────────────────
        $repairs = ProductRepair::with(['handledBy', 'branch', 'productSerial'])
            ->where('product_id', $product->id)
            ->latest()
            ->get();

        // ── Stock movements ────────────────────────────────────────────────────
        $movements = StockMovement::with(['branch'])
            ->where('product_id', $product->id)
            ->orderByDesc('created_at')
            ->get();

        // ── Build unified timeline ─────────────────────────────────────────────
        $timeline = collect();

        foreach ($purchaseItems as $pi) {
            $timeline->push([
                'type' => 'purchase',
                'icon' => 'fa-shopping-cart',
                'color' => '#1976D2',
                'label' => 'Purchased',
                'date' => $pi->purchase->purchase_date ?? $pi->created_at,
                'summary' => 'Qty: '.number_format($pi->quantity, 0).' × ₱'.number_format($pi->unit_cost, 2).' = ₱'.number_format($pi->subtotal, 2),
                'detail' => 'Supplier: '.($pi->purchase->supplier->supplier_name ?? '—').' · Branch: '.($pi->purchase->branch->branch_name ?? '—').' · Ref: '.($pi->purchase->reference_number ?? '—'),
                'user' => null,
                'status' => $pi->purchase->payment_status ?? null,
                'raw' => $pi,
            ]);
        }

        foreach ($stockIns as $si) {
            $timeline->push([
                'type' => 'stock_in',
                'icon' => 'fa-arrow-down',
                'color' => '#10b981',
                'label' => 'Stock In',
                'date' => $si->created_at,
                'summary' => 'Qty: '.number_format($si->quantity, 0).' (sold: '.number_format($si->sold, 0).', remaining: '.number_format($si->quantity - $si->sold, 0).')',
                'detail' => 'Branch: '.($si->branch->branch_name ?? '—').($si->reason ? ' · Reason: '.$si->reason : ''),
                'user' => null,
                'status' => null,
                'raw' => $si,
            ]);
        }

        foreach ($transfers as $t) {
            $timeline->push([
                'type' => 'transfer',
                'icon' => 'fa-exchange-alt',
                'color' => '#8b5cf6',
                'label' => 'Branch Transfer',
                'date' => $t->created_at,
                'summary' => 'Qty: '.$t->quantity.' · '.($t->fromBranch->branch_name ?? '—').' → '.($t->toBranch->branch_name ?? '—'),
                'detail' => $t->notes ?? '',
                'user' => null,
                'status' => $t->status,
                'raw' => $t,
            ]);
        }

        foreach ($saleItems as $si) {
            $timeline->push([
                'type' => 'sale',
                'icon' => 'fa-cash-register',
                'color' => '#f59e0b',
                'label' => 'Sold',
                'date' => $si->sale->created_at ?? $si->created_at,
                'summary' => 'Qty: '.number_format($si->quantity, 2).' × ₱'.number_format($si->unit_price, 2).' = ₱'.number_format($si->subtotal, 2),
                'detail' => 'Sale #'.($si->sale->reference_number ?? $si->sale_id).' · Branch: '.($si->sale->branch->branch_name ?? '—').' · Customer: '.($si->sale->customer->full_name ?? 'Walk-in').' · Payment: '.ucfirst($si->sale->payment_method ?? '—'),
                'user' => $si->sale->cashier->name ?? null,
                'status' => $si->sale->status ?? null,
                'raw' => $si,
            ]);
        }

        foreach ($refunds as $r) {
            $timeline->push([
                'type' => 'refund',
                'icon' => 'fa-undo',
                'color' => '#ef4444',
                'label' => 'Refund',
                'date' => $r->created_at,
                'summary' => 'Qty: '.$r->quantity_refunded.' · ₱'.number_format($r->refund_amount, 2),
                'detail' => 'Reason: '.($r->reason ?? '—').($r->notes ? ' · Notes: '.$r->notes : ''),
                'user' => $r->cashier->name ?? null,
                'status' => $r->status,
                'raw' => $r,
            ]);
        }

        foreach ($repairs as $rep) {
            $timeline->push([
                'type' => 'repair',
                'icon' => 'fa-tools',
                'color' => '#0891b2',
                'label' => 'Repair / Warranty Claim',
                'date' => $rep->created_at,
                'summary' => ucfirst(str_replace('_', ' ', $rep->repair_type)).' · '.ucfirst(str_replace('_', ' ', $rep->status)).($rep->repair_cost > 0 ? ' · Cost: ₱'.number_format($rep->repair_cost, 2) : ''),
                'detail' => 'Issue: '.$rep->issue_description.($rep->resolution_notes ? ' · Resolution: '.$rep->resolution_notes : '').($rep->serial_number ? ' · S/N: '.$rep->serial_number : ''),
                'user' => $rep->handledBy->name ?? null,
                'status' => $rep->status,
                'raw' => $rep,
            ]);
        }

        $timeline = $timeline->sortByDesc('date')->values();

        // ── Serial summary for electronics ─────────────────────────────────────
        $serialSummary = null;
        if ($isElectronic) {
            $serials = $product->serials()->with(['branch', 'saleItem.sale.customer', 'repairs'])->get();
            $serialSummary = [
                'total' => $serials->count(),
                'in_stock' => $serials->where('status', 'in_stock')->count(),
                'sold' => $serials->where('status', 'sold')->count(),
                'returned' => $serials->where('status', 'returned')->count(),
                'defective' => $serials->where('status', 'defective')->count(),
                'warranty_active' => $serials->filter(fn ($s) => $s->warranty_expiry_date && $s->warranty_expiry_date->isFuture())->count(),
                'warranty_expired' => $serials->filter(fn ($s) => $s->warranty_expiry_date && $s->warranty_expiry_date->isPast())->count(),
                'items' => $serials,
            ];
        }

        $branches = \App\Models\Branch::all();
        $users = \App\Models\User::orderBy('name')->get();

        return view('SuperAdmin.products.lifecycle', compact(
            'product', 'isElectronic', 'timeline',
            'serialSummary', 'repairs', 'branches', 'users'
        ));
    }

    public function storeRepair(\Illuminate\Http\Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'serial_number' => 'nullable|string|max:255',
            'repair_type' => 'required|in:in_warranty,out_of_warranty,inspection',
            'status' => 'required|in:received,in_progress,repaired,returned,unrepairable',
            'issue_description' => 'required|string',
            'resolution_notes' => 'nullable|string',
            'repair_cost' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'returned_date' => 'nullable|date',
            'branch_id' => 'nullable|exists:branches,id',
            'handled_by' => 'nullable|exists:users,id',
        ]);

        $validated['product_id'] = $product->id;
        $validated['repair_cost'] = $validated['repair_cost'] ?? 0;

        if (! empty($validated['serial_number'])) {
            $serial = ProductSerial::where('serial_number', $validated['serial_number'])
                ->where('product_id', $product->id)
                ->first();
            if ($serial) {
                $validated['product_serial_id'] = $serial->id;
            }
        }

        $repair = ProductRepair::create($validated);

        return response()->json(['success' => true, 'repair' => $repair->load('handledBy', 'branch')]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
        }

        return redirect()
            ->route('superadmin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
