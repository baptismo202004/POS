@extends('layouts.app')
@section('title', 'Product Lifecycle — ' . $product->product_name)

@push('stylesDashboard')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root{--navy:#0D47A1;--blue:#1976D2;--blue-lt:#42A5F5;--green:#10b981;--red:#ef4444;--amber:#f59e0b;--purple:#8b5cf6;--cyan:#0891b2;--bg:#EBF3FB;--card:#fff;--border:rgba(25,118,210,0.12);--text:#1a2744;--muted:#6b84aa;}
*{box-sizing:border-box;}
body{background:var(--bg);font-family:'Segoe UI',sans-serif;}
.lc-wrap{padding:24px 22px 60px;max-width:1400px;margin:0 auto;}

/* Header */
.lc-header{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:22px;}
.lc-title{font-size:22px;font-weight:900;color:var(--navy);margin:0;}
.lc-sub{font-size:12px;color:var(--muted);margin-top:3px;}
.lc-crumb{font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--blue);opacity:.7;margin-bottom:4px;}

/* Product identity card */
.lc-identity{display:flex;align-items:center;gap:16px;background:var(--card);border-radius:16px;border:1px solid var(--border);padding:16px 20px;margin-bottom:20px;box-shadow:0 2px 10px rgba(13,71,161,0.06);}
.lc-identity-img{width:64px;height:64px;border-radius:12px;object-fit:cover;border:1px solid var(--border);}
.lc-identity-img-ph{width:64px;height:64px;border-radius:12px;background:rgba(13,71,161,0.07);display:flex;align-items:center;justify-content:center;font-size:24px;color:var(--muted);}
.lc-identity-name{font-size:18px;font-weight:800;color:var(--navy);}
.lc-identity-meta{font-size:12px;color:var(--muted);margin-top:2px;}
.lc-identity-badges{display:flex;gap:6px;flex-wrap:wrap;margin-top:6px;}

/* KPI strip */
.lc-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin-bottom:20px;}
.lc-kpi{background:var(--card);border-radius:12px;border:1px solid var(--border);padding:12px 14px;box-shadow:0 1px 8px rgba(13,71,161,0.05);position:relative;overflow:hidden;}
.lc-kpi::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
.lc-kpi.g::before{background:var(--green);}
.lc-kpi.b::before{background:var(--blue);}
.lc-kpi.a::before{background:var(--amber);}
.lc-kpi.r::before{background:var(--red);}
.lc-kpi.p::before{background:var(--purple);}
.lc-kpi.c::before{background:var(--cyan);}
.lc-kpi-label{font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;}
.lc-kpi-value{font-size:18px;font-weight:900;color:var(--navy);}

/* Layout */
.lc-cols{display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start;}
@media(max-width:960px){.lc-cols{grid-template-columns:1fr;}}

