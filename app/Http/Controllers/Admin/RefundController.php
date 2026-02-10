<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockOut;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    public function index()
    {
        // Get today's refunds data
        $today = \Carbon\Carbon::today();
        
        $todayRefunds = \App\Models\Refund::whereDate('created_at', $today)
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_refunds, COALESCE(SUM(refund_amount), 0) as total_refund_amount, COALESCE(SUM(quantity_refunded), 0) as total_items')
            ->first();
        
        // Get this month's refunds
        $thisMonth = \Carbon\Carbon::now()->startOfMonth();
        $monthlyRefunds = \App\Models\Refund::whereDate('created_at', '>=', $thisMonth)
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as total_refunds, COALESCE(SUM(refund_amount), 0) as total_refund_amount, COALESCE(SUM(quantity_refunded), 0) as total_items')
            ->first();
        
        // Get recent refunds for the table
        $refunds = Refund::with(['sale', 'saleItem.product', 'product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('Admin.refunds.index', compact(
            'refunds',
            'todayRefunds',
            'monthlyRefunds'
        ));
    }

    public function create(Request $request)
    {
        $saleId = $request->get('sale_id');
        $saleItemId = $request->get('sale_item_id');
        
        $sale = Sale::with(['saleItems.product', 'cashier'])->find($saleId);
        $saleItem = SaleItem::with('product')->find($saleItemId);
        
        if (!$sale || !$saleItem) {
            return response()->json(['error' => 'Sale or sale item not found'], 404);
        }
        
        return view('Admin.refunds.create', compact('sale', 'saleItem'));
    }

    public function store(Request $request)
    {
        try {
            // Debug: Log incoming request data
            \Log::info('Refund request data: ' . json_encode($request->all()));
            
            $validator = Validator::make($request->all(), [
                'sale_id' => 'required|exists:sales,id',
                'sale_item_id' => 'required|exists:sale_items,id',
                'product_id' => 'required|exists:products,id',
                'quantity_refunded' => 'required|integer|min:1',
                'refund_amount' => 'required|numeric|min:0',
                'reason' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                \Log::error('Refund validation failed: ' . json_encode($validator->errors()->toArray()));
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            \Log::info('Validation passed, starting transaction');
            
            DB::transaction(function () use ($request) {
                \Log::info('Finding sale item: ' . $request->sale_item_id);
                $saleItem = SaleItem::find($request->sale_item_id);
                
                if (!$saleItem) {
                    \Log::error('Sale item not found: ' . $request->sale_item_id);
                    throw new \Exception('Sale item not found');
                }
                
                \Log::info('Sale item found, checking existing refunds');
                // Validate that refund quantity doesn't exceed sold quantity
                $totalRefunded = Refund::where('sale_item_id', $request->sale_item_id)
                    ->where('status', 'approved')
                    ->sum('quantity_refunded');
                    
                \Log::info('Total refunded: ' . $totalRefunded . ', requested: ' . $request->quantity_refunded . ', sold: ' . $saleItem->quantity);
                    
                if ($totalRefunded + $request->quantity_refunded > $saleItem->quantity) {
                    throw new \Exception('Cannot refund more items than were sold');
                }

                \Log::info('Creating refund record');
                // Create refund record
                $refund = Refund::create([
                    'sale_id' => $request->sale_id,
                    'sale_item_id' => $request->sale_item_id,
                    'product_id' => $request->product_id,
                    'cashier_id' => auth()->id(),
                    'quantity_refunded' => $request->quantity_refunded,
                    'refund_amount' => $request->refund_amount,
                    'reason' => $request->reason,
                    'status' => 'approved', // Auto-approve for simplicity
                    'notes' => $request->notes,
                ]);
                
                \Log::info('Refund created with ID: ' . $refund->id);

                // Update sale total amount by deducting refund amount
                $sale = \App\Models\Sale::find($request->sale_id);
                if ($sale) {
                    $currentTotal = $sale->total_amount;
                    $newTotal = $currentTotal - $request->refund_amount;
                    
                    $sale->total_amount = max(0, $newTotal);
                    $sale->save();
                    
                    \Log::info('Sale total updated: ' . $currentTotal . ' -> ' . $sale->total_amount . ' (Refund: ' . $request->refund_amount . ')');
                }

                // Update inventory - add back refunded items
                $product = Product::find($request->product_id);
                if ($product) {
                    \Log::info('Updating inventory for product: ' . $product->id);
                    
                    // Try to find and update StockIn record (primary method)
                    $stockIn = \App\Models\StockIn::where('product_id', $product->id)
                        ->where('branch_id', auth()->user()->branch_id ?? 1)
                        ->first();
                        
                    if ($stockIn) {
                        // Reduce sold count (add back to inventory)
                        $newSold = max(0, $stockIn->sold - $request->quantity_refunded);
                        $stockIn->sold = $newSold;
                        $stockIn->save();
                        \Log::info('Updated StockIn sold count: ' . $stockIn->sold . ' (reduced by ' . $request->quantity_refunded . ')');
                    } else {
                        \Log::warning('No StockIn record found for product: ' . $product->id . ', creating new record');
                        
                        // Create new StockIn record if none exists
                        \App\Models\StockIn::create([
                            'product_id' => $product->id,
                            'branch_id' => auth()->user()->branch_id ?? 1,
                            'quantity' => $request->quantity_refunded,
                            'sold' => 0,
                            'price' => $request->refund_amount / $request->quantity_refunded,
                        ]);
                        \Log::info('Created new StockIn record for refunded items');
                    }
                    
                    // Try to update StockOut record (secondary method)
                    $stockOut = StockOut::where('sale_id', $request->sale_id)
                        ->where('product_id', $request->product_id)
                        ->first();
                        
                    if ($stockOut) {
                        \Log::info('Found stock out record, updating quantity');
                        $newQuantity = max(0, $stockOut->quantity - $request->quantity_refunded);
                        
                        if ($newQuantity <= 0) {
                            $stockOut->delete();
                            \Log::info('StockOut record deleted (fully refunded)');
                        } else {
                            $stockOut->quantity = $newQuantity;
                            $stockOut->save();
                            \Log::info('StockOut quantity updated to: ' . $stockOut->quantity);
                        }
                    } else {
                        \Log::info('No StockOut record found - this is normal for some sales');
                    }
                } else {
                    \Log::warning('Product not found: ' . $request->product_id);
                }
                
                \Log::info('Refund transaction completed successfully');
            });

            \Log::info('Returning success response');
            return response()->json(['success' => true, 'message' => 'Refund processed successfully']);

        } catch (\Exception $e) {
            \Log::error('Refund processing error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Error processing refund: ' . $e->getMessage()], 500);
        }
    }

    public function approve(Refund $refund)
    {
        try {
            DB::transaction(function () use ($refund) {
                $refund->update(['status' => 'approved']);
                
                // Update inventory
                $product = $refund->product;
                if ($product) {
                    $stockOut = StockOut::where('product_id', $product->id)
                        ->where('sale_item_id', $refund->sale_item_id)
                        ->first();
                        
                    if ($stockOut) {
                        $stockOut->quantity -= $refund->quantity_refunded;
                        if ($stockOut->quantity <= 0) {
                            $stockOut->delete();
                        } else {
                            $stockOut->save();
                        }
                    }
                }
            });

            return redirect()->back()->with('success', 'Refund approved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error approving refund: ' . $e->getMessage());
        }
    }

    public function reject(Refund $refund)
    {
        $refund->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Refund rejected successfully');
    }
}
