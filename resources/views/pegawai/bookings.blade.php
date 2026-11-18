
@extends('layouts.panel')

@section('panel')

<style>
    .booking-table td {
        vertical-align: top;
    }

    .admin-feedback {
        margin-top: 8px;
        padding: 10px 12px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 4px solid #16a34a;
        border-radius: 7px;
        color: #166534;
        font-size: 13px;
        line-height: 1.6;
        max-width: 350px;
    }

    .admin-feedback strong {
        display: block;
        margin-bottom: 4px;
        color: #15803d;
    }

    .admin-feedback p {
        margin: 0;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    .no-feedback {
        color: #9ca3af;
        font-size: 13px;
    }
</style>

<div class="page-heading">
    <div>
        <h1>Peminjaman Saya</h1>
        <p>Riwayat dan status seluruh pengajuan Anda.</p>
    </div>

    <a class="btn" href="{{ route('pegawai.booking.create') }}">
        ＋ Ajukan Peminjaman
    </a>
</div>

<div class="table-wrap">
    <table class="booking-table">
        <thead>
            <tr>
                <th>Kegiatan</th>
                <th>Auditorium</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Catatan Admin</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td>{{ $b->event_name }}</td>

                    <td>{{ $b->auditorium->name }}</td>

                    <td>
                        {{ $b->booking_date->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}
                    </td>

                    <td>
                        <span class="status {{ strtolower(str_replace(' ', '-', $b->status)) }}">
                            {{ $b->status }}
                        </span>
                    </td>

                    <td>
                        @if(!empty($b->admin_note))
                            <div class="admin-feedback">
                                <strong>Catatan dari Admin</strong>
                                <p>{{ $b->admin_note }}</p>
                            </div>
                        @else
                            <span class="no-feedback">
                                Belum ada catatan
                            </span>
                        @endif
                    </td>

                    <td>
                        @if($b->status === 'Pending')
                            <form method="POST"
                                  action="{{ route('pegawai.booking.cancel', $b) }}">
                                @csrf
                                @method('PATCH')

                                <button class="link danger"
                                        data-confirm="Batalkan pengajuan ini?">
                                    Batalkan
                                </button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        Belum ada data pengajuan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $bookings->links() }}

@endsection
