@extends('layouts.panel')

@section('panel')

@php
    $total = $stats['total'] ?? 0;
    $cancelled = $stats['cancelled'] ?? 0;

    $pendingPercent = $total > 0 ? (($stats['pending'] ?? 0) / $total) * 100 : 0;
    $approvedPercent = $total > 0 ? (($stats['approved'] ?? 0) / $total) * 100 : 0;
    $rejectedPercent = $total > 0 ? (($stats['rejected'] ?? 0) / $total) * 100 : 0;
    $cancelledPercent = $total > 0 ? ($cancelled / $total) * 100 : 0;

    $todayJakarta = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

    $pendingBookings = \App\Models\Booking::with(['user', 'auditorium'])
        ->whereRaw('LOWER(TRIM(status)) IN (?, ?)', ['pending', 'menunggu'])
        ->latest()
        ->get();

    // Ambil pengajuan berstatus Disetujui mulai dari HARI H (>= hari ini) saja
    $upcomingBookings = \App\Models\Booking::with(['user', 'auditorium'])
        ->whereRaw('LOWER(TRIM(status)) IN (?, ?)', ['disetujui', 'approved'])
        ->whereDate('booking_date', '>=', $todayJakarta)
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
        display: block;
        text-decoration: none;
        color: inherit;
        cursor: pointer;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.68) 0%, rgba(255, 255, 255, 0.42) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.55) !important;
        border-radius: 16px;
        padding: 17px 18px;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.06) !important;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease, background .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.88) 0%, rgba(255, 255, 255, 0.68) 100%) !important;
        border-color: rgba(35, 124, 77, 0.35) !important;
        box-shadow: 0 12px 32px rgba(22, 54, 38, 0.1) !important;
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
        cursor: pointer;
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
        text-decoration: none;
        color: inherit;
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
       GRID 2 KOLOM: PENGAJUAN TERBARU & PEMINJAMAN MENDATANG
       ============================================================ */
    .dash-cards-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: start;
    }

    .dash-feed-panel {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.72) 0%, rgba(255, 255, 255, 0.46) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.62) !important;
        border-radius: 16px !important;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.05) !important;
        padding: 18px 20px 20px;
        min-width: 0;
    }

    .dash-feed-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .dash-feed-head-left {
        display: flex;
        align-items: baseline;
        gap: 7px;
        min-width: 0;
    }

    .dash-feed-head h2 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 800;
        color: #163626;
        letter-spacing: -0.02em;
    }

    .dash-feed-count {
        font-size: 12px;
        font-weight: 700;
        color: #64786c;
    }

    .dash-feed-more {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #1f7a49;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        transition: color .15s ease;
    }

    .dash-feed-more:hover {
        color: #145332;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    .dash-feed-more svg {
        width: 14px;
        height: 14px;
    }

    /* LIST SCROLL */
    .dash-feed-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-height: 430px;
        overflow-y: auto;
        padding: 2px 3px 4px 1px;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
    }

    .dash-feed-list::-webkit-scrollbar {
        width: 5px;
    }

    .dash-feed-list::-webkit-scrollbar-thumb {
        background: rgba(22, 54, 38, 0.18);
        border-radius: 999px;
    }

    /* KARTU SIMETRIS & SEIMBANG */
    .proto-card {
        display: block;
        padding: 15px 18px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.92);
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
        margin-bottom: 12px;
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
        margin: 3px 0 0;
        font-size: 12px;
        color: #5c7265;
        display: flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .proto-card-sub .event-name {
        font-weight: 600;
        color: #465d50;
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

    /* SOFT STATUS PILL DI KANAN ATAS */
    .proto-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 13px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        line-height: 1.2;
    }

    .proto-badge.live {
        background: #dff3e6;
        color: #1b653c;
    }

    .proto-badge.upcoming {
        background: #fef3c7;
        color: #854d0e;
    }

    .proto-badge.pending {
        background: #ffedd5;
        color: #9a3412;
    }

    /* BARIS WAKTU BAWAH: TANGGAL & JAM SEJAJAR RAPI */
    .proto-schedule-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 10px;
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
        text-align: center;
        color: #5e7366;
        padding: 34px 16px;
        font-size: 12.5px;
        background: rgba(255, 255, 255, 0.55);
        border: 1px dashed rgba(22, 54, 38, 0.16);
        border-radius: 13px;
    }

    /* RESPONSIVE */
    @media (max-width: 1120px) {
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

        .proto-schedule-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
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
    }

    @media (prefers-reduced-motion: reduce) {
        .stat-card,
        .pie-chart,
        .legend-item,
        .pie-tooltip,
        .proto-card {
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
        <a href="{{ route('admin.bookings') }}" class="stat-card">
            <div class="stat-top">
                <small>Total Pengajuan</small>
                <span class="stat-icon blue">
                    <i data-lucide="clipboard-list"></i>
                </span>
            </div>
            <strong>{{ $stats['total'] }}</strong>
            <p>Seluruh pengajuan masuk</p>
        </a>

        <a href="{{ route('admin.bookings', ['status' => 'Pending']) }}" class="stat-card">
            <div class="stat-top">
                <small>Pending</small>
                <span class="stat-icon amber">
                    <i data-lucide="clock"></i>
                </span>
            </div>
            <strong>{{ $stats['pending'] }}</strong>
            <p>Menunggu verifikasi Admin</p>
        </a>

        <a href="{{ route('admin.bookings', ['status' => 'Disetujui']) }}" class="stat-card">
            <div class="stat-top">
                <small>Disetujui</small>
                <span class="stat-icon green">
                    <i data-lucide="check-circle-2"></i>
                </span>
            </div>
            <strong>{{ $stats['approved'] }}</strong>
            <p>Pengajuan telah disetujui</p>
        </a>

        <a href="{{ route('admin.bookings', ['status' => 'Ditolak']) }}" class="stat-card">
            <div class="stat-top">
                <small>Ditolak</small>
                <span class="stat-icon red">
                    <i data-lucide="x-circle"></i>
                </span>
            </div>
            <strong>{{ $stats['rejected'] }}</strong>
            <p>Pengajuan ditolak Admin</p>
        </a>
    </div>

    {{-- DIAGRAM PIE --}}
    <div class="dashboard-panel">
        <div class="section-head">
            <div>
                <h2>Statistik Pengajuan</h2>
                <p>Arahkan kursor atau klik diagram dan kategori untuk membuka halaman kelola pengajuan.</p>
            </div>
        </div>

        @if($total > 0)
            <div class="chart-content">
                <div class="pie-chart"
                     id="bookingPie"
                     data-total="{{ $total }}"
                     data-base-url="{{ route('admin.bookings') }}">

                    <div class="pie-tooltip" id="pieTooltip"></div>

                    <div class="pie-center">
                        <strong id="pieTotal">{{ $total }}</strong>
                        <span id="pieLabel">Total Pengajuan</span>
                    </div>
                </div>

                <div class="chart-legend">
                    <a href="{{ route('admin.bookings', ['status' => 'Pending']) }}"
                       class="legend-item"
                       data-name="Pending"
                       data-url="{{ route('admin.bookings', ['status' => 'Pending']) }}"
                       data-value="{{ $stats['pending'] }}"
                       data-percent="{{ number_format($pendingPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color pending"></span>
                            Pending
                        </div>

                        <strong>{{ $stats['pending'] }} pengajuan</strong>
                    </a>

                    <a href="{{ route('admin.bookings', ['status' => 'Disetujui']) }}"
                       class="legend-item"
                       data-name="Disetujui"
                       data-url="{{ route('admin.bookings', ['status' => 'Disetujui']) }}"
                       data-value="{{ $stats['approved'] }}"
                       data-percent="{{ number_format($approvedPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color approved"></span>
                            Disetujui
                        </div>

                        <strong>{{ $stats['approved'] }} pengajuan</strong>
                    </a>

                    <a href="{{ route('admin.bookings', ['status' => 'Ditolak']) }}"
                       class="legend-item"
                       data-name="Ditolak"
                       data-url="{{ route('admin.bookings', ['status' => 'Ditolak']) }}"
                       data-value="{{ $stats['rejected'] }}"
                       data-percent="{{ number_format($rejectedPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color rejected"></span>
                            Ditolak
                        </div>

                        <strong>{{ $stats['rejected'] }} pengajuan</strong>
                    </a>

                    <a href="{{ route('admin.bookings', ['status' => 'Dibatalkan']) }}"
                       class="legend-item"
                       data-name="Dibatalkan"
                       data-url="{{ route('admin.bookings', ['status' => 'Dibatalkan']) }}"
                       data-value="{{ $cancelled }}"
                       data-percent="{{ number_format($cancelledPercent, 1, '.', '') }}">

                        <div class="legend-label">
                            <span class="legend-color cancelled"></span>
                            Dibatalkan
                        </div>

                        <strong>{{ $cancelled }} pengajuan</strong>
                    </a>
                </div>
            </div>
        @else
            <div class="chart-empty">
                Belum ada data pengajuan untuk ditampilkan.
            </div>
        @endif
    </div>

    {{-- 2 PANEL CARD: PENGAJUAN TERBARU & PEMINJAMAN MENDATANG --}}
    <div class="dash-cards-grid">

        {{-- PANEL 1: PENGAJUAN TERBARU (PENDING) --}}
        <div class="dash-feed-panel">
            <div class="dash-feed-head">
                <div class="dash-feed-head-left">
                    <h2>Pengajuan Terbaru</h2>
                    <span class="dash-feed-count">({{ $pendingBookings->count() }})</span>
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
                            $bDate = $b->booking_date
                                ? \Carbon\Carbon::parse($b->booking_date)->locale('id')->translatedFormat('d F Y')
                                : '-';
                            $bTime = !empty($b->start_time)
                                ? str_replace(':', '.', substr($b->start_time, 0, 5)) . ' WIB'
                                  . (!empty($b->end_time) ? ' - ' . str_replace(':', '.', substr($b->end_time, 0, 5)) . ' WIB' : '')
                                : 'Waktu menyesuaikan';
                            $bOrg = $b->organization ?? $b->organizer ?? optional($b->user)->name ?? 'Pemohon';
                            $bVenue = optional($b->auditorium)->name ?? $b->event_name;
                            $hasDistinctEvent = !empty(optional($b->auditorium)->name) && !empty($b->event_name);
                        @endphp
                        <a href="{{ route('admin.bookings', ['booking_id' => $b->id, 'open' => $b->id]) }}" class="proto-card">
                            <div class="proto-card-top">
                                <div class="proto-card-heading">
                                    <h3 class="proto-card-title">{{ $bVenue }}</h3>
                                    <p class="proto-card-sub">
                                        @if($hasDistinctEvent)
                                            <span class="event-name">{{ $b->event_name }}</span>
                                            <span class="sub-dot">•</span>
                                        @endif
                                        <span class="org-name">{{ $bOrg }}</span>
                                    </p>
                                </div>
                                <span class="proto-badge pending">Pending</span>
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
                    Tidak ada pengajuan pending saat ini.
                </div>
            @endif
        </div>

        {{-- PANEL 2: PEMINJAMAN MENDATANG (MULAI HARI H SAJA) --}}
        <div class="dash-feed-panel">
            <div class="dash-feed-head">
                <div class="dash-feed-head-left">
                    <h2>Peminjaman Mendatang</h2>
                    <span class="dash-feed-count">({{ $upcomingBookings->count() }})</span>
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
                            $eventDay = \Carbon\Carbon::parse($u->booking_date, 'Asia/Jakarta')->startOfDay();
                            $diffDays = (int) $today->diffInDays($eventDay, false);

                            if ($diffDays > 0) {
                                $badgeText = 'Dalam ' . $diffDays . ' hari';
                                $badgeClass = 'upcoming';
                            } else {
                                $badgeText = 'Hari ini';
                                $badgeClass = 'live';
                            }

                            $uDate = \Carbon\Carbon::parse($u->booking_date)->locale('id')->translatedFormat('d F Y');
                            $uTime = !empty($u->start_time)
                                ? str_replace(':', '.', substr($u->start_time, 0, 5)) . ' WIB'
                                  . (!empty($u->end_time) ? ' - ' . str_replace(':', '.', substr($u->end_time, 0, 5)) . ' WIB' : '')
                                : 'Waktu menyesuaikan';
                            $uOrg = $u->organization ?? $u->organizer ?? optional($u->user)->name ?? 'Pemohon';
                            $uVenue = optional($u->auditorium)->name ?? $u->event_name;
                            $hasDistinctEvent = !empty(optional($u->auditorium)->name) && !empty($u->event_name);
                        @endphp
                        <a href="{{ route('admin.bookings', ['booking_id' => $u->id, 'open' => $u->id]) }}" class="proto-card">
                            <div class="proto-card-top">
                                <div class="proto-card-heading">
                                    <h3 class="proto-card-title">{{ $uVenue }}</h3>
                                    <p class="proto-card-sub">
                                        @if($hasDistinctEvent)
                                            <span class="event-name">{{ $u->event_name }}</span>
                                            <span class="sub-dot">•</span>
                                        @endif
                                        <span class="org-name">{{ $uOrg }}</span>
                                    </p>
                                </div>
                                <span class="proto-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                            </div>

                            <div class="proto-schedule-bar">
                                <div class="proto-meta-item">
                                    <i data-lucide="calendar"></i>
                                    <span>{{ $uDate }}</span>
                                </div>
                                <div class="proto-meta-item">
                                    <i data-lucide="clock"></i>
                                    <span>{{ $uTime }}</span>
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
        const baseUrl = pie.dataset.baseUrl;

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

        function getPieSectionItem(event) {
            const rect = pie.getBoundingClientRect();

            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const x = event.clientX - centerX;
            const y = event.clientY - centerY;

            const distance = Math.sqrt(x * x + y * y);
            const radius = rect.width / 2;
            const innerRadius = radius * 0.58;

            if (distance < innerRadius) {
                return null;
            }

            let angle = Math.atan2(y, x) * 180 / Math.PI + 90;

            if (angle < 0) {
                angle += 360;
            }

            const percent = (angle / 360) * 100;
            let acc = 0;

            for (const item of legendItems) {
                const itemPercent = parseFloat(item.dataset.percent) || 0;
                if (itemPercent > 0 && percent >= acc && percent <= acc + itemPercent) {
                    return item;
                }
                acc += itemPercent;
            }

            return null;
        }

        // Hover pada diagram pie
        pie.addEventListener('mousemove', function (event) {
            const matchedItem = getPieSectionItem(event);
            if (matchedItem) {
                showDetail(matchedItem);
            } else {
                resetDetail();
            }
        });

        pie.addEventListener('mouseleave', resetDetail);

        // Klik pada diagram pie langsung mengarah ke Kelola Pengajuan
        pie.addEventListener('click', function (event) {
            const matchedItem = getPieSectionItem(event);
            if (matchedItem && matchedItem.dataset.url) {
                window.location.href = matchedItem.dataset.url;
            } else if (baseUrl) {
                window.location.href = baseUrl;
            }
        });

        // Hover pada legenda di samping diagram
        legendItems.forEach(function (item) {
            item.addEventListener('mouseenter', function () {
                showDetail(item);
            });
            item.addEventListener('mouseleave', resetDetail);
        });
    });
</script>

@endsection