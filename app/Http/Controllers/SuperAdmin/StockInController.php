<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Purchase;
use Illuminate\Support\Facades\Log;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Get stock ins with relationships
            $query = StockIn::with(['product', 'branch', 'purchase']);
            
            // Apply sorting if requested
            if ($request->has('sort')) {
                $direction = $request->get('direction', 'asc');
                switch ($request->get('sort')) {
                    case 'product':
                        $query->join('products', 'stock_ins.product_id', '=', 'products.id')
                              ->orderBy('products.product_name', $direction);
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
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
            'items.*.price' => 'required|numeric|min:0',
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

            $totalStockedIn = StockIn::where('purchase_id', $validated['purchase_id'])
                                     ->where('product_id', $productId)
                                     ->sum('quantity');

            $availableQuantity = $purchaseItem->quantity - $totalStockedIn;
            $currentStockInQuantity = $items->sum('quantity');

            if ($currentStockInQuantity > $availableQuantity) {
                $productName = $purchaseItem->product->product_name;
                $errorMessages[] = "Cannot stock in {$currentStockInQuantity} for {$productName}. Only {$availableQuantity} remaining.";
                continue;
            }

            foreach ($items as $item) {
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    continue;
                }

                StockIn::create([
                    'product_id' => $item['product_id'],
                    'branch_id' => $validated['branch_id'],
                    'purchase_id' => $validated['purchase_id'],
                    'unit_type_id' => !empty($item['unit_type_id']) ? $item['unit_type_id'] : null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
                $stockInCount++;
            }
        }

        if (!empty($errorMessages)) {
            return back()->withInput()->with('error', implode('<br>', $errorMessages));
        }

        if ($stockInCount > 0) {
            return redirect()->route('superadmin.stockin.index')
                             ->with('success', $stockInCount . ' item(s) have been successfully stocked in.');
        }

        return back()->withInput()->with('error', 'No items were stocked in. Please provide a quantity for at least one item.');
    }
}
