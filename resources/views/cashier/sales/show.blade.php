@extends('layouts.app')
@section('title', 'Sale Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Sale Details</h2>
                <a href="{{ route('cashier.sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Sales
                </a>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Sale Details</h5>
                        <div class="text-muted small">Receipt #{{ $sale->reference_number ?? $sale->id }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-{{ $sale->status === 'pending' ? 'warning' : ($sale->status === 'voided' ? 'danger' : 'success') }}">
                            {{ ucfirst($sale->status) }}
                        </span>
                        <span class="badge bg-{{ $sale->payment_method == 'cash' ? 'success' : 'info' }}">
                            {{ ucfirst($sale->payment_method) }}
                        </span>
                        <a href="{{ route('cashier.pos.receipt.pdf', $sale) }}" target="_blank" class="btn btn-primary btn-sm">
                            Print Receipt
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Sale Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Sale ID:</strong></td><td class="text-end">#{{ $sale->id }}</td></tr>
                                <tr><td><strong>Date:</strong></td><td class="text-end">{{ \Carbon\Carbon::parse($sale->created_at)->format('n/j/Y') }}</td></tr>
                                <tr><td><strong>Status:</strong></td><td class="text-end">{{ ucfirst($sale->status) }}</td></tr>
                                <tr><td><strong>Branch:</strong></td><td class="text-end">{{ $sale->branch->branch_name ?? 'N/A' }}</td></tr>
                                <tr><td><strong>Cashier:</strong></td><td class="text-end">{{ $sale->cashier->name ?? 'N/A' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Payment Method:</strong></td><td class="text-end">{{ ucfirst($sale->payment_method) }}</td></tr>
                                <tr><td><strong>Total Amount:</strong></td><td class="text-end">₱{{ number_format($sale->total_amount, 2) }}</td></tr>
                                <tr><td><strong>Payment Status:</strong></td><td class="text-end">{{ $sale->payment_method === 'cash' ? 'Paid' : 'Unpaid' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Customer Details</h6>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Name:</strong> {{ $sale->customer->full_name ?? 'Walk-in' }}</p>
                                            <p class="mb-1"><strong>Company/School:</strong> {{ $sale->customer->company_school_name ?? '-' }}</p>
                                            <p class="mb-1"><strong>Phone:</strong> {{ $sale->customer->phone ?? '-' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Email:</strong> {{ $sale->customer->email ?? '-' }}</p>
                                            <p class="mb-1"><strong>Facebook:</strong> {{ $sale->customer->facebook ?? '-' }}</p>
                                            <p class="mb-1"><strong>Address:</strong> {{ $sale->customer->address ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-muted">Notes</h6>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0">{{ $sale->notes ?: 'No notes.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($sale->status === 'pending')
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-muted">Mark as Completed</h6>
                                <div class="alert alert-warning">
                                    This order is pending. Please enter serial numbers for each item, then mark it as completed.
                                </div>
                                <form method="POST" action="{{ route('cashier.sales.mark-completed', $sale) }}">
                                    @csrf
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th style="width: 260px;">Serial Number</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sale->saleItems as $item)
                                                    @php
                                                        $reservedSerial = $serialsBySaleItemId[$item->id] ?? null;
                                                        $reservedSerialNumber = $reservedSerial->serial_number ?? '';
                                                        $requiresSerial = (($item->product->tracking_type ?? 'none') !== 'none');
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                                        <td>
                                                            <input type="text" class="form-control" name="serials[{{ $item->id }}]" value="{{ old('serials.' . $item->id, $reservedSerialNumber) }}" placeholder="Enter serial" {{ $requiresSerial ? 'required' : '' }} {{ $reservedSerialNumber ? 'readonly' : '' }}>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        Mark as Completed
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <h6 class="mt-3">Items Sold</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Warranty</th>
                                    <th class="text-end">Warranty Start</th>
                                    <th class="text-end">Warranty Expiry</th>
                                    <th class="text-end">Warranty Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->saleItems as $item)
                                    @php
                                        $serial = $serialsBySaleItemId[$item->id] ?? null;
                                        $warrantyMonths = (int) ($item->warranty_months ?? 0);
                                        $warrantyStart = $serial && $serial->sold_at ? \Carbon\Carbon::parse($serial->sold_at) : null;
                                        $warrantyExpiry = $serial && $serial->warranty_expiry_date ? \Carbon\Carbon::parse($serial->warranty_expiry_date) : null;

                                        if ($warrantyExpiry) {
                                            $warrantyStatus = $warrantyExpiry->endOfDay()->isFuture() ? 'Active' : 'Expired';
                                        } elseif ($warrantyMonths > 0) {
                                            $warrantyStatus = $warrantyStart ? 'Active' : 'Not Started';
                                        } else {
                                            $warrantyStatus = 'No Warranty';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ rtrim(rtrim(number_format((float) ($item->quantity ?? 0), 6, '.', ''), '0'), '.') }}</td>
                                        <td class="text-end">₱{{ number_format((float) ($item->unit_price ?? 0), 2) }}</td>
                                        <td class="text-end">₱{{ number_format((float) ($item->subtotal ?? 0), 2) }}</td>
                                        <td class="text-end">{{ $warrantyMonths > 0 ? ($warrantyMonths . ' month(s)') : ($warrantyExpiry ? '-' : '-') }}</td>
                                        <td class="text-end">{{ $warrantyStart ? $warrantyStart->format('n/j/Y') : '-' }}</td>
                                        <td class="text-end">{{ $warrantyExpiry ? $warrantyExpiry->format('n/j/Y') : '-' }}</td>
                                        <td class="text-end">
                                            @if($warrantyStatus === 'Active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($warrantyStatus === 'Expired')
                                                <span class="badge bg-danger">Expired</span>
                                            @else
                                                <span class="text-muted">{{ $warrantyStatus }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
