@extends('layouts.app')

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
        --amber:   #f59e0b;
        --bg:      #EBF3FB;
        --card:    #ffffff;
        --border:  rgba(25,118,210,0.12);
        --text:    #1a2744;
        --muted:   #6b84aa;
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

    /* ── Wrap ── */
    .sp-wrap { position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif; }

    /* ── Page header ── */
    .sp-page-head {
        display:flex;align-items:center;justify-content:space-between;
        margin-bottom:22px;flex-wrap:wrap;gap:14px;
        animation:spUp .4s ease both;
    }
    .sp-ph-left { display:flex;align-items:center;gap:13px; }
    .sp-ph-icon {
        width:48px;height:48px;border-radius:14px;
        background:linear-gradient(135deg,var(--navy),var(--blue-lt));
        display:flex;align-items:center;justify-content:center;
        font-size:20px;color:#fff;
        box-shadow:0 6px 20px rgba(13,71,161,0.28);
    }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }
    .sp-ph-actions { display:flex;align-items:center;gap:9px;flex-wrap:wrap; }

    /* ── Buttons ── */
    .sp-btn {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 18px;border-radius:11px;
        font-size:13px;font-weight:700;cursor:pointer;
        font-family:'Nunito',sans-serif;
        border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;
    }
    .sp-btn-primary { background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26); }
    .sp-btn-primary:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }
    .sp-btn-amber { background:linear-gradient(135deg,#d97706,#f59e0b);color:#fff;box-shadow:0 4px 14px rgba(245,158,11,0.28); }
    .sp-btn-amber:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(245,158,11,0.38);color:#fff; }
    .sp-btn-outline { background:var(--card);color:var(--navy);border:1.5px solid var(--border); }
    .sp-btn-outline:hover { background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px); }
    .sp-btn-soft { background:rgba(13,71,161,0.06);color:var(--navy);border:1.5px solid var(--border); }
    .sp-btn-soft:hover { background:rgba(13,71,161,0.12);color:var(--navy); }
    .sp-btn-danger { background:transparent;color:var(--red);border:1.5px solid rgba(239,68,68,0.30); }
    .sp-btn-danger:hover { background:rgba(239,68,68,0.07);border-color:var(--red);color:var(--red); }

    /* ── Card ── */
    .sp-card {
        background:var(--card);border-radius:20px;
        border:1px solid var(--border);
        box-shadow:0 4px 28px rgba(13,71,161,0.09);
        overflow:hidden;animation:spUp .45s ease both;
    }
    .sp-card-head {
        padding:15px 22px;
        background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);
        display:flex;align-items:center;justify-content:space-between;
        position:relative;overflow:hidden;
    }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-card-body { padding: 22px; }

    /* ── Form look ── */
    .sp-form .form-label { font-size:11.5px;font-weight:700;color:var(--navy);letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;font-family:'Nunito',sans-serif; }
    .sp-form .form-control,
    .sp-form .form-select {
        border-radius:11px;
        border:1.5px solid var(--border);
        padding:10px 14px;
        font-size:13.5px;
        background:#fafcff;
        color:var(--text);
        font-family:'Plus Jakarta Sans',sans-serif;
        box-shadow:none;
        transition:border-color .18s, box-shadow .18s;
    }
    .sp-form .form-control:focus,
    .sp-form .form-select:focus {
        border-color:var(--blue-lt);
        box-shadow:0 0 0 3px rgba(66,165,245,0.12);
        background:#fff;
    }

    /* ── Items ── */
    .sp-form .item-row { border:1px solid var(--border) !important; border-radius:16px !important; background:rgba(255,255,255,0.92); box-shadow:0 3px 16px rgba(13,71,161,0.06); }
    .sp-form .item-row.bg-light { background:rgba(240,246,255,0.72) !important; }
    .sp-form .item-row .input-group > .form-control,
    .sp-form .item-row .input-group > .form-select { border-radius:11px; }
    .sp-form .serials-container > div { border:1px solid var(--border) !important; border-radius:14px; background:rgba(13,71,161,0.03); }

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@section('content')
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@4/dist/tesseract.min.js"></script>

    <div class="d-flex min-vh-100" style="background:var(--bg);">
        <div class="sp-bg">
            <div class="sp-blob sp-blob-1"></div>
            <div class="sp-blob sp-blob-2"></div>
        </div>

        <main class="flex-fill p-4" style="position:relative;z-index:1;">
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
                        <button type="button" id="ocr-button" class="sp-btn sp-btn-amber">
                            <i class="fas fa-eye"></i> OCR
                        </button>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-receipt"></i> Purchase Information</div>
                    </div>

                    <div class="sp-card-body sp-form">
                        <form method="POST" action="{{ route('superadmin.purchases.store') }}">
                        @csrf

                        <div class="row g-3 mb-4">

                            <div class="col-md-4">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select supplier-select">
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

                        <h5 style="font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);margin-top:6px;">Purchase Items</h5>
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

                        <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
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


                <div class="col-md-2">
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

    <!-- UNMATCHED ITEM TEMPLATE -->
    <template id="unmatched-item-template">
        <div class="item-row border rounded p-3 mb-3 bg-light">
            <input type="hidden" name="items[][is_new]" value="1">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">New Product Name</label>
                    <input type="text" name="items[][product_name]" class="form-control" required>
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


                <div class="col-md-2">
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
            <div class="col-md-3">
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
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Add New Supplier</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSupplierForm" action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Supplier Name</label>
                            <input type="text" name="supplier_name" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
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

    <!-- ADD PRODUCT MODAL -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Add New Product</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="addProductForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="product_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Barcode</label>
                            <input type="text" name="barcode" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-select">
                                    <option value="">-- Select Brand --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Product Type</label>
                                <select name="product_type_id" class="form-select">
                                    <option value="">-- Select Product Type --</option>
                                                                    @foreach($product_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Type</label>
                                <select name="unit_type_id" class="form-select">
                                    <option value="">-- Select Unit Type --</option>
                                                                    @foreach($unit_types as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="tracking_type" value="none">
                        <input type="hidden" name="warranty_type" value="none">
                        <input type="hidden" name="status" value="active">
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="saveProductBtn">Save</button>
                </div>

            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }
        
        .branch-select {
            width: 100% !important;
        }
        
        .select2-container {
            width: 100% !important;
        }
    </style>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    console.log('Purchases create script loaded successfully!');
    
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOM loaded, initializing purchases...');

        const productsMeta = {};
        @foreach($products as $p)
            productsMeta["{{ $p->id }}"] = {
                category_type: "{{ $p->category?->category_type ?? 'non_electronic' }}",
                warranty_coverage_months: {{ (int) ($p->warranty_coverage_months ?? 0) }}
            };
        @endforeach

        function ymd(d) {
            return d.toISOString().slice(0, 10);
        }

        function parseYmd(value) {
            if (!value) return null;
            const parts = String(value).split('-');
            if (parts.length !== 3) return null;
            const y = parseInt(parts[0], 10);
            const m = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[2], 10);
            const dt = new Date(y, m, day);
            if (Number.isNaN(dt.getTime())) return null;
            return dt;
        }

        function addMonthsSafe(dateObj, months) {
            const d = new Date(dateObj.getTime());
            const day = d.getDate();
            d.setMonth(d.getMonth() + months);

            // Handle month overflow (e.g. Jan 31 + 1 month)
            if (d.getDate() < day) {
                d.setDate(0);
            }
            return d;
        }

        function computeWarrantyExpiry(purchaseDateStr, warrantyMonths) {
            const purchaseDate = parseYmd(purchaseDateStr);
            if (!purchaseDate) return '';
            const m = parseInt(String(warrantyMonths || '0'), 10);
            if (!m || m <= 0) return '';
            return ymd(addMonthsSafe(purchaseDate, m));
        }

        function getPurchaseDateValue() {
            const inp = document.querySelector('input[name="purchase_date"]');
            return inp ? inp.value : '';
        }

        function updateSerialWarrantyDefaultsForRow(row) {
            if (!row) return;
            const productId = row.querySelector('.product-select')?.value;
            const meta = productsMeta[String(productId)] || { warranty_coverage_months: 0 };
            const purchaseDateStr = getPurchaseDateValue();
            const computed = computeWarrantyExpiry(purchaseDateStr, meta.warranty_coverage_months);

            const host = row.querySelector('.electronics-panel-host');
            if (!host || host.dataset.loaded !== '1') return;

            const sameToggle = host.querySelector('.js-same-expiry-toggle');
            const sharedWrap = host.querySelector('.js-shared-expiry-wrap');
            const sharedInput = host.querySelector('.js-shared-expiry-input');
            const serialInputs = host.querySelectorAll('input[type="date"][name$="[warranty_expiry]"]');

            if (sharedInput && !sharedInput.value && computed) {
                sharedInput.value = computed;
            }

            if (sameToggle && sameToggle.checked) {
                if (sharedWrap) sharedWrap.classList.remove('d-none');
                const val = (sharedInput && sharedInput.value) ? sharedInput.value : computed;
                serialInputs.forEach(function(inp) {
                    inp.value = val || '';
                });
            } else {
                if (sharedWrap) sharedWrap.classList.add('d-none');
                serialInputs.forEach(function(inp) {
                    if (!inp.value && computed) {
                        inp.value = computed;
                    }
                });
            }
        }

        function normalizeCategoryType(value) {
            return String(value || '').trim().toLowerCase();
        }

        function isElectronicCategoryType(categoryType) {
            const ct = normalizeCategoryType(categoryType);
            return ct === 'electronic_without_serial' || ct === 'electronic_with_serial';
        }

        function requiresSerials(categoryType) {
            return normalizeCategoryType(categoryType) === 'electronic_with_serial';
        }

        function applyCategoryTypeUI(row, productId) {
            if (!row) return;

            const meta = productsMeta[String(productId)] || { category_type: 'non_electronic' };
            row.dataset.categoryType = normalizeCategoryType(meta.category_type || 'non_electronic');

            const serialsContainer = row.querySelector('.serials-container');
            if (!serialsContainer) return;

            if (isElectronicCategoryType(row.dataset.categoryType)) {
                serialsContainer.classList.remove('d-none');
                ensureElectronicsPanelLoaded(row);

                // Only show serial entry UI if category requires serials
                const host = serialsContainer.querySelector('.electronics-panel-host');
                if (host) {
                    const body = host.querySelector('.electronics-panel-body');
                    if (body) {
                        body.classList.toggle('d-none', !requiresSerials(row.dataset.categoryType));
                    }
                }
            } else {
                serialsContainer.classList.add('d-none');
                const host = serialsContainer.querySelector('.electronics-panel-host');
                if (host) {
                    host.innerHTML = '';
                    host.dataset.loaded = '0';
                }
            }
        }

        async function ensureElectronicsPanelLoaded(row) {
            const host = row.querySelector('.electronics-panel-host');
            if (!host) return;
            if (host.dataset.loaded === '1') return;

            const url = '{{ route('superadmin.purchases.electronics.panel') }}';
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            host.innerHTML = html;
            host.dataset.loaded = '1';

            // Wire events inside loaded panel
            const sameToggle = host.querySelector('.js-same-expiry-toggle');
            const sharedInput = host.querySelector('.js-shared-expiry-input');
            if (sameToggle) {
                sameToggle.addEventListener('change', function() {
                    updateSerialWarrantyDefaultsForRow(row);
                });
            }
            if (sharedInput) {
                sharedInput.addEventListener('input', function() {
                    updateSerialWarrantyDefaultsForRow(row);
                });
            }

            updateSerialWarrantyDefaultsForRow(row);
        }

        const container = document.getElementById('items-container');
        const template = document.getElementById('item-template');
        const addItemBtn = document.getElementById('add-item-btn');
        const productModal = new bootstrap.Modal(document.getElementById('addProductModal'));
        const supplierModal = new bootstrap.Modal(document.getElementById('addSupplierModal'));

        let lastSelect = null;
        let itemIndex = 0;

        // ----------------- PRODUCT SELECT2 & ADD NEW PRODUCT -----------------
        function initProductSelect(wrapper) {
            const select = $(wrapper).find('.product-select');

            select.select2({
                placeholder: '-- Select Product --',
                width: '100%'
            });

            // Load unit types whenever a product is selected
            select.on('change', function () {
                const row = $(this).closest('.item-row')[0];
                const productId = this.value;
                console.log('Product changed, loading units for product ID:', productId);
                if (row && productId) {
                    applyCategoryTypeUI(row, productId);
                    loadProductUnits(row, productId);
                }
            });

            select.on('select2:open', function () {
                lastSelect = select;

                setTimeout(() => {
                    const results = document.querySelector('.select2-results__options');
                    const search = document.querySelector('.select2-search__field');
                    const term = search ? search.value : '';

                    if (!results || document.querySelector('.select2-add-supplier')) return;

                    const li = document.createElement('li');
                    li.className = 'select2-results__option select2-add-supplier';
                    li.innerHTML = `➕ Add new supplier`;

                    li.addEventListener('mousedown', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        select.select2('close');
                        $('#addProductForm [name="product_name"]').val(term);
                        productModal.show();
                    });

                    results.appendChild(li);
                }, 0);
            });
        }

        function addItem(productData = null) {
            const node = template.content.cloneNode(true);

            // Update name attributes with current index
            node.querySelectorAll('[name^="items[]"]').forEach(el => {
                const name = el.getAttribute('name').replace('[]', `[${itemIndex}]`);
                el.setAttribute('name', name);
            });

            container.appendChild(node);
            const newRow = container.querySelector('.item-row:last-child');
            initProductSelect(newRow);

            if (productData) {
                $(newRow).find('.product-select').val(productData.id).trigger('change');
                $(newRow).find('input[name$="[primary_quantity]"]').val(productData.quantity);
                $(newRow).find('input[name$="[cost]"]').val(productData.cost);
            }

            itemIndex++;
            updateTotals();
        }

        addItemBtn.addEventListener('click', () => addItem());

        const purchaseDateInput = document.querySelector('input[name="purchase_date"]');
        if (purchaseDateInput) {
            purchaseDateInput.addEventListener('input', function() {
                document.querySelectorAll('.item-row').forEach(function(row) {
                    updateSerialWarrantyDefaultsForRow(row);
                });
            });
        }

        function addUnmatchedItem(productData = null) {
            const unmatchedTemplate = document.getElementById('unmatched-item-template');
            const node = unmatchedTemplate.content.cloneNode(true);

            node.querySelectorAll('[name^="items[]"]').forEach(el => {
                const name = el.getAttribute('name').replace('[]', `[${itemIndex}]`);
                el.setAttribute('name', name);
            });

            container.appendChild(node);
            const newRow = container.querySelector('.item-row:last-child');

            if (productData) {
                $(newRow).find('input[name$="[product_name]"]').val(productData.name);
                $(newRow).find('input[name$="[primary_quantity]"]').val(productData.quantity);
                $(newRow).find('input[name$="[cost]"]').val(productData.cost);
            }

            itemIndex++;
            updateTotals();
        }

        function loadProductUnits(row, productId) {
            const unitSelect = row.querySelector('.unit-type-select');

            if (!unitSelect) {
                console.warn('Missing unit controls for row, cannot load units');
                return;
            }

            unitSelect.innerHTML = '<option value="">-- Select Unit --</option>';

            if (!productId) {
                console.warn('No productId passed to loadProductUnits');
                return;
            }

            console.log('Requesting unit types for product ID:', productId);

            $.getJSON(`/superadmin/products/${productId}/unit-types`, function (response) {
                console.log('Unit types response for product', productId, response);
                const units = response.units || [];

                units.forEach(function (unit) {
                    const option = document.createElement('option');
                    option.value = unit.id;
                    option.textContent = unit.name;
                    unitSelect.appendChild(option);
                });

                // If only one unit, auto-select it as the primary unit and set multiplier
                if (units.length === 1) {
                    unitSelect.value = units[0].id;
                }
            }).fail(function () {
                // On failure, keep the select empty but leave the controls usable
            });
        }

        // --- SERIALS VISIBILITY ---
        container.addEventListener('change', function(e) {
            if (e.target.classList.contains('product-select')) {
                const row = e.target.closest('.item-row');
                const productId = e.target.value;

                applyCategoryTypeUI(row, productId);
                loadProductUnits(row, productId);
            }

            if (e.target.classList.contains('unit-type-select')) {
                updateTotals();
            }
        });

        function updateTotals() {
            let grandTotal = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const primaryInput = row.querySelector('input[name$="[primary_quantity]"]');
                const quantity = primaryInput ? (parseFloat(primaryInput.value) || 0) : 0;
                const cost = parseFloat(row.querySelector('.item-cost').value) || 0;
                grandTotal += quantity * cost;
            });
            document.getElementById('grand-total').value = grandTotal.toFixed(2);
        }

        container.addEventListener('input', function(e) {
            if (e.target.matches('input[name^="items["]') || e.target.matches('.item-cost')) {
                updateTotals();
            }
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-item-btn')) {
                e.target.closest('.item-row').remove();
                updateTotals();
            }

            // --- ADD / REMOVE SERIALS ---
            if (e.target.classList.contains('add-serial-btn')) {
                console.log('Add serial button clicked!');
                
                const itemRow = e.target.closest('.item-row');
                const serialsContainer = itemRow.querySelector('.serial-entries-container');
                const serialTemplate = document.getElementById('serial-entry-template');
                const itemIndex = Array.from(container.children).indexOf(itemRow);
                const serialIndex = serialsContainer.children.length;

                console.log('Creating serial entry for item:', itemIndex, 'serial:', serialIndex);

                const node = serialTemplate.content.cloneNode(true);

                node.querySelector('[name="serial_number"]').name = `items[${itemIndex}][serials][${serialIndex}][serial_number]`;
                const warrantyInput = node.querySelector('[name="warranty_expiry"]');
                warrantyInput.name = `items[${itemIndex}][serials][${serialIndex}][warranty_expiry]`;
                warrantyInput.value = '';

                serialsContainer.appendChild(node);

                updateSerialWarrantyDefaultsForRow(itemRow);
            }

            if (e.target.classList.contains('remove-serial-btn')) {
                e.target.closest('.serial-entry-row').remove();
            }

            if (e.target.classList.contains('toggle-electronics-panel-btn')) {
                const row = e.target.closest('.item-row');
                const body = row.querySelector('.electronics-panel-body');
                if (body) {
                    body.classList.toggle('d-none');
                    e.target.textContent = body.classList.contains('d-none') ? 'Show' : 'Hide';
                }
            }
        });

        document.querySelector('form[action="{{ route('superadmin.purchases.store') }}"]').addEventListener('submit', function (e) {
            let firstError = null;

            document.querySelectorAll('.item-row').forEach(row => {
                const categoryType = row.dataset.categoryType || 'non_electronic';
                if (!requiresSerials(categoryType)) return;

                const qtyInput = row.querySelector('input[name$="[primary_quantity]"]');
                const qty = qtyInput ? parseInt(qtyInput.value || '0', 10) : 0;

                const serialEntriesContainer = row.querySelector('.serial-entries-container');
                const serialCount = serialEntriesContainer ? serialEntriesContainer.querySelectorAll('.serial-entry-row').length : 0;

                if (qty > 0 && serialCount !== qty) {
                    if (!firstError) {
                        firstError = 'For Electronic (with serial) products, serial entries must match the quantity.';
                    }
                }
            });

            if (firstError) {
                e.preventDefault();
                alert(firstError);
            }
        });

        const $supplierSelect = $('.supplier-select');
        const $supplierForm = $('#addSupplierForm');
        const $supplierNameInput = $supplierForm.find('[name="supplier_name"]');
        const $saveSupplierBtn = $('#saveSupplierBtn');

        $supplierSelect.select2({ width: '100%' }).on('select2:open', function () {
            setTimeout(() => {
                const results = document.querySelector('.select2-results__options');
                if (!results || document.querySelector('.select2-add-supplier')) return;

                const search = document.querySelector('.select2-search__field');
                const term = search ? search.value : '';

                const li = document.createElement('li');
                li.className = 'select2-results__option select2-add-product';
                li.innerHTML = `➕ Add new supplier`;

                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $supplierSelect.select2('close');
                    $supplierNameInput.val(term);
                    supplierModal.show();
                });

                results.appendChild(li);
            }, 0);
        });

        $saveSupplierBtn.on('click', function () {
            const name = $supplierNameInput.val().trim();
            if (!name) {
                Swal.fire('Error', 'Supplier name is required.', 'error');
                return;
            }

            $.ajax({
                url: $supplierForm.attr('action'),
                method: 'POST',
                data: $supplierForm.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.success) {
                        const newOption = new Option(response.supplier.supplier_name, response.supplier.id, true, true);
                        $supplierSelect.append(newOption).trigger('change');
                        supplierModal.hide();
                        $supplierForm[0].reset();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Supplier saved successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        let errorMessages = 'An unknown error occurred.';
                        if (response.errors) {
                            errorMessages = Object.values(response.errors).map(e => e[0]).join('<br>');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            html: errorMessages,
                            didOpen: () => {
                                const supplierModal = document.getElementById('addSupplierModal');
                                if (supplierModal) {
                                    supplierModal.setAttribute('aria-hidden', 'true');
                                }
                            },
                            didClose: () => {
                                const supplierModal = document.getElementById('addSupplierModal');
                                if (supplierModal) {
                                    supplierModal.removeAttribute('aria-hidden');
                                }
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    let errorMessages = 'An unexpected error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        html: errorMessages,
                        didOpen: () => {
                            const supplierModal = document.getElementById('addSupplierModal');
                            if (supplierModal) {
                                supplierModal.setAttribute('aria-hidden', 'true');
                            }
                        },
                        didClose: () => {
                            const supplierModal = document.getElementById('addSupplierModal');
                            if (supplierModal) {
                                supplierModal.removeAttribute('aria-hidden');
                            }
                        }
                    });
                }
            });
        });

        $('#saveProductBtn').on('click', function () {
            const form = $('#addProductForm');
            $.ajax({
                url: '{{ route("superadmin.products.store") }}',
                method: 'POST',
                data: form.serialize(),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response.success) {
                        const newOption = new Option(response.product.product_name, response.product.id, true, true);
                        lastSelect.append(newOption).trigger('change');
                        productModal.hide();
                        form[0].reset();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Product saved successfully!',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        let errorMessages = 'An unknown error occurred.';
                        if (response.errors) {
                            errorMessages = Object.values(response.errors).map(e => e[0]).join('<br>');
                        }
                        Swal.fire({ icon: 'error', title: 'Oops...', html: errorMessages });
                    }
                },
                error: function (xhr) {
                    let errorMessages = 'An unexpected error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessages = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('<br>');
                    }
                    Swal.fire({ icon: 'error', title: 'Save Failed', html: errorMessages });
                }
            });
        });

        // ----------------- MODAL FOCUS FIX -----------------
        const addSupplierModalEl = document.getElementById('addSupplierModal');
        const addProductModalEl = document.getElementById('addProductModal');

        // When a new modal is shown, hide the one behind it
        $(addProductModalEl).on('show.bs.modal', function () {
            $(addSupplierModalEl).attr('aria-hidden', 'true');
        });

        // When the new modal is hidden, show the original one again
        $(addProductModalEl).on('hidden.bs.modal', function () {
            $(addSupplierModalEl).removeAttr('aria-hidden');
        });

        // ----------------- OCR FUNCTIONALITY -----------------
        const ocrButton = document.getElementById('ocr-button');
        const ocrFileInput = document.createElement('input');
        ocrFileInput.type = 'file';
        ocrFileInput.accept = 'image/*';
        ocrFileInput.multiple = true; // Allow multiple file selection

        ocrButton.addEventListener('click', () => ocrFileInput.click());

        ocrFileInput.addEventListener('change', function(event) {
            const files = Array.from(event.target.files);
            if (files.length === 0) return;

            // Show loading with proper animation
            Swal.fire({
                title: 'Processing Receipt Images',
                html: '<div style="display: flex; justify-content: center; margin: 20px 0;"><div style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3085d6; border-radius: 50%; animation: spin 1s linear infinite;"></div></div><p>Scanning ' + files.length + ' image(s)... Please wait.</p><style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>',
                allowOutsideClick: false,
                showConfirmButton: false
            });

            // Process all images
            const allTexts = [];
            let processedCount = 0;

            files.forEach((file, index) => {
                Tesseract.recognize(file, 'eng', { logger: m => console.log(`File ${index + 1}:`, m) })
                    .then(({ data: { text } }) => {
                        allTexts[index] = text;
                        processedCount++;
                        
                        // Update loading message
                        Swal.update({
                            html: '<div style="display: flex; justify-content: center; margin: 20px 0;"><div style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #3085d6; border-radius: 50%; animation: spin 1s linear infinite;"></div></div><p>Processed ' + processedCount + '/' + files.length + ' image(s)... Please wait.</p><style>@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>'
                        });

                        // When all images are processed
                        if (processedCount === files.length) {
                            // Combine all texts
                            const combinedText = allTexts.filter(text => text).join('\n');
                            
                            // Filter the combined text to show only product items and reference number
                            const lines = combinedText.split('\n');
                            const filteredLines = [];
                            let referenceNumber = null;
                            
                            lines.forEach(line => {
                                line = line.trim();
                                if (!line) return;
                                
                                // Check for reference number
                                if (/(?:REFERENCE NO|REF NO|REFERENCE):\s*([A-Z0-9-]+)/i.test(line)) {
                                    referenceNumber = line;
                                    filteredLines.push(line);
                                    return;
                                }
                                
                                // Less restrictive filtering - capture more potential products
                                // Skip only obvious non-product lines
                                if (/^(TOTAL|SUBTOTAL|CASH|CHANGE|VAT|DISCOUNT|PAYMENT|AMOUNT|QTY|PRICE|ITEM|DESCRIPTION|ORDER|INVOICE|RECEIPT|THANK YOU|SHOPPING AT|OFFICIAL RECEIPT|X|TAX|SERVICE|CHARGE|DRIVER|HELLO|GROSS|LESS)$/i.test(line)) {
                                    return;
                                }
                                
                                // Skip lines that are just dates
                                if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(line) || /^\d{1,2}:\d{2}\s*(AM|PM)$/.test(line)) {
                                    return;
                                }
                                
                                // Skip lines that are just numbers
                                if (/^\d+(\.\d{2})?$/.test(line)) {
                                    return;
                                }
                                
                                // Skip very short lines (likely OCR noise)
                                if (line.length < 3) {
                                    return;
                                }
                                
                                // Include more lines - be less restrictive
                                // Accept lines that contain letters and are reasonable length
                                if (/[a-zA-Z]/.test(line) && line.length >= 3 && line.length <= 100) {
                                    filteredLines.push(line);
                                }
                            });
                            
                            const filteredText = filteredLines.join('\n');

                            // Auto-process the combined full text through backend (no preview modal)
                            $.ajax({
                                url: '{{ route("superadmin.purchases.ocr-product-match") }}',
                                method: 'POST',
                                data: { _token: '{{ csrf_token() }}', text: combinedText },
                                success: function(response) {
                                    console.log('OCR Response:', response);
                                    Swal.close();
                                    let message = '';
                                    let icon = 'success';

                                    // Auto-fill reference number if found
                                    if (response.reference_number) {
                                        $('input[name="reference_number"]').val(response.reference_number);
                                        console.log('Reference number filled:', response.reference_number);
                                    }

                                    if (response.products && response.products.length > 0) {
                                        message = '<div style="text-align: left;"><strong>Found Products:</strong><ul>';
                                        response.products.forEach(function(product) {
                                            message += `<li>${product.name} (Qty: ${product.quantity}, Cost: ${product.cost})</li>`;
                                        });
                                        message += '</ul></div>';

                                        let newProductsCount = 0;
                                        let existingProductsCount = 0;
                                        
                                        response.products.forEach(function(product) {
                                            console.log('Adding product:', product);
                                            if (product.is_new) {
                                                newProductsCount++;
                                            } else {
                                                existingProductsCount++;
                                            }
                                            
                                            addItem({
                                                id: product.id,
                                                quantity: product.quantity,
                                                cost: product.cost
                                            });
                                        });
                                        
                                        if (existingProductsCount > 0) {
                                            if (message) message += '<br>';
                                            message += `<strong>${existingProductsCount} existing product(s) matched</strong>`;
                                        }
                                        
                                        if (newProductsCount > 0) {
                                            if (message) message += '<br>';
                                            message += `<strong>${newProductsCount} new product(s) created</strong>`;
                                        }
                                    }

                                    if (response.unmatched_products && response.unmatched_products.length > 0) {
                                        if (message) message += '<br>';
                                        message += '<div style="text-align: left;"><strong>New Products Added:</strong><ul>';
                                        response.unmatched_products.forEach(function(product) {
                                            message += `<li>${product.name} (Qty: ${product.quantity}, Cost: ${product.cost})</li>`;
                                        });
                                        message += '</ul></div>';

                                        // Add unmatched products as new items with OCR data
                                        response.unmatched_products.forEach(function(product) {
                                            console.log('Adding unmatched product:', product);
                                            addUnmatchedItem({
                                                name: product.name,
                                                quantity: product.quantity,
                                                cost: product.cost
                                            });
                                        });
                                    }

                                    if (!message) {
                                        message = 'Could not find any products from the scanned text.';
                                        icon = 'warning';
                                    } else {
                                        message += '<br><small>Products have been added to the purchase list below. You can edit their details before saving.</small>';
                                    }

                                    console.log('Filtered text (debug):', filteredText);
                                    console.log('Final message:', message);

                                    // Add a small delay to ensure items are added before showing the message
                                    setTimeout(() => {
                                        Swal.fire({
                                            title: 'OCR Scan Complete',
                                            html: message,
                                            icon: icon,
                                            showConfirmButton: true,
                                            confirmButtonText: 'OK'
                                        });
                                    }, 300);
                                },
                                error: function() {
                                    Swal.close();
                                    Swal.fire('Error', 'An error occurred while matching products.', 'error');
                                }
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        console.error('OCR Error for file', file.name, ':', err);
                        Swal.fire({ 
                            title: 'OCR Error', 
                            text: 'Could not recognize text from ' + file.name, 
                            icon: 'error' 
                        });
                    });
            });
        });

    });
    </script>
@endsection
