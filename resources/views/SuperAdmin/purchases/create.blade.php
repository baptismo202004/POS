@extends('layouts.app')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:    #0D47A1;
        --blue:    #1976D2;
        --blue-lt: #42A5F5;
        --bg:      #EBF3FB;
        --card:    #ffffff;
        --border:  rgba(25,118,210,0.12);
        --text:    #1a2744;
        --muted:   #6b84aa;
        --red:     #ef4444;
        --green:   #10b981;
        --amber:   #f59e0b;
        --shadow:  0 4px 28px rgba(13,71,161,0.09);
        --theme-color: var(--navy);
    }

    /* ── Background ── */
    .sp-bg { position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg); }
    .sp-bg::before {
        content:'';position:absolute;inset:0;
        background:
            radial-gradient(ellipse 60% 50% at 0% 0%,    rgba(13,71,161,0.09) 0%,transparent 60%),
            radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.07) 0%,transparent 55%);
    }
    .sp-blob { position:absolute;border-radius:50%;filter:blur(60px);opacity:.11; }
    .sp-blob-1 { width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite; }
    .sp-blob-2 { width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite; }
    @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

    .sp-wrap { position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif; }

    /* ── Page header ── */
    .sp-page-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:14px; }
    .sp-ph-left { display:flex;align-items:center;gap:13px; }
    .sp-ph-icon { width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28); }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
    .sp-ph-actions { display:flex;align-items:center;gap:9px;flex-wrap:wrap; }

    /* ── Buttons ── */
    .sp-btn { display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap; }
    .sp-btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26); }
    .sp-btn-primary:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }
    .sp-btn-outline { background:var(--card);color:var(--navy);border:1.5px solid var(--border); }
    .sp-btn-outline:hover { background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px); }
    .sp-btn-soft { background:rgba(13,71,161,0.06);color:var(--navy);border:1.5px solid var(--border); }
    .sp-btn-soft:hover { background:rgba(13,71,161,0.12);color:var(--navy); }

    /* ── Card ── */
    .sp-card { background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden; }
    .sp-card-head { padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden; }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-c-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif; }
    .sp-card-body { padding:22px; }

    /* ── Form ── */
    .sp-form label.form-label { font-size:11px;letter-spacing:.10em;text-transform:uppercase;color:var(--muted);font-weight:900;font-family:'Nunito',sans-serif; }
    .sp-form .form-control,
    .sp-form .form-select { border-radius:12px;border:1.5px solid var(--border);font-size:13px;padding:10px 12px;background:#fff;color:var(--text);transition:border-color .18s,box-shadow .18s; }
    .sp-form .form-control:focus,
    .sp-form .form-select:focus { border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.12); }
    .sp-form #grand-total { font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);background:rgba(240,246,255,0.55); }
    .sp-form .item-row { border:1px solid rgba(13,71,161,0.09) !important;border-radius:16px !important;background:rgba(240,246,255,0.55);box-shadow:0 10px 26px rgba(13,71,161,0.06); }
    .sp-form .section-title { font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);font-size:14px;margin:16px 0 10px;display:flex;align-items:center;gap:8px; }
    .sp-form .section-title i { color:rgba(25,118,210,0.9); }

    /* ── Supplier modal ── */
    .purchases-modal .modal-content { border-radius:18px;border:1px solid rgba(25,118,210,0.18);box-shadow:0 18px 50px rgba(13,71,161,0.18); }
    .purchases-modal .modal-header { background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);color:#fff;border-bottom:none; }
    .purchases-modal .modal-header h5 { color:#fff;font-family:'Nunito',sans-serif;font-weight:900; }
    .purchases-modal .btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue));border:none;box-shadow:0 4px 14px rgba(13,71,161,0.26);border-radius:12px;font-weight:900;font-family:'Nunito',sans-serif; }
    .purchases-modal .btn-secondary { background:rgba(25,118,210,0.10);color:var(--navy);border:1px solid rgba(25,118,210,0.20);border-radius:12px;font-weight:900;font-family:'Nunito',sans-serif; }

    /* ── Select2 ── */
    .sp-form .select2-container--default .select2-selection--single { height:42px;border-radius:12px;border:1.5px solid var(--border);display:flex;align-items:center; }
    .sp-form .select2-container--default .select2-selection--single .select2-selection__rendered { padding-left:12px;color:var(--text);font-size:13px; }
    .sp-form .select2-container--default .select2-selection--single .select2-selection__arrow { height:42px; }
    .select2-dropdown { border-radius:14px;border:1px solid rgba(25,118,210,0.22);overflow:hidden; }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background:linear-gradient(135deg,rgba(13,71,161,0.12),rgba(25,118,210,0.12));color:var(--text); }
    .select2-results__option.select2-add-supplier { font-weight:900;font-family:'Nunito',sans-serif;color:var(--navy); }
