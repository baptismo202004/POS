<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $sale->reference_number ?? $sale->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .container { width: 100%; }
        .header { text-align: center; margin-bottom: 16px; }
        .header img { max-width: 90px; margin-bottom: 8px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 2px 0; font-size: 11px; }
        .row { width: 100%; margin-bottom: 10px; }
        .col { display: inline-block; vertical-align: top; width: 49%; }
        .box { border: 1px solid #ddd; padding: 10px; border-radius: 4px; }
        .title { font-weight: bold; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border-bottom: 1px solid #eee; padding: 6px 4px; }
        th { text-align: left; background: #f5f5f5; }
        .right { text-align: right; }
        .totals { margin-top: 10px; }
        .totals .line { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals .grand { font-size: 16px; font-weight: bold; }
        .muted { color: #666; }
        .signature { margin-top: 30px; }
        .signature .line { border-top: 1px solid #111; width: 240px; padding-top: 6px; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        @php
            $logoPath = public_path('images/BGH LOGO.png');
            $logoSrc = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : '';
        @endphp
        @if($logoSrc)
            <img src="{{ $logoSrc }}" alt="BGH Logo">
        @endif
        <h1>BGH IT Solutions</h1>
        <p>Purok A-1, Balirong City of Naga Cebu</p>
        <p>bghsupport@bghitsolutions.com</p>
        <p>09973849783</p>
        <p style="margin-top: 10px;"><strong>RECEIPT</strong></p>
        <p class="muted">{{ $sale->reference_number ?: 'PR-' . $sale->created_at->format('Ymd') . $sale->id }}</p>
    </div>

    <div class="row">
        <div class="col">
            <div class="box">
                <div class="title">Order Information</div>
                <div><strong>Date:</strong> {{ $sale->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</div>
                <div><strong>Order Status:</strong> {{ ucfirst($sale->status) }}</div>
                <div><strong>Payment Type:</strong> {{ ucfirst($sale->payment_method) }}</div>
                <div><strong>Payment Status:</strong> {{ $sale->payment_method === 'cash' ? 'Paid' : 'Unpaid' }}</div>
                <div><strong>Branch:</strong> {{ $sale->branch->branch_name ?? 'N/A' }}</div>
            </div>
        </div>
        <div class="col">
            <div class="box">
                <div class="title">Customer Details</div>
                <div><strong>Name:</strong> {{ $sale->customer->full_name ?? 'Walk-in' }}</div>
                <div><strong>Company/School:</strong> {{ $sale->customer->company_school_name ?? '-' }}</div>
                <div><strong>Phone:</strong> {{ $sale->customer->phone ?? '-' }}</div>
                <div><strong>Email:</strong> {{ $sale->customer->email ?? '-' }}</div>
                <div><strong>Facebook:</strong> {{ $sale->customer->facebook ?? '-' }}</div>
                <div><strong>Address:</strong> {{ $sale->customer->address ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="title">Order Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Description</th>
                    <th style="width: 12%;" class="right">Qty</th>
                    <th style="width: 18%;" class="right">Unit Price</th>
                    <th style="width: 18%;" class="right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $index => $item)
                    @php
                        $serial = $serialsBySaleItemId[$item->id] ?? null;
                        $serialNumber = $serial->serial_number ?? null;

                        $warrantyMonths = (int) ($item->warranty_months ?? 0);
                        $warrantyStart = $serial && $serial->sold_at ? \Carbon\Carbon::parse($serial->sold_at) : null;
                        $warrantyExpiry = $serial && $serial->warranty_expiry_date ? \Carbon\Carbon::parse($serial->warranty_expiry_date) : null;

                        $warrantyStatus = 'N/A';
                        if ($warrantyMonths <= 0) {
                            $warrantyStatus = 'No Warranty';
                        } elseif (!$warrantyStart) {
                            $warrantyStatus = 'Not Started';
                        } elseif ($warrantyExpiry && $warrantyExpiry->endOfDay()->isPast()) {
                            $warrantyStatus = 'Expired';
                        } else {
                            $warrantyStatus = 'Active';
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ $item->product->product_name ?? 'N/A' }}
                            @if(optional($item->unitType)->unit_name)
                                <div class="muted">Unit: {{ $item->unitType->unit_name }}</div>
                            @endif

                            @if($serialNumber)
                                <div class="muted">Serial: <span class="nowrap">{{ $serialNumber }}</span></div>
                            @endif

                            @if($warrantyMonths > 0)
                                <div class="muted">
                                    Warranty: {{ $warrantyMonths }} month(s)
                                    @if($warrantyStart)
                                        | Start: {{ $warrantyStart->format('n/j/Y') }}
                                    @endif
                                    @if($warrantyExpiry)
                                        | Expiry: {{ $warrantyExpiry->format('n/j/Y') }}
                                    @endif
                                    | Status: {{ $warrantyStatus }}
                                </div>
                            @endif
                        </td>
                        <td class="right">{{ rtrim(rtrim(number_format((float) ($item->quantity ?? 0), 6, '.', ''), '0'), '.') }}</td>
                        <td class="right">&#8369;{{ number_format((float) ($item->unit_price ?? 0), 2) }}</td>
                        <td class="right">&#8369;{{ number_format((float) ($item->subtotal ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="line grand">
                <div>TOTAL AMOUNT</div>
                <div>&#8369;{{ number_format((float) ($sale->total_amount ?? 0), 2) }}</div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 16px;">
        <div class="col">
            <div class="box">
                <div class="title">Cashier</div>
                <div><strong>Name:</strong> {{ $sale->cashier->name ?? 'N/A' }}</div>
                <div class="signature">
                    <div class="line">Signature</div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="box">
                <div class="title">Notes</div>
                <div class="muted">This document is system generated.</div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
