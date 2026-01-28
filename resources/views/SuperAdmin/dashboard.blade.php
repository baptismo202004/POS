@extends('layouts.app')
@section('title', 'Dashboard')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@7.2.1/dist/gridstack.min.css"/>
    <style>
        body { font-family: 'Inter', sans-serif;}
        .sidebar { width: 220px; }
        .dash-header { font-size: 28px; font-weight:700; color: var(--color-deep-navy); }
        .search-input { border-radius: 999px; padding-left: 44px; padding-right: 1rem; }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9aa6b2; }
        .stat-card { border-radius: 14px; padding: 18px; display:flex; align-items:center; justify-content:space-between; }
        .stat-icon { width:56px;height:56px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.6); }
        .card-soft { background: var(--color-card-bg); border-radius:12px; }
        .panel { background: var(--color-card-bg); border-radius:12px; padding:18px; border:1px solid var(--color-divider); }
        .small-circle { width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#e3f2ff;color:#1e3a8a;font-weight:600 }
        .bottom-avatar { position:fixed; left:32px; bottom:24px; display:flex; flex-direction:column; align-items:center; gap:6px }
        .ring { width:72px;height:72px;border-radius:50%;display:grid;place-items:center;background:conic-gradient(#2b8af9 var(--pct), rgba(0,0,0,0.06) 0); }
        .ring-inner { width:56px;height:56px;border-radius:50%;background:#fff;display:grid;place-items:center }

        /* User dropdown aesthetics */
        :root { --icon-color: var(--color-blue); --icon-muted: #60a5fa; --icon-stroke: 1.6; }
        .icon { width:20px; height:20px; color:var(--icon-color); opacity:0.98; }
        .icon path, .icon rect { stroke:currentColor; fill:none; stroke-width:var(--icon-stroke); stroke-linecap:round; stroke-linejoin:round; }
        .icon circle { fill: currentColor; }
        .icon-badge { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#fff; box-shadow:0 8px 20px rgba(15,23,42,0.06); }
        .user-avatar { width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3b82f6,#7c3aed);color:#fff;font-weight:700;box-shadow:0 6px 14px rgba(59,130,246,0.12); }
        .user-dropdown-menu { min-width:210px;border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,0.08);padding:6px; }
        .dropdown-item svg { opacity:0.95; width:18px;height:18px; }
        .dropdown-item { border-radius:8px; padding:8px 12px; }
        .dropdown-item:hover { background: rgba(242,159,103,0.15); }
        .dropdown-toggle .username { font-weight:600;color: var(--color-text); }
        .dropdown-toggle .role { font-size:12px;color:var(--icon-muted);margin-left:2px; }
        .caret-icon { opacity:0.75; color:var(--icon-muted); }
        /* (dark mode removed) */
        .page-tabs .tab { display:inline-block; padding:8px 12px; border-radius:8px; color: var(--color-text-muted); text-decoration:none; }
        .page-tabs .tab.active { color: var(--color-deep-navy); background:#FFFFFF; border:1px solid var(--color-divider); }
        .legend-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
        .status-card { background:#1E1E2C; color:#FFFFFF; border-radius:12px; padding:18px; }
        .status-card .value { font-size:32px; font-weight:700; color:#34B1AA; }
        /* Dashboard-specific styles */
        .kpi { position: relative; margin-bottom: 6px; border-radius:10px; padding:10px 12px; background: var(--color-card-bg); border:1px solid var(--color-divider); }
        .kpi .label { color: var(--color-text-muted); font-weight: 600; }
        .kpi .value { font-size: 28px; font-weight: 700; color: var(--color-deep-navy); }
        .kpi .value.profit { color: var(--color-teal); }
        /* blur-on-hover without layout shift */
        .kpi .value .amt { filter:none; transition: filter 160ms ease; display:inline-block; }
        .kpi .pct { opacity:0; font-size: 12px; margin-left: 8px; transition: opacity 160ms ease; }
        .kpi:hover .value .amt { filter: blur(3px); }
        .kpi:hover .pct { opacity:1; }
        .pct.up { color: var(--color-teal); }
        .pct.down { color: var(--color-red); }
        .pill-input { border-radius: 999px; border:1px solid var(--color-divider); background: #FFFFFF; }
        .btn-soft { background: #FFFFFF; border:1px solid var(--color-divider); }
        .panel { background: var(--color-card-bg); border-radius:14px; padding:18px; box-shadow: 0 6px 18px rgba(15,23,42,0.08); border:1px solid var(--color-divider); }
        
        /* POS Widget specific styles */
        .widget-item { transition: all 0.2s ease; }
        .widget-item:hover { background: rgba(242,159,103,0.05); border-radius: 6px; }
        .widget-number { font-family: 'Inter', monospace; font-weight: 600; }
        .stock-badge-danger { background: rgba(239,68,68,0.1); color: #dc2626; }
        .stock-badge-warning { background: rgba(245,158,11,0.1); color: #d97706; }
        .rank-badge { background: linear-gradient(135deg,#f3f4f6,#e5e7eb); color: #374151; font-weight: 600; min-width: 20px; text-align: center; }
        
                /* Additional widget styles */
        .status-badge-active { background: rgba(34,197,94,0.1); color: #16a34a; }
        .status-badge-inactive { background: rgba(107,114,128,0.1); color: #6b7280; }
        .widget-section { margin-bottom: 0.5rem; }
        .widget-section-title { font-size: 0.75rem; font-weight: 600; color: var(--color-text-muted); margin-bottom: 0.5rem; }
        
        /* New Dashboard Layout Styles */
        .col-lg-2-4 { 
            flex: 0 0 auto; 
            width: 20%; 
            padding: 0 12px;
        }
        
        /* KPI Cards - Row 1 with Admin Color Mapping */
        .kpi-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(15,23,42,0.06);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15,23,42,0.12);
        }
        
        .kpi-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .kpi-icon {
            font-size: 24px;
            margin-right: 12px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(242,159,103,0.1);
            border-radius: 12px;
        }
        
        /* Sales Widget - Blue accent */
        .kpi-card[data-widget="today-sales"] .kpi-icon {
            background: rgba(59,143,243,0.1);
            color: var(--info-blue);
        }
        
        /* Profit Widget - Teal accent */
        .kpi-card[data-widget="today-profit"] .kpi-icon {
            background: rgba(52,177,170,0.1);
            color: var(--success-teal);
        }
        
        /* Expenses Widget - Yellow accent */
        .kpi-card[data-widget="today-expenses"] .kpi-icon {
            background: rgba(224,181,15,0.1);
            color: var(--warning-yellow);
        }
        
        /* Critical Stock Widget - Yellow/Red accent */
        .kpi-card[data-widget="critical-stock"] .kpi-icon {
            background: rgba(224,181,15,0.1);
            color: var(--warning-yellow);
        }
        
        .kpi-card[data-widget="critical-stock"].critical .kpi-icon {
            background: rgba(220,38,38,0.1);
            color: var(--danger-red);
        }
        
        /* Cash on Hand Widget - Blue accent */
        .kpi-card[data-widget="cash-on-hand"] .kpi-icon {
            background: rgba(59,143,243,0.1);
            color: var(--info-blue);
        }
        
        /* Responsive adjustments for different widget sizes */
        .grid-stack-item[gs-w="1"] .kpi-value {
            font-size: 18px;
        }
        
        .grid-stack-item[gs-w="2"] .kpi-value {
            font-size: 24px;
        }
        
        .grid-stack-item[gs-w="3"] .kpi-value {
            font-size: 28px;
        }
        
        .grid-stack-item[gs-h="1"] .kpi-card {
            padding: 12px;
        }
        
        .grid-stack-item[gs-h="2"] .kpi-card {
            padding: 16px;
        }
        
        .grid-stack-item[gs-h="3"] .kpi-card {
            padding: 20px;
        }
        
        /* Chart container responsiveness */
        .grid-stack-item-content canvas {
            max-width: 100%;
            height: auto !important;
        }
        
        /* List responsiveness */
        .top-list, .performance-list, .alerts-list {
            max-height: 100%;
            overflow-y: auto;
        }
        
        .grid-stack-item[gs-h="2"] .top-list,
        .grid-stack-item[gs-h="2"] .performance-list,
        .grid-stack-item[gs-h="2"] .alerts-list {
            max-height: 120px;
        }
        
        .grid-stack-item[gs-h="3"] .top-list,
        .grid-stack-item[gs-h="3"] .performance-list,
        .grid-stack-item[gs-h="3"] .alerts-list {
            max-height: 200px;
        }
        
        .grid-stack-item[gs-h="4"] .top-list,
        .grid-stack-item[gs-h="4"] .performance-list,
        .grid-stack-item[gs-h="4"] .alerts-list {
            max-height: 280px;
        }
        
        .kpi-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .kpi-body {
            text-align: center;
        }
        
        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-family: 'Inter', monospace;
        }
        
        /* Sales Widget - Primary text */
        .kpi-card[data-widget="today-sales"] .kpi-value {
            color: var(--text-primary);
        }
        
        /* Profit Widget - Teal for positive, Red for negative */
        .kpi-card[data-widget="today-profit"] .kpi-value.profit-positive {
            color: var(--profit-positive);
        }
        
        .kpi-card[data-widget="today-profit"] .kpi-value.profit-negative {
            color: var(--profit-negative);
        }
        
        /* Expenses Widget - Primary text */
        .kpi-card[data-widget="today-expenses"] .kpi-value {
            color: var(--text-primary);
        }
        
        /* Critical Stock - Yellow warning, Red critical */
        .kpi-card[data-widget="critical-stock"] .kpi-value {
            color: var(--low-stock-warning);
        }
        
        .kpi-card[data-widget="critical-stock"].critical .kpi-value {
            color: var(--low-stock-critical);
        }
        
        /* Cash on Hand - Contextual coloring */
        .kpi-card[data-widget="cash-on-hand"] .kpi-value.cash-normal {
            color: var(--cash-normal);
        }
        
        .kpi-card[data-widget="cash-on-hand"] .kpi-value.cash-balanced {
            color: var(--cash-balanced);
        }
        
        .kpi-card[data-widget="cash-on-hand"] .kpi-value.cash-mismatch {
            color: var(--cash-mismatch);
        }
        
        .kpi-change {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .change-indicator {
            margin-right: 4px;
            font-size: 16px;
        }
        
        .change-value.positive {
            color: var(--sales-trend-up);
        }
        
        .change-value.negative {
            color: var(--sales-trend-down);
        }
        
        .kpi-subtitle {
            font-size: 12px;
            color: var(--inactive-text);
        }
        
        /* Alerts Panel - Admin Color Mapping */
        .alerts-panel {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }
        
        .alerts-list {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .alert-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: var(--card-bg);
            border-left: 4px solid;
            transition: all 0.2s ease;
        }
        
        .alert-item:hover {
            transform: translateX(4px);
        }
        
        /* Alert Type Colors */
        .alert-item.critical {
            border-left-color: var(--danger-red);
            background: rgba(220,38,38,0.05);
        }
        
        .alert-item.warning {
            border-left-color: var(--warning-yellow);
            background: rgba(224,181,15,0.05);
        }
        
        .alert-item.action-needed {
            border-left-color: var(--attention-orange);
            background: rgba(242,159,103,0.05);
        }
        
        .alert-item.info {
            border-left-color: var(--info-blue);
            background: rgba(59,143,243,0.05);
        }
        
        .alert-icon {
            margin-right: 12px;
            font-size: 18px;
        }
        
        .alert-content {
            flex: 1;
        }
        
        .alert-title {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
            color: var(--text-primary);
        }
        
        .alert-description {
            font-size: 11px;
            color: var(--inactive-text);
        }
        
        /* Top Lists - Admin Color Mapping */
        .top-list {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .top-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: rgba(242,159,103,0.05);
            transition: all 0.2s ease;
        }
        
        .top-item:hover {
            background: rgba(242,159,103,0.1);
            transform: translateX(4px);
        }
        
        .top-rank {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--attention-orange);
            color: white;
            border-radius: 8px;
            font-weight: 700;
            font-size: 12px;
            margin-right: 12px;
        }
        
        .top-rank.gold {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }
        
        .top-rank.silver {
            background: linear-gradient(135deg, #e5e7eb, #9ca3af);
        }
        
        .top-rank.bronze {
            background: linear-gradient(135deg, #f97316, #ea580c);
        }
        
        .top-content {
            flex: 1;
        }
        
        .top-name {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
            color: var(--text-primary);
        }
        
        .top-details {
            font-size: 11px;
            color: var(--inactive-text);
        }
        
        .top-metrics {
            text-align: right;
        }
        
        .top-value {
            font-weight: 700;
            font-size: 14px;
            color: var(--success-teal);
        }
        
        .top-subtitle {
            font-size: 10px;
            color: var(--inactive-text);
        }
        
        /* Performance List - Admin Color Mapping */
        .performance-list {
            max-height: 280px;
            overflow-y: auto;
        }
        
        .performance-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
            background: rgba(52,177,170,0.05);
            transition: all 0.2s ease;
        }
        
        .performance-item:hover {
            background: rgba(52,177,170,0.1);
            transform: translateX(4px);
        }
        
        .performance-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--info-blue), var(--hover-blue));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin-right: 12px;
        }
        
        .performance-content {
            flex: 1;
        }
        
        .performance-name {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
            color: var(--text-primary);
        }
        
        .performance-stats {
            font-size: 11px;
            color: var(--inactive-text);
        }
        
        .performance-value {
            text-align: right;
        }
        
        .performance-amount {
            font-weight: 700;
            font-size: 14px;
            color: var(--success-teal);
        }
        
        .performance-transactions {
            font-size: 10px;
            color: var(--inactive-text);
        }
        
        /* Transaction Summary - Admin Color Mapping */
        .transaction-summary {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }
        
        .summary-item {
            padding: 16px;
            border-radius: 8px;
            background: var(--card-bg);
            margin: 8px;
            transition: all 0.2s ease;
        }
        
        .summary-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15,23,42,0.08);
        }
        
        .summary-label {
            font-size: 12px;
            color: var(--inactive-text);
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'Inter', monospace;
        }
        
        .summary-value.alert-value {
            color: var(--danger-red);
        }
        
        /* GridStack Dashboard Customization */
        .grid-stack {
            background: transparent;
        }
        
        .grid-stack-item {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(15,23,42,0.06);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .grid-stack-item:hover {
            box-shadow: 0 8px 24px rgba(15,23,42,0.12);
        }
        
        .grid-stack-item-content {
            padding: 20px;
            height: 100%;
            overflow: auto;
        }
        
        /* Edit Mode Styles */
        .dashboard-edit-mode .grid-stack-item {
            cursor: move;
            border: 2px dashed var(--attention-orange);
        }
        
        .dashboard-edit-mode .grid-stack-item.ui-draggable-dragging {
            opacity: 0.8;
            transform: rotate(2deg);
            box-shadow: 0 12px 32px rgba(15,23,42,0.2);
        }
        
        /* Resize Handles */
        .grid-stack-item .ui-resizable-handle {
            background: var(--attention-orange);
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .dashboard-edit-mode .grid-stack-item .ui-resizable-handle {
            opacity: 0.7;
        }
        
        .grid-stack-item .ui-resizable-se {
            width: 20px;
            height: 20px;
            background: linear-gradient(135deg, transparent 50%, var(--attention-orange) 50%);
            border-radius: 0 0 14px 0;
        }
        
        /* Customize Button */
        .customize-dashboard-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: var(--info-blue);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(59,143,243,0.3);
        }
        
        .customize-dashboard-btn:hover {
            background: var(--hover-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59,143,243,0.4);
        }
        
        .customize-dashboard-btn.editing {
            background: var(--success-teal);
        }
        
        /* Edit Mode Controls */
        .edit-controls {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 999;
            display: none;
            flex-direction: column;
            gap: 10px;
        }
        
        .edit-controls.show {
            display: flex;
        }
        
        .edit-btn {
            background: white;
            border: 1px solid var(--card-border);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(15,23,42,0.1);
        }
        
        .edit-btn:hover {
            background: var(--app-bg);
            transform: translateY(-1px);
        }
        
        .edit-btn.save {
            background: var(--success-teal);
            color: white;
            border-color: var(--success-teal);
        }
        
        .edit-btn.reset {
            background: var(--danger-red);
            color: white;
            border-color: var(--danger-red);
        }
        
        /* Widget Grid Layout */
        .widget-grid {
            min-height: 600px;
        }
        
        /* Responsive adjustments for GridStack */
        @media (max-width: 768px) {
            .grid-stack-item {
                margin-bottom: 10px;
            }
            
            .customize-dashboard-btn {
                top: 10px;
                right: 10px;
                padding: 10px 16px;
                font-size: 14px;
            }
            
            .edit-controls {
                top: 60px;
                right: 10px;
            }
        }
        
        /* Color Palette - Admin Dashboard Mapping */
        :root {
            /* Global Structure */
            --app-bg: #F9FAFB;
            --card-bg: #FFFFFF;
            --card-border: #E5E7EB;
            --page-header: #1E1E2C;
            
            /* KPI Colors */
            --text-primary: #1E1E2C;
            --text-secondary: #111827;
            --success-teal: #34B1AA;
            --danger-red: #DC2626;
            --warning-yellow: #E0B50F;
            --attention-orange: #F29F67;
            --info-blue: #3B8FF3;
            --hover-blue: #256FE0;
            --inactive-text: #A1A1AA;
            
            /* Chart Colors */
            --chart-this-week: #F29F67;
            --chart-last-week: #1E1E2C;
            --chart-target: #3B8FF3;
            --chart-positive-area: rgba(52,177,170,0.15);
            --chart-warning: #E0B50F;
            
            /* State Colors */
            --profit-positive: #34B1AA;
            --profit-negative: #DC2626;
            --sales-trend-up: #34B1AA;
            --sales-trend-down: #DC2626;
            --expenses-warning: #E0B50F;
            --low-stock-warning: #E0B50F;
            --low-stock-critical: #DC2626;
            --cash-normal: #1E1E2C;
            --cash-balanced: #34B1AA;
            --cash-mismatch: #F29F67;
        }
    </style>
@endpush
@php
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $end = Carbon::today();
    $start = $end->copy()->subDays(6); // last 7 days including today

    // Prepare label dates for each day in range
    $datePeriod = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
    $labels = [];
    $labelKeys = []; // Y-m-d keys to align data
    foreach ($datePeriod as $d) {
        $labels[] = $d->format('D');
        $labelKeys[] = $d->format('Y-m-d');
    }

    // Sales sums by date (created_at)
    $salesRows = DB::table('sales')
        ->selectRaw('DATE(created_at) as d, SUM(total_amount) as s')
        ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
        ->groupBy('d')
        ->pluck('s', 'd');

    // Expenses sums by date (expense_date)
    $expenseRows = DB::table('expenses')
        ->selectRaw('expense_date as d, SUM(amount) as s')
        ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
        ->groupBy('d')
        ->pluck('s', 'd');

    $salesData = [];
    $expensesData = [];
    foreach ($labelKeys as $key) {
        $salesData[] = (float) ($salesRows[$key] ?? 0);
        $expensesData[] = (float) ($expenseRows[$key] ?? 0);
    }
@endphp
    @section('content')
    <div class="p-3 p-lg-4">
        <!-- Customize Dashboard Button -->
        <button class="customize-dashboard-btn" id="customizeBtn">
            <i class="fas fa-puzzle-piece me-2"></i>Customize Dashboard
        </button>
        
        <!-- Edit Mode Controls -->
        <div class="edit-controls" id="editControls">
            <button class="edit-btn save" id="saveLayoutBtn">
                <i class="fas fa-save me-1"></i>Save Layout
            </button>
            <button class="edit-btn reset" id="resetLayoutBtn">
                <i class="fas fa-undo me-1"></i>Reset to Default
            </button>
            <button class="edit-btn" id="cancelEditBtn">
                <i class="fas fa-times me-1"></i>Cancel
            </button>
        </div>

        <div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
            <div>
                <div class="dash-header mb-1">Good Morning, {{ auth()->user()->name ?? 'User' }}</div>
                <div class="text-muted small">Your performance overview for today</div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-secondary btn-sm">🔄 Refresh</button>
                <button class="btn btn-secondary btn-sm">📊 Export</button>
            </div>
        </div>

        <!-- GridStack Widget Grid -->
        <div class="widget-grid grid-stack" id="dashboardGrid">
            <!-- Today's Sales Widget -->
            <div class="grid-stack-item" gs-id="widget-today-sales" gs-x="0" gs-y="0" gs-w="2" gs-h="2" id="widget-today-sales">
                <div class="grid-stack-item-content">
                    <div class="kpi-card" data-widget="today-sales">
                        <div class="kpi-header">
                            <div class="kpi-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="kpi-title">Today's Sales</div>
                        </div>
                        <div class="kpi-body">
                            <div class="kpi-value" id="todaySalesAmount">₱0.00</div>
                            <div class="kpi-change" id="todaySalesChange">
                                <span class="change-indicator">→</span>
                                <span class="change-value">+0.0%</span>
                            </div>
                            <div class="kpi-subtitle" id="todaySalesTransactions">0 transactions</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Profit Widget -->
            <div class="grid-stack-item" gs-id="widget-today-profit" gs-x="2" gs-y="0" gs-w="2" gs-h="2" id="widget-today-profit">
                <div class="grid-stack-item-content">
                    <div class="kpi-card" data-widget="today-profit">
                        <div class="kpi-header">
                            <div class="kpi-icon">
                                <i class="fas fa-arrow-trend-up"></i>
                            </div>
                            <div class="kpi-title">Today's Profit</div>
                        </div>
                        <div class="kpi-body">
                            <div class="kpi-value" id="todayProfitAmount">₱0.00</div>
                            <div class="kpi-subtitle">Net profit</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Expenses Widget -->
            <div class="grid-stack-item" gs-id="widget-today-expenses" gs-x="4" gs-y="0" gs-w="2" gs-h="2" id="widget-today-expenses">
                <div class="grid-stack-item-content">
                    <div class="kpi-card" data-widget="today-expenses">
                        <div class="kpi-header">
                            <div class="kpi-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <div class="kpi-title">Today's Expenses</div>
                        </div>
                        <div class="kpi-body">
                            <div class="kpi-value" id="todayExpensesAmount">₱0.00</div>
                            <div class="kpi-subtitle" id="biggestExpenseCategory">No expenses</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critical Stock Widget -->
            <div class="grid-stack-item" gs-id="widget-critical-stock" gs-x="6" gs-y="0" gs-w="2" gs-h="2" id="widget-critical-stock">
                <div class="grid-stack-item-content">
                    <div class="kpi-card" data-widget="critical-stock">
                        <div class="kpi-header">
                            <div class="kpi-icon">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div class="kpi-title">Critical Stock</div>
                        </div>
                        <div class="kpi-body">
                            <div class="kpi-value" id="criticalStockCount">0</div>
                            <div class="kpi-subtitle">items below minimum</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash on Hand Widget -->
            <div class="grid-stack-item" gs-id="widget-cash-on-hand" gs-x="8" gs-y="0" gs-w="2" gs-h="2" id="widget-cash-on-hand">
                <div class="grid-stack-item-content">
                    <div class="kpi-card" data-widget="cash-on-hand">
                        <div class="kpi-header">
                            <div class="kpi-icon">
                                <i class="fas fa-cash-register"></i>
                            </div>
                            <div class="kpi-title">Cash on Hand</div>
                        </div>
                        <div class="kpi-body">
                            <div class="kpi-value" id="cashOnHandAmount">₱0.00</div>
                            <div class="kpi-subtitle">Cash sales today</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales & Profit Trend Chart -->
            <div class="grid-stack-item" gs-id="widget-trend-chart" gs-x="0" gs-y="2" gs-w="6" gs-h="4" id="widget-trend-chart">
                <div class="grid-stack-item-content">
                    <div class="panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="color: var(--attention-orange); font-size: 20px;">
                                    <i class="fas fa-chart-area"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Sales & Profit Trend</div>
                                    <div class="text-muted small">Last 7 days performance</div>
                                </div>
                            </div>
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="trendType" id="trendSales" value="sales" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="trendSales">Sales</label>
                                
                                <input type="radio" class="btn-check" name="trendType" id="trendProfit" value="profit" autocomplete="off">
                                <label class="btn btn-outline-primary" for="trendProfit">Profit</label>
                            </div>
                        </div>
                        <div style="height:280px;">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts Panel -->
            <div class="grid-stack-item" gs-id="widget-alerts" gs-x="6" gs-y="2" gs-w="4" gs-h="4" id="widget-alerts">
                <div class="grid-stack-item-content">
                    <div class="panel alerts-panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="color: var(--danger-red); font-size: 18px;">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="fw-semibold">Alerts Panel</div>
                            </div>
                            <div class="badge bg-danger" id="totalAlertsCount">0</div>
                        </div>
                        <div id="alertsList" class="alerts-list">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Loading alerts...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="grid-stack-item" gs-id="widget-top-products" gs-x="0" gs-y="6" gs-w="4" gs-h="4" id="widget-top-products">
                <div class="grid-stack-item-content">
                    <div class="panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="color: var(--attention-orange); font-size: 18px;">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="fw-semibold">Top Products (Revenue)</div>
                            </div>
                            <div class="text-muted small">Last 30 days</div>
                        </div>
                        <div id="topProductsList" class="top-list">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Branches -->
            <div class="grid-stack-item" gs-id="widget-top-branches" gs-x="4" gs-y="6" gs-w="4" gs-h="4" id="widget-top-branches">
                <div class="grid-stack-item-content">
                    <div class="panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="color: var(--info-blue); font-size: 18px;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="fw-semibold">Top Branches</div>
                            </div>
                            <div class="text-muted small">By revenue</div>
                        </div>
                        <div id="topBranchesList" class="top-list">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cashier Performance -->
            <div class="grid-stack-item" gs-id="widget-cashier-performance" gs-x="8" gs-y="6" gs-w="4" gs-h="4" id="widget-cashier-performance">
                <div class="grid-stack-item-content">
                    <div class="panel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="color: var(--info-blue); font-size: 18px;">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="fw-semibold">Cashier Performance</div>
                            </div>
                            <div class="text-muted small">Today</div>
                        </div>
                        <div id="cashierPerformanceList" class="performance-list">
                            <div class="text-center text-muted py-3">
                                <div class="spinner-border spinner-border-sm me-2"></div>
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Summary -->
            <div class="grid-stack-item" gs-id="widget-transaction-summary" gs-x="0" gs-y="10" gs-w="12" gs-h="2" id="widget-transaction-summary">
                <div class="grid-stack-item-content">
                    <div class="panel transaction-summary">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="summary-item">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="fas fa-receipt me-2" style="color: var(--text-primary); font-size: 16px;"></i>
                                        <div class="summary-label">Total Transactions</div>
                                    </div>
                                    <div class="summary-value" id="totalTransactions">0</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-item">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="fas fa-chart-line me-2" style="color: var(--text-primary); font-size: 16px;"></i>
                                        <div class="summary-label">Avg Transaction Value</div>
                                    </div>
                                    <div class="summary-value" id="avgTransactionValue">₱0.00</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-item">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="fas fa-trophy me-2" style="color: var(--text-primary); font-size: 16px;"></i>
                                        <div class="summary-label">Highest Sale Today</div>
                                    </div>
                                    <div class="summary-value" id="highestSaleToday">₱0.00</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-item">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <i class="fas fa-shield-halved me-2" style="color: var(--attention-orange); font-size: 16px;"></i>
                                        <div class="summary-label">Unusual Activities</div>
                                    </div>
                                    <div class="summary-value alert-value" id="unusualActivitiesCount">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/gridstack@7.2.1/dist/gridstack-all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Global variables
  let grid;
  let trendChartInstance = null;
  let currentTrendType = 'sales';
  let isEditMode = false;
  let dashboardRefreshInterval = null;
  let originalLayout = null;
  
  // Helper function - query elements ONLY at update time
  function $(id) {
    return document.getElementById(id);
  }
  
  // DOM safety utilities
  function qs(root, selector) {
    if (!root || !root.querySelector) return null;
    try { return root.querySelector(selector); } catch (_) { return null; }
  }
  function qsById(id) {
    return document.getElementById(id);
  }
  function qsIn(widgetId, selectorOrId) {
    const root = qsById(widgetId);
    if (!root) return null;
    if (!selectorOrId) return null;
    const sel = selectorOrId.startsWith('#') || selectorOrId.startsWith('.') ? selectorOrId : `#${selectorOrId}`;
    return qs(root, sel);
  }
  function isAttached(el) {
    return !!(el && document.contains(el));
  }
  function waitForEl(selector, { timeout = 5000, root = document } = {}) {
    const start = performance.now();
    return new Promise((resolve, reject) => {
      const check = () => {
        const el = root.querySelector(selector);
        if (el) return resolve(el);
        if (performance.now() - start > timeout) return reject(new Error(`Timeout waiting for ${selector}`));
        requestAnimationFrame(check);
      };
      check();
    });
  }
  function debounce(fn, wait = 400) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
  }
  function readLayoutFromDOM() {
    return Array.from(document.querySelectorAll('.grid-stack-item')).map(el => ({
      id: el.getAttribute('gs-id') || el.id,
      x: parseInt(el.getAttribute('gs-x') || '0', 10),
      y: parseInt(el.getAttribute('gs-y') || '0', 10),
      w: parseInt(el.getAttribute('gs-w') || '1', 10),
      h: parseInt(el.getAttribute('gs-h') || '1', 10)
    })).filter(n => !!n.id).sort((a,b) => (a.y - b.y) || (a.x - b.x));
  }
  
  // Default layout
  const defaultLayout = [
    {id: 'widget-today-sales', x: 0, y: 0, w: 2, h: 2},
    {id: 'widget-today-profit', x: 2, y: 0, w: 2, h: 2},
    {id: 'widget-today-expenses', x: 4, y: 0, w: 2, h: 2},
    {id: 'widget-critical-stock', x: 6, y: 0, w: 2, h: 2},
    {id: 'widget-cash-on-hand', x: 8, y: 0, w: 2, h: 2},
    {id: 'widget-trend-chart', x: 0, y: 2, w: 6, h: 4},
    {id: 'widget-alerts', x: 6, y: 2, w: 4, h: 4},
    {id: 'widget-top-products', x: 0, y: 6, w: 4, h: 4},
    {id: 'widget-top-branches', x: 4, y: 6, w: 4, h: 4},
    {id: 'widget-cashier-performance', x: 8, y: 6, w: 4, h: 4},
    {id: 'widget-transaction-summary', x: 0, y: 10, w: 12, h: 2}
  ];
  
  // Layout-safe application (no destructive grid.load())
  function applyLayoutSafely(layout) {
    if (!grid || !Array.isArray(layout)) return;
    grid.batchUpdate();
    try {
      layout.forEach(n => {
        if (!n || !n.id) return;
        const el = document.getElementById(n.id);
        if (el) {
          grid.update(el, { x: n.x ?? 0, y: n.y ?? 0, w: n.w ?? 1, h: n.h ?? 1 });
        } else {
          // Only add if missing; keep minimal content to avoid crashes
          grid.addWidget({ id: n.id, x: n.x ?? 0, y: n.y ?? 0, w: n.w ?? 1, h: n.h ?? 1, content: '<div class="grid-stack-item-content"></div>' });
        }
      });
    } finally {
      grid.commit();
    }
    // Mount widgets after layout is applied
    WidgetManager.mountAll();
  }

  // Widget lifecycle manager
  let trendChartObserver = null;
  const WidgetManager = {
    registry: {
      'widget-trend-chart': {
        async mount(el) {
          if (!el) return;
          // Avoid duplicate
          if (trendChartInstance) {
            try { trendChartInstance.destroy(); } catch (_) {}
            trendChartInstance = null;
          }
          let canvas;
          try {
            canvas = await waitForEl('canvas#trendChart, canvas', { root: el, timeout: 4000 });
          } catch (_) {
            return; // no canvas, keep placeholder
          }
          if (!(canvas instanceof HTMLCanvasElement) || !isAttached(canvas)) return;
          const ctx = canvas.getContext('2d');
          if (!ctx) return;
          trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [
              { label: 'Sales', data: [], borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)', tension: 0.4 },
              { label: 'Profit', data: [], borderColor: 'rgb(34, 197, 94)', backgroundColor: 'rgba(34, 197, 94, 0.1)', tension: 0.4 }
            ]},
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true, ticks: { callback: function(value){ return '₱' + value.toLocaleString(); } } } } }
          });
          // Initial data
          fetchChartData().then(d => { if (d) updateTrendChart(d); }).catch(() => {});
          // Observe DOM changes to recover if canvas replaced
          trendChartObserver?.disconnect?.();
          trendChartObserver = new MutationObserver(() => {
            const currentCanvas = el.querySelector('canvas');
            if (!currentCanvas || !isAttached(currentCanvas)) {
              // Re-mount
              this.destroy(el);
              this.mount(el);
            }
          });
          trendChartObserver.observe(el, { childList: true, subtree: true });
        },
        resize() {
          if (trendChartInstance) {
            try { trendChartInstance.resize(); } catch (_) {}
          }
        },
        destroy() {
          trendChartObserver?.disconnect?.();
          trendChartObserver = null;
          if (trendChartInstance) {
            try { trendChartInstance.destroy(); } catch (_) {}
            trendChartInstance = null;
          }
        }
      }
    },
    mountById(id) { const el = qsById(id); if (!el) return; const w = this.registry[id]; if (w?.mount) w.mount.call(this.registry[id], el); },
    destroyById(id) { const w = this.registry[id]; if (w?.destroy) w.destroy(); },
    onAdded(items) { (items||[]).forEach(i => i?.el?.id && this.mountById(i.el.id)); },
    onRemoved(items) { (items||[]).forEach(i => i?.el?.id && this.destroyById(i.el.id)); },
    onResized(items) { (items||[]).forEach(i => i?.el?.id && this.registry[i.el.id]?.resize?.(i.el)); },
    mountAll() { document.querySelectorAll('.grid-stack-item').forEach(el => this.mountById(el.id)); }
  };

  // Initialize GridStack (ONCE only)
  function initGridStack() {
    grid = GridStack.init({
      float: false,
      cellHeight: 70,
      minRow: 1,
      resizable: { handles: 'e, se, s, sw, w' },
      draggable: { handle: '.kpi-header, .panel' },
      removable: false,
      autoPosition: false,
      staticGrid: false,
      disableDrag: false,
      disableResize: false,
      alwaysShowResizeHandle: false,
      itemClass: 'grid-stack-item'
    }, '.widget-grid');

    // GridStack event-driven lifecycle (normalize single node vs array)
    grid.on('added', (e, itemOrItems) => {
      const items = Array.isArray(itemOrItems) ? itemOrItems : [itemOrItems];
      WidgetManager.onAdded(items);
    });
    grid.on('removed', (e, itemOrItems) => {
      const items = Array.isArray(itemOrItems) ? itemOrItems : [itemOrItems];
      WidgetManager.onRemoved(items);
    });
    grid.on('resizestop', (e, nodeOrNodes) => {
      const nodes = Array.isArray(nodeOrNodes) ? nodeOrNodes : [nodeOrNodes];
      WidgetManager.onResized(nodes);
      autoSaveLayout();
    });
    grid.on('dragstop', () => autoSaveLayout());
    grid.on('change', () => autoSaveLayout());

    // Load saved layout on init (non-destructive)
    loadUserLayout();

    console.log('✅ GridStack initialized');
  }
  
  // 🔥 Fetch dashboard data (REPEATED)
  function fetchDashboardData() {
    console.log('📊 Fetching dashboard data...');
    return fetch('/dashboard/widgets', {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      console.log('✅ Dashboard data received:', data);
      return data;
    })
    .catch(error => {
      console.error('❌ Error fetching dashboard data:', error);
      return null;
    });
  }
  
  // Normalize backend response into the structure expected by update* functions
  function normalizeDashboardResponse(payload) {
    if (!payload) return null;
    if (payload.kpis || payload.topProducts || payload.topBranches || payload.cashierPerformance || payload.transactionSummary) {
      return payload;
    }
    const kpisSrc = payload.todayKPIs || {};
    const perf = payload.performance || {};
    const ops = payload.operations || {};
    return {
      kpis: {
        sales: {
          amount: kpisSrc.sales?.amount ?? 0,
          transactions: kpisSrc.sales?.transactions ?? 0,
          change: kpisSrc.sales?.change ?? 0
        },
        profit: {
          amount: kpisSrc.profit?.amount ?? 0,
          isPositive: !!kpisSrc.profit?.isPositive
        },
        expenses: {
          amount: kpisSrc.expenses?.amount ?? 0,
          biggestCategory: kpisSrc.expenses?.biggestCategory || null
        },
        criticalStock: typeof kpisSrc.criticalStock === 'number' ? { count: kpisSrc.criticalStock } : (kpisSrc.criticalStock || { count: 0 }),
        cashOnHand: { amount: kpisSrc.cashOnHand?.amount ?? 0 }
      },
      alerts: payload.alerts || null,
      topProducts: perf.topProductsByRevenue || null,
      topBranches: perf.topBranches || null,
      cashierPerformance: ops.cashierPerformance || null,
      transactionSummary: ops.transactionSummary || null
    };
  }

  // 🔥 Update dashboard UI (REPEATED)
  function updateDashboardUI(raw) {
    const data = normalizeDashboardResponse(raw);
    if (!data) { console.warn('⚠️ No data to update UI'); return; }
    console.log('🔄 Updating dashboard UI...');

    if (data.kpis) updateTodayKPIs(data.kpis);
    if (data.alerts) updateAlertsPanel(data.alerts);
    if (data.topProducts) updateTopProducts(data.topProducts);
    if (data.topBranches) updateTopBranches(data.topBranches);
    if (data.cashierPerformance) updateCashierPerformance(data.cashierPerformance);
    if (data.transactionSummary) updateTransactionSummary(data.transactionSummary);
    if (data.alerts) updateUnusualActivities(data.alerts);

    fetchChartData().then(chartData => { if (chartData) updateTrendChart(chartData); });
    console.log('✅ Dashboard UI updated');
  }
  
  // 🔥 Initialize trend chart via WidgetManager (no timeouts)
  function initTrendChart() {
    WidgetManager.mountById('widget-trend-chart');
  }

  // Chart data fetch and update
  async function fetchChartData(type = (currentTrendType || 'sales')) {
    try {
      const res = await fetch(`/dashboard/chart?type=${encodeURIComponent(type)}`, {
        method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      return data;
    } catch (err) {
      console.warn('Chart data fetch failed:', err);
      return null;
    }
  }
  function updateTrendChart(payload) {
    if (!trendChartInstance || !payload) return;
    const labels = payload.labels || [];
    const sales = payload.sales || payload.data || [];
    const profit = payload.profit || [];
    trendChartInstance.data.labels = labels;
    if (trendChartInstance.data.datasets[0]) trendChartInstance.data.datasets[0].data = sales;
    if (trendChartInstance.data.datasets[1]) trendChartInstance.data.datasets[1].data = profit;
    try { trendChartInstance.update(); } catch (_) {}
  }
  
  // 🔥 Update alerts panel
  function updateAlertsPanel(alerts) {
    console.log('🚨 Updating alerts panel with data:', alerts);
    const alertsList = qsIn('widget-alerts', 'alertsList');
    const totalAlertsElement = qsIn('widget-alerts', 'totalAlertsCount');
    
    if (totalAlertsElement) {
      const totalAlerts = alerts.outOfStock + alerts.negativeProfit + alerts.voidedSales + alerts.belowCostSales + alerts.highDiscountUsage;
      totalAlertsElement.textContent = totalAlerts;
    }
    
    if (alertsList) {
      const alertItems = [];
      
      if (alerts.outOfStock > 0) {
        alertItems.push(`<div class="alert-item critical"><i class="fas fa-exclamation-triangle"></i> ${alerts.outOfStock} items out of stock</div>`);
      }
      
      if (alerts.negativeProfit > 0) {
        alertItems.push(`<div class="alert-item warning"><i class="fas fa-arrow-trend-down"></i> ${alerts.negativeProfit} items sold below cost</div>`);
      }
      
      if (alerts.voidedSales > 0) {
        alertItems.push(`<div class="alert-item info"><i class="fas fa-ban"></i> ${alerts.voidedSales} voided sales today</div>`);
      }
      
      if (alerts.belowCostSales > 0) {
        alertItems.push(`<div class="alert-item warning"><i class="fas fa-exclamation-triangle"></i> ${alerts.belowCostSales} items sold below cost</div>`);
      }
      
      if (alerts.highDiscountUsage > 0) {
        alertItems.push(`<div class="alert-item info"><i class="fas fa-percentage"></i> ${alerts.highDiscountUsage} high discount transactions</div>`);
      }
      
      alertsList.innerHTML = alertItems.length > 0 ? alertItems.join('') : '<div class="alert-item success"><i class="fas fa-check-circle"></i> No alerts</div>';
    }
  }
  
  // 🔥 Update KPIs
  function updateTodayKPIs(kpis) {
    console.log('📊 Updating KPIs with data:', kpis);
    
    // Sales KPI
    const salesAmount = qsIn('widget-today-sales', 'todaySalesAmount');
    if (salesAmount) {
      salesAmount.textContent = peso(kpis.sales.amount);
    }
    
    const salesTransactions = qsIn('widget-today-sales', 'todaySalesTransactions');
    if (salesTransactions) {
      salesTransactions.textContent = `${kpis.sales.transactions} transactions`;
    }
    
    const salesChange = qsIn('widget-today-sales', 'todaySalesChange');
    if (salesChange) {
      const changeValue = salesChange.querySelector('.change-value');
      const changeIndicator = salesChange.querySelector('.change-indicator');
      if (changeValue && changeIndicator) {
        changeValue.textContent = `${kpis.sales.change >= 0 ? '+' : ''}${kpis.sales.change}%`;
        changeValue.className = `change-value ${kpis.sales.change >= 0 ? 'positive' : 'negative'}`;
        changeIndicator.textContent = kpis.sales.change >= 0 ? '↑' : '↓';
      }
    }
    
    // Profit KPI
    const profitElement = qsIn('widget-today-profit', 'todayProfitAmount');
    if (profitElement) {
      profitElement.textContent = peso(kpis.profit.amount);
      profitElement.className = `kpi-value ${kpis.profit.isPositive ? 'profit-positive' : 'profit-negative'}`;
    }
    
    // Expenses KPI
    const expensesAmount = qsIn('widget-today-expenses', 'todayExpensesAmount');
    if (expensesAmount) {
      expensesAmount.textContent = peso(kpis.expenses.amount);
    }
    
    const biggestExpenseCategory = qsIn('widget-today-expenses', 'biggestExpenseCategory');
    if (biggestExpenseCategory) {
      biggestExpenseCategory.textContent = kpis.expenses.biggestCategory 
        ? `${kpis.expenses.biggestCategory.name}: ₱${kpis.expenses.biggestCategory.total.toFixed(2)}`
        : 'No expenses today';
    }
    
    // Critical Stock KPI
    const criticalStockElement = qsIn('widget-critical-stock', 'criticalStockCount');
    const criticalStockCard = document.querySelector('[data-widget="critical-stock"]');
    if (criticalStockElement && criticalStockCard && kpis.criticalStock) {
      criticalStockElement.textContent = kpis.criticalStock.count;
      if (kpis.criticalStock.count === 0) {
        criticalStockCard.classList.add('critical');
      } else {
        criticalStockCard.classList.remove('critical');
      }
    }
    
    // Cash on Hand KPI
    const cashOnHandElement = qsIn('widget-cash-on-hand', 'cashOnHandAmount');
    if (cashOnHandElement && kpis.cashOnHand) {
      cashOnHandElement.textContent = peso(kpis.cashOnHand.amount);
    }
    
    console.log('✅ KPI update completed');
  }
  
  // 🔥 Update other widgets
  function updateTopProducts(products) {
    console.log('🏆 Updating top products with data:', products);
    
    const productsList = $('topProductsList');
    if (!productsList) return;
    
    if (!products || products.length === 0) {
      productsList.innerHTML = '<div class="text-center text-muted py-4">No sales data available</div>';
      return;
    }
    
    productsList.innerHTML = products.map((product, index) => {
      const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
      return `
        <div class="top-item">
          <div class="top-rank ${rankClass}">${index + 1}</div>
          <div class="top-content">
            <div class="top-name">${product.product_name}</div>
            <div class="top-metrics">
              <div class="top-value">${peso(product.revenue)}</div>
              <div class="top-subtitle">${product.contribution_percent}% of total</div>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }
  
  function updateTopBranches(branches) {
    console.log('🏢 Updating top branches with data:', branches);
    
    const branchesList = $('topBranchesList');
    if (!branchesList) return;
    
    if (!branches || branches.length === 0) {
      branchesList.innerHTML = '<div class="text-center text-muted py-4">No branch data available</div>';
      return;
    }
    
    branchesList.innerHTML = branches.map((branch, index) => {
      const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
      return `
        <div class="top-item">
          <div class="top-rank ${rankClass}">${index + 1}</div>
          <div class="top-content">
            <div class="top-name">${branch.branch_name}</div>
            <div class="top-metrics">
              <div class="top-value">${peso(branch.revenue)}</div>
              <div class="top-subtitle">${branch.profit_margin}% margin</div>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }
  
  function updateCashierPerformance(cashiers) {
    console.log('💰 Updating cashier performance with data:', cashiers);
    
    const performanceList = $('cashierPerformanceList');
    if (!performanceList) return;
    
    if (!cashiers || cashiers.length === 0) {
      performanceList.innerHTML = '<div class="text-center text-muted py-4">No cashier data available</div>';
      return;
    }
    
    performanceList.innerHTML = cashiers.map(cashier => `
      <div class="performance-item">
        <div class="performance-avatar">${getInitials(cashier.name)}</div>
        <div class="performance-content">
          <div class="performance-name">${cashier.name}</div>
          <div class="performance-stats">${cashier.transaction_count} transactions • Avg: ${peso(cashier.avg_transaction)}</div>
        </div>
        <div class="performance-value">
          <div class="performance-amount">${peso(cashier.total_sales)}</div>
          <div class="performance-transactions">Total sales</div>
        </div>
      </div>
    `).join('');
  }
  
  function updateTransactionSummary(summary) {
    console.log('📋 Updating transaction summary with data:', summary);
    
    const totalTransactionsEl = $('totalTransactions');
    const avgTransactionEl = $('avgTransactionValue');
    const highestSaleEl = $('highestSaleToday');
    
    if (totalTransactionsEl) totalTransactionsEl.textContent = summary.totalTransactions;
    if (avgTransactionEl) avgTransactionEl.textContent = peso(summary.avgTransactionValue);
    if (highestSaleEl) highestSaleEl.textContent = peso(summary.highestSaleToday);
  }
  
  function updateUnusualActivities(alerts) {
    console.log('⚠️ Updating unusual activities with data:', alerts);
    
    const unusualCountEl = $('unusualActivitiesCount');
    if (unusualCountEl) {
      const unusualCount = alerts.belowCostSales + alerts.highDiscountUsage;
      unusualCountEl.textContent = unusualCount;
    }
  }
  
  // Edit Mode Controls
  function enterEditMode() {
    isEditMode = true;
    originalLayout = readLayoutFromDOM();
    
    document.body.classList.add('dashboard-edit-mode');
    document.getElementById('customizeBtn').classList.add('editing');
    document.getElementById('customizeBtn').innerHTML = '<i class="fas fa-puzzle-piece me-2"></i>Editing...';
    document.getElementById('editControls').classList.add('show');
    
    // Enable dragging and resizing
    grid.enable();
    
    // Show resize handles
    document.querySelectorAll('.grid-stack-item .ui-resizable-handle').forEach(handle => {
      handle.style.opacity = '0.7';
    });
  }
  
  function exitEditMode() {
    isEditMode = false;
    
    document.body.classList.remove('dashboard-edit-mode');
    document.getElementById('customizeBtn').classList.remove('editing');
    document.getElementById('customizeBtn').innerHTML = '<i class="fas fa-puzzle-piece me-2"></i>Customize Dashboard';
    document.getElementById('editControls').classList.remove('show');
    
    // Disable dragging and resizing
    grid.disable();
    
    // Hide resize handles
    document.querySelectorAll('.grid-stack-item .ui-resizable-handle').forEach(handle => {
      handle.style.opacity = '0';
    });
  }
  
  // Layout Management
  function saveLayout() {
    const currentLayout = readLayoutFromDOM();

    fetch('/dashboard/layout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        layout: currentLayout
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showNotification('Layout saved successfully!', 'success');
      } else {
        showNotification('Failed to save layout', 'error');
      }
    })
    .catch(error => {
      console.error('Error saving layout:', error);
      showNotification('Error saving layout', 'error');
    });
  }
  
  function loadUserLayout() {
    fetch('/dashboard/layout')
      .then(response => response.json())
      .then(data => {
        const layout = (data && Array.isArray(data.layout) && data.layout.length > 0) ? data.layout : defaultLayout;
        applyLayoutSafely(layout);
        if (!window.gridStackReady) {
          window.gridStackReady = true;
          fetchDashboardData().then(updateDashboardUI);
        }
      })
      .catch(error => {
        console.error('Error loading layout:', error);
        // Fallback to default layout
        applyLayoutSafely(defaultLayout);
        if (!window.gridStackReady) {
          window.gridStackReady = true;
          fetchDashboardData().then(updateDashboardUI);
        }
      });
  }
  
  function resetToDefault() {
    if (confirm('Are you sure you want to reset to the default layout? This will reload the page.')) {
      fetch('/dashboard/layout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          layout: defaultLayout,
          reset: true
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showNotification('Layout reset to default!', 'success');
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          showNotification('Failed to reset layout', 'error');
        }
      })
      .catch(error => {
        console.error('Error resetting layout:', error);
        showNotification('Error resetting layout', 'error');
      });
    }
  }
  
  function cancelEdit() {
    if (originalLayout) {
      applyLayoutSafely(originalLayout);
    }
    exitEditMode();
  }

  const autoSaveLayout = debounce(() => {
    const current = readLayoutFromDOM();
    fetch('/dashboard/layout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ layout: current })
    }).catch(() => {});
  }, 600);
  
  // Notification helper
  function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed top-0 start-50 translate-middle-x mt-3`;
    notification.style.zIndex = '9999';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.remove();
    }, 3000);
  }
  
  // Event Listeners
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize GridStack
    initGridStack();
    
    // Customize button
    document.getElementById('customizeBtn').addEventListener('click', function() {
      if (isEditMode) {
        exitEditMode();
      } else {
        enterEditMode();
      }
    });
    
    // Edit control buttons
    document.getElementById('saveLayoutBtn').addEventListener('click', saveLayout);
    document.getElementById('resetLayoutBtn').addEventListener('click', resetToDefault);
    document.getElementById('cancelEditBtn').addEventListener('click', cancelEdit);
    
    // Data will load after layout applied in loadUserLayout()

    // Auto-refresh widgets every 60 seconds (using the main interval)
    // Note: dashboardRefreshInterval is already set in the main DOMContentLoaded
    
    // Trend type toggle
    document.querySelectorAll('input[name="trendType"]').forEach(radio => {
      radio.addEventListener('change', function() {
        currentTrendType = this.value;
        fetchChartData().then(chartData => {
          if (chartData) updateTrendChart(chartData);
        });
      });
    });
  });
  
  // Prevent accidental navigation during edit mode
  window.addEventListener('beforeunload', function(e) {
    if (isEditMode) {
      e.preventDefault();
      e.returnValue = 'You have unsaved changes to your dashboard layout. Are you sure you want to leave?';
      return e.returnValue;
    }
  });
</script>

@push('scripts')
<script>
  function peso(n) {
    return new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'}).format(n || 0);
  }
  
  function getInitials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
  }
</script>
@endpush
