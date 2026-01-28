<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockOut;
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
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'sale_item_id' => 'required|exists:sale_items,id',
            'product_id' => 'required|exists:products,id',
            'quantity_refunded' => 'required|integer|min:1',
            'refund_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::transaction(function () use ($request) {
                $saleItem = SaleItem::find($request->sale_item_id);
                
                // Validate that refund quantity doesn't exceed sold quantity
                $totalRefunded = Refund::where('sale_item_id', $request->sale_item_id)
                    ->where('status', 'approved')
                    ->sum('quantity_refunded');
                    
                if ($totalRefunded + $request->quantity_refunded > $saleItem->quantity) {
                    throw new \Exception('Cannot refund more items than were sold');
                }

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

                // Update inventory - add back the refunded items
                $product = Product::find($request->product_id);
                if ($product) {
                    // Find or create inventory record for this product
                    $stockOut = StockOut::where('product_id', $product->id)
                        ->where('sale_item_id', $saleItem->id)
                        ->first();
                        
                    if ($stockOut) {
                        // Reduce the quantity from stock out (effectively adding back to inventory)
                        $stockOut->quantity -= $request->quantity_refunded;
                        if ($stockOut->quantity <= 0) {
                            $stockOut->delete();
                        } else {
                            $stockOut->save();
                        }
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Refund processed successfully']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
