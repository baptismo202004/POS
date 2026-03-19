<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function show(Sale $sale)
    {
        // Load related data needed for the summary and items table
        $sale->load(['saleItems.product', 'saleItems.refunds', 'branch', 'cashier']);

        // Total quantity of items in this sale
        $totalQuantity = $sale->saleItems->sum('quantity');

        // Total refunded amount (only approved refunds)
        $totalRefunds = $sale->saleItems->sum(function ($item) {
            return $item->refunds->where('status', 'approved')->sum('refund_amount');
        });

        return view('Admin.sales.show', compact('sale', 'totalQuantity', 'totalRefunds'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['saleItems.product', 'saleItems.unitType', 'cashier']);
        
        // Simple barcode generation using HTML5 canvas or image
        $barcode = $sale->id;
        
        $receiptUrl = route('admin.sales.receipt', $sale);
        
        return view('Admin.sales.receipt', compact('sale', 'barcode'));
    }
}
