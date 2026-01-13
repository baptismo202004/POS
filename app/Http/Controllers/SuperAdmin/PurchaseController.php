<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ProductType;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
   
    public function index()
    {
        $purchases = Purchase::with(['items.product'])
            ->latest('purchase_date')
            ->paginate(15);

        return view('SuperAdmin.purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();
        $product_types = ProductType::all();
        $unit_types = UnitType::all();

        return view('SuperAdmin.purchases.create', compact('products', 'brands', 'categories', 'product_types', 'unit_types'));
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.reference_number' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $totalCost = 0;
            foreach ($validated['items'] as $item) {
                $totalCost += $item['quantity'] * $item['cost'];
            }

            $purchase = Purchase::create([
                'purchase_date' => $validated['purchase_date'],
                'total_cost' => $totalCost,
            ]);

            foreach ($validated['items'] as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'reference_number' => $item['reference_number'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $item['quantity'] * $item['cost'],
                ]);
            }
        });

        return redirect()->route('superadmin.purchases.index')->with('success', 'Purchase created successfully.');
    }
}