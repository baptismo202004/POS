<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockOut;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $refunds = Refund::with(['sale', 'saleItem.product', 'cashier'])
            ->whereHas('sale', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.refunds.index', compact('refunds'));
    }

    public function create()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $sales = Sale::with(['items.product'])
            ->where('branch_id', $branchId)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cashier.refunds.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $request->validate([
            'sale_item_id' => 'required|exists:sale_items,id',
            'quantity_refunded' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $saleItem = SaleItem::with(['sale', 'product'])->findOrFail($request->sale_item_id);

            if ($saleItem->sale->branch_id !== $branchId) {
                abort(403, 'Unauthorized access to this sale item');
            }

            if ($request->quantity_refunded > $saleItem->quantity) {
                return back()->withInput()->with('error', 'Refund quantity cannot exceed original quantity');
            }

            $refundAmount = $request->quantity_refunded * $saleItem->unit_price;

            $refund = Refund::create([
                'sale_id' => $saleItem->sale_id,
                'sale_item_id' => $saleItem->id,
                'product_id' => $saleItem->product_id,
                'quantity_refunded' => $request->quantity_refunded,
                'refund_amount' => $refundAmount,
                'reason' => $request->reason,
                'status' => 'approved',
                'processed_by' => $user->id,
                'refund_date' => now(),
            ]);

            StockOut::where('sale_id', $saleItem->sale_id)
                ->where('product_id', $saleItem->product_id)
                ->decrement('quantity', $request->quantity_refunded);

            DB::commit();

            return redirect()->route('cashier.refunds.index')
                ->with('success', 'Refund processed successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Error processing refund: '.$e->getMessage());
        }
    }

    public function show(Refund $refund)
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        if (! $branchId) {
            abort(403, 'No branch assigned to this cashier');
        }

        $refund->load(['sale', 'saleItem.product', 'cashier']);

        if ($refund->sale->branch_id !== $branchId) {
            abort(403, 'Unauthorized access to this refund');
        }

        return view('cashier.refunds.show', compact('refund'));
    }
}
