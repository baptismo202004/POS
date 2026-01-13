<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Purchase;


class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with(['product', 'branch'])->latest()->paginate(15);
        return view('SuperAdmin.stockin.index', compact('stockIns'));
    }

    public function create()
    {
        $products = Product::all();
        $branches = Branch::all();
        $purchases = Purchase::with('items')->get();
        return view('SuperAdmin.stockin.create', compact('products', 'branches', 'purchases'));
    }

    public function getProductsByPurchase(Purchase $purchase)
    {
        return response()->json($purchase->items()->with('product')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'purchase_id' => 'required|exists:purchases,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        $purchase = Purchase::with('items.product')->findOrFail($validated['purchase_id']);
        $stockInCount = 0;
        $errorMessages = [];

        foreach ($validated['items'] as $item) {
            if (empty($item['quantity']) || $item['quantity'] <= 0) {
                continue;
            }

            $purchaseItem = $purchase->items->firstWhere('product_id', $item['product_id']);

            if (!$purchaseItem) {
                return back()->withInput()->with('error', 'Invalid product found in the stock-in request.');
            }

            $totalStockedIn = StockIn::where('purchase_id', $validated['purchase_id'])
                                     ->where('product_id', $item['product_id'])
                                     ->sum('quantity');

            $availableQuantity = $purchaseItem->quantity - $totalStockedIn;

            if ($item['quantity'] > $availableQuantity) {
                $errorMessages[] = "Cannot stock in {$item['quantity']} for {$purchaseItem->product->product_name}. Only {$availableQuantity} remaining.";
                continue;
            }

            StockIn::create([
                'product_id' => $item['product_id'],
                'branch_id' => $validated['branch_id'],
                'purchase_id' => $validated['purchase_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
            $stockInCount++;
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
