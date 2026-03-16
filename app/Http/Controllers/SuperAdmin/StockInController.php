<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('stock_movements')
                ->join('products', 'stock_movements.product_id', '=', 'products.id')
                ->join('branches', 'stock_movements.branch_id', '=', 'branches.id')
                ->whereIn('stock_movements.movement_type', ['purchase', 'adjustment', 'transfer'])
                ->select([
                    'stock_movements.*',
                    'products.product_name',
                    'branches.branch_name',
                ]);
            
            // Apply sorting if requested
            if ($request->has('sort')) {
                $direction = $request->get('direction', 'asc');
                switch ($request->get('sort')) {
                    case 'product':
                        $query->orderBy('products.product_name', $direction);
                        break;
                    default:
                        $query->orderBy('stock_movements.created_at', 'desc');
                }
            } else {
                $query->orderBy('stock_movements.created_at', 'desc');
            }
            
            $stockIns = $query->paginate(15);
            
            return view('SuperAdmin.stockin.index', compact('stockIns'));
        } catch (\Exception $e) {
            Log::error('Error loading Stock In index page: ' . $e->getMessage());
            return response()->view('errors.500', ['message' => 'Error loading stock records: ' . $e->getMessage()], 500);
        }
    }

    public function create()
    {
        // Eagerly load unit types to ensure they are available in the view
        $products = Product::with('unitTypes')->get();
        $branches = Branch::all();
        $purchases = Purchase::with('items.product.unitTypes')->get();
        return view('SuperAdmin.stockin.create', compact('products', 'branches', 'purchases'));
    }

    public function getProductsByPurchase(Purchase $purchase)
    {
        // Load items with product and its unitTypes
        $items = $purchase->items()->with('product.unitTypes')->get();
        
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'purchase_id' => 'required|exists:purchases,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_type_id' => 'nullable|exists:unit_types,id',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $purchase = Purchase::with('items.product')->findOrFail($validated['purchase_id']);
        $stockInCount = 0;
        $errorMessages = [];

        $groupedItems = collect($validated['items'])->groupBy('product_id');

        foreach ($groupedItems as $productId => $items) {
            $purchaseItem = $purchase->items->firstWhere('product_id', $productId);

            if (!$purchaseItem) {
                return back()->withInput()->with('error', 'Invalid product found in the stock-in request.');
            }

            $alreadyStockedBase = (float) DB::table('stock_movements')
                ->where('source_type', 'purchases')
                ->where('source_id', $validated['purchase_id'])
                ->where('product_id', $productId)
                ->where('movement_type', 'purchase')
                ->sum('quantity_base');

            $purchaseUnitTypeId = (int) ($purchaseItem->unit_type_id ?? 0);
            $purchaseFactor = (float) (DB::table('product_unit_type')
                ->where('product_id', $productId)
                ->where('unit_type_id', $purchaseUnitTypeId)
                ->value('conversion_factor') ?? 1);
            $purchaseFactor = $purchaseFactor > 0 ? $purchaseFactor : 1;
            $purchasedBase = (float) $purchaseItem->quantity * $purchaseFactor;

            $availableBase = $purchasedBase - $alreadyStockedBase;

            $currentStockInQuantity = 0;
            foreach ($items as $item) {
                $qty = (int) ($item['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $unitTypeId = !empty($item['unit_type_id']) ? (int) $item['unit_type_id'] : null;
                $factor = 1;
                if ($unitTypeId) {
                    $factor = (float) (DB::table('product_unit_type')
                        ->where('product_id', $productId)
                        ->where('unit_type_id', $unitTypeId)
                        ->value('conversion_factor') ?? 1);
                }

                $currentStockInQuantity += (float) ($qty * $factor);
            }

            if ($currentStockInQuantity > $availableBase) {
                $productName = $purchaseItem->product->product_name;
                $errorMessages[] = "Cannot stock in {$currentStockInQuantity} base units for {$productName}. Only {$availableBase} remaining.";
                continue;
            }

            foreach ($items as $item) {
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                $unitTypeId = !empty($item['unit_type_id']) ? (int) $item['unit_type_id'] : null;
                $factor = 1;
                if ($unitTypeId) {
                    $factor = (float) (DB::table('product_unit_type')
                        ->where('product_id', $item['product_id'])
                        ->where('unit_type_id', $unitTypeId)
                        ->value('conversion_factor') ?? 1);
                }

                $baseQty = (float) (((int) $item['quantity']) * $factor);

                DB::transaction(function () use ($validated, $item, $baseQty) {
                    DB::table('branch_stocks')->updateOrInsert(
                        ['product_id' => (int) $item['product_id'], 'branch_id' => (int) $validated['branch_id']],
                        ['quantity_base' => 0, 'created_at' => now(), 'updated_at' => now()]
                    );

                    DB::table('branch_stocks')
                        ->where('product_id', (int) $item['product_id'])
                        ->where('branch_id', (int) $validated['branch_id'])
                        ->update([
                            'quantity_base' => DB::raw('quantity_base + ' . (float) $baseQty),
                            'updated_at' => now(),
                        ]);

                    DB::table('stock_movements')->insert([
                        'product_id' => (int) $item['product_id'],
                        'branch_id' => (int) $validated['branch_id'],
                        'source_type' => 'purchases',
                        'source_id' => (int) $validated['purchase_id'],
                        'movement_type' => 'purchase',
                        'quantity_base' => (float) $baseQty,
                        'created_at' => now(),
                    ]);
                });

                $stockInCount++;
            }
        }

        if (!empty($errorMessages)) {
            return back()->withInput()->with('error', implode('<br>', $errorMessages));
        }

        if ($stockInCount > 0) {
            return redirect()->route('superadmin.stockin.index')
                             ->with('success', 'Stock adjusted successfully.');
        }

        return back()->withInput()->with('error', 'No items were stocked in. Please provide a quantity for at least one item.');
    }
}
