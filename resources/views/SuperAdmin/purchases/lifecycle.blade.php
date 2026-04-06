@extends('layouts.app')
@section('title', 'Purchase Lifecycle — ' . ($purchase->reference_number ?? '#'.$purchase->id))

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{--navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--green:#10b981;--red:#ef4444;--amber:#f59e0b;--purple:#8b5cf6;--cyan:#0891b2;--bg:#EBF3FB;--card:#fff;--border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;}
*{box-sizing:border-box;}
body{background:var(--bg);font-family:'Segoe UI',sans-serif;}
.pl-wrap{padding:24px 22px 60px;max-width:1400px;margin:0 auto;}

/* Header */
.pl-header{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;}
.pl-crumb{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--blue);opacity:.7;margin-bottom:4px;}
.pl-title{font-size:22px;font-weight:900;color:var(--navy);margin:0;}
.pl-sub{font-size:12px;color:var(--muted);margin-top:3px;}

/* Purchase identity */
.pl-identity{background:var(--card);border-radius:16px;border:1px solid var(--border);padding:16px 20px;margin-bottom:18px;box-shadow:0 2px 10px rgba(13,71,161,0.06);display:flex;flex-wrap:wrap;gap:24px;align-items:center;}
.pl-id-field{display:flex;flex-direction:column;gap:2px;}
.pl-id-label{font-size:10px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);}
.pl-id-value{font-size:14px;font-weight:800;color:var(--navy);}

/* KPI strip */
.pl-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:18px;}
.pl-kpi{background:var(--card);border-radius:12px;border:1px solid var(--border);padding:12px 14px;box-shadow:0 1px 8px rgba(13,71,161,0.05);position:relative;overflow:hidden;}
.pl-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
.pl-kpi.g::before{background:var(--green);}
.pl-kpi.b::before{background:var(--blue);}
.pl-kpi.a::before{background:var(--amber);}
.pl-kpi.r::before{background:var(--red);}
.pl-kpi.p::before{background:var(--purple);}
.pl-kpi-label{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;}
.pl-kpi-value{font-size:18px;font-weight:900;color:var(--navy);}
.pl-kpi-sub{font-size:10.5px;color:var(--muted);margin-top:3px;}

/* Layout */
.pl-cols{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start;}
@media(max-width:960px){.pl-cols{grid-template-columns:1fr;}}

/* Card */
.pl-card{background:var(--card);border-radius:16px;border:1px solid var(--border);box-shadow:0 2px 12px rgba(13,71,161,0.07);overflow:hidden;margin-bottom:16px;}
.pl-card-head{padding:11px 18px;background:linear-gradient(135deg,var(--navy),var(--blue));display:flex;align-items:center;justify-content:space-between;}
.pl-card-title{font-size:13px;font-weight:800;color:#fff;display:flex;align-items:center;gap:7px;}
.pl-card-title i{color:rgba(0,229,255,.85);}
.pl-card-badge{background:rgba(255,255,255,0.18);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;}

/* Item fulfillment rows */
.pl-item-row{padding:14px 18px;border-bottom:1px solid var(--border);}
.pl-item-row:last-child{border-bottom:none;}
.pl-item-name{font-size:14px;font-weight:800;color:var(--navy);margin-bottom:6px;}
.pl-item-meta{font-size:11.5px;color:var(--muted);margin-bottom:10px;}
.pl-item-stats{display:flex;gap:16px;flex-wrap:wrap;margin-bottom:8px;}
.pl-stat{display:flex;flex-direction:column;gap:1px;}
.pl-stat-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);}
.pl-stat-value{font-size:15px;font-weight:900;color:var(--navy);}
.pl-stat-value.g{color:var(--green);}
.pl-stat-value.a{color:var(--amber);}
.pl-stat-value.r{color:var(--red);}

/* Progress bar */
.pl-bar-wrap{height:8px;border-radius:6px;background:rgba(13,71,161,0.08);overflow:hidden;}
.pl-bar{height:100%;border-radius:6px;transition:width .4s ease;}
.pl-bar-label{display:flex;justify-content:space-between;font-size:10.5px;color:var(--muted);margin-top:3px;}

