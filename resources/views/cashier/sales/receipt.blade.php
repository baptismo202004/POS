<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Receipt #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }
        
        .company-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .receipt-number {
            font-weight: bold;
            color: #333;
        }
        
        .receipt-date {
            color: #666;
        }
        
        .customer-info {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .customer-info div {
            margin-bottom: 5px;
        }
        
        .items-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        
        .items-table th {
            text-align: left;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        
        .items-table td {
            padding: 8px 0;
            font-size: 14px;
        }
        
        .item-name {
            font-weight: 500;
        }
        
        .item-details {
            font-size: 12px;
            color: #666;
        }
        
        .totals-section {
            border-top: 2px dashed #333;
            padding-top: 20px;
            margin-bottom: 20px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .total-row.grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 8px;
        }
        
        .payment-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .payment-method {
            background: #f0f8ff;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 5px;
            font-weight: 500;
        }
        
        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        
        .cashier-info {
            margin-bottom: 10px;
        }
        
        .thank-you {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        
        .actions {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
                padding: 20px;
            }
            
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="company-name">{{ config('app.name', 'POS System') }}</div>
            <div class="company-address">{{ $sale->branch->branch_name ?? 'Main Branch' }}</div>
            <div class="company-address">{{ $sale->branch->address ?? '123 Main St, City' }}</div>
            <div class="company-address">Tel: {{ $sale->branch->phone ?? '123-456-7890' }}</div>
        </div>

        <!-- Receipt Info -->
        <div class="receipt-info">
            <div class="receipt-number">Receipt #{{ $sale->id }}</div>
            <div class="receipt-date">{{ $sale->created_at->format('M d, Y h:i A') }}</div>
        </div>

        <!-- Customer Info -->
        @if($sale->customer)
        <div class="customer-info">
            <div><strong>Customer:</strong> {{ $sale->customer->full_name }}</div>
            @if($sale->customer->phone)
                <div><strong>Contact:</strong> {{ $sale->customer->phone }}</div>
            @endif
        </div>
        @endif

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: right;">Qty</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->product->product_name }}</div>
                        @if($item->unitType)
                            <div class="item-details">{{ $item->unitType->name }}</div>
                        @endif
                    </td>
                    <td style="text-align: right;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">₱{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">₱{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₱{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if($sale->discount_amount > 0)
            <div class="total-row">
                <span>Discount:</span>
                <span>-₱{{ number_format($sale->discount_amount, 2) }}</span>
            </div>
            @endif
            <div class="total-row grand-total">
                <span>Total:</span>
                <span>₱{{ number_format($sale->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div>Payment Method</div>
            <div class="payment-method">{{ ucfirst($sale->payment_method) }}</div>
            
            @if($sale->credit)
                <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                    <div style="font-weight: bold; color: #856404; margin-bottom: 5px;">
                        📋 CREDIT PAYMENT
                    </div>
                    <div style="font-size: 12px; color: #856404;">
                        <div>Ref: {{ $sale->credit->reference_number ?? 'N/A' }}</div>
                        @if($sale->credit->customer)
                            <div>Customer: {{ $sale->credit->customer->full_name }}</div>
                        @endif
                        <div>Credit Amount: ₱{{ number_format($sale->credit->credit_amount ?? 0, 2) }}</div>
                        <div>Paid: ₱{{ number_format($sale->credit->paid_amount ?? 0, 2) }}</div>
                        <div>Balance: ₱{{ number_format($sale->credit->remaining_balance ?? 0, 2) }}</div>
                        <div>Status: {{ ucfirst($sale->credit->status ?? 'unknown') }}</div>
                        @if($sale->credit->notes)
                            <div style="margin-top: 5px; font-style: italic;">Notes: {{ $sale->credit->notes }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Receipt Footer -->
        <div class="receipt-footer">
            <div class="cashier-info">
                Cashier: {{ $sale->cashier->name ?? 'System' }}
            </div>
            <div class="thank-you">Thank you for your purchase!</div>
            <div>Please come again</div>
            @if($sale->status === 'voided')
                <div style="color: red; font-weight: bold; margin-top: 10px;">
                    *** VOIDED ***
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions">
        <button class="btn btn-primary" onclick="window.print()">
            🖨️ Print Receipt
        </button>
        <a href="{{ route('cashier.sales.index') }}" class="btn btn-secondary">
            ← Back to Sales
        </a>
        @if($sale->status !== 'voided')
            <button class="btn btn-success" onclick="window.location.href='{{ route('cashier.sales.create') }}'">
                ➕ New Sale
            </button>
        @endif
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
