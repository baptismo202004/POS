@extends('layouts.app')
@section('title', 'Dashboard')

@push('stylesDashboard')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@7.2.1/dist/gridstack.min.css"/>
    <style>
        :root {
            /* Electric Modern Palette */
            --electric-blue: #0D47A1;
            --neon-blue: #2196F3;
            --cyan-bright: #00E5FF;
            --magenta: #E91E63;
            --violet: #9C27B0;
            --lime-electric: #C6FF00;
            --slate-bg: #ECEFF1;
            --ice-white: #FAFBFC;
            
            /* Dashboard Color Mapping */
            --app-bg: linear-gradient(135deg, #ECEFF1 0%, #E8EAF6 100%);
            --card-bg: #FAFBFC;
            --card-border: rgba(13, 71, 161, 0.15);
            --page-header: #263238;
            
            /* KPI Colors - Electric Theme */
            --text-primary: #263238;
            --text-secondary: #546E7A;
            --success-teal: #43A047;
            --danger-red: #E53935;
            --warning-yellow: #C6FF00;
            --attention-orange: #E91E63;
            --info-blue: #2196F3;
            --hover-blue: #00E5FF;
            --inactive-text: #78909C;
            
            /* Chart Colors - Electric */
            --chart-this-week: #2196F3;
            --chart-last-week: #0D47A1;
            --chart-target: #00E5FF;
            --chart-positive-area: rgba(0, 229, 255, 0.15);
            --chart-warning: #C6FF00;
            
            /* State Colors */
            --profit-positive: #43A047;
            --profit-negative: #E53935;
            --sales-trend-up: #43A047;
            --sales-trend-down: #E53935;
            --expenses-warning: #C6FF00;
            --low-stock-warning: #C6FF00;
            --low-stock-critical: #E91E63;
            --cash-normal: #263238;
            --cash-balanced: #43A047;
            --cash-mismatch: #E91E63;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--slate-bg) 0%, #E8EAF6 100%);
        }
        
        .sidebar { width: 220px; }
        
        .dash-header { 
            font-size: 28px; 
            font-weight: 800; 
            background: linear-gradient(135deg, var(--electric-blue), var(--neon-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.02em;
        }
        
        .search-input { 
            border-radius: 999px; 
            padding-left: 44px; 
            padding-right: 1rem;
            border: 2px solid var(--electric-blue);
            transition: all 0.3s;
        }
        
        .search-input:focus {
            border-color: var(--cyan-bright);
            box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
        }
        
        .search-icon { 
            position: absolute; 
            left: 1rem; 
            top: 50%; 
            transform: translateY(-50%); 
            color: var(--neon-blue);
        }
        
        .stat-card { 
            border-radius: 14px; 
            padding: 18px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(33, 150, 243, 0.15);
            border-color: var(--cyan-bright);
        }
        
        .stat-icon { 
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(0, 229, 255, 0.15));
            color: var(--neon-blue);
        }
        
        .card-soft { 
            background: var(--card-bg); 
            border-radius: 12px;
            border: 2px solid var(--card-border);
        }
        
        .panel { 
            background: var(--card-bg);
            border-radius: 14px; 
            padding: 18px; 
            border: 2px solid var(--card-border);
            box-shadow: 0 6px 18px rgba(13, 71, 161, 0.08);
        }
        
        .small-circle { 
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }
        
        .bottom-avatar { 
            position: fixed; 
            left: 32px; 
            bottom: 24px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 6px;
        }
        
        .ring { 
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: conic-gradient(var(--cyan-bright) var(--pct), rgba(13, 71, 161, 0.1) 0);
        }
        
        .ring-inner { 
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
        }

        /* User dropdown aesthetics */
        .icon { 
            width: 20px; 
            height: 20px; 
            color: var(--neon-blue);
            opacity: 0.98;
        }
        
        .icon path, 
        .icon rect { 
            stroke: currentColor; 
            fill: none; 
            stroke-width: 1.6;
            stroke-linecap: round; 
            stroke-linejoin: round;
        }
        
        .icon circle { fill: currentColor; }
        
        .icon-badge { 
            width: 44px; 
            height: 44px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: #fff; 
            box-shadow: 0 8px 20px rgba(13, 71, 161, 0.08);
        }
        
        .user-avatar { 
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            font-weight: 700;
            box-shadow: 0 6px 14px rgba(33, 150, 243, 0.2);
        }
        
        .user-dropdown-menu { 
            min-width: 210px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(13, 71, 161, 0.12);
            padding: 6px;
            border: 1px solid var(--card-border);
        }
        
        .dropdown-item svg { 
            opacity: 0.95; 
            width: 18px;
            height: 18px;
            color: var(--neon-blue);
        }
        
        .dropdown-item { 
            border-radius: 8px; 
            padding: 8px 12px;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover { 
            background: rgba(0, 229, 255, 0.1);
            color: var(--electric-blue);
        }
        
        .dropdown-toggle .username { 
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .dropdown-toggle .role { 
            font-size: 12px;
            color: var(--neon-blue);
            margin-left: 2px;
        }
        
        .caret-icon { 
            opacity: 0.75; 
            color: var(--neon-blue);
        }
        
        .page-tabs .tab { 
            display: inline-block; 
            padding: 8px 12px; 
            border-radius: 8px; 
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .page-tabs .tab.active { 
            color: var(--electric-blue);
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.1), rgba(0, 229, 255, 0.1));
            border: 1px solid var(--cyan-bright);
            font-weight: 600;
        }
        
        .legend-dot { 
            width: 8px; 
            height: 8px; 
            border-radius: 50%; 
            display: inline-block; 
            margin-right: 6px;
        }
        
        .status-card { 
            background: linear-gradient(135deg, var(--electric-blue), var(--neon-blue));
            color: #FFFFFF; 
            border-radius: 12px; 
            padding: 18px;
            box-shadow: 0 8px 24px rgba(13, 71, 161, 0.3);
        }
        
        .status-card .value { 
            font-size: 32px; 
            font-weight: 700; 
            color: var(--cyan-bright);
        }
        
        /* Dashboard-specific styles */
        .kpi { 
            position: relative; 
            margin-bottom: 6px; 
            border-radius: 10px; 
            padding: 10px 12px; 
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }
        
        .kpi .label { 
            color: var(--text-secondary);
            font-weight: 600;
        }
        
        .kpi .value { 
            font-size: 28px; 
            font-weight: 700; 
            color: var(--electric-blue);
        }
        
        .kpi .value.profit { 
            color: var(--profit-positive);
        }
        
        /* Blur-on-hover without layout shift */
        .kpi .value .amt { 
            filter: none; 
            transition: filter 160ms ease; 
            display: inline-block;
        }
        
        .kpi .pct { 
            opacity: 0; 
            font-size: 12px; 
            margin-left: 8px; 
            transition: opacity 160ms ease;
        }
        
        .kpi:hover .value .amt { filter: blur(3px); }
        .kpi:hover .pct { opacity: 1; }
        
        .pct.up { color: var(--profit-positive); }
        .pct.down { color: var(--danger-red); }
        
        .pill-input { 
            border-radius: 999px; 
            border: 2px solid var(--electric-blue);
            background: #FFFFFF;
            transition: all 0.3s;
        }
        
        .pill-input:focus {
            border-color: var(--cyan-bright);
            box-shadow: 0 0 0 0.2rem rgba(0, 229, 255, 0.25);
        }
        
        .btn-soft { 
            background: #FFFFFF; 
            border: 2px solid var(--electric-blue);
            color: var(--electric-blue);
            transition: all 0.3s;
        }
        
        .btn-soft:hover {
            background: var(--electric-blue);
            color: #FFFFFF;
            transform: translateY(-2px);
        }
        
        .panel { 
            background: var(--card-bg);
            border-radius: 14px; 
            padding: 18px; 
            box-shadow: 0 6px 18px rgba(13, 71, 161, 0.08);
            border: 2px solid var(--card-border);
        }
        
        /* POS Widget specific styles */
        .widget-item { 
            transition: all 0.2s ease;
        }
        
        .widget-item:hover { 
            background: rgba(0, 229, 255, 0.08);
            border-radius: 6px;
        }
        
        .widget-number { 
            font-family: 'Inter', monospace; 
            font-weight: 600;
        }
        
        .stock-badge-danger { 
            background: rgba(233, 30, 99, 0.1);
            color: var(--magenta);
        }
        
        .stock-badge-warning { 
            background: rgba(198, 255, 0, 0.1);
            color: #827717;
        }
        
        .rank-badge { 
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            font-weight: 600; 
            min-width: 20px; 
            text-align: center;
        }
        
        /* Additional widget styles */
        .status-badge-active { 
            background: rgba(67, 160, 71, 0.1);
            color: var(--success-teal);
        }
        
        .status-badge-inactive { 
            background: rgba(120, 144, 156, 0.1);
            color: var(--inactive-text);
        }
        
        .widget-section { margin-bottom: 0.5rem; }
        
        .widget-section-title { 
            font-size: 0.75rem; 
            font-weight: 600; 
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        /* New Dashboard Layout Styles */
        .col-lg-2-4 { 
            flex: 0 0 auto; 
            width: 20%; 
            padding: 0 12px;
        }
        
        /* KPI Cards - Row 1 with Electric Colors */
        .kpi-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 20px;
            border: 2px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(0, 229, 255, 0.08), transparent 70%);
            pointer-events: none;
        }
        
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(13, 71, 161, 0.15);
            border-color: var(--cyan-bright);
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
            background: rgba(33, 150, 243, 0.1);
            border-radius: 12px;
            transition: all 0.3s;
        }
        
        .kpi-card:hover .kpi-icon {
            transform: scale(1.1);
        }
        
        /* Monthly Returns Widget - Warning Orange */
        .kpi-card[data-widget="monthly-returns"] {
            border-top: 4px solid #ff9800;
        }
        
        .kpi-card[data-widget="monthly-returns"] .kpi-icon {
            background: linear-gradient(135deg, rgba(255, 152, 0, 0.15), rgba(255, 87, 34, 0.15));
            color: #ff9800;
        }

        /* Monthly Profit Widget - Success Green */
        .kpi-card[data-widget="monthly-profit"] {
            border-top: 4px solid var(--success-teal);
        }
        
        .kpi-card[data-widget="monthly-profit"] .kpi-icon {
            background: linear-gradient(135deg, rgba(67, 160, 71, 0.15), rgba(102, 187, 106, 0.15));
            color: var(--success-teal);
        }

        /* Monthly Sales Widget - Magenta */
        .kpi-card[data-widget="monthly-sales"] {
            border-top: 4px solid var(--magenta);
        }
        
        .kpi-card[data-widget="monthly-sales"] .kpi-icon {
            background: linear-gradient(135deg, rgba(233, 30, 99, 0.15), rgba(156, 39, 176, 0.15));
            color: var(--magenta);
        }

        /* Monthly Expenses Widget - Orange */
        .kpi-card[data-widget="monthly-expenses"] {
            border-top: 4px solid var(--warning-yellow);
        }
        
        .kpi-card[data-widget="monthly-expenses"] .kpi-icon {
            background: linear-gradient(135deg, rgba(198, 255, 0, 0.15), rgba(174, 234, 0, 0.15));
            color: #827717;
        }

        /* Sales Widget - Neon Blue */
        .kpi-card[data-widget="today-sales"] {
            border-top: 4px solid var(--neon-blue);
        }
        
        .kpi-card[data-widget="today-sales"] .kpi-icon {
            background: linear-gradient(135deg, rgba(33, 150, 243, 0.15), rgba(0, 229, 255, 0.15));
            color: var(--neon-blue);
        }
        
        /* Profit Widget - Success Green */
        .kpi-card[data-widget="today-profit"] {
            border-top: 4px solid var(--success-teal);
        }
        
        .kpi-card[data-widget="today-profit"] .kpi-icon {
            background: linear-gradient(135deg, rgba(67, 160, 71, 0.15), rgba(102, 187, 106, 0.15));
            color: var(--success-teal);
        }
        
        /* Expenses Widget - Lime Electric */
        .kpi-card[data-widget="today-expenses"] {
            border-top: 4px solid var(--lime-electric);
        }
        
        .kpi-card[data-widget="today-expenses"] .kpi-icon {
            background: linear-gradient(135deg, rgba(198, 255, 0, 0.15), rgba(174, 234, 0, 0.15));
            color: #827717;
        }
        
        /* Cash on Hand Widget - Electric Blue */
        .kpi-card[data-widget="cash-on-hand"] {
            border-top: 4px solid var(--electric-blue);
        }
        
        .kpi-card[data-widget="cash-on-hand"] .kpi-icon {
            background: linear-gradient(135deg, rgba(13, 71, 161, 0.15), rgba(33, 150, 243, 0.15));
            color: var(--electric-blue);
        }
        
        /* Responsive adjustments */
        .grid-stack-item[gs-w="1"] .kpi-value { font-size: 18px; }
        .grid-stack-item[gs-w="2"] .kpi-value { font-size: 24px; }
        .grid-stack-item[gs-w="3"] .kpi-value { font-size: 28px; }
        
        .grid-stack-item[gs-h="1"] .kpi-card { padding: 12px; }
        .grid-stack-item[gs-h="2"] .kpi-card { padding: 16px; }
        .grid-stack-item[gs-h="3"] .kpi-card { padding: 20px; }
        
        /* Chart container responsiveness */
        .grid-stack-item-content canvas {
            max-width: 100%;
            height: auto !important;
        }
        
        /* List responsiveness */
        .top-list, 
        .performance-list, 
        .alerts-list {
            max-height: 100%;
            overflow-y: auto;
        }
        
        .grid-stack-item[gs-h="2"] .top-list,
        .grid-stack-item[gs-h="2"] .performance-list,
        .grid-stack-item[gs-h="2"] .alerts-list { max-height: 120px; }
        
        .grid-stack-item[gs-h="3"] .top-list,
        .grid-stack-item[gs-h="3"] .performance-list,
        .grid-stack-item[gs-h="3"] .alerts-list { max-height: 200px; }
        
        .grid-stack-item[gs-h="4"] .top-list,
        .grid-stack-item[gs-h="4"] .performance-list,
        .grid-stack-item[gs-h="4"] .alerts-list { max-height: 280px; }
        
        .kpi-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .kpi-body { text-align: center; }
        
        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-family: 'Inter', monospace;
        }
        
        /* Value color variants */
        .kpi-card[data-widget="monthly-sales"] .kpi-value { color: var(--magenta); }
        .kpi-card[data-widget="monthly-expenses"] .kpi-value { color: #827717; }
        .kpi-card[data-widget="monthly-profit"] .kpi-value { color: var(--success-teal); }
        .kpi-card[data-widget="monthly-returns"] .kpi-value { color: #ff9800; }
        .kpi-card[data-widget="today-sales"] .kpi-value { color: var(--neon-blue); }
        .kpi-card[data-widget="today-profit"] .kpi-value.profit-positive { color: var(--profit-positive); }
        .kpi-card[data-widget="today-profit"] .kpi-value.profit-negative { color: var(--profit-negative); }
        .kpi-card[data-widget="today-expenses"] .kpi-value { color: #827717; }
        .kpi-card[data-widget="cash-on-hand"] .kpi-value { color: var(--electric-blue); }
        
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
        
        .change-value.positive { color: var(--sales-trend-up); }
        .change-value.negative { color: var(--sales-trend-down); }
        
        .kpi-subtitle {
            font-size: 12px;
            color: var(--inactive-text);
        }
        
        /* Alerts Panel - Electric Theme */
        .alerts-panel {
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
        }
        
        .alerts-list {
            max-height: 280px;
            overflow-y: auto;
        }
        
        /* Clickable elements */
        .clickable {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .alert-item.clickable:hover {
            background: rgba(33, 150, 243, 0.1);
            transform: translateX(4px);
        }
        
        .top-item.clickable:hover {
            background: rgba(0, 229, 255, 0.1);
            transform: translateX(4px);
        }
        
        .performance-item.clickable:hover {
            background: rgba(67, 160, 71, 0.1);
            transform: translateX(4px);
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
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.1);
        }
        
        /* Alert Type Colors - Electric */
        .alert-item.critical {
            border-left-color: var(--magenta);
            background: rgba(233, 30, 99, 0.05);
        }
        
        .alert-item.warning {
            border-left-color: var(--lime-electric);
            background: rgba(198, 255, 0, 0.05);
        }
        
        .alert-item.action-needed {
            border-left-color: var(--neon-blue);
            background: rgba(33, 150, 243, 0.05);
        }
        
        .alert-item.info {
            border-left-color: var(--cyan-bright);
            background: rgba(0, 229, 255, 0.05);
        }
        
        .alert-icon {
            margin-right: 12px;
            font-size: 18px;
        }
        
        .alert-content { flex: 1; }
        
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
        
        /* Top Lists - Electric Theme */
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
            background: rgba(0, 229, 255, 0.05);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .top-item:hover {
            background: rgba(0, 229, 255, 0.1);
            transform: translateX(4px);
            border-color: var(--cyan-bright);
        }
        
        .top-rank {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--neon-blue);
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
        
        .top-content { flex: 1; }
        
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
        
        .top-metrics { text-align: right; }
        
        .top-value {
            font-weight: 700;
            font-size: 14px;
            color: var(--neon-blue);
        }
        
        .top-subtitle {
            font-size: 10px;
            color: var(--inactive-text);
        }
        
        /* Performance List - Electric Theme */
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
            background: rgba(33, 150, 243, 0.05);
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .performance-item:hover {
            background: rgba(33, 150, 243, 0.1);
            transform: translateX(4px);
            border-color: var(--neon-blue);
        }
        
        .performance-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            margin-right: 12px;
        }
        
        .performance-content { flex: 1; }
        
        .performance-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .performance-stats {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 2px;
        }
        
        .performance-value {
            text-align: right;
        }
        
        .performance-amount {
            font-size: 16px;
            font-weight: bold;
            color: var(--text-primary);
        }
        
        .performance-transactions {
            font-size: 11px;
            color: var(--text-secondary);
        }
        
        /* Transaction Summary */
        .transaction-summary {
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
        }
        
        .summary-item {
            padding: 16px;
            border-radius: 8px;
            background: var(--card-bg);
            margin: 8px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .summary-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(13, 71, 161, 0.12);
            border-color: var(--cyan-bright);
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
            color: var(--electric-blue);
            font-family: 'Inter', monospace;
        }
        
        .summary-value.alert-value {
            color: var(--magenta);
        }
        
            
        .grid-stack-item {
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .grid-stack-item:hover {
            box-shadow: 0 12px 32px rgba(13, 71, 161, 0.15);
            border-color: var(--cyan-bright);
        }
        
        .grid-stack-item-content {
            padding: 20px;
            height: 100%;
            overflow: auto;
        }
        
        
        
        /* Static Dashboard Layout */
        .dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 0;
        }

        .dashboard-row {
            display: flex;
            gap: 16px;
            width: 100%;
        }

        /* Row 1: KPI Cards - 4 columns */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        /* Row 2: Middle Row - 50%, 25%, 25% */
        .middle-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 16px;
        }

        /* Row 3: Bottom Row - 70%, 30% */
        .bottom-row {
            display: grid;
            grid-template-columns: 7fr 3fr;
            gap: 16px;
        }

        /* KPI Card Styles */
        .kpi-card {
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .kpi-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .kpi-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Widget Card Styles */
        .widget-card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 2px solid var(--card-border);
            box-shadow: 0 4px 12px rgba(13, 71, 161, 0.08);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .widget-card:hover {
            box-shadow: 0 8px 24px rgba(13, 71, 161, 0.12);
            border-color: var(--cyan-bright);
        }

        .widget-header {
            padding: 16px 20px;
            border-bottom: 2px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, rgba(33, 150, 243, 0.02), transparent);
        }

        .widget-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--electric-blue);
            margin: 0;
        }

        .widget-badge {
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            background: rgba(0, 229, 255, 0.1);
            color: var(--neon-blue);
            font-weight: 600;
        }

        .widget-content {
            padding: 20px;
        }

        /* Specific Widget Heights */
        .top-branches-card,
        .cashier-performance-card,
        .alerts-card {
            height: 180px;
        }

        .trend-chart-card,
        .transaction-summary-card {
            height: 250px;
        }

        /* Chart Container */
        .chart-container {
            height: 180px;
            position: relative;
        }

        /* Trend Controls */
        .trend-controls {
            display: flex;
            gap: 8px;
        }

        .trend-option {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            font-size: 14px;
            color: var(--text-secondary);
            transition: color 0.2s ease;
        }

        .trend-option input[type="radio"] {
            margin: 0;
            accent-color: var(--cyan-bright);
        }

        .trend-option input[type="radio"]:checked + span {
            color: var(--neon-blue);
            font-weight: 600;
        }

        /* Summary Stats */
        .summary-stats {
            display: flex;
            flex-direction: column;
            gap: 16px;
            height: 100%;
            justify-content: center;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .spinner {
            width: 24px;
            height: 24px;
            border: 2px solid var(--card-border);
            border-top: 2px solid var(--cyan-bright);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Alert Count Badge */
        .alert-count {
            background: linear-gradient(135deg, var(--magenta), var(--violet)) !important;
            color: white !important;
        }
        
        /* Responsive adjustments for Static Dashboard */
        @media (max-width: 767.98px) {
            .dashboard-container { gap: 12px; }
            .kpi-row { grid-template-columns: 1fr; gap: 12px; }
            .middle-row { grid-template-columns: 1fr; gap: 12px; }
            .bottom-row { grid-template-columns: 1fr; gap: 12px; }
            .kpi-card { height: 120px; padding: 16px; }
            .kpi-value { font-size: 20px; }
            .kpi-title { font-size: 12px; }
            .widget-card { height: auto; min-height: 180px; }
            .widget-content { padding: 16px; }
            .chart-container { height: 200px; }
            .summary-stats { gap: 12px; }
            .summary-value { font-size: 16px; }
        }

        /* Tablet responsive */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .kpi-row { grid-template-columns: repeat(2, 1fr); gap: 14px; }
            .middle-row { grid-template-columns: 1fr 1fr; gap: 14px; }
            .middle-row .alerts-card { grid-column: span 2; }
            .bottom-row { grid-template-columns: 1fr; gap: 14px; }
            .kpi-card { height: 140px; padding: 18px; }
            .kpi-value { font-size: 22px; }
            .widget-card { height: 200px; }
            .chart-container { height: 160px; }
        }

        /* Large desktop */
        @media (min-width: 1200px) {
            .dashboard-container { max-width: 1400px; margin: 0 auto; }
            .kpi-card { height: 160px; }
            .widget-card { height: auto; }
            .trend-chart-card { height: 280px; }
            .transaction-summary-card { height: 280px; }
            .chart-container { height: 200px; }
        }
        
        /* Branch Sales List Styles */
        .branch-sales-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .branch-sales-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--color-divider);
            transition: background-color 0.2s ease;
        }
        
        .branch-sales-item:hover {
            background-color: var(--teal-hover);
        }
        
        .branch-sales-item:last-child {
            border-bottom: none;
        }
        
        .branch-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .branch-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--teal);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .branch-name {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .branch-amount {
            font-weight: 600;
            color: var(--teal);
        }
        
        /* Expense Categories List Styles */
        .expense-categories-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .expense-category-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--color-divider);
            transition: background-color 0.2s ease;
        }
        
        .expense-category-item:hover {
            background-color: var(--teal-hover);
        }
        
        .expense-category-item:last-child {
            border-bottom: none;
        }
        
        .expense-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .expense-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: var(--error);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .expense-name {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .expense-amount {
            font-weight: 600;
            color: var(--error);
        }
        
        /* Monthly Sales List Styles */
        .monthly-sales-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .monthly-sales-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
            transition: background-color 0.2s ease;
        }
        
        .monthly-sales-item:hover {
            background-color: var(--teal-hover);
        }
        
        .monthly-sales-item:last-child {
            border-bottom: none;
        }
        
        .monthly-sales-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .monthly-sales-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--neon-blue), var(--cyan-bright));
            color: var(--electric-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .monthly-sales-name {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .monthly-sales-amount {
            font-weight: 600;
            color: var(--magenta);
        }

        /* Monthly Profit List Styles */
        .monthly-profit-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .monthly-profit-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
            transition: background-color 0.2s ease;
        }
        
        .monthly-profit-item:hover {
            background-color: var(--success-hover);
        }
        
        .monthly-profit-item:last-child {
            border-bottom: none;
        }
        
        .monthly-profit-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .monthly-profit-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(67, 160, 71, 0.15), rgba(102, 187, 106, 0.15));
            color: var(--success-teal);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .monthly-profit-name {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .monthly-profit-amount {
            font-weight: 600;
            color: var(--success-teal);
        }

        /* Monthly Returns List Styles */
        .monthly-returns-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .monthly-returns-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
            transition: background-color 0.2s ease;
        }
        
        .monthly-returns-item:hover {
            background-color: var(--warning-hover);
        }
        
        .monthly-returns-item:last-child {
            border-bottom: none;
        }
        
        .monthly-returns-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .monthly-returns-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 152, 0, 0.15), rgba(255, 87, 34, 0.15));
            color: #ff9800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .monthly-returns-name {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .monthly-returns-amount {
            font-weight: 600;
            color: #ff9800;
        }

        /* Monthly Expenses List Styles */
        .monthly-expenses-list {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .monthly-expenses-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid var(--border-light);
            transition: background-color 0.2s ease;
        }
        
        .monthly-expenses-item:hover {
            background-color: var(--warning-hover);
        }
        
        .monthly-expenses-item:last-child {
            border-bottom: none;
        }
        
        .monthly-expenses-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .monthly-expenses-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 152, 0, 0.15), rgba(255, 87, 34, 0.15));
            color: #ff9800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .monthly-expenses-name {
            font-weight: 500;
            color: var(--color-text);
        }
        
        .monthly-expenses-amount {
            font-weight: 600;
            color: #827717;
        }
    </style>
