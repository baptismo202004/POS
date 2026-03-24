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
    @keyframes spb1{from{transform:translate(0,0)}50%{transform:translate(28px,18px)}}
    @keyframes spb2{from{transform:translate(0,0)}50%{transform:translate(-20px,-22px)}}

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

    /* ── Button ── */
    .sp-btn-primary {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 18px;border-radius:11px;border:none;cursor:pointer;
        font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;
        color:#fff;text-decoration:none;white-space:nowrap;
        background:linear-gradient(135deg,var(--navy),var(--blue));
        box-shadow:0 4px 14px rgba(13,71,161,0.26);
        transition:all .2s ease;
    }
    .sp-btn-primary:hover { transform:translateY(-2px);box-shadow:0 7px 20px rgba(13,71,161,0.36);color:#fff; }

    .sp-btn-secondary {
        display:inline-flex;align-items:center;gap:7px;
        padding:9px 18px;border-radius:11px;border:none;cursor:pointer;
        font-family:'Nunito',sans-serif;font-size:13px;font-weight:700;
        color:var(--navy);text-decoration:none;white-space:nowrap;
        background:rgba(13,71,161,0.08);border:1px solid var(--border);
        transition:all .2s ease;
    }
    .sp-btn-secondary:hover { background:rgba(13,71,161,0.16);color:var(--blue); }

    /* ── Card ── */
    .sp-card {
        background:var(--card);border-radius:20px;
        border:1px solid var(--border);
        box-shadow:0 4px 28px rgba(13,71,161,0.09);
        overflow:hidden;animation:spUp .45s ease both;
        margin-bottom:20px;
    }

    /* ── Card gradient header ── */
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

    /* ── Transaction Details ── */
    .sp-details-grid {
        display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
        gap:20px;padding:22px;
    }
    .sp-detail-item {
        display:flex;flex-direction:column;gap:6px;
    }
    .sp-detail-label {
        font-size:11px;font-weight:700;text-transform:uppercase;
        color:var(--muted);letter-spacing:.06em;font-family:'Nunito',sans-serif;
    }
    .sp-detail-value {
        font-size:14px;font-weight:600;color:var(--text);
    }
    .sp-reference {
        font-family:'Nunito',sans-serif;font-size:16px;font-weight:800;
        color:var(--navy);background:rgba(13,71,161,0.08);
        padding:6px 12px;border-radius:8px;display:inline-block;
    }

    /* ── Table ── */
    .sp-table-wrap { overflow-x:auto;max-height:520px;overflow-y:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;width:5px;}
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}

    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th {
        position:sticky;top:0;z-index:3;
        background:rgba(13,71,161,0.03);
        padding:11px 16px;
        font-size:11px;font-weight:700;color:var(--navy);
        letter-spacing:.06em;text-transform:uppercase;
        border-bottom:1px solid var(--border);white-space:nowrap;
    }
    .sp-table tbody td {
        padding:13px 16px;font-size:13.5px;color:var(--text);
        border-bottom:1px solid rgba(25,118,210,0.06);
        vertical-align:middle;
        transition:background .15s;
    }
    .sp-table tbody tr:nth-child(even) td { background:rgba(240,246,255,0.55); }
    .sp-table tbody tr:hover td { background:rgba(21,101,192,0.05); }
    .sp-table tbody tr { animation:spRow .3s ease both; }
    .sp-table tbody tr:nth-child(1){animation-delay:.03s}
    .sp-table tbody tr:nth-child(2){animation-delay:.06s}
    .sp-table tbody tr:nth-child(3){animation-delay:.09s}
    @keyframes spRow{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}

    .sp-table td.empty-row { text-align:center;color:var(--muted);font-style:italic;padding:40px; }

    /* Product name */
    .sp-product-name {
        color:var(--blue);font-weight:600;text-decoration:none;
        transition:color .15s;display:flex;align-items:center;gap:8px;
    }
    .sp-product-name:hover { color:var(--navy);text-decoration:underline; }
    .sp-product-name i { font-size:11px;opacity:.6; }

    /* Quantity */
    .sp-quantity { font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:var(--navy); }

    /* Unit Prices */
    .sp-unit-prices {
        display:flex;flex-direction:column;gap:4px;
    }
    .sp-unit-price {
        display:flex;align-items:center;gap:6px;
        font-size:12px;color:var(--text);
    }
    .sp-unit-name {
        font-weight:600;color:var(--muted);
    }
    .sp-unit-value {
        font-family:'Nunito',sans-serif;font-weight:700;color:var(--navy);
    }

    /* Status Badge */
    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }

    /* Success Message */
    .sp-success-message {
        background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(16,185,129,0.05));
        border:1px solid rgba(16,185,129,0.2);
        border-radius:12px;padding:16px;margin-bottom:20px;
        display:flex;align-items:center;gap:12px;
        animation:spUp .4s ease both;
    }
    .sp-success-icon {
        width:40px;height:40px;border-radius:50%;
        background:rgba(16,185,129,0.15);
        display:flex;align-items:center;justify-content:center;
        color:#10b981;font-size:18px;
    }
    .sp-success-text {
        flex:1;
    }
    .sp-success-title {
        font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:#047857;
        margin-bottom:2px;
    }
    .sp-success-desc {
        font-size:12px;color:#065f46;
    }

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

            {{-- Success Message --}}
            <div class="sp-success-message">
                <div class="sp-success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="sp-success-text">
                    <div class="sp-success-title">Stock In Successfully Added!</div>
                    <div class="sp-success-desc">Transaction completed with reference {{ $stockInHead->reference_number }}</div>
                </div>
            </div>

            {{-- ── Page header ── --}}
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
                    <a href="{{ route('superadmin.stockin.index') }}" class="sp-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Stock In
                    </a>
                    <a href="{{ route('superadmin.stockin.create') }}" class="sp-btn-primary">
                        <i class="fas fa-plus"></i> Add New Stock In
                    </a>
                </div>
            </div>

            {{-- ── Transaction Details Card ── --}}
            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-info-circle"></i> Transaction Information
                    </div>
                </div>
                <div class="sp-details-grid">
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Reference Number</div>
                        <div class="sp-detail-value sp-reference">{{ $stockInHead->reference_number }}</div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Branch</div>
                        <div class="sp-detail-value">{{ $stockInHead->branch->branch_name ?? 'N/A' }}</div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Purchase Reference</div>
                        <div class="sp-detail-value">{{ $stockInHead->purchase->reference_number ?? 'N/A' }}</div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Supplier</div>
                        <div class="sp-detail-value">{{ $stockInHead->purchase->supplier->supplier_name ?? 'N/A' }}</div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Stock In Date</div>
                        <div class="sp-detail-value">{{ \Carbon\Carbon::parse($stockInHead->stock_in_date)->format('M d, Y') }}</div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Total Quantity</div>
                        <div class="sp-detail-value sp-quantity">{{ number_format($stockInHead->total_quantity, 2) }} units</div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Status</div>
                        <div class="sp-detail-value">
                            <span class="sp-badge sp-badge-green">
                                <i class="fas fa-circle me-1" style="font-size:7px;"></i> {{ ucfirst($stockInHead->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="sp-detail-item">
                        <div class="sp-detail-label">Created By</div>
                        <div class="sp-detail-value">{{ $stockInHead->creator->name ?? 'System' }}</div>
                    </div>
                </div>
            </div>

            {{-- ── Stock Items Card ── --}}
            <div class="sp-card">
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-boxes"></i> Stock Items Added
                    </div>
                    <span class="sp-c-badge">{{ $formattedMovements->count() }} items</span>
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
                                        <div class="sp-product-name">
                                            <i class="fas fa-box"></i>
                                            {{ $movement['product_name'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="sp-quantity">{{ number_format($movement['quantity'], 2) }}</span>
                                        <span style="color:var(--muted);font-size:12px;"> units</span>
                                    </td>
                                    <td>
                                        <div class="sp-unit-prices">
                                            @foreach($movement['unit_prices'] as $unitName => $price)
                                                <div class="sp-unit-price">
                                                    <span class="sp-unit-name">{{ $unitName }}:</span>
                                                    <span class="sp-unit-value">₱{{ $price }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span style="color:var(--text);font-weight:500;">
                                            <i class="fas fa-calendar-alt me-1" style="font-size:11px;opacity:.6;"></i>
                                            {{ \Carbon\Carbon::parse($movement['created_at'])->format('M d, Y h:i A') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty-row">
                                        <i class="fas fa-inbox me-2"></i>No stock items found for this transaction.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- end sp-wrap --}}
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@endsection
