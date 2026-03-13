@extends('layouts.app')
@section('title', 'Create Expense')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
    .sp-page-head { display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both; }
    .sp-ph-left { display:flex;align-items:center;gap:13px; }
    .sp-ph-icon { width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28); }
    .sp-ph-crumb { font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif; }
    .sp-ph-title { font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1; }
    .sp-ph-sub   { font-size:12px;color:var(--muted);margin-top:2px; }

    /* ── Back button ── */
    .sp-btn-outline-back {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 18px;border-radius:11px;cursor:pointer;
        font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;
        color:var(--navy);background:var(--card);
        border:1.5px solid var(--border);text-decoration:none;
        transition:all .2s ease;
    }
    .sp-btn-outline-back:hover { background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px); }

    /* ── Card ── */
    .sp-card { background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .45s ease both; }

    /* ── Card gradient header ── */
    .sp-card-head { padding:18px 26px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);position:relative;overflow:hidden; }
    .sp-card-head::before { content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 88% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none; }
    .sp-card-head::after  { content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none; }
    .sp-card-head-title { font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;color:#fff;display:flex;align-items:center;gap:9px;position:relative;z-index:1; }
    .sp-card-head-title i { color:rgba(0,229,255,.85); }
    .sp-card-head-sub { font-size:12px;color:rgba(255,255,255,0.65);margin-top:4px;position:relative;z-index:1;font-family:'Plus Jakarta Sans',sans-serif; }

    /* ── Form body ── */
    .sp-form-body { padding:28px 26px; }

    /* Section divider */
    .sp-section-label {
        font-size:10px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;
        color:var(--blue);font-family:'Nunito',sans-serif;
        display:flex;align-items:center;gap:10px;
        margin-bottom:18px;margin-top:4px;
    }
    .sp-section-label::after { content:'';flex:1;height:1px;background:var(--border); }

    /* Form labels */
    .sp-label {
        display:block;font-size:11.5px;font-weight:700;
        color:var(--navy);letter-spacing:.05em;text-transform:uppercase;
        margin-bottom:7px;font-family:'Nunito',sans-serif;
    }
    .sp-label .req { color:var(--red);margin-left:2px; }

    /* Inputs / Selects / Textarea */
    .sp-input, .sp-select, .sp-textarea {
        width:100%;border-radius:11px;
        border:1.5px solid var(--border);
        padding:10px 14px;font-size:13.5px;color:var(--text);
        background:#fafcff;font-family:'Plus Jakarta Sans',sans-serif;
        transition:border-color .18s,box-shadow .18s;
        outline:none;box-shadow:none;
        -webkit-appearance:none;appearance:none;
    }
    .sp-input:focus, .sp-select:focus, .sp-textarea:focus {
        border-color:var(--blue-lt);
        box-shadow:0 0 0 3px rgba(66,165,245,0.12);
        background:#fff;
    }
    .sp-textarea { resize:vertical;min-height:92px; }
    .sp-hint { font-size:11.5px;color:var(--muted);margin-top:5px; }

    /* File input */
    .sp-file-wrap {
        border:2px dashed rgba(25,118,210,0.20);
        border-radius:13px;padding:22px 18px;
        background:rgba(235,243,251,0.6);
        display:flex;align-items:center;gap:14px;
        transition:border-color .18s,background .18s;cursor:pointer;
    }
    .sp-file-wrap:hover { border-color:var(--blue-lt);background:rgba(66,165,245,0.05); }
    .sp-file-wrap input[type="file"] { position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%; }
    .sp-file-icon { width:44px;height:44px;border-radius:12px;background:rgba(13,71,161,0.10);display:flex;align-items:center;justify-content:center;color:var(--navy);font-size:18px;flex-shrink:0; }
    .sp-file-text-main { font-size:13px;font-weight:700;color:var(--navy);font-family:'Nunito',sans-serif; }
    .sp-file-text-sub  { font-size:11.5px;color:var(--muted);margin-top:2px; }

    /* Select2 override */
    .select2-container { width:100% !important; }
    .select2-container--default .select2-selection--single {
        border-radius:11px !important;border:1.5px solid var(--border) !important;
        height:44px !important;background:#fafcff !important;
        font-family:'Plus Jakarta Sans',sans-serif !important;font-size:13.5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height:44px !important;color:var(--text) !important;padding-left:14px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height:42px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single {
        border-color:var(--blue-lt) !important;box-shadow:0 0 0 3px rgba(66,165,245,0.12) !important;
    }
    .select2-dropdown { border-radius:12px !important;border:1.5px solid var(--border) !important;box-shadow:0 8px 28px rgba(13,71,161,0.12) !important;font-family:'Plus Jakarta Sans',sans-serif !important;font-size:13.5px !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:var(--blue) !important; }
    .select2-search--dropdown .select2-search__field { border-radius:8px !important;border:1.5px solid var(--border) !important;font-family:'Plus Jakarta Sans',sans-serif !important;font-size:13px !important; }

    /* Column divider */
    .sp-col-divider { border:none;border-left:1px solid var(--border);margin:0; }

    /* ── Form footer ── */
    .sp-form-footer {
        padding:18px 26px;
        border-top:1px solid var(--border);
        background:rgba(13,71,161,0.02);
        display:flex;justify-content:flex-end;
    }
    .sp-btn-submit {
        display:inline-flex;align-items:center;gap:8px;
        padding:10px 26px;border-radius:11px;cursor:pointer;
        font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;
        color:#fff;border:none;
        background:linear-gradient(135deg,var(--navy),var(--blue));
        box-shadow:0 4px 14px rgba(13,71,161,0.26);
        transition:all .2s ease;position:relative;overflow:hidden;
    }
    .sp-btn-submit::before {
        content:'';position:absolute;inset:0;
        background:linear-gradient(135deg,transparent 0%,rgba(255,255,255,0.08) 50%,transparent 100%);
        transform:translateX(-100%);transition:transform .4s ease;
    }
    .sp-btn-submit:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36); }
    .sp-btn-submit:hover::before { transform:translateX(100%); }

    @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
