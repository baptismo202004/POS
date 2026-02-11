<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Credit;
use App\Models\Product;
use App\Models\StockOut;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit',
            'customer_name' => 'nullable|string|max:255',
            'credit_due_date' => 'nullable|required_if:payment_method,credit|date',
            'credit_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed', 
                'errors' => $validator->errors(),
                'request_data' => $request->all()
            ], 422);
        }
        
        try {
            DB::transaction(function () use ($request) {
                // Debug: Log the items structure
                Log::info('Items structure:', $request->items);
                
                $branchId = $request->items[0]['branch_id'] ?? 1; // Default to 1 if not provided
                
                // Debug: Log branch info
                Log::info('Branch ID from first item: ' . $branchId);
                Log::info('First item data: ', $request->items[0] ?? []);

                $sale = Sale::create([
                    'cashier_id' => Auth::id() ?? 1,
                    'employee_id' => Auth::id() ?? 1,
                    'branch_id' => $branchId,
                    'total_amount' => $request->total,
                    'payment_method' => $request->payment_method,
                ]);

                foreach ($request->items as $item) {
                    // Debug: Log each item
                    Log::info('Processing item:', $item);
                    
                    $productId = $item['product_id'] ?? null;
                    
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Update inventory - deduct stock
                    $product = Product::find($productId);
                    if ($product) {
                        // Create StockOut record to track inventory deduction
                        StockOut::create([
                            'product_id' => $product->id,
                            'sale_id' => $sale->id,
                            'quantity' => $item['quantity'],
                            'branch_id' => $item['branch_id'] ?? $branchId,
                        ]);
                        
                        Log::info('StockOut created for product ' . $productId . ': ' . $item['quantity'] . ' units, sale_id: ' . $sale->id);
                    }
                }

                // If credit payment, create credit record
                if ($request->payment_method === 'credit') {
                    Log::info('Creating credit record with branch_id: ' . $branchId);
                    
                    Credit::create([
                        'sale_id' => $sale->id,
                        'cashier_id' => Auth::id() ?? 1,
                        'branch_id' => $branchId,
                        'credit_amount' => $request->total,
                        'paid_amount' => 0,
                        'remaining_balance' => $request->total,
                        'status' => 'active',
                        'date' => $request->credit_due_date ?? date('Y-m-d', strtotime('+30 days')),
                        'notes' => $request->credit_notes ?? '',
                    ]);
                    
                    Log::info('Credit record created successfully');
                }
            });

            return response()->json(['success' => true, 'message' => 'Order processed successfully']);

        } catch (\Exception $e) {
            Log::error('POS Checkout Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
