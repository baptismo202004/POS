<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PosAdminController extends Controller
{
    public function index()
    {
        return view('Admin.pos.index');
    }

    public function lookup(Request $request)
    {
        // For POST requests, get data from form data
        $keyword = trim((string) $request->input('barcode', ''));
        $mode = $request->input('mode', 'list');
        
        Log::info("[POS_ADMIN_LOOKUP] keyword='{$keyword}', mode='{$mode}'");

        // Validate only if barcode is provided
        if (!empty($keyword)) {
            $request->validate(['barcode' => 'required|string']);
        }

        // First, let's check if any products exist at all
        $totalProducts = Product::count();
        $totalStockRecords = StockIn::count();
        Log::info("[POS_ADMIN_LOOKUP] Total products in DB: {$totalProducts}, Total stock records: {$totalStockRecords}");

        // Return test data if no products in database
        if ($totalProducts === 0) {
            return response()->json([
                'items' => [
                    [
                        'product_id' => 1,
                        'name' => 'Test Product 1 (No DB Products)',
                        'barcode' => 'TEST001',
                        'price' => 100.00,
                        'total_stock' => 5,
                        'branches' => [
                            ['branch_id' => 1, 'branch_name' => 'Main Branch', 'stock' => 5]
                        ]
                    ],
                    [
                        'product_id' => 2,
                        'name' => 'Test Product 2 (No DB Products)',
                        'barcode' => 'TEST002',
                        'price' => 200.00,
                        'total_stock' => 3,
                        'branches' => [
                            ['branch_id' => 1, 'branch_name' => 'Main Branch', 'stock' => 3]
                        ]
                    ]
                ]
            ]);
        }

        // List mode for typeahead/multi results
        if ($mode === 'list') {
            // If keyword is empty, get all products from stock_ins table
            if (empty($keyword)) {
                // Get all products that have stock records
                $productIds = StockIn::distinct('product_id')->pluck('product_id');
                $matches = Product::whereIn('id', $productIds)->get();
                Log::info("[POS_ADMIN_LOOKUP] Getting all products from stock_ins: " . count($matches) . " products found");
            } else {
                $matches = Product::query()
                    ->where(function ($q) use ($keyword) {
                        $q->where('product_name', 'LIKE', "%{$keyword}%")
                          ->orWhere('barcode', 'LIKE', "%{$keyword}%")
                          ->orWhere('model_number', 'LIKE', "%{$keyword}%");
                    })
                    ->limit(20)
                    ->get();
                Log::info("[POS_ADMIN_LOOKUP] Found " . count($matches) . " products matching keyword: '{$keyword}'");
            }

            $items = $matches->map(function ($p) {
                // Calculate available stock: quantity - sold
                $stockRecords = StockIn::where('product_id', $p->id)->get();
                $totalStock = 0;
                $branches = [];
                
                foreach ($stockRecords as $stock) {
                    $availableStock = $stock->quantity - $stock->sold;
                    $totalStock += $availableStock;
                    
                    if ($availableStock > 0) {
                        $branch = Branch::find($stock->branch_id);
                        $branches[] = [
                            'branch_id' => $stock->branch_id,
                            'branch_name' => optional($branch)->name,
                            'stock' => (int) $availableStock,
                        ];
                    }
                }
                
                $latestStockIn = StockIn::where('product_id', $p->id)->orderBy('id', 'desc')->first();
                $price = $latestStockIn && isset($latestStockIn->price) ? (float) $latestStockIn->price : 0.00;
                
                Log::info("[POS_ADMIN_LOOKUP] Product: {$p->product_name} (ID: {$p->id}) - Available Stock: {$totalStock}");
                
                return [
                    'product_id' => $p->id,
                    'name' => $p->product_name,
                    'barcode' => $p->barcode,
                    'price' => $price,
                    'total_stock' => (int) $totalStock,
                    'branches' => $branches,
                ];
            })->filter(function ($item) {
                // Only show products with available stock > 0
                $hasStock = $item['total_stock'] > 0;
                Log::info("[POS_ADMIN_LOOKUP] Product: {$item['name']} - Stock: {$item['total_stock']}, Has Stock: " . ($hasStock ? 'YES' : 'NO'));
                return $hasStock;
            })->values();

            Log::info("[POS_ADMIN_LOOKUP] Returning " . count($items) . " items for keyword: '{$keyword}'");
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

        // Calculate available stock: quantity - sold
        $stockRecords = StockIn::where('product_id', $product->id)->get();
        $totalStock = 0;
        $byBranch = [];
        
        foreach ($stockRecords as $stock) {
            $availableStock = $stock->quantity - $stock->sold;
            $totalStock += $availableStock;
            
            if ($availableStock > 0) {
                $branch = Branch::find($stock->branch_id);
                $byBranch[] = [
                    'branch_id' => $stock->branch_id,
                    'branch_name' => optional($branch)->name,
                    'stock' => (int) $availableStock,
                ];
            }
        }

        if ($totalStock <= 0) {
            return response()->json(['error' => 'Product is out of stock'], 422);
        }

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

    public function store(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $items = $data['items'];
            $total = $data['total'];
            
            Log::info("[POS_STORE] Processing order with " . count($items) . " items, total: ₱{$total}");
            
            DB::beginTransaction();
            
            // Create sale record with all required fields
            $sale = Sale::create([
                'cashier_id' => auth()->id(),
                'employee_id' => auth()->user()->employee_id ?? 'EMP' . auth()->id(),
                'customer_id' => null, // Walk-in customer
                'branch_id' => auth()->user()->branch_id ?? 1, // Default to branch 1 or user's branch
                'total_amount' => $total,
                'tax' => 0, // No tax for now
                'payment_method' => 'cash' // Default payment method
            ]);
            
            Log::info("[POS_STORE] Created sale record: {$sale->id}");
            
            // Process each item
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                
                Log::info("[POS_STORE] Processing item: Product {$productId}, Quantity: {$quantity}");
                
                // Find stock records and update sold quantities
                $stockRecords = StockIn::where('product_id', $productId)
                    ->where('quantity', '>', DB::raw('sold'))
                    ->orderBy('id', 'asc')
                    ->get();
                
                $remainingQuantity = $quantity;
                $updatedStock = 0;
                
                foreach ($stockRecords as $stock) {
                    if ($remainingQuantity <= 0) break;
                    
                    $availableStock = $stock->quantity - $stock->sold;
                    $toDeduct = min($remainingQuantity, $availableStock);
                    
                    $stock->sold += $toDeduct;
                    $stock->save();
                    
                    $remainingQuantity -= $toDeduct;
                    $updatedStock += $toDeduct;
                    
                    Log::info("[POS_STORE] Updated stock record {$stock->id}: +{$toDeduct} sold, remaining: {$remainingQuantity}");
                }
                
                if ($remainingQuantity > 0) {
                    DB::rollBack();
                    Log::error("[POS_STORE] Insufficient stock for product {$productId}");
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$item['name']}"
                    ], 422);
                }
                
                // Create sale item record
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $price * $quantity
                ]);
                
                Log::info("[POS_STORE] Created sale item for product {$productId}");
            }
            
            DB::commit();
            
            Log::info("[POS_STORE] Order processed successfully: Sale #{$sale->id}");
            
            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully',
                'order_id' => $sale->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[POS_STORE] Order processing failed: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateCashier(Request $request)
    {
        // Placeholder for validateCashier method
        return response()->json(['message' => 'Cashier validation not implemented yet']);
    }
}