</style>
@endpush

@section('content')
<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <main class="flex-fill" style="position:relative;z-index:1;">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-cart-plus"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Operations</div>
                        <div class="sp-ph-title">Add New Purchase</div>
                        <div class="sp-ph-sub">Create a purchase order and add items</div>
                    </div>
                </div>
                <div class="sp-ph-actions">
                    <a href="{{ route('superadmin.purchases.index') }}" class="sp-btn sp-btn-outline">
                        <i class="fas fa-arrow-left"></i> Back to Purchases
                    </a>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-pen-to-square"></i> Purchase Form</div>
                    <span class="sp-c-badge">Step 1: Details</span>
                </div>

                <div class="sp-card-body sp-form">
                    <form method="POST" action="{{ route('superadmin.purchases.store') }}">
                        @csrf

                        <div class="row g-3 mb-2">
                            <div class="col-md-4">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select supplier-select" required>
                                    <option value="">-- Select Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference No.</label>
                                <input type="text" name="reference_number" class="form-control">
                                @error('reference_number')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="section-title"><i class="fas fa-boxes-stacked"></i> Purchase Items</div>
                        <div id="items-container"></div>

                        <div class="row mt-4 align-items-end">
                            <div class="col-md-auto ms-auto">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-select">
                                    <option value="pending" selected>Pending</option>
                                    <option value="paid">Paid</option>
                                </select>
                            </div>
                            <div class="col-md-auto">
                                <label class="form-label">Grand Total</label>
                                <input type="text" id="grand-total" class="form-control fs-5 fw-bold" readonly value="0.00">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" id="add-item-btn" class="sp-btn sp-btn-soft">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                            <button type="submit" class="sp-btn sp-btn-primary">
                                <i class="fas fa-save"></i> Save Purchase
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ITEM TEMPLATE -->
<template id="item-template">
    <div class="item-row border rounded p-3 mb-3">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Product</label>
                <select name="items[][product_id]" class="form-select product-select" required>
                    <option value="">-- Select Product --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Qty - Unit</label>
                <div class="input-group">
                    <input type="number" name="items[][primary_quantity]" class="form-control primary-qty-input" min="1" value="1" required>
                    <select name="items[][unit_type_id]" class="form-select unit-type-select" required>
                        <option value="">-- Select Unit --</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cost</label>
                <input type="number" name="items[][cost]" class="form-control item-cost" step="0.01" required>
            </div>
            <div class="col-md-1">
                <label class="form-label invisible">Remove</label>
                <button type="button" class="btn btn-danger remove-item-btn w-100">Remove</button>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-12 serials-container d-none">
                <div class="electronics-panel-host" data-loaded="0"></div>
            </div>
        </div>
    </div>
</template>

<!-- SERIAL ENTRY TEMPLATE -->
<template id="serial-entry-template">
    <div class="row g-3 align-items-center serial-entry-row mb-2">
        <div class="col-md-4">
            <label class="form-label">Serial Number / IMEI</label>
            <input type="text" name="serial_number" class="form-control" placeholder="Enter serial number" required>
        </div>
        <div class="col-md-3 js-per-serial-expiry-wrap">
            <label class="form-label">Warranty Expiry</label>
            <input type="date" name="warranty_expiry" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label invisible">Remove</label>
            <button type="button" class="btn btn-sm btn-outline-danger remove-serial-btn w-100">Remove</button>
        </div>
    </div>