/* Card */
.lc-card{background:var(--card);border-radius:16px;border:1px solid var(--border);box-shadow:0 2px 12px rgba(13,71,161,0.07);overflow:hidden;margin-bottom:16px;}
.lc-card-head{padding:11px 18px;background:linear-gradient(135deg,var(--navy),var(--blue));display:flex;align-items:center;justify-content:space-between;}
.lc-card-title{font-size:13px;font-weight:800;color:#fff;display:flex;align-items:center;gap:7px;}
.lc-card-title i{color:rgba(0,229,255,.85);}
.lc-card-badge{background:rgba(255,255,255,0.18);color:#fff;font-size:11px;font-weight:700;padding:2px 9px;border-radius:20px;}

/* Timeline */
.lc-timeline{padding:16px 20px;position:relative;}
.lc-timeline::before{content:'';position:absolute;left:36px;top:0;bottom:0;width:2px;background:var(--border);}
.lc-event{display:flex;gap:14px;margin-bottom:20px;position:relative;}
.lc-event:last-child{margin-bottom:0;}
.lc-event-dot{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;color:#fff;flex-shrink:0;z-index:1;box-shadow:0 2px 8px rgba(0,0,0,0.15);}
.lc-event-body{flex:1;background:rgba(240,246,255,0.5);border:1px solid var(--border);border-radius:12px;padding:10px 14px;}
.lc-event-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:4px;}
.lc-event-label{font-size:12px;font-weight:800;color:var(--navy);}
.lc-event-date{font-size:11px;color:var(--muted);}
.lc-event-summary{font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px;}
.lc-event-detail{font-size:11.5px;color:var(--muted);}
.lc-event-user{font-size:11px;color:var(--blue);margin-top:4px;font-weight:600;}
.lc-event-user i{margin-right:3px;}

/* Badges */
.bp{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10.5px;font-weight:700;}
.bp-green{background:rgba(16,185,129,0.12);color:#059669;}
.bp-red{background:rgba(239,68,68,0.12);color:#dc2626;}
.bp-amber{background:rgba(245,158,11,0.12);color:#d97706;}
.bp-blue{background:rgba(25,118,210,0.12);color:#1565c0;}
.bp-purple{background:rgba(139,92,246,0.12);color:#7c3aed;}
.bp-cyan{background:rgba(8,145,178,0.12);color:#0e7490;}
.bp-gray{background:rgba(107,132,170,0.12);color:#4b5563;}

/* Serial table */
.lc-tbl{width:100%;border-collapse:collapse;font-size:12.5px;}
.lc-tbl th{padding:8px 12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);border-bottom:1px solid var(--border);background:rgba(13,71,161,0.02);}
.lc-tbl td{padding:9px 12px;border-bottom:1px solid rgba(25,118,210,0.05);color:var(--text);vertical-align:middle;}
.lc-tbl tr:last-child td{border-bottom:none;}
.lc-tbl tr:hover td{background:rgba(21,101,192,0.04);}

/* Warranty bar */
.lc-warranty-bar{height:6px;border-radius:4px;background:rgba(13,71,161,0.08);overflow:hidden;margin-top:3px;}
.lc-warranty-fill{height:100%;border-radius:4px;}

/* Repair form */
.lc-form label{font-size:12px;font-weight:700;color:var(--muted);margin-bottom:3px;display:block;}
.lc-form input,.lc-form select,.lc-form textarea{width:100%;padding:8px 11px;border-radius:9px;border:1.5px solid var(--border);font-size:13px;background:var(--card);color:var(--text);outline:none;margin-bottom:10px;}
.lc-form input:focus,.lc-form select:focus,.lc-form textarea:focus{border-color:var(--blue-lt);box-shadow:0 0 0 3px rgba(66,165,245,0.1);}
.lc-form textarea{resize:vertical;min-height:70px;}
</style>
@endpush

@section('content')
<div class="lc-wrap">

{{-- Header --}}
<div class="lc-header">
    <div>
        <div class="lc-crumb">
            <a href="{{ $backIndexRoute ?? route('superadmin.products.index') }}" style="color:inherit;text-decoration:none;">Products</a>
            &rsaquo;
            <a href="{{ $backShowRoute ?? route('superadmin.products.show', $product) }}" style="color:inherit;text-decoration:none;">{{ $product->product_name }}</a>
            &rsaquo; Lifecycle
        </div>
        <h1 class="lc-title"><i class="fas fa-history me-2" style="color:var(--blue);"></i>Product Lifecycle</h1>
        <div class="lc-sub">Full history: purchases · stock-ins · transfers · sales · refunds{{ $isElectronic ? ' · serials · warranty · repairs' : '' }}</div>
    </div>
    <a href="{{ $backShowRoute ?? route('superadmin.products.show', $product) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Back to Product
    </a>
</div>

{{-- Product identity --}}
<div class="lc-identity">
    @if($product->image)
        <img src="{{ asset('storage/'.$product->image) }}" class="lc-identity-img" alt="{{ $product->product_name }}">
    @else
        <div class="lc-identity-img-ph"><i class="fas fa-box"></i></div>
    @endif
    <div class="flex-fill">
        <div class="lc-identity-name">{{ $product->product_name }}</div>
        <div class="lc-identity-meta">
            Barcode: {{ $product->barcode ?? '—' }}
            @if($product->model_number) · Model: {{ $product->model_number }} @endif
            · Brand: {{ $product->brand->brand_name ?? '—' }}
            · Category: {{ $product->category->category_name ?? '—' }}
        </div>
        <div class="lc-identity-badges">
            <span class="bp {{ $product->status === 'active' ? 'bp-green' : 'bp-red' }}">{{ ucfirst($product->status) }}</span>
            <span class="bp bp-blue">{{ $product->display_product_type }}</span>
            @if($isElectronic && $product->warranty_coverage_months)
                <span class="bp bp-cyan"><i class="fas fa-shield-alt me-1"></i>{{ $product->warranty_coverage_months }}mo warranty</span>
            @endif
            @if($isElectronic && $product->voltage_specs)
                <span class="bp bp-gray">{{ $product->voltage_specs }}</span>
            @endif
        </div>
    </div>
</div>

{{-- KPI strip --}}
<div class="lc-kpis">
    <div class="lc-kpi b">
        <div class="lc-kpi-label">Timeline Events</div>
        <div class="lc-kpi-value">{{ $timeline->count() }}</div>
    </div>
    <div class="lc-kpi g">
        <div class="lc-kpi-label">Total Revenue</div>
        <div class="lc-kpi-value">₱{{ number_format($timeline->where('type','sale')->sum(fn($e) => $e['raw']->subtotal ?? 0), 0) }}</div>
    </div>
    <div class="lc-kpi a">
        <div class="lc-kpi-label">Total Purchased</div>
        <div class="lc-kpi-value">{{ number_format($timeline->where('type','purchase')->sum(fn($e) => $e['raw']->quantity ?? 0), 0) }}</div>
    </div>
    <div class="lc-kpi r">
        <div class="lc-kpi-label">Total Refunds</div>
        <div class="lc-kpi-value">{{ $timeline->where('type','refund')->count() }}</div>
    </div>
    @if($isElectronic && $serialSummary)
    <div class="lc-kpi g">
        <div class="lc-kpi-label">Serials In Stock</div>
        <div class="lc-kpi-value">{{ $serialSummary['in_stock'] }}</div>
    </div>
    <div class="lc-kpi a">
        <div class="lc-kpi-label">Warranty Active</div>
        <div class="lc-kpi-value">{{ $serialSummary['warranty_active'] }}</div>
    </div>
    <div class="lc-kpi r">
        <div class="lc-kpi-label">Warranty Expired</div>
        <div class="lc-kpi-value">{{ $serialSummary['warranty_expired'] }}</div>
    </div>
    <div class="lc-kpi c">
        <div class="lc-kpi-label">Repairs Logged</div>
        <div class="lc-kpi-value">{{ $repairs->count() }}</div>
    </div>
    @endif
</div>

<div class="lc-cols">
    {{-- LEFT: Timeline --}}
    <div>
        <div class="lc-card">
            <div class="lc-card-head">
                <div class="lc-card-title"><i class="fas fa-stream"></i> Full Lifecycle Timeline</div>
                <span class="lc-card-badge">{{ $timeline->count() }} events</span>
            </div>
            <div class="lc-timeline">
                @forelse($timeline as $event)
                @php
                    $statusBadge = match($event['status'] ?? '') {
                        'completed','approved','repaired','returned','paid' => 'bp-green',
                        'pending','received','in_progress','in_warranty' => 'bp-amber',
                        'voided','rejected','unrepairable','defective','out_of_warranty' => 'bp-red',
                        'partial' => 'bp-cyan',
                        default => 'bp-gray',
                    };
                @endphp
                <div class="lc-event">
                    <div class="lc-event-dot" style="background:{{ $event['color'] }}">
                        <i class="fas {{ $event['icon'] }}"></i>
                    </div>
                    <div class="lc-event-body">
                        <div class="lc-event-header">
                            <span class="lc-event-label">{{ $event['label'] }}</span>
                            <div style="display:flex;align-items:center;gap:6px;">
                                @if($event['status'])
                                    <span class="bp {{ $statusBadge }}">{{ ucfirst(str_replace('_',' ',$event['status'])) }}</span>
                                @endif
                                <span class="lc-event-date">{{ $event['date'] ? \Carbon\Carbon::parse($event['date'])->format('M d, Y H:i') : '—' }}</span>
                            </div>
                        </div>
                        <div class="lc-event-summary">{{ $event['summary'] }}</div>
                        @if($event['detail'])
                            <div class="lc-event-detail">{{ $event['detail'] }}</div>
                        @endif
                        @if($event['user'])
                            <div class="lc-event-user"><i class="fas fa-user-circle"></i>{{ $event['user'] }}</div>
                        @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:40px;color:var(--muted);">
                    <i class="fas fa-history" style="font-size:32px;opacity:.2;display:block;margin-bottom:10px;"></i>
                    No lifecycle events recorded yet.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- RIGHT: Serials + Repairs --}}
    <div>

        {{-- Serial numbers panel (electronics only) --}}
        @if($isElectronic && $serialSummary)
        <div class="lc-card">
            <div class="lc-card-head">
                <div class="lc-card-title"><i class="fas fa-barcode"></i> Serial Numbers</div>
                <span class="lc-card-badge">{{ $serialSummary['total'] }} units</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="lc-tbl">
                    <thead>
                        <tr>
                            <th>Serial #</th><th>Branch</th><th>Status</th><th>Warranty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serialSummary['items'] as $serial)
                        @php
                            $wExpiry = $serial->warranty_expiry_date;
                            $wActive = $wExpiry && $wExpiry->isFuture();
                            $wDaysLeft = $wExpiry ? now()->diffInDays($wExpiry, false) : null;
                            $wPct = ($wExpiry && $product->warranty_coverage_months > 0)
                                ? max(0, min(100, round($wDaysLeft / ($product->warranty_coverage_months * 30) * 100)))
                                : 0;
                        @endphp
                        <tr>
                            <td style="font-weight:700;font-size:12px;font-family:monospace;">{{ $serial->serial_number }}</td>
                            <td style="font-size:11px;">{{ $serial->branch->branch_name ?? '—' }}</td>
                            <td>
                                <span class="bp {{ match($serial->status) { 'in_stock'=>'bp-green','sold'=>'bp-blue','returned'=>'bp-amber','defective'=>'bp-red','lost'=>'bp-gray',default=>'bp-gray' } }}">
                                    {{ ucfirst(str_replace('_',' ',$serial->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($wExpiry)
                                    <div style="font-size:11px;color:{{ $wActive ? 'var(--green)' : 'var(--red)' }};font-weight:600;">
                                        {{ $wActive ? 'Active' : 'Expired' }}
                                        @if($wActive) · {{ $wDaysLeft }}d left @endif
                                    </div>
                                    <div class="lc-warranty-bar">
                                        <div class="lc-warranty-fill" style="width:{{ $wPct }}%;background:{{ $wActive ? 'var(--green)' : 'var(--red)' }};"></div>
                                    </div>
                                    <div style="font-size:10px;color:var(--muted);">{{ $wExpiry->format('M d, Y') }}</div>
                                @else
                                    <span style="font-size:11px;color:var(--muted);">—</span>
                                @endif
                            </td>
                        </tr>
                        @if($serial->saleItem)
                        <tr>
                            <td colspan="4" style="background:rgba(245,158,11,0.04);padding:6px 12px;font-size:11px;color:var(--muted);">
                                <i class="fas fa-cash-register me-1" style="color:var(--amber);"></i>
                                Sold via Sale #{{ $serial->saleItem->sale->reference_number ?? $serial->saleItem->sale_id }}
                                @if($serial->saleItem->sale->customer) · {{ $serial->saleItem->sale->customer->full_name }} @endif
                                @if($serial->sold_at) · {{ \Carbon\Carbon::parse($serial->sold_at)->format('M d, Y') }} @endif
                            </td>
                        </tr>
                        @endif
                        @if($serial->repairs->isNotEmpty())
                        <tr>
                            <td colspan="4" style="background:rgba(8,145,178,0.04);padding:6px 12px;font-size:11px;color:var(--muted);">
                                <i class="fas fa-tools me-1" style="color:var(--cyan);"></i>
                                {{ $serial->repairs->count() }} repair(s) logged
                                · Latest: {{ $serial->repairs->last()->status }}
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="4" style="text-align:center;padding:20px;color:var(--muted);">No serials recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Repair log --}}
        <div class="lc-card">
            <div class="lc-card-head">
                <div class="lc-card-title"><i class="fas fa-tools"></i> Repair / Warranty Claims</div>
                <span class="lc-card-badge">{{ $repairs->count() }}</span>
            </div>
            <div style="padding:14px 16px;">
                @forelse($repairs as $rep)
                <div style="border:1px solid var(--border);border-radius:10px;padding:10px 14px;margin-bottom:10px;background:rgba(8,145,178,0.03);">
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:4px;">
                        <span style="font-weight:700;font-size:13px;color:var(--navy);">
                            {{ ucfirst(str_replace('_',' ',$rep->repair_type)) }}
                            @if($rep->serial_number) · <span style="font-family:monospace;font-size:12px;">{{ $rep->serial_number }}</span> @endif
                        </span>
                        <div style="display:flex;gap:5px;align-items:center;">
                            <span class="bp {{ match($rep->status) { 'repaired','returned'=>'bp-green','in_progress'=>'bp-amber','received'=>'bp-blue','unrepairable'=>'bp-red',default=>'bp-gray' } }}">
                                {{ ucfirst(str_replace('_',' ',$rep->status)) }}
                            </span>
                            <span style="font-size:11px;color:var(--muted);">{{ $rep->received_date->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div style="font-size:12.5px;color:var(--text);margin-bottom:3px;">{{ $rep->issue_description }}</div>
                    @if($rep->resolution_notes)
                        <div style="font-size:11.5px;color:var(--muted);">Resolution: {{ $rep->resolution_notes }}</div>
                    @endif
                    <div style="display:flex;gap:12px;margin-top:5px;font-size:11px;color:var(--muted);">
                        @if($rep->repair_cost > 0) <span><i class="fas fa-peso-sign me-1"></i>₱{{ number_format($rep->repair_cost, 2) }}</span> @endif
                        @if($rep->handledBy) <span><i class="fas fa-user me-1"></i>{{ $rep->handledBy->name }}</span> @endif
                        @if($rep->branch) <span><i class="fas fa-map-marker-alt me-1"></i>{{ $rep->branch->branch_name }}</span> @endif
                        @if($rep->returned_date) <span><i class="fas fa-calendar-check me-1"></i>Returned {{ $rep->returned_date->format('M d, Y') }}</span> @endif
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px;">No repairs logged yet.</div>
                @endforelse

                {{-- Log new repair form --}}
                <div style="margin-top:14px;border-top:1px solid var(--border);padding-top:14px;">
                    <div style="font-size:12px;font-weight:800;color:var(--navy);margin-bottom:10px;">
                        <i class="fas fa-plus-circle me-1" style="color:var(--cyan);"></i>Log New Repair / Warranty Claim
                    </div>
                    <form class="lc-form" id="repairForm">
                        @csrf
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div>
                                <label>Serial Number (optional)</label>
                                <input type="text" name="serial_number" placeholder="e.g. SN12345678">
                            </div>
                            <div>
                                <label>Repair Type</label>
                                <select name="repair_type">
                                    <option value="in_warranty">In Warranty</option>
                                    <option value="out_of_warranty">Out of Warranty</option>
                                    <option value="inspection">Inspection</option>
                                </select>
                            </div>
                            <div>
                                <label>Status</label>
                                <select name="status">
                                    <option value="received">Received</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="repaired">Repaired</option>
                                    <option value="returned">Returned to Customer</option>
                                    <option value="unrepairable">Unrepairable</option>
                                </select>
                            </div>
                            <div>
                                <label>Repair Cost (₱)</label>
                                <input type="number" name="repair_cost" min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div>
                                <label>Received Date</label>
                                <input type="date" name="received_date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div>
                                <label>Returned Date</label>
                                <input type="date" name="returned_date">
                            </div>
                            <div>
                                <label>Branch</label>
                                <select name="branch_id">
                                    <option value="">— Select Branch —</option>
                                    @foreach($branches as $b)
                                        <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Handled By</label>
                                <select name="handled_by">
                                    <option value="">— Select User —</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label>Issue Description *</label>
                            <textarea name="issue_description" placeholder="Describe the issue..." required></textarea>
                        </div>
                        <div>
                            <label>Resolution Notes</label>
                            <textarea name="resolution_notes" placeholder="What was done to fix it..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-save me-1"></i> Save Repair Record
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Current stock per branch --}}
        <div class="lc-card">
            <div class="lc-card-head">
                <div class="lc-card-title"><i class="fas fa-warehouse"></i> Current Stock by Branch</div>
            </div>
            <div style="overflow-x:auto;">
                <table class="lc-tbl">
                    <thead><tr><th>Branch</th><th class="text-end">Stock (base units)</th></tr></thead>
                    <tbody>
                        @forelse($product->branchStocks as $bs)
                        <tr>
                            <td>{{ $bs->branch->branch_name ?? '—' }}</td>
                            <td class="text-end" style="font-weight:700;color:{{ $bs->quantity_base > 0 ? 'var(--green)' : 'var(--red)' }};">
                                {{ number_format($bs->quantity_base, 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="2" style="text-align:center;padding:16px;color:var(--muted);">No stock records.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>{{-- /.right col --}}
</div>{{-- /.lc-cols --}}

</div>{{-- /.lc-wrap --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('repairForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const data = Object.fromEntries(new FormData(form));

    try {
        const res = await fetch('{{ route("superadmin.products.repairs.store", $product) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });
        const json = await res.json();
        if (json.success) {
            Swal.fire({ icon: 'success', title: 'Repair logged!', text: 'The repair record has been saved.', timer: 2000, showConfirmButton: false })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: json.message || 'Could not save repair.' });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Network Error', text: err.message });
    }
});
</script>
@endpush
@endsection
