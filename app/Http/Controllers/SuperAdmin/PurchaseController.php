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
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $suppliers = Supplier::where('status', 'active')->get();

        return view('SuperAdmin.purchases.create', compact('products', 'brands', 'categories', 'product_types', 'unit_types', 'suppliers'));
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_date' => 'required|date',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_type_id' => 'required|exists:unit_types,id',
            'items.*.cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $totalCost = 0;
            foreach ($validated['items'] as $item) {
                $totalCost += $item['quantity'] * $item['cost'];
            }

            $purchase = Purchase::create([
                'purchase_date' => $validated['purchase_date'],
                'supplier_id' => $validated['supplier_id'],
                'total_cost' => $totalCost,
                'payment_status' => $validated['payment_status'],
                'reference_number' => $validated['reference_number'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $purchase->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_type_id' => $item['unit_type_id'],
                    'unit_cost' => $item['cost'],
                    'subtotal' => $item['quantity'] * $item['cost'],
                ]);
            }
        });

        return redirect()->route('superadmin.purchases.index')->with('success', 'Purchase created successfully.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.product', 'items.unitType');
        return view('SuperAdmin.purchases.show', compact('purchase'));
    }

    public function matchProduct(Request $request)
    {
        $text = $request->input('text', '');
        $lines = explode("\n", $text);
        $matchedProducts = [];
        $referenceNumber = null;

        // Extract Reference Number
        foreach ($lines as $line) {
            if (preg_match('/(?:REFERENCE NO|REF NO|REFERENCE):\s*([A-Z0-9-]+)/i', $line, $matches)) {
                $referenceNumber = trim($matches[1]);
                break;
            }
        }

        foreach ($lines as $line) {
            // Regex to capture quantity, item description, and price
            if (preg_match('/^(\d+)\s+(.+?)\s+(\d+\.\d{2})/', $line, $matches)) {
                $quantity = (int)$matches[1];
                $productName = trim($matches[2]);
                $cost = (float)$matches[3];

                // Find the product in the database
                $product = Product::where('product_name', 'like', '%' . $productName . '%')->first();

                if ($product) {
                    $matchedProducts[] = [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'quantity' => $quantity,
                        'cost' => $cost,
                    ];
                }
            }
        }

        return response()->json([
            'reference_number' => $referenceNumber,
            'products' => $matchedProducts
        ]);
    }
}