<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Product;
use App\Models\Sale;
use App\Services\CustomerService;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (! in_array($sortBy, ['id', 'total_amount', 'created_at', 'payment_method'])) {
            $sortBy = 'created_at';
        }

        $query = Sale::with(['items.product', 'cashier'])
            ->where('branch_id', $branchId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('items.product', function ($subQ) use ($search) {
                        $subQ->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $sales = $query->orderBy($sortBy, $sortDirection)
            ->paginate(15);

        return view('cashier.sales.index', [
            'sales' => $sales->appends($request->query()),
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $products = Product::with(['unitTypes'])
            ->where('status', 'active')
            ->get();

        return view('cashier.sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,gcash,other,credit',
            'customer_name' => 'nullable|string|max:255',
            'customer_contact' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:255',
            'credit_due_date' => 'nullable|date',
            'credit_notes' => 'nullable|string|max:1000',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.unit_type_id' => 'required|exists:unit_types,id',
            'items.*.branch_id' => 'nullable|exists:branches,id',
        ]);

        try {
            DB::transaction(function () use ($validated, $branchId, $user) {
                $inventory = app(InventoryService::class);
                $subtotal = 0;
                $saleItems = [];
                $customerId = null;

                // Handle customer creation/lookup for credit sales or when customer info is provided
                if ($validated['payment_method'] === 'credit' ||
                    ! empty($validated['customer_name']) ||
                    ! empty($validated['customer_phone'])) {

                    $customerData = [
                        'full_name' => $validated['customer_name'] ?? '',
                        'phone' => $validated['customer_phone'] ?? $validated['customer_contact'] ?? null,
                        'email' => $validated['customer_email'] ?? null,
                        'address' => $validated['customer_address'] ?? null,
                    ];

                    $customer = CustomerService::findOrCreateCustomer($customerData, $branchId);
                    $customerId = $customer->id;
                }

                // Group items by branch and calculate totals
                $itemsByBranch = [];
                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    // Use provided branch_id or fallback to cashier's branch
                    $itemBranchId = $item['branch_id'] ?? $branchId;
                    $itemSubtotal = $item['quantity'] * $item['unit_price'];
                    $subtotal += $itemSubtotal;

                    $baseQty = $inventory->convertToBaseQuantity((int) $item['product_id'], (int) $item['unit_type_id'], (float) $item['quantity']);

                    $currentBase = $inventory->availableStockBase((int) $item['product_id'], (int) $itemBranchId);
                    if ($currentBase < $baseQty) {
                        throw new \Exception("Insufficient stock for {$product->product_name} at Branch {$itemBranchId}. Available: {$currentBase}, Required: {$baseQty}");
                    }

                    if (! isset($itemsByBranch[$itemBranchId])) {
                        $itemsByBranch[$itemBranchId] = [
                            'items' => [],
                            'subtotal' => 0,
                        ];
                    }

                    $itemsByBranch[$itemBranchId]['items'][] = [
                        'product_id' => $item['product_id'],
                        'quantity' => (float) $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $itemSubtotal,
                        'unit_type_id' => $item['unit_type_id'],
                    ];
                    $itemsByBranch[$itemBranchId]['subtotal'] += $itemSubtotal;
                }

                $totalAmount = $subtotal;

                // Apply discount
                $discountAmount = 0;
                if (! empty($validated['discount_amount'])) {
                    $discountAmount = $validated['discount_amount'];
                } elseif (! empty($validated['discount_percentage'])) {
                    $discountAmount = $totalAmount * ($validated['discount_percentage'] / 100);
                }

                $totalAmount -= $discountAmount;

                // Generate receipt group ID if multiple branches are involved
                $receiptGroupId = null;
                if (count($itemsByBranch) > 1) {
                    $receiptGroupId = 'RCP-'.date('Ymd').'-'.str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
                }

                $sales = [];
                $branchProportions = [];

                // Calculate proportional amounts for each branch
                foreach ($itemsByBranch as $branchId => $branchData) {
                    $branchProportions[$branchId] = $branchData['subtotal'] / $subtotal;
                }

                // Get current count for reference number generation
                $currentSaleCount = Sale::whereDate('created_at', today())->count();

                // Create sales for each branch
                foreach ($itemsByBranch as $branchId => $branchData) {
                    // Calculate proportional amounts for this branch
                    $branchTotalAmount = $totalAmount * $branchProportions[$branchId];
                    $branchDiscountAmount = $discountAmount * $branchProportions[$branchId];
                    $branchSubtotal = $branchData['subtotal'];

                    // Create unique reference number for each branch sale
                    $currentSaleCount++;
                    $referenceNumber = 'REF-'.date('Ymd').'-'.str_pad($currentSaleCount, 4, '0', STR_PAD_LEFT);

                    // Debug logging
                    Log::info('Creating sale with reference: '.$referenceNumber);
                    Log::info('Branch ID: '.$branchId);
                    Log::info('Total Amount: '.$branchTotalAmount);

                    $sale = Sale::create([
                        'branch_id' => $branchId,
                        'cashier_id' => $user->id,
                        'customer_id' => $customerId,
                        'total_amount' => $branchTotalAmount,
                        'subtotal' => $branchSubtotal,
                        'discount_amount' => $branchDiscountAmount,
                        'payment_method' => $validated['payment_method'],
                        'status' => 'completed',
                        'reference_number' => $referenceNumber,
                        'receipt_group_id' => $receiptGroupId,
                    ]);

                    Log::info('Sale created with ID: '.$sale->id.' and reference: '.$sale->reference_number);

                    // Create sale items for this branch
                    foreach ($branchData['items'] as $item) {
                        $sale->items()->create($item);
                    }

                    $sales[] = $sale;

                    // Update stock for this branch
                    foreach ($branchData['items'] as $item) {
                        $baseQty = $inventory->convertToBaseQuantity((int) $item['product_id'], (int) $item['unit_type_id'], (float) $item['quantity']);
                        $inventory->decreaseStock((int) $branchId, (int) $item['product_id'], (float) $baseQty, 'sale', 'sales', (int) $sale->id, now());
                    }
                }

                // Create credit if payment method is credit (only once for the entire receipt)
                if ($validated['payment_method'] === 'credit' && $customerId) {
                    CustomerService::createCredit([
                        'customer' => [
                            'full_name' => $validated['customer_name'] ?? '',
                            'phone' => $validated['customer_phone'] ?? $validated['customer_contact'] ?? null,
                            'email' => $validated['customer_email'] ?? null,
                            'address' => $validated['customer_address'] ?? null,
                        ],
                        'credit_amount' => $totalAmount,
                        'sale_id' => $sales[0]->id, // Use first sale as reference
                        'status' => 'active',
                        'date' => $validated['credit_due_date'] ?? now()->addDays(30), // Use provided date or default 30 days
                        'notes' => $validated['credit_notes'] ?? 'Credit from POS Sale #'.($receiptGroupId ?? $sales[0]->id),
                        'credit_type' => 'sales',
                    ], $branchId, $user->id);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Sale completed successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Sale creation error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show(Sale $sale)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($sale->branch_id !== $branchId) {
            abort(403, 'Unauthorized access');
        }

        $sale->load(['items.product', 'items.unitType', 'cashier', 'customer', 'credit.customer']);

        return view('cashier.sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($sale->branch_id !== $branchId) {
            abort(403, 'Unauthorized access');
        }

        // Load the current sale with its relationships
        $sale->load(['items.product', 'items.unitType', 'cashier', 'branch', 'customer', 'credit.customer']);

        // If this sale is part of a receipt group, load all related sales
        $relatedSales = collect([]);
        $receiptGroupId = $sale->receipt_group_id;

        if ($receiptGroupId) {
            $relatedSales = Sale::with(['items.product', 'items.unitType', 'branch'])
                ->where('receipt_group_id', $receiptGroupId)
                ->where('id', '!=', $sale->id)
                ->get();
        }

        return view('cashier.sales.receipt', compact('sale', 'relatedSales', 'receiptGroupId'));
    }

    public function void(Sale $sale)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        if ($sale->branch_id !== $branchId) {
            abort(403, 'Unauthorized access');
        }

        if ($sale->status === 'voided') {
            return response()->json([
                'success' => false,
                'message' => 'Sale is already voided',
            ]);
        }

        try {
            DB::transaction(function () use ($sale, $branchId, $user) {
                // Restore stock
                $restockItems = [];
                foreach ($sale->items as $item) {
                    $restockItems[] = [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_type_id' => $item->unit_type_id,
                    ];
                }

                $this->addStock($restockItems, $branchId);

                // Update sale status
                $sale->update([
                    'status' => 'voided',
                    'voided_by' => $user->id,
                    'voided_at' => now(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Sale voided successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error voiding sale: '.$e->getMessage(),
            ]);
        }
    }

    public function getProductPrice($productId, Request $request)
    {
        $unitTypeId = $request->query('unit_type_id');
        $branchId = $request->query('branch_id', Auth::user()->branch_id);

        if ($unitTypeId) {
            // Get price from product_unit_type table for specific unit type
            try {
                $price = DB::table('product_unit_type')
                    ->where('product_id', $productId)
                    ->where('unit_type_id', $unitTypeId)
                    ->value('price');

                if ($price !== null) {
                    return response()->json(['success' => true, 'price' => $price]);
                }
            } catch (\Exception $e) {
                // Fall through to stock-based pricing
            }
        }

        // Fallback: latest purchase unit cost for this product (not branch-specific)
        $latestCost = (float) (DB::table('purchase_items')
            ->where('product_id', (int) $productId)
            ->orderByDesc('id')
            ->value('unit_cost') ?? 0);

        if ($latestCost > 0) {
            return response()->json([
                'success' => true,
                'price' => $latestCost,
            ]);
        }

        // Final fallback to product default price
        $product = Product::find($productId);

        return response()->json([
            'success' => true,
            'price' => $product->price ?? 0,
        ]);
    }

    public function checkStock($productId, $branchId)
    {
        $inventory = app(InventoryService::class);
        $currentStock = $inventory->availableStockBase((int) $productId, (int) $branchId);

        return response()->json([
            'success' => true,
            'current_stock_base' => (float) $currentStock,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->query('q');
        $branchId = Auth::user()->branch_id;

        $inventory = app(InventoryService::class);

        $products = Product::with(['unitTypes'])
            ->where('status', 'active')
            ->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        $productsWithStock = $products->map(function ($product) use ($branchId, $inventory) {
            $product->current_stock = $inventory->availableStockBase((int) $product->id, (int) $branchId);

            $unitRows = DB::table('product_unit_type')
                ->join('unit_types', 'unit_types.id', '=', 'product_unit_type.unit_type_id')
                ->where('product_unit_type.product_id', $product->id)
                ->select('unit_types.id', 'unit_types.unit_name', 'product_unit_type.conversion_factor', 'product_unit_type.is_base')
                ->get();

            $baseUnit = $unitRows->firstWhere('is_base', 1);
            $packUnit = $unitRows
                ->filter(fn ($u) => (int) ($u->is_base ?? 0) !== 1 && (float) ($u->conversion_factor ?? 0) > 1)
                ->sortByDesc('conversion_factor')
                ->first();

            if ($baseUnit && $packUnit && (float) $packUnit->conversion_factor > 0) {
                $packFactor = (float) $packUnit->conversion_factor;
                $packs = (int) floor((float) $product->current_stock / $packFactor);
                $remainder = (float) ((float) $product->current_stock - ($packs * $packFactor));
                $product->stock_breakdown = [
                    'unit' => $packUnit->unit_name,
                    'packs' => $packs,
                    'remainder_base_unit' => $baseUnit->unit_name,
                    'remainder' => $remainder,
                ];
            }

            return $product;
        });

        return response()->json($productsWithStock);
    }

    public function lookupCustomer(Request $request)
    {
        $phone = $request->query('phone');
        $name = $request->query('name');

        try {
            if ($phone) {
                $customer = \App\Models\Customer::where('phone', $phone)
                    ->where('status', 'active')
                    ->first();
            } elseif ($name) {
                $customer = \App\Models\Customer::where('full_name', 'like', "%{$name}%")
                    ->where('status', 'active')
                    ->first();
            } else {
                return response()->json(['success' => false, 'message' => 'No search criteria provided']);
            }

            if ($customer) {
                return response()->json(['success' => true, 'customer' => $customer]);
            } else {
                return response()->json(['success' => false, 'message' => 'Customer not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error looking up customer']);
        }
    }

    public function reports(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $dateFrom = $request->query('date_from', Carbon::today()->startOfMonth());
        $dateTo = $request->query('date_to', Carbon::today()->endOfDay());

        $sales = Sale::with(['items.product', 'cashier'])
            ->where('branch_id', $branchId)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalTransactions = $sales->count();

        return view('cashier.sales.reports', compact('sales', 'totalSales', 'totalTransactions', 'dateFrom', 'dateTo'));
    }
}
