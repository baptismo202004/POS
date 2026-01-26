<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Branch;
use App\Models\StockTransfer;

class StockTransferController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $branches = Branch::all();
        $transfers = StockTransfer::with(['product', 'fromBranch', 'toBranch'])->latest()->paginate(15);

        return view('SuperAdmin.stocktransfer.index', compact('products', 'branches', 'transfers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $product = Product::find($request->product_id);
        $stockAtSource = $product->getStockAtBranch($request->from_branch_id);

        if ($stockAtSource < $request->quantity) {
            return back()->with('error', 'Not enough stock at the source branch.');
        }

        StockTransfer::create($request->all());

        return back()->with('success', 'Stock transfer request created successfully.');
    }

    public function update(Request $request, StockTransfer $stockTransfer)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'This transfer has already been processed.');
        }

        if ($request->status == 'approved') {
            $product = $stockTransfer->product;
            $stockAtSource = $product->getStockAtBranch($stockTransfer->from_branch_id);

            if ($stockAtSource < $stockTransfer->quantity) {
                $stockTransfer->update(['status' => 'rejected', 'notes' => 'Rejected due to insufficient stock at time of approval.']);
                return back()->with('error', 'Transfer rejected due to insufficient stock.');
            }

            // Deduct stock from source
            $product->stockOuts()->create([
                'branch_id' => $stockTransfer->from_branch_id,
                'quantity' => $stockTransfer->quantity,
                'reason' => 'Stock Transfer',
            ]);

            // Add stock to destination
            $product->stockIns()->create([
                'branch_id' => $stockTransfer->to_branch_id,
                'quantity' => $stockTransfer->quantity,
                'reason' => 'Stock Transfer',
            ]);

            $stockTransfer->update(['status' => 'approved']);
            return back()->with('success', 'Stock transfer approved and processed.');
        }

        $stockTransfer->update(['status' => 'rejected']);
        return back()->with('success', 'Stock transfer has been rejected.');
    }
}
