@extends('layouts.app')

@section('content')
@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--bg:#EBF3FB;--card:#fff;
        --border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;
    }
    .sp-page{background:var(--bg);font-family:'Plus Jakarta Sans',sans-serif;color:var(--text);}
    .sp-bg{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden;background:var(--bg);}
    .sp-bg::before{content:'';position:absolute;inset:0;background:
        radial-gradient(ellipse 60% 50% at 0% 0%,rgba(13,71,161,0.09) 0%,transparent 60%),
        radial-gradient(ellipse 50% 40% at 100% 100%,rgba(0,176,255,0.07) 0%,transparent 55%);
    }
    .sp-blob{position:absolute;border-radius:50%;filter:blur(60px);opacity:.11;}
    .sp-blob-1{width:420px;height:420px;background:#1976D2;top:-130px;left:-130px;}
    .sp-blob-2{width:300px;height:300px;background:#00B0FF;bottom:-90px;right:-90px;}
    .sp-wrap{position:relative;z-index:1;padding:18px 24px 44px;}
    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:12px;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
    .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}
    .sp-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;font-size:13px;font-weight:700;cursor:pointer;font-family:'Nunito',sans-serif;border:none;transition:all .2s ease;text-decoration:none;white-space:nowrap;}
    .sp-btn-primary{background:linear-gradient(135deg,var(--navy),var(--blue));color:#fff;box-shadow:0 4px 14px rgba(13,71,161,0.26);}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;}
    .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;}
    .sp-card-body{padding:18px 22px;}
    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
    .sp-table thead th{background:linear-gradient(135deg, rgba(13,71,161,0.92), rgba(25,118,210,0.92));padding:11px 16px;font-size:11px;font-weight:800;color:#fff;letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:13px 16px;font-size:13.5px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
</style>
@endpush

<div class="sp-page">
    <div class="sp-bg">
        <div class="sp-blob sp-blob-1"></div>
        <div class="sp-blob sp-blob-2"></div>
    </div>
    <div class="d-flex min-vh-100">
        <main class="flex-fill p-4" style="position:relative;z-index:1;">
            <div class="sp-wrap">
                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-undo"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Refunds</div>
                            <div class="sp-ph-title">Refunds & Returns</div>
                            <div class="sp-ph-sub">Overview of your refund and return transactions</div>
                        </div>
                    </div>
                </div>

                <div class="sp-card" style="margin-bottom:16px;">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-gauge"></i> Summary</div>
                    </div>
                    <div class="sp-card-body">
                        <div class="row">
                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Today's Refunds</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($todayRefunds->total_refund_amount ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-undo fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Items Refunded Today</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todayRefunds->total_items ?? 0 }} items</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-box fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-6 mb-4">
                                <div class="card border-left-warning shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">This Month's Refunds</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($monthlyRefunds->total_refund_amount ?? 0, 2) }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-table"></i> Recent Refunds</div>
                    </div>
                    <div class="sp-card-body">
                        <div class="sp-table-wrap">
                            <table class="sp-table table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Cashier</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($refunds as $refund)
                            <tr>
                                <td>{{ $refund->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $refund->product->product_name ?? 'N/A' }}</td>
                                <td>{{ $refund->quantity_refunded }}</td>
                                <td>₱{{ number_format($refund->refund_amount, 2) }}</td>
                                <td>{{ $refund->reason }}</td>
                                <td>
                                    <span class="badge bg-{{ $refund->status == 'approved' ? 'success' : ($refund->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($refund->status) }}
                                    </span>
                                </td>
                                <td>{{ $refund->cashier->name ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No refunds found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                        </div>

                        @if($refunds->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $refunds->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
