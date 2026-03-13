@extends('layouts.app')

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

    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

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

    /* ── Buttons ── */
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
        display:flex;align-items:center;gap:10px;margin-bottom:18px;margin-top:4px;
    }
    .sp-section-label::after { content:'';flex:1;height:1px;background:var(--border); }

    /* Form fields */
    .sp-form-group { margin-bottom:0; }
    .sp-label {
        display:block;font-size:11.5px;font-weight:700;
        color:var(--navy);letter-spacing:.05em;text-transform:uppercase;
        margin-bottom:7px;font-family:'Nunito',sans-serif;
    }
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
    .sp-textarea { resize:vertical;min-height:90px; }
    .sp-hint { font-size:11.5px;color:var(--muted);margin-top:5px; }

    /* Select2 override */
    .select2-container--default .select2-selection--single {
        border-radius:11px !important;
        border:1.5px solid var(--border) !important;
        height:44px !important;
        background:#fafcff !important;
        font-family:'Plus Jakarta Sans',sans-serif !important;
        font-size:13.5px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height:44px !important;
        color:var(--text) !important;
        padding-left:14px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height:42px !important;
    }
    .select2-container { width: 100% !important; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open  .select2-selection--single {
        border-color:var(--blue-lt) !important;
        box-shadow:0 0 0 3px rgba(66,165,245,0.12) !important;
    }
    .select2-dropdown {
        border-radius:12px !important;
        border:1.5px solid var(--border) !important;
        box-shadow:0 8px 28px rgba(13,71,161,0.12) !important;
        font-family:'Plus Jakarta Sans',sans-serif !important;
        font-size:13.5px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background:var(--blue) !important;
    }
    .select2-search--dropdown .select2-search__field {
        border-radius:8px !important;
        border:1.5px solid var(--border) !important;
        font-family:'Plus Jakarta Sans',sans-serif !important;
        font-size:13px !important;
    }

    /* ── Form footer ── */
    .sp-form-footer {
        padding:18px 26px;
        border-top:1px solid var(--border);
        background:rgba(13,71,161,0.02);
        display:flex;justify-content:flex-end;gap:10px;
    }
    .sp-btn-cancel {
        display:inline-flex;align-items:center;gap:7px;
        padding:10px 20px;border-radius:11px;cursor:pointer;
        font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;
        color:var(--muted);background:transparent;
        border:1.5px solid var(--border);
        transition:all .18s ease;
    }
    .sp-btn-cancel:hover { background:rgba(107,132,170,0.08);color:var(--text); }
    .sp-btn-submit {
        display:inline-flex;align-items:center;gap:8px;
        padding:10px 24px;border-radius:11px;cursor:pointer;
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
                    <div class="sp-ph-icon"><i class="fas fa-credit-card"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Finance / Credits</div>
                        <div class="sp-ph-title">Create New Credit</div>
                        <div class="sp-ph-sub">Fill in the details below to record a new credit entry</div>
                    </div>
                </div>
                <a href="{{ route('admin.credits.index') }}" class="sp-btn-outline-back">
                    <i class="fas fa-arrow-left"></i> Back to Credits
                </a>
            </div>

            {{-- ── Form card ── --}}
            <div class="sp-card">

                {{-- Card gradient header --}}
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-plus-circle"></i> New Credit Information
                    </div>
                    <div class="sp-card-head-sub">All fields marked with * are required</div>
                </div>

                {{-- Form --}}
                <form id="creditForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="sp-form-body">

                        {{-- Section: Assignment --}}
                        <div class="sp-section-label"><i class="fas fa-building" style="color:var(--blue);opacity:.7;font-size:11px;"></i> Assignment</div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="sp-form-group">
                                    <label for="branch_id" class="sp-label">Branch *</label>
                                    <select class="sp-select" name="branch_id" id="branch_id" required>
                                        <option value="">Select Branch</option>
                                        @if($userBranch)
                                            <option value="{{ $userBranch->id }}" selected>{{ $userBranch->branch_name }}</option>
                                        @endif
                                        @foreach(\App\Models\Branch::where('status', 'active')->get() as $branch)
                                            @if(!$userBranch || $branch->id != $userBranch->id)
                                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sp-form-group">
                                    <label for="customer_id" class="sp-label">Customer *</label>
                                    <select class="form-select" name="customer_id" id="customer_id" required style="display:none;">
                                        <option value="">Select Customer</option>
                                        @if($allCustomers->isNotEmpty())
                                            @foreach($allCustomers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section: Credit Details --}}
                        <div class="sp-section-label"><i class="fas fa-peso-sign" style="color:var(--blue);opacity:.7;font-size:11px;"></i> Credit Details</div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="sp-form-group">
                                    <label for="credit_amount" class="sp-label">Credit Amount (₱) *</label>
                                    <input type="number" class="sp-input" name="credit_amount" id="credit_amount" step="0.01" min="0" required placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sp-form-group">
                                    <label for="credit_type" class="sp-label">Credit Type *</label>
                                    <select class="sp-select" name="credit_type" id="credit_type" required>
                                        <option value="">Select Credit Type</option>
                                        <option value="cash">Cash</option>
                                        <option value="grocery">Grocery (Requires Sale ID)</option>
                                        <option value="electronics">Electronics (Requires Sale ID)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" id="sale_id_field" style="display: none;">
                                <div class="sp-form-group">
                                    <label for="sale_id" class="sp-label">Sale ID *</label>
                                    <input type="number" class="sp-input" name="sale_id" id="sale_id" placeholder="Enter Sale ID">
                                    <div class="sp-hint"><i class="fas fa-info-circle me-1"></i>Required for Grocery and Electronics credits</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="sp-form-group">
                                    <label for="date" class="sp-label">Credit Date *</label>
                                    <input type="date" class="sp-input" name="date" id="date" required>
                                </div>
                            </div>
                        </div>

                        {{-- Section: Notes --}}
                        <div class="sp-section-label"><i class="fas fa-sticky-note" style="color:var(--blue);opacity:.7;font-size:11px;"></i> Additional Notes</div>

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="sp-form-group">
                                    <label for="notes" class="sp-label">Notes <span style="font-size:10px;color:var(--muted);text-transform:none;font-weight:400;">(Optional)</span></label>
                                    <textarea class="sp-textarea" name="notes" id="notes" rows="3" placeholder="Add any additional notes about this credit..."></textarea>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end sp-form-body --}}

                    {{-- Footer buttons --}}
                    <div class="sp-form-footer">
                        <button type="button" class="sp-btn-cancel" onclick="history.back()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="sp-btn-submit" id="sp-submit-btn">
                            <i class="fas fa-check"></i> Create Credit
                        </button>
                    </div>

                </form>
            </div>{{-- end sp-card --}}

        </div>{{-- end sp-wrap --}}
    </main>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialize Select2 for customer dropdown
    $(document).ready(function() {
        $('#customer_id').select2({
            placeholder: "Search or add new customer...",
            allowClear: true,
            width: '100%',
            tags: true, // Allow adding new options
            createTag: function (params) {
                var term = $.trim(params.term);
                
                if (term === '') {
                    return null;
                }
                
                return {
                    id: 'new-' + term, // Use 'new-' prefix to identify new customers
                    text: term + ' (New Customer)',
                    newOption: true
                }
            }
        });

        $('#customer_id').on('select2:open', function () {
            const container = document.querySelector('#customer_id + .select2');
            const dropdown = document.querySelector('.select2-container--open .select2-dropdown');
            const input = document.querySelector('.select2-container--open .select2-search__field');
            if (!input || !container || !dropdown) {
                return;
            }

            input.setAttribute('type', 'text');
            input.removeAttribute('title');
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('inputmode', 'text');

            const w = Math.ceil(container.getBoundingClientRect().width);
            dropdown.style.width = `${w}px`;
        });
    });

    // Set credit date to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        document.getElementById('date').min = today.toISOString().split('T')[0];
        document.getElementById('date').value = today.toISOString().split('T')[0];
    });

    // Handle credit type change to show/hide sale_id field
    document.getElementById('credit_type').addEventListener('change', function() {
        const creditType = this.value;
        const saleIdField = document.getElementById('sale_id_field');
        const saleIdInput = document.getElementById('sale_id');
        
        if (creditType === 'grocery' || creditType === 'electronics') {
            saleIdField.style.display = 'block';
            saleIdInput.setAttribute('required', 'required');
        } else {
            saleIdField.style.display = 'none';
            saleIdInput.removeAttribute('required');
            saleIdInput.value = '';
        }
    });

    document.getElementById('creditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Creating Credit...',
            html: 'Please wait while we create the credit.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('/admin/credits', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/admin/credits';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'An error occurred while creating the credit.',
                    confirmButtonColor: '#2563eb'
                });
                
                // Show validation errors if any
                if (data.errors) {
                    let errorMessages = '';
                    for (let field in data.errors) {
                        errorMessages += data.errors[field].join('\n') + '\n';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: errorMessages,
                        confirmButtonColor: '#2563eb'
                    });
                }
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while creating the credit.',
                confirmButtonColor: '#2563eb'
            });
        });
    });
</script>
@endsection