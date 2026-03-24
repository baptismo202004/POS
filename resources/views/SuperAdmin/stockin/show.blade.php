@extends('layouts.app')
@section('title', 'Stock In Details')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush

@section('content')
    <div class="p-3 p-lg-4">
        <main class="flex-fill p-4">
            <div class="container-fluid">
                <div class="card card-rounded shadow-sm animate__animated animate__fadeIn">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="m-0">Stock In Details</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('superadmin.stockin.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            @if($stockMovement->source_type === 'purchases')
                                <a href="{{ route('superadmin.purchases.show', $stockMovement->source_id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-receipt"></i> View Purchase
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Main Stock In Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-light">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Stock In Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Stock Movement ID:</strong></td>
                                                <td>#{{ $stockMovement->id }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Product:</strong></td>
                                                <td>
                                                    <a href="{{ route('superadmin.products.show', $stockMovement->product_id) }}">
                                                        {{ $stockMovement->product_name }}
                                                    </a>
                                                    @if($stockMovement->barcode)
                                                        <br><small class="text-muted">Barcode: {{ $stockMovement->barcode }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Branch:</strong></td>
                                                <td>{{ $stockMovement->branch_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Movement Type:</strong></td>
                                                <td>
                                                    <span class="badge bg-success">{{ ucfirst($stockMovement->movement_type) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Quantity (Base Units):</strong></td>
                                                <td>
                                                    <span class="badge bg-primary fs-6">{{ number_format($stockMovement->quantity_base, 2) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date & Time:</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($stockMovement->created_at)->format('M d, Y h:i A') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card border-light">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Source Information</h6>
                                    </div>
                                    <div class="card-body">
                                        @if($stockMovement->source_type === 'purchases' && $purchaseDetails)
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Source Type:</strong></td>
                                                    <td>Purchase</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Purchase ID:</strong></td>
                                                    <td>#{{ $stockMovement->source_id }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Reference Number:</strong></td>
                                                    <td>{{ $purchaseDetails->reference_number ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Purchase Date:</strong></td>
                                                    <td>{{ $purchaseDetails->purchase_date ? \Carbon\Carbon::parse($purchaseDetails->purchase_date)->format('M d, Y') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Cost:</strong></td>
                                                    <td>{{ number_format($purchaseDetails->total_cost ?? 0, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Payment Status:</strong></td>
                                                    <td>
                                                        <span class="badge bg-{{ $purchaseDetails->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($purchaseDetails->payment_status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        @else
                                            <p class="text-muted">
                                                <strong>Source Type:</strong> {{ ucfirst($stockMovement->source_type ?? 'N/A') }}<br>
                                                <strong>Source ID:</strong> #{{ $stockMovement->source_id ?? 'N/A' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Stock Movements -->
                        @if($relatedMovements && $relatedMovements->count() > 1)
                            <div class="card border-light">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Related Stock Movements (Same Transaction)</h6>
                                    <span class="badge bg-info">{{ $relatedMovements->count() }} items</span>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Movement ID</th>
                                                    <th>Product</th>
                                                    <th>Barcode</th>
                                                    <th>Quantity (Base)</th>
                                                    <th>Date & Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($relatedMovements as $movement)
                                                    <tr class="{{ $movement->id == $stockMovement->id ? 'table-primary' : '' }}">
                                                        <td>
                                                            <a href="{{ route('superadmin.stockin.show', $movement->id) }}">
                                                                #{{ $movement->id }}
                                                            </a>
                                                            @if($movement->id == $stockMovement->id)
                                                                <span class="badge bg-primary ms-1">Current</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('superadmin.products.show', $movement->product_id) }}">
                                                                {{ $movement->product_name }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <code>{{ $movement->barcode ?? 'N/A' }}</code>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ number_format($movement->quantity_base, 2) }}</span>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($movement->created_at)->format('M d, Y h:i A') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
