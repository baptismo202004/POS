@extends('layouts.app')
@section('title', 'Customers')

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
            --green: #10b981;
            --red: #ef4444;
            --amber: #f59e0b;
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

        .customers-theme h2 { font-family: 'Nunito', sans-serif; font-weight: 900; letter-spacing: .2px; }
        .customers-theme .text-muted { color: var(--muted) !important; }

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
        .ph-title { font-family:'Nunito',sans-serif; font-size:24px; font-weight:900; color:var(--navy); }
        .ph-sub   { font-size:12px; color:var(--muted); margin-top:2px; }

        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 13.5px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(13,71,161,0.28);
            transition: all .22s cubic-bezier(.34,1.56,.64,1);
        }
        .btn-add:hover { transform: translateY(-2px); box-shadow: 0 8px 22px rgba(13,71,161,0.36); color:#fff; }

        .filter-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 3px 16px rgba(13,71,161,0.07);
            padding: 18px 22px;
            margin-bottom: 20px;
        }
        .filter-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .search-wrap { position: relative; flex: 1; min-width: 200px; max-width: 340px; }
        .search-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 13px;
            pointer-events: none;
        }
        .search-input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border-radius: 11px;
            border: 1.5px solid var(--border);
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff;
            color: var(--text);
            outline: none;
            transition: border-color .18s, box-shadow .18s;
        }
        .search-input:focus { border-color: var(--blue-lt); box-shadow: 0 0 0 3px rgba(66,165,245,0.12); }
        .search-input::placeholder { color: #b0c0d8; }

        .btn-search {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--navy), var(--blue));
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            box-shadow: 0 3px 12px rgba(13,71,161,0.22);
            transition: all .2s ease;
        }
        .btn-search:hover { transform: translateY(-1px); color:#fff; }

        .btn-clear {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 18px;
            border-radius: 11px;
            background: transparent;
            color: var(--muted);
            border: 1.5px solid var(--border);
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            text-decoration: none;
            transition: all .2s ease;
        }
        .btn-clear:hover { border-color: var(--red); color: var(--red); background: rgba(239,68,68,0.05); }

        .customer-card {
            background: var(--card);
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 3px 18px rgba(13,71,161,0.07);
            padding: 20px 22px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            transition: all .25s ease;
        }
        .customer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 36px rgba(13,71,161,0.13);
            border-color: rgba(25,118,210,0.28);
        }
        .cust-left { display: flex; align-items: center; gap: 16px; flex: 1; min-width: 0; }
        .cust-avatar {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--navy), var(--blue-lt));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 900;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(13,71,161,0.22);
        }
        .cust-info { min-width: 0; }
        .cust-name {
            font-family: 'Nunito', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 4px;
        }
        .cust-meta {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            font-size: 12px;
            color: var(--muted);
        }
        .cust-meta span { display: inline-flex; align-items: center; gap: 4px; }
        .cust-meta i { font-size: 11px; color: var(--blue-lt); }
        .cust-address {
            font-size: 12px;
            color: var(--muted);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .cust-address i { font-size: 11px; color: var(--blue-lt); }
        .cust-badges { display: flex; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap; }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
        }
        .b-active  { background:rgba(16,185,129,0.11); color:#047857; border:1px solid rgba(16,185,129,0.22); }
        .b-blocked { background:rgba(239,68,68,0.10);  color:#b91c1c; border:1px solid rgba(239,68,68,0.18); }

        .credit-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family:'Nunito',sans-serif;
            background: rgba(13,71,161,0.07);
            color: var(--navy);
            border: 1px solid rgba(13,71,161,0.14);
        }
        .credit-pill i { font-size: 10px; color: var(--blue-lt); }

        .cust-actions { display: flex; align-items: center; gap: 7px; flex-shrink: 0; }
        .act-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            border: 1.5px solid;
            transition: all .18s ease;
            text-decoration: none;
            background: transparent;
        }
        .act-view   { color: var(--navy);  border-color: rgba(13,71,161,0.22); }
        .act-view:hover   { background: var(--navy);  color: #fff; border-color: var(--navy);  transform: scale(1.12); }
        .act-edit   { color: #b45309; border-color: rgba(245,158,11,0.30); }
        .act-edit:hover   { background: var(--amber); color: #fff; border-color: var(--amber); transform: scale(1.12); }
        .act-delete { color: #b91c1c; border-color: rgba(239,68,68,0.25); }
        .act-delete:hover { background: var(--red);   color: #fff; border-color: var(--red);   transform: scale(1.12); }

        .empty-state { text-align: center; padding: 60px 24px; }
        .empty-ico {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: rgba(13,71,161,0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: var(--blue-lt);
            margin: 0 auto 18px;
        }
        .empty-state h4 { font-family:'Nunito',sans-serif; font-size:18px; font-weight:800; color:var(--navy); margin-bottom:8px; }
        .empty-state p  { font-size:13px; color:var(--muted); }

        .pag-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .pag-info { font-size: 12px; color: var(--muted); }
        .pag-bar .pagination { margin-bottom: 0; }
        .pag-bar .page-link {
            border-radius: 8px !important;
            border: 1.5px solid var(--border) !important;
            color: var(--navy) !important;
            font-weight: 700;
        }
        .pag-bar .page-item.active .page-link {
            background: var(--navy) !important;
            border-color: var(--navy) !important;
            color: #fff !important;
        }
        .pag-bar .page-link:hover {
            background: rgba(13,71,161,0.08);
        }
    </style>
@endpush

@section('content')
<div class="customers-theme">
    <div class="bg-layer">
        <div class="bg-blob bb1"></div>
        <div class="bg-blob bb2"></div>
    </div>

    <div class="p-3 p-lg-4" style="position: relative; z-index: 1;">
        <div class="container-fluid">
        <div class="page-head">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-users"></i></div>
                <div>
                    <div class="ph-title">Customers</div>
                    <div class="ph-sub">Manage customer accounts and credit limits</div>
                </div>
            </div>
            <a href="{{ route('cashier.customers.create') }}" class="btn-add">
                <i class="fas fa-user-plus"></i> Add Customer
            </a>
        </div>

        <div class="filter-card">
            <form method="GET" action="{{ route('cashier.customers.index') }}" class="filter-row">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search customers..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="{{ route('cashier.customers.index') }}" class="btn-clear">
                    <i class="fas fa-times"></i> Clear
                </a>
            </form>
        </div>

        <!-- Customers List -->
        @forelse($customers as $customer)
            <div class="customer-card">
                <div class="cust-left">
                    <div class="cust-avatar">{{ strtoupper(substr($customer->full_name, 0, 1)) }}</div>
                    <div class="cust-info">
                        <div class="cust-name">{{ $customer->full_name }}</div>
                        <div class="cust-meta">
                            @if($customer->phone)
                                <span><i class="fas fa-phone"></i> {{ $customer->phone }}</span>
                            @endif
                            @if($customer->email)
                                <span><i class="fas fa-envelope"></i> {{ $customer->email }}</span>
                            @endif
                        </div>
                        @if($customer->address)
                            <div class="cust-address"><i class="fas fa-map-marker-alt"></i> {{ $customer->address }}</div>
                        @endif
                        <div class="cust-badges">
                            @if($customer->status == 'active')
                                <span class="badge b-active"><i class="fas fa-circle" style="font-size:7px;"></i> Active</span>
                            @else
                                <span class="badge b-blocked"><i class="fas fa-ban" style="font-size:9px;"></i> Blocked</span>
                            @endif
                            <span class="credit-pill"><i class="fas fa-credit-card"></i> Credit Limit: ₱{{ number_format($customer->max_credit_limit, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="cust-actions">
                    <a href="{{ route('cashier.customers.show', $customer->id) }}" class="act-btn act-view"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('cashier.customers.edit', $customer->id) }}" class="act-btn act-edit"><i class="fas fa-edit"></i></a>
                    <button type="button" class="act-btn act-delete" onclick="deleteCustomer({{ $customer->id }})"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-ico"><i class="fas fa-users"></i></div>
                <h4>No customers found</h4>
                <p>Get started by adding your first customer.</p>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="pag-bar">
                <div class="pag-info">Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers</div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteCustomer(customerId) {
    Swal.fire({
        title: 'Delete Customer?',
        text: 'Are you sure you want to delete this customer? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cashier/customers/${customerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Customer has been deleted successfully.',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to delete customer.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the customer.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}
</script>
@endpush
