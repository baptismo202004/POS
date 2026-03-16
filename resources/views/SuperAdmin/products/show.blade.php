@extends('layouts.app')

@include('layouts.theme-base')

@section('content')
<div class="container-fluid" style="padding: 30px;">
    <div class="p-4 card-rounded shadow-sm bg-white" style="width: 100%;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="m-0">Product Details</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('superadmin.products.index') }}" class="btn btn-primary">Back to Products</a>
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

                <div class="mt-3">
                    <button class="btn btn-success w-100" onclick="addImage()">
                        <i class="bi bi-plus-circle"></i> Add Image
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
                            $unitTypeCount = $product->unitTypes->count();

                            $conversionSummaryParts = [];
                            if ($baseUnit) {
                                foreach ($product->unitTypes as $ut) {
                                    $isBase = isset($ut->pivot) && !empty($ut->pivot->is_base);
                                    if ($isBase) {
                                        continue;
                                    }

                                    $factor = isset($ut->pivot) && isset($ut->pivot->conversion_factor) ? (float) $ut->pivot->conversion_factor : null;
                                    if (!$factor || $factor <= 0) {
                                        continue;
                                    }

                                    $factorText = rtrim(rtrim(number_format($factor, 6, '.', ''), '0'), '.');
                                    $conversionSummaryParts[] = '1 ' . $baseUnit->name . ' × ' . $factorText . ' ' . $ut->name;
                                }
                            }
                            $conversionSummary = implode(', ', $conversionSummaryParts);
                        @endphp

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
                            @if(!empty($conversionSummary))
                                <tr>
                                    <th>Conversion</th>
                                    <td class="text-muted">{{ $conversionSummary }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>Status</th>
                                <td><span class="badge bg-{{ $product->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($product->status) }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">Unit Conversions</h5>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="unitConversionsToggle" aria-expanded="false" aria-controls="unitConversionsCollapse">
                                Show
                            </button>
                        </div>

                        <div id="unitConversionsCollapse" style="display:none;">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Unit</th>
                                            <th>Conversion Factor</th>
                                            <th>Display</th>
                                            <th style="width: 140px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($product->unitTypes as $unitType)
                                            @php
                                                $isBase = isset($unitType->pivot) && !empty($unitType->pivot->is_base);
                                                $factor = isset($unitType->pivot) && isset($unitType->pivot->conversion_factor) ? (float) $unitType->pivot->conversion_factor : 1.0;
                                                $factorText = rtrim(rtrim(number_format($factor, 6, '.', ''), '0'), '.');

                                                $updateAction = \Illuminate\Support\Facades\Route::has('superadmin.products.unit-conversions.update')
                                                    ? route('superadmin.products.unit-conversions.update', [$product->id, $unitType->id])
                                                    : url("/superadmin/products/{$product->id}/unit-conversions/{$unitType->id}");
                                                $destroyAction = \Illuminate\Support\Facades\Route::has('superadmin.products.unit-conversions.destroy')
                                                    ? route('superadmin.products.unit-conversions.destroy', [$product->id, $unitType->id])
                                                    : url("/superadmin/products/{$product->id}/unit-conversions/{$unitType->id}");
                                            @endphp
                                            <tr>
                                                <td>{{ $unitType->name }}</td>
                                                <td>{{ $factorText }}</td>
                                                <td>
                                                    @if($isBase || !$baseName)
                                                        {{ $unitType->name }}{{ ($isBase && $unitTypeCount > 1) ? ' (base)' : '' }}
                                                    @else
                                                        1 {{ $baseName }} × {{ $factorText }} {{ $unitType->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                                        <form method="POST" action="{{ $updateAction }}" class="d-flex gap-1 align-items-center">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="number" step="0.000001" min="0.000001" name="conversion_factor" class="form-control form-control-sm" style="width: 110px;" value="{{ $isBase ? 1 : $factorText }}" {{ $isBase ? 'readonly' : '' }}>
                                                            <input type="hidden" name="is_base" value="0">
                                                            <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                        </form>

                                                        <form method="POST" action="{{ $updateAction }}" class="d-inline-block">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="conversion_factor" value="{{ $factorText ?: 1 }}">
                                                            <input type="hidden" name="is_base" value="1">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary">Set Base</button>
                                                        </form>

                                                        <form method="POST" action="{{ $destroyAction }}" class="d-inline-block" onsubmit="return confirm('Delete this unit conversion?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" {{ $isBase ? 'disabled' : '' }}>Delete</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">No unit conversions configured.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <hr>

                            @php
                                $storeAction = \Illuminate\Support\Facades\Route::has('superadmin.products.unit-conversions.store')
                                    ? route('superadmin.products.unit-conversions.store', $product->id)
                                    : url("/superadmin/products/{$product->id}/unit-conversions");
                            @endphp

                            <form method="POST" action="{{ $storeAction }}" class="row g-2 align-items-end">
                                @csrf
                                <div class="col-md-4">
                                    <label class="form-label">Unit Type</label>
                                    <select name="unit_type_id" class="form-control" required>
                                        @foreach(\App\Models\UnitType::all() as $ut)
                                            <option value="{{ $ut->id }}">{{ $ut->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Based On</label>
                                    <select name="reference_unit_type_id" class="form-control" required>
                                        @foreach($product->unitTypes as $ref)
                                            <option value="{{ $ref->id }}" {{ (isset($ref->pivot) && !empty($ref->pivot->is_base)) ? 'selected' : '' }}>{{ $ref->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Factor</label>
                                    <input type="number" step="0.000001" min="0.000001" name="conversion_factor" class="form-control" value="1" required>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input" type="checkbox" value="1" id="isBase" name="is_base">
                                        <label class="form-check-label" for="isBase">Set as base</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mt-2">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<script>
    (function() {
        const toggleBtn = document.getElementById('unitConversionsToggle');
        const panel = document.getElementById('unitConversionsCollapse');

        if (!toggleBtn || !panel) {
            return;
        }

        function update(isOpen) {
            panel.style.display = isOpen ? '' : 'none';
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggleBtn.textContent = isOpen ? 'Hide' : 'Show';
        }

        update(false);

        toggleBtn.addEventListener('click', function() {
            const isOpen = toggleBtn.getAttribute('aria-expanded') === 'true';
            update(!isOpen);
        });
    })();

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
                Swal.fire({
                    title: '<div style="display: flex; align-items: center; gap: 10px;"><div class="spinner-border text-primary" role="status" style="width: 1.5rem; height: 1.5rem;"><span class="visually-hidden">Loading...</span></div> Uploading...</div>',
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
