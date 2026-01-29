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
        return view('admin.sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load('saleItems.product');
        $generator = new BarcodeGeneratorHTML();
        $barcode = $generator->getBarcode($sale->id, $generator::TYPE_CODE_128);

        $receiptUrl = route('superadmin.admin.sales.receipt', $sale);
        // QR Code generation is disabled due to GD extension issues
        $qrCodeBase64 = null;

        return view('admin.sales.receipt', compact('sale', 'barcode', 'qrCodeBase64'));
    }
}
