@php
    // Expected variables from controller:
    // $brands, $categories, $productTypes, $unitTypes, $branches
    // For edit: $product (with serials loaded)
    $isEdit = isset($product);
@endphp

@extends('layouts.app')
@section('title', 'Products')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@if($isCashierContext)
@push('stylesDashboard')
    <style>
        .sidebar-fixed {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
    </style>
@endpush
@endif

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
        --amber:   #f59e0b;
    }

    .sp-bg { position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden; background: var(--bg); }
    .sp-bg::before {
        content: ''; position: absolute; inset: 0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%, transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%, transparent 55%);
    }
    .sp-blob { position: absolute; border-radius: 50%; filter: blur(60px); opacity: .11; }
    .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
    .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    .sp-wrap{position:relative;z-index:1;max-width:1400px;margin:0 auto;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif;}

    .sp-page-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 22px; flex-wrap: wrap; gap: 14px;
        animation: spUp .4s ease both;
    }
    .sp-ph-left { display: flex; align-items: center; gap: 13px; }
    .sp-ph-icon {
        width: 46px; height: 46px; border-radius: 14px;
        background: linear-gradient(135deg, var(--navy), var(--blue-lt));
        display: flex; align-items: center; justify-content: center;
        font-size: 19px; color: #fff;
        box-shadow: 0 6px 18px rgba(13,71,161,0.28);
    }
    .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.7;margin-bottom:3px;font-family:'Nunito',sans-serif;}
    .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

    .sp-btn-back{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;border:1.5px solid var(--border);background:var(--card);color:var(--navy);font-size:13px;font-weight:700;text-decoration:none;cursor:pointer;transition:all .2s ease;font-family:'Nunito',sans-serif;}
    .sp-btn-back:hover{background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px);}

    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .45s ease both;}

    .sp-card-head{padding:16px 26px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
    .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:15px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}

    .sp-form-body{padding:28px 26px;}

    .sp-form-body .form-label{font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;display:block;font-family:'Nunito',sans-serif;}

    .sp-form-body .form-control,
    .sp-form-body .form-select,
    .sp-form-body select.form-control{
        width:100%;padding:10px 14px;
        border-radius:11px;border:1.5px solid var(--border);
        font-size:13.5px;color:var(--text);background:#fafcff;
        font-family:'Plus Jakarta Sans',sans-serif;
        transition:border-color .18s,box-shadow .18s;outline:none;
        appearance:none;
    }

    .sp-form-body .form-control:focus,
    .sp-form-body .form-select:focus,
    .sp-form-body select.form-control:focus{
        border-color:var(--blue-lt);
        box-shadow:0 0 0 3px rgba(66,165,245,0.12);
        background:#fff;
    }

    .sp-form-body input[type="file"].form-control{
        padding:8px 12px;
        cursor:pointer;
    }

    .sp-section{display:flex;align-items:center;gap:10px;margin:24px 0 16px;}
    .sp-section-label{font-size:10px;font-weight:800;letter-spacing:.15em;text-transform:uppercase;color:var(--blue);white-space:nowrap;font-family:'Nunito',sans-serif;}
    .sp-section::after{content:'';flex:1;height:1px;background:linear-gradient(90deg,var(--border),transparent);}

    .sp-hidden-note{background:rgba(66,165,245,0.08);border:1.5px dashed rgba(66,165,245,0.3);border-radius:11px;padding:10px 14px;font-size:12.5px;color:var(--blue);font-style:italic;display:flex;align-items:center;gap:8px;}

    .sp-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 16px; border-radius: 11px;
        font-size: 13px; font-weight: 700; cursor: pointer;
        font-family: 'Nunito', sans-serif;
        border: none; transition: all .2s ease; text-decoration: none; white-space: nowrap;
    }
    .sp-btn-outline {
        background: transparent;
        color: var(--navy);
        border: 1.5px solid var(--border);
    }
    .sp-btn-outline:hover { background: rgba(13,71,161,0.06); border-color: var(--blue-lt); }

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@include('layouts.theme-base')

