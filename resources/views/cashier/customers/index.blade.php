@extends('layouts.app')
@section('title', 'Customers')

@php
    $isCashierContext = request()->is('cashier/*');
@endphp

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--cyan:#00E5FF;
            --green:#10b981;--red:#ef4444;--amber:#f59e0b;
            --bg:#EBF3FB;--card:#ffffff;--border:rgba(25,118,210,0.12);
            --text:#1a2744;--muted:#6b84aa;
        }

        body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

        .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
        .sp-bg::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);}
        .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
        .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;animation:spb1 9s ease-in-out infinite;}
        .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;animation:spb2 11s ease-in-out infinite;}
        @keyframes spb1{0%,100%{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
        @keyframes spb2{0%,100%{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

        .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;}

        .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:14px;animation:spUp .4s ease both;}
        .sp-ph-left{display:flex;align-items:center;gap:13px;}
        .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
        .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
        .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
        .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}

        .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
        .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
        .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
        .sp-btn-good{background:linear-gradient(135deg,#059669,#10b981);color:#fff;box-shadow:0 4px 14px rgba(16,185,129,0.24);}
        .sp-btn-good:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(16,185,129,0.34);color:#fff;}

        .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;animation:spUp .55s ease both;}
        .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
        .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
        .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
        .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
        .sp-card-head-title i{color:rgba(0,229,255,.85);}
        .sp-card-body{padding:18px 22px;}

        .sp-table-wrap{overflow-x:auto;}
        .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
        .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 14px;font-size:10.5px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
        .sp-table tbody td{padding:12px 14px;font-size:13px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
        .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
        .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}

        .sp-table-scroll{max-height:520px;overflow-y:auto;overflow-x:hidden;}

        .sp-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.02em;font-family:'Nunito',sans-serif;}
        .sp-badge-warn{background:rgba(245,158,11,0.12);color:#b45309;border:1px solid rgba(245,158,11,0.22);}
        .sp-badge-good{background:rgba(16,185,129,0.12);color:#047857;border:1px solid rgba(16,185,129,0.22);}
        .sp-badge-bad{background:rgba(239,68,68,0.12);color:#b91c1c;border:1px solid rgba(239,68,68,0.22);}

        .sp-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .sp-mini-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:7px 10px;border-radius:10px;border:1.5px solid var(--border);background:#fff;color:var(--navy);font-size:12px;font-weight:800;font-family:'Nunito',sans-serif;text-decoration:none;transition:all .15s ease;}
        .sp-mini-btn:hover{transform:translateY(-1px);box-shadow:0 8px 16px rgba(13,71,161,0.10);}
        .sp-mini-primary{border-color:rgba(25,118,210,0.22);}
        .sp-mini-danger{border-color:rgba(239,68,68,0.25);color:#b91c1c;}

        .pagination{margin-bottom:0;}
        .page-link{border-radius:10px !important;border:1.5px solid var(--border) !important;color:var(--navy) !important;}
        .page-item.active .page-link{background:linear-gradient(135deg,var(--navy),var(--blue)) !important;border-color:transparent !important;color:#fff !important;}

        @keyframes spUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
    </style>
@endpush

@section('content')
<div class="d-flex min-vh-100">
    <div class="sp-bg"><div class="sp-blob sp-blob-1"></div><div class="sp-blob sp-blob-2"></div></div>
    <main class="flex-fill p-4">
        <div class="sp-wrap">

            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Cashier</div>
                        <div class="sp-ph-title">Customers</div>
                        <div class="sp-ph-sub">Manage customers and account status</div>
                    </div>
                </div>
                <div class="sp-actions">
                    <form method="GET" action="{{ route('cashier.customers.index') }}" class="d-flex" style="gap:8px;align-items:center;flex-wrap:wrap;">
                       
                    </form>
                    <a href="{{ route('cashier.customers.create') }}" class="sp-btn sp-btn-good"><i class="fas fa-plus"></i> Add Customer</a>
                </div>
            </div>

            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title"><i class="fas fa-list"></i> Customer List</div>
                </div>
                <div class="sp-card-body">
                    <div class="sp-table-wrap">
                        <div class="sp-table-scroll">
                            <table class="sp-table">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Customer Name</th>
                                        <th>Created At</th>
                                        <th>Created By</th>
                                        <th>Branch</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->id }}</td>
                                            <td>
                                                <strong>{{ $customer->full_name }}</strong>
                                                @if(($customer->outstanding_balance ?? 0) > 0)
                                                    <span class="sp-badge sp-badge-warn ms-1"><i class="fas fa-exclamation-circle"></i> Has Balance</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</td>
                                            <td>{{ $customer->user->name ?? 'N/A' }}</td>
                                            <td>{{ $branchName ?? 'Branch' }}</td>
                                            <td>
                                                @if($customer->status == 'active')
                                                    <span class="sp-badge sp-badge-good"><i class="fas fa-check-circle"></i> Active</span>
                                                @else
                                                    <span class="sp-badge sp-badge-bad"><i class="fas fa-ban"></i> {{ ucfirst($customer->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="sp-actions">
                                                    <a href="{{ route('cashier.customers.show', $customer->id) }}" class="sp-mini-btn sp-mini-primary" title="View Customer" style="text-decoration:none;">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('cashier.customers.edit', $customer->id) }}" class="sp-mini-btn" title="Edit Customer" style="text-decoration:none;">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="sp-mini-btn sp-mini-danger" onclick="deleteCustomer({{ $customer->id }})" title="Delete Customer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No customers found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($customers->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $customers->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </main>
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
