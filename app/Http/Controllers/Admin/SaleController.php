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
        return view('Admin.sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $sale->load('saleItems.product');
        
        // Simple barcode generation using HTML5 canvas or image
        $barcode = $sale->id;
        
        $receiptUrl = route('superadmin.admin.sales.receipt', $sale);
        
        return view('Admin.sales.receipt', compact('sale', 'barcode'));
    }
}