@section('content')

    <div class="d-flex min-vh-100" style="background: var(--bg);">
        <div class="sp-bg">
            <div class="sp-blob sp-blob-1"></div>
            <div class="sp-blob sp-blob-2"></div>
        </div>

        <main class="flex-fill p-4" style="position: relative; z-index: 1;">
            <div class="sp-wrap">
                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas {{ $isEdit ? 'fa-pen' : 'fa-plus' }}"></i></div>
                        <div>
                            <div class="sp-ph-title">{{ $isEdit ? 'Edit Product' : 'Add Product' }}</div>
                            <div class="sp-ph-sub">Manage your product information</div>
                        </div>
                    </div>
                    <a href="{{ $isCashierContext ? route('cashier.products.index') : route('superadmin.products.index') }}" class="sp-btn-back"><i class="fas fa-arrow-left"></i> Back to products</a>
                </div>

                <div class="sp-card">
                    <div class="sp-form-body">
                        <form method="POST" action="{{ $isEdit ? ($isCashierContext ? route('cashier.products.update', $product) : route('superadmin.products.update', $product)) : ($isCashierContext ? route('cashier.products.store') : route('superadmin.products.store')) }}" enctype="multipart/form-data" id="productForm">
                                @if($isEdit) @method('PUT') @endif
                                @csrf

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Product Name</label>
                                        <input type="text" name="product_name" class="form-control" required value="{{ $isEdit ? $product->product_name : '' }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Barcode</label>
                                        <input type="text" name="barcode" class="form-control" required value="{{ $isEdit ? $product->barcode : '' }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Brand</label>
                                        <select name="brand_id" id="brandSelect" class="form-control select2-tags" style="width:100%">
                                            <option value="">-- Select Brand --</option>
                                            @foreach($brands ?? [] as $brand)
                                                <option value="{{ $brand->id }}" {{ $isEdit && $product->brand_id == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" id="categorySelect" class="form-control select2-tags" style="width:100%">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories ?? [] as $cat)
                                                <option value="{{ $cat->id }}" {{ $isEdit && $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Unit Types</label>
                                        <select name="unit_type_ids[]" id="unitTypeSelect" class="form-control" style="width:100%" multiple>
                                            @php
                                                $selectedUnitTypes = old('unit_type_ids', $isEdit ? $product->unitTypes->pluck('id')->all() : []);
                                            @endphp
                                            @foreach($unitTypes ?? [] as $ut)
                                                <option value="{{ $ut->id }}" {{ in_array($ut->id, $selectedUnitTypes) ? 'selected' : '' }}>{{ $ut->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @php
                                        $pivotMap = [];
                                        $baseUnitTypeId = null;
                                        if ($isEdit) {
                                            foreach ($product->unitTypes as $ut) {
                                                $pivotMap[$ut->id] = [
                                                    'conversion_factor' => isset($ut->pivot->conversion_factor) ? (float) $ut->pivot->conversion_factor : 1.0,
                                                    'is_base' => isset($ut->pivot->is_base) ? (bool) $ut->pivot->is_base : false,
                                                    'unit_name' => $ut->unit_name,
                                                ];
                                                if (!is_null($ut->pivot) && !empty($ut->pivot->is_base)) {
                                                    $baseUnitTypeId = $ut->id;
                                                }
                                            }
                                        }

                                        $oldBaseUnitTypeId = old('base_unit_type_id');
                                        if (!is_null($oldBaseUnitTypeId) && $oldBaseUnitTypeId !== '') {
                                            $baseUnitTypeId = (int) $oldBaseUnitTypeId;
                                        }
                                    @endphp

                                    <div class="col-12" id="unitConversionsSection" style="display:none;">
                                        <div class="mt-2">
                                            <div class="fw-semibold mb-2">Unit Conversions</div>
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 30%;">Unit</th>
                                                            <th style="width: 20%;">Base</th>
                                                            <th>Conversion Factor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="unitConversionsBody"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 electronic-field d-none">
                                        <label class="form-label">Model Number</label>
                                        <input type="text" name="model_number" class="form-control" value="{{ $isEdit ? $product->model_number : '' }}">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Image</label>
                                        <input type="file" name="image" class="form-control">
                                        @if($isEdit && $product->image)
                                            <small class="text-muted">Current image: {{ basename($product->image) }}</small>
                                        @endif
                                    </div>

                                    <div class="col-md-4 electronic-field d-none">
                                        <label class="form-label">Warranty Type</label>
                                        <select name="warranty_type" class="form-control">
                                            <option value="shop" {{ $isEdit && $product->warranty_type == 'shop' ? 'selected' : '' }}>Shop</option>
                                            <option value="manufacturer" {{ $isEdit && $product->warranty_type == 'manufacturer' ? 'selected' : '' }}>Manufacturer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Warranty Coverage (months)</label>
                                        <input type="number" name="warranty_coverage_months" min="0" class="form-control" value="{{ $isEdit ? $product->warranty_coverage_months : '' }}">
                                    </div>

                                    <div class="col-md-3 electronic-field d-none">
                                        <label class="form-label">Voltage Specs</label>
                                        <input type="text" name="voltage_specs" class="form-control" value="{{ $isEdit ? $product->voltage_specs : '' }}" placeholder="e.g. 110-220V">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active" {{ $isEdit && $product->status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ $isEdit && $product->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn" style="background-color:var(--theme-color); color:white">{{ $isEdit ? 'Update Product' : 'Save Product' }}</button>
                                </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- Scripts --}}

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            console.log('jQuery and Select2 loaded successfully');
            
            // Initialize all select2 dropdowns
            $('#brandSelect').select2({
                tags: true,
                placeholder: '-- Select or create --',
                allowClear: true,
                width: 'resolve'
            });

            $('#categorySelect').select2({
                tags: true,
                placeholder: '-- Select Category --',
                allowClear: true,
                width: 'resolve'
            });

            $('#unitTypeSelect').select2({
                placeholder: '-- Select items --',
                allowClear: true,
                width: 'resolve'
            });

            const unitTypesData = @json(($unitTypes ?? collect())->map(function($u){
                return ['id' => $u->id, 'name' => $u->name, 'unit_name' => $u->unit_name];
            })->values());

            const pivotMap = @json($pivotMap ?? []);
            const initialBaseUnitTypeId = @json($baseUnitTypeId);

            const categoriesMeta = {};
            @foreach($categories ?? [] as $c)
                categoriesMeta["{{ $c->id }}"] = {
                    category_type: "{{ $c->category_type ?? 'non_electronic' }}"
                };
            @endforeach

            function normalizeUnitName(name) {
                return String(name || '').trim().toLowerCase().replace(/\s+/g, ' ');
            }

            function unitScalar(unitName) {
                const n = normalizeUnitName(unitName);
                const count = {
                    'pc': 1,
                    'pcs': 1,
                    'piece': 1,
                    'pieces': 1,
                    'can': 1,
                    'cans': 1,
                    'dozen': 12,
                };
                if (count[n] != null) return { type: 'count', scalar: count[n] };

                const mass = {
                    'mg': 0.001,
                    'milligram': 0.001,
                    'milligrams': 0.001,
                    'g': 1,
                    'gram': 1,
                    'grams': 1,
                    'kg': 1000,
                    'kilogram': 1000,
                    'kilograms': 1000,
                };
                if (mass[n] != null) return { type: 'mass', scalar: mass[n] };

                const volume = {
                    'ml': 1,
                    'milliliter': 1,
                    'milliliters': 1,
                    'l': 1000,
                    'liter': 1000,
                    'liters': 1000,
                    'kl': 1000000,
                    'kiloliter': 1000000,
                    'kiloliters': 1000000,
                };
                if (volume[n] != null) return { type: 'volume', scalar: volume[n] };

                return null;
            }

            function computeFactor(unitName, baseUnitName) {
                const u = unitScalar(unitName);
                const b = unitScalar(baseUnitName);
                if (!u || !b) return null;
                if (u.type !== b.type) return null;
                if (b.scalar <= 0 || u.scalar <= 0) return null;
                return Math.round((u.scalar / b.scalar) * 1000000) / 1000000;
            }

            function selectedUnitTypeIds() {
                const v = $('#unitTypeSelect').val() || [];
                return v.map(id => parseInt(id, 10)).filter(n => !isNaN(n));
            }

            function resolveBaseUnitTypeId(ids) {
                const requested = parseInt($('input[name="base_unit_type_id"]:checked').val() || '', 10);
                if (!isNaN(requested) && ids.includes(requested)) return requested;
                if (initialBaseUnitTypeId && ids.includes(parseInt(initialBaseUnitTypeId, 10))) return parseInt(initialBaseUnitTypeId, 10);
                return ids.length ? ids[0] : null;
            }

            function renderConversions() {
                const ids = selectedUnitTypeIds();
                const section = document.getElementById('unitConversionsSection');
                const body = document.getElementById('unitConversionsBody');
                const requestedBase = parseInt($('input[name="base_unit_type_id"]:checked').val() || '', 10);
                const existingFactorInputs = {};
                document.querySelectorAll('#unitConversionsBody input[name^="conversion_factor["]').forEach((el) => {
                    const match = el.name.match(/^conversion_factor\[(\d+)\]$/);
                    if (match && match[1]) {
                        existingFactorInputs[parseInt(match[1], 10)] = el.value;
                    }
                });

                body.innerHTML = '';

                if (!ids.length || ids.length === 1) {
                    section.style.display = 'none';
                    return;
                }

                section.style.display = '';

                const baseId = (!isNaN(requestedBase) && ids.includes(requestedBase))
                    ? requestedBase
                    : resolveBaseUnitTypeId(ids);
                const baseUnit = unitTypesData.find(u => u.id === baseId);
                const baseUnitName = baseUnit ? baseUnit.unit_name : null;

                ids.forEach((id) => {
                    const unit = unitTypesData.find(u => u.id === id);
                    const displayName = unit ? unit.name : ('Unit #' + id);
                    const unitName = unit ? unit.unit_name : null;

                    const existing = pivotMap && pivotMap[id] ? pivotMap[id] : null;

                    const isBase = (baseId === id);
                    let factor = 1;
                    if (isBase) {
                        factor = 1;
                    } else if (existingFactorInputs[id] != null && existingFactorInputs[id] !== '') {
                        const v = parseFloat(existingFactorInputs[id]);
                        factor = !isNaN(v) && v > 0 ? v : 1;
                    } else if (existing && existing.conversion_factor) {
                        factor = existing.conversion_factor;
                    } else {
                        const computed = computeFactor(unitName, baseUnitName);
                        factor = computed != null ? computed : 1;
                    }

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${displayName}</td>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="base_unit_type_id" value="${id}" ${isBase ? 'checked' : ''}>
                            </div>
                        </td>
                        <td>
                            <input type="number" step="0.000001" min="0.000001" class="form-control form-control-sm" name="conversion_factor[${id}]" value="${factor}" ${isBase ? 'readonly' : ''}>
                        </td>
                    `;

                    body.appendChild(tr);
                });
            }

            $(document).on('change', '#unitTypeSelect', function() {
                renderConversions();
            });

            $(document).on('change', 'input[name="base_unit_type_id"]', function() {
                renderConversions();
            });

            renderConversions();

            // --- Conditional Visibility Logic (Category Type) ---
            const categorySelect = $('#categorySelect');
            const electronicFields = $('.electronic-field');

            function isElectronicCategoryType(categoryType) {
                return categoryType === 'electronic_with_serial' || categoryType === 'electronic_without_serial';
            }

            function toggleFieldsByCategory() {
                const raw = categorySelect.val();
                const meta = categoriesMeta[String(raw)] || { category_type: 'non_electronic' };
                const isElectronic = isElectronicCategoryType(meta.category_type);

                if (isElectronic) {
                    electronicFields.removeClass('d-none');
                } else {
                    electronicFields.addClass('d-none');
                }
            }

            categorySelect.on('change', function () {
                toggleFieldsByCategory();
            });

            setTimeout(function() {
                toggleFieldsByCategory();
            }, 100);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Make loading functions globally accessible
        function showLoading() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }
        }

        function hideLoading() {
            const submitBtn = document.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '{{ $isEdit ? 'Update Product' : 'Save Product' }}';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up form handler');
            const productForm = document.getElementById('productForm');
            if (productForm) {
                console.log('Product form found, attaching submit handler');
                productForm.addEventListener('submit', function(e) {
                    console.log('Form submit event triggered');
                    e.preventDefault();
                    e.stopPropagation();

                    // Show loading state immediately
                    showLoading();

                    const form = this;
                    const formData = new FormData(form);
                    const url = form.action;

                    // Log form data for debugging
                    console.log('=== FORM SUBMISSION DEBUG ===');
                    console.log('Form action URL:', url);
                    console.log('Form data entries:');
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}: ${value}`);
                    }
                    console.log('==============================');

                    // Clear previous errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', response.headers);
                        return response.json().then(data => {
                            console.log('Response data:', data);
                            if (!response.ok) {
                                throw data;
                            }
                            return data;
                        });
                    })
                    .then(data => {
                        console.log('Success response:', data);
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '{{ $isEdit ? 'Product updated successfully.' : 'Product created successfully.' }}',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route('superadmin.products.index') }}';
                            });
                        } else if (data.errors) {
                            console.error('Validation errors:', data.errors);
                            Object.keys(data.errors).forEach(key => {
                                const field = document.querySelector(`[name="${key}"]`);
                                if (field) {
                                    field.classList.add('is-invalid');
                                    const error = document.createElement('div');
                                    error.className = 'invalid-feedback';
                                    error.innerText = data.errors[key][0];
                                    field.parentNode.appendChild(error);
                                    console.error(`Field error for ${key}:`, data.errors[key][0]);
                                } else {
                                    console.error(`Field not found for error key: ${key}`);
                                }
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please check the form for errors.',
                            });
                        } else {
                            throw new Error(data.message || 'An unknown error occurred.');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        let errorMessage = 'Something went wrong. Please try again.';
                        if (error.message) {
                            errorMessage = error.message;
                        }
                        if (error.errors) {
                            console.error('Server validation errors:', error.errors);
                            errorMessage = 'Validation failed. Check console for details.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'An Error Occurred',
                            text: errorMessage,
                        });
                    })
                    .finally(() => {
                        // Always hide loading state
                        hideLoading();
                    });
                });
            }
        });
    </script>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection

