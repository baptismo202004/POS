@extends('layouts.app')
@section('title', 'Add Customer')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Use CashierSidebar */
        .main-content {
            margin-left: 280px !important;
        }

        :root {
            --navy: #0D47A1;
            --blue: #1976D2;
            --blue-lt: #42A5F5;
            --bg: #f0f6ff;
            --card: #ffffff;
            --border: rgba(25,118,210,0.13);
            --text: #1a2744;
            --muted: #6b84aa;
            --red: #ef4444;
            --green: #10b981;
            --shadow: 0 4px 28px rgba(13,71,161,0.09);
        }

        .customers-theme {
            position: relative;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }
        .customers-theme .bg-layer {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .customers-theme .bg-layer::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 0% 0%, rgba(13,71,161,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 100% 100%, rgba(0,176,255,0.08) 0%, transparent 55%);
        }
        .customers-theme .bg-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: .11;
            pointer-events: none;
        }
        .customers-theme .bb1 { width:420px; height:420px; background: var(--blue); top:-130px; left:-130px; animation: bf1 9s ease-in-out infinite; }
        .customers-theme .bb2 { width:300px; height:300px; background:#00B0FF; bottom:-90px; right:-90px; animation: bf2 11s ease-in-out infinite; }
        @keyframes bf1 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(28px,18px)} }
        @keyframes bf2 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(-20px,-22px)} }

        .wrap {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 24px 56px;
        }

        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .ph-left { display: flex; align-items: center; gap: 13px; }
        .ph-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,71,161,0.28);
        }
        .ph-crumb {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--blue);
            opacity: .7;
            margin-bottom: 3px;
        }
        .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .ph-sub   { font-size:12px; color:var(--muted); margin-top:2px; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 11px;
            border: 1.5px solid var(--border);
            background: var(--card);
            color: var(--navy);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s ease;
            font-family:'Nunito',sans-serif;
        }
        .btn-back:hover { background: var(--navy); color: #fff; border-color: var(--navy); transform: translateX(-3px); }

        .main-card {
            background: var(--card);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .c-head {
            padding: 16px 26px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        .c-head::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 120% at 88% 50%, rgba(0,229,255,0.15), transparent);
            pointer-events: none;
        }
        .c-head::after {
            content: '';
            position: absolute;
            width:220px;
            height:220px;
            border-radius:50%;
            background: rgba(255,255,255,0.05);
            top:-90px;
            right:-50px;
            pointer-events:none;
        }
        .c-head-title {
            font-family:'Nunito',sans-serif;
            font-size:15px;
            font-weight:800;
            color:#fff;
            display:flex;
            align-items:center;
            gap:8px;
            position:relative;
            z-index:1;
        }
        .c-head-title i { color:rgba(0,229,255,.85); }

        .form-body { padding: 28px 26px; }

        .f-row { display: flex; flex-wrap: wrap; margin: 0 -8px; }
        .f-col-6  { width: 50%;  padding: 0 8px; margin-bottom: 20px; }
        .f-col-12 { width: 100%; padding: 0 8px; margin-bottom: 20px; }
        @media(max-width:640px) { .f-col-6 { width:100%; } }

        .f-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            margin-bottom: 6px;
            letter-spacing: .01em;
        }
        .f-label .req { color: var(--red); margin-left: 2px; }

        .f-input, .f-select, .f-textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: 11px;
            border: 1.5px solid var(--border);
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff;
            color: var(--text);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .f-input:focus, .f-select:focus, .f-textarea:focus {
            border-color: var(--blue-lt);
            box-shadow: 0 0 0 3px rgba(66,165,245,0.12);
        }
        .f-input::placeholder, .f-textarea::placeholder { color: #b0c0d8; }
        .f-textarea { resize: vertical; min-height: 90px; }

        .f-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b84aa' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 13px center;
            padding-right: 36px;
        }

        .input-group { display: flex; }
        .input-prefix {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 14px;
            background: rgba(13,71,161,0.07);
            border: 1.5px solid var(--border);
            border-right: none;
            border-radius: 11px 0 0 11px;
            font-weight: 800;
            color: var(--navy);
            font-family:'Nunito',sans-serif;
            font-size: 14px;
        }
        .input-group .f-input { border-radius: 0 11px 11px 0; }

        .f-error {
            font-size: 11.5px;
            color: var(--red);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .f-error i { font-size: 10px; }

        .form-foot {
            padding: 18px 26px;
            background: rgba(13,71,161,0.03);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 26px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            font-family:'Nunito',sans-serif;
            box-shadow: 0 4px 16px rgba(13,71,161,0.28);
            transition: all .22s cubic-bezier(.34,1.56,.64,1);
        }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(13,71,161,0.36); color:#fff; }
        .btn-save:active { transform: scale(.97); }
        .btn-save:disabled { opacity: .6; cursor: not-allowed; transform: none; }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 12px;
            background: transparent;
            color: var(--muted);
            border: 1.5px solid var(--border);
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            transition: all .2s ease;
            text-decoration: none;
        }
        .btn-cancel:hover { border-color: var(--red); color: var(--red); background: rgba(239,68,68,0.05); }
    </style>
