<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the purchases.
     */
    public function index()
    {
        $purchases = Purchase::with(['branch', 'items.product'])
            ->latest('purchase_date')
            ->paginate(15);

        return view('SuperAdmin.purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $branches = Branch::all();
        $products = Product::where('status', 'active')->get();
        $brands = Brand::where('status', 'active')->get();
        $categories = Category::where('status', 'active')->get();

        return view('SuperAdmin.purchases.create', compact('branches', 'products', 'brands', 'categories'));
    }

    /**
     * Store a newly created purchase in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $totalCost = 0;
            foreach ($validated['items'] as $item) {
                $totalCost += $item['quantity'] * $item['cost'];
            }

            $purchase = Purchase::create([
                'reference_number' => $validated['reference_number'],
                'purchase_date' => $validated['purchase_date'],
                'branch_id' => $validated['branch_id'],
                'total_cost' => $totalCost,
            ]);

            foreach ($validated['items'] as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $item['quantity'] * $item['cost'],
                ]);
            }
        });

        return redirect()->route('superadmin.purchases.index')->with('success', 'Purchase created successfully.');
    }
}