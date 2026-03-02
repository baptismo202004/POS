@extends('layouts.app')

@section('title', 'Below Cost Sales')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Items Sold Below Purchase Price</h1>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ url('/superadmin/sales/below-cost') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date', optional($startDate)->toDateString()) }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date', optional($endDate)->toDateString()) }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ url('/superadmin/sales/below-cost') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference #</th>
                        <th>Branch</th>
                        <th>Cashier</th>
                        <th>Product</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Purchase Price</th>
                        <th class="text-end">Sold Price</th>
                        <th class="text-end">Purchase Total</th>
                        <th class="text-end">Sold Total</th>
                        <th class="text-end">Loss</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        @php
                            $loss = ($item->purchase_total ?? 0) - ($item->sold_total ?? 0);
                        @endphp
                        <tr>
                            <td>{{ optional($item->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @if ($item->sale_id)
                                    <a href="{{ route('superadmin.sales.show', $item->sale_id) }}">#{{ $item->sale_id }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $item->branch_name ?? 'N/A' }}</td>
                            <td>{{ $item->cashier_name ?? 'N/A' }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">₱{{ number_format($item->purchase_price ?? 0, 2) }}</td>
                            <td class="text-end">₱{{ number_format($item->sold_unit_price ?? 0, 2) }}</td>
                            <td class="text-end">₱{{ number_format($item->purchase_total ?? 0, 2) }}</td>
                            <td class="text-end">₱{{ number_format($item->sold_total ?? 0, 2) }}</td>
                            <td class="text-end text-danger">₱{{ number_format($loss, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">No items were sold below purchase price for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $items->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
