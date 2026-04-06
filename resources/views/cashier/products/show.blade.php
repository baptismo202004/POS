@extends('layouts.app')
@section('title', 'Product Details')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:    #0D47A1;
        --blue:    #1976D2;
        --blue-lt: #42A5F5;
        --green:   #10b981;
        --red:     #ef4444;
        --bg:      #EBF3FB;
        --card:    #ffffff;
        --border:  rgba(25,118,210,0.12);
        --text:    #1a2744;
        --muted:   #6b84aa;
    }

    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }
    .sp-badge-red   { background:rgba(239,68,68,0.10);color:#b91c1c; }
    .sp-badge-blue  { background:rgba(13,71,161,0.10);color:var(--navy); }
</style>
@endpush

@section('content')
<div class="container-fluid" style="padding: 30px;">
    <div class="p-4 card-rounded shadow-sm bg-white" style="width: 100%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Product Details</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('cashier.products.lifecycle', $product) }}" class="btn btn-primary">
                    <i class="fas fa-history me-1"></i> Product Lifecycle
                </a>
                <a href="{{ route('cashier.products.index') }}" class="btn btn-outline-secondary">Back to Products</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product_name }}" class="img-fluid rounded">
                @else
                    <div class="img-fluid rounded bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif

                <div class="mt-3">
                    <button class="btn btn-success w-100" onclick="addImage()">
                        <i class="fas fa-plus-circle me-1"></i> Add Image
                    </button>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <h3 class="mb-0">{{ $product->product_name }}</h3>
                        <p class="text-muted mb-3">{{ $product->barcode }}</p>

                        @php
                            $baseUnit = $product->unitTypes->firstWhere('pivot.is_base', true);
                            $baseName = $baseUnit ? $baseUnit->name : null;

                            $warrantyLabel = 'None';
                            if (!empty($product->warranty_type) && $product->warranty_type !== 'none') {
                                $warrantyLabel = ucfirst(str_replace('_', ' ', $product->warranty_type));
                                if (!is_null($product->warranty_coverage_months)) {
                                    $warrantyLabel .= ' • ' . (int) $product->warranty_coverage_months . ' month' . ((int) $product->warranty_coverage_months !== 1 ? 's' : '');
                                }
                            }
                        @endphp

                        <table class="table table-sm mb-3">
                            <tr>
                                <th>Brand</th>
                                <td>{{ $product->brand->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Product Type</th>
                                <td>{{ $product->display_product_type ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Unit Types</th>
                                <td>
                                    @forelse($product->unitTypes as $unitType)
                                        <span class="sp-badge sp-badge-blue me-1">{{ $unitType->name }}</span>
                                    @empty
                                        <span class="text-muted">N/A</span>
                                    @endforelse
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($product->status === 'active')
                                        <span class="sp-badge sp-badge-green">Active</span>
                                    @else
                                        <span class="sp-badge sp-badge-red">{{ ucfirst($product->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Warranty</th>
                                <td>{{ $warrantyLabel }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Unit Conversions</h5>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="unitConversionsToggle">Show</button>
                        </div>

                        <div id="unitConversionsCollapse" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Unit</th>
                                            <th>Conversion Factor</th>
                                            <th>Display</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($product->unitTypes as $unitType)
                                            @php
                                                $isBase = isset($unitType->pivot) && !empty($unitType->pivot->is_base);
                                                $factor = isset($unitType->pivot->conversion_factor) ? (float) $unitType->pivot->conversion_factor : 1.0;
                                                $factorText = rtrim(rtrim(number_format($factor, 6, '.', ''), '0'), '.');
                                                $unitTypeCount = $product->unitTypes->count();
                                            @endphp
                                            <tr>
                                                <td>{{ $unitType->name }}</td>
                                                <td>{{ $factorText }}</td>
                                                <td>
                                                    @if($isBase || !$baseName)
                                                        {{ $unitType->name }}{{ ($isBase && $unitTypeCount > 1) ? ' (base)' : '' }}
                                                    @else
                                                        1 {{ $unitType->name }} × {{ $factorText }} {{ $baseName }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No unit conversions configured.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const toggleBtn = document.getElementById('unitConversionsToggle');
        const panel = document.getElementById('unitConversionsCollapse');

        toggleBtn.addEventListener('click', function() {
            const isOpen = panel.style.display !== 'none';
            panel.style.display = isOpen ? 'none' : '';
            toggleBtn.textContent = isOpen ? 'Show' : 'Hide';
        });
    })();

    function addImage() {
        Swal.fire({
            title: 'Upload Product Image',
            html: `
                <div class="mb-3">
                    <input type="file" class="form-control" id="imageFile" name="image" accept="image/*" required>
                </div>
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

                Swal.showLoading();

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('image', file);

                return fetch('{{ route("cashier.products.updateImage", $product->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || data.error || 'Upload failed');
                    }
                    return data;
                })
                .catch(error => {
                    Swal.showValidationMessage(error.message);
                    return false;
                });
            }
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Swal.fire({ icon: 'success', title: 'Success!', text: 'Image uploaded successfully', timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
            }
        });
    }
</script>
@endpush
