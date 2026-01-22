@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="p-4 card-rounded shadow-sm bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Product Details</h2>
            <a href="{{ route('superadmin.products.index') }}" class="btn btn-primary">Back to Products</a>
        </div>

        <div class="row">
            <div class="col-md-4">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}" class="img-fluid rounded">
                @else
                    <div class="img-fluid rounded bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <span>No Image</span>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <h3>{{ $product->product_name }}</h3>
                <p class="text-muted">{{ $product->barcode }}</p>
                <table class="table table-bordered">
                    <tr>
                        <th>Brand</th>
                        <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Product Type</th>
                        <td>{{ $product->productType->type_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Unit Types</th>
                        <td>
                            @foreach($product->unitTypes as $unitType)
                                <span class="badge bg-secondary">{{ $unitType->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td><span class="badge bg-{{ $product->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($product->status) }}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