@endpush

@section('content')
<div class="customers-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="wrap">
        <!-- Header -->
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-user-plus"></i></div>
                <div>
                    <div class="ph-crumb">Customers › Add New</div>
                    <div class="ph-title">Add Customer</div>
                    <div class="ph-sub">Create a new customer account</div>
                </div>
            </div>
            <a href="{{ route('cashier.customers.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Customers</a>
        </div>

        <!-- Customer Form -->
        <div class="main-card">
            <div class="c-head">
                <div class="c-head-title"><i class="fas fa-user-circle"></i> Customer Information</div>
            </div>

            <form id="customerForm" method="POST" action="{{ route('cashier.customers.store') }}">
                @csrf
                <div class="form-body">
                    <div class="f-row">
                        <div class="f-col-6">
                            <label for="full_name" class="f-label">Full Name <span class="req">*</span></label>
                            <input type="text" class="f-input" id="full_name" name="full_name" required placeholder="Enter full name">
                            @error('full_name')
                                <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="f-col-6">
                            <label for="phone" class="f-label">Phone Number</label>
                            <input type="tel" class="f-input" id="phone" name="phone" placeholder="e.g. 09171234567">
                            @error('phone')
                                <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="f-row">
                        <div class="f-col-6">
                            <label for="email" class="f-label">Email Address</label>
                            <input type="email" class="f-input" id="email" name="email" placeholder="e.g. juan@email.com">
                            @error('email')
                                <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="f-col-6">
                            <label for="max_credit_limit" class="f-label">Credit Limit <span class="req">*</span></label>
                            <div class="input-group">
                                <span class="input-prefix">₱</span>
                                <input type="number" class="f-input" id="max_credit_limit" name="max_credit_limit" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            @error('max_credit_limit')
                                <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="f-row">
                        <div class="f-col-12">
                            <label for="address" class="f-label">Address</label>
                            <textarea class="f-textarea" id="address" name="address" rows="3" placeholder="Enter full address..."></textarea>
                            @error('address')
                                <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="f-row">
                        <div class="f-col-6">
                            <label for="status" class="f-label">Status <span class="req">*</span></label>
                            <select class="f-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="blocked">Blocked</option>
                            </select>
                            @error('status')
                                <div class="f-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-foot">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Customer
                    </button>
                    <a href="{{ route('cashier.customers.index') }}" class="btn-cancel"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    fetch('{{ route('cashier.customers.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28a745'
            }).then(() => {
                window.location.href = '{{ route('cashier.customers.index') }}';
            });
        } else {
            // Handle validation errors
            if (data.errors) {
                let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
                for (let field in data.errors) {
                    data.errors[field].forEach(error => {
                        errorHtml += `<li>${error}</li>`;
                    });
                }
                errorHtml += '</ul></div>';
                
                // Insert errors at the top of the form
                const form = document.getElementById('customerForm');
                form.insertAdjacentHTML('afterbegin', errorHtml);
                
                // Scroll to top
                window.scrollTo(0, 0);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to create customer.',
                    confirmButtonColor: '#dc3545'
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while creating the customer.',
            confirmButtonColor: '#dc3545'
        });
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endpush
