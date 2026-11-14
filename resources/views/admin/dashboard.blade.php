
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
    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 16px;
    }

    .stat-top small {
        color: #64748b;
        font-weight: 500;
    }

    .stat-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }

    .stat-icon svg {
        width: 22px;
        height: 22px;
    }

    .stat-icon.blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .stat-icon.amber {
        background: #fef3c7;
        color: #d97706;
    }

    .stat-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .stat-icon.red {
        background: #fee2e2;
        color: #dc2626;
    }

    .stat-card strong {
        display: block;
        font-size: 28px;
        font-weight: 700;
    }

    /* KARTU DIAGRAM */
    .chart-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        margin-top: 25px;
    }

    .chart-heading {
        margin-bottom: 25px;
    }

    .chart-heading h2 {
        margin: 0 0 6px;
        font-size: 19px;
    }

    .chart-heading p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .chart-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 65px;
        flex-wrap: wrap;
    }

    /* DIAGRAM PIE */
    .pie-chart {
        width: 250px;
        height: 250px;
        position: relative;
        border-radius: 50%;
        flex-shrink: 0;
        transition: transform .3s ease;
    }

    .pie-chart:hover {
        transform: scale(1.04);
    }

    .pie-center {
        position: absolute;
        width: 145px;
        height: 145px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        pointer-events: none;
    }

    .pie-center strong {
        font-size: 32px;
        color: #1e293b;
        line-height: 1.2;
    }

    .pie-center span {
        font-size: 12px;
        color: #64748b;
    }

    /* TOOLTIP */
    .pie-tooltip {
        position: absolute;
        z-index: 5;
        top: -45px;
        left: 50%;
        transform: translateX(-50%);
        background: #1e293b;
        color: #fff;
        padding: 9px 14px;
        border-radius: 8px;
        font-size: 12px;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: .2s ease;
        pointer-events: none;
    }

    .pie-tooltip.show {
        opacity: 1;
        visibility: visible;
    }

    /* LEGENDA */
    .chart-legend {
        min-width: 230px;
        max-width: 300px;
        width: 100%;
    }

    .legend-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 9px;
        transition: background .2s ease, transform .2s ease;
        cursor: pointer;
    }

    .legend-item:hover,
    .legend-item.active {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .legend-label {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #475569;
        font-size: 14px;
    }

    .legend-color {
        width: 13px;
        height: 13px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .legend-item strong {
        color: #1e293b;
        font-size: 14px;
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
        color: #64748b;
        text-align: center;
        font-size: 13px;
        padding: 25px;
    }

    /* PANEL PENGAJUAN TERBARU */
    .recent-bookings-card {
        background: #ffffff;
        border: 1px solid #dcece1;
        border-radius: 24px;
        padding: 24px 30px 30px;
        margin-top: 25px;
        box-shadow: 0 4px 16px rgba(20, 60, 35, 0.04);
        box-sizing: border-box;
        width: 100%;
    }

    .recent-bookings-card .section-head {
        margin: 0 0 16px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .recent-bookings-card .section-head h2 {
        margin: 0;
        color: #14532d;
    }

    .recent-bookings-card .section-head a {
        color: #15803d;
        text-decoration: none;
        font-weight: 600;
    }

    .recent-bookings-card .section-head a:hover {
        color: #166534;
        text-decoration: underline;
    }

    /* TABEL PENGAJUAN TERBARU */
    .recent-bookings-card .table-wrap {
        background: #ffffff;
        border: 1px solid #dcece1;
        border-radius: 22px;
        overflow: hidden;
        margin: 0;
        width: 100%;
    }

    .recent-bookings-card table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .recent-bookings-card table thead th {
        background: #f0f9f3;
        color: #527466;
        border-bottom: 1px solid #dcece1;
    }

    .recent-bookings-card table tbody td {
        background: #ffffff;
        border-bottom: 1px solid #e5eee8;
    }

/* HOVER HALUS PADA BARIS PENGAJUAN TERBARU */
.recent-bookings-card table tbody tr {
    transition: background-color .2s ease;
}

.recent-bookings-card table tbody tr td {
    transition: background-color .2s ease;
}

.recent-bookings-card table tbody tr:hover td {
    background-color: #f5faf6;
}

    .recent-bookings-card table tbody tr:last-child td {
        border-bottom: none;
    }

    .recent-bookings-card table th,
    .recent-bookings-card table td {
        padding: 15px 12px;
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
        .chart-card {
            padding: 18px;
        }

        .chart-content {
            gap: 30px;
        }

        .pie-chart {
            width: 210px;
            height: 210px;
        }

        .pie-center {
            width: 125px;
            height: 125px;
        }

        .chart-legend {
            max-width: 100%;
        }

        .recent-bookings-card {
            padding: 18px;
            border-radius: 18px;
        }

        .recent-bookings-card .table-wrap {
            overflow-x: auto;
        }

        .recent-bookings-card table {
            min-width: 650px;
        }

        .recent-bookings-card .section-head {
            align-items: flex-start;
        }
    }

    @media (max-width: 480px) {
        .stats {
            grid-template-columns: 1fr;
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

{{-- JUDUL DASHBOARD --}}
<div class="page-heading">
    <div>
        <h1>Dashboard Admin</h1>
        <p>Ringkasan aktivitas peminjaman auditorium.</p>
    </div>

    <a class="btn" href="{{ route('admin.bookings') }}">
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
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <small>Pending</small>
            <span class="stat-icon amber">
                <i data-lucide="clock"></i>
            </span>
        </div>
        <strong>{{ $stats['pending'] }}</strong>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <small>Disetujui</small>
            <span class="stat-icon green">
                <i data-lucide="check-circle"></i>
            </span>
        </div>
        <strong>{{ $stats['approved'] }}</strong>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <small>Ditolak</small>
            <span class="stat-icon red">
                <i data-lucide="x-circle"></i>
            </span>
        </div>
        <strong>{{ $stats['rejected'] }}</strong>
    </div>

</div>

{{-- DIAGRAM PIE --}}
<div class="chart-card">

    <div class="chart-heading">
        <h2>Statistik Pengajuan</h2>
        <p>Arahkan kursor ke diagram atau kategori untuk melihat detail.</p>
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
<div class="recent-bookings-card">

    <div class="section-head">
        <h2>Pengajuan Terbaru</h2>
        <a href="{{ route('admin.bookings') }}">Lihat semua →</a>
    </div>

    <div class="table-wrap">
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
                @forelse($bookings as $b)
                    <tr>
                        <td>{{ $b->user->name }}</td>
                        <td>{{ $b->event_name }}</td>
                        <td>{{ $b->auditorium->name }}</td>
                        <td>{{ $b->booking_date->format('d/m/Y') }}</td>
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
