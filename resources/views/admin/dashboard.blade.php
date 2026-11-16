@extends('layouts.panel')

@section('panel')

@php
    $total = $stats['total'];
    $cancelled = $stats['cancelled'] ?? 0;

    $pendingPercent = $total > 0 ? ($stats['pending'] / $total) * 100 : 0;
    $approvedPercent = $total > 0 ? ($stats['approved'] / $total) * 100 : 0;
    $rejectedPercent = $total > 0 ? ($stats['rejected'] / $total) * 100 : 0;
    $cancelledPercent = $total > 0 ? ($cancelled / $total) * 100 : 0;
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

    .dashboard-page {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 10px 35px;
    }

    /* HEADER (DISAMAKAN DENGAN DASHBOARD PEMINJAM) */
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
        box-shadow: 0 4px 14px rgba(35, 124, 77, 0.25);
        transition: .2s ease;
        white-space: nowrap;
    }

    .dashboard-action:hover {
        background: #1a633c;
        color: white;
        transform: translateY(-2px);
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

    /* PANEL GLASS UMUM (UNTUK DIAGRAM & PENGAJUAN TERBARU) */
    .dashboard-panel {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.28) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        border-radius: 16px;
        padding: 12px 20px 18px 20px !important;
        margin-bottom: 20px;
        min-width: 0;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.05) !important;
        box-sizing: border-box;
        width: 100%;
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
        margin-top: 0 !important;
        padding-top: 0 !important;
        margin-bottom: 14px;
    }

    .section-head h2 {
        font-size: 15.5px;
        font-weight: 750;
        color: #163626;
        margin: 0 0 2px !important;
        padding-top: 0 !important;
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
        font-size: 11.5px;
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
    }

    /* KARTU DIAGRAM PIE (GLASSMORPHISM) */
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
        font-weight: 750;
        color: #163626;
        line-height: 1.2;
    }

    .pie-center span {
        font-size: 11.5px;
        font-weight: 600;
        color: #4c6456;
    }

    /* TOOLTIP */
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

    /* LEGENDA KACA SUB-ITEM */
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
        background: rgba(255, 255, 255, 0.42);
        border: 1px solid rgba(255, 255, 255, 0.35);
        transition: background .2s ease, transform .2s ease;
        cursor: pointer;
    }

    .legend-item:hover,
    .legend-item.active {
        background: rgba(255, 255, 255, 0.72);
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

    /* TABEL PENGAJUAN TERBARU (SAMA PERSIS DENGAN DASHBOARD PEMINJAM) */
    .dashboard-table-wrap {
        width: 100%;
        background: rgba(255, 255, 255, 0.42) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-radius: 12px;
        overflow-x: auto;
        overflow-y: hidden;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        min-width: 600px;
    }

    .dashboard-table th {
        background: rgba(35, 124, 77, 0.14) !important;
        color: #163626 !important;
        font-size: 11px;
        font-weight: 750;
        text-align: left;
        padding: 13px 14px;
        border-bottom: none !important;
    }

    .dashboard-table td {
        color: #243b2e;
        font-weight: 500;
        padding: 13px 14px;
        border-bottom: 1px solid rgba(22, 54, 38, 0.06);
        vertical-align: middle;
        transition: background 0.2s ease;
    }

    .dashboard-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.55) !important;
    }

    .dashboard-table tr:last-child td {
        border-bottom: none;
    }

    .dashboard-table .empty {
        text-align: center;
        color: #4c6456;
        padding: 25px 10px;
    }

    /* BADGE STATUS */
    .status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status.pending {
        color: #b4690e;
        background: rgba(254, 240, 213, 0.92);
    }

    .status.disetujui {
        color: #157343;
        background: rgba(220, 243, 229, 0.92);
    }

    .status.ditolak {
        color: #c53030;
        background: rgba(254, 226, 226, 0.92);
    }

    .status.dibatalkan {
        color: #475569;
        background: rgba(226, 232, 240, 0.92);
    }

    /* RESPONSIVE */
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

        .dashboard-panel {
            padding: 12px 15px 15px 15px !important;
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
        .pie-chart,
        .legend-item,
        .pie-tooltip {
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

    {{-- PENGAJUAN TERBARU --}}
    <div class="dashboard-panel" style="margin-bottom: 0;">
        <div class="section-head">
            <div>
                <h2>Pengajuan Terbaru</h2>
                <p>Daftar pengajuan peminjaman auditorium terbaru</p>
            </div>

            <a href="{{ route('admin.bookings') }}">
                Lihat semua
                <i data-lucide="arrow-right"></i>
            </a>
        </div>

        <div class="dashboard-table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Kegiatan</th>
                        <th>Auditorium</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td>{{ $b->user->name }}</td>
                            <td>{{ $b->event_name }}</td>
                            <td>{{ $b->auditorium->name }}</td>
                            <td>{{ $b->booking_date->format('d M Y') }}</td>
                            <td>
                                <span class="status {{ strtolower($b->status) }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                Belum ada pengajuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    if (window.lucide) {
        lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const pie = document.getElementById('bookingPie');
        const tooltip = document.getElementById('pieTooltip');
        const pieTotal = document.getElementById('pieTotal');
        const pieLabel = document.getElementById('pieLabel');
        const legendItems = document.querySelectorAll('.legend-item');

        if (!pie || !tooltip) return;

        const total = pie.dataset.total;

        // Membuat diagram pie menggunakan JavaScript.
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

            // Abaikan area tengah diagram.
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