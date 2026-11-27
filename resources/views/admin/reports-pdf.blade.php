<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Peminjaman Auditorium</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 24px 26px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8.5px;
            color: #183829;
            line-height: 1.4;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 5px;
            color: #163626;
        }

        .meta {
            color: #566b5e;
            margin-bottom: 12px;
            font-size: 8.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #e8f2eb;
            color: #1c5035;
            font-weight: bold;
            text-align: left;
        }

        th,
        td {
            border: 1px solid #cddbd1;
            padding: 5px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .num {
            text-align: center;
            width: 32px;
        }

        .status-col {
            text-align: center;
            white-space: nowrap;
            font-weight: bold;
        }

        tr:nth-child(even) td {
            background: #f8fbf8;
        }

        .empty-cell {
            text-align: center;
            padding: 14px;
            color: #566b5e;
        }
    </style>
</head>

<body>
    @php
        $filterStart = $startDate ?? request('start_date');
        $filterEnd = $endDate ?? request('end_date');
        $filterStatus = request('status');
        $filterQuery = request('q');
        $printedAt = $generatedAt ?? now()->timezone('Asia/Jakarta');
        $periodeDisplay = $periodeLabel ?? (($filterStart || $filterEnd)
            ? (($filterStart ? \Carbon\Carbon::parse($filterStart)->format('d/m/Y') : 'Awal')
                . ' – '
                . ($filterEnd ? \Carbon\Carbon::parse($filterEnd)->format('d/m/Y') : 'Akhir'))
            : 'Semua Tanggal');
    @endphp

    <h1>Laporan Riwayat Peminjaman Auditorium</h1>

    <div class="meta">
        <strong>Periode:</strong> {{ $periodeDisplay }}
        @if($filterStatus && $filterStatus !== 'Pending')
            | <strong>Status:</strong> {{ $filterStatus }}
        @endif
        @if($filterQuery)
            | <strong>Pencarian:</strong> "{{ $filterQuery }}"
        @endif
        <br>
        <strong>Dicetak:</strong> {{ \Carbon\Carbon::parse($printedAt)->format('d/m/Y H:i') }} WIB
        | <strong>Total data:</strong> {{ $bookings->count() }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="num">No</th>
                <th>Peminjam</th>
                <th>Auditorium</th>
                <th>Kegiatan</th>
                <th>Organisasi</th>
                <th>Tanggal</th>
                <th>Jam</th>
                <th class="status-col">Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse($bookings as $index => $booking)
                <tr>
                    <td class="num">{{ $index + 1 }}</td>

                    <td>
                        <strong>{{ $booking->user->name ?? '-' }}</strong>
                        <br>
                        <span>{{ $booking->applicant_identifier ?? $booking->user->nim_nip ?? '-' }}</span>
                    </td>

                    <td>{{ $booking->auditorium->name ?? '-' }}</td>

                    <td>{{ $booking->event_name ?? '-' }}</td>

                    <td>{{ $booking->organization ?? '-' }}</td>

                    <td>
                        {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : '-' }}
                    </td>

                    <td>
                        {{ substr((string) $booking->start_time, 0, 5) }}–{{ substr((string) $booking->end_time, 0, 5) }}
                    </td>

                    <td class="status-col">{{ $booking->status ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-cell">
                        Tidak ada data riwayat peminjaman pada filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>