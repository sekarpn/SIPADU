@extends('layouts.panel')

@section('panel')

@php
    $total = $stats['total'] ?? 0;
    $cancelled = $stats['cancelled'] ?? 0;

    $pendingPercent = $total > 0 ? (($stats['pending'] ?? 0) / $total) * 100 : 0;
    $approvedPercent = $total > 0 ? (($stats['approved'] ?? 0) / $total) * 100 : 0;
    $rejectedPercent = $total > 0 ? (($stats['rejected'] ?? 0) / $total) * 100 : 0;
    $cancelledPercent = $total > 0 ? ($cancelled / $total) * 100 : 0;

    $pendingBookings = \App\Models\Booking::with(['user', 'auditorium'])
        ->whereRaw('LOWER(TRIM(status)) IN (?, ?)', ['pending', 'menunggu'])
        ->latest()
        ->get();

    // Ambil SEMUA pengajuan berstatus Disetujui, diurutkan dari tanggal terdekat
    $upcomingBookings = \App\Models\Booking::with(['user', 'auditorium'])
        ->whereRaw('LOWER(TRIM(status)) IN (?, ?)', ['disetujui', 'approved'])
        ->orderBy('booking_date', 'asc')
        ->orderBy('start_time', 'asc')
        ->get();
@endphp

