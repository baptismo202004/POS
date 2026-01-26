<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\Branch;

class PosAdminController extends Controller
{
    public function index()
    {
        return view('Admin.pos.index');
    }

    public function lookup(Request $request)
    {
        $keyword = trim((string) $request->input('barcode'));
        Log::info("[POS_ADMIN_LOOKUP] keyword='{$keyword}'");

        $request->validate(['barcode' => 'required|string']);

        // List mode for typeahead/multi results
        if ($request->query('mode') === 'list') {
            $matches = Product::query()
                ->where(function ($q) use ($keyword) {
                    $q->where('product_name', 'LIKE', "%{$keyword}%")
                      ->orWhere('barcode', 'LIKE', "%{$keyword}%")
                      ->orWhere('model_number', 'LIKE', "%{$keyword}%");
                })
                ->limit(20)
                ->get();

            $items = $matches->map(function ($p) {
                $totalStock = StockIn::where('product_id', $p->id)->sum('quantity') ?? 0;
                $branches = StockIn::where('product_id', $p->id)
                    ->selectRaw('branch_id, SUM(quantity) as qty')
                    ->groupBy('branch_id')
                    ->get()
                    ->map(function ($row) {
                        $branch = Branch::find($row->branch_id);
                        return [
                            'branch_id' => $row->branch_id,
                            'branch_name' => optional($branch)->name,
                            'stock' => (int) $row->qty,
                        ];
                    });
                $latestStockIn = StockIn::where('product_id', $p->id)->orderBy('id', 'desc')->first();
                $price = $latestStockIn && isset($latestStockIn->price) ? (float) $latestStockIn->price : 0.00;
                return [
                    'product_id' => $p->id,
                    'name' => $p->product_name,
                    'barcode' => $p->barcode,
                    'price' => $price,
                    'total_stock' => (int) $totalStock,
                    'branches' => $branches,
                ];
            });

            return response()->json(['items' => $items]);
        }

        // Exact matching phases similar to cashier POS
        $product = Product::where('barcode', $keyword)->first()
            ?: Product::where('model_number', $keyword)->first()
            ?: Product::where('product_name', $keyword)->first()
            ?: Product::where('product_name', 'LIKE', "%{$keyword}%")->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $totalStock = StockIn::where('product_id', $product->id)->sum('quantity') ?? 0;
        $byBranch = StockIn::where('product_id', $product->id)
            ->selectRaw('branch_id, SUM(quantity) as qty')
            ->groupBy('branch_id')
            ->get()
            ->map(function ($row) {
                $branch = Branch::find($row->branch_id);
                return [
                    'branch_id' => $row->branch_id,
                    'branch_name' => optional($branch)->name,
                    'stock' => (int) $row->qty,
                ];
            });

        $latestStockIn = StockIn::where('product_id', $product->id)->orderBy('id', 'desc')->first();
        $price = $latestStockIn && isset($latestStockIn->price) ? (float) $latestStockIn->price : 0.00;

        return response()->json([
            'product_id' => $product->id,
            'name' => $product->product_name,
            'barcode' => $product->barcode,
            'price' => $price,
            'total_stock' => (int) $totalStock,
            'branches' => $byBranch,
        ]);
    }
}
