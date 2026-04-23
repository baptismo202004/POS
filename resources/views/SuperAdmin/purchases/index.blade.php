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

    /* ── Card ── */
    .sp-card {
        background:var(--card);border-radius:20px;
        border:1px solid var(--border);
        box-shadow:0 4px 28px rgba(13,71,161,0.09);
        overflow:hidden;animation:spUp .45s ease both;
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
    .sp-c-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif; }

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
    .sp-table tbody tr:nth-child(4){animation-delay:.12s}
    .sp-table tbody tr:nth-child(5){animation-delay:.15s}
    @keyframes spRow{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}

    .sp-table td.empty-row { text-align:center;color:var(--muted);font-style:italic;padding:40px; }

    /* Ref number pill */
    .sp-ref {
        display:inline-flex;align-items:center;gap:5px;
        font-family:'Nunito',sans-serif;font-size:12px;font-weight:700;
        color:var(--navy);background:rgba(13,71,161,0.08);
        border:1px solid var(--border);
        padding:3px 10px;border-radius:20px;letter-spacing:.03em;
    }

    /* Date link */
    .sp-date-link {
        color:var(--blue);font-weight:600;text-decoration:none;
        transition:color .15s;
    }
    .sp-date-link:hover { color:var(--navy);text-decoration:underline; }

    /* Badges */
    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }
    .sp-badge-amber { background:rgba(245,158,11,0.12);color:#92400e; }

    /* Total cost */
    .sp-total { font-family:'Nunito',sans-serif;font-size:14px;font-weight:800;color:var(--navy); }

    /* View Items button */
    .sp-tbl-btn {
        display:inline-flex;align-items:center;gap:5px;
        padding:5px 12px;border-radius:8px;border:none;cursor:pointer;
        font-size:12px;font-weight:700;font-family:'Nunito',sans-serif;
        text-decoration:none;transition:all .18s ease;
        color:var(--blue);background:rgba(13,71,161,0.08);
    }
    .sp-tbl-btn:hover { background:rgba(13,71,161,0.16);color:var(--navy); }

    /* ── Pagination ── */
    .sp-pagination {
        padding:14px 22px;
        background:rgba(13,71,161,0.03);
        border-top:1px solid var(--border);
        display:flex;align-items:center;justify-content:center;
    }
    .sp-pagination .pagination { margin:0; }
    .sp-pagination .page-link {
        border-radius:8px !important;margin:0 2px;
        border:1.5px solid var(--border);
        color:var(--navy);font-weight:700;font-size:13px;
        font-family:'Nunito',sans-serif;transition:all .18s ease;
    }
    .sp-pagination .page-link:hover { background:rgba(13,71,161,0.08);border-color:var(--blue-lt); }
    .sp-pagination .page-item.active .page-link {
        background:linear-gradient(135deg,var(--navy),var(--blue));
        border-color:var(--navy);color:#fff;
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

            {{-- ── Page header ── --}}
            <div class="sp-page-head">
                <div class="sp-ph-left">
                    <div class="sp-ph-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div>
                        <div class="sp-ph-crumb">Operations</div>
                        <div class="sp-ph-title">Purchases</div>
                        <div class="sp-ph-sub">Track and manage all purchase orders</div>
                    </div>
                </div>
                <a href="{{ route('superadmin.purchases.create') }}" class="sp-btn-primary">
                    <i class="fas fa-plus"></i> Add New Purchase
                </a>
            </div>

            {{-- ── Main card ── --}}
            <div class="sp-card">

                {{-- Card gradient header --}}
                <div class="sp-card-head">
                    <div class="sp-card-head-title">
                        <i class="fas fa-list"></i> Purchase Orders
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;position:relative;z-index:1;">
                        <form method="GET" action="{{ route('superadmin.purchases.index') }}" style="display:flex;gap:8px;align-items:center;">
                            <div style="position:relative;">
                                <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.6);font-size:12px;"></i>
                                <input type="text" name="search" id="purchaseSearchInput" value="{{ $search ?? '' }}"
                                    placeholder="Search product, supplier, ref..."
                                    style="padding:7px 12px 7px 30px;border-radius:9px;border:1.5px solid rgba(255,255,255,0.25);background:rgba(255,255,255,0.12);color:#fff;font-size:12.5px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;width:240px;"
                                    onfocus="this.style.background='rgba(255,255,255,0.2)'"
                                    onblur="this.style.background='rgba(255,255,255,0.12)'">
                            </div>
                            <button type="submit" style="padding:7px 14px;border-radius:9px;border:none;background:rgba(255,255,255,0.2);color:#fff;font-size:12px;font-weight:700;cursor:pointer;">Search</button>
                            @if($search)
                                <a href="{{ route('superadmin.purchases.index') }}" style="padding:7px 12px;border-radius:9px;border:1.5px solid rgba(255,255,255,0.25);color:rgba(255,255,255,0.8);font-size:12px;text-decoration:none;">Clear</a>
                            @endif
                        </form>
                        <span class="sp-c-badge">{{ $purchases->total() }} records</span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="sp-table-wrap">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th>Reference Number</th>
                                <th>Purchase Date</th>
                                <th>Payment Status</th>
                                <th>Items</th>
                                <th>Total Cost</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>
                                        <span class="sp-ref">
                                            <i class="fas fa-hashtag" style="font-size:9px;opacity:.6;"></i>
                                            {{ $purchase->reference_number ?: 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('superadmin.purchases.show', $purchase) }}" class="sp-date-link">
                                            <i class="fas fa-calendar-alt me-1" style="font-size:11px;opacity:.6;"></i>
                                            {{ optional($purchase->purchase_date)->format('M d, Y') ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($purchase->payment_status === 'paid')
                                            <span class="sp-badge sp-badge-green">
                                                <i class="fas fa-circle me-1" style="font-size:7px;"></i> Paid
                                            </span>
                                        @else
                                            <span class="sp-badge sp-badge-amber">
                                                <i class="fas fa-circle me-1" style="font-size:7px;"></i> {{ ucfirst($purchase->payment_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-weight:600;">{{ $purchase->items->count() }}</span>
                                        <span style="color:var(--muted);font-size:12px;"> item(s)</span>
                                        @if($search)
                                            <div style="font-size:11px;color:var(--muted);margin-top:3px;">
                                                {{ $purchase->items->pluck('product.product_name')->filter()->implode(', ') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="sp-total">₱{{ number_format($purchase->total_cost, 2) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('superadmin.purchases.show', $purchase) }}" class="sp-tbl-btn">
                                            <i class="fas fa-eye"></i> View Items
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-row">
                                        <i class="fas fa-inbox me-2"></i>No purchases found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="sp-pagination">
                    {{ $purchases->links() }}
                </div>

            </div>{{-- end sp-card --}}
        </div>{{-- end sp-wrap --}}
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Auto-submit when search is cleared
    document.getElementById('purchaseSearchInput')?.addEventListener('input', function () {
        if (this.value === '') {
            this.closest('form').submit();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: 'var(--theme-color)',
            });
        @endif
    });
</script>

@endsection