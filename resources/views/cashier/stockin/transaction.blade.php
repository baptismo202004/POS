@extends('layouts.app')
@section('title', 'Stock In Transaction')

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root{--navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--green:#10b981;--bg:#EBF3FB;--card:#fff;--border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;}
    .sp-wrap{position:relative;z-index:1;padding:28px 24px 56px;font-family:'Plus Jakarta Sans',sans-serif;}
    .sp-page-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:14px;}
    .sp-ph-left{display:flex;align-items:center;gap:13px;}
    .sp-ph-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--navy),var(--blue-lt));display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(13,71,161,0.28);}
    .sp-ph-crumb{font-size:10.5px;font-weight:700;letter-spacing:.13em;text-transform:uppercase;color:var(--blue);opacity:.75;margin-bottom:3px;font-family:'Nunito',sans-serif;}
    .sp-ph-title{font-family:'Nunito',sans-serif;font-size:24px;font-weight:900;color:var(--navy);line-height:1.1;}
    .sp-ph-sub{font-size:12px;color:var(--muted);margin-top:2px;}
    .sp-btn-primary{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;border:none;cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;color:#fff;text-decoration:none;white-space:nowrap;background:linear-gradient(135deg,var(--navy),var(--blue));box-shadow:0 4px 14px rgba(13,71,161,0.26);transition:all .2s ease;}
    .sp-btn-primary:hover{transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff;}
    .sp-btn-secondary{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:11px;border:1px solid var(--border);cursor:pointer;font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;color:var(--navy);text-decoration:none;white-space:nowrap;background:rgba(13,71,161,0.08);transition:all .2s ease;}
    .sp-btn-secondary:hover{background:rgba(13,71,161,0.16);color:var(--blue);}
    .sp-card{background:var(--card);border-radius:20px;border:1px solid var(--border);box-shadow:0 4px 28px rgba(13,71,161,0.09);overflow:hidden;margin-bottom:20px;}
    .sp-card-head{padding:15px 22px;background:linear-gradient(135deg,var(--navy) 0%,var(--blue) 100%);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;}
    .sp-card-head::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 120% at 85% 50%,rgba(0,229,255,0.14),transparent);pointer-events:none;}
    .sp-card-head::after{content:'';position:absolute;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,0.05);top:-90px;right:-50px;pointer-events:none;}
    .sp-card-head-title{font-family:'Nunito',sans-serif;font-size:14.5px;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px;position:relative;z-index:1;}
    .sp-card-head-title i{color:rgba(0,229,255,.85);}
    .sp-details-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;padding:22px;}
    .sp-detail-label{font-size:11px;font-weight:700;text-transform:uppercase;color:var(--muted);letter-spacing:.06em;font-family:'Nunito',sans-serif;margin-bottom:6px;}
    .sp-detail-value{font-size:14px;font-weight:600;color:var(--text);}
    .sp-reference{font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;color:var(--navy);background:rgba(13,71,161,0.08);padding:6px 12px;border-radius:8px;display:inline-block;}
    .sp-quantity{font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:var(--navy);}
    .sp-badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif;}
    .sp-badge-green{background:rgba(16,185,129,0.12);color:#047857;}
    .sp-table-wrap{overflow-x:auto;}
    .sp-table{width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif;}
    .sp-table thead th{background:rgba(13,71,161,0.03);padding:11px 16px;font-size:11px;font-weight:700;color:var(--navy);letter-spacing:.06em;text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap;}
    .sp-table tbody td{padding:13px 16px;font-size:13.5px;color:var(--text);border-bottom:1px solid rgba(25,118,210,0.06);vertical-align:middle;}
    .sp-table tbody tr:nth-child(even) td{background:rgba(240,246,255,0.55);}
    .sp-table tbody tr:hover td{background:rgba(21,101,192,0.05);}
    .sp-unit-prices{display:flex;flex-direction:column;gap:4px;}
    .sp-unit-price{display:flex;align-items:center;gap:6px;font-size:12px;}
    .sp-unit-name{font-weight:600;color:var(--muted);}
    .sp-unit-value{font-family:'Nunito',sans-serif;font-weight:700;color:var(--navy);}
</style>
@endpush

@section('content')
<div class="sp-wrap">

    <div class="sp-page-head">
        <div class="sp-ph-left">
            <div class="sp-ph-icon"><i class="fas fa-receipt"></i></div>
            <div>
                <div class="sp-ph-crumb">Transaction Details</div>
                <div class="sp-ph-title">Stock In Transaction</div>
                <div class="sp-ph-sub">View detailed breakdown of recent stock transaction</div>
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('cashier.stockin.index') }}" class="sp-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Stock In
            </a>
            <a href="{{ route('cashier.stockin.create') }}" class="sp-btn-primary">
                <i class="fas fa-plus"></i> Add New Stock In
            </a>
        </div>
    </div>

    <div class="sp-card">
        <div class="sp-card-head">
            <div class="sp-card-head-title"><i class="fas fa-info-circle"></i> Transaction Information</div>
        </div>
        <div class="sp-details-grid">
            <div>
                <div class="sp-detail-label">Reference Number</div>
                <div class="sp-detail-value sp-reference">{{ $stockInHead->reference_number }}</div>
            </div>
            <div>
                <div class="sp-detail-label">Branch</div>
                <div class="sp-detail-value">{{ $stockInHead->branch->branch_name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="sp-detail-label">Purchase Reference</div>
                <div class="sp-detail-value">{{ $stockInHead->purchase->reference_number ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="sp-detail-label">Supplier</div>
                <div class="sp-detail-value">{{ $stockInHead->purchase->supplier->supplier_name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="sp-detail-label">Stock In Date</div>
                <div class="sp-detail-value">{{ \Carbon\Carbon::parse($stockInHead->stock_in_date)->format('M d, Y') }}</div>
            </div>
            <div>
                <div class="sp-detail-label">Total Quantity</div>
                <div class="sp-detail-value sp-quantity">{{ number_format($stockInHead->total_quantity, 2) }} units</div>
            </div>
            <div>
                <div class="sp-detail-label">Status</div>
                <div class="sp-detail-value">
                    <span class="sp-badge sp-badge-green">
                        <i class="fas fa-circle me-1" style="font-size:7px;"></i> {{ ucfirst($stockInHead->status) }}
                    </span>
                </div>
            </div>
            <div>
                <div class="sp-detail-label">Created By</div>
                <div class="sp-detail-value">{{ $stockInHead->creator->name ?? 'System' }}</div>
            </div>
        </div>
    </div>

    <div class="sp-card">
        <div class="sp-card-head">
            <div class="sp-card-head-title"><i class="fas fa-boxes"></i> Stock Items Added</div>
            <span style="position:relative;z-index:1;background:rgba(255,255,255,0.18);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $formattedMovements->count() }} items</span>
        </div>
        <div class="sp-table-wrap">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Prices</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formattedMovements as $movement)
                    <tr>
                        <td>
                            <span style="color:var(--blue);font-weight:600;display:flex;align-items:center;gap:8px;">
                                <i class="fas fa-box" style="font-size:11px;opacity:.6;"></i>
                                {{ $movement['product_name'] }}
                            </span>
                        </td>
                        <td>
                            <span class="sp-quantity">{{ number_format($movement['quantity'], 2) }}</span>
                            <span style="color:var(--muted);font-size:12px;"> units</span>
                        </td>
                        <td>
                            <div class="sp-unit-prices">
                                @forelse($movement['unit_prices'] as $unitName => $price)
                                    <div class="sp-unit-price">
                                        <span class="sp-unit-name">{{ $unitName }} (pc):</span>
                                        <span class="sp-unit-value">₱{{ $price }}</span>
                                    </div>
                                @empty
                                    <span style="color:var(--muted);font-size:12px;">—</span>
                                @endforelse
                            </div>
                        </td>
                        <td>
                            <i class="fas fa-calendar-alt me-1" style="font-size:11px;opacity:.6;"></i>
                            {{ \Carbon\Carbon::parse($movement['created_at'])->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;color:var(--muted);padding:40px;">
                            <i class="fas fa-inbox me-2"></i>No stock items found for this transaction.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
