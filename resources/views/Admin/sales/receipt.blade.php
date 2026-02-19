<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - {{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            background: #fff;
        }
        
        /* Paper Size Options - Uncomment the one you want to use */
        
        /* Small Receipt: 9x13cm (3.54x5.12 inches) */
        .receipt-container {
            width: 9cm;
            min-height: 13cm;
            margin: 20px auto;
            padding: 15px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* 58mm Thermal Printer (219px) */
        /*
        .receipt-container {
            width: 219px;
            margin: 20px auto;
            padding: 8px;
            border: 1px solid #ccc;
        }
        */
        
        /* 72mm Thermal Printer (272px) */
        /*
        .receipt-container {
            width: 272px;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ccc;
        }
        */
        
        /* 80mm Thermal Printer (302px) - Original */
        /*
        .receipt-container {
            width: 302px;
            margin: 20px auto;
            padding: 10px;
            border: 1px solid #ccc;
        }
        */
        
        /* A4 Paper */
        /*
        .receipt-container {
            width: 210mm;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            max-width: 794px;
        }
        */
        
        /* Letter Paper */
        /*
        .receipt-container {
            width: 8.5in;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            max-width: 816px;
        }
        */
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .details, .items, .total, .footer {
            margin-bottom: 15px;
        }
        .details p {
            margin: 3px 0;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            padding: 5px 0;
            font-size: 11px;
        }
        .items thead {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
        }
        .items .amount {
            text-align: right;
        }
        .total {
            border-top: 1px dashed #000;
            padding-top: 5px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }
        .barcode-container {
            text-align: center;
            margin-top: 20px;
        }
        .qr-placeholder {
            width: 100px;
            height: 100px;
            border: 1px solid #000;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        @media print {
            @page {
                margin: 0;
                size: 9cm 13cm; /* Small Receipt: 9x13cm */
            }
            body {
                margin: 0;
                padding: 0;
            }
            .receipt-container {
                margin: 0;
                padding: 15px;
                border: none;
                box-shadow: none;
                width: 9cm;
                min-height: 13cm;
            }
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <img src="{{ asset('/images/BGH LOGO.png') }}" alt="BGH IT Solutions Logo" style="max-width: 60px; margin-bottom: 8px;">
            <h1>BGH IT Solutions</h1>
            <p>Purok A-1, Balirong City of Naga Cebu</p>
            <p>bghsupport@bghitsolutions.com</p>
            <p>09973849783</p>
            <br>
            <h2>PAYMENT RECEIPT</h2>
            <p>{{ $sale->reference_number ?: 'PR-' . $sale->created_at->format('Ymd') . $sale->id }}</p>
            <p>{{ $sale->created_at->format('Y-M-d') }}</p>
        </div>

        <div class="details">
            <p>Cashier: {{ $sale->cashier->name ?? 'N/A' }}</p>
            <p>Date: {{ $sale->created_at->format('Y-M-d') }}</p>
            <p>Reference: {{ $sale->reference_number ?: 'PR-' . $sale->created_at->format('Ymd') . $sale->id }}</p>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th class="amount">Price</th>
                        <th class="amount">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->saleItems as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ Str::limit($item->product->product_name, 20) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="amount">P{{ number_format($item->unit_price, 2) }}</td>
                        <td class="amount">P{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="total">
            <span>TOTAL</span>
            <span>PHP {{ number_format($sale->total_amount, 2) }}</span>
        </div>

        <div class="footer">
            <p style="text-align:center;">Thank you for your shopping!</p>
        </div>
    </div>
</body>
</html>
