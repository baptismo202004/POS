<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SaleService;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class PosController extends Controller
{
    protected $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function validateCashier(Request $request)
    {
        try {
            $data = $request->validate([
                'employee_id' => 'required|string',
            ]);

            $employeeId = trim($data['employee_id']);
            Log::info("[POS_VALIDATE_CASHIER] Validating employee_id='{$employeeId}'");

            $user = User::where('employee_id', $employeeId)->first();
            if (!$user) {
                Log::warning("[POS_VALIDATE_CASHIER] employee_id not found: '{$employeeId}'");
                return response()->json(['error' => 'Employee not found'], 404);
            }

            $name = $user->name ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            $name = $name ?: $employeeId;
            Log::info("[POS_VALIDATE_CASHIER] employee_id validated. name='{$name}'");
            return response()->json([
                'employee_id' => $employeeId,
                'name' => $name,
                'user_id' => $user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('[POS_VALIDATE_CASHIER] Error: ' . $e->getMessage());
            return response()->json(['error' => 'Validation error'], 500);
        }
    }

    public function index()
    {
        return view('pos.index');
    }

    public function lookup(Request $request)
    {
        $barcode = $request->input('barcode');
        Log::info("[POS_LOOKUP] Received request for keyword: '{$barcode}'");

        try {
            $request->validate(['barcode' => 'required|string']);

            $keyword = trim($barcode);

            // If list mode requested, return multiple matches for manual typing
            if ($request->query('mode') === 'list') {
                Log::info("[POS_LOOKUP] List mode enabled for keyword: '{$keyword}'");
                $matches = Product::query()
                    ->where(function ($q) use ($keyword) {
                        $q->where('product_name', 'LIKE', "%{$keyword}%")
                          ->orWhere('barcode', 'LIKE', "%{$keyword}%")
                          ->orWhere('model_number', 'LIKE', "%{$keyword}%");
                    })
                    ->limit(10)
                    ->get();

                $items = $matches->map(function ($p) {
                    $stock = StockIn::where('product_id', $p->id)->sum('quantity') ?? 0;
                    $latestStockIn = StockIn::where('product_id', $p->id)->orderBy('id', 'desc')->first();
                    $price = $latestStockIn && isset($latestStockIn->price) ? (float) $latestStockIn->price : 0.00;
                    return [
                        'product_id' => $p->id,
                        'name' => $p->product_name,
                        'barcode' => $p->barcode,
                        'price' => $price,
                        'stock' => $stock,
                    ];
                });

                Log::info("[POS_LOOKUP] List mode returning ".count($items)." items for keyword: '{$keyword}'");
                return response()->json(['items' => $items]);
            }

            $product = null;

            // Phase 1: Exact barcode
            Log::info("[POS_LOOKUP] Phase 1 - Exact barcode match for: '{$keyword}'");
            $product = Product::where('barcode', $keyword)->first();

            // Phase 2: Exact SKU/Model Number (if not found)
            if (!$product) {
                Log::info("[POS_LOOKUP] Phase 1 - No match. Proceeding to Phase 2 - Exact SKU/model_number for: '{$keyword}'");
                $product = Product::where('model_number', $keyword)->first();
            }

            // Phase 3: Exact product name (if not found)
            if (!$product) {
                Log::info("[POS_LOOKUP] Phase 2 - No match. Proceeding to Phase 3 - Exact product_name for: '{$keyword}'");
                $product = Product::where('product_name', $keyword)->first();
            }

            // Phase 4: Partial product name LIKE %keyword% (if not found)
            if (!$product) {
                Log::info("[POS_LOOKUP] Phase 3 - No match. Proceeding to Phase 4 - Partial product_name LIKE for: '{$keyword}'");
                $product = Product::where('product_name', 'LIKE', "%{$keyword}%")->first();
            }

            if (!$product) {
                Log::warning("[POS_LOOKUP] No product matched after all phases for keyword: '{$keyword}'");
                return response()->json(['error' => 'Product not found'], 404);
            }
            Log::info("[POS_LOOKUP] Match found: {$product->product_name} (ID: {$product->id})");

            // Explicit and safe stock calculation using only stock_ins
            $currentStock = StockIn::where('product_id', $product->id)->sum('quantity') ?? 0;
            Log::info("[POS_LOOKUP] Calculated stock for Product ID {$product->id}: IN={$currentStock}, CURRENT={$currentStock}");

            if ($currentStock <= 0) {
                Log::warning("[POS_LOOKUP] Product is out of stock: {$product->product_name}");
                return response()->json(['error' => 'Product is out of stock'], 422);
            }

            // Use latest StockIn price as selling price fallback
            $latestStockIn = StockIn::where('product_id', $product->id)->orderBy('id', 'desc')->first();
            $rawPrice = $latestStockIn && isset($latestStockIn->price) ? $latestStockIn->price : null;
            $price = is_null($rawPrice) ? 0.00 : (float) $rawPrice;
            Log::info("[POS_LOOKUP] Retrieved price for Product ID {$product->id} from latest StockIn: {$price}");

            $responseData = [
                'product_id' => $product->id,
                'name' => $product->product_name,
                'price' => $price,
                'stock' => $currentStock,
            ];
            Log::info("[POS_LOOKUP] Sending successful response payload: ", $responseData);

            return response()->json($responseData);

        } catch (\Exception $e) {
            Log::error("[POS_LOOKUP] An unexpected error occurred for barcode '{$barcode}': " . $e->getMessage());
            return response()->json(['error' => 'An internal server error occurred.'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'employee_id' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        try {
            $saleData = $request->all();
            $saleData['cashier_id'] = Auth::id();
            $saleData['employee_id'] = $request->input('employee_id');
            $saleData['customer_id'] = $request->input('customer_id');
            $saleData['tax'] = $request->input('tax', 0);
            $saleData['branch_id'] = $request->input('branch_id') ?? optional(Auth::user())->branch_id;

            $sale = $this->saleService->processSale($saleData);

            return response()->json(['success' => 'Sale completed successfully.', 'sale_id' => $sale->id], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