@endpush

@section('content')
<div class="d-flex min-vh-100" style="background:var(--bg);">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>

    <main class="flex-fill p-4" style="position:relative;z-index:1;">
        <div class="sp-wrap">

            {{-- ── Page header ── --}}
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Finance</div>
                        <div class="sp-ph-title">Add New Expense</div>
                        <div class="sp-ph-sub">Record operational expenses and attach receipts</div>
                    </div>
                </div>
                <a href="{{ route('admin.expenses.index') }}" class="sp-btn-outline-back">
                    <i class="fas fa-arrow-left"></i> Back to Expenses
                </a>
            </div>

            {{-- ── Form card ── --}}
            <div class="sp-card">

                {{-- Gradient header --}}
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-plus-circle"></i> Expense Form
                    </div>
                    <div class="sp-card-head-sub">All fields marked with * are required</div>
                </div>

                {{-- Form --}}
                <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="sp-form-body">

                        <div class="row g-4">

                            {{-- ── Col A: Expense Information ── --}}
                            <div class="col-md-6">
                                <div class="sp-section-label">
                                    <i class="fas fa-file-invoice-dollar" style="font-size:11px;opacity:.7;"></i>
                                    Expense Information
                                </div>

                                <div class="mb-3">
                                    <label for="expense_category_id" class="sp-label">Expense Category <span class="req">*</span></label>
                                    <select class="sp-select" name="expense_category_id" id="expense_category_id" required>
                                        <option value="" disabled selected>Select a category...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="expense_date" class="sp-label">Expense Date <span class="req">*</span></label>
                                    <input type="date" class="sp-input" name="expense_date" id="expense_date" value="{{ now()->format('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="sp-label">Amount <span class="req">*</span></label>
                                    <input type="number" class="sp-input" name="amount" id="amount" placeholder="0.00" step="0.01" required>
                                </div>

                                <div class="mb-0">
                                    <label for="payment_method" class="sp-label">Payment Method <span class="req">*</span></label>
                                    <select class="sp-select" name="payment_method" id="payment_method" required>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                        <option value="GCash">GCash</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                            </div>

                            {{-- ── Col B: Additional Details ── --}}
                            <div class="col-md-6">
                                <div class="sp-section-label">
                                    <i class="fas fa-clipboard-list" style="font-size:11px;opacity:.7;"></i>
                                    Additional Details
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="sp-label">Description</label>
                                    <textarea class="sp-textarea" name="description" id="description" rows="3" placeholder="Enter a short description..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="supplier_name" class="sp-label">Supplier</label>
                                    <select class="sp-select" name="supplier_name" id="supplier_name">
                                        <option value="">Select a supplier or type new name (optional)</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->supplier_name }}">{{ $supplier->supplier_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="sp-hint"><i class="fas fa-info-circle me-1"></i>Select existing supplier or type to create new one</div>
                                </div>

                                <div class="mb-0">
                                    <label for="reference_number" class="sp-label">Reference Number</label>
                                    <input type="text" class="sp-input" name="reference_number" id="reference_number" placeholder="e.g., Invoice #, OR #">
                                </div>
                            </div>

                        </div>{{-- end row --}}

                        {{-- ── Receipt Attachment ── --}}
                        <div class="sp-section-label mt-4">
                            <i class="fas fa-paperclip" style="font-size:11px;opacity:.7;"></i>
                            Receipt Attachment
                        </div>

                        <div class="mb-0">
                            <label for="receipt" class="sp-label">Upload Receipt <span style="font-size:10px;color:var(--muted);text-transform:none;font-weight:400;">(Image or PDF)</span></label>
                            <div class="sp-file-wrap position-relative">
                                <input class="sp-input" type="file" name="receipt" id="receipt" accept="image/*,.pdf" style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;z-index:2;">
                                <div class="sp-file-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <div>
                                    <div class="sp-file-text-main">Click to upload or drag & drop</div>
                                    <div class="sp-file-text-sub">Supports: JPG, PNG, PDF — max 10MB</div>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end sp-form-body --}}

                    {{-- Footer buttons --}}
                    <div class="sp-form-footer">
                        <button type="submit" class="sp-btn-submit">
                            <i class="fas fa-save"></i> Save Expense
                        </button>
                    </div>

                </form>
            </div>{{-- end sp-card --}}

        </div>{{-- end sp-wrap --}}
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#expense_category_id').select2();
    $('#supplier_name').select2({
        tags: true,
        createTag: function (params) {
            // Don't create empty tags
            if ($.trim(params.term) === '') {
                return null;
            }
            return {
                id: params.term,
                text: params.term,
                newOption: true
            };
        }
    });

    // Auto-select payment method based on amount
    $('#amount').on('input change keyup paste', function() {
        const amount = parseFloat($(this).val()) || 0;
        const paymentMethod = $('#payment_method');

        console.log('Amount changed:', amount); // Debug log

        // Logic for automatic payment method selection
        if (amount <= 1000) {
            paymentMethod.val('Cash');
            console.log('Selected: Cash');
        } else if (amount <= 10000) {
            paymentMethod.val('GCash');
            console.log('Selected: GCash');
        } else if (amount <= 50000) {
            paymentMethod.val('Bank');
            console.log('Selected: Bank');
        } else {
            paymentMethod.val('Bank');
            console.log('Selected: Bank (large amount)');
        }

        // Add visual feedback
        paymentMethod.addClass('border-success');
        setTimeout(() => {
            paymentMethod.removeClass('border-success');
        }, 1000);
    });

    // Trigger once on page load in case there's a default value
    $('#amount').trigger('input');

    // File input label update
    $('#receipt').on('change', function() {
        const file = this.files[0];
        if (file) {
            $(this).closest('.sp-file-wrap').find('.sp-file-text-main').text(file.name);
            $(this).closest('.sp-file-wrap').find('.sp-file-text-sub').text((file.size / 1024).toFixed(1) + ' KB');
        }
    });
});
</script>
@endsection