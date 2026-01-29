@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 60px); padding: 30px;">
    <div class="p-4 card-rounded shadow-sm bg-white" style="width: 100%; max-width: 950px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Product Details</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-success" onclick="addImage()">
                    <i class="bi bi-plus-circle"></i> Add Image
                </button>
                <a href="{{ route('superadmin.stockin.index') }}" class="btn btn-primary">Back to stock in</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="position-relative">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}" class="img-fluid rounded">
                    @else
                        <div class="img-fluid rounded bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <span>No Image</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $product->product_name }}</h3>
                        <p class="text-muted mb-3">{{ $product->barcode }}</p>
                        <table class="table table-sm mb-3">
                            <tr>
                                <th style="width: 120px;">Brand</th>
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
                                        <span class="badge bg-secondary me-1">{{ $unitType->name }}</span>
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
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<script>
    function addImage() {
        Swal.fire({
            title: 'Add Product Image',
            html: `
                <form id="imageUploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="mb-3">
                        <label for="imageFile" class="form-label">Choose Image</label>
                        <input type="file" class="form-control" id="imageFile" name="image" accept="image/*" required>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Upload',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2563eb',
            preConfirm: () => {
                const fileInput = document.getElementById('imageFile');
                const file = fileInput.files[0];
                
                if (!file) {
                    Swal.showValidationMessage('Please select an image');
                    return false;
                }
                
                // Show loading state
                Swal.showLoading();
                Swal.update({
                    title: 'Uploading...',
                    html: 'Please wait while we upload your image.',
                    showConfirmButton: false,
                    showCancelButton: false
                });
                
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('image', file);
                
                return fetch('{{ route("superadmin.products.updateImage", $product->id) }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        return data;
                    } else {
                        throw new Error(data.message || 'Upload failed');
                    }
                })
                .catch(error => {
                    Swal.showValidationMessage(error.message);
                    return false;
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Image uploaded successfully',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
</script>
@endsection
