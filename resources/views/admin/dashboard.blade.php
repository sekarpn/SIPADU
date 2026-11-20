@extends('layouts.panel')

@section('panel')

@php
    $total = $stats['total'] ?? 0;
    $cancelled = $stats['cancelled'] ?? 0;

    $pendingPercent = $total > 0 ? (($stats['pending'] ?? 0) / $total) * 100 : 0;
    $approvedPercent = $total > 0 ? (($stats['approved'] ?? 0) / $total) * 100 : 0;
    $rejectedPercent = $total > 0 ? (($stats['rejected'] ?? 0) / $total) * 100 : 0;
    $cancelledPercent = $total > 0 ? ($cancelled / $total) * 100 : 0;

    $sourceBookings = isset($bookings) ? collect($bookings) : collect();

    $pendingBookings = $sourceBookings->filter(function ($item) {
        return in_array(strtolower((string) ($item->status ?? '')), ['pending', 'menunggu'], true);
    });

    if ($pendingBookings->isEmpty() && ($stats['pending'] ?? 0) > 0) {
        $pendingBookings = \App\Models\Booking::with(['user', 'auditorium'])
            ->whereIn('status', ['Pending', 'pending', 'PENDING', 'Menunggu', 'menunggu'])
            ->latest()
            ->get();
    }

    $upcomingBookings = isset($upcomingBookings)
        ? collect($upcomingBookings)
        : \App\Models\Booking::with(['user', 'auditorium'])
            ->whereIn('status', ['Disetujui', 'disetujui', 'Approved', 'approved', 'APPROVED'])
            ->whereDate('booking_date', '>=', now()->toDateString())
            ->orderBy('booking_date', 'asc')
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

    .section-head a {
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
        transition: background 0.2s ease, gap 0.2s ease, color 0.2s ease;
    }

    .section-head a svg {
        width: 14px;
        height: 14px;
    }

    .section-head a:hover {
        background: rgba(35, 124, 77, 0.18);
        color: #155a35;
        gap: 7px;
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

    /* GRID 2 KOLOM BERDAMPINGAN: DAFTAR PENGAJUAN TERBARU (PENDING) & JADWAL PEMINJAMAN MENDATANG */
    .dash-tables-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: start;
    }

    .dash-table-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.68) 0%, rgba(255, 255, 255, 0.42) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.55) !important;
        border-radius: 16px !important;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.06) !important;
        overflow: hidden;
        min-width: 0;
    }

    .dash-table-card .section-head {
        padding: 16px 18px !important;
        margin: 0 !important;
        border-bottom: 1px solid rgba(22, 54, 38, 0.09);
        background: rgba(255, 255, 255, 0.28);
    }

    .dash-title-row {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .dash-badge-count {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        background: rgba(35, 124, 77, 0.14);
        color: #1f7a49;
    }

    .dash-badge-count.amber {
        background: #fef3c7;
        color: #92400e;
    }

    .dash-cell-primary {
        display: block;
        font-weight: 700;
        color: #163626;
    }

    .dash-cell-sub {
        display: block;
        font-size: 11.5px;
        font-weight: 500;
        color: #64786c;
        margin-top: 2px;
    }

    /* BADGE STATUS */
    .status {
        display: inline-flex;
        align-items: center;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status.pending,
    .status.menunggu {
        color: #92400e;
        background: #fef3c7;
        border: 1px solid #fcd34d;
    }

    .status.disetujui,
    .status.approved {
        color: #065f46;
        background: #bbf7d0;
        border: 1px solid #34d399;
    }

    .status.ditolak,
    .status.rejected {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fca5a5;
    }

    .status.dibatalkan,
    .status.cancelled {
        color: #475569;
        background: #e2e8f0;
        border: 1px solid #cbd5e1;
    }

    .dash-empty-state {
        text-align: center;
        color: #4c6456;
        padding: 32px 14px;
        font-size: 12.5px;
    }

    /* RESPONSIVE */
    @media (max-width: 1180px) {
        .dash-tables-grid {
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

        .dashboard-panel {
            padding: 14px 15px 16px !important;
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

    {{-- 2 TABEL BERDAMPINGAN: DAFTAR PENGAJUAN TERBARU (PENDING) & JADWAL PEMINJAMAN MENDATANG --}}
    <div class="dash-tables-grid">

        {{-- TABEL 1: DAFTAR PENGAJUAN TERBARU (KHUSUS STATUS PENDING) --}}
        <div class="dash-table-card">
            <div class="section-head">
                <div>
                    <div class="dash-title-row">
                        <h2>Daftar Pengajuan Terbaru</h2>
                        <span class="dash-badge-count amber">{{ $pendingBookings->count() }} Pending</span>
                    </div>
                    <p>Pengajuan berstatus pending yang menunggu verifikasi.</p>
                </div>

                <a href="{{ route('admin.bookings', ['status' => 'pending']) }}">
                    Lihat semua
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div class="sipadu-table-scroll">
                <table>
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
                        @forelse($pendingBookings as $b)
                            <tr>
                                <td>
                                    <span class="dash-cell-primary">{{ optional($b->user)->name ?? 'Pemohon' }}</span>
                                </td>
                                <td>
                                    <span class="dash-cell-primary">{{ $b->event_name }}</span>
                                </td>
                                <td>{{ optional($b->auditorium)->name ?? '-' }}</td>
                                <td>
                                    {{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    <span class="status pending">
                                        Pending
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-row="1">
                                <td colspan="5" class="dash-empty-state">
                                    Tidak ada pengajuan pending saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 2: JADWAL PEMINJAMAN MENDATANG --}}
        <div class="dash-table-card">
            <div class="section-head">
                <div>
                    <div class="dash-title-row">
                        <h2>Jadwal Peminjaman Mendatang</h2>
                        <span class="dash-badge-count">{{ $upcomingBookings->count() }} Jadwal</span>
                    </div>
                    <p>Agenda penggunaan auditorium yang telah disetujui.</p>
                </div>

                <a href="{{ route('admin.bookings', ['status' => 'approved']) }}">
                    Lihat semua
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div class="sipadu-table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Auditorium</th>
                            <th>Kegiatan</th>
                            <th>Peminjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($upcomingBookings as $u)
                            <tr>
                                <td>
                                    <span class="dash-cell-primary">
                                        {{ $u->booking_date ? \Carbon\Carbon::parse($u->booking_date)->format('d M Y') : '-' }}
                                    </span>
                                    @if(!empty($u->start_time))
                                        <span class="dash-cell-sub">
                                            {{ substr($u->start_time, 0, 5) }}@if(!empty($u->end_time)) - {{ substr($u->end_time, 0, 5) }} WIB @endif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="dash-cell-primary">{{ optional($u->auditorium)->name ?? '-' }}</span>
                                </td>
                                <td>{{ $u->event_name }}</td>
                                <td>{{ optional($u->user)->name ?? 'Pemohon' }}</td>
                                <td>
                                    <span class="status disetujui">
                                        Disetujui
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr data-empty-row="1">
                                <td colspan="5" class="dash-empty-state">
                                    Belum ada jadwal peminjaman mendatang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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