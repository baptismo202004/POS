<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReceiptTemplate;

class ReceiptTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Standard Sales Receipt',
                'type' => 'sale',
                'header_content' => '<div style="text-align: center; font-family: Arial;">
                    <h2>{{ $company_name ?? "BGH Pharmacy" }}</h2>
                    <p>{{ $company_address ?? "123 Main St, City" }}</p>
                    <p>Tel: {{ $company_phone ?? "123-456-7890" }}</p>
                    <hr>
                    <h3>SALES RECEIPT</h3>
                    <p>Receipt #: {{ $receipt_number ?? "0001" }}</p>
                    <p>Date: {{ $date ?? now()->format("M d, Y H:i") }}</p>
                    <p>Cashier: {{ $cashier ?? "Admin" }}</p>
                </div>',
                'body_content' => '<div style="margin: 20px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #000;">
                                <th style="text-align: left;">Item</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Price</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items ?? [] as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">₱{{ number_format($item->price, 2) }}</td>
                                <td style="text-align: right;">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                    <div style="text-align: right;">
                        <p>Subtotal: ₱{{ number_format($subtotal ?? 0, 2) }}</p>
                        <p>Tax: ₱{{ number_format($tax ?? 0, 2) }}</p>
                        <p><strong>Total: ₱{{ number_format($total ?? 0, 2) }}</strong></p>
                    </div>
                </div>',
                'footer_content' => '<div style="text-align: center; margin-top: 30px;">
                    <p>Payment Method: {{ $payment_method ?? "Cash" }}</p>
                    <p>{{ $thank_you_message ?? "Thank you for your purchase!" }}</p>
                    <p>Please come again!</p>
                    <hr>
                    <p style="font-size: 12px;">This is a computer-generated receipt</p>
                </div>',
                'css_styles' => 'body { font-family: Arial, sans-serif; padding: 20px; }',
                'settings' => json_encode(['font_size' => '12px', 'include_logo' => true]),
                'is_default' => true,
                'is_active' => true,
                'paper_size' => '80mm',
                'orientation' => 'portrait',
            ],
            [
                'name' => 'Refund Receipt',
                'type' => 'refund',
                'header_content' => '<div style="text-align: center; font-family: Arial;">
                    <h2>{{ $company_name ?? "BGH Pharmacy" }}</h2>
                    <h3>REFUND RECEIPT</h3>
                    <p>Refund #: {{ $refund_number ?? "R0001" }}</p>
                    <p>Date: {{ $date ?? now()->format("M d, Y H:i") }}</p>
                    <p>Original Receipt: {{ $original_receipt ?? "0001" }}</p>
                </div>',
                'body_content' => '<div style="margin: 20px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #000;">
                                <th style="text-align: left;">Item</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Price</th>
                                <th style="text-align: right;">Refund</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items ?? [] as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">₱{{ number_format($item->price, 2) }}</td>
                                <td style="text-align: right;">₱{{ number_format($item->refund_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                    <div style="text-align: right;">
                        <p><strong>Total Refund: ₱{{ number_format($total_refund ?? 0, 2) }}</strong></p>
                    </div>
                </div>',
                'footer_content' => '<div style="text-align: center; margin-top: 30px;">
                    <p>Refund Reason: {{ $refund_reason ?? "Customer Request" }}</p>
                    <p>Processed by: {{ $cashier ?? "Admin" }}</p>
                    <p>{{ $thank_you_message ?? "Thank you for your understanding!" }}</p>
                </div>',
                'css_styles' => 'body { font-family: Arial, sans-serif; padding: 20px; }',
                'settings' => json_encode(['font_size' => '12px']),
                'is_default' => false,
                'is_active' => true,
                'paper_size' => '80mm',
                'orientation' => 'portrait',
            ],
            [
                'name' => 'Purchase Order',
                'type' => 'purchase',
                'header_content' => '<div style="text-align: center; font-family: Arial;">
                    <h2>{{ $company_name ?? "BGH Pharmacy" }}</h2>
                    <h3>PURCHASE ORDER</h3>
                    <p>PO #: {{ $po_number ?? "PO0001" }}</p>
                    <p>Date: {{ $date ?? now()->format("M d, Y") }}</p>
                    <p>Supplier: {{ $supplier_name ?? "Supplier Name" }}</p>
                </div>',
                'body_content' => '<div style="margin: 20px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid #000;">
                                <th style="text-align: left;">Item</th>
                                <th style="text-align: center;">Qty</th>
                                <th style="text-align: right;">Unit Price</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items ?? [] as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right;">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td style="text-align: right;">₱{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <hr>
                    <div style="text-align: right;">
                        <p>Subtotal: ₱{{ number_format($subtotal ?? 0, 2) }}</p>
                        <p>Tax: ₱{{ number_format($tax ?? 0, 2) }}</p>
                        <p><strong>Total: ₱{{ number_format($total ?? 0, 2) }}</strong></p>
                    </div>
                </div>',
                'footer_content' => '<div style="text-align: center; margin-top: 30px;">
                    <p>Expected Delivery: {{ $delivery_date ?? "TBD" }}</p>
                    <p>Payment Terms: {{ $payment_terms ?? "Net 30" }}</p>
                    <p>Prepared by: {{ $prepared_by ?? "Admin" }}</p>
                </div>',
                'css_styles' => 'body { font-family: Arial, sans-serif; padding: 20px; }',
                'settings' => json_encode(['font_size' => '12px']),
                'is_default' => false,
                'is_active' => true,
                'paper_size' => 'A4',
                'orientation' => 'portrait',
            ],
        ];

        foreach ($templates as $template) {
            ReceiptTemplate::create($template);
        }
    }
}
