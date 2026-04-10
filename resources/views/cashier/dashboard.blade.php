@extends('layouts.app')
@section('title', 'Cashier Dashboard')

@push('stylesDashboard')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @font-face { font-family:'Geist'; src:url('https://cdn.jsdelivr.net/npm/geist@1.3.0/dist/fonts/geist-sans/Geist-Regular.woff2') format('woff2'); font-weight:400; font-display:swap; }
        @font-face { font-family:'Geist'; src:url('https://cdn.jsdelivr.net/npm/geist@1.3.0/dist/fonts/geist-sans/Geist-Medium.woff2') format('woff2'); font-weight:500; font-display:swap; }
        @font-face { font-family:'Geist'; src:url('https://cdn.jsdelivr.net/npm/geist@1.3.0/dist/fonts/geist-sans/Geist-SemiBold.woff2') format('woff2'); font-weight:600; font-display:swap; }
        @font-face { font-family:'Geist'; src:url('https://cdn.jsdelivr.net/npm/geist@1.3.0/dist/fonts/geist-sans/Geist-Bold.woff2') format('woff2'); font-weight:700; font-display:swap; }

        aside#sidebar,
        aside.sidebar-fixed {
            display: none !important;
        }

        main.main-content {
            margin-left: 0 !important;
        }

        /* ══════════════════════════════════════════
           BACKGROUND REDESIGN — BGH Blue Mesh
           Everything below this block is unchanged
        ══════════════════════════════════════════ */
        .cashier-dashboard-page {
            font-family: 'Geist', 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.02em;
            color: #0C0F1A;
            min-height: calc(100vh - 1px);
            position: relative;
            overflow-x: hidden;

            /* BGH blue gradient mesh background */
            background:
                radial-gradient(ellipse 70% 55% at 0% 0%,    rgba(13,71,161,0.13) 0%, transparent 55%),
                radial-gradient(ellipse 55% 45% at 100% 100%, rgba(0,176,255,0.10) 0%, transparent 52%),
                radial-gradient(ellipse 40% 35% at 60% 15%,  rgba(66,165,245,0.07) 0%, transparent 50%),
                #EBF3FB;
        }

        /* Animated floating blobs — replaces old grid dots */
        .cashier-dashboard-page::before {
            content: '';
            position: fixed;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: rgba(25,118,210,0.09);
            filter: blur(80px);
            top: -160px;
            left: -160px;
            pointer-events: none;
            z-index: 0;
            animation: cd-blob-a 11s ease-in-out infinite;
        }

        .cashier-dashboard-page::after {
            content: '';
            position: fixed;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(0,176,255,0.08);
            filter: blur(70px);
            bottom: -100px;
            right: -100px;
            pointer-events: none;
            z-index: 0;
            animation: cd-blob-b 13s ease-in-out infinite;
        }

        @keyframes cd-blob-a {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(30px, 22px) scale(1.06); }
        }
        @keyframes cd-blob-b {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-22px, -18px) scale(1.05); }
        }
        /* ══════════════════════════════════════════
           END BACKGROUND REDESIGN
        ══════════════════════════════════════════ */

        /* Animations (KPI + Nav) — UNCHANGED */
        .cd-kpi-card, .cd-nav-card {
            will-change: transform, box-shadow;
        }

        .cd-kpi-card::before,
        .cd-nav-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.58) 50%, transparent 60%);
            pointer-events: none;
            z-index: 2;
        }

        .cd-kpi-card:hover::before,
        .cd-nav-card:hover::before {
            animation: cd-shine-sweep .55s ease forwards;
        }

        @keyframes cd-shine-sweep {
            0%   { left: -100%; opacity: 1; }
            100% { left: 150%; opacity: 0; }
        }

        .cd-kpi-card::after,
        .cd-nav-card::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            width: 0;
            height: 0;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
            pointer-events: none;
            z-index: 1;
        }

        .cd-kpi-card[data-color="blue"]::after  { background: rgba(26,86,219,.20); }
        .cd-kpi-card[data-color="mint"]::after  { background: rgba(16,185,129,.20); }
        .cd-kpi-card[data-color="amber"]::after { background: rgba(245,158,11,.20); }
        .cd-kpi-card[data-color="rose"]::after  { background: rgba(244,63,94,.20); }
        .cd-nav-card::after { background: rgba(59,130,246,.18); }

        .cd-kpi-card.is-clicked { animation: cd-kpi-bounce .5s cubic-bezier(.34,1.56,.64,1) forwards; }
        @keyframes cd-kpi-bounce {
            0%   { transform: scale(1) translateY(0); }
            18%  { transform: scale(.91) translateY(3px); }
            45%  { transform: scale(.97) translateY(-5px); }
            72%  { transform: scale(1.01) translateY(-1px); }
            100% { transform: scale(1) translateY(0); }
        }

        .cd-kpi-card.is-clicked::after,
        .cd-nav-card.is-clicked::after {
            animation: cd-ripple .6s ease-out forwards;
        }

        @keyframes cd-ripple {
            0%   { width: 0; height: 0; opacity: .55; transform: translate(-50%,-50%) scale(0); }
            65%  { width: 290px; height: 290px; opacity: .2;  transform: translate(-50%,-50%) scale(1); }
            100% { width: 330px; height: 330px; opacity: 0;   transform: translate(-50%,-50%) scale(1.1); }
        }

        .cd-kpi-card:hover .cd-kpi-icon,
        .cd-nav-card:hover .cd-nav-icon {
            transform: scale(1.08) rotate(-4deg);
        }

        .cd-nav-card.is-clicked { animation: cd-card-click .55s cubic-bezier(.34,1.56,.64,1) forwards; }
        @keyframes cd-card-click {
            0%   { transform: scale(1) translateY(0); }
            18%  { transform: scale(.93) translateY(2px); }
            45%  { transform: scale(.96) translateY(-2px); }
            70%  { transform: scale(.99) translateY(-1px); }
            100% { transform: scale(1) translateY(0); }
        }

        .cd-kpi-card, .cd-nav-card {
            opacity: 0;
            transform: translateY(16px);
            animation: cd-entry-fade .5s ease forwards;
        }

        @keyframes cd-entry-fade {
            to { opacity: 1; transform: translateY(0); }
        }

        .cd-kpi-card:nth-child(1) { animation-delay: .05s; }
        .cd-kpi-card:nth-child(2) { animation-delay: .12s; }
        .cd-kpi-card:nth-child(3) { animation-delay: .19s; }
        .cd-kpi-card:nth-child(4) { animation-delay: .26s; }

        .cd-nav-card:nth-child(1)  { animation-delay: .32s; }
        .cd-nav-card:nth-child(2)  { animation-delay: .37s; }
        .cd-nav-card:nth-child(3)  { animation-delay: .42s; }
        .cd-nav-card:nth-child(4)  { animation-delay: .47s; }
        .cd-nav-card:nth-child(5)  { animation-delay: .52s; }
        .cd-nav-card:nth-child(6)  { animation-delay: .57s; }
        .cd-nav-card:nth-child(7)  { animation-delay: .62s; }
        .cd-nav-card:nth-child(8)  { animation-delay: .67s; }
        .cd-nav-card:nth-child(9)  { animation-delay: .72s; }
        .cd-nav-card:nth-child(10) { animation-delay: .77s; }

        /* Navigation overlay */
        #nav-overlay {
            position: fixed;
            inset: 0;
            background: rgba(242,244,249,0);
            z-index: 9999;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .4s;
        }

        #nav-overlay.active {
            pointer-events: all;
            background: rgba(242,244,249,.88);
            backdrop-filter: blur(8px);
        }

        .nav-loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            opacity: 0;
            transform: scale(.82);
            transition: opacity .3s .1s, transform .3s .1s;
        }

        #nav-overlay.active .nav-loader {
            opacity: 1;
            transform: scale(1);
        }

        .nav-loader-icon {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: linear-gradient(135deg,#1A56DB,#06B6D4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
            box-shadow: 0 8px 30px rgba(26,86,219,.35);
            animation: loader-bounce .7s ease-in-out infinite alternate;
        }

        @keyframes loader-bounce {
            from { transform: translateY(0); }
            to   { transform: translateY(-8px); }
        }

        .nav-loader-label {
            font-size: 14px;
            font-weight: 600;
            color: #1A56DB;
            letter-spacing: -0.01em;
        }

        .nav-loader-dots { display: flex; gap: 6px; }
        .nav-loader-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: linear-gradient(135deg,#1A56DB,#06B6D4);
            animation: dot-bounce .7s ease-in-out infinite alternate;
        }
        .nav-loader-dots span:nth-child(2) { animation-delay: .15s; }
        .nav-loader-dots span:nth-child(3) { animation-delay: .30s; }

        @keyframes dot-bounce {
            from { transform: translateY(0); opacity: .4; }
            to   { transform: translateY(-5px); opacity: 1; }
        }

        .cashier-dashboard-wrap {
            position: relative;
            z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 28px 60px;
        }

        .cd-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .cd-breadcrumb-line {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .cd-breadcrumb-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #06B6D4;
            animation: cd-pulse-dot 2s ease-in-out infinite;
        }

        @keyframes cd-pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        .cd-breadcrumb-text {
            font-size: 12px;
            font-weight: 500;
            color: #8892AA;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .cd-title {
            font-size: 28px;
            font-weight: 700;
            color: #0C0F1A;
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .cd-title span {
            background: linear-gradient(135deg, #1A56DB, #06B6D4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cd-welcome {
            margin-top: 6px;
            font-size: 14px;
            color: #8892AA;
        }

        .cd-welcome strong {
            color: #384060;
            font-weight: 500;
        }

        .cd-branch-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #EFF6FF, #ECFEFF);
            border: 1px solid rgba(59,130,246,0.25);
            color: #1A56DB;
            padding: 3px 10px 3px 7px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-left: 8px;
        }

        .cd-branch-chip::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10B981;
        }

        .cd-user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #FFFFFF;
            border: 1px solid rgba(200, 207, 224, 0.6);
            border-radius: 40px;
            padding: 6px 14px 6px 6px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(12,15,26,0.06), 0 8px 32px rgba(12,15,26,0.04);
        }

        .cd-user-pill:hover {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18);
        }

        .cd-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1A56DB, #06B6D4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: white;
            flex-shrink: 0;
            overflow: hidden;
        }

        .cd-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .cd-user-name {
            font-size: 13px;
            font-weight: 500;
            color: #0C0F1A;
            line-height: 1.15;
        }

        .cd-user-role {
            font-size: 11px;
            color: #8892AA;
            line-height: 1.15;
        }

        .cd-date-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            padding: 12px 20px;
            background: #FFFFFF;
            border: 1px solid rgba(200, 207, 224, 0.6);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(12,15,26,0.06), 0 8px 32px rgba(12,15,26,0.04);
            flex-wrap: wrap;
            gap: 12px;
        }

        .cd-date-left {
            font-size: 14px;
            font-weight: 600;
            color: #384060;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cd-live-time {
            font-size: 20px;
            font-weight: 700;
            color: #0C0F1A;
            letter-spacing: -0.03em;
        }

        .cd-live-time-sub { font-size: 12px; color: #8892AA; font-weight: 400; }

        .cd-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        @media (max-width: 1024px) { .cd-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px)  { .cd-kpi-grid { grid-template-columns: 1fr; } }

        .cd-kpi-card {
            background: #FFFFFF;
            border: 1px solid rgba(200, 207, 224, 0.6);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(12,15,26,0.06), 0 8px 32px rgba(12,15,26,0.04);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .cd-kpi-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(12,15,26,0.10); }

        .cd-kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .cd-kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .cd-kpi-icon.blue  { background: #EFF6FF; color: #1A56DB; }
        .cd-kpi-icon.mint  { background: #ECFDF5; color: #10B981; }
        .cd-kpi-icon.amber { background: #FFFBEB; color: #F59E0B; }
        .cd-kpi-icon.rose  { background: #FFF1F2; color: #F43F5E; }

        .cd-kpi-value {
            font-size: 26px;
            font-weight: 700;
            color: #0C0F1A;
            letter-spacing: -0.04em;
            line-height: 1;
            margin-bottom: 6px;
        }

        .cd-kpi-label {
            font-size: 12px;
            color: #8892AA;
            font-weight: 400;
            letter-spacing: 0.02em;
        }

        .cd-kpi-bar {
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            border-radius: 16px 0 0 16px;
        }

        .cd-kpi-bar.blue  { background: linear-gradient(180deg, #1A56DB, #06B6D4); }
        .cd-kpi-bar.mint  { background: linear-gradient(180deg, #10B981, #34D399); }
        .cd-kpi-bar.amber { background: linear-gradient(180deg, #F59E0B, #FCD34D); }
        .cd-kpi-bar.rose  { background: linear-gradient(180deg, #F43F5E, #FB7185); }

        .cd-section-label {
            font-size: 11px;
            font-weight: 700;
            color: #8892AA;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cd-section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(200, 207, 224, 0.6);
        }

        .cd-nav-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 32px;
        }

        @media (max-width: 1024px) { .cd-nav-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 640px)  { .cd-nav-grid { grid-template-columns: repeat(2, 1fr); } }

        .cd-nav-card {
            background: #FFFFFF;
            border: 1px solid rgba(200, 207, 224, 0.6);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #0C0F1A;
            transition: all 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 1px 3px rgba(12,15,26,0.06), 0 8px 32px rgba(12,15,26,0.04);
        }

        .cd-nav-card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 12px 30px rgba(59,130,246,0.12);
            border-color: rgba(59,130,246,0.35);
            text-decoration: none;
            color: #0C0F1A;
        }

        .cd-nav-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #F2F4F9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #3B82F6;
            transition: all 0.22s;
            flex-shrink: 0;
        }

        .cd-nav-card:hover .cd-nav-icon {
            background: linear-gradient(135deg, #1A56DB, #06B6D4);
            color: #FFFFFF;
        }

        .cd-nav-title {
            font-size: 13px;
            font-weight: 700;
            color: #0C0F1A;
            line-height: 1.2;
        }

        .cd-nav-arrow {
            margin-left: auto;
            color: #C8CFE0;
            font-size: 11px;
            transition: all 0.2s;
        }

        .cd-nav-card:hover .cd-nav-arrow {
            color: #3B82F6;
            transform: translateX(2px);
        }

        .cd-main-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) { .cd-main-grid { grid-template-columns: 1fr; } }

        .cd-card-panel {
            background: #FFFFFF;
            border: 1px solid rgba(200, 207, 224, 0.6);
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 1px 3px rgba(12,15,26,0.06), 0 8px 32px rgba(12,15,26,0.04);
        }

        .cd-panel-title {
            font-size: 15px;
            font-weight: 700;
            color: #0C0F1A;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cd-panel-title i {
            color: #3B82F6;
            font-size: 13px;
        }

        .cd-chart-wrap { position: relative; height: 220px; }

        .cd-sales-list {
            display: flex;
            flex-direction: column;
            gap: 2px;
            max-height: 260px;
            overflow-y: auto;
        }

        .cd-sales-list::-webkit-scrollbar { width: 3px; }
        .cd-sales-list::-webkit-scrollbar-thumb { background: #C8CFE0; border-radius: 3px; }

        .cd-sale-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 10px;
            border-radius: 10px;
            transition: background 0.15s;
        }

        .cd-sale-row:hover { background: #F2F4F9; }

        .cd-sale-left { display: flex; align-items: center; gap: 10px; }

        .cd-sale-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1A56DB, #06B6D4);
            flex-shrink: 0;
        }

        .cd-sale-id { font-size: 11px; color: #8892AA; font-weight: 500; }
        .cd-sale-time { font-size: 11px; color: #C8CFE0; }

        .cd-sale-amount {
            font-size: 14px;
            font-weight: 700;
            color: #0C0F1A;
        }

        .cd-bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) { .cd-bottom-grid { grid-template-columns: 1fr; } }

        .cd-product-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border-radius: 10px;
            transition: background 0.15s;
        }

        .cd-product-row:hover { background: #F2F4F9; }

        .cd-product-rank {
            font-size: 11px;
            font-weight: 700;
            color: #C8CFE0;
            width: 18px;
            flex-shrink: 0;
        }

        .cd-product-bar-wrap { flex: 1; }

        .cd-product-name {
            font-size: 13px;
            color: #0C0F1A;
            font-weight: 500;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .cd-product-bar-bg {
            height: 4px;
            background: #F2F4F9;
            border-radius: 4px;
            overflow: hidden;
        }

        .cd-product-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #1A56DB, #06B6D4);
            border-radius: 4px;
        }

        .cd-product-revenue {
            font-size: 13px;
            font-weight: 700;
            color: #0C0F1A;
            flex-shrink: 0;
            min-width: 80px;
            text-align: right;
        }

        .cd-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #F2F4F9;
        }

        .cd-stat-row:last-child { border-bottom: none; }
        .cd-stat-left { display: flex; align-items: center; gap: 10px; }

        .cd-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .cd-stat-label { font-size: 13px; color: #384060; }
        .cd-stat-value { font-size: 14px; font-weight: 700; color: #0C0F1A; }

        .low-stock-item:hover { background-color: rgba(59,130,246,0.08); transition: background-color 0.3s ease; }
        .low-stock-item .badge { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
    </style>
@endpush

@section('content')
@php
    $cashierUser = auth()->user();
    $cashierAvatarUrl = null;
    if ($cashierUser && !empty($cashierUser->profile_picture)) {
        $av = $cashierUser->profile_picture;
        if (\Illuminate\Support\Str::startsWith($av, ['http://', 'https://', '//'])) {
            $cashierAvatarUrl = $av;
        } elseif (\Illuminate\Support\Str::startsWith($av, 'storage/')) {
            $cashierAvatarUrl = asset($av);
        } else {
            $candidate = ltrim($av, '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($candidate)) {
                $cashierAvatarUrl = asset('storage/' . $candidate);
            } elseif (file_exists(public_path($candidate))) {
                $cashierAvatarUrl = asset($candidate);
            } else {
                $cashierAvatarUrl = asset($candidate);
            }
        }
    }
@endphp

<div class="cashier-dashboard-page">
    <div id="nav-overlay">
        <div class="nav-loader">
            <div class="nav-loader-icon" id="loaderIcon"><i class="fas fa-gauge-high"></i></div>
            <div class="nav-loader-label" id="loaderLabel">Loading…</div>
            <div class="nav-loader-dots"><span></span><span></span><span></span></div>
        </div>
    </div>
    <div class="cashier-dashboard-wrap">
        <div class="cd-header">
            <div>
                <div class="cd-breadcrumb-line">
                    <div class="cd-breadcrumb-dot"></div>
                    <span class="cd-breadcrumb-text">Point of Sale · Branch View</span>
                </div>
                <div class="cd-title">Cashier <span>Dashboard</span></div>
                <div class="cd-welcome">
                    Welcome back, <strong>{{ $cashierUser->name ?? 'Cashier' }}</strong>
                    @if($branch)
                        <span class="cd-branch-chip">{{ $branch->branch_name }}</span>
                    @endif
                </div>
            </div>

            <a href="{{ route('cashier.sales.create') }}" class="cd-user-pill" style="text-decoration:none;background:linear-gradient(135deg,#1565C0,#1976D2);color:#fff;gap:7px;padding:8px 16px;">
                <i class="fas fa-cash-register" style="font-size:14px;"></i>
                <span style="font-size:13px;font-weight:700;font-family:'Nunito',sans-serif;">POS</span>
            </a>

            <div class="dropdown">
                <button class="cd-user-pill" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="cd-avatar">
                        @if(!empty($cashierAvatarUrl))
                            <img src="{{ $cashierAvatarUrl }}" alt="{{ $cashierUser->name ?? 'Cashier' }}">
                        @else
                            {{ $cashierUser ? strtoupper(substr($cashierUser->name,0,1)) : 'C' }}
                        @endif
                    </div>
                    <div>
                        <div class="cd-user-name">{{ $cashierUser->name ?? 'Cashier' }}</div>
                        <div class="cd-user-role">Cashier</div>
                    </div>
                    <i class="fas fa-chevron-down" style="color:#8892AA;font-size:10px;margin-left:4px;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="border-radius: 14px; border: 1px solid rgba(200, 207, 224, 0.6); box-shadow: 0 20px 60px rgba(12,15,26,0.12); overflow: hidden;">
                    <li class="px-3 py-2" style="border-bottom: 1px solid #F2F4F9;">
                        <div class="small fw-semibold">{{ $cashierUser->name ?? 'Cashier' }}</div>
                        <div class="small text-muted">{{ $cashierUser->email ?? '' }}</div>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user" style="width:14px;color:#3B82F6"></i> Profile</a></li>
                    <li><hr class="dropdown-divider" style="margin: 4px 0;"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="color:#F43F5E;">
                                <i class="fas fa-sign-out-alt" style="width:14px"></i> Logout
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <div class="cd-date-bar">
            <div class="cd-date-left">
                <i class="fas fa-calendar-day" style="color:#3B82F6"></i>
                <span id="liveDate"></span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <i class="fas fa-clock" style="color:#06B6D4;font-size:13px;"></i>
                <span class="cd-live-time" id="liveTime">--:--:-- --</span>
                <span class="cd-live-time-sub">local time</span>
            </div>
        </div>

        <div class="cd-kpi-grid">
            <div class="cd-kpi-card" data-color="blue" data-label="Today's Sales" data-icon="cash-register" onclick="showSalesSummaryModal()">
                <div class="cd-kpi-bar blue"></div>
                <div class="cd-kpi-top">
                    <div class="cd-kpi-icon blue"><i class="fas fa-cash-register"></i></div>
                </div>
                <div class="cd-kpi-value">₱{{ number_format($todaySales->total_revenue ?? 0, 2) }}</div>
                <div class="cd-kpi-label">Today's Sales</div>
            </div>

            <div class="cd-kpi-card" data-color="mint" data-label="Credit Payments" data-icon="coins" onclick="showCreditRevenueModal()">
                <div class="cd-kpi-bar mint"></div>
                <div class="cd-kpi-top">
                    <div class="cd-kpi-icon mint"><i class="fas fa-coins"></i></div>
                </div>
                <div class="cd-kpi-value">₱{{ number_format($todayCreditRevenue ?? 0, 2) }}</div>
                <div class="cd-kpi-label">Credit Payments Today</div>
            </div>

            <div class="cd-kpi-card" data-color="amber" data-label="Today's Expenses" data-icon="wallet" onclick="showExpensesSummaryModal()">
                <div class="cd-kpi-bar amber"></div>
                <div class="cd-kpi-top">
                    <div class="cd-kpi-icon amber"><i class="fas fa-wallet"></i></div>
                </div>
                <div class="cd-kpi-value">₱{{ number_format($todayExpenses, 2) }}</div>
                <div class="cd-kpi-label">Today's Expenses</div>
            </div>

            <div class="cd-kpi-card" data-color="rose" data-label="Cash on Hand" data-icon="money-bill-wave" style="cursor: default;">
                <div class="cd-kpi-bar rose"></div>
                <div class="cd-kpi-top">
                    <div class="cd-kpi-icon rose"><i class="fas fa-money-bill-wave"></i></div>
                </div>
                <div class="cd-kpi-value">₱{{ number_format($cashOnHandToday, 2) }}</div>
                <div class="cd-kpi-label">Cash on Hand Today</div>
            </div>

            <div class="cd-kpi-card" data-color="amber" data-label="Procurement Needs" data-icon="truck-loading" onclick="window.location.href='{{ route('cashier.procurement') }}'">
                <div class="cd-kpi-bar amber"></div>
                <div class="cd-kpi-top">
                    <div class="cd-kpi-icon amber"><i class="fas fa-truck-loading"></i></div>
                </div>
                <div class="cd-kpi-value" id="procurementCount">—</div>
                <div class="cd-kpi-label">Procurement Needs</div>
            </div>
        </div>

        <div class="cd-section-label">Quick Access</div>
        @php
            $dashboardRoutes = [
                'products' => route('cashier.products.index'),
                'product_category' => route('cashier.categories.index'),
                'purchases' => route('cashier.purchases.index'),
                'inventory' => route('cashier.inventory.index'),
                'stock_management' => route('cashier.stock-management.index'),
                'stock_in' => route('cashier.stockin.index'),
                'customer' => route('cashier.customers.index'),
                'refund_return' => route('cashier.refunds.index'),
                'sales' => route('cashier.sales.index'),
                'sales_report' => route('cashier.sales.reports'),
                'expenses' => route('cashier.expenses.index'),
                'credit' => route('cashier.credit.index'),
            ];
        @endphp

        <div class="cd-nav-grid">
            @foreach ($modules as $moduleKey => $moduleData)
                @canAccess($moduleKey, 'view')
                    <a href="{{ $dashboardRoutes[$moduleKey] ?? '#' }}" class="cd-nav-card {{ in_array($moduleKey, ['stock_in','product_category']) ? 'd-none' : '' }}" data-label="{{ $moduleData['label'] }}" data-icon="{{ $moduleData['icon'] ?? 'gauge-high' }}">
                        <div class="cd-nav-icon"><i class="fas fa-{{ $moduleData['icon'] ?? 'cogs' }}"></i></div>
                        <span class="cd-nav-title">{{ $moduleData['label'] }}</span>
                        <i class="fas fa-arrow-right cd-nav-arrow"></i>
                    </a>
                @endif
            @endforeach
        </div>

        <div class="cd-section-label">Analytics</div>
        <div class="cd-main-grid">
            <div class="cd-card-panel">
                <div class="cd-panel-title">
                    <i class="fas fa-chart-line"></i>
                    Sales Trend
                    <span style="margin-left:auto;font-family:'Inter';font-size:12px;color:#8892AA;">Last 7 Days</span>
                </div>
                <div class="cd-chart-wrap">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="cd-card-panel">
                <div class="cd-panel-title">
                    <i class="fas fa-clock-rotate-left"></i>
                    Recent Sales
                    <span style="margin-left:auto;font-size:11px;font-weight:500;color:#8892AA;background:#F2F4F9;padding:2px 8px;border-radius:10px;">Today</span>
                </div>
                <div class="cd-sales-list">
                    @if($recentSales->count() > 0)
                        @foreach($recentSales as $sale)
                            <div class="cd-sale-row">
                                <div class="cd-sale-left">
                                    <div class="cd-sale-dot"></div>
                                    <div>
                                        <div class="cd-sale-id">#{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</div>
                                        <div class="cd-sale-time">{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</div>
                                    </div>
                                </div>
                                <div class="cd-sale-amount">₱{{ number_format($sale->total_amount, 2) }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">No sales today</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="cd-bottom-grid">
            <div class="cd-card-panel">
                <div class="cd-panel-title">
                    <i class="fas fa-trophy"></i>
                    Top Products Today
                </div>
                @if($topProducts->count() > 0)
                    @php $topMaxRevenue = (float) ($topProducts->max('revenue') ?? 0); @endphp
                    @foreach($topProducts as $i => $product)
                        @php
                            $pct = $topMaxRevenue > 0 ? round(((float) $product->revenue / $topMaxRevenue) * 100) : 0;
                        @endphp
                        <div class="cd-product-row">
                            <span class="cd-product-rank">#{{ $i + 1 }}</span>
                            <div class="cd-product-bar-wrap">
                                <div class="cd-product-name">{{ $product->product_name }}</div>
                                <div class="cd-product-bar-bg">
                                    <div class="cd-product-bar-fill" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                            <span class="cd-product-revenue">₱{{ number_format($product->revenue, 2) }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">No products sold today</div>
                @endif
            </div>

            <div class="cd-card-panel">
                <div class="cd-panel-title">
                    <i class="fas fa-gauge-high"></i>
                    Quick Stats
                </div>
                @php
                    $totalTransactions = (int) ($todaySales->total_sales ?? 0);
                    $refundsApproved = (int) ($todayRefunds->total_refunds ?? 0);
                    $totalRevenue = (float) ($todaySales->total_revenue ?? 0);
                    $avgTransaction = $totalTransactions > 0 ? ($totalRevenue / $totalTransactions) : 0;
                @endphp
                <div class="cd-stat-row">
                    <div class="cd-stat-left">
                        <div class="cd-stat-icon" style="background:#EFF6FF;color:#1A56DB"><i class="fas fa-receipt"></i></div>
                        <span class="cd-stat-label">Total Transactions</span>
                    </div>
                    <span class="cd-stat-value">{{ $totalTransactions }}</span>
                </div>
                <div class="cd-stat-row">
                    <div class="cd-stat-left">
                        <div class="cd-stat-icon" style="background:#FFF1F2;color:#F43F5E"><i class="fas fa-undo"></i></div>
                        <span class="cd-stat-label">Refunds Approved</span>
                    </div>
                    <span class="cd-stat-value">{{ $refundsApproved }}</span>
                </div>
                <div class="cd-stat-row">
                    <div class="cd-stat-left">
                        <div class="cd-stat-icon" style="background:#F0FDF4;color:#16A34A"><i class="fas fa-hand-holding-usd"></i></div>
                        <span class="cd-stat-label">Avg. Transaction</span>
                    </div>
                    <span class="cd-stat-value">₱{{ number_format($avgTransaction, 2) }}</span>
                </div>
                <div class="cd-stat-row">
                    <div class="cd-stat-left">
                        <div class="cd-stat-icon" style="background:#ECFDF5;color:#10B981"><i class="fas fa-money-bill-wave"></i></div>
                        <span class="cd-stat-label">Cash on Hand</span>
                    </div>
                    <span class="cd-stat-value">₱{{ number_format($cashOnHandToday, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function showSalesSummaryModal() {
    Swal.fire({
        title: "Today's Sales",
        html: `
            <div class="text-start">
                <p class="mb-1"><strong>Total Revenue:</strong> ₱{{ number_format($todaySales->total_revenue ?? 0, 2) }}</p>
                <p class="mb-1"><strong>Transactions:</strong> {{ $todaySales->total_sales ?? 0 }}</p>
                @if($todayRefunds)
                    <p class="mb-1"><strong>Approved Refunds:</strong> {{ $todayRefunds->total_refunds }} (₱{{ number_format($todayRefunds->total_refund_amount ?? 0, 2) }})</p>
                @endif
                <p class="text-muted mt-2">This summary is for your branch today.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Close',
    });
}

function showCreditRevenueModal() {
    Swal.fire({
        title: 'Credit Payments Today',
        html: `
            <div class="text-start">
                <p class="mb-1"><strong>Total Payments Collected:</strong> ₱{{ number_format($todayCreditRevenue ?? 0, 2) }}</p>
                <hr>
                <p class="mb-1"><strong>Customers who paid today:</strong></p>
                @if(isset($todayCreditPayments) && $todayCreditPayments->count())
                    <ul class="mb-2" style="max-height: 180px; overflow-y: auto; padding-left: 1.2rem;">
                        @foreach($todayCreditPayments as $payment)
                            <li class="mb-1">
                                <strong>{{ $payment->credit->customer->full_name ?? 'Unknown Customer' }}</strong>
                                – ₱{{ number_format($payment->payment_amount, 2) }}
                                <span class="text-muted small">({{ $payment->created_at->format('h:i A') }})</span>
                            </li>
                        @endforeach
                    </ul>
                    @if($todayCreditPayments->count() === 10)
                        <p class="text-muted small mb-0">Showing latest 10 payments.</p>
                    @endif
                @else
                    <p class="text-muted mb-0">No credit payments recorded yet today.</p>
                @endif
                <p class="text-muted mt-2">All amounts are for this branch only.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Close',
    });
}

function showExpensesSummaryModal() {
    Swal.fire({
        title: "Today's Expenses",
        html: `
            <div class="text-start">
                <p class="mb-1"><strong>Total Expenses:</strong> ₱{{ number_format($todayExpenses ?? 0, 2) }}</p>
                <p class="text-muted mt-2">All expenses recorded today for this branch.</p>
            </div>
        `,
        icon: 'info',
        confirmButtonText: 'Close',
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const dateEl = document.getElementById('liveDate');
    const timeEl = document.getElementById('liveTime');
    if (dateEl) {
        dateEl.textContent = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    if (timeEl) {
        const tick = () => {
            const now = new Date();
            const h = now.getHours();
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            const h12 = h % 12 || 12;
            timeEl.textContent = `${h12}:${m}:${s} ${ampm}`;
        };
        tick();
        setInterval(tick, 1000);
    }

    (function () {
        const overlay = document.getElementById('nav-overlay');
        const loaderIcon = document.getElementById('loaderIcon');
        const loaderLabel = document.getElementById('loaderLabel');
        let busy = false;

        function resetAll() {
            document.querySelectorAll('.cd-kpi-card, .cd-nav-card').forEach(c => {
                c.classList.remove('is-clicked');
                c.style.pointerEvents = '';
                c.style.opacity = '';
                c.style.transform = '';
            });
            busy = false;
        }

        function showOverlay(label, icon, href) {
            if (!overlay || !loaderIcon || !loaderLabel) {
                window.location.href = href;
                return;
            }
            loaderIcon.innerHTML = `<i class="fas fa-${icon}"></i>`;
            loaderLabel.textContent = `Opening ${label}…`;
            overlay.classList.add('active');

            setTimeout(() => {
                window.location.href = href;
            }, 650);
        }

        function fadeOthers(clicked, selector) {
            document.querySelectorAll(selector).forEach((other, idx) => {
                if (other === clicked) return;
                other.style.transition = `opacity .32s ${idx * 28}ms ease, transform .32s ${idx * 28}ms ease`;
                other.style.opacity = '0';
                other.style.transform = 'translateY(7px) scale(.96)';
                other.style.pointerEvents = 'none';
            });
        }

        function dimGroup(selector) {
            document.querySelectorAll(selector).forEach((c, idx) => {
                c.style.transition = `opacity .28s ${idx * 22}ms ease`;
                c.style.opacity = '0.28';
                c.style.pointerEvents = 'none';
            });
        }

        document.querySelectorAll('.cd-kpi-card').forEach(card => {
            card.addEventListener('click', function () {
                if (busy) return;
                busy = true;

                this.classList.remove('is-clicked');
                void this.offsetWidth;
                this.classList.add('is-clicked');

                fadeOthers(this, '.cd-kpi-card');
                dimGroup('.cd-nav-card');

                setTimeout(resetAll, 900);
            }, { capture: true });
        });

        document.querySelectorAll('.cd-nav-card').forEach(card => {
            card.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (!href || href === '#') return;
                e.preventDefault();
                if (busy) return;
                busy = true;

                this.classList.remove('is-clicked');
                void this.offsetWidth;
                this.classList.add('is-clicked');

                fadeOthers(this, '.cd-nav-card');
                dimGroup('.cd-kpi-card');

                const label = this.dataset.label || 'Loading';
                const icon = this.dataset.icon || 'gauge-high';
                setTimeout(() => showOverlay(label, icon, href), 320);
            });
        });
    })();

    // Sales Chart
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels),
                datasets: [{
                    label: 'Sales',
                    data: @json($salesData),
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    tension: 0.4,
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        display: false
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            callback: function(value) { 
                                return '₱' + value.toLocaleString(); 
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

});

function viewProductDetails(productName, branchName) {
    // Close the modal first
    const modal = bootstrap.Modal.getInstance(document.getElementById('lowStockModal'));
    if (modal) {
        modal.hide();
    }
    
    // Show info about the product with branch information
    Swal.fire({
        icon: 'info',
        title: 'Product Details',
        html: `
            <div class="text-start">
                <p><strong>Product:</strong> ${productName}</p>
                <p><strong>Branch:</strong> <span class="badge bg-primary">${branchName}</span></p>
                <p><strong>Status:</strong> <span class="badge bg-danger">Low Stock</span></p>
                <p class="text-muted">Click "Manage Stock" to update inventory levels for this branch.</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Manage Stock',
        cancelButtonText: 'Close',
        confirmButtonColor: '#2196F3'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route('cashier.inventory.index') }}';
        }
    });

}

function showLowStockModal() {
    fetch('/cashier/dashboard/low-stock')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let modalHtml = `
                    <div class="modal fade" id="lowStockModal" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                        Low Stock Items
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="list-group">`;
                
                const halfLength = Math.ceil(data.lowStockItems.length / 2);
                const firstColumn = data.lowStockItems.slice(0, halfLength);
                const secondColumn = data.lowStockItems.slice(halfLength);
                
                firstColumn.forEach(item => {
                    modalHtml += `
                                                <div class="list-group-item low-stock-item d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="viewProductDetails('${item.product_name}', '${item.branch_name}')">
                                                    <div>
                                                        <i class="fas fa-box text-warning me-2"></i>
                                                        <span class="fw-medium">${item.product_name}</span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-map-marker-alt me-1"></i>${item.branch_name}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-danger me-2">${item.current_stock}</span>
                                                        <small class="text-muted">${item.unit_name}</small>
                                                    </div>
                                                </div>`;
                });
                
                modalHtml += `
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="list-group">`;
                
                secondColumn.forEach(item => {
                    modalHtml += `
                                                <div class="list-group-item low-stock-item d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="viewProductDetails('${item.product_name}', '${item.branch_name}')">
                                                    <div>
                                                        <i class="fas fa-box text-warning me-2"></i>
                                                        <span class="fw-medium">${item.product_name}</span>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-map-marker-alt me-1"></i>${item.branch_name}
                                                        </small>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="badge bg-danger me-2">${item.current_stock}</span>
                                                        <small class="text-muted">${item.unit_name}</small>
                                                    </div>
                                                </div>`;
                });
                
                modalHtml += `
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                
                const existingModal = document.getElementById('lowStockModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                const modal = new bootstrap.Modal(document.getElementById('lowStockModal'));
                modal.show();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to load low stock items'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to load low stock items'
            });
        });
}

function peso(n) {
    return new Intl.NumberFormat('en-PH', {style: 'currency', currency: 'PHP'}).format(n || 0);
}

// Fetch dashboard alerts (procurement, out-of-stock, etc.) on load
(function loadDashboardAlerts() {
    fetch('{{ route("cashier.dashboard.alerts") }}')
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('procurementCount');
            if (el) {
                el.textContent = data.procurementNeeds ?? 0;
                if ((data.procurementNeeds ?? 0) > 0) {
                    el.style.color = '#f59e0b';
                }
            }
        })
        .catch(() => {});
})();

function animateAndNavigate(event, module) {
    event.preventDefault();
    const clickedCard = event.currentTarget;
    const allCards = document.querySelectorAll('.nav-card');
    const targetUrl = clickedCard.href;

    const isProductsClick = module === 'products';
    const isCategoryClick = module === 'product_category';
    const isPurchasesClick = module === 'purchases';
    const isInventoryClick = module === 'inventory';
    const isStockInClick = module === 'stock_in';

    const sidebar = document.createElement('div');
    sidebar.style.cssText = `
        position: fixed;
        left: 0;
        top: 0;
        width: 220px;
        height: 100vh;
        background: linear-gradient(180deg, #0D47A1 0%, #1565C0 100%);
        box-shadow: 2px 0 15px rgba(0,0,0,0.2);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        padding: 20px 10px;
        transform: translateX(-100%);
        transition: transform 0.5s ease-in-out;
    `;
    document.body.appendChild(sidebar);

    makeSidebarResizable(sidebar);

    const sidebarHeader = document.createElement('div');
    sidebarHeader.style.cssText = `
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255,255,255,0.2);
    `;
    
    const logoContainer = document.createElement('div');
    logoContainer.style.cssText = `
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    const logoImg = document.createElement('img');
    logoImg.src = '/images/BGH LOGO.png';
    logoImg.style.cssText = `
        height: 40px;
        width: auto;
        object-fit: contain;
        cursor: pointer;
        transition: transform 0.3s ease;
    `;
    
    logoImg.addEventListener('click', () => {
        if (window.location.pathname === '/cashier/dashboard') {
            const sidebar = document.querySelector('[style*="position: fixed"][style*="left: 0"]');
            if (sidebar) {
                sidebar.style.transform = 'translateX(-100%)';
                setTimeout(() => {
                    sidebar.remove();
                }, 300);
            }
        }
    });
    
    logoContainer.appendChild(logoImg);
    sidebarHeader.appendChild(logoContainer);
    sidebar.appendChild(sidebarHeader);

    allCards.forEach((card, index) => {
        if (card !== clickedCard) {
            const cardContent = card.cloneNode(true);
            cardContent.className = 'nav-card';
            cardContent.style.cssText = `
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 20px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                margin-bottom: 10px;
                cursor: pointer;
                text-decoration: none;
                color: white;
                transition: all 0.3s ease;
                transform: translateX(-100%);
                opacity: 0;
            `;

            const iconEl = cardContent.querySelector('.nav-icon');
            if (iconEl) {
                iconEl.style.cssText = `
                    width: 48px;
                    height: 48px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: linear-gradient(135deg, #2196F3, #00E5FF);
                    color: #0D47A1;
                    font-size: 20px;
                    flex-shrink: 0;
                `;
            }

            const contentEl = cardContent.querySelector('.nav-content');
            if (contentEl) {
                contentEl.style.cssText = `display: block; color: white;`;
            }

            const titleEl = cardContent.querySelector('.nav-content h5');
            if (titleEl) {
                titleEl.style.cssText = `margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: white; text-decoration: none;`;
            }

            const descEl = cardContent.querySelector('.nav-content p');
            if (descEl) {
                descEl.style.cssText = `margin: 0; font-size: 12px; color: rgba(255, 255, 255, 0.85); text-decoration: none;`;
            }

            cardContent.querySelectorAll('a, h5, p, span').forEach(el => {
                el.style.textDecoration = 'none';
            });
            
            cardContent.removeAttribute('onclick');
            
            cardContent.addEventListener('mouseenter', () => {
                cardContent.style.background = 'rgba(255,255,255,0.2)';
                cardContent.style.transform = 'translateX(5px)';
            });
            cardContent.addEventListener('mouseleave', () => {
                cardContent.style.background = 'rgba(255,255,255,0.1)';
                cardContent.style.transform = 'translateX(0)';
            });
            
            sidebar.appendChild(cardContent);
            
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            
            setTimeout(() => {
                cardContent.style.transform = 'translateX(0)';
                cardContent.style.opacity = '1';
            }, 100 + (index * 100));
            
            setTimeout(() => {
                card.style.display = 'none';
            }, 500 + (index * 100));
        }
    });

    const sidebarNavItems = Array.from(sidebar.querySelectorAll('.nav-card'));

    const productsItem = sidebarNavItems.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Products');
    const categoryItem = sidebarNavItems.find(i => (i.querySelector('.nav-content h5')?.textContent || '').trim() === 'Product Category');
    if (productsItem && categoryItem) {
        categoryItem.style.marginLeft = '18px';
        categoryItem.style.paddingLeft = '20px';
        productsItem.insertAdjacentElement('afterend', categoryItem);
    }

    if (isProductsClick || isCategoryClick || isPurchasesClick || isInventoryClick || isStockInClick) {
        sidebar.style.transform = 'translateX(0)';
        sessionStorage.setItem('cashierSidebarHTML', sidebar.outerHTML);
        localStorage.setItem('cashierSidebarHTML', sidebar.outerHTML);
    }

    clickedCard.style.transition = 'all 0.5s ease-in-out';
    clickedCard.style.transform = 'scale(0.95)';
    clickedCard.style.opacity = '0.3';

    setTimeout(() => {
        window.location.href = targetUrl;
    }, 1500);
}
</script>
@endpush