@endpush

@php
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $end = Carbon::today();
    $start = $end->copy()->subDays(6);

    $datePeriod = new DatePeriod($start, new DateInterval('P1D'), $end->copy()->addDay());
    $labels = [];
    $labelKeys = [];
    foreach ($datePeriod as $d) {
        $labels[] = $d->format('D');
        $labelKeys[] = $d->format('Y-m-d');
    }

    $salesRows = DB::table('sales')
        ->selectRaw('DATE(created_at) as d, SUM(total_amount) as s')
        ->whereBetween('created_at', [$start->startOfDay(), $end->endOfDay()])
        ->groupBy('d')
        ->pluck('s', 'd');

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

    <div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
        <div>
            <div class="dash-header mb-1">Good Morning, {{ auth()->user()->name ?? 'User' }}</div>
            <div class="text-muted small">Your performance overview for today</div>
        </div>
    </div>

    <!-- Static Dashboard Layout -->
    <div class="dashboard-container">
        <!-- Row 1: Top KPI Cards -->
        <div class="dashboard-row kpi-row">
            <div class="kpi-card clickable" data-widget="today-sales" onclick="showTodaySalesModal()">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Today's Sales</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="todaySalesAmount">₱0.00</div>
                    <div class="kpi-subtitle" id="todaySalesTransactions">0 transactions</div>
                </div>
            </div>

            <div class="kpi-card clickable" data-widget="today-expenses" onclick="showTodayExpensesModal()">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Today's Expenses</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="todayExpensesAmount">₱0.00</div>
                    <div class="kpi-subtitle">Operating costs</div>
                </div>
            </div>

            <div class="kpi-card" data-widget="cash-on-hand">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                            <path d="M7 15h.01M12 15h.01M17 15h.01"/>
                        </svg>
                    </div>
                    <div class="kpi-title"> Cash on Hand</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="cashOnHandAmount">₱0.00</div>
                    <div class="kpi-subtitle">Total cash sales this year</div>
                </div>
            </div>

            <div class="kpi-card" data-widget="today-profit">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="2" x2="12" y2="22"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Today's Profit</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="todayProfitAmount">₱0.00</div>
                    <div class="kpi-subtitle">Net profit margin</div>
                </div>
            </div>
        </div>

        <!-- Row 2: Monthly KPI Cards -->
        <div class="dashboard-row kpi-row">
            <div class="kpi-card clickable" data-widget="monthly-sales" onclick="showMonthlySalesModal()">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <line x1="9" y1="9" x2="15" y2="9"/>
                            <line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Monthly Sales</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="monthlySalesAmount">₱0.00</div>
                    <div class="kpi-subtitle" id="monthlySalesChange">No change</div>
                </div>
            </div>

            <div class="kpi-card clickable" data-widget="monthly-expenses" onclick="showMonthlyExpensesModal()">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Monthly Expenses</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="monthlyExpensesAmount">₱0.00</div>
                    <div class="kpi-subtitle">Operating costs</div>
                </div>
            </div>

            <div class="kpi-card clickable" data-widget="monthly-returns" onclick="showMonthlyReturnsModal()">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 4v10a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1z"/>
                            <path d="M7 4v10a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z"/>
                            <path d="M13 4v10a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1z"/>
                            <path d="M19 4v10a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1z"/>
                            <path d="M3 20h18"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Returns/Refunds</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="monthlyReturnsAmount">₱0.00</div>
                    <div class="kpi-subtitle" id="monthlyReturnsChange">No change</div>
                </div>
            </div>

            <div class="kpi-card clickable" data-widget="monthly-profit" onclick="showMonthlyProfitModal()">
                <div class="kpi-header">
                    <div class="kpi-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="2" x2="12" y2="22"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                    </div>
                    <div class="kpi-title">Monthly Profit</div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value" id="monthlyProfitAmount">₱0.00</div>
                    <div class="kpi-subtitle" id="monthlyProfitChange">No change</div>
                </div>
            </div>
        </div>

        <!-- Row 2: Middle Row -->
        <div class="dashboard-row middle-row">
            <div class="widget-card top-branches-card">
                <div class="widget-header">
                    <h3 class="widget-title">Top Branches</h3>
                    <div class="widget-badge">Revenue</div>
                </div>
                <div class="widget-content">
                    <div id="topBranchesList" class="branches-list">
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-card cashier-performance-card">
                <div class="widget-header">
                    <h3 class="widget-title">Cashier Performance</h3>
                    <div class="widget-badge">Today</div>
                </div>
                <div class="widget-content">
                    <div id="cashierPerformanceList" class="performance-list">
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="widget-card alerts-card">
                <div class="widget-header">
                    <h3 class="widget-title">Alerts</h3>
                    <div class="widget-badge alert-count" id="totalAlertsCount">0</div>
                </div>
                <div class="widget-content">
                    <div id="alertsList" class="alerts-list">
                        <div class="loading-spinner">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Bottom Row -->
        <div class="dashboard-row bottom-row">
            <div class="widget-card trend-chart-card">
                <div class="widget-header">
                    <h3 class="widget-title">Sales & Profit Trend</h3>
                    <div class="trend-controls">
                        <label class="trend-option">
                            <input type="radio" name="trendType" value="sales" checked>
                            <span>Sales</span>
                        </label>
                        <label class="trend-option">
                            <input type="radio" name="trendType" value="profit">
                            <span>Profit</span>
                        </label>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="widget-card transaction-summary-card">
                <div class="widget-header">
                    <h3 class="widget-title">Transaction Summary</h3>
                    <div class="widget-badge">Today</div>
                </div>
                <div class="widget-content">
                    <div class="summary-stats">
                        <div class="summary-item">
                            <div class="summary-label">Total Transactions</div>
                            <div class="summary-value" id="totalTransactions">0</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Average Value</div>
                            <div class="summary-value" id="avgTransactionValue">₱0.00</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Highest Sale</div>
                            <div class="summary-value" id="highestSaleToday">₱0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Utility functions
  function viewCashierDetails(cashierId) {
    window.location.href = '/superadmin/users/' + cashierId;
  }
  
  function getInitials(name) {
    return name.split(' ').map(word => word.charAt(0).toUpperCase()).join('').slice(0, 2);
  }
  
  function peso(amount) {
    return '₱' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2 });
  }

  const isDebug = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
  function debugLog(...args) { if (isDebug) console.log(...args); }
  function debugError(...args) { if (isDebug) console.error(...args); }

  let trendChartInstance = null;
  let currentTrendType = 'sales';
  let dashboardRefreshInterval = null;
  
  function $(id) { return document.getElementById(id); }
  
  function qs(root, selector) {
    if (!root || !root.querySelector) return null;
    try { return root.querySelector(selector); } catch (_) { return null; }
  }
  
  function qsById(id) { return document.getElementById(id); }
  
  function qsIn(widgetId, selectorOrId) {
    const root = qsById(widgetId);
    if (!root) return null;
    if (!selectorOrId) return null;
    const sel = selectorOrId.startsWith('#') || selectorOrId.startsWith('.') ? selectorOrId : '#${selectorOrId}';
    return qs(root, sel);
  }
  
  function isAttached(el) { return !!(el && document.contains(el)); }
  
  function waitForEl(selector, { timeout = 5000, root = document } = {}) {
    const start = performance.now();
    return new Promise((resolve, reject) => {
      const check = () => {
        const el = root.querySelector(selector);
        if (el) return resolve(el);
        if (performance.now() - start > timeout) return reject(new Error(Timeout waiting for ${selector}));
        requestAnimationFrame(check);
      };
      check();
    });
  }
  
  function debounce(fn, wait = 400) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), wait); };
  }
  
  function initDashboard() {
    debugLog('🚀 Initializing electric dashboard...');
    initTrendChart();
    fetchDashboardData().then(updateDashboardUI);
    fetchMonthlySalesData().then(updateMonthlySalesUI);
    fetchMonthlyExpensesData().then(updateMonthlyExpensesUI);
    fetchMonthlyProfitData().then(updateMonthlyProfitUI);
    fetchMonthlyReturnsData().then(updateMonthlyReturnsUI);
    dashboardRefreshInterval = setInterval(() => {
      fetchDashboardData().then(updateDashboardUI);
      fetchMonthlySalesData().then(updateMonthlySalesUI);
      fetchMonthlyExpensesData().then(updateMonthlyExpensesUI);
      fetchMonthlyProfitData().then(updateMonthlyProfitUI);
      fetchMonthlyReturnsData().then(updateMonthlyReturnsUI);
    }, 60000);
    debugLog('✅ Electric dashboard initialized');
  }
  
  function fetchDashboardData() {
    debugLog('📊 Fetching dashboard data...');
    return fetch('/dashboard/widgets', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(HTTP error! status: ${response.status});
      return response.json();
    })
    .then(data => {
      debugLog('✅ Dashboard data received:', data);
      return data;
    })
    .catch(error => {
      debugError('❌ Error fetching dashboard data:', error);
      return null;
    });
  }

  function fetchMonthlyExpensesData() {
    debugLog('📈 Fetching monthly expenses data...');
    return fetch('/dashboard/monthly-expenses', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(HTTP error! status: ${response.status});
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly expenses data received:', data);
      return data;
    })
    .catch(error => {
      debugError('❌ Error fetching monthly expenses data:', error);
      return null;
    });
  }
  
  function fetchMonthlySalesData() {
    debugLog('📈 Fetching monthly sales data...');
    return fetch('/dashboard/monthly-sales', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(HTTP error! status: ${response.status});
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly sales data received:', data);
      debugLog('🔢 Total sales value:', data.total_sales);
      return data;
    })
    .catch(error => {
      debugError('❌ Error fetching monthly sales data:', error);
      return null;
    });
  }
  
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
        sales: { amount: kpisSrc.sales?.amount ?? 0, transactions: kpisSrc.sales?.transactions ?? 0, change: kpisSrc.sales?.change ?? 0 },
        profit: { amount: kpisSrc.profit?.amount ?? 0, isPositive: !!kpisSrc.profit?.isPositive },
        expenses: { amount: kpisSrc.expenses?.amount ?? 0, biggestCategory: kpisSrc.expenses?.biggestCategory || null },
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

  function updateDashboardUI(raw) {
    const data = normalizeDashboardResponse(raw);
    if (!data) { debugLog('⚠️ No data to update UI'); return; }
    debugLog('🔄 Updating dashboard UI...');

    if (data.kpis) updateTodayKPIs(data.kpis);
    if (data.alerts) updateAlertsPanel(data.alerts);
    if (data.topProducts) updateTopProducts(data.topProducts);
    if (data.topBranches) updateTopBranches(data.topBranches);
    if (data.cashierPerformance) updateCashierPerformance(data.cashierPerformance);
    if (data.transactionSummary) updateTransactionSummary(data.transactionSummary);
    if (data.alerts) updateUnusualActivities(data.alerts);

    fetchChartData().then(chartData => { if (chartData) updateTrendChart(chartData); });
    debugLog('✅ Dashboard UI updated');
  }

  function fetchMonthlyProfitData() {
    debugLog('📈 Fetching monthly profit data...');
    return fetch('/dashboard/monthly-profit', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(HTTP error! status: ${response.status});
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly profit data received:', data);
      return data;
    })
    .catch(error => {
      debugError('❌ Error fetching monthly profit data:', error);
      return null;
    });
  }

  function fetchMonthlyReturnsData() {
    debugLog('📈 Fetching returns/refunds data...');
    return fetch('/dashboard/monthly-returns', {      
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(HTTP error! status: ${response.status});
      return response.json();
    })
    .then(data => {
      debugLog('✅ Returns/Refunds data received:', data);
      return data;
    })
    .catch(error => {
      debugError('❌ Error fetching returns/refunds data:', error);
      return null;
    });
  }

  function updateMonthlyReturnsUI(data) {
    if (!data) { debugLog('⚠️ No returns/refunds data to update UI'); return; }
    debugLog('🔄 Updating returns/refunds UI...');

    const monthlyReturnsElement = document.getElementById('monthlyReturnsAmount');
    const monthlyReturnsChangeElement = document.getElementById('monthlyReturnsChange');

    if (monthlyReturnsElement) {
      monthlyReturnsElement.textContent = '₱' + data.total_returns.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    if (monthlyReturnsChangeElement) {
      const changePercent = data.returns_change_percentage;
      const changeText = changePercent > 0 ? ↑ ${changePercent}% from last month : 
                        changePercent < 0 ? ↓ ${Math.abs(changePercent)}% from last month : 
                        'No change from last month';
      monthlyReturnsChangeElement.textContent = changeText;
      monthlyReturnsChangeElement.style.color = changePercent > 0 ? 'var(--profit-positive)' : 
                                          changePercent < 0 ? 'var(--profit-negative)' : 
                                          'var(--inactive-text)';
    }

    debugLog('✅ Returns/Refunds UI updated');
  }

  function updateMonthlyProfitUI(data) {
    if (!data) { debugLog('⚠️ No monthly profit data to update UI'); return; }
    debugLog('🔄 Updating monthly profit UI...');

    const monthlyProfitElement = document.getElementById('monthlyProfitAmount');
    const monthlyProfitChangeElement = document.getElementById('monthlyProfitChange');

    if (monthlyProfitElement) {
      monthlyProfitElement.textContent = '₱' + data.net_profit.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      monthlyProfitElement.className = data.net_profit >= 0 ? 'kpi-value profit-positive' : 'kpi-value profit-negative';
    }

    if (monthlyProfitChangeElement) {
      const changePercent = data.profit_change_percentage;
      const changeText = changePercent > 0 ? ↑ ${changePercent}% from last month : 
                        changePercent < 0 ? ↓ ${Math.abs(changePercent)}% from last month : 
                        'No change from last month';
      monthlyProfitChangeElement.textContent = changeText;
      monthlyProfitChangeElement.style.color = changePercent > 0 ? 'var(--profit-positive)' : 
                                          changePercent < 0 ? 'var(--profit-negative)' : 
                                          'var(--inactive-text)';
    }

    // Debug: Log profit breakdown
    debugLog('📊 Monthly Profit Breakdown:', {
      'Total Sales': '₱' + data.total_sales.toFixed(2),
      'COGS': '₱' + data.cogs.toFixed(2),
      'Gross Profit': '₱' + data.gross_profit.toFixed(2),
      'Operating Expenses': '₱' + data.total_expenses.toFixed(2),
      'Net Profit': '₱' + data.net_profit.toFixed(2)
    });

    debugLog('✅ Monthly profit UI updated');
  }

  function updateMonthlyExpensesUI(data) {
    if (!data) { debugLog('⚠️ No monthly expenses data to update UI'); return; }
    debugLog('🔄 Updating monthly expenses UI...');

    const monthlyExpensesElement = document.getElementById('monthlyExpensesAmount');

    if (monthlyExpensesElement) {
      monthlyExpensesElement.textContent = '₱' + data.total_expenses.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    debugLog('✅ Monthly expenses UI updated');
  }

  function updateMonthlySalesUI(data) {
    if (!data) { debugLog('⚠️ No monthly sales data to update UI'); return; }
    debugLog('🔄 Updating monthly sales UI...', data);

    const monthlySalesElement = document.getElementById('monthlySalesAmount');
    const monthlyChangeElement = document.getElementById('monthlySalesChange');

    debugLog('🔍 Found elements:', {
      monthlySalesElement: !!monthlySalesElement,
      monthlyChangeElement: !!monthlyChangeElement,
      total_sales: data.total_sales
    });

    if (monthlySalesElement) {
      const newValue = '₱' + data.total_sales.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      monthlySalesElement.textContent = newValue;
      debugLog('✅ Updated monthly sales amount to:', newValue);
    } else {
      debugLog('❌ monthlySalesAmount element not found');
    }

    if (monthlyChangeElement) {
      const changePercent = data.sales_change_percentage;
      const changeText = changePercent > 0 ? ↑ ${changePercent}% from last month : 
                        changePercent < 0 ? ↓ ${Math.abs(changePercent)}% from last month : 
                        'No change from last month';
      monthlyChangeElement.textContent = changeText;
      monthlyChangeElement.style.color = changePercent > 0 ? 'var(--profit-positive)' : 
                                          changePercent < 0 ? 'var(--danger-red)' : 
                                          'var(--inactive-text)';
      debugLog('✅ Updated monthly sales change to:', changeText);
    } else {
      debugLog('❌ monthlySalesChange element not found');
    }

    debugLog('✅ Monthly sales UI updated');
  }
  
  function initTrendChart() {
    const canvas = document.getElementById('trendChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    if (!ctx) return;
    
    if (trendChartInstance) {
      try { trendChartInstance.destroy(); } catch (_) {}
      trendChartInstance = null;
    }
    
    trendChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          label: 'Sales',
          data: [],
          borderColor: '#2196F3',
          backgroundColor: 'rgba(33, 150, 243, 0.1)',
          tension: 0.4,
          borderWidth: 3
        }, {
          label: 'Profit',
          data: [],
          borderColor: '#00E5FF',
          backgroundColor: 'rgba(0, 229, 255, 0.1)',
          tension: 0.4,
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { 
            position: 'top',
            labels: { boxWidth: 20, padding: 20, color: '#263238', font: { weight: 600 } }
          }
        },
        scales: {
          y: { 
            beginAtZero: true,
            ticks: { callback: function(value) { return '₱' + value.toLocaleString(); }, color: '#546E7A' },
            grid: { color: 'rgba(13, 71, 161, 0.1)' }
          },
          x: {
            ticks: { color: '#546E7A' },
            grid: { color: 'rgba(13, 71, 161, 0.1)' }
          }
        }
      }
    });
    
    fetchChartData().then(chartData => {
      if (chartData) updateTrendChart(chartData);
    }).catch(() => {});
  }

  async function fetchChartData(type = currentTrendType || 'sales') {
    try {
      const res = await fetch(`/dashboard/chart-data?type=${encodeURIComponent(type)}`, {
        method: 'GET', 
        headers: { 
          'Accept': 'application/json', 
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
      });
      if (!res.ok) throw new Error(HTTP ${res.status});
      return await res.json();
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
  
  function updateAlertsPanel(alerts) {
    debugLog('🚨 Updating alerts panel with data:', alerts);
    const alertsList = $('alertsList');
    const totalAlertsElement = $('totalAlertsCount');
    
    if (totalAlertsElement) {
      const totalAlerts = alerts.outOfStock + alerts.negativeProfit + alerts.voidedSales + alerts.belowCostSales + alerts.highDiscountUsage;
      totalAlertsElement.textContent = totalAlerts;
    }
    
    if (alertsList) {
      const alertItems = [];
      
      if (alerts.outOfStock > 0) {
        alertItems.push(`<div class="alert-item critical clickable" onclick="window.location.href='/superadmin/inventory?filter=out-of-stock'"><i class="fas fa-exclamation-triangle alert-icon" style="color:#E91E63"></i><div class="alert-content"><div class="alert-title">${alerts.outOfStock} items out of stock</div><div class="alert-description">Restock needed</div></div></div>`);
      }
      
      if (alerts.negativeProfit > 0) {
        alertItems.push(`<div class="alert-item warning clickable" onclick="window.location.href='/superadmin/sales'"><i class="fas fa-arrow-trend-down alert-icon" style="color:#C6FF00"></i><div class="alert-content"><div class="alert-title">${alerts.negativeProfit} items sold below cost</div><div class="alert-description">Review pricing</div></div></div>`);
      }
      
      if (alerts.voidedSales > 0) {
        alertItems.push(`<div class="alert-item info clickable" onclick="window.location.href='/superadmin/sales'"><i class="fas fa-ban alert-icon" style="color:#00E5FF"></i><div class="alert-content"><div class="alert-title">${alerts.voidedSales} voided sales today</div><div class="alert-description">Monitor activity</div></div></div>`);
      }
      
      if (alerts.belowCostSales > 0) {
        alertItems.push(`<div class="alert-item warning clickable" onclick="window.location.href='/superadmin/sales'"><i class="fas fa-exclamation-triangle alert-icon" style="color:#C6FF00"></i><div class="alert-content"><div class="alert-title">${alerts.belowCostSales} items sold below cost</div><div class="alert-description">Check margins</div></div></div>`);
      }
      
      if (alerts.highDiscountUsage > 0) {
        alertItems.push(`<div class="alert-item info clickable" onclick="window.location.href='/superadmin/sales?filter=below-price'"><i class="fas fa-percentage alert-icon" style="color:#2196F3"></i><div class="alert-content"><div class="alert-title">${alerts.highDiscountUsage} high discount transactions</div><div class="alert-description">Review approvals</div></div></div>`);
      }
      
      if (alerts.belowPrice > 0) {
        alertItems.push(`<div class="alert-item warning clickable" onclick="window.location.href='/superadmin/sales?filter=below-price'"><i class="fas fa-arrow-trend-down alert-icon" style="color:#C6FF00"></i><div class="alert-content"><div class="alert-title">${alerts.belowPrice} products sold below cost</div><div class="alert-description">Review pricing strategy</div></div></div>`);
      }
      
      alertsList.innerHTML = alertItems.length > 0 ? alertItems.join('') : '<div class="alert-item" style="border-left-color:#43A047;background:rgba(67,160,71,0.05)"><i class="fas fa-check-circle alert-icon" style="color:#43A047"></i><div class="alert-content"><div class="alert-title">No alerts</div><div class="alert-description">All systems normal</div></div></div>';
    }
  }
  
  function updateTodayKPIs(kpis) {
    debugLog('📊 Updating KPIs with data:', kpis);
    
    const salesAmount = $('todaySalesAmount');
    if (salesAmount) salesAmount.textContent = peso(kpis.sales.amount);
    
    const salesTransactions = $('todaySalesTransactions');
    if (salesTransactions) salesTransactions.textContent = kpis.sales.transactions + ' transactions';
    
    const profitElement = $('todayProfitAmount');
    if (profitElement) {
      profitElement.textContent = peso(kpis.profit.amount);
      profitElement.className = 'kpi-value ' + (kpis.profit.isPositive ? 'profit-positive' : 'profit-negative');
    }
    
    const expensesAmount = $('todayExpensesAmount');
    if (expensesAmount) expensesAmount.textContent = peso(kpis.expenses.amount);
    
    const cashOnHandElement = $('cashOnHandAmount');
    if (cashOnHandElement && kpis.cashOnHand) {
      cashOnHandElement.textContent = peso(kpis.cashOnHand.amount);
    }
    
    debugLog('✅ KPI update completed');
  }
  
  function updateTopProducts(products) {
    debugLog('🏆 Updating top products with data:', products);
    const productsList = $('topProductsList');
    if (!productsList) return;
    if (!products || products.length === 0) {
      productsList.innerHTML = '<div class="text-center text-muted py-4">No sales data available</div>';
      return;
    }
    productsList.innerHTML = products.map((product, index) => {
      const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
      return '<div class="top-item clickable" onclick="window.location.href=\'/superadmin/products/' + product.id + '\'">' +
          '<div class="top-rank ' + rankClass + '">' + (index + 1) + '</div>' +
          '<div class="top-content">' +
            '<div class="top-name">' + product.product_name + '</div>' +
            '<div class="top-metrics">' +
              '<div class="top-value">' + peso(product.revenue) + '</div>' +
              '<div class="top-subtitle">' + product.contribution_percent + '% of total</div>' +
            '</div>' +
          '</div>' +
        '</div>';
    }).join('');
  }
  
  function updateTopBranches(branches) {
    debugLog('🏢 Updating top branches with data:', branches);
    console.log('Branches data:', branches);
    const branchesList = $('topBranchesList');
    if (!branchesList) return;
    if (!branches || branches.length === 0) {
      branchesList.innerHTML = '<div class="text-center text-muted py-4">No branch data available</div>';
      return;
    }
    branchesList.innerHTML = branches.map((branch, index) => {
      console.log('Branch item:', branch);
      const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
      const branchId = branch.branch_id || branch.id || 'unknown';
      console.log('Branch ID:', branchId, 'Branch Name:', branch.branch_name);
      return '<div class="top-item clickable" data-branch-id="' + branchId + '">' +
          '<div class="top-rank ' + rankClass + '">' + (index + 1) + '</div>' +
          '<div class="top-content">' +
            '<div class="top-name">' + branch.branch_name + '</div>' +
            '<div class="top-metrics">' +
              '<div class="top-value">' + peso(branch.revenue) + '</div>' +
              '<div class="top-subtitle">' + branch.profit_margin + '% margin</div>' +
            '</div>' +
          '</div>' +
        '</div>';
  }).join('');

  // Add event listeners to branch items
  document.querySelectorAll('.top-item[data-branch-id]').forEach(item => {
    item.addEventListener('click', function() {
      const branchId = this.getAttribute('data-branch-id');
      console.log('Clicked branch ID:', branchId);
      window.location.href = '/superadmin/branches/' + branchId;
    });
  });
}

function updateCashierPerformance(cashiers) {
  debugLog('💰 Updating cashier performance with data:', cashiers);
  const performanceList = $('cashierPerformanceList');
  if (!performanceList) return;
  if (!cashiers || cashiers.length === 0) {
    performanceList.innerHTML = '<div class="text-center text-muted py-4">No cashier data available</div>';
    return;
  }
  performanceList.innerHTML = cashiers.map(cashier => `
    <div class="performance-item clickable" onclick="viewCashierDetails('${cashier.id}')">
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

  // Add event listeners to make items clickable
  try {
    document.querySelectorAll('.performance-item[data-cashier-id]').forEach(item => {
      item.addEventListener('click', function() {
        const cashierId = this.getAttribute('data-cashier-id');
        console.log('Clicked cashier ID:', cashierId);
        viewCashierDetails(cashierId);
      });
    });
  } catch (e) {
    console.error('Error adding click listeners:', e);
  }
}

function updateTransactionSummary(summary) {
  debugLog('📋 Updating transaction summary with data:', summary);
  if (!summary) return;
  
  const totalTransactionsEl = $('totalTransactions');
  const avgTransactionEl = $('avgTransactionValue');
  const highestSaleEl = $('highestSaleToday');
  
  if (totalTransactionsEl) totalTransactionsEl.textContent = summary.totalTransactions;
  if (avgTransactionEl) avgTransactionEl.textContent = peso(summary.avgTransactionValue);
  if (highestSaleEl) highestSaleEl.textContent = peso(summary.highestSaleToday);
}
  
  function updateUnusualActivities(alerts) {
    debugLog('⚠️ Updating unusual activities with data:', alerts);
    const unusualCountEl = $('unusualActivitiesCount');
    if (unusualCountEl) {
      const unusualCount = alerts.belowCostSales + alerts.highDiscountUsage;
      unusualCountEl.textContent = unusualCount;
    }
  }

  function makeChartResponsive() {
    if (trendChartInstance) {
      const isMobile = window.innerWidth < 768;
      trendChartInstance.options.plugins.legend.position = isMobile ? 'bottom' : 'top';
      trendChartInstance.options.plugins.legend.labels.boxWidth = isMobile ? 12 : 20;
      trendChartInstance.options.plugins.legend.labels.padding = isMobile ? 10 : 20;
      trendChartInstance.options.scales.y.ticks.callback = function(value) {
        return isMobile ? '₱' + (value/1000) + 'k' : '₱' + value.toLocaleString();
      };
      trendChartInstance.update();
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
    
    let resizeTimeout;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(makeChartResponsive, 250);
    });
    
    document.querySelectorAll('input[name="trendType"]').forEach(radio => {
      radio.addEventListener('change', function() {
        currentTrendType = this.value;
        fetchChartData().then(chartData => {
          if (chartData) updateTrendChart(chartData);
        });
      });
  });

  });

  let branchSalesPieChartInstance = null;

  function showTodaySalesModal() {
    debugLog('📊 Opening today\'s sales modal...');
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('todaySalesModal'));
    modal.show();
    
    // Fetch branch sales data
    fetchBranchSalesData();
  }

  function fetchBranchSalesData() {
    debugLog('📈 Fetching branch sales data...');
    
    return fetch('/dashboard/branch-sales-today', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      debugLog('✅ Branch sales data received:', data);
      updateBranchSalesModal(data);
    })
    .catch(error => {
      debugError('❌ Error fetching branch sales data:', error);
      document.getElementById('branchSalesList').innerHTML = 
        '<div class="text-center text-danger">Error loading branch sales data</div>';
    });
  }

  function updateBranchSalesModal(data) {
    if (!data || !data.branches) {
      document.getElementById('branchSalesList').innerHTML = 
        '<div class="text-center text-muted">No branch sales data available</div>';
      return;
    }

    // Update branch list
    const branchListHtml = data.branches.map((branch, index) => {
      const initials = getInitials(branch.branch_name || 'Unknown');
      return `
        <div class="branch-sales-item">
          <div class="branch-info">
            <div class="branch-icon">${initials}</div>
            <div class="branch-name">${branch.branch_name}</div>
          </div>
          <div class="branch-amount">${peso(branch.total_sales)}</div>
        </div>
      `;
    }).join('');
    
    document.getElementById('branchSalesList').innerHTML = branchListHtml;
    
    // Update summary
    document.getElementById('modalTotalSales').textContent = peso(data.total_sales || 0);
    document.getElementById('modalTotalTransactions').textContent = data.total_transactions || 0;
    document.getElementById('modalTotalBranches').textContent = data.branch_count || 0;
    
    // Create pie chart
    createBranchSalesPieChart(data.branches);
  }

  function createBranchSalesPieChart(branches) {
    const ctx = document.getElementById('branchSalesPieChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (branchSalesPieChartInstance) {
      branchSalesPieChartInstance.destroy();
    }

    const chartData = {
      labels: branches.map(b => b.branch_name),
      datasets: [{
        data: branches.map(b => parseFloat(b.total_sales || 0)),
        backgroundColor: [
          '#00A896', // Teal
          '#028090', // Blue-Teal  
          '#05668D', // Deep Blue
          '#02C39A', // Mint Green
          '#F0F3BD', // Soft Yellow
          '#E63946', // Red
          '#2196F3', // Blue
          '#4CAF50', // Green
        ],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    };

    branchSalesPieChartInstance = new Chart(ctx, {
      type: 'pie',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              usePointStyle: true,
              font: {
                size: 11
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return label + ': ₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 }) + ' (' + percentage + '%)';
              }
            }
          }
        }
      }
    });
  }

  let monthlyExpensesChartInstance = null;

  function showMonthlyExpensesModal() {
    debugLog('📊 Opening monthly expenses modal...');
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('monthlyExpensesModal'));
    modal.show();
    
    // Fetch monthly expenses data
    fetchMonthlyExpensesModalData();
  }

  function fetchMonthlyExpensesModalData() {
    debugLog('📈 Fetching monthly expenses data...');
    
    return fetch('/dashboard/monthly-expenses-breakdown', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly expenses data received:', data);
      updateMonthlyExpensesModal(data);
    })
    .catch(error => {
      debugError('❌ Error fetching monthly expenses data:', error);
      document.getElementById('monthlyExpensesList').innerHTML = 
        '<div class="text-center text-danger">Error loading monthly expenses data</div>';
    });
  }

  function updateMonthlyExpensesModal(data) {
    if (!data || !data.monthly_expenses) {
      document.getElementById('monthlyExpensesList').innerHTML = 
        '<div class="text-center text-muted">No monthly expenses data available</div>';
      return;
    }

    // Update monthly expenses list
    const monthlyExpensesHtml = data.monthly_expenses.map((month, index) => {
      const initials = getInitials(month.month_name || 'Unknown');
      return `
        <div class="monthly-expenses-item">
          <div class="monthly-expenses-info">
            <div class="monthly-expenses-icon">${initials}</div>
            <div class="monthly-expenses-name">${month.month_name}</div>
          </div>
          <div class="monthly-expenses-amount">${peso(month.total_expenses)}</div>
        </div>
      `;
    }).join('');
    
    document.getElementById('monthlyExpensesList').innerHTML = monthlyExpensesHtml;
    
    // Update summary
    document.getElementById('modalTotalMonthlyExpenses').textContent = peso(data.total_expenses || 0);
    document.getElementById('modalAverageMonthlyExpenses').textContent = peso(data.average_monthly_expenses || 0);
    document.getElementById('modalHighestMonthExpenses').textContent = peso(data.highest_month_expenses || 0);
    
    // Create pie chart
    createMonthlyExpensesChart(data.monthly_expenses);
  }

  function createMonthlyExpensesChart(monthlyData) {
    const ctx = document.getElementById('monthlyExpensesChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (monthlyExpensesChartInstance) {
      monthlyExpensesChartInstance.destroy();
    }

    const chartData = {
      labels: monthlyData.map(m => m.month_name),
      datasets: [{
        label: 'Monthly Expenses',
        data: monthlyData.map(m => parseFloat(m.total_expenses || 0)),
        backgroundColor: 'rgba(255, 152, 0, 0.1)',
        borderColor: '#ff9800',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#ff9800',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    };

    monthlyExpensesChartInstance = new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                return label + ': ₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 });
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString('en-PH');
              },
              color: '#546E7A'
            },
            grid: {
              color: 'rgba(255, 152, 0, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#546E7A',
              maxRotation: 45,
              minRotation: 45
            },
            grid: {
              color: 'rgba(255, 152, 0, 0.1)'
            }
          }
        }
      }
    });
  }

  let monthlyProfitChartInstance = null;

  function showMonthlyProfitModal() {
    debugLog('📊 Opening monthly profit modal...');
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('monthlyProfitModal'));
    modal.show();
    
    // Fetch monthly profit data
    fetchMonthlyProfitModalData();
  }

  function fetchMonthlyProfitModalData() {
    debugLog('📈 Fetching monthly profit data...');
    
    return fetch('/dashboard/monthly-profit-breakdown', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly profit data received:', data);
      updateMonthlyProfitModal(data);
    })
    .catch(error => {
      debugError('❌ Error fetching monthly profit data:', error);
      document.getElementById('monthlyProfitList').innerHTML = 
        '<div class="text-center text-danger">Error loading monthly profit data</div>';
    });
  }

  function updateMonthlyProfitModal(data) {
    if (!data || !data.monthly_profit) {
      document.getElementById('monthlyProfitList').innerHTML = 
        '<div class="text-center text-muted">No monthly profit data available</div>';
      return;
    }

    // Update monthly profit list
    const monthlyProfitHtml = data.monthly_profit.map((month, index) => {
      const initials = getInitials(month.month_name || 'Unknown');
      return `
        <div class="monthly-profit-item">
          <div class="monthly-profit-info">
            <div class="monthly-profit-icon">${initials}</div>
            <div class="monthly-profit-name">${month.month_name}</div>
          </div>
          <div class="monthly-profit-amount">${peso(month.net_profit)}</div>
        </div>
      `;
    }).join('');
    
    document.getElementById('monthlyProfitList').innerHTML = monthlyProfitHtml;
    
    // Update summary
    document.getElementById('modalTotalProfit').textContent = peso(data.total_profit || 0);
    document.getElementById('modalAverageProfit').textContent = peso(data.average_monthly_profit || 0);
    document.getElementById('modalBestProfit').textContent = peso(data.best_month_profit || 0);
    
    // Create line chart
    createMonthlyProfitChart(data.monthly_profit);
  }

  function createMonthlyProfitChart(monthlyData) {
    const ctx = document.getElementById('monthlyProfitChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (monthlyProfitChartInstance) {
      monthlyProfitChartInstance.destroy();
    }

    const chartData = {
      labels: monthlyData.map(m => m.month_name),
      datasets: [{
        label: 'Monthly Profit',
        data: monthlyData.map(m => parseFloat(m.net_profit || 0)),
        backgroundColor: 'rgba(67, 160, 71, 0.1)',
        borderColor: 'var(--success-teal)',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: 'var(--success-teal)',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    };

    monthlyProfitChartInstance = new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                return label + ': ₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 });
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString('en-PH');
              },
              color: '#546E7A'
            },
            grid: {
              color: 'rgba(67, 160, 71, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#546E7A',
              maxRotation: 45,
              minRotation: 45
            },
            grid: {
              color: 'rgba(67, 160, 71, 0.1)'
            }
          }
        }
      }
    });
  }

  let monthlyReturnsChartInstance = null;

  function showMonthlyReturnsModal() {
    debugLog('📊 Opening monthly returns modal...');
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('monthlyReturnsModal'));
    modal.show();
    
    // Fetch monthly returns data
    fetchMonthlyReturnsModalData();
  }

  function fetchMonthlyReturnsModalData() {
    debugLog('📈 Fetching monthly returns data...');
    
    return fetch('/dashboard/monthly-returns-breakdown', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly returns data received:', data);
      updateMonthlyReturnsModal(data);
    })
    .catch(error => {
      debugError('❌ Error fetching monthly returns data:', error);
      document.getElementById('monthlyReturnsList').innerHTML = 
        '<div class="text-center text-danger">Error loading monthly returns data</div>';
    });
  }

  function updateMonthlyReturnsModal(data) {
    if (!data || !data.monthly_returns) {
      document.getElementById('monthlyReturnsList').innerHTML = 
        '<div class="text-center text-muted">No monthly returns data available</div>';
      return;
    }

    // Update monthly returns list
    const monthlyReturnsHtml = data.monthly_returns.map((month, index) => {
      const initials = getInitials(month.month_name || 'Unknown');
      return `
        <div class="monthly-returns-item">
          <div class="monthly-returns-info">
            <div class="monthly-returns-icon">${initials}</div>
            <div class="monthly-returns-name">${month.month_name}</div>
          </div>
          <div class="monthly-returns-amount">${peso(month.total_returns)}</div>
        </div>
      `;
    }).join('');
    
    document.getElementById('monthlyReturnsList').innerHTML = monthlyReturnsHtml;
    
    // Update summary
    document.getElementById('modalTotalReturns').textContent = peso(data.total_returns || 0);
    document.getElementById('modalAverageReturns').textContent = peso(data.average_monthly_returns || 0);
    document.getElementById('modalHighestReturns').textContent = peso(data.highest_month_returns || 0);
    
    // Create line chart
    createMonthlyReturnsChart(data.monthly_returns);
  }

  function createMonthlyReturnsChart(monthlyData) {
    const ctx = document.getElementById('monthlyReturnsChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (monthlyReturnsChartInstance) {
      monthlyReturnsChartInstance.destroy();
    }

    const chartData = {
      labels: monthlyData.map(m => m.month_name),
      datasets: [{
        label: 'Monthly Returns',
        data: monthlyData.map(m => parseFloat(m.total_returns || 0)),
        backgroundColor: 'rgba(255, 152, 0, 0.1)',
        borderColor: '#ff9800',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#ff9800',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    };

    monthlyReturnsChartInstance = new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                return label + ': ₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 });
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString('en-PH');
              },
              color: '#546E7A'
            },
            grid: {
              color: 'rgba(255, 152, 0, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#546E7A',
              maxRotation: 45,
              minRotation: 45
            },
            grid: {
              color: 'rgba(255, 152, 0, 0.1)'
            }
          }
        }
      }
    });
  }

  let expensesPieChartInstance = null;

  function showTodayExpensesModal() {
    debugLog('📊 Opening today\'s expenses modal...');
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('todayExpensesModal'));
    modal.show();
    
    // Fetch expense data
    fetchExpenseData();
  }

  function fetchExpenseData() {
    debugLog('📈 Fetching expense data...');
    
    return fetch('/dashboard/expenses-today', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      debugLog('✅ Expense data received:', data);
      updateExpensesModal(data);
    })
    .catch(error => {
      debugError('❌ Error fetching expense data:', error);
      document.getElementById('expenseCategoriesList').innerHTML = 
        '<div class="text-center text-danger">Error loading expense data</div>';
    });
  }

  function updateExpensesModal(data) {
    if (!data || !data.categories) {
      document.getElementById('expenseCategoriesList').innerHTML = 
        '<div class="text-center text-muted">No expense data available</div>';
      return;
    }

    // Update expense categories list
    const categoryListHtml = data.categories.map((category, index) => {
      const initials = getInitials(category.category_name || 'Unknown');
      return `
        <div class="expense-category-item">
          <div class="expense-info">
            <div class="expense-icon">${initials}</div>
            <div class="expense-name">${category.category_name}</div>
          </div>
          <div class="expense-amount">${peso(category.total_amount)}</div>
        </div>
      `;
    }).join('');
    
    document.getElementById('expenseCategoriesList').innerHTML = categoryListHtml;
    
    // Update summary
    document.getElementById('modalTotalExpenses').textContent = peso(data.total_expenses || 0);
    document.getElementById('modalTotalCategories').textContent = data.category_count || 0;
    document.getElementById('modalTotalExpenseTransactions').textContent = data.transaction_count || 0;
    
    // Create pie chart
    createExpensesPieChart(data.categories);
  }

  function createExpensesPieChart(categories) {
    const ctx = document.getElementById('expensesPieChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (expensesPieChartInstance) {
      expensesPieChartInstance.destroy();
    }

    const chartData = {
      labels: categories.map(c => c.category_name),
      datasets: [{
        data: categories.map(c => parseFloat(c.total_amount || 0)),
        backgroundColor: [
          '#E63946', // Red
          '#F0F3BD', // Soft Yellow
          '#05668D', // Deep Blue
          '#028090', // Blue-Teal  
          '#00A896', // Teal
          '#02C39A', // Mint Green
          '#2196F3', // Blue
          '#4CAF50', // Green
        ],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    };

    expensesPieChartInstance = new Chart(ctx, {
      type: 'pie',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              usePointStyle: true,
              font: {
                size: 11
              }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return label + ': ₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 }) + ' (' + percentage + '%)';
              }
            }
          }
        }
      }
    });
  }

  let monthlySalesChartInstance = null;

  function showMonthlySalesModal() {
    debugLog('📊 Opening monthly sales modal...');
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('monthlySalesModal'));
    modal.show();
    
    // Fetch monthly sales data
    fetchMonthlySalesModalData();
  }

  function fetchMonthlySalesModalData() {
    debugLog('📈 Fetching monthly sales data...');
    
    return fetch('/dashboard/monthly-sales-breakdown', {
      method: 'GET',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest', 
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    })
    .then(response => {
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      debugLog('✅ Monthly sales data received:', data);
      updateMonthlySalesModal(data);
    })
    .catch(error => {
      debugError('❌ Error fetching monthly sales data:', error);
      document.getElementById('monthlySalesList').innerHTML = 
        '<div class="text-center text-danger">Error loading monthly sales data</div>';
    });
  }

  function updateMonthlySalesModal(data) {
    if (!data || !data.monthly_sales) {
      document.getElementById('monthlySalesList').innerHTML = 
        '<div class="text-center text-muted">No monthly sales data available</div>';
      return;
    }

    // Update monthly sales list
    const monthlySalesHtml = data.monthly_sales.map((month, index) => {
      const initials = getInitials(month.month_name || 'Unknown');
      return `
        <div class="monthly-sales-item">
          <div class="monthly-sales-info">
            <div class="monthly-sales-icon">${initials}</div>
            <div class="monthly-sales-name">${month.month_name}</div>
          </div>
          <div class="monthly-sales-amount">${peso(month.total_sales)}</div>
        </div>
      `;
    }).join('');
    
    document.getElementById('monthlySalesList').innerHTML = monthlySalesHtml;
    
    // Update summary
    document.getElementById('modalTotalMonthlySales').textContent = peso(data.total_sales || 0);
    document.getElementById('modalAverageMonthlySales').textContent = peso(data.average_monthly_sales || 0);
    document.getElementById('modalBestMonthSales').textContent = peso(data.best_month_sales || 0);
    
    // Create bar chart
    createMonthlySalesChart(data.monthly_sales);
  }

  function createMonthlySalesChart(monthlyData) {
    const ctx = document.getElementById('monthlySalesChart');
    if (!ctx) return;

    // Destroy existing chart if it exists
    if (monthlySalesChartInstance) {
      monthlySalesChartInstance.destroy();
    }

    const chartData = {
      labels: monthlyData.map(m => m.month_name),
      datasets: [{
        label: 'Monthly Sales',
        data: monthlyData.map(m => parseFloat(m.total_sales || 0)),
        backgroundColor: '#00A896', // Teal
        borderColor: '#00A896',
        borderWidth: 2
      }]
    };

    monthlySalesChartInstance = new Chart(ctx, {
      type: 'bar',
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                const label = context.label || '';
                const value = context.parsed || 0;
                return label + ': ₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2 });
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString('en-PH');
              },
              color: '#546E7A'
            },
            grid: {
              color: 'rgba(13, 71, 161, 0.1)'
            }
          },
          x: {
            ticks: {
              color: '#546E7A'
            },
            grid: {
              color: 'rgba(13, 71, 161, 0.1)'
            }
          }
        }
      }
    });
  }
</script>

<!-- Today's Sales Modal -->
<div class="modal fade" id="todaySalesModal" tabindex="-1" aria-labelledby="todaySalesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="todaySalesModalLabel">
          <i class="fas fa-chart-line me-2"></i>Today's Sales by Branch
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Branch Sales List -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-store me-2"></i>Branch Sales</h6>
            <div id="branchSalesList" class="branch-sales-list">
              <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading branch sales data...
              </div>
            </div>
          </div>
          
          <!-- Pie Chart -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Sales Distribution</h6>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="branchSalesPieChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <small class="text-muted">Total Sales</small>
                    <div class="h5 mb-0 text-primary" id="modalTotalSales">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Total Transactions</small>
                    <div class="h5 mb-0 text-info" id="modalTotalTransactions">0</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Branches</small>
                    <div class="h5 mb-0 text-success" id="modalTotalBranches">0</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/superadmin/sales'">
          <i class="fas fa-arrow-right me-2"></i>View All Sales
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Today's Expenses Modal -->
<div class="modal fade" id="todayExpensesModal" tabindex="-1" aria-labelledby="todayExpensesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="todayExpensesModalLabel">
          <i class="fas fa-receipt me-2"></i>Today's Expenses by Category
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Expense Categories List -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-list me-2"></i>Expense Categories</h6>
            <div id="expenseCategoriesList" class="expense-categories-list">
              <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading expense data...
              </div>
            </div>
          </div>
          
          <!-- Pie Chart -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Expense Distribution</h6>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="expensesPieChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <small class="text-muted">Total Expenses</small>
                    <div class="h5 mb-0 text-danger" id="modalTotalExpenses">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Categories</small>
                    <div class="h5 mb-0 text-info" id="modalTotalCategories">0</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Transactions</small>
                    <div class="h5 mb-0 text-warning" id="modalTotalExpenseTransactions">0</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/superadmin/expenses'">
          <i class="fas fa-arrow-right me-2"></i>View All Expenses
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Sales Modal -->
<div class="modal fade" id="monthlySalesModal" tabindex="-1" aria-labelledby="monthlySalesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="monthlySalesModalLabel">
          <i class="fas fa-chart-line me-2"></i>Monthly Sales Breakdown
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Monthly Sales List -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-calendar me-2"></i>Monthly Sales by Month</h6>
            <div id="monthlySalesList" class="monthly-sales-list">
              <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading monthly sales data...
              </div>
            </div>
          </div>
          
          <!-- Bar Chart -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Sales Trend</h6>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="monthlySalesChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <small class="text-muted">Total Sales</small>
                    <div class="h5 mb-0 text-primary" id="modalTotalMonthlySales">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Average Monthly</small>
                    <div class="h5 mb-0 text-info" id="modalAverageMonthlySales">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Best Month</small>
                    <div class="h5 mb-0 text-success" id="modalBestMonthSales">₱0.00</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/superadmin/sales'">
          <i class="fas fa-arrow-right me-2"></i>View All Sales
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Expenses Modal -->
<div class="modal fade" id="monthlyExpensesModal" tabindex="-1" aria-labelledby="monthlyExpensesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="monthlyExpensesModalLabel">
          <i class="fas fa-receipt me-2"></i>Monthly Expenses Breakdown
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Monthly Expenses List -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-calendar me-2"></i>Monthly Expenses by Month</h6>
            <div id="monthlyExpensesList" class="monthly-expenses-list">
              <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading monthly expenses data...
              </div>
            </div>
          </div>
          
          <!-- Line Chart -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Expense Trend</h6>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="monthlyExpensesChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <small class="text-muted">Total Expenses</small>
                    <div class="h5 mb-0 text-danger" id="modalTotalMonthlyExpenses">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Average Monthly</small>
                    <div class="h5 mb-0 text-warning" id="modalAverageMonthlyExpenses">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Highest Month</small>
                    <div class="h5 mb-0 text-info" id="modalHighestMonthExpenses">₱0.00</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/superadmin/expenses'">
          <i class="fas fa-arrow-right me-2"></i>View All Expenses
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Returns Modal -->
<div class="modal fade" id="monthlyReturnsModal" tabindex="-1" aria-labelledby="monthlyReturnsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="monthlyReturnsModalLabel">
          <i class="fas fa-undo me-2"></i>Monthly Returns/Refunds Breakdown
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Monthly Returns List -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-calendar me-2"></i>Monthly Returns by Month</h6>
            <div id="monthlyReturnsList" class="monthly-returns-list">
              <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading monthly returns data...
              </div>
            </div>
          </div>
          
          <!-- Line Chart -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Returns Trend</h6>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="monthlyReturnsChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <small class="text-muted">Total Returns</small>
                    <div class="h5 mb-0 text-warning" id="modalTotalReturns">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Average Monthly</small>
                    <div class="h5 mb-0 text-info" id="modalAverageReturns">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Highest Month</small>
                    <div class="h5 mb-0 text-danger" id="modalHighestReturns">₱0.00</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/superadmin/refunds'">
          <i class="fas fa-arrow-right me-2"></i>View All Returns
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Profit Modal -->
<div class="modal fade" id="monthlyProfitModal" tabindex="-1" aria-labelledby="monthlyProfitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="monthlyProfitModalLabel">
          <i class="fas fa-chart-line me-2"></i>Monthly Profit Breakdown
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Monthly Profit List -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-calendar me-2"></i>Monthly Profit by Month</h6>
            <div id="monthlyProfitList" class="monthly-profit-list">
              <div class="text-center text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Loading monthly profit data...
              </div>
            </div>
          </div>
          
          <!-- Line Chart -->
          <div class="col-md-6">
            <h6 class="mb-3"><i class="fas fa-chart-line me-2"></i>Profit Trend</h6>
            <div class="chart-container" style="position: relative; height: 300px;">
              <canvas id="monthlyProfitChart"></canvas>
            </div>
          </div>
        </div>
        
        <!-- Summary -->
        <div class="row mt-3">
          <div class="col-12">
            <div class="card bg-light">
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-md-4">
                    <small class="text-muted">Total Profit</small>
                    <div class="h5 mb-0 text-success" id="modalTotalProfit">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Average Monthly</small>
                    <div class="h5 mb-0 text-info" id="modalAverageProfit">₱0.00</div>
                  </div>
                  <div class="col-md-4">
                    <small class="text-muted">Best Month</small>
                    <div class="h5 mb-0 text-primary" id="modalBestProfit">₱0.00</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="window.location.href='/superadmin/reports'">
          <i class="fas fa-arrow-right me-2"></i>View All Reports
        </button>
      </div>
    </div>
  </div>
</div>

@endpush