/* Timeline */
.pl-timeline{padding:16px 20px;position:relative;}
.pl-timeline::before{content:'';position:absolute;left:36px;top:0;bottom:0;width:2px;background:var(--border);}
.pl-event{display:flex;gap:14px;margin-bottom:18px;position:relative;}
.pl-event:last-child{margin-bottom:0;}
.pl-event-dot{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;color:#fff;flex-shrink:0;z-index:1;box-shadow:0 2px 8px rgba(0,0,0,0.15);}
.pl-event-body{flex:1;background:rgba(240,246,255,0.5);border:1px solid var(--border);border-radius:12px;padding:10px 14px;}
.pl-event-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:4px;}
.pl-event-label{font-size:12px;font-weight:800;color:var(--navy);}
.pl-event-date{font-size:11px;color:var(--muted);}
.pl-event-summary{font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px;}
.pl-event-detail{font-size:11.5px;color:var(--muted);}
.pl-event-user{font-size:11px;color:var(--blue);margin-top:4px;font-weight:600;}

/* Badges */
.bp{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10.5px;font-weight:700;}
.bp-green{background:rgba(16,185,129,0.12);color:#059669;}
.bp-red{background:rgba(239,68,68,0.12);color:#dc2626;}
.bp-amber{background:rgba(245,158,11,0.12);color:#d97706;}
.bp-blue{background:rgba(25,118,210,0.12);color:#1565c0;}
.bp-gray{background:rgba(107,132,170,0.12);color:#4b5563;}

/* Serial table */
.pl-tbl{width:100%;border-collapse:collapse;font-size:12.5px;}
.pl-tbl th{padding:8px 12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);border-bottom:1px solid var(--border);background:rgba(13,71,161,0.02);}
.pl-tbl td{padding:9px 12px;border-bottom:1px solid rgba(25,118,210,0.05);color:var(--text);vertical-align:middle;}
.pl-tbl tr:last-child td{border-bottom:none;}
.pl-tbl tr:hover td{background:rgba(21,101,192,0.04);}
</style>
@endpush

@section('content')
<div class="pl-wrap">

{{-- Header --}}
<div class="pl-header">
    <div>
        <div class="pl-crumb">
            <a href="{{ $backIndexRoute ?? route('superadmin.purchases.index') }}" style="color:inherit;text-decoration:none;">Purchases</a>
            &rsaquo;
            <a href="{{ $backShowRoute ?? route('superadmin.purchases.show', $purchase) }}" style="color:inherit;text-decoration:none;">{{ $purchase->reference_number ?? '#'.$purchase->id }}</a>
            &rsaquo; Lifecycle
        </div>
        <h1 class="pl-title"><i class="fas fa-history me-2" style="color:var(--blue);"></i>Purchase Lifecycle</h1>
        <div class="pl-sub">Full journey: purchase → stock-in → sales → refunds · per-item fulfillment tracking</div>
    </div>
    <a href="{{ $backShowRoute ?? route('superadmin.purchases.show', $purchase) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Purchase
    </a>
</div>

{{-- Purchase identity --}}
<div class="pl-identity">
    <div class="pl-id-field">
        <div class="pl-id-label">Reference</div>
        <div class="pl-id-value">{{ $purchase->reference_number ?? '—' }}</div>
    </div>
    <div class="pl-id-field">
        <div class="pl-id-label">Supplier</div>
        <div class="pl-id-value">{{ $purchase->supplier->supplier_name ?? '—' }}</div>
    </div>
    <div class="pl-id-field">
        <div class="pl-id-label">Branch</div>
        <div class="pl-id-value">{{ $purchase->branch->branch_name ?? '—' }}</div>
    </div>
    <div class="pl-id-field">
        <div class="pl-id-label">Purchase Date</div>
        <div class="pl-id-value">{{ $purchase->purchase_date?->format('M d, Y') ?? '—' }}</div>
    </div>
    <div class="pl-id-field">
        <div class="pl-id-label">Payment</div>
        <div>
            <span class="bp {{ $purchase->payment_status === 'paid' ? 'bp-green' : 'bp-amber' }}">
                {{ ucfirst($purchase->payment_status) }}
            </span>
        </div>
    </div>
    <div class="pl-id-field">
        <div class="pl-id-label">Total Cost</div>
        <div class="pl-id-value" style="color:var(--red);">₱{{ number_format($purchase->total_cost, 2) }}</div>
    </div>
</div>

{{-- KPIs --}}
<div class="pl-kpis">
    <div class="pl-kpi b">
        <div class="pl-kpi-label">Items Purchased</div>
        <div class="pl-kpi-value">{{ $purchase->items->count() }}</div>
        <div class="pl-kpi-sub">product lines</div>
    </div>
    <div class="pl-kpi g">
        <div class="pl-kpi-label">Total Stocked In</div>
        <div class="pl-kpi-value">{{ number_format($totalStockedIn, 0) }}</div>
        <div class="pl-kpi-sub">units received</div>
    </div>
    <div class="pl-kpi a">
        <div class="pl-kpi-label">Total Sold</div>
        <div class="pl-kpi-value">{{ number_format($totalSold, 0) }}</div>
        <div class="pl-kpi-sub">units sold</div>
    </div>
    <div class="pl-kpi r">
        <div class="pl-kpi-label">Total Refunded</div>
        <div class="pl-kpi-value">{{ number_format($totalRefunded, 0) }}</div>
        <div class="pl-kpi-sub">units returned</div>
    </div>
    <div class="pl-kpi g">
        <div class="pl-kpi-label">Revenue Generated</div>
        <div class="pl-kpi-value" style="font-size:15px;">₱{{ number_format($totalRevenue, 0) }}</div>
        <div class="pl-kpi-sub">from sales</div>
    </div>
    <div class="pl-kpi {{ $grossProfit >= 0 ? 'g' : 'r' }}">
        <div class="pl-kpi-label">Gross Profit</div>
        <div class="pl-kpi-value" style="font-size:15px;color:{{ $grossProfit >= 0 ? 'var(--green)' : 'var(--red)' }};">
            ₱{{ number_format($grossProfit, 0) }}
        </div>
        <div class="pl-kpi-sub">revenue − cost</div>
    </div>
    @if($serials->isNotEmpty())
    <div class="pl-kpi b">
        <div class="pl-kpi-label">Serials Tracked</div>
        <div class="pl-kpi-value">{{ $serials->count() }}</div>
        <div class="pl-kpi-sub">{{ $serials->where('status','sold')->count() }} sold</div>
    </div>
    @endif
</div>

<div class="pl-cols">

    {{-- LEFT: Item fulfillment + Timeline --}}
    <div>

        {{-- Per-item fulfillment --}}
        <div class="pl-card">
            <div class="pl-card-head">
                <div class="pl-card-title"><i class="fas fa-boxes"></i> Item Fulfillment Tracker</div>
                <span class="pl-card-badge">{{ $purchase->items->count() }} items</span>
            </div>
            @foreach($itemSummaries as $s)
            @php
                $barColor = $s['sold_pct'] >= 100 ? 'var(--green)' : ($s['sold_pct'] > 0 ? 'var(--amber)' : 'var(--blue-lt)');
            @endphp
            <div class="pl-item-row">
                <div class="pl-item-name">{{ $s['item']->product->product_name ?? '—' }}</div>
                <div class="pl-item-meta">
                    Purchased: {{ number_format($s['item']->quantity, 0) }} {{ $s['item']->unitType->unit_name ?? '' }}
                    · Unit Cost: ₱{{ number_format($s['item']->unit_cost, 2) }}
                    · Subtotal: ₱{{ number_format($s['item']->subtotal, 2) }}
                    @php $catType = $s['item']->product->category->category_type ?? ''; @endphp
                    @if(str_contains(strtolower($catType), 'electronic'))
                        · <span class="bp bp-blue">Electronic</span>
                    @endif
                </div>
                <div class="pl-item-stats">
                    <div class="pl-stat">
                        <div class="pl-stat-label">Stocked In</div>
                        <div class="pl-stat-value b">{{ number_format($s['stocked_in'], 0) }}</div>
                    </div>
                    <div class="pl-stat">
                        <div class="pl-stat-label">Sold</div>
                        <div class="pl-stat-value {{ $s['sold'] > 0 ? 'g' : '' }}">{{ number_format($s['sold'], 0) }}</div>
                    </div>
                    <div class="pl-stat">
                        <div class="pl-stat-label">Refunded</div>
                        <div class="pl-stat-value {{ $s['refunded'] > 0 ? 'r' : '' }}">{{ number_format($s['refunded'], 0) }}</div>
                    </div>
                    <div class="pl-stat">
                        <div class="pl-stat-label">Remaining</div>
                        <div class="pl-stat-value a">{{ number_format($s['remaining'], 0) }}</div>
                    </div>
                    <div class="pl-stat">
                        <div class="pl-stat-label">Sold %</div>
                        <div class="pl-stat-value" style="color:{{ $barColor }};">{{ $s['sold_pct'] }}%</div>
                    </div>
                </div>
                <div class="pl-bar-wrap">
                    <div class="pl-bar" style="width:{{ $s['sold_pct'] }}%;background:{{ $barColor }};"></div>
                </div>
                <div class="pl-bar-label">
                    <span>0</span>
                    <span>{{ $s['sold_pct'] }}% sold of {{ number_format($s['stocked_in'], 0) }} stocked</span>
                    <span>{{ number_format($s['stocked_in'], 0) }}</span>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Timeline --}}
        <div class="pl-card">
            <div class="pl-card-head">
                <div class="pl-card-title"><i class="fas fa-stream"></i> Full Lifecycle Timeline</div>
                <span class="pl-card-badge">{{ $timeline->count() }} events</span>
            </div>
            <div class="pl-timeline">
                @forelse($timeline as $event)
                @php
                    $statusBadge = match($event['status'] ?? '') {
                        'completed','approved','paid' => 'bp-green',
                        'pending','received' => 'bp-amber',
                        'voided','rejected' => 'bp-red',
                        default => 'bp-gray',
                    };
                @endphp
                <div class="pl-event">
                    <div class="pl-event-dot" style="background:{{ $event['color'] }}">
                        <i class="fas {{ $event['icon'] }}"></i>
                    </div>
                    <div class="pl-event-body">
                        <div class="pl-event-header">
                            <span class="pl-event-label">{{ $event['label'] }}</span>
                            <div style="display:flex;align-items:center;gap:6px;">
                                @if(!empty($event['status']))
                                    <span class="bp {{ $statusBadge }}">{{ ucfirst(str_replace('_',' ',$event['status'])) }}</span>
                                @endif
                                <span class="pl-event-date">
                                    {{ $event['date'] ? \Carbon\Carbon::parse($event['date'])->format('M d, Y H:i') : '—' }}
                                </span>
                            </div>
                        </div>
                        <div class="pl-event-summary">{{ $event['summary'] }}</div>
                        @if(!empty($event['detail']))
                            <div class="pl-event-detail">{{ $event['detail'] }}</div>
                        @endif
                        @if(!empty($event['items']))
                            <div style="margin-top:8px;display:flex;flex-direction:column;gap:3px;">
                                @foreach($event['items'] as $li)
                                <div style="display:flex;align-items:center;gap:8px;font-size:12px;">
                                    <span style="background:rgba(25,118,210,0.1);color:var(--navy);font-weight:700;padding:1px 8px;border-radius:20px;white-space:nowrap;">
                                        {{ $li['qty'] }} {{ $li['unit'] }}
                                    </span>
                                    <span style="color:var(--text);font-weight:600;">{{ $li['name'] }}</span>
                                    <span style="color:var(--muted);margin-left:auto;white-space:nowrap;">₱{{ $li['cost'] }}/unit</span>
                                </div>
                                @endforeach
                            </div>
                        @endif
                        @if(!empty($event['user']))
                            <div class="pl-event-user"><i class="fas fa-user-circle me-1"></i>{{ $event['user'] }}</div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:40px;color:var(--muted);">
                    <i class="fas fa-history" style="font-size:32px;opacity:.2;display:block;margin-bottom:10px;"></i>
                    No lifecycle events yet.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- RIGHT: Serials + Sales breakdown --}}
    <div>

        {{-- Serial numbers --}}
        @if($serials->isNotEmpty())
        <div class="pl-card">
            <div class="pl-card-head">
                <div class="pl-card-title"><i class="fas fa-barcode"></i> Serial Numbers</div>
                <span class="pl-card-badge">{{ $serials->count() }}</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="pl-tbl">
                    <thead>
                        <tr><th>Serial #</th><th>Status</th><th>Branch</th><th>Warranty</th></tr>
                    </thead>
                    <tbody>
                        @foreach($serials as $serial)
                        <tr>
                            <td style="font-weight:700;font-family:monospace;font-size:12px;">{{ $serial->serial_number }}</td>
                            <td>
                                <span class="bp {{ match($serial->status) { 'in_stock','purchased'=>'bp-blue','sold'=>'bp-green','returned'=>'bp-amber','defective'=>'bp-red',default=>'bp-gray' } }}">
                                    {{ ucfirst(str_replace('_',' ',$serial->status)) }}
                                </span>
                            </td>
                            <td style="font-size:11px;">{{ $serial->branch->branch_name ?? '—' }}</td>
                            <td style="font-size:11px;">
                                @if($serial->warranty_expiry_date)
                                    @php $wDate = \Carbon\Carbon::parse($serial->warranty_expiry_date); @endphp
                                    <span style="color:{{ $wDate->isFuture() ? 'var(--green)' : 'var(--red)' }};font-weight:600;">
                                        {{ $wDate->format('M d, Y') }}
                                    </span>
                                @else
                                    <span style="color:var(--muted);">—</span>
                                @endif
                            </td>
                        </tr>
                        @if($serial->saleItem)
                        <tr>
                            <td colspan="4" style="background:rgba(245,158,11,0.04);padding:5px 12px;font-size:11px;color:var(--muted);">
                                <i class="fas fa-cash-register me-1" style="color:var(--amber);"></i>
                                Sold via Sale #{{ $serial->saleItem->sale->reference_number ?? $serial->saleItem->sale_id }}
                                @if($serial->saleItem->sale->customer) · {{ $serial->saleItem->sale->customer->full_name }} @endif
                                @if($serial->sold_at) · {{ \Carbon\Carbon::parse($serial->sold_at)->format('M d, Y') }} @endif
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Sales breakdown --}}
        <div class="pl-card">
            <div class="pl-card-head">
                <div class="pl-card-title"><i class="fas fa-cash-register"></i> Sales from this Purchase</div>
                <span class="pl-card-badge">{{ $saleItems->pluck('sale_id')->unique()->count() }} sales</span>
            </div>
            @if($saleItems->isEmpty())
            <div style="padding:24px;text-align:center;color:var(--muted);font-size:13px;">No sales recorded yet.</div>
            @else
            <div style="overflow-x:auto;">
                <table class="pl-tbl">
                    <thead>
                        <tr><th>Sale #</th><th>Product</th><th>Qty</th><th class="text-end">Revenue</th><th>Cashier</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach($saleItems as $si)
                        <tr>
                            <td style="font-weight:700;font-size:12px;">{{ $si->sale->reference_number ?? '#'.$si->sale_id }}</td>
                            <td style="font-size:12px;">{{ $si->product->product_name ?? '—' }}</td>
                            <td>{{ number_format($si->quantity, 0) }}</td>
                            <td class="text-end" style="font-weight:700;color:var(--green);">₱{{ number_format($si->subtotal, 2) }}</td>
                            <td style="font-size:11px;">{{ $si->sale->cashier->name ?? '—' }}</td>
                            <td style="font-size:11px;white-space:nowrap;">{{ $si->sale->created_at?->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="font-weight:700;font-size:12px;color:var(--muted);padding:9px 12px;">Total</td>
                            <td class="text-end" style="font-weight:900;color:var(--green);padding:9px 12px;">₱{{ number_format($totalRevenue, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>

        {{-- Refunds --}}
        @if($refunds->isNotEmpty())
        <div class="pl-card">
            <div class="pl-card-head">
                <div class="pl-card-title"><i class="fas fa-undo"></i> Refunds</div>
                <span class="pl-card-badge">{{ $refunds->count() }}</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="pl-tbl">
                    <thead>
                        <tr><th>Product</th><th>Qty</th><th class="text-end">Amount</th><th>Status</th><th>Reason</th></tr>
                    </thead>
                    <tbody>
                        @foreach($refunds as $r)
                        <tr>
                            <td style="font-size:12px;">{{ $r->product->product_name ?? '—' }}</td>
                            <td>{{ $r->quantity_refunded }}</td>
                            <td class="text-end" style="font-weight:700;color:var(--red);">₱{{ number_format($r->refund_amount, 2) }}</td>
                            <td><span class="bp {{ match($r->status ?? '') { 'approved'=>'bp-green','pending'=>'bp-amber','rejected'=>'bp-red',default=>'bp-gray' } }}">{{ ucfirst($r->status ?? '—') }}</span></td>
                            <td style="font-size:11px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->reason ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>{{-- /.right --}}
</div>{{-- /.pl-cols --}}

</div>{{-- /.pl-wrap --}}
@endsection
