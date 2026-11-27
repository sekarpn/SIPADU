@extends('layouts.panel')

@section('panel')

@php
    $user = auth()->user();
    $hasActiveAuditorium = $auditoriums->contains(fn ($item) => (bool) ($item->is_active ?? true));
    $chartLabelsSafe = $bulanLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $defaultYearSafe = (int) ($defaultChartYear ?? now('Asia/Jakarta')->year);
    $availableYearsSafe = !empty($availableYears) ? array_values($availableYears) : [$defaultYearSafe];
    $chartByYearSafe = !empty($chartByYear)
        ? $chartByYear
        : [
            (string) $defaultYearSafe => [
                'approved' => $chartData ?? array_fill(0, 12, 0),
                'rejected' => array_fill(0, 12, 0),
            ],
        ];

    $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');
    $todayStartJakarta = $nowJakarta->copy()->startOfDay();

    // Hanya jadwal berstatus disetujui yang tanggalnya tidak lewat dan waktu berakhirnya masih > waktu sekarang
    $upcomingList = ($upcomingBookings ?? collect())
        ->filter(function ($item) use ($nowJakarta, $todayStartJakarta) {
            $status = strtolower(trim((string) ($item->status ?? 'disetujui')));
            if (!in_array($status, ['disetujui', 'approved', ''], true)) {
                return false;
            }

            $tgl = $item->display_date
                ? \Carbon\Carbon::parse($item->display_date, 'Asia/Jakarta')
                : (!empty($item->booking_date) ? \Carbon\Carbon::parse($item->booking_date, 'Asia/Jakarta') : null);

            if (!$tgl || $tgl->copy()->startOfDay()->lt($todayStartJakarta)) {
                return false;
            }

            $endRaw = !empty($item->end_time)
                ? trim((string) $item->end_time)
                : (!empty($item->display_end_time) ? str_replace('.', ':', trim((string) $item->display_end_time)) . ':00' : '23:59:59');

            $endAt = \Carbon\Carbon::parse($tgl->toDateString() . ' ' . $endRaw, 'Asia/Jakarta');

            return $endAt->gt($nowJakarta);
        })
        ->values();
@endphp

