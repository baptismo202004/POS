<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSerial;
use App\Models\Sale;
use App\Models\StockIn;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function show(Sale $sale)
    {
        // Load related data needed for the summary and items table
        $sale->load(['saleItems.product', 'saleItems.refunds', 'branch', 'cashier', 'customer']);

        $serialsBySaleItemId = ProductSerial::whereIn('sale_item_id', $sale->saleItems->pluck('id')->filter())
            ->get()
            ->keyBy('sale_item_id');

        // Total quantity of items in this sale
        $totalQuantity = $sale->saleItems->sum('quantity');

        // Total refunded amount (only approved refunds)
        $totalRefunds = $sale->saleItems->sum(function ($item) {
            return $item->refunds->where('status', 'approved')->sum('refund_amount');
        });

        return view('Admin.sales.show', compact('sale', 'totalQuantity', 'totalRefunds', 'serialsBySaleItemId'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['saleItems.product', 'saleItems.unitType', 'cashier']);
        
        // Simple barcode generation using HTML5 canvas or image
        $barcode = $sale->id;
        
        $receiptUrl = route('admin.sales.receipt', $sale);
        
        return view('Admin.sales.receipt', compact('sale', 'barcode'));
    }

    public function receiptPdf(Sale $sale)
    {
        $sale->load(['saleItems.product', 'saleItems.unitType', 'cashier', 'branch', 'customer']);

        $serialsBySaleItemId = ProductSerial::whereIn('sale_item_id', $sale->saleItems->pluck('id')->filter())
            ->get()
            ->keyBy('sale_item_id');

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html = view('Admin.sales.receipt_pdf', compact('sale', 'serialsBySaleItemId'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'receipt-' . ($sale->reference_number ?: $sale->id) . '.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function markCompleted(Request $request, Sale $sale)
    {
        if ($sale->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending orders can be marked as completed.');
        }

        $sale->load(['saleItems.product', 'saleItems.unitType', 'cashier', 'branch']);

        $serials = $request->input('serials', []);
        if (!is_array($serials)) {
            $serials = [];
        }

        $expectedItemIds = $sale->saleItems->pluck('id')->map(fn ($v) => (string) $v)->toArray();
        $usedSerials = [];
        foreach ($sale->saleItems as $saleItem) {
            $trackingType = (string) ($saleItem->product->tracking_type ?? 'none');
            if ($trackingType === 'none') {
                continue;
            }

            if ((float) $saleItem->quantity !== 1.0) {
                return redirect()->back()->with('error', 'Serialized items must have quantity of 1 before completing.');
            }

            $saleItemId = (string) $saleItem->id;
            $value = isset($serials[$saleItemId]) ? trim((string) $serials[$saleItemId]) : '';
            if ($value === '') {
                return redirect()->back()->with('error', 'Serial number is required for all serialized items before completing.');
            }

            $key = strtolower($value);
            if (isset($usedSerials[$key])) {
                return redirect()->back()->with('error', 'Duplicate serial number entered: ' . $value);
            }
            $usedSerials[$key] = true;
        }

        try {
            DB::beginTransaction();

            foreach ($sale->saleItems as $saleItem) {
                $trackingType = (string) ($saleItem->product->tracking_type ?? 'none');
                $serialNumber = trim((string) ($serials[(string) $saleItem->id] ?? ''));
                $productId = (int) $saleItem->product_id;
                $branchId = (int) $sale->branch_id;
                $unitTypeId = (int) $saleItem->unit_type_id;
                $quantity = (float) $saleItem->quantity;

                // For serialized items, stock availability is validated by the serial itself.
                // Do not block completion on StockIn quantities if a valid serial exists.
                if ($trackingType !== 'none') {
                    $serial = ProductSerial::where('serial_number', $serialNumber)->first();
                    if (!$serial) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Invalid serial number: ' . $serialNumber);
                    }
                    if ((int) $serial->product_id !== (int) $productId) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Serial does not match product for one of the items.');
                    }
                    if ($serial->status === 'sold') {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Serial is already sold/assigned: ' . $serialNumber);
                    }
                    if (!empty($serial->sale_item_id) && (int) $serial->sale_item_id !== (int) $saleItem->id) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Serial is already assigned to another item: ' . $serialNumber);
                    }
                    if (!in_array((string) $serial->status, ['purchased', 'in_stock', 'assigned'], true)) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Serial is not eligible for sale (must be purchased, in_stock, or assigned): ' . $serialNumber);
                    }
                    if ((string) $serial->status === 'assigned' && (int) ($serial->sale_item_id ?? 0) !== (int) $saleItem->id) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Assigned serial does not match this item: ' . $serialNumber);
                    }
                    if (!empty($serial->branch_id) && (int) $serial->branch_id !== (int) $branchId) {
                        DB::rollBack();
                        return redirect()->back()->with('error', 'Serial does not belong to the selected branch: ' . $serialNumber);
                    }
                    if (empty($serial->branch_id)) {
                        $serial->branch_id = $branchId;
                    }
                    if (empty($serial->sale_item_id)) {
                        $serial->sale_item_id = $saleItem->id;
                    }

                    $warrantyMonths = (int) ($saleItem->warranty_months ?? 0);
                    $warrantyExpiry = null;
                    if ($warrantyMonths > 0) {
                        $warrantyExpiry = Carbon::now()->addMonths($warrantyMonths)->toDateString();
                    }

                    $serial->status = 'sold';
                    $serial->sold_at = Carbon::now();
                    $serial->warranty_expiry_date = $warrantyExpiry;
                    $serial->sale_item_id = $saleItem->id;
                    $serial->save();

                    continue;
                }

                $factor = (float) (DB::table('product_unit_type')
                    ->where('product_id', (int) $productId)
                    ->where('unit_type_id', (int) $unitTypeId)
                    ->value('conversion_factor') ?? 1);
                if ($factor <= 0) {
                    $factor = 1.0;
                }

                $requestedBaseQty = (float) $quantity * $factor;
                $stockRecords = StockIn::where('product_id', $productId)
                    ->where('branch_id', $branchId)
                    ->where('quantity', '>', DB::raw('sold'))
                    ->orderBy('id', 'asc')
                    ->get();

                $remainingBaseQuantity = $requestedBaseQty;
                foreach ($stockRecords as $stock) {
                    if ($remainingBaseQuantity <= 0) {
                        break;
                    }
                    $availableStock = $stock->quantity - $stock->sold;
                    $toDeduct = min($remainingBaseQuantity, $availableStock);
                    $stock->sold += $toDeduct;
                    $stock->save();
                    $remainingBaseQuantity -= $toDeduct;

                    \App\Models\StockOut::create([
                        'stock_in_id' => $stock->id,
                        'product_id' => $productId,
                        'sale_id' => $sale->id,
                        'quantity' => $toDeduct,
                        'branch_id' => $branchId,
                    ]);
                }

                if ($remainingBaseQuantity > 0) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Insufficient stock to complete this order.');
                }
            }

            $sale->status = 'completed';
            $sale->save();

            DB::commit();

            return redirect()
                ->route('admin.main.sales.show', $sale)
                ->with('success', 'Order marked as completed.')
                ->with('print_receipt', true);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to complete order: ' . $e->getMessage());
        }
    }

    public function updateNotes(Request $request, Sale $sale)
    {
        $notes = $request->input('notes');
        if ($notes !== null) {
            $notes = trim((string) $notes);
        }

        $sale->notes = $notes === '' ? null : $notes;
        $sale->save();

        return redirect()->back()->with('success', 'Notes updated successfully.');
    }

    public function saveSerial(Request $request, Sale $sale, $saleItem)
    {
        if ($sale->status !== 'pending') {
            return redirect()->back()->with('error', 'You can only save serials for pending orders.');
        }

        $sale->load(['saleItems.product']);
        $item = $sale->saleItems->firstWhere('id', (int) $saleItem);
        if (!$item) {
            return redirect()->back()->with('error', 'Invalid sale item.');
        }

        $trackingType = (string) ($item->product->tracking_type ?? 'none');
        if ($trackingType === 'none') {
            return redirect()->back()->with('error', 'This item does not require a serial number.');
        }

        $serialNumber = trim((string) $request->input('serial_number', ''));
        if ($serialNumber === '') {
            return redirect()->back()->with('error', 'Serial number is required.');
        }

        $serial = ProductSerial::where('serial_number', $serialNumber)->first();
        if (!$serial) {
            return redirect()->back()->with('error', 'Invalid serial number: ' . $serialNumber);
        }
        if ((int) $serial->product_id !== (int) $item->product_id) {
            return redirect()->back()->with('error', 'Serial does not match the selected product.');
        }
        if ($serial->status === 'sold') {
            return redirect()->back()->with('error', 'Serial is already sold: ' . $serialNumber);
        }
        if (!in_array((string) $serial->status, ['purchased', 'in_stock', 'assigned'], true)) {
            return redirect()->back()->with('error', 'Serial is not eligible (must be purchased, in_stock, or assigned): ' . $serialNumber);
        }
        if (!empty($serial->sale_item_id) && (int) $serial->sale_item_id !== (int) $item->id) {
            return redirect()->back()->with('error', 'Serial is already assigned to another item: ' . $serialNumber);
        }

        $branchId = (int) $sale->branch_id;
        if (!empty($serial->branch_id) && (int) $serial->branch_id !== (int) $branchId) {
            return redirect()->back()->with('error', 'Serial does not belong to the selected branch: ' . $serialNumber);
        }
        if (empty($serial->branch_id)) {
            $serial->branch_id = $branchId;
        }

        $serial->sale_item_id = $item->id;
        $serial->status = 'assigned';
        $serial->save();

        return redirect()->back()->with('success', 'Serial assigned for item: ' . ($item->product->product_name ?? 'Item'));
    }
}
