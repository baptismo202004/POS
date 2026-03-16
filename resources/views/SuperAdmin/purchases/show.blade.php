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
    .sp-btn-success { background:linear-gradient(135deg,#059669,#10b981); color:#fff; box-shadow:0 4px 14px rgba(16,185,129,0.28); }
    .sp-btn-success:hover { transform:translateY(-2px); box-shadow:0 7px 20px rgba(16,185,129,0.38); color:#fff; }
    .sp-btn-outline { background:var(--card);color:var(--navy);border:1.5px solid var(--border); }
    .sp-btn-outline:hover { background:var(--navy);color:#fff;border-color:var(--navy);transform:translateX(-3px); }

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
    .sp-c-badge { position:relative;z-index:1;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;font-family:'Nunito',sans-serif; }
    .sp-card-body { padding: 22px; }

    /* ── Summary ── */
    .sp-kv { display:flex;flex-direction:column;gap:4px; }
    .sp-kv-label { font-size:10.5px;font-weight:800;letter-spacing:.13em;text-transform:uppercase;color:var(--muted);font-family:'Nunito',sans-serif; }
    .sp-kv-value { font-size:14px;font-weight:800;color:var(--navy);font-family:'Nunito',sans-serif; }

    .sp-ref {
        display:inline-flex;align-items:center;gap:5px;
        font-family:'Nunito',sans-serif;font-size:12px;font-weight:700;
        color:var(--navy);background:rgba(13,71,161,0.08);
        border:1px solid var(--border);
        padding:3px 10px;border-radius:20px;letter-spacing:.03em;
        width:fit-content;
    }

    /* Badges */
    .sp-badge { display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;font-family:'Nunito',sans-serif; }
    .sp-badge-green { background:rgba(16,185,129,0.12);color:#047857; }
    .sp-badge-amber { background:rgba(245,158,11,0.12);color:#92400e; }

    /* ── Table ── */
    .sp-table-wrap { overflow-x:auto; }
    .sp-table-wrap::-webkit-scrollbar{height:5px;}
    .sp-table-wrap::-webkit-scrollbar-thumb{background:rgba(13,71,161,0.15);border-radius:4px;}
    .sp-table { width:100%;border-collapse:separate;border-spacing:0;font-family:'Plus Jakarta Sans',sans-serif; }
    .sp-table thead th {
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
    }
    .sp-table tbody tr:nth-child(even) td { background:rgba(240,246,255,0.55); }
    .sp-table tbody tr:hover td { background:rgba(21,101,192,0.05); }
    .sp-table tfoot td { padding:13px 16px; border-top:1px solid var(--border); background:rgba(13,71,161,0.02); }

    .sp-total { font-family:'Nunito',sans-serif;font-size:14px;font-weight:900;color:var(--navy); }

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

                <div class="sp-page-head">
                    <div class="sp-ph-left">
                        <div class="sp-ph-icon"><i class="fas fa-shopping-cart"></i></div>
                        <div>
                            <div class="sp-ph-crumb">Operations</div>
                            <div class="sp-ph-title">Purchase Details</div>
                            <div class="sp-ph-sub">View purchase order information</div>
                        </div>
                    </div>
                    <div class="sp-ph-actions">
                        @if($purchase->payment_status === 'pending')
                            <form method="POST" action="{{ route('superadmin.purchases.mark-paid', $purchase) }}" class="d-inline" data-confirm-mark-paid>
                                @csrf
                                <button type="submit" class="sp-btn sp-btn-success">
                                    <i class="fas fa-check"></i> Mark as Paid
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('superadmin.purchases.index') }}" class="sp-btn sp-btn-outline">
                            <i class="fas fa-arrow-left"></i> Back to Purchases
                        </a>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-head">
                        <div class="sp-card-head-title"><i class="fas fa-receipt"></i> Purchase Summary</div>
                        <span class="sp-c-badge">{{ $purchase->items->count() }} items</span>
                    </div>

                    <div class="sp-card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="sp-kv">
                                    <div class="sp-kv-label">Reference Number</div>
                                    <div class="sp-ref"><i class="fas fa-hashtag" style="font-size:9px;opacity:.6;"></i> {{ $purchase->reference_number ?: 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="sp-kv">
                                    <div class="sp-kv-label">Purchase Date</div>
                                    <div class="sp-kv-value"><i class="fas fa-calendar-alt me-1" style="font-size:12px;opacity:.7;"></i> {{ optional($purchase->purchase_date)->format('M d, Y') }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="sp-kv">
                                    <div class="sp-kv-label">Payment Status</div>
                                    <div>
                                        @if($purchase->payment_status === 'paid')
                                            <span class="sp-badge sp-badge-green"><i class="fas fa-circle me-1" style="font-size:7px;"></i> Paid</span>
                                        @else
                                            <span class="sp-badge sp-badge-amber"><i class="fas fa-circle me-1" style="font-size:7px;"></i> {{ ucfirst($purchase->payment_status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="font-family:'Nunito',sans-serif;font-weight:900;color:var(--navy);margin: 8px 0 10px;">Purchased Items</div>
                        <div class="sp-table-wrap">
                            <table class="sp-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Unit Type</th>
                                        <th>Unit Cost</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchase->items as $item)
                                        <tr>
                                            <td style="font-weight:600;">{{ $item->product->product_name ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->unitType->unit_name ?? 'N/A' }}</td>
                                            <td>₱{{ number_format($item->unit_cost, 2) }}</td>
                                            <td style="font-weight:700;color:var(--navy);">₱{{ number_format($item->subtotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4" style="color:var(--muted);">No items found for this purchase.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($purchase->items->isNotEmpty())
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-end" style="font-weight:800;color:var(--muted);font-family:'Nunito',sans-serif;">Total Amount</td>
                                            <td><span class="sp-total">₱{{ number_format($purchase->items->sum('subtotal'), 2) }}</span></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS bundle (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[data-confirm-mark-paid]');
            if (!form || typeof Swal === 'undefined') return;

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Mark as Paid?'
                    , text: 'This will set the payment status to Paid.'
                    , icon: 'question'
                    , showCancelButton: true
                    , confirmButtonText: 'Yes, mark as paid'
                    , cancelButtonText: 'Cancel'
                    , confirmButtonColor: '#198754'
                    , cancelButtonColor: '#6c757d'
                    , reverseButtons: true
                    , background: '#ffffff'
                    , customClass: {
                        popup: 'shadow-lg rounded-4'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection
