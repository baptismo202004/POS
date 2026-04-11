<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\StockIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectronicPosController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $branchType = optional(\Illuminate\Support\Facades\Auth::user()->branch)->branch_type ?? 'electronics';

        return view('Admin.pos.electronics', compact('branchType'));
    }

    public function lookup(Request $request)
    {
        $keyword = trim((string) $request->input('barcode', ''));
        $mode = $request->input('mode', 'list');

        // Electronic POS always filters to electronic products
        $electronicsOnly = true;

        Log::info("[POS_ELECTRONICS_LOOKUP] keyword='{$keyword}', mode='{$mode}'");

        if (! empty($keyword)) {
            $request->validate(['barcode' => 'required|string']);
        }

        if ($mode !== 'list') {
            // Keep behavior compatible with existing UI; electronics page uses list mode.
            $mode = 'list';
        }

        $branchNames = Branch::pluck('branch_name', 'id');
        $allBranchIds = Branch::pluck('id')->values();

        $matchesQuery = Product::query();
        if ($electronicsOnly) {
            $matchesQuery
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->leftJoin('product_types', 'products.product_type_id', '=', 'product_types.id')
                ->where(function ($q) {
                    $q->whereRaw("LOWER(TRIM(categories.category_type)) LIKE 'electronic%'")
                        ->orWhere('product_types.is_electronic', true)
                        ->orWhere('product_types.type_name', 'LIKE', '%elect%');
                })
                ->select('products.*');
        }

        if (! empty($keyword)) {
            $matchesQuery->where(function ($q) use ($keyword) {
                $q->where('products.product_name', 'LIKE', "%{$keyword}%")
                    ->orWhere('products.barcode', 'LIKE', "%{$keyword}%")
                    ->orWhere('products.model_number', 'LIKE', "%{$keyword}%");
            });
        }

        $matches = $matchesQuery
            ->with('category')
            ->orderBy('products.product_name')
            ->limit(100)
            ->get();

        $items = $matches->map(function ($p) use ($branchNames, $allBranchIds) {
            $stockRecords = StockIn::with('unitType')
                ->where('product_id', $p->id)
                ->whereColumn('quantity', '>', 'sold')
                ->orderBy('id', 'asc')
                ->get();

            $totalStock = 0;
            $branchStocks = [];

            foreach ($allBranchIds as $bId) {
                $branchStocks[(int) $bId] = [
                    'branch_id' => (int) $bId,
                    'branch_name' => $branchNames[(int) $bId] ?? null,
                    'stock' => 0,
                    'stock_units' => [],
                    'latest_price' => 0.00,
                ];
            }

            $unitFactors = DB::table('product_unit_type')
                ->where('product_id', (int) $p->id)
                ->pluck('conversion_factor', 'unit_type_id')
                ->map(function ($v) {
                    $f = (float) $v;

                    return $f > 0 ? $f : 1.0;
                })
                ->toArray();

            foreach ($stockRecords as $stock) {
                $availableStock = $stock->quantity - $stock->sold;
                $totalStock += $availableStock;

                if ($availableStock > 0) {
                    $bId = (int) $stock->branch_id;
                    if (isset($branchStocks[$bId])) {
                        $branchStocks[$bId]['stock'] += (float) $availableStock;
                    }
                }
            }

            foreach ($branchStocks as $bId => $bData) {
                $latestStockIn = StockIn::with(['unitPrices.unitType'])
                    ->where('product_id', (int) $p->id)
                    ->where('branch_id', (int) $bId)
                    ->whereHas('unitPrices')
                    ->orderByDesc('id')
                    ->first();

                $units = [];
                if ($latestStockIn) {
                    foreach ($latestStockIn->unitPrices as $up) {
                        $unitTypeId = (int) ($up->unit_type_id ?? 0);
                        $price = (float) ($up->price ?? 0);
                        if ($unitTypeId <= 0 || $price <= 0) {
                            continue;
                        }

                        $factor = isset($unitFactors[$unitTypeId]) ? (float) $unitFactors[$unitTypeId] : 1.0;
                        if (! is_finite($factor) || $factor <= 0) {
                            $factor = 1.0;
                        }

                        $baseAvailable = (float) ($bData['stock'] ?? 0);
                        $unitAvailable = $factor > 0 ? ($baseAvailable / $factor) : 0;

                        $units[] = [
                            'unit_type_id' => $unitTypeId,
                            'unit_name' => optional($up->unitType)->unit_name,
                            'stock' => (int) round($unitAvailable),
                            'price' => (float) $price,
                        ];
                    }
                }

                $branchStocks[$bId]['stock_units'] = $units;
                $branchStocks[$bId]['latest_price'] = isset($units[0]) ? (float) ($units[0]['price'] ?? 0) : 0.00;
            }

            $branches = array_values(array_map(function ($branchData) {
                $price = (float) ($branchData['latest_price'] ?? 0.00);
                $units = $branchData['stock_units'] ?? [];

                if (is_array($units)) {
                    $units = array_values(array_map(function ($u) {
                        return [
                            'unit_type_id' => (int) ($u['unit_type_id'] ?? 0),
                            'unit_name' => $u['unit_name'] ?? null,
                            'stock' => (float) ($u['stock'] ?? 0),
                            'price' => (float) ($u['price'] ?? 0.00),
                        ];
                    }, $units));
                } else {
                    $units = [];
                }

                return [
                    'branch_id' => $branchData['branch_id'],
                    'branch_name' => $branchData['branch_name'],
                    'stock' => $branchData['stock'],
                    'stock_units' => $units,
                    'price' => $price,
                ];
            }, $branchStocks));

            $defaultPrice = isset($branches[0]) ? (float) ($branches[0]['price'] ?? 0) : 0.00;

            return [
                'product_id' => $p->id,
                'name' => $p->product_name,
                'barcode' => $p->barcode,
                'price' => $defaultPrice,
                'total_stock' => (int) $totalStock,
                'branches' => $branches,
                'category_type' => $p->category?->category_type ?? 'non_electronic',
                'warranty_coverage_months' => (int) ($p->warranty_coverage_months ?? 0),
            ];
        })->values();

        return response()->json(['items' => $items]);
    }
}