<style>
    /* BACKGROUND GRADASI HALUS AGAR EFEK BLUR KACA TERLIHAT NYATA */
    body,
    .main-wrapper,
    .content-wrapper,
    main {
        background-color: #b5d6c5 !important;
        background-image: linear-gradient(135deg, #cfe6da 0%, #b5d6c5 52%, #a2c9b4 100%) !important;
        background-attachment: fixed !important;
        min-height: 100vh;
    }

    /* Cegah halaman dashboard melebar dan seragamkan scrollbar layar seperti Pengajuan Terbaru */
    html,
    body {
        max-width: 100%;
        overflow-x: clip !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }

    html::-webkit-scrollbar-track,
    body::-webkit-scrollbar-track {
        background: transparent;
    }

    html::-webkit-scrollbar-thumb,
    body::-webkit-scrollbar-thumb {
        background: rgba(22, 54, 38, 0.18);
        border-radius: 999px;
    }

    html::-webkit-scrollbar-thumb:hover,
    body::-webkit-scrollbar-thumb:hover {
        background: rgba(22, 54, 38, 0.32);
    }

    .app-layout {
        width: 100%;
        max-width: 100%;
        overflow-x: clip !important;
    }

    .app-layout > main,
    .app-layout > main > .main-content,
    .dashboard-page {
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .dashboard-page {
        width: 100%;
        padding: 10px 10px 35px;
        overflow-x: clip;
    }

    /* HEADER */
    .dashboard-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .dashboard-welcome h1 {
        font-size: 27px;
        font-weight: 750;
        color: #163626;
        margin: 0 0 5px;
        letter-spacing: -0.3px;
    }

    /* EFEK MENGETIK (TYPEWRITER) PADA JUDUL SELAMAT DATANG */
    .dashboard-welcome h1.is-typing::after {
        content: '';
        display: inline-block;
        width: 2.5px;
        height: 0.92em;
        margin-left: 4px;
        vertical-align: -2px;
        background-color: #237c4d;
        border-radius: 2px;
        animation: sipaduCaretBlink 0.75s step-end infinite;
    }

    @keyframes sipaduCaretBlink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

    .dashboard-welcome p {
        font-size: 13px;
        color: #324a3c;
        margin: 0;
    }

    .dashboard-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 10px;
        background: #237c4d;
        color: white;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid #237c4d;
        box-shadow: 0 4px 14px rgba(35, 124, 77, 0.25);
        transition: .2s ease;
        white-space: nowrap;
    }

    .dashboard-action:hover {
        background: #1a633c;
        color: white;
        transform: translateY(-2px);
    }

    .dashboard-action.is-disabled {
        background: #d8e2dc;
        border-color: #c3d1c9;
        color: #6b7f73;
        box-shadow: none;
        cursor: not-allowed;
        pointer-events: none;
    }

    .dashboard-action svg {
        width: 16px;
        height: 16px;
    }

    /* STATISTIK (GLASSMORPHISM REALISTIS & STATIS) */
    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.28) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        border-radius: 15px;
        padding: 17px 18px;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.05) !important;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .stat-top small {
        color: #324a3c;
        font-size: 12.5px;
        font-weight: 600;
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .stat-icon svg {
        width: 16px;
        height: 16px;
    }

    .stat-icon.blue {
        background: rgba(33, 128, 77, 0.16);
        color: #1f7a49;
    }

    .stat-icon.amber {
        background: rgba(217, 119, 6, 0.16);
        color: #d97706;
    }

    .stat-icon.green {
        background: rgba(22, 128, 74, 0.18);
        color: #16804a;
    }

    .stat-icon.red {
        background: rgba(220, 69, 69, 0.16);
        color: #dc4545;
    }

    .stat-card strong {
        display: block;
        font-size: 26px;
        font-weight: 750;
        color: #163626;
        line-height: 1.2;
    }

    .stat-card p {
        margin: 6px 0 0;
        color: #4c6456;
        font-size: 11px;
    }

    /* PANEL GRAFIK & PEMINJAMAN MENDATANG */
    .dashboard-analytics-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    /* KONTROL TAHUN DI POJOK KANAN GRAFIK */
    .chart-year-nav {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 2px;
        padding: 3px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(35, 124, 77, 0.25);
        box-shadow: 0 2px 6px rgba(22, 54, 38, 0.04);
        flex-shrink: 0;
    }

    .chart-year-btn {
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 50%;
        background: transparent;
        color: #1f7a49;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        transition: background .18s ease, color .18s ease, opacity .18s ease;
    }

    .chart-year-btn:hover:not(:disabled) {
        background: #e5f3e9;
        color: #163626;
    }

    .chart-year-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
    }

    .chart-year-btn svg {
        width: 14px;
        height: 14px;
    }

    .chart-year-dropdown {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .chart-year-trigger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        color: #163626;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 800;
        padding: 4px 10px;
        margin: 0;
        border-radius: 999px;
        cursor: pointer;
        font-variant-numeric: tabular-nums;
        line-height: 1.2;
        transition: background .18s ease, color .18s ease;
    }

    .chart-year-trigger:hover,
    .chart-year-dropdown.is-open .chart-year-trigger {
        background: rgba(35, 124, 77, 0.1) !important;
        color: #163626;
    }

    .chart-year-trigger svg {
        width: 13px;
        height: 13px;
        color: #1f7a49;
        flex-shrink: 0;
        transition: transform .2s ease;
    }

    .chart-year-dropdown.is-open .chart-year-trigger svg {
        transform: rotate(180deg);
    }

    .chart-year-menu {
        position: absolute;
        top: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        min-width: 86px;
        padding: 4px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid rgba(35, 124, 77, 0.22);
        box-shadow: 0 10px 24px rgba(22, 54, 38, 0.14);
        z-index: 40;
        display: none;
        flex-direction: column;
        gap: 2px;
    }

    .chart-year-dropdown.is-open .chart-year-menu {
        display: flex;
    }

    .chart-year-option {
        width: 100%;
        border: none;
        background: transparent;
        color: #163626;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        padding: 6px 10px;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        font-variant-numeric: tabular-nums;
        transition: background .15s ease, color .15s ease;
    }

    .chart-year-option:hover {
        background: #eaf5ed;
        color: #1f7a49;
    }

    .chart-year-option.is-active {
        background: #237c4d;
        color: #ffffff;
    }

    .chart-canvas-wrap {
        position: relative;
        width: 100%;
        height: 255px;
        margin-top: 4px;
        padding: 10px 12px 8px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(22, 54, 38, 0.08);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }

    .chart-toolbar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
        min-height: 24px;
        flex-wrap: wrap;
    }

    .chart-legend-inline {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        font-size: 11px;
        font-weight: 600;
        color: #4c6456;
    }

    .chart-legend-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .chart-legend-dot {
        width: 9px;
        height: 9px;
        border-radius: 3px;
        display: inline-block;
    }

    .chart-legend-dot.is-approved {
        background: #237c4d;
    }

    .chart-legend-dot.is-rejected {
        background: #dc4545;
    }

    .chart-click-detail {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 3px 10px;
        border-radius: 999px;
        background: #eaf5ed;
        border: 1px solid rgba(35, 124, 77, 0.28);
        font-size: 11px;
        font-weight: 700;
        color: #163626;
    }

    .chart-click-detail.is-hidden {
        display: none;
    }

    .chart-click-detail .detail-approved {
        color: #1b653c;
    }

    .chart-click-detail .detail-rejected {
        color: #b91c1c;
    }

    .chart-click-clear {
        border: none;
        background: transparent;
        color: #5c7265;
        font-size: 13px;
        line-height: 1;
        padding: 0 2px;
        cursor: pointer;
    }

    .chart-click-clear:hover {
        color: #163626;
    }

    .chart-canvas-inner {
        position: relative;
        flex: 1;
        min-height: 0;
        width: 100%;
    }

    .chart-canvas-inner canvas {
        cursor: pointer;
    }

    /* KARTU PEMINJAMAN MENDATANG */
    .upcoming-feed-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
        max-height: 255px;
        overflow-y: auto;
        padding: 2px 3px 4px 1px;
        margin-top: 4px;
        flex: 1;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
    }

    .upcoming-feed-list::-webkit-scrollbar {
        width: 5px;
    }

    .upcoming-feed-list::-webkit-scrollbar-thumb {
        background: rgba(22, 54, 38, 0.18);
        border-radius: 999px;
    }

    .upcoming-card {
        display: flex;
        align-items: stretch;
        gap: 13px;
        padding: 13px 15px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(22, 54, 38, 0.09);
        box-shadow: 0 2px 8px rgba(22, 54, 38, 0.03);
        transition: background .18s ease, border-color .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    .upcoming-card:hover {
        background: #ffffff;
        border-color: rgba(35, 124, 77, 0.34);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(22, 54, 38, 0.07);
    }

    .upcoming-date-box {
        flex-shrink: 0;
        width: 62px;
        min-height: 64px;
        border-radius: 12px;
        background: linear-gradient(145deg, #eaf5ed 0%, #dff0e5 100%);
        border: 1px solid rgba(35, 124, 77, 0.22);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 6px 4px;
        box-sizing: border-box;
    }

    .upcoming-date-day {
        font-size: 19px;
        font-weight: 800;
        line-height: 1.05;
        color: #163626;
        font-variant-numeric: tabular-nums;
    }

    .upcoming-date-month {
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: 0.04em;
        color: #1f7a49;
        margin-top: 3px;
        text-transform: uppercase;
    }

    .upcoming-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .upcoming-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .upcoming-heading {
        min-width: 0;
    }

    .upcoming-title {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #163626;
        line-height: 1.3;
        letter-spacing: -0.015em;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color .15s ease;
    }

    .upcoming-card:hover .upcoming-title {
        color: #1f7a49;
    }

    .upcoming-org {
        margin: 3px 0 0;
        font-size: 11.5px;
        font-weight: 600;
        color: #1f7a49;
        display: flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
    }

    .upcoming-org svg {
        width: 13px;
        height: 13px;
        color: #237c4d;
        flex-shrink: 0;
    }

    .upcoming-org span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .upcoming-status-pill {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
        white-space: nowrap;
    }

    .upcoming-status-pill.is-ongoing {
        background: #dff3e6;
        color: #1b653c;
        border: 1px solid rgba(35, 124, 77, 0.24);
    }

    .upcoming-status-pill.is-ongoing::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #237c4d;
        flex-shrink: 0;
    }

    .upcoming-status-pill.is-upcoming {
        background: #fef3c7;
        color: #854d0e;
        border: 1px solid rgba(217, 119, 6, 0.22);
    }

    .upcoming-schedule-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 7px;
        border-top: 1px solid rgba(22, 54, 38, 0.06);
    }

    .upcoming-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11.5px;
        font-weight: 500;
        color: #3d5446;
        min-width: 0;
    }

    .upcoming-meta-item svg {
        width: 13.5px;
        height: 13.5px;
        color: #6b8274;
        flex-shrink: 0;
    }

    .upcoming-meta-item span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    /* PANEL TENGAH */
    .dashboard-middle {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(280px, 1fr);
        gap: 16px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    .dashboard-panel {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.28) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        border-radius: 16px;
        padding: 16px 20px 18px 20px !important;
        min-width: 0;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.05) !important;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .dashboard-panel .section-head {
        margin-top: 0 !important;
        padding-top: 0 !important;
        margin-bottom: 14px;
    }

    .dashboard-panel .section-head h2 {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .section-head-title {
        display: flex;
        align-items: baseline;
        gap: 7px;
    }

    .section-head h2 {
        font-size: 15.5px;
        font-weight: 800;
        color: #163626;
        margin: 0 0 2px;
        letter-spacing: -0.02em;
    }

    .section-head-count {
        font-size: 12px;
        font-weight: 700;
        color: #64786c;
    }

    .section-head p {
        font-size: 11px;
        color: #4c6456;
        margin: 0;
    }

    .section-head a {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #1f7a49;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        transition: gap 0.2s ease, color 0.2s ease;
    }

    .section-head a svg {
        width: 14px;
        height: 14px;
    }

    .section-head a:hover {
        color: #155a35;
        gap: 7px;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    /* KARTU PENGAJUAN TERBARU */
    .borrower-feed-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
        max-height: 292px;
        overflow-y: auto;
        padding: 2px 3px 4px 1px;
        margin-top: 2px;
        flex: 1;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
    }

    .borrower-feed-list::-webkit-scrollbar {
        width: 5px;
    }

    .borrower-feed-list::-webkit-scrollbar-thumb {
        background: rgba(22, 54, 38, 0.18);
        border-radius: 999px;
    }

    .proto-card {
        display: block;
        padding: 14px 17px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(22, 54, 38, 0.09);
        box-shadow: 0 2px 8px rgba(22, 54, 38, 0.03);
        text-decoration: none;
        color: #163626;
        transition: background .18s ease, border-color .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    .proto-card:hover {
        background: #ffffff;
        border-color: rgba(35, 124, 77, 0.34);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(22, 54, 38, 0.07);
    }

    .proto-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 11px;
    }

    .proto-card-heading {
        min-width: 0;
    }

    .proto-card-title {
        margin: 0;
        font-size: 14.5px;
        font-weight: 800;
        color: #163626;
        line-height: 1.35;
        letter-spacing: -0.015em;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color .15s ease;
    }

    .proto-card:hover .proto-card-title {
        color: #1f7a49;
    }

    .proto-card-sub {
        margin: 4px 0 0;
        font-size: 12px;
        color: #5c7265;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        min-width: 0;
    }

    .proto-sub-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        max-width: 100%;
    }

    .proto-sub-item svg {
        width: 13px;
        height: 13px;
        color: #237c4d;
        flex-shrink: 0;
    }

    .proto-card-sub .event-name {
        font-weight: 600;
        color: #465d50;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .proto-card-sub .sub-dot {
        color: #9cb0a4;
        flex-shrink: 0;
    }

    .proto-card-sub .org-name {
        font-weight: 600;
        color: #1f7a49;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .proto-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        line-height: 1.2;
    }

    .proto-badge.pending {
        background: #fef3c7;
        color: #854d0e;
        border: 1px solid rgba(217, 119, 6, 0.22);
    }

    .proto-badge.disetujui,
    .proto-badge.approved {
        background: #dff3e6;
        color: #1b653c;
        border: 1px solid rgba(35, 124, 77, 0.24);
    }

    .proto-badge.ditolak,
    .proto-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid rgba(220, 69, 69, 0.22);
    }

    .proto-badge.revisi {
        background: #fef3c7;
        color: #854d0e;
        border: 1px solid rgba(217, 119, 6, 0.22);
    }

    .proto-badge.dibatalkan,
    .proto-badge.cancelled {
        background: #e2e8f0;
        color: #475569;
        border: 1px solid rgba(71, 85, 105, 0.2);
    }

    .proto-schedule-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 9px;
        border-top: 1px solid rgba(22, 54, 38, 0.06);
    }

    .proto-meta-item {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 500;
        color: #3d5446;
        min-width: 0;
    }

    .proto-meta-item svg {
        width: 14px;
        height: 14px;
        color: #6b8274;
        flex-shrink: 0;
    }

    .proto-meta-item span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .dash-empty-state {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #5e7366;
        padding: 32px 16px;
        font-size: 12.5px;
        background: rgba(255, 255, 255, 0.55);
        border: 1px dashed rgba(22, 54, 38, 0.16);
        border-radius: 13px;
    }

    /* INFORMASI SIPADU */
    .guide-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 2px;
        flex: 1;
    }

    .guide-item {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        background: rgba(255, 255, 255, 0.48) !important;
        border: 1px solid rgba(255, 255, 255, 0.42) !important;
        border-radius: 13px;
        padding: 12px 14px;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .guide-item:hover {
        background: rgba(255, 255, 255, 0.72) !important;
        transform: translateX(2px);
    }

    .guide-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: rgba(33, 128, 77, 0.16);
        color: #1f7a49;
    }

    .guide-icon svg {
        width: 16px;
        height: 16px;
    }

    .guide-item strong {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #163626;
        margin-bottom: 2px;
    }

    .guide-item p {
        font-size: 11px;
        color: #4c6456;
        line-height: 1.4;
        margin: 0;
    }

    /* AUDITORIUM */
    .auditorium-section {
        margin-top: 0;
    }

    .auditorium-carousel-wrap {
        position: relative;
        min-width: 0;
    }

    .auditorium-carousel-controls {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .auditorium-arrow {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(35, 124, 77, .22);
        border-radius: 50%;
        background: rgba(255,255,255,.72);
        color: #237c4d;
        cursor: pointer;
        transition: background .2s ease, transform .2s ease, opacity .2s ease;
    }

    .auditorium-arrow:hover { background: #e5f3e9; transform: scale(1.06); }
    .auditorium-arrow svg { width: 17px; height: 17px; }

    .auditorium-cards {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
        padding: 3px 2px 12px;
        overscroll-behavior-x: contain;
    }
    .auditorium-cards::-webkit-scrollbar {
        height: 5px;
    }
    .auditorium-cards::-webkit-scrollbar-track {
        background: transparent;
    }
    .auditorium-cards::-webkit-scrollbar-thumb {
        background: rgba(22, 54, 38, 0.18);
        border-radius: 999px;
    }
    .auditorium-cards > .auditorium-card {
        flex: 0 0 calc((100% - 32px) / 3);
        scroll-snap-align: start;
    }

    .auditorium-card {
        background: rgba(255, 255, 255, 0.42) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        min-width: 0;
        box-shadow: 0 4px 16px rgba(22, 54, 38, 0.04) !important;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        padding: 0 !important;
        transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
    }

    .auditorium-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.68) !important;
        box-shadow: 0 12px 26px rgba(22, 54, 38, 0.1) !important;
    }

    .auditorium-card:focus-visible {
        outline: 2px solid #237c4d;
        outline-offset: 3px;
    }

    .auditorium-card.is-inactive-card {
        background: rgba(255, 255, 255, 0.34) !important;
        border-color: rgba(252, 165, 165, 0.55) !important;
    }

    .auditorium-image-wrap {
        width: 100% !important;
        height: 175px !important;
        overflow: hidden !important;
        position: relative;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #eaf4ee;
    }

    .aud-slider {
        width: 100%;
        height: 175px;
        position: relative;
        overflow: hidden;
    }

    .aud-slider.is-modal-slider {
        height: 250px;
    }

    .aud-slider-track {
        display: flex;
        width: 100%;
        height: 100%;
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .aud-slide {
        flex: 0 0 100%;
        width: 100%;
        height: 100%;
    }

    .aud-slide img,
    .auditorium-card img,
    .auditorium-image-wrap img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 0 !important;
        margin: 0 !important;
        transition: transform .2s ease;
    }

    .auditorium-card:hover .aud-slide img {
        transform: scale(1.04);
    }

    .aud-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.92);
        color: #163626;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16);
        opacity: 0.88;
        transition: background .2s ease, transform .2s ease, opacity .2s ease;
    }

    .aud-slider-btn:hover {
        background: #ffffff;
        opacity: 1;
        transform: translateY(-50%) scale(1.08);
    }

    .aud-slider-btn.prev {
        left: 10px;
    }

    .aud-slider-btn.next {
        right: 10px;
    }

    .aud-slider-count {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 3;
        padding: 3px 9px;
        border-radius: 999px;
        background: rgba(15, 32, 23, 0.62);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.02em;
        backdrop-filter: blur(4px);
    }

    .aud-slider.is-modal-slider .aud-slider-count {
        right: auto;
        left: 14px;
        top: 14px;
    }

    .aud-slider-dots {
        position: absolute;
        bottom: 9px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(15, 32, 23, 0.38);
        backdrop-filter: blur(4px);
    }

    .aud-slider-dot {
        width: 6px;
        height: 6px;
        padding: 0;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.52);
        cursor: pointer;
        transition: width .2s ease, background .2s ease;
    }

    .aud-slider-dot.is-active {
        width: 14px;
        border-radius: 999px;
        background: #ffffff;
    }

    .auditorium-no-image {
        width: 100% !important;
        height: 175px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dff0e5, #eef7f1);
        color: #237c4d;
        border-radius: 0 !important;
        margin: 0 !important;
    }

    .auditorium-no-image svg {
        width: 32px;
        height: 32px;
    }

    .auditorium-card-content {
        padding: 14px 16px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .auditorium-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }

    .auditorium-card-content h3 {
        margin: 0;
        color: #163626;
        font-size: 16px;
        font-weight: 750;
        line-height: 1.35;
    }

    .aud-status-pill {
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .aud-status-pill.is-active {
        color: #065f46;
        background: #bbf7d0;
        border: 1px solid #34d399;
    }

    .aud-status-pill.is-inactive {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fca5a5;
    }

    .auditorium-description {
        color: #4c6456;
        font-size: 11.5px;
        line-height: 1.5;
        margin: 0 0 10px;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 34px;
    }

    .auditorium-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .auditorium-meta span {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        font-size: 11px;
        color: #3c5446;
        font-weight: 500;
        line-height: 1.4;
    }

    .auditorium-meta svg {
        width: 14px;
        height: 14px;
        color: #237c4d;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .aud-inactive-alert {
        margin-bottom: 12px;
        padding: 9px 11px;
        border-radius: 9px;
        background: rgba(254, 242, 242, 0.96);
        border: 1px solid #fca5a5;
        color: #991b1b;
        font-size: 11px;
        line-height: 1.5;
        word-break: break-word;
    }

    .aud-inactive-alert strong {
        display: block;
        color: #7f1d1d;
        margin-bottom: 2px;
    }

    .auditorium-actions {
        display: grid;
        grid-template-columns: 1fr;
        margin-top: auto;
    }

    .auditorium-actions a,
    .auditorium-actions .btn-booking-disabled {
        width: 100%;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-align: center;
        text-decoration: none;
        padding: 10px 12px;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        transition: .2s;
        position: relative;
        z-index: 2;
    }

    .auditorium-actions a svg,
    .auditorium-actions .btn-booking-disabled svg {
        width: 15px;
        height: 15px;
    }

    .btn-booking {
        color: white;
        background: #237c4d;
        border: 1px solid #237c4d;
    }

    .btn-booking:hover {
        background: #1a633c;
        color: white;
    }

    .btn-booking-disabled {
        color: #991b1b;
        background: rgba(254, 226, 226, 0.9);
        border: 1px solid #fca5a5;
        cursor: not-allowed;
        opacity: 0.92;
    }

    .empty-auditorium {
        background: rgba(255, 255, 255, 0.42);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        color: #4c6456;
        font-size: 13px;
        grid-column: 1 / -1;
    }

    /* SLIDE-OVER DETAIL DRAWER */
    .auditorium-modal {
        position: fixed;
        inset: 0;
        z-index: 9995;
        display: flex;
        align-items: stretch;
        justify-content: flex-end;
        padding: 18px;
        background: rgba(12, 28, 19, 0.36);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.32s ease, visibility 0.32s ease;
    }

    .auditorium-modal.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .auditorium-modal-box {
        position: relative;
        width: min(490px, calc(100vw - 36px));
        max-width: 490px;
        height: 100%;
        max-height: calc(100dvh - 36px);
        margin-left: auto;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
        background: #ffffff;
        border: 1px solid #e2ebe5;
        border-radius: 20px;
        box-shadow: -12px 10px 42px rgba(22, 54, 38, 0.16);
        transform: translate3d(calc(100% + 28px), 0, 0);
        transition: transform 0.38s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .auditorium-modal-box::-webkit-scrollbar {
        width: 5px;
    }

    .auditorium-modal-box::-webkit-scrollbar-thumb {
        background: rgba(22, 54, 38, 0.18);
        border-radius: 999px;
    }

    .auditorium-modal.show .auditorium-modal-box {
        transform: translate3d(0, 0, 0);
    }

    .modal-close {
        position: sticky;
        float: right;
        top: 14px;
        right: 14px;
        margin-right: 14px;
        margin-bottom: -42px;
        z-index: 4;
        width: 34px;
        height: 34px;
        border: 1px solid #dce5df;
        border-radius: 10px;
        background: rgba(255, 255, 255, .95);
        color: #163626;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .12);
        transition: background .18s ease;
    }

    .modal-close:hover {
        background: #e9f5ed;
    }

    .modal-close svg {
        width: 17px;
        height: 17px;
    }

    .modal-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
    }

    .modal-no-image {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #eaf5ed, #f4f8f5);
        color: #237c4d;
    }

    .modal-no-image svg {
        width: 48px;
        height: 48px;
    }

    .modal-content {
        padding: 22px 24px 24px;
    }

    .modal-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .modal-content h2 {
        color: #163626;
        font-size: 20px;
        font-weight: 800;
        margin: 0;
        line-height: 1.3;
    }

    .modal-description {
        font-size: 13px;
        color: #4c6456;
        line-height: 1.7;
        margin-bottom: 18px;
        white-space: pre-line;
    }

    .modal-deactivation-box {
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        font-size: 12.5px;
        line-height: 1.55;
        word-break: break-word;
    }

    .modal-deactivation-box strong {
        display: block;
        color: #7f1d1d;
        margin-bottom: 3px;
    }

    .modal-info-title {
        color: #163626;
        font-size: 13px;
        font-weight: 800;
        margin: 18px 0 10px;
    }

    .modal-info-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .modal-info-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 12.5px;
        color: #4c6456;
        line-height: 1.6;
    }

    .modal-info-item strong {
        color: #163626;
    }

    .modal-info-item svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: #237c4d;
        margin-top: 2px;
    }

    .modal-facilities {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .facility-tag {
        padding: 7px 11px;
        border-radius: 8px;
        background: #eaf5ed;
        color: #237c4d;
        font-size: 11.5px;
        font-weight: 600;
    }

    .modal-actions {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #ebf0ed;
        display: flex;
        justify-content: flex-end;
    }

    .modal-actions a,
    .modal-actions .modal-btn-disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 10px;
        padding: 11px 18px;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
    }

    .modal-actions a {
        background: #237c4d;
        color: white;
        border: 1px solid #237c4d;
    }

    .modal-actions a:hover {
        background: #1a633c;
        color: white;
    }

    .modal-actions .modal-btn-disabled {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        cursor: not-allowed;
        opacity: 0.92;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .auditorium-cards > .auditorium-card { flex-basis: calc((100% - 16px) / 2); }
    }

    @media (max-width: 900px) {
        .dashboard-analytics-row,
        .dashboard-middle {
            grid-template-columns: 1fr;
        }

        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .auditorium-modal {
            padding: 12px;
        }

        .auditorium-modal-box {
            width: min(440px, calc(100vw - 24px));
            max-height: calc(100dvh - 24px);
            border-radius: 18px;
        }
    }

    @media (max-width: 550px) {
        .dashboard-topbar {
            align-items: flex-start;
        }

        .dashboard-welcome h1 {
            font-size: 22px;
        }

        .dashboard-topbar .dashboard-action {
            width: 100%;
        }

        .stats {
            gap: 10px;
        }

        .stat-card {
            padding: 13px;
        }

        .stat-card strong {
            font-size: 22px;
        }

        .upcoming-card {
            padding: 12px;
            gap: 10px;
        }

        .upcoming-date-box {
            width: 54px;
            min-height: 56px;
            border-radius: 10px;
        }

        .upcoming-date-day {
            font-size: 16px;
        }

        .upcoming-date-month {
            font-size: 9px;
        }

        .upcoming-card-top {
            flex-wrap: wrap;
            gap: 6px;
        }

        .upcoming-schedule-bar,
        .proto-schedule-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .auditorium-cards > .auditorium-card { flex-basis: 85%; }
        .auditorium-arrow { width: 32px; height: 32px; }

        .dashboard-panel {
            padding: 14px 16px 16px 16px !important;
        }

        .auditorium-modal {
            padding: 10px;
        }

        .auditorium-modal-box {
            width: 100%;
            max-width: calc(100vw - 20px);
            max-height: calc(100dvh - 20px);
            border-radius: 18px;
        }

        .aud-slider.is-modal-slider,
        .modal-image {
            height: 210px !important;
        }

        .modal-content {
            padding: 18px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .proto-card,
        .upcoming-card,
        .guide-item,
        .auditorium-card {
            transition: none;
        }
    }
</style>

<div class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-topbar">
        <div class="dashboard-welcome">
            <h1 data-typewriter="Selamat datang, {{ $user->name }}!">Selamat datang, {{ $user->name }}!</h1>
            <p>Temukan auditorium impian Anda untuk pengajuan peminjaman Anda.</p>
        </div>

        @if($hasActiveAuditorium)
            <a class="dashboard-action" href="{{ route('borrower.booking.create') }}">
                <i data-lucide="plus"></i>
                Ajukan Peminjaman
            </a>
        @else
            <button type="button" class="dashboard-action is-disabled" disabled aria-disabled="true" title="Semua auditorium sedang dinonaktifkan">
                <i data-lucide="ban"></i>
                Ajukan Peminjaman
            </button>
        @endif
    </div>

    {{-- STATISTIK / SCORECARD --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-top">
                <small>Total Pengajuan</small>
                <span class="stat-icon blue">
                    <i data-lucide="calendar-days"></i>
                </span>
            </div>
            <strong>{{ $stats['total'] }}</strong>
            <p>Seluruh pengajuan Anda</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Pending</small>
                <span class="stat-icon amber">
                    <i data-lucide="clock"></i>
                </span>
            </div>
            <strong>{{ $stats['pending'] }}</strong>
            <p>Menunggu keputusan Admin</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Disetujui</small>
                <span class="stat-icon green">
                    <i data-lucide="check-circle-2"></i>
                </span>
            </div>
            <strong>{{ $stats['approved'] }}</strong>
            <p>Pengajuan telah disetujui</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Ditolak</small>
                <span class="stat-icon red">
                    <i data-lucide="x-circle"></i>
                </span>
            </div>
            <strong>{{ $stats['rejected'] }}</strong>
            <p>Lihat detail alasan</p>
        </div>
    </div>

    {{-- GRAFIK PEMINJAMAN & PEMINJAMAN MENDATANG --}}
    <div class="dashboard-analytics-row">
        {{-- KIRI: GRAFIK STATISTIK PEMINJAMAN --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <div class="section-head-title">
                        <h2>Statistik Peminjaman</h2>
                    </div>
                    <p>Klik atau arahkan kursor pada grafik untuk rincian bulanan</p>
                </div>

                {{-- NAVIGASI TAHUN --}}
                <div class="chart-year-nav" aria-label="Pilih tahun statistik peminjaman">
                    <button
                        type="button"
                        class="chart-year-btn"
                        id="chartPrevYearBtn"
                        aria-label="Tahun sebelumnya"
                        title="Tahun sebelumnya"
                    >
                        <i data-lucide="chevron-left"></i>
                    </button>

                    <div class="chart-year-dropdown" id="chartYearDropdown">
                        <button
                            type="button"
                            class="chart-year-trigger"
                            id="chartYearTrigger"
                            aria-haspopup="listbox"
                            aria-expanded="false"
                            aria-label="Pilih tahun"
                        >
                            <span id="chartYearLabel">{{ $defaultYearSafe }}</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>

                        <div class="chart-year-menu" id="chartYearMenu" role="listbox" aria-label="Daftar tahun">
                            @foreach($availableYearsSafe as $yr)
                                <button
                                    type="button"
                                    class="chart-year-option {{ (int) $yr === $defaultYearSafe ? 'is-active' : '' }}"
                                    data-year="{{ $yr }}"
                                    role="option"
                                    aria-selected="{{ (int) $yr === $defaultYearSafe ? 'true' : 'false' }}"
                                >
                                    {{ $yr }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <button
                        type="button"
                        class="chart-year-btn"
                        id="chartNextYearBtn"
                        aria-label="Tahun berikutnya"
                        title="Tahun berikutnya"
                    >
                        <i data-lucide="chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="chart-canvas-wrap">
                <div class="chart-toolbar-row">
                    <div class="chart-legend-inline">
                        <span class="chart-legend-item">
                            <span class="chart-legend-dot is-approved"></span>
                            Disetujui
                        </span>
                        <span class="chart-legend-item">
                            <span class="chart-legend-dot is-rejected"></span>
                            Ditolak
                        </span>
                    </div>

                    <div class="chart-click-detail is-hidden" id="chartClickDetail">
                        <span id="chartDetailMonth">Jan 2026</span>
                        <span>•</span>
                        <span class="detail-approved" id="chartDetailApproved">Disetujui: 0</span>
                        <span>|</span>
                        <span class="detail-rejected" id="chartDetailRejected">Ditolak: 0</span>
                        <button type="button" class="chart-click-clear" id="chartDetailClose" aria-label="Tutup rincian">&times;</button>
                    </div>
                </div>

                <div class="chart-canvas-inner">
                    <canvas
                        id="sipaduBorrowerChart"
                        data-labels="{{ json_encode($chartLabelsSafe) }}"
                        data-yearly="{{ json_encode($chartByYearSafe) }}"
                        data-years="{{ json_encode($availableYearsSafe) }}"
                        data-default-year="{{ $defaultYearSafe }}"
                    ></canvas>
                </div>
            </div>
        </div>

        {{-- KANAN: PEMINJAMAN MENDATANG --}}
        <div class="dashboard-panel" id="borrowerUpcomingPanel">
            <div class="section-head">
                <div>
                    <div class="section-head-title">
                        <h2>Peminjaman Mendatang</h2>
                        <span class="section-head-count" id="borrowerUpcomingCount">({{ $upcomingList->count() }})</span>
                    </div>
                    <p>Jadwal penggunaan auditorium terdekat yang telah disetujui</p>
                </div>
            </div>

            @if($upcomingList->isNotEmpty())
                <div class="upcoming-feed-list" id="borrowerUpcomingList">
                    @foreach($upcomingList as $item)
                        @php
                            $tglObj = $item->display_date
                                ? \Carbon\Carbon::parse($item->display_date, 'Asia/Jakarta')->locale('id')
                                : \Carbon\Carbon::parse($item->booking_date, 'Asia/Jakarta')->locale('id');
                            $eventDay = $tglObj->copy()->startOfDay();
                            $diffDays = (int) $todayStartJakarta->diffInDays($eventDay, false);

                            $endTimeRaw = !empty($item->end_time)
                                ? trim((string) $item->end_time)
                                : (!empty($item->display_end_time) ? str_replace('.', ':', trim((string) $item->display_end_time)) . ':00' : '23:59:59');
                            $endTimestampMs = \Carbon\Carbon::parse($tglObj->toDateString() . ' ' . $endTimeRaw, 'Asia/Jakarta')->valueOf();

                            $boxDay = $tglObj->format('d');
                            $boxMonthYear = strtoupper($tglObj->translatedFormat('M Y'));
                            $fullDateText = $tglObj->translatedFormat('d F Y');

                            $startFmt = $item->display_start_time
                                ?? str_replace(':', '.', substr((string) $item->start_time, 0, 5));
                            $endFmt = $item->display_end_time
                                ?? str_replace(':', '.', substr((string) $item->end_time, 0, 5));

                            $venueName = optional($item->auditorium)->name ?? $item->event_name ?? 'Auditorium';
                            $organizerName = $item->display_organizer
                                ?? ($item->organization ?: (optional($item->user)->name ?? '-'));

                            $isToday = $diffDays <= 0;
                            $badgeLabel = $isToday
                                ? 'Hari ini'
                                : ('Dalam ' . $diffDays . ' hari');
                        @endphp

                        <div class="upcoming-card" data-end-ts="{{ $endTimestampMs }}">
                            <div class="upcoming-date-box">
                                <span class="upcoming-date-day">{{ $boxDay }}</span>
                                <span class="upcoming-date-month">{{ $boxMonthYear }}</span>
                            </div>

                            <div class="upcoming-body">
                                <div class="upcoming-card-top">
                                    <div class="upcoming-heading">
                                        <h3 class="upcoming-title" title="{{ $venueName }}">{{ $venueName }}</h3>
                                        <p class="upcoming-org" title="{{ $organizerName }}">
                                            <i data-lucide="user"></i>
                                            <span>{{ $organizerName }}</span>
                                        </p>
                                    </div>

                                    <span class="upcoming-status-pill {{ $isToday ? 'is-ongoing' : 'is-upcoming' }}">
                                        {{ $badgeLabel }}
                                    </span>
                                </div>

                                <div class="upcoming-schedule-bar">
                                    <div class="upcoming-meta-item">
                                        <i data-lucide="calendar"></i>
                                        <span>{{ $fullDateText }}</span>
                                    </div>

                                    <div class="upcoming-meta-item">
                                        <i data-lucide="clock"></i>
                                        <span>{{ $startFmt }} WIB - {{ $endFmt }} WIB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="dash-empty-state">
                    Belum ada jadwal peminjaman auditorium mendatang.
                </div>
            @endif
        </div>
    </div>

    {{-- PENGAJUAN DAN INFORMASI --}}
    <div class="dashboard-middle">

        {{-- PENGAJUAN TERBARU --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <div class="section-head-title">
                        <h2>Pengajuan Terbaru</h2>
                        <span class="section-head-count">({{ $bookings->count() }})</span>
                    </div>
                    <p>Ringkasan pengajuan peminjaman Anda</p>
                </div>

                <a href="{{ route('borrower.bookings') }}">
                    Lihat semua
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            @if($bookings->isNotEmpty())
                <div class="borrower-feed-list">
                    @foreach($bookings as $b)
                        @php
                            $statusRaw = strtolower(trim((string) $b->status));
                            $badgeClass = match ($statusRaw) {
                                'disetujui', 'approved' => 'disetujui',
                                'ditolak', 'rejected' => 'ditolak',
                                'perlu revisi' => 'revisi',
                                'dibatalkan', 'cancelled' => 'dibatalkan',
                                default => 'pending',
                            };

                            $bDate = $b->booking_date
                                ? \Carbon\Carbon::parse($b->booking_date, 'Asia/Jakarta')->locale('id')->translatedFormat('d F Y')
                                : '-';
                            $bTime = !empty($b->start_time)
                                ? str_replace(':', '.', substr($b->start_time, 0, 5)) . ' WIB'
                                  . (!empty($b->end_time) ? ' - ' . str_replace(':', '.', substr($b->end_time, 0, 5)) . ' WIB' : '')
                                : 'Waktu menyesuaikan';
                            $bOrg = trim((string) ($b->organization ?? ''));
                            $bVenue = optional($b->auditorium)->name ?? $b->event_name;
                            $hasDistinctEvent = !empty(optional($b->auditorium)->name) && !empty($b->event_name);
                        @endphp

                        <a href="{{ route('borrower.bookings', ['booking_id' => $b->id]) }}" class="proto-card">
                            <div class="proto-card-top">
                                <div class="proto-card-heading">
                                    <h3 class="proto-card-title">{{ $bVenue }}</h3>
                                    @if($hasDistinctEvent || $bOrg !== '')
                                        <p class="proto-card-sub">
                                            @if($hasDistinctEvent)
                                                <span class="proto-sub-item" title="{{ $b->event_name }}">
                                                    <i data-lucide="bookmark"></i>
                                                    <span class="event-name">{{ $b->event_name }}</span>
                                                </span>
                                            @endif
                                            @if($hasDistinctEvent && $bOrg !== '')
                                                <span class="sub-dot">•</span>
                                            @endif
                                            @if($bOrg !== '')
                                                <span class="proto-sub-item" title="{{ $bOrg }}">
                                                    <i data-lucide="user"></i>
                                                    <span class="org-name">{{ $bOrg }}</span>
                                                </span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <span class="proto-badge {{ $badgeClass }}">{{ $b->status }}</span>
                            </div>

                            <div class="proto-schedule-bar">
                                <div class="proto-meta-item">
                                    <i data-lucide="calendar"></i>
                                    <span>{{ $bDate }}</span>
                                </div>
                                <div class="proto-meta-item">
                                    <i data-lucide="clock"></i>
                                    <span>{{ $bTime }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty-state">
                    Belum ada pengajuan peminjaman.
                </div>
            @endif
        </div>

        {{-- INFORMASI SIPADU --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <h2>Informasi SIPADU</h2>
                    <p>Panduan singkat peminjaman</p>
                </div>

                <a href="{{ route('borrower.guides') }}">
                    Informasi lebih lanjut
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div class="guide-list">
                <div class="guide-item">
                    <div class="guide-icon">
                        <i data-lucide="building-2"></i>
                    </div>
                    <div>
                        <strong>Pilih Auditorium</strong>
                        <p>Lihat informasi ruangan sebelum mengajukan peminjaman.</p>
                    </div>
                </div>

                <div class="guide-item">
                    <div class="guide-icon">
                        <i data-lucide="file-check"></i>
                    </div>
                    <div>
                        <strong>Lengkapi Pengajuan</strong>
                        <p>Isi informasi kegiatan dan jadwal dengan benar.</p>
                    </div>
                </div>

                <div class="guide-item">
                    <div class="guide-icon">
                        <i data-lucide="list-checks"></i>
                    </div>
                    <div>
                        <strong>Pantau Status</strong>
                        <p>Pantau perkembangan pengajuan di menu Peminjaman Saya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR AUDITORIUM --}}
    <div class="dashboard-panel auditorium-section">
        <div class="section-head">
            <div>
                <h2>Pilihan Auditorium</h2>
                <p>Geser untuk melihat auditorium lainnya</p>
            </div>
            <div class="auditorium-carousel-controls" aria-label="Navigasi pilihan auditorium">
                <button type="button" class="auditorium-arrow" aria-label="Auditorium sebelumnya" onclick="scrollAuditoriums(this, -1)"><i data-lucide="chevron-left"></i></button>
                <button type="button" class="auditorium-arrow" aria-label="Auditorium berikutnya" onclick="scrollAuditoriums(this, 1)"><i data-lucide="chevron-right"></i></button>
            </div>
        </div>

        <div class="auditorium-carousel-wrap">
        <div class="auditorium-cards" id="auditoriumCards">
            @forelse($auditoriums as $a)

                @php
                    $isActive = (bool) ($a->is_active ?? true);
                    $deactivationReason = trim((string) (
                        $a->deactivation_reason
                        ?? $a->inactive_reason
                        ?? $a->keterangan_nonaktif
                        ?? $a->alasan_nonaktif
                        ?? $a->notes
                        ?? $a->note
                        ?? ''
                    ));

                    $description = is_string($a->description) && trim($a->description) !== ''
                        ? $a->description
                        : 'Informasi auditorium belum tersedia.';

                    $lokasiKampus = is_string($a->lokasi_kampus) && trim($a->lokasi_kampus) !== ''
                        ? trim($a->lokasi_kampus)
                        : (is_string($a->location) && trim($a->location) !== '' ? trim($a->location) : 'Lokasi kampus belum tersedia');

                    $lokasiGedung = is_string($a->lokasi_gedung) && trim($a->lokasi_gedung) !== ''
                        ? trim($a->lokasi_gedung)
                        : null;

                    $jamMulaiFmt = $a->jam_mulai
                        ? \Carbon\Carbon::parse($a->jam_mulai)->format('H:i')
                        : '07:00';

                    $jamSelesaiFmt = $a->jam_selesai
                        ? \Carbon\Carbon::parse($a->jam_selesai)->format('H:i')
                        : '17:00';

                    $jamOperasional = $jamMulaiFmt . ' - ' . $jamSelesaiFmt;

                    $capacity = is_scalar($a->capacity) && trim((string) $a->capacity) !== ''
                        ? (is_numeric($a->capacity) ? $a->capacity . ' orang' : $a->capacity)
                        : 'Belum tersedia';

                    $rawFacilities = $a->facilities;
                    if (is_string($rawFacilities)) {
                        $decodedFacilities = json_decode($rawFacilities, true);
                        $rawFacilities = is_array($decodedFacilities)
                            ? $decodedFacilities
                            : array_filter(array_map('trim', explode(',', $rawFacilities)));
                    }

                    $facilities = is_array($rawFacilities)
                        ? $rawFacilities
                        : [];

                    $facilities = array_filter($facilities, function ($item) {
                        return is_scalar($item) && trim((string) $item) !== '';
                    });

                    // Ambil seluruh foto auditorium (images array + fallback image_path lama)
                    $rawImages = $a->images;
                    if (is_string($rawImages)) {
                        $decodedImages = json_decode($rawImages, true);
                        $rawImages = is_array($decodedImages) ? $decodedImages : [];
                    }
                    $photoPaths = array_values(array_filter(
                        is_array($rawImages) ? $rawImages : [],
                        fn ($p) => is_string($p) && trim($p) !== ''
                    ));
                    if (empty($photoPaths) && is_string($a->image_path) && trim($a->image_path) !== '') {
                        $photoPaths[] = trim($a->image_path);
                    }

                    $photoItems = array_map(fn ($p) => [
                        'path' => $p,
                        'url'  => asset('storage/' . $p),
                    ], $photoPaths);

                    $shortDescription = \Illuminate\Support\Str::limit(
                        $description,
                        90
                    );
                @endphp

                {{-- KARTU AUDITORIUM --}}
                <article
                    class="auditorium-card {{ !$isActive ? 'is-inactive-card' : '' }}"
                    onclick="openAuditoriumModal('auditorium-modal-{{ $a->id }}')"
                    role="button"
                    tabindex="0"
                    aria-label="Lihat detail {{ $a->name }}"
                    onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openAuditoriumModal('auditorium-modal-{{ $a->id }}'); }"
                >

                    <div class="auditorium-image-wrap">
                        @if(count($photoItems) > 0)
                            <div class="aud-slider" data-aud-slider data-index="0" data-total="{{ count($photoItems) }}">
                                <div class="aud-slider-track">
                                    @foreach($photoItems as $idx => $item)
                                        <div class="aud-slide">
                                            <img
                                                src="{{ $item['url'] }}"
                                                alt="Foto {{ $a->name }} ({{ $idx + 1 }})"
                                                loading="lazy"
                                            >
                                        </div>
                                    @endforeach
                                </div>

                                @if(count($photoItems) > 1)
                                    <span class="aud-slider-count">1 / {{ count($photoItems) }}</span>

                                    <button type="button" class="aud-slider-btn prev" aria-label="Foto sebelumnya" onclick="event.stopPropagation(); stepAudSlider(this, -1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <button type="button" class="aud-slider-btn next" aria-label="Foto berikutnya" onclick="event.stopPropagation(); stepAudSlider(this, 1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>

                                    <div class="aud-slider-dots" onclick="event.stopPropagation()">
                                        @foreach($photoItems as $idx => $item)
                                            <button
                                                type="button"
                                                class="aud-slider-dot {{ $idx === 0 ? 'is-active' : '' }}"
                                                aria-label="Ke foto {{ $idx + 1 }}"
                                                data-slide-index="{{ $idx }}"
                                                onclick="event.stopPropagation(); goToAudSlide(this)"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="auditorium-no-image">
                                <i data-lucide="building-2"></i>
                            </div>
                        @endif
                    </div>

                    <div class="auditorium-card-content">
                        <div class="auditorium-card-head">
                            <h3>{{ $a->name }}</h3>
                            @if($isActive)
                                <span class="aud-status-pill is-active">Aktif</span>
                            @else
                                <span class="aud-status-pill is-inactive">Nonaktif</span>
                            @endif
                        </div>

                        <p class="auditorium-description">
                            {{ $shortDescription }}
                        </p>

                        <div class="auditorium-meta">
                            <span>
                                <i data-lucide="map-pin"></i>
                                {{ $lokasiKampus }}
                            </span>

                            @if($lokasiGedung)
                                <span>
                                    <i data-lucide="building"></i>
                                    {{ \Illuminate\Support\Str::limit($lokasiGedung, 55) }}
                                </span>
                            @endif

                            <span>
                                <i data-lucide="clock"></i>
                                Jam Operasional: {{ $jamOperasional }}
                            </span>

                            <span>
                                <i data-lucide="users"></i>
                                Kapasitas: {{ $capacity }}
                            </span>
                        </div>

                        @if(!$isActive && $deactivationReason !== '')
                            <div class="aud-inactive-alert">
                                <strong>Catatan Admin:</strong>
                                {!! nl2br(e($deactivationReason)) !!}
                            </div>
                        @endif

                        <div class="auditorium-actions">
                            @if($isActive)
                                <a
                                    href="{{ route('borrower.booking.create', ['auditorium' => $a->id]) }}"
                                    class="btn-booking"
                                    onclick="event.stopPropagation()"
                                >
                                    <i data-lucide="calendar-plus"></i>
                                    Ajukan Peminjaman
                                </a>
                            @else
                                <button
                                    type="button"
                                    class="btn-booking-disabled"
                                    disabled
                                    aria-disabled="true"
                                    title="Auditorium sedang dinonaktifkan"
                                    onclick="event.stopPropagation()"
                                >
                                    <i data-lucide="ban"></i>
                                    Ajukan Peminjaman
                                </button>
                            @endif
                        </div>
                    </div>
                </article>

                {{-- MODAL / DRAWER DETAIL LENGKAP --}}
                <div
                    class="auditorium-modal"
                    id="auditorium-modal-{{ $a->id }}"
                    onclick="if(event.target === this) closeAuditoriumModal(this.id)"
                >
                    <div class="auditorium-modal-box">

                        <button
                            type="button"
                            class="modal-close"
                            onclick="closeAuditoriumModal('auditorium-modal-{{ $a->id }}')"
                            aria-label="Tutup detail"
                        >
                            <i data-lucide="x"></i>
                        </button>

                        @if(count($photoItems) > 0)
                            <div class="aud-slider is-modal-slider" data-aud-slider data-index="0" data-total="{{ count($photoItems) }}">
                                <div class="aud-slider-track">
                                    @foreach($photoItems as $idx => $item)
                                        <div class="aud-slide">
                                            <img
                                                src="{{ $item['url'] }}"
                                                alt="Foto {{ $a->name }} ({{ $idx + 1 }})"
                                            >
                                        </div>
                                    @endforeach
                                </div>

                                @if(count($photoItems) > 1)
                                    <span class="aud-slider-count">1 / {{ count($photoItems) }}</span>

                                    <button type="button" class="aud-slider-btn prev" aria-label="Foto sebelumnya" onclick="stepAudSlider(this, -1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <button type="button" class="aud-slider-btn next" aria-label="Foto berikutnya" onclick="stepAudSlider(this, 1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>

                                    <div class="aud-slider-dots">
                                        @foreach($photoItems as $idx => $item)
                                            <button
                                                type="button"
                                                class="aud-slider-dot {{ $idx === 0 ? 'is-active' : '' }}"
                                                aria-label="Ke foto {{ $idx + 1 }}"
                                                data-slide-index="{{ $idx }}"
                                                onclick="goToAudSlide(this)"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="modal-no-image">
                                <i data-lucide="building-2"></i>
                            </div>
                        @endif

                        <div class="modal-content">
                            <div class="modal-title-row">
                                <h2>{{ $a->name }}</h2>
                                @if($isActive)
                                    <span class="aud-status-pill is-active">Aktif</span>
                                @else
                                    <span class="aud-status-pill is-inactive">Nonaktif</span>
                                @endif
                            </div>

                            @if(!$isActive && $deactivationReason !== '')
                                <div class="modal-deactivation-box">
                                    <strong>Catatan Admin</strong>
                                    {!! nl2br(e($deactivationReason)) !!}
                                </div>
                            @endif

                            <div class="modal-description">
                                {{ $description }}
                            </div>

                            <h3 class="modal-info-title">
                                Informasi Auditorium
                            </h3>

                            <div class="modal-info-list">
                                <div class="modal-info-item">
                                    <i data-lucide="map-pin"></i>
                                    <div>
                                        <strong>Lokasi Kampus</strong><br>
                                        {{ $lokasiKampus }}
                                    </div>
                                </div>

                                <div class="modal-info-item">
                                    <i data-lucide="building"></i>
                                    <div>
                                        <strong>Lokasi Gedung</strong><br>
                                        {{ $lokasiGedung ?: 'Informasi lokasi gedung belum tersedia' }}
                                    </div>
                                </div>

                                <div class="modal-info-item">
                                    <i data-lucide="clock"></i>
                                    <div>
                                        <strong>Jam Operasional</strong><br>
                                        {{ $jamOperasional }}
                                    </div>
                                </div>

                                <div class="modal-info-item">
                                    <i data-lucide="users"></i>
                                    <div>
                                        <strong>Kapasitas</strong><br>
                                        {{ $capacity }}
                                    </div>
                                </div>
                            </div>

                            <h3 class="modal-info-title">
                                Fasilitas Auditorium
                            </h3>

                            @if(count($facilities) > 0)
                                <div class="modal-facilities">
                                    @foreach($facilities as $facility)
                                        <span class="facility-tag">
                                            {{ $facility }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="auditorium-description">
                                    Informasi fasilitas belum tersedia.
                                </p>
                            @endif

                            <div class="modal-actions">
                                @if($isActive)
                                    <a
                                        href="{{ route('borrower.booking.create', ['auditorium' => $a->id]) }}"
                                    >
                                        <i data-lucide="calendar-plus"></i>
                                        Ajukan Peminjaman
                                    </a>
                                @else
                                    <button
                                        type="button"
                                        class="modal-btn-disabled"
                                        disabled
                                        aria-disabled="true"
                                        title="Auditorium sedang dinonaktifkan"
                                    >
                                        <i data-lucide="ban"></i>
                                        Ajukan Peminjaman
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="empty-auditorium">
                    Data auditorium belum tersedia.
                </div>
            @endforelse
        </div>
        </div>
    </div>
</div>

{{-- CHART.JS CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

{{-- JAVASCRIPT DETAIL, CHART, TYPEWRITER & SLIDER AUDITORIUM --}}
<script>
    const AUD_SLIDE_INTERVAL = 4000;

    function pruneExpiredBorrowerUpcoming() {
        const listEl = document.getElementById('borrowerUpcomingList');
        if (!listEl) return;

        const nowMs = Date.now();
        const cards = listEl.querySelectorAll('.upcoming-card[data-end-ts]');
        let removed = false;

        cards.forEach(function (card) {
            const endTs = Number(card.dataset.endTs || 0);
            if (endTs > 0 && nowMs >= endTs) {
                card.remove();
                removed = true;
            }
        });

        if (removed) {
            const remaining = listEl.querySelectorAll('.upcoming-card[data-end-ts]').length;
            const countEl = document.getElementById('borrowerUpcomingCount');
            if (countEl) {
                countEl.textContent = '(' + remaining + ')';
            }

            if (remaining === 0) {
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'dash-empty-state';
                emptyDiv.textContent = 'Belum ada jadwal peminjaman auditorium mendatang.';
                listEl.replaceWith(emptyDiv);
            }
        }
    }

    function initBorrowerChart() {
        const canvas = document.getElementById('sipaduBorrowerChart');
        if (!canvas || typeof Chart === 'undefined') return;

        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const yearlyData = JSON.parse(canvas.dataset.yearly || '{}');
        const availableYears = JSON.parse(canvas.dataset.years || '[]').map(Number);
        let currentYear = Number(canvas.dataset.defaultYear || availableYears[availableYears.length - 1] || new Date().getFullYear());

        const yearDropdown = document.getElementById('chartYearDropdown');
        const yearTrigger = document.getElementById('chartYearTrigger');
        const yearLabel = document.getElementById('chartYearLabel');
        const yearOptions = document.querySelectorAll('#chartYearMenu .chart-year-option');
        const prevBtn = document.getElementById('chartPrevYearBtn');
        const nextBtn = document.getElementById('chartNextYearBtn');
        const detailBox = document.getElementById('chartClickDetail');
        const detailMonth = document.getElementById('chartDetailMonth');
        const detailApproved = document.getElementById('chartDetailApproved');
        const detailRejected = document.getElementById('chartDetailRejected');
        const detailClose = document.getElementById('chartDetailClose');

        function getYearSeries(year) {
            const entry = yearlyData[String(year)] || {};
            return {
                approved: Array.isArray(entry.approved) ? entry.approved : new Array(12).fill(0),
                rejected: Array.isArray(entry.rejected) ? entry.rejected : new Array(12).fill(0)
            };
        }

        function closeYearDropdown() {
            if (!yearDropdown || !yearTrigger) return;
            yearDropdown.classList.remove('is-open');
            yearTrigger.setAttribute('aria-expanded', 'false');
        }

        function syncYearControls() {
            const idx = availableYears.indexOf(currentYear);
            if (yearLabel) yearLabel.textContent = String(currentYear);
            yearOptions.forEach(function (opt) {
                const isSelected = Number(opt.dataset.year) === currentYear;
                opt.classList.toggle('is-active', isSelected);
                opt.setAttribute('aria-selected', isSelected ? 'true' : 'false');
            });
            if (prevBtn) prevBtn.disabled = idx <= 0;
            if (nextBtn) nextBtn.disabled = idx === -1 || idx >= availableYears.length - 1;
        }

        function showMonthDetail(monthIndex) {
            if (!detailBox || monthIndex < 0 || monthIndex >= labels.length) return;
            const series = getYearSeries(currentYear);
            detailMonth.textContent = labels[monthIndex] + ' ' + currentYear;
            detailApproved.textContent = 'Disetujui: ' + (series.approved[monthIndex] ?? 0);
            detailRejected.textContent = 'Ditolak: ' + (series.rejected[monthIndex] ?? 0);
            detailBox.classList.remove('is-hidden');
        }

        const initialSeries = getYearSeries(currentYear);

        const borrowerChart = new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Disetujui',
                        data: initialSeries.approved,
                        backgroundColor: 'rgba(35, 124, 77, 0.85)',
                        hoverBackgroundColor: '#1a633c',
                        borderRadius: function (ctx) {
                            const rejectedVal = Number(ctx.chart.data.datasets[1]?.data?.[ctx.dataIndex] || 0);
                            const topRadius = rejectedVal === 0 ? 6 : 0;
                            return { topLeft: topRadius, topRight: topRadius, bottomLeft: 6, bottomRight: 6 };
                        },
                        borderSkipped: false,
                        barPercentage: 0.62,
                        categoryPercentage: 0.78
                    },
                    {
                        label: 'Ditolak',
                        data: initialSeries.rejected,
                        backgroundColor: 'rgba(220, 69, 69, 0.82)',
                        hoverBackgroundColor: '#b91c1c',
                        borderRadius: function (ctx) {
                            const approvedVal = Number(ctx.chart.data.datasets[0]?.data?.[ctx.dataIndex] || 0);
                            const bottomRadius = approvedVal === 0 ? 6 : 0;
                            return { topLeft: 6, topRight: 6, bottomLeft: bottomRadius, bottomRight: bottomRadius };
                        },
                        borderSkipped: false,
                        barPercentage: 0.62,
                        categoryPercentage: 0.78
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                onClick: function (event, elements, chart) {
                    let monthIndex = -1;
                    if (elements && elements.length > 0) {
                        monthIndex = elements[0].index;
                    } else {
                        const nearest = chart.getElementsAtEventForMode(event, 'index', { intersect: false }, true);
                        if (nearest && nearest.length > 0) {
                            monthIndex = nearest[0].index;
                        }
                    }

                    if (monthIndex !== -1) {
                        showMonthDetail(monthIndex);
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#163626',
                        titleColor: '#ffffff',
                        bodyColor: '#eef7f1',
                        padding: 10,
                        cornerRadius: 9,
                        usePointStyle: true,
                        boxWidth: 8,
                        boxHeight: 8,
                        callbacks: {
                            title: function (items) {
                                return (items[0]?.label || '') + ' ' + currentYear;
                            },
                            label: function (ctx) {
                                return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' peminjaman';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            color: '#4c6456',
                            font: { size: 11, weight: '600' }
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        border: { display: false },
                        grid: {
                            color: 'rgba(22, 54, 38, 0.08)',
                            drawTicks: false
                        },
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: '#4c6456',
                            padding: 8,
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });

        function changeChartYear(nextYear) {
            if (!availableYears.includes(nextYear)) return;
            currentYear = nextYear;
            const series = getYearSeries(currentYear);
            borrowerChart.data.datasets[0].data = series.approved;
            borrowerChart.data.datasets[1].data = series.rejected;
            borrowerChart.update();
            syncYearControls();
            closeYearDropdown();

            if (detailBox && !detailBox.classList.contains('is-hidden')) {
                detailBox.classList.add('is-hidden');
            }
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                const idx = availableYears.indexOf(currentYear);
                if (idx > 0) changeChartYear(availableYears[idx - 1]);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                const idx = availableYears.indexOf(currentYear);
                if (idx !== -1 && idx < availableYears.length - 1) {
                    changeChartYear(availableYears[idx + 1]);
                }
            });
        }

        if (yearTrigger && yearDropdown) {
            yearTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = yearDropdown.classList.toggle('is-open');
                yearTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (!yearDropdown.contains(e.target)) {
                    closeYearDropdown();
                }
            });
        }

        yearOptions.forEach(function (opt) {
            opt.addEventListener('click', function (e) {
                e.stopPropagation();
                changeChartYear(Number(this.dataset.year));
            });
        });

        if (detailClose && detailBox) {
            detailClose.addEventListener('click', function () {
                detailBox.classList.add('is-hidden');
            });
        }

        syncYearControls();
    }

    function initWelcomeTypewriter() {
        const heading = document.querySelector('.dashboard-welcome h1[data-typewriter]');
        if (!heading) return;

        const fullText = heading.getAttribute('data-typewriter') || '';
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (prefersReducedMotion || !fullText) {
            heading.textContent = fullText;
            return;
        }

        const initialHeight = heading.offsetHeight;
        if (initialHeight > 0) {
            heading.style.minHeight = initialHeight + 'px';
        }

        heading.textContent = '';
        heading.classList.add('is-typing');

        let index = 0;
        const typingSpeed = 42;

        const timer = setInterval(() => {
            index++;
            heading.textContent = fullText.slice(0, index);

            if (index >= fullText.length) {
                clearInterval(timer);
                heading.style.minHeight = '';

                setTimeout(() => {
                    heading.classList.remove('is-typing');
                }, 1200);
            }
        }, typingSpeed);
    }

    function scrollAuditoriums(button, direction) {
        const track = document.getElementById('auditoriumCards');
        if (!track) return;
        const card = track.querySelector('.auditorium-card');
        const distance = card ? card.getBoundingClientRect().width + 16 : track.clientWidth * 0.8;
        track.scrollBy({ left: distance * direction, behavior: 'smooth' });
    }

    function setAudSliderIndex(slider, nextIndex) {
        const total = parseInt(slider.dataset.total || '1', 10);
        if (total <= 1) return;

        const normalized = ((nextIndex % total) + total) % total;
        slider.dataset.index = String(normalized);

        const track = slider.querySelector('.aud-slider-track');
        if (track) {
            track.style.transform = 'translateX(-' + (normalized * 100) + '%)';
        }

        const counter = slider.querySelector('.aud-slider-count');
        if (counter) {
            counter.textContent = (normalized + 1) + ' / ' + total;
        }

        slider.querySelectorAll('.aud-slider-dot').forEach((dot, idx) => {
            dot.classList.toggle('is-active', idx === normalized);
        });
    }

    function startAudSliderAutoplay(slider) {
        const total = parseInt(slider.dataset.total || '1', 10);
        if (total <= 1) return;

        stopAudSliderAutoplay(slider);
        slider._autoTimer = setInterval(() => {
            if (document.visibilityState === 'visible') {
                const current = parseInt(slider.dataset.index || '0', 10);
                setAudSliderIndex(slider, current + 1);
            }
        }, AUD_SLIDE_INTERVAL);
    }

    function stopAudSliderAutoplay(slider) {
        if (slider._autoTimer) {
            clearInterval(slider._autoTimer);
            slider._autoTimer = null;
        }
    }

    function resetAudSliderAutoplay(slider) {
        stopAudSliderAutoplay(slider);
        if (!slider.matches(':hover')) {
            startAudSliderAutoplay(slider);
        }
    }

    function stepAudSlider(btn, delta) {
        const slider = btn.closest('[data-aud-slider]');
        if (!slider) return;
        const current = parseInt(slider.dataset.index || '0', 10);
        setAudSliderIndex(slider, current + delta);
        resetAudSliderAutoplay(slider);
    }

    function goToAudSlide(dotBtn) {
        const slider = dotBtn.closest('[data-aud-slider]');
        if (!slider) return;
        const index = parseInt(dotBtn.dataset.slideIndex || '0', 10);
        setAudSliderIndex(slider, index);
        resetAudSliderAutoplay(slider);
    }

    function openAuditoriumModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.add('show');

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeAuditoriumModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function () {
        /* HAPUS OTOMATIS PEMINJAMAN MENDATANG YANG JAMNYA SUDAH BERAKHIR */
        pruneExpiredBorrowerUpcoming();
        setInterval(pruneExpiredBorrowerUpcoming, 15000);

        initBorrowerChart();
        initWelcomeTypewriter();

        if (window.lucide) {
            window.lucide.createIcons();
        }

        document.querySelectorAll('.auditorium-modal').forEach(modal => {
            document.body.appendChild(modal);

            modal.addEventListener('wheel', function (e) {
                const box = modal.querySelector('.auditorium-modal-box');
                if (!box || !box.contains(e.target)) {
                    e.preventDefault();
                    return;
                }
                const atTop = box.scrollTop <= 0 && e.deltaY < 0;
                const atBottom = Math.ceil(box.scrollTop + box.clientHeight) >= box.scrollHeight && e.deltaY > 0;
                if (atTop || atBottom) {
                    e.preventDefault();
                }
            }, { passive: false });
        });

        document.querySelectorAll('[data-aud-slider]').forEach(slider => {
            let startX = 0;

            slider.addEventListener('mouseenter', () => stopAudSliderAutoplay(slider));
            slider.addEventListener('mouseleave', () => startAudSliderAutoplay(slider));

            slider.addEventListener('touchstart', e => {
                startX = e.touches[0].clientX;
                stopAudSliderAutoplay(slider);
            }, { passive: true });

            slider.addEventListener('touchend', e => {
                const diff = e.changedTouches[0].clientX - startX;
                if (Math.abs(diff) > 35) {
                    e.stopPropagation();
                    const current = parseInt(slider.dataset.index || '0', 10);
                    setAudSliderIndex(slider, current + (diff < 0 ? 1 : -1));
                }
                startAudSliderAutoplay(slider);
            }, { passive: true });

            startAudSliderAutoplay(slider);
        });
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.auditorium-modal.show')
                .forEach(function(modal) {
                    modal.classList.remove('show');
                });
            const yearDropdown = document.getElementById('chartYearDropdown');
            const yearTrigger = document.getElementById('chartYearTrigger');
            if (yearDropdown && yearTrigger) {
                yearDropdown.classList.remove('is-open');
                yearTrigger.setAttribute('aria-expanded', 'false');
            }
        }
    });
</script>

@endsection