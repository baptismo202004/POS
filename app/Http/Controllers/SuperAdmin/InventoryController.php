<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $branches = Branch::all();
        $sortBy = $request->query('sort_by', 'product_name');
        $sortDirection = $request->query('sort_direction', 'asc');
        $search = $request->query('search');
        $filter = $request->query('filter', 'all');
        
        if (!in_array($sortBy, ['product_name', 'current_stock', 'total_sold', 'total_revenue'])) {
            $sortBy = 'product_name';
        }
        
        $productsQuery = Product::with(['brand', 'category', 'stockIns', 'stockOuts', 'saleItems']);
        
        if ($search) {
            $productsQuery->where('product_name', 'like', "%{$search}%")
                          ->orWhereHas('brand', function ($q) use ($search) {
                              $q->where('brand_name', 'like', "%{$search}%");
                          })
                          ->orWhereHas('category', function ($q) use ($search) {
                              $q->where('category_name', 'like', "%{$search}%");
                          });
        }
        
        // Apply filter based on parameter
        if ($filter !== 'all') {
            switch ($filter) {
                case 'out-of-stock':
                    // Filter will be applied after getting results using the accessor
                    break;
            }
        }
        
        $products = $productsQuery->get()->sortBy([
            [$sortBy, $sortDirection]
        ]);
        
        // Apply out-of-stock filter if needed
        if ($filter === 'out-of-stock') {
            $products = $products->filter(function($product) {
                return $product->current_stock <= 10;
            });
        }
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 15),
            $products->count(),
            15,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        return view('SuperAdmin.inventory.index', [
            'products' => $products->appends($request->query()),
            'branches' => $branches,
            'sortBy' => $sortBy,
            'sortDirection' => $sortDirection,
            'filter' => $filter
        ])->with('branchesJson', $branches->toJson());
    }

    public function stockIn(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $product->stockIns()->create([
            'quantity' => $request->quantity,
            'branch_id' => $request->branch_id,
            'reason' => 'Stock In'
        ]);

        return back()->with('success', 'Stock added successfully.');
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $currentStock = $product->current_stock;
        $newStock = $request->quantity;

        if ($newStock > $currentStock) {
            $product->stockIns()->create([
                'quantity' => $newStock - $currentStock,
                'branch_id' => $request->branch_id,
                'price' => 0, // Default price for stock adjustments
            ]);
        } elseif ($newStock < $currentStock) {
            $product->stockOuts()->create([
                'quantity' => $currentStock - $newStock,
                'branch_id' => $request->branch_id,
            ]);
        }

        return back()->with('success', 'Stock adjusted successfully.');
    }
}
