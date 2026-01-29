<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Credit;
use App\Models\Product;
use App\Models\StockOut;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    public function checkout(Request $request)
    {
        // Log the raw request data for debugging
        \Log::info('Raw request data:', $request->all());
        \Log::info('Items data:', $request->items);
        
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.branch_id' => 'nullable|exists:branches,id',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,credit',
            'customer_name' => 'required_if:payment_method,credit|string|max:255',
            'date' => 'nullable|required_if:payment_method,credit|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
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
            $sale = DB::transaction(function () use ($request) {
                $customerId = null;

                // If payment method is credit, create a new customer
                if ($request->payment_method === 'credit') {
                    $customer = Customer::create(['full_name' => $request->customer_name]);
                    $customerId = $customer->id;
                }
                // Debug: Log the items structure
                \Log::info('Items structure:', $request->items);
                
                $sale = Sale::create([
                    'cashier_id' => auth()->id() ?? 1,
                    'branch_id' => 1, // Add branch_id (default to 1)
                    'total_amount' => $request->total,
                    'payment_method' => $request->payment_method,
                    'product_names' => 'POS Sale',
                    'customer_id' => $customerId,
                    'customer_name' => $request->customer_name,
                ]);

                foreach ($request->items as $item) {
                    // Debug: Log each item
                    \Log::info('Processing item:', $item);
                    
                    $productId = $item['product_id'] ?? null;
                    $branchId = $item['branch_id'] ?? 1; // Default to branch 1 if not specified
                    
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $productId,
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Find stock records for the specific branch and update sold quantities
                    $stockRecords = \App\Models\StockIn::where('product_id', $productId)
                        ->where('branch_id', $branchId)
                        ->where('quantity', '>', DB::raw('sold'))
                        ->orderBy('id', 'asc')
                        ->get();

                    $remainingQuantity = $item['quantity'];

                    foreach ($stockRecords as $stock) {
                        if ($remainingQuantity <= 0) break;

                        $availableStock = $stock->quantity - $stock->sold;
                        $toDeduct = min($remainingQuantity, $availableStock);

                        $stock->sold += $toDeduct;
                        $stock->save();

                        $remainingQuantity -= $toDeduct;
                    }

                    if ($remainingQuantity > 0) {
                        throw new \Exception('Insufficient stock for product ID: ' . $productId . ' in branch: ' . $branchId);
                    }
                }

                // If credit payment, create credit record
                if ($request->payment_method === 'credit') {
                    Credit::create([
                        'customer_id' => $customerId,
                        'sale_id' => $sale->id,
                        'cashier_id' => auth()->id() ?? 1,
                        'credit_amount' => $request->total,
                        'paid_amount' => 0,
                        'remaining_balance' => $request->total,
                        'status' => 'active',
                        'date' => $request->date ?? date('Y-m-d'),
                        'notes' => $request->notes ?? '',
                    ]);
                }

                return $sale;
            });

            return response()->json(['success' => true, 'message' => 'Order processed successfully', 'order_id' => $sale->id]);

        } catch (\Exception $e) {
            \Log::error('POS Checkout Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
