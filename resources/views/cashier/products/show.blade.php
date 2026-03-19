@extends('layouts.app')
@section('title', 'Product Details')

@section('content')
<div class="p-3 p-lg-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="p-4 shadow-sm bg-white" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="m-0">Product Details</h2>
                        <a href="{{ route('cashier.products.index') }}" class="btn btn-outline-primary">Back</a>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold">Name</div>
                            <div>{{ $product->product_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold">Barcode</div>
                            <div>{{ $product->barcode ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold">Model Number</div>
                            <div>{{ $product->model_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold">Status</div>
                            <div>{{ $product->status ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