</template>

<!-- ADD SUPPLIER MODAL -->
<div class="modal fade purchases-modal" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Add New Supplier</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSupplierForm" action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" name="supplier_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSupplierBtn">Save Supplier</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Please check your input',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)'
        });
    @endif

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '{!! session('error') !!}',
            confirmButtonText: 'Okay',
            confirmButtonColor: 'var(--theme-color)'
        });
    @endif

    const itemsContainer = document.getElementById('items-container');
    const addItemBtn     = document.getElementById('add-item-btn');
    const template       = document.getElementById('item-template');

    if (!template) { console.error('item-template not found'); return; }

    let itemIndex = 0;

    // ── Totals ──────────────────────────────────────────────────────────────
    function updateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty  = parseFloat(row.querySelector('input[name$="[primary_quantity]"]')?.value) || 0;
            const cost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
            grandTotal += qty * cost;
        });
        const el = document.getElementById('grand-total');
        if (el) { el.value = grandTotal.toFixed(2); }
    }

    // ── Conversion factor ────────────────────────────────────────────────────
    function updateRowConversionFactor(row) {
        if (!row) return;
        const select = row.querySelector('.unit-type-select');
        if (!select) return;
        const factor = select.selectedOptions?.[0]?.dataset?.conversionFactor;
        row.dataset.conversionFactor = (factor !== undefined && factor !== null) ? factor : '1';
    }

    // ── Load product unit types ──────────────────────────────────────────────
    function loadProductUnits(row, productId) {
        const unitSelect = row.querySelector('.unit-type-select');
        if (!unitSelect) return;
        unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';
        if (!productId) return;

        $.getJSON(`/superadmin/products/${productId}/unit-types`, function (response) {
            (response.units || []).forEach(function (unit) {
                const opt = document.createElement('option');
                opt.value = unit.id;
                opt.textContent = unit.name;
                opt.dataset.conversionFactor = unit.conversion_factor ?? 1;
                unitSelect.appendChild(opt);
            });
            if ((response.units || []).length === 1) {
                unitSelect.value = response.units[0].id;
            }
            updateRowConversionFactor(row);
        });
    }

    // ── Product metadata ─────────────────────────────────────────────────────
    const productsMeta = {};
    @foreach($products as $p)
        productsMeta["{{ $p->id }}"] = {
            category_type: "{{ $p->category?->category_type ?? 'non_electronic' }}",
            warranty_coverage_months: {{ (int) ($p->warranty_coverage_months ?? 0) }}
        };
    @endforeach

    function normalizeCategoryType(v) { return String(v || '').trim().toLowerCase(); }
    function isElectronic(ct) { const c = normalizeCategoryType(ct); return c === 'electronic_without_serial' || c === 'electronic_with_serial'; }
    function requiresSerials(ct) { return normalizeCategoryType(ct) === 'electronic_with_serial'; }

    // ── Date / warranty helpers ──────────────────────────────────────────────
    function ymd(d) { return d.toISOString().slice(0, 10); }
    function parseYmd(v) {
        if (!v) return null;
        const p = String(v).split('-');
        if (p.length !== 3) return null;
        const dt = new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
        return isNaN(dt.getTime()) ? null : dt;
    }
    function addMonthsSafe(d, m) {
        const r = new Date(d.getTime()), day = r.getDate();
        r.setMonth(r.getMonth() + m);
        if (r.getDate() < day) { r.setDate(0); }
        return r;
    }
    function computeWarrantyExpiry(dateStr, months) {
        const d = parseYmd(dateStr);
        if (!d) return '';
        const m = parseInt(String(months || '0'), 10);
        if (!m || m <= 0) return '';
        return ymd(addMonthsSafe(d, m));
    }
    function getPurchaseDateValue() {
        return document.querySelector('input[name="purchase_date"]')?.value || '';
    }

    // ── Serial warranty defaults ─────────────────────────────────────────────
    function updateSerialWarrantyDefaultsForRow(row) {
        if (!row) return;
        const productId = row.querySelector('.product-select')?.value;
        const meta      = productsMeta[String(productId)] || { warranty_coverage_months: 0 };
        const computed  = computeWarrantyExpiry(getPurchaseDateValue(), meta.warranty_coverage_months);

        const host = row.querySelector('.electronics-panel-host');
        if (!host || host.dataset.loaded !== '1') return;

        const sameToggle      = host.querySelector('.js-same-expiry-toggle');
        const sharedWrap      = host.querySelector('.js-shared-expiry-wrap');
        const sharedInput     = host.querySelector('.js-shared-expiry-input');
        const dateInputs      = host.querySelectorAll('input[type="date"][name$="[warranty_expiry]"]');
        const perSerialWraps  = host.querySelectorAll('.js-per-serial-expiry-wrap');
        const coverageHint    = host.querySelector('.js-warranty-coverage-hint');

        // Show warranty coverage hint
        if (coverageHint) {
            const months = parseInt(meta.warranty_coverage_months || 0, 10);
            if (months > 0) {
                coverageHint.textContent = `(Warranty: ${months} month${months !== 1 ? 's' : ''})`;
                coverageHint.style.display = '';
            } else {
                coverageHint.style.display = 'none';
            }
        }

        if (sharedInput && !sharedInput.value && computed) { sharedInput.value = computed; }

        const isSame = sameToggle && sameToggle.checked;

        if (isSame) {
            if (sharedWrap) sharedWrap.classList.remove('d-none');
            // Hide per-serial expiry fields
            perSerialWraps.forEach(w => w.classList.add('d-none'));
            const val = (sharedInput && sharedInput.value) ? sharedInput.value : computed;
            dateInputs.forEach(inp => { inp.value = val || ''; });
        } else {
            if (sharedWrap) sharedWrap.classList.add('d-none');
            // Show per-serial expiry fields
            perSerialWraps.forEach(w => w.classList.remove('d-none'));
            dateInputs.forEach(inp => { if (!inp.value && computed) { inp.value = computed; } });
        }
    }

    // ── Serial counter badge ─────────────────────────────────────────────────
    function updateSerialCounter(itemRow) {
        const sc = itemRow.querySelector('.serial-entries-container');
        const badge = itemRow.querySelector('.serial-counter');
        if (sc && badge) {
            const n = sc.children.length;
            badge.textContent = `${n} serial${n !== 1 ? 's' : ''}`;
            badge.style.display = n > 0 ? 'inline-block' : 'none';
        }
    }

    // ── Load electronics panel ───────────────────────────────────────────────
    async function ensureElectronicsPanelLoaded(row) {
        const host = row.querySelector('.electronics-panel-host');
        if (!host || host.dataset.loaded === '1') return;

        const res  = await fetch('{{ route('superadmin.purchases.electronics.panel') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const html = await res.text();
        host.innerHTML = html;
        host.dataset.loaded = '1';

        const sameToggle  = host.querySelector('.js-same-expiry-toggle');
        const sharedInput = host.querySelector('.js-shared-expiry-input');
        if (sameToggle)  { sameToggle.addEventListener('change', () => updateSerialWarrantyDefaultsForRow(row)); }
        if (sharedInput) { sharedInput.addEventListener('input',  () => updateSerialWarrantyDefaultsForRow(row)); }

        updateSerialWarrantyDefaultsForRow(row);
        updateSerialCounter(row);
    }

    // ── Apply category UI ────────────────────────────────────────────────────
    function applyCategoryTypeUI(row, productId) {
        if (!row) return;
        const meta = productsMeta[String(productId)] || { category_type: 'non_electronic' };
        row.dataset.categoryType = normalizeCategoryType(meta.category_type || 'non_electronic');

        const sc = row.querySelector('.serials-container');
        if (!sc) return;

        if (requiresSerials(row.dataset.categoryType)) {
            sc.classList.remove('d-none');
            ensureElectronicsPanelLoaded(row);
        } else {
            sc.classList.add('d-none');
            const host = sc.querySelector('.electronics-panel-host');
            if (host) { host.innerHTML = ''; host.dataset.loaded = '0'; }
        }
        updateRowConversionFactor(row);
    }

    // ── Add row ──────────────────────────────────────────────────────────────
    function addRow() {
        const node = template.content.cloneNode(true);
        node.querySelectorAll('[name^="items[]"]').forEach(el => {
            el.setAttribute('name', el.getAttribute('name').replace('[]', `[${itemIndex}]`));
        });
        itemsContainer.appendChild(node);
        itemIndex++;
        updateTotals();
    }

    addItemBtn.addEventListener('click', addRow);
    addRow();

    // ── Purchase date change ─────────────────────────────────────────────────
    const purchaseDateInput = document.querySelector('input[name="purchase_date"]');
    if (purchaseDateInput) {
        purchaseDateInput.addEventListener('change', function () {
            document.querySelectorAll('.item-row').forEach(row => updateSerialWarrantyDefaultsForRow(row));
        });
    }

    // ── Container events ─────────────────────────────────────────────────────
    itemsContainer.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('.item-row');
            applyCategoryTypeUI(row, e.target.value);
            loadProductUnits(row, e.target.value);
        }
        if (e.target.classList.contains('unit-type-select')) {
            updateRowConversionFactor(e.target.closest('.item-row'));
            updateTotals();
        }
    });

    itemsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('primary-qty-input') || e.target.classList.contains('item-cost')) {
            updateTotals();
        }
    });

    itemsContainer.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
            e.target.closest('.item-row').remove();
            updateTotals();
        }

        if (e.target.classList.contains('add-serial-btn')) {
            const itemRow       = e.target.closest('.item-row');
            const sc            = itemRow.querySelector('.serial-entries-container');
            const serialTpl     = document.getElementById('serial-entry-template');
            const curItemIndex  = Array.from(itemsContainer.children).indexOf(itemRow);
            const serialIndex   = sc.children.length;

            const node = serialTpl.content.cloneNode(true);
            node.querySelector('[name="serial_number"]').name = `items[${curItemIndex}][serials][${serialIndex}][serial_number]`;
            const wi = node.querySelector('[name="warranty_expiry"]');
            wi.name  = `items[${curItemIndex}][serials][${serialIndex}][warranty_expiry]`;

            const productId = itemRow.querySelector('.product-select')?.value;
            const meta      = productsMeta[String(productId)] || { warranty_coverage_months: 0 };
            wi.value = computeWarrantyExpiry(getPurchaseDateValue(), meta.warranty_coverage_months) || '';

            sc.appendChild(node);
            updateSerialWarrantyDefaultsForRow(itemRow);
            updateSerialCounter(itemRow);
        }

        if (e.target.classList.contains('remove-serial-btn')) {
            const serialRow = e.target.closest('.serial-entry-row');
            const itemRow   = serialRow.closest('.item-row');
            serialRow.remove();
            updateSerialCounter(itemRow);
        }

        if (e.target.classList.contains('toggle-electronics-panel-btn')) {
            const row  = e.target.closest('.item-row');
            const body = row.querySelector('.electronics-panel-body');
            if (body) {
                body.classList.toggle('d-none');
                e.target.textContent = body.classList.contains('d-none') ? 'Show' : 'Hide';
            }
        }
    });

    // ── Supplier Select2 ─────────────────────────────────────────────────────
    const $supplierSelect    = $('.supplier-select');
    const $supplierForm      = $('#addSupplierForm');
    const $supplierNameInput = $supplierForm.find('[name="supplier_name"]');
    const $saveSupplierBtn   = $('#saveSupplierBtn');

    function initSupplierSelect2() {
        $supplierSelect.select2({ width: '100%' }).off('select2:open').on('select2:open', function () {
            setTimeout(() => {
                const results = document.querySelector('.select2-results__options');
                if (!results || document.querySelector('.select2-add-supplier')) return;
                const term = document.querySelector('.select2-search__field')?.value || '';
                const li = document.createElement('li');
                li.className = 'select2-results__option select2-add-supplier';
                li.innerHTML = '➕ Add new supplier';
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault(); e.stopPropagation();
                    $supplierSelect.select2('close');
                    $supplierNameInput.val(term);
                    $('#addSupplierModal').modal('show');
                });
                results.appendChild(li);
            }, 0);
        });
    }

    initSupplierSelect2();

    $saveSupplierBtn.on('click', function () {
        const name = $supplierNameInput.val().trim();
        if (!name) { Swal.fire('Error', 'Supplier name is required.', 'error'); return; }
        $.ajax({
            url: $supplierForm.attr('action'),
            method: 'POST',
            data: $supplierForm.serialize(),
            success: function (response) {
                if (!response.success || !response.supplier) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Unexpected response from server.' });
                    return;
                }
                const supplier = response.supplier;
                // Destroy Select2, append option to raw <select>, reinitialize
                $supplierSelect.select2('destroy');
                $supplierSelect.append(new Option(supplier.supplier_name, supplier.id, false, false));
                initSupplierSelect2();
                $supplierSelect.val(supplier.id).trigger('change');
                $('#addSupplierModal').modal('hide');
                $supplierForm[0].reset();
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Supplier saved!', showConfirmButton: false, timer: 2000, timerProgressBar: true });
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join('<br>') : 'An error occurred.';
                Swal.fire({ icon: 'error', title: 'Save Failed', html: msg });
            }
        });
    });

    // ── Form submit validation ────────────────────────────────────────────────
    const purchaseForm = document.querySelector('form[method="POST"][action*="purchases"]');
    if (purchaseForm) {
        purchaseForm.addEventListener('submit', function (e) {
            let firstError = null;
            const allSerials = [];

            if (!document.querySelector('select[name="supplier_id"]')?.value) {
                firstError = 'Supplier must be selected before saving purchase.';
            }

            if (!firstError) {
                document.querySelectorAll('.item-row').forEach(row => {
                    if (firstError) return;
                    const ct = row.dataset.categoryType || 'non_electronic';
                    const needsSerial = requiresSerials(ct);
                    const qty = parseFloat(row.querySelector('input[name$="[primary_quantity]"]')?.value || '0');
                    const cf  = parseFloat(row.dataset.conversionFactor || '1') || 1;
                    const required = Math.round(qty * cf);
                    const sc = row.querySelector('.serial-entries-container');
                    const inputs = sc ? sc.querySelectorAll('input[name$="[serial_number]"]') : [];
                    const count = inputs.length;

                    if (needsSerial && count === 0) {
                        firstError = 'This product requires serial numbers; please add them before saving.'; return;
                    }
                    if (count > 0 && count !== required) {
                        firstError = `Serial numbers must match quantity. Required: ${required}, Provided: ${count}`; return;
                    }

                    const vals = Array.from(inputs).map(i => i.value.trim()).filter(v => v);
                    if (vals.length !== new Set(vals).size) {
                        firstError = 'Duplicate serial number detected in the form.'; return;
                    }

                    for (const inp of inputs) {
                        const s = inp.value.trim();
                        if (!s) continue;
                        if (s.length < 8 || s.length > 30) { firstError = `Serial "${s}" must be 8–30 characters.`; return; }
                        if (!/^[A-Z0-9-]+$/i.test(s)) { firstError = `Serial "${s}" contains invalid characters.`; return; }
                        allSerials.push(s);
                    }
                });
            }

            if (firstError) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Validation Error', text: firstError, confirmButtonText: 'Got it' });
                return;
            }

            if (allSerials.length > 0) {
                e.preventDefault();
                fetch('{{ route("superadmin.purchases.check-serials") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ serial_numbers: allSerials })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.duplicates && data.duplicates.length > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Duplicate Serial Numbers',
                            html: `The following already exist:<br><ul style="text-align:left">${data.duplicates.map(s => `<li>${s}</li>`).join('')}</ul>`,
                            confirmButtonText: 'Got it'
                        });
                    } else {
                        purchaseForm.submit();
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Unable to verify serial numbers. Please try again.' }));
            }
        });
    }
});
</script>
@endpush
