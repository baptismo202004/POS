@extends('layouts.app')

@include('layouts.theme-base')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:    #0D47A1;
        --blue:    #1976D2;
        --blue-lt: #42A5F5;
        --cyan:    #00E5FF;
        --green:   #10b981;
        --red:     #ef4444;
        --bg:      #EBF3FB;
        --card:    #ffffff;
        --border:  rgba(25,118,210,0.12);
        --text:    #1a2744;
        --muted:   #6b84aa;
    }

    .sp-page { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text); }

    /* Background */
    .sp-bg { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; background: var(--bg); }
    .sp-bg::before {
        content:''; position:absolute; inset:0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%, transparent 55%);
    }
    .sp-blob { position:absolute; border-radius:50%; filter:blur(60px); opacity:.11; }
    .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
    .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    /* Wrap */
    .sp-wrap { position:relative; z-index:1; padding:28px 24px 56px; }

    /* Page header */
    .sp-page-head {
        display:flex; align-items:center; justify-content:space-between;
        margin-bottom:22px; flex-wrap:wrap; gap:14px;
        animation:spUp .4s ease both;
    }
    .sp-ph-left  { display:flex; align-items:center; gap:13px; }
    .sp-ph-icon  {
        width:48px; height:48px; border-radius:14px;
        background:linear-gradient(135deg,var(--navy),var(--blue-lt));
        display:flex; align-items:center; justify-content:center;
        font-size:20px; color:#fff;
        box-shadow:0 6px 20px rgba(13,71,161,0.28);
    }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
    .sp-ph-actions { display:flex; align-items:center; gap:9px; flex-wrap:wrap; }

    /* Buttons */
    .sp-btn {
        display:inline-flex; align-items:center; gap:7px;
        padding:9px 18px; border-radius:11px;
        font-size:13px; font-weight:700; cursor:pointer;
        font-family:'Nunito',sans-serif;
        border:none; transition:all .2s ease; text-decoration:none; white-space:nowrap;
    }
    .sp-btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue)); color:#fff; box-shadow:0 4px 14px rgba(13,71,161,0.26); }
    .sp-btn-primary:hover { transform:translateY(-2px); box-shadow:0 7px 20px rgba(13,71,161,0.36); color:#fff; }
    .sp-btn-success { background:linear-gradient(135deg,#059669,#10b981); color:#fff; box-shadow:0 4px 14px rgba(16,185,129,0.28); }
    .sp-btn-success:hover { transform:translateY(-2px); box-shadow:0 7px 20px rgba(16,185,129,0.38); color:#fff; }
    .sp-btn-outline { background:var(--card); color:var(--navy); border:1.5px solid var(--border); }
    .sp-btn-outline:hover { background:var(--navy); color:#fff; border-color:var(--navy); transform:translateX(-3px); }

    /* Card */
    .sp-card {
        background:var(--card); border-radius:20px;
        border:1px solid var(--border);
        box-shadow:0 4px 28px rgba(13,71,161,0.09);
        overflow:hidden; animation:spUp .45s ease both;
    }

    /* Card gradient header */
    .sp-card-head {
        padding:16px 26px;
        background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
        display:flex; align-items:center; justify-content:space-between;
        position:relative; overflow:hidden;
    }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:15px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-card-head-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 12px;border-radius:20px;font-family:'Nunito',sans-serif; }

    /* Card body */
    .sp-card-body { padding:28px 26px; }

    /* Image panel */
    .sp-img-panel {
        border-radius:16px; overflow:hidden;
        border:1px solid var(--border);
        background:linear-gradient(145deg,#f0f6ff,#e8f0fd);
        aspect-ratio:1/1;
        display:flex; align-items:center; justify-content:center;
        position:relative;
        box-shadow:0 4px 20px rgba(13,71,161,0.08);
    }
    .sp-img-panel img { width:100%;height:100%;object-fit:cover;display:block; }
    .sp-img-empty { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:var(--muted);padding:30px;text-align:center; }
    .sp-img-empty i { font-size:52px;opacity:.22;color:var(--navy); }
    .sp-img-empty span { font-size:13px;font-weight:600; }
    .sp-img-overlay { position:absolute;bottom:12px;right:12px;z-index:2; }
    .sp-img-overlay-btn {
        display:inline-flex;align-items:center;gap:6px;
        padding:7px 14px;border-radius:9px;border:none;cursor:pointer;
        font-size:12px;font-weight:700;font-family:'Nunito',sans-serif;
        background:rgba(13,71,161,0.82);color:#fff;
        backdrop-filter:blur(8px);
        box-shadow:0 3px 12px rgba(13,71,161,0.35);
        transition:all .2s ease;
    }
    .sp-img-overlay-btn:hover { background:var(--navy);transform:translateY(-1px); }

    /* Product name & barcode */
    .sp-prod-name { font-family:'Nunito',sans-serif;font-size:26px;font-weight:900;color:var(--navy);line-height:1.2;margin-bottom:6px; }
    .sp-prod-barcode {
        display:inline-flex;align-items:center;gap:6px;
        font-size:12.5px;color:var(--muted);font-weight:600;
        background:rgba(13,71,161,0.06);border:1px solid var(--border);
        padding:4px 12px;border-radius:20px;margin-bottom:22px;
        font-family:'Nunito',sans-serif;
    }

    /* Details table */
    .sp-details-table { width:100%;border-collapse:collapse; }
    .sp-details-table tr { border-bottom:1px solid rgba(25,118,210,0.07); }
    .sp-details-table tr:last-child { border-bottom:none; }
    .sp-details-table th {
        padding:12px 0; width:140px;
        font-size:11px;font-weight:700;letter-spacing:.07em;
        text-transform:uppercase;color:var(--muted);
        font-family:'Nunito',sans-serif;vertical-align:middle;
    }
    .sp-details-table td {
        padding:12px 0;
        font-size:13.5px;color:var(--text);font-weight:500;
        vertical-align:middle;
    }

    /* Badges */
    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }
    .sp-badge-red   { background:rgba(239,68,68,0.10);color:#b91c1c; }
    .sp-badge-blue  { background:rgba(13,71,161,0.10);color:var(--navy); }

    @keyframes spUp { from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)} }
</style>
@endpush

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
                                    $conversionSummaryParts[] = '1 ' . $ut->name . ' × ' . $factorText . ' ' . $baseUnit->name;
                                }
                            }
                            $conversionSummary = implode(', ', $conversionSummaryParts);
                        @endphp

                        <table class="table table-sm mb-3">
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
                                <td>{{ $product->display_product_type }}</td>
                            </tr>
                            <tr>
                                <th>Unit Types</th>
                                <td>
                                    @foreach($product->unitTypes as $unitType)
                                        <span class="sp-badge sp-badge-blue me-1">{{ $unitType->name }}</span>
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
                                <td>
                                    @if($product->status == 'active')
                                        <span class="sp-badge sp-badge-green">Active</span>
                                    @else
                                        <span class="sp-badge sp-badge-red">Inactive</span>
                                    @endif
                                </td>
                            </tr>

                            @php
                                $warrantyLabel = 'None';

                                if (!empty($product->warranty_type) && $product->warranty_type !== 'none') {
                                    $warrantyLabel = ucfirst(str_replace('_', ' ', $product->warranty_type));

                                    if (!is_null($product->warranty_coverage_months)) {
                                        $warrantyLabel .= ' • ' . (int) $product->warranty_coverage_months . ' month' . ((int) $product->warranty_coverage_months !== 1 ? 's' : '');
                                    }
                                }
                            @endphp

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
                                                        1 {{ $unitType->name }} × {{ $factorText }} {{ $baseName }}
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
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async (response) => {
                    const raw = await response.text();
                    let data;
                    try {
                        data = raw ? JSON.parse(raw) : {};
                    } catch (e) {
                        data = null;
                    }

                    if (!response.ok) {
                        const msg = (data && (data.message || data.error))
                            ? (data.message || data.error)
                            : `Upload failed (HTTP ${response.status}).`;
                        throw new Error(msg);
                    }

                    if (data && data.success) return data;
                    const msg = (data && (data.message || data.error))
                        ? (data.message || data.error)
                        : 'Upload failed';
                    throw new Error(msg);
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