<style>
    .dashboard-page {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 0 35px;
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
        font-size: 26px;
        font-weight: 800;
        color: #163626;
        margin: 0 0 5px;
        letter-spacing: -0.03em;
    }

    .dashboard-welcome p {
        font-size: 13px;
        color: #4c6456;
        margin: 0;
    }

    .dashboard-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 11px;
        background: #237c4d;
        color: #ffffff;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(35, 124, 77, 0.25);
        transition: background .2s ease, transform .2s ease;
        white-space: nowrap;
    }

    .dashboard-action:hover {
        background: #1a633c;
        color: #ffffff;
        transform: translateY(-2px);
    }

    .dashboard-action svg {
        width: 16px;
        height: 16px;
    }

    /* STATISTIK (GLASSMORPHISM) */
    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.68) 0%, rgba(255, 255, 255, 0.42) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.55) !important;
        border-radius: 16px;
        padding: 17px 18px;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.06) !important;
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
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
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
        font-weight: 800;
        color: #163626;
        line-height: 1.2;
    }

    .stat-card p {
        margin: 6px 0 0;
        color: #4c6456;
        font-size: 11px;
    }

    /* PANEL GLASS DIAGRAM PIE */
    .dashboard-panel {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.68) 0%, rgba(255, 255, 255, 0.42) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.55) !important;
        border-radius: 16px;
        padding: 16px 20px 20px !important;
        margin-bottom: 20px;
        min-width: 0;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.06) !important;
        box-sizing: border-box;
        width: 100%;
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 0 0 14px !important;
        padding: 0 !important;
    }

    .section-head h2 {
        font-size: 15.5px;
        font-weight: 800;
        color: #163626;
        margin: 0 0 3px !important;
        padding: 0 !important;
        letter-spacing: -0.02em;
    }

    .section-head p {
        font-size: 11.5px;
        color: #4c6456;
        margin: 0;
    }

    /* KARTU DIAGRAM PIE */
    .chart-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 65px;
        padding: 10px 0 6px;
        flex-wrap: wrap;
    }

    .pie-chart {
        width: 240px;
        height: 240px;
        position: relative;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.08);
        transition: transform .3s ease;
    }

    .pie-chart:hover {
        transform: scale(1.04);
    }

    .pie-center {
        position: absolute;
        width: 140px;
        height: 140px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(22, 54, 38, 0.08);
        pointer-events: none;
    }

    .pie-center strong {
        font-size: 30px;
        font-weight: 800;
        color: #163626;
        line-height: 1.2;
    }

    .pie-center span {
        font-size: 11.5px;
        font-weight: 600;
        color: #4c6456;
    }

    .pie-tooltip {
        position: absolute;
        z-index: 5;
        top: -45px;
        left: 50%;
        transform: translateX(-50%);
        background: #163626;
        color: #fff;
        padding: 8px 14px;
        border-radius: 9px;
        font-size: 11.5px;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        box-shadow: 0 6px 18px rgba(22, 54, 38, 0.2);
        transition: .2s ease;
        pointer-events: none;
    }

    .pie-tooltip.show {
        opacity: 1;
        visibility: visible;
    }

    .chart-legend {
        min-width: 240px;
        max-width: 310px;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 11px 14px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.48);
        border: 1px solid rgba(255, 255, 255, 0.45);
        transition: background .2s ease, transform .2s ease;
        cursor: pointer;
    }

    .legend-item:hover,
    .legend-item.active {
        background: rgba(255, 255, 255, 0.78);
        transform: translateX(4px);
    }

    .legend-label {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #324a3c;
        font-size: 13px;
        font-weight: 600;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .legend-item strong {
        color: #163626;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
    }

    .legend-color.pending {
        background: #f59e0b;
    }

    .legend-color.approved {
        background: #16a34a;
    }

    .legend-color.rejected {
        background: #ef4444;
    }

    .legend-color.cancelled {
        background: #94a3b8;
    }

    .chart-empty {
        color: #4c6456;
        text-align: center;
        font-size: 13px;
        padding: 25px;
    }

    /* ============================================================
       GRID 2 KOLOM: PENGAJUAN TERBARU & PEMINJAMAN MENDATANG (UPGRADED UI)
       ============================================================ */
    .dash-cards-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: start;
    }

    .dash-feed-panel {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.74) 0%, rgba(255, 255, 255, 0.48) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.65) !important;
        border-radius: 18px !important;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.06) !important;
        padding: 18px 20px 20px;
        min-width: 0;
    }

    .dash-feed-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(22, 54, 38, 0.08);
    }

    .dash-feed-title-wrap {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
    }

    .dash-feed-title-wrap h2 {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        color: #163626;
        letter-spacing: -0.02em;
    }

    .dash-feed-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
        height: 24px;
        padding: 0 8px;
        border-radius: 999px;
        background: rgba(35, 124, 77, 0.14);
        color: #165e38;
        font-size: 11.5px;
        font-weight: 800;
    }

    .dash-feed-count.is-amber {
        background: rgba(217, 119, 6, 0.15);
        color: #92400e;
    }

    .dash-feed-more {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 12px;
        border-radius: 10px;
        background: rgba(35, 124, 77, 0.11);
        color: #1f7a49;
        text-decoration: none;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        transition: background .2s ease, gap .2s ease, color .2s ease;
    }

    .dash-feed-more:hover {
        background: #237c4d;
        color: #ffffff;
        gap: 7px;
    }

    .dash-feed-more svg {
        width: 14px;
        height: 14px;
    }

    /* AREA SCROLL DAFTAR CARD */
    .dash-feed-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
        max-height: 430px;
        overflow-y: auto;
        padding: 3px 4px 4px 1px;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(72, 131, 91, 0.42) rgba(22, 54, 38, 0.05);
    }

    .dash-feed-list::-webkit-scrollbar {
        width: 5px;
    }

    .dash-feed-list::-webkit-scrollbar-track {
        background: rgba(22, 54, 38, 0.05);
        border-radius: 999px;
    }

    .dash-feed-list::-webkit-scrollbar-thumb {
        background: rgba(72, 131, 91, 0.42);
        border-radius: 999px;
    }

    .dash-feed-list::-webkit-scrollbar-thumb:hover {
        background: rgba(72, 131, 91, 0.68);
    }

    /* KARTU ITEM DENGAN KALENDER MINI + AKSEN GARIS KIRI */
    .dash-booking-card {
        position: relative;
        display: grid;
        grid-template-columns: 62px minmax(0, 1fr);
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.78);
        border: 1px solid rgba(22, 54, 38, 0.12);
        border-radius: 14px;
        padding: 13px 15px 13px 17px;
        color: #163626;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(22, 54, 38, 0.03);
        transition: background .2s ease, border-color .2s ease, transform .2s ease, box-shadow .2s ease;
    }

    .dash-booking-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #237c4d;
    }

    .dash-booking-card.accent-pending::before {
        background: #f59e0b;
    }

    .dash-booking-card.accent-upcoming::before {
        background: #eab308;
    }

    .dash-booking-card.accent-live::before {
        background: #16a34a;
    }

    .dash-booking-card:hover {
        background: #ffffff;
        border-color: #2f7d58;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(22, 54, 38, 0.08);
    }

    /* BLOK TANGGAL KALENDER MINI */
    .dash-date-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 4px;
        border-radius: 11px;
        background: rgba(35, 124, 77, 0.09);
        border: 1px solid rgba(35, 124, 77, 0.16);
        text-align: center;
    }

    .dash-booking-card.accent-pending .dash-date-tile {
        background: rgba(245, 158, 11, 0.10);
        border-color: rgba(245, 158, 11, 0.22);
    }

    .dash-date-day {
        font-size: 19px;
        font-weight: 800;
        line-height: 1.05;
        color: #163626;
        letter-spacing: -0.03em;
    }

    .dash-date-month {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #1f7a49;
        margin-top: 2px;
    }

    .dash-booking-card.accent-pending .dash-date-month {
        color: #b45309;
    }

    .dash-date-year {
        font-size: 9.5px;
        font-weight: 600;
        color: #64786c;
        margin-top: 1px;
    }

    /* KONTEN KANAN KARTU */
    .dash-card-body {
        min-width: 0;
    }

    .dash-booking-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .dash-booking-title-group {
        min-width: 0;
    }

    .dash-booking-title {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #163626;
        line-height: 1.32;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dash-booking-sub {
        margin: 2px 0 0;
        font-size: 12px;
        font-weight: 600;
        color: #4c6456;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* BADGE STATUS DI POJOK KANAN */
    .dash-card-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
        flex-shrink: 0;
        line-height: 1.2;
    }

    .dash-card-badge::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    .dash-card-badge.live {
        background: #dcfce7;
        color: #166534;
        border: 1px solid rgba(22, 101, 52, 0.18);
    }

    .dash-card-badge.upcoming {
        background: #fef08a;
        color: #713f12;
        border: 1px solid rgba(161, 98, 7, 0.22);
    }

    .dash-card-badge.pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid rgba(180, 83, 9, 0.2);
    }

    /* FOOTER KARTU: CHIP PENGUSUL & JAM + PANAH */
    .dash-booking-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .dash-booking-chips {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .dash-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border-radius: 7px;
        background: #f2f6f3;
        border: 1px solid rgba(22, 54, 38, 0.08);
        font-size: 11px;
        font-weight: 600;
        color: #2c3e33;
        max-width: 100%;
    }

    .dash-chip.is-org {
        background: rgba(35, 124, 77, 0.08);
        border-color: rgba(35, 124, 77, 0.14);
        color: #163626;
        font-weight: 700;
    }

    .dash-chip svg {
        width: 12.5px;
        height: 12.5px;
        color: #1f7a49;
        flex-shrink: 0;
    }

    .dash-chip span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dash-card-arrow {
        width: 24px;
        height: 24px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64786c;
        flex-shrink: 0;
        transition: background .18s ease, color .18s ease, transform .18s ease;
    }

    .dash-card-arrow svg {
        width: 14px;
        height: 14px;
    }

    .dash-booking-card:hover .dash-card-arrow {
        background: rgba(35, 124, 77, 0.12);
        color: #1f7a49;
        transform: translateX(2px);
    }

    .dash-empty-state {
        text-align: center;
        color: #4c6456;
        padding: 32px 14px;
        font-size: 12.5px;
        background: rgba(255, 255, 255, 0.5);
        border: 1px dashed rgba(22, 54, 38, 0.18);
        border-radius: 13px;
    }

    /* RESPONSIVE */
    @media (max-width: 1180px) {
        .dash-cards-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 900px) {
        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .chart-content {
            gap: 35px;
        }
    }

    @media (max-width: 600px) {
        .dashboard-topbar {
            align-items: flex-start;
        }

        .dashboard-welcome h1 {
            font-size: 22px;
        }

        .dashboard-topbar .dashboard-action {
            width: 100%;
        }

        .dashboard-panel,
        .dash-feed-panel {
            padding: 15px 16px 16px !important;
        }

        .chart-content {
            gap: 28px;
        }

        .pie-chart {
            width: 205px;
            height: 205px;
        }

        .pie-center {
            width: 120px;
            height: 120px;
        }

        .chart-legend {
            max-width: 100%;
        }
    }

    @media (max-width: 480px) {
        .stats {
            gap: 10px;
        }

        .stat-card {
            padding: 13px;
        }

        .stat-card strong {
            font-size: 22px;
        }

        .dash-booking-card {
            grid-template-columns: 54px minmax(0, 1fr);
            gap: 11px;
            padding: 12px 12px 12px 15px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .pie-chart,
        .legend-item,
        .pie-tooltip,
        .dash-booking-card {
            transition: none;
        }
    }
</style>

<div class="dashboard-page">

    {{-- JUDUL DASHBOARD --}}
    <div class="dashboard-topbar">
        <div class="dashboard-welcome">
            <h1>Dashboard Admin</h1>
            <p>Ringkasan aktivitas peminjaman auditorium pada SIPADU.</p>
        </div>

        <a class="dashboard-action" href="{{ route('admin.bookings') }}">
            <i data-lucide="clipboard-list"></i>
            Kelola Pengajuan
        </a>
    </div>

    {{-- STATISTIK --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-top">
                <small>Total Pengajuan</small>
                <span class="stat-icon blue">
                    <i data-lucide="clipboard-list"></i>
                </span>
            </div>
            <strong>{{ $stats['total'] }}</strong>
            <p>Seluruh pengajuan masuk</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Pending</small>
                <span class="stat-icon amber">
                    <i data-lucide="clock"></i>
                </span>
            </div>
            <strong>{{ $stats['pending'] }}</strong>
            <p>Menunggu verifikasi Admin</p>
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
            <p>Pengajuan ditolak Admin</p>
        </div>
    </div>

    {{-- DIAGRAM PIE --}}
    <div class="dashboard-panel">
        <div class="section-head">
            <div>
                <h2>Statistik Pengajuan</h2>
                <p>Arahkan kursor ke diagram atau kategori untuk melihat detail.</p>
            </div>
        </div>

        @if($total > 0)
            <div class="chart-content">
                <div class="pie-chart"
                     id="bookingPie"
                     data-total="{{ $total }}">

                    <div class="pie-tooltip" id="pieTooltip"></div>

                    <div class="pie-center">
                        <strong id="pieTotal">{{ $total }}</strong>
                        <span id="pieLabel">Total Pengajuan</span>
                    </div>
                </div>

                <div class="chart-legend">
                    <div class="legend-item"
                         data-name="Pending"
                         data-value="{{ $stats['pending'] }}"
                         data-percent="{{ number_format($pendingPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color pending"></span>
                            Pending
                        </div>

                        <strong>{{ $stats['pending'] }} pengajuan</strong>
                    </div>

                    <div class="legend-item"
                         data-name="Disetujui"
                         data-value="{{ $stats['approved'] }}"
                         data-percent="{{ number_format($approvedPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color approved"></span>
                            Disetujui
                        </div>

                        <strong>{{ $stats['approved'] }} pengajuan</strong>
                    </div>

                    <div class="legend-item"
                         data-name="Ditolak"
                         data-value="{{ $stats['rejected'] }}"
                         data-percent="{{ number_format($rejectedPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color rejected"></span>
                            Ditolak
                        </div>

                        <strong>{{ $stats['rejected'] }} pengajuan</strong>
                    </div>

                    <div class="legend-item"
                         data-name="Dibatalkan"
                         data-value="{{ $cancelled }}"
                         data-percent="{{ number_format($cancelledPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color cancelled"></span>
                            Dibatalkan
                        </div>

                        <strong>{{ $cancelled }} pengajuan</strong>
                    </div>
                </div>
            </div>
        @else
            <div class="chart-empty">
                Belum ada data pengajuan untuk ditampilkan.
            </div>
        @endif
    </div>

    {{-- 2 PANEL KARTU BERDAMPINGAN: PENGAJUAN TERBARU & PEMINJAMAN MENDATANG --}}
    <div class="dash-cards-grid">

        {{-- KARTU 1: PENGAJUAN TERBARU (PENDING) --}}
        <div class="dash-feed-panel">
            <div class="dash-feed-head">
                <div class="dash-feed-title-wrap">
                    <h2>Pengajuan Terbaru</h2>
                    <span class="dash-feed-count is-amber">{{ $pendingBookings->count() }}</span>
                </div>

                <a href="{{ route('admin.bookings', ['status' => 'Pending']) }}" class="dash-feed-more">
                    Lihat semua
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            @if($pendingBookings->isNotEmpty())
                <div class="dash-feed-list">
                    @foreach($pendingBookings as $b)
                        @php
                            $bParsed = $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->locale('id') : null;
                            $bDay = $bParsed ? $bParsed->format('d') : '--';
                            $bMonth = $bParsed ? $bParsed->translatedFormat('M') : '-';
                            $bYear = $bParsed ? $bParsed->format('Y') : '';

                            $bTime = !empty($b->start_time)
                                ? str_replace(':', '.', substr($b->start_time, 0, 5))
                                  . (!empty($b->end_time) ? ' – ' . str_replace(':', '.', substr($b->end_time, 0, 5)) . ' WIB' : ' WIB')
                                : 'Waktu menyesuaikan';
                            $bOrg = $b->organization ?? $b->organizer ?? optional($b->user)->name ?? 'Pemohon';
                        @endphp
                        <a href="{{ route('admin.bookings', ['q' => $b->event_name, 'open' => $b->id]) }}" class="dash-booking-card accent-pending">
                            <div class="dash-date-tile">
                                <span class="dash-date-day">{{ $bDay }}</span>
                                <span class="dash-date-month">{{ $bMonth }}</span>
                                <span class="dash-date-year">{{ $bYear }}</span>
                            </div>

                            <div class="dash-card-body">
                                <div class="dash-booking-card-top">
                                    <div class="dash-booking-title-group">
                                        <h3 class="dash-booking-title">{{ optional($b->auditorium)->name ?? $b->event_name }}</h3>
                                        @if(!empty(optional($b->auditorium)->name) && !empty($b->event_name))
                                            <p class="dash-booking-sub">{{ $b->event_name }}</p>
                                        @endif
                                    </div>
                                    <span class="dash-card-badge pending">Pending</span>
                                </div>

                                <div class="dash-booking-footer">
                                    <div class="dash-booking-chips">
                                        <span class="dash-chip is-org">
                                            <i data-lucide="user"></i>
                                            <span>{{ $bOrg }}</span>
                                        </span>
                                        <span class="dash-chip">
                                            <i data-lucide="clock"></i>
                                            <span>{{ $bTime }}</span>
                                        </span>
                                    </div>
                                    <span class="dash-card-arrow">
                                        <i data-lucide="chevron-right"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty-state">
                    Tidak ada pengajuan pending saat ini.
                </div>
            @endif
        </div>

        {{-- KARTU 2: PEMINJAMAN MENDATANG --}}
        <div class="dash-feed-panel">
            <div class="dash-feed-head">
                <div class="dash-feed-title-wrap">
                    <h2>Peminjaman Mendatang</h2>
                    <span class="dash-feed-count">{{ $upcomingBookings->count() }}</span>
                </div>

                <a href="{{ route('admin.bookings', ['status' => 'Disetujui']) }}" class="dash-feed-more">
                    Lihat semua
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            @if($upcomingBookings->isNotEmpty())
                <div class="dash-feed-list">
                    @foreach($upcomingBookings as $u)
                        @php
                            $now = \Carbon\Carbon::now('Asia/Jakarta');
                            $today = $now->copy()->startOfDay();
                            $eventDay = $u->booking_date
                                ? \Carbon\Carbon::parse($u->booking_date, 'Asia/Jakarta')->startOfDay()
                                : null;
                            $diffDays = $eventDay ? (int) $today->diffInDays($eventDay, false) : null;

                            if ($diffDays !== null && $diffDays > 0) {
                                $badgeText = 'Dalam ' . $diffDays . ' hari';
                                $badgeClass = 'upcoming';
                                $cardAccent = 'accent-upcoming';
                            } else {
                                $badgeText = 'Berlangsung';
                                $badgeClass = 'live';
                                $cardAccent = 'accent-live';
                            }

                            $uParsed = $u->booking_date ? \Carbon\Carbon::parse($u->booking_date)->locale('id') : null;
                            $uDay = $uParsed ? $uParsed->format('d') : '--';
                            $uMonth = $uParsed ? $uParsed->translatedFormat('M') : '-';
                            $uYear = $uParsed ? $uParsed->format('Y') : '';

                            $uTime = !empty($u->start_time)
                                ? str_replace(':', '.', substr($u->start_time, 0, 5))
                                  . (!empty($u->end_time) ? ' – ' . str_replace(':', '.', substr($u->end_time, 0, 5)) . ' WIB' : ' WIB')
                                : 'Waktu menyesuaikan';
                            $uOrg = $u->organization ?? $u->organizer ?? optional($u->user)->name ?? 'Pemohon';
                        @endphp
                        <a href="{{ route('admin.bookings', ['q' => $u->event_name, 'open' => $u->id]) }}" class="dash-booking-card {{ $cardAccent }}">
                            <div class="dash-date-tile">
                                <span class="dash-date-day">{{ $uDay }}</span>
                                <span class="dash-date-month">{{ $uMonth }}</span>
                                <span class="dash-date-year">{{ $uYear }}</span>
                            </div>

                            <div class="dash-card-body">
                                <div class="dash-booking-card-top">
                                    <div class="dash-booking-title-group">
                                        <h3 class="dash-booking-title">{{ optional($u->auditorium)->name ?? $u->event_name }}</h3>
                                        @if(!empty(optional($u->auditorium)->name) && !empty($u->event_name))
                                            <p class="dash-booking-sub">{{ $u->event_name }}</p>
                                        @endif
                                    </div>
                                    <span class="dash-card-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                </div>

                                <div class="dash-booking-footer">
                                    <div class="dash-booking-chips">
                                        <span class="dash-chip is-org">
                                            <i data-lucide="user"></i>
                                            <span>{{ $uOrg }}</span>
                                        </span>
                                        <span class="dash-chip">
                                            <i data-lucide="clock"></i>
                                            <span>{{ $uTime }}</span>
                                        </span>
                                    </div>
                                    <span class="dash-card-arrow">
                                        <i data-lucide="chevron-right"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="dash-empty-state">
                    Belum ada jadwal peminjaman mendatang.
                </div>
            @endif
        </div>

    </div>

</div>

<script>
    if (window.lucide) {
        lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', function () {
        /* DIAGRAM PIE */
        const pie = document.getElementById('bookingPie');
        const tooltip = document.getElementById('pieTooltip');
        const pieTotal = document.getElementById('pieTotal');
        const pieLabel = document.getElementById('pieLabel');
        const legendItems = document.querySelectorAll('.legend-item');

        if (!pie || !tooltip) return;

        const total = pie.dataset.total;

        const colors = [
            '#f59e0b',
            '#16a34a',
            '#ef4444',
            '#94a3b8'
        ];

        let accumulated = 0;
        const gradientParts = [];

        legendItems.forEach(function (item, index) {
            const percent = parseFloat(item.dataset.percent) || 0;
            const start = accumulated;
            accumulated += percent;

            if (percent > 0) {
                gradientParts.push(
                    colors[index] + ' ' + start + '% ' + accumulated + '%'
                );
            }
        });

        pie.style.background = 'conic-gradient(' + gradientParts.join(', ') + ')';

        function showDetail(item) {
            const name = item.dataset.name;
            const value = item.dataset.value;
            const percent = item.dataset.percent;

            tooltip.textContent = name + ': ' + value +
                ' pengajuan (' + percent + '%)';

            tooltip.classList.add('show');

            pieTotal.textContent = value;
            pieLabel.textContent = name;

            legendItems.forEach(function (legend) {
                legend.classList.toggle('active', legend === item);
            });
        }

        function resetDetail() {
            tooltip.classList.remove('show');

            pieTotal.textContent = total;
            pieLabel.textContent = 'Total Pengajuan';

            legendItems.forEach(function (item) {
                item.classList.remove('active');
            });
        }

        function selectPieSection(event) {
            const rect = pie.getBoundingClientRect();

            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const x = event.clientX - centerX;
            const y = event.clientY - centerY;

            const distance = Math.sqrt(x * x + y * y);
            const radius = rect.width / 2;
            const innerRadius = radius * 0.58;

            if (distance < innerRadius) {
                resetDetail();
                return;
            }

            let angle = Math.atan2(y, x) * 180 / Math.PI + 90;

            if (angle < 0) {
                angle += 360;
            }

            const percent = (angle / 360) * 100;
            let accumulated = 0;

            for (const item of legendItems) {
                const value = parseFloat(item.dataset.percent) || 0;

                if (value <= 0) continue;

                accumulated += value;

                if (percent <= accumulated) {
                    showDetail(item);
                    return;
                }
            }

            resetDetail();
        }

        legendItems.forEach(function (item) {
            item.addEventListener('mouseenter', function () {
                showDetail(item);
            });

            item.addEventListener('mouseleave', resetDetail);

            item.addEventListener('focus', function () {
                showDetail(item);
            });

            item.addEventListener('blur', resetDetail);
        });

        pie.addEventListener('mousemove', selectPieSection);
        pie.addEventListener('mouseleave', resetDetail);
        pie.addEventListener('click', selectPieSection);
    });
</script>

@endsection