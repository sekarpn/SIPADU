
@extends('layouts.panel')

@section('panel')

<div class="page-heading">
    <div>
        <h1>Pengelolaan Pengajuan</h1>
        <p>Periksa permohonan, dokumen surat, dan tetapkan keputusan.</p>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Peminjam</th>
                <th>Kegiatan</th>
                <th>Auditorium</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Surat Peminjaman</th>
                <th>Status</th>
                <th>Keputusan</th>
            </tr>
        </thead>

        <tbody>
            @forelse($bookings as $b)
                <tr>
                    <td>
                        {{ $b->user->name }}
                    </td>

                    <td>
                        {{ $b->event_name }}
                        <small class="subline">
                            {{ $b->organization }}
                        </small>
                    </td>

                    <td>
                        {{ $b->auditorium->name }}
                    </td>

                    <td>
                        {{ $b->booking_date->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}
                    </td>

                    {{-- DOWNLOAD SURAT --}}
                    <td>
                        @if($b->surat_path)
                            <a
                                href="{{ route('admin.booking.letter', $b) }}"
                                class="btn"
                                style="font-size:12px;padding:8px 12px;"
                            >
                                ↓ Unduh Surat
                            </a>
                        @else
                            <span style="color:#dc2626;font-size:13px;">
                                Belum ada surat
                            </span>
                        @endif
                    </td>

                    <td>
                        <span class="status {{ strtolower($b->status) }}">
                            {{ $b->status }}
                        </span>
                    </td>

                    <td>
                        @if($b->status === 'Pending')

                            <form
                                method="POST"
                                action="{{ route('admin.booking.decide', $b) }}"
                                class="decision"
                            >
                                @csrf
                                @method('PATCH')

                                <input
                                    name="admin_note"
                                    placeholder="Catatan (opsional)"
                                >

                                <button
                                    name="status"
                                    value="Disetujui"
                                    class="mini approve"
                                >
                                    Setujui
                                </button>

                                <button
                                    name="status"
                                    value="Ditolak"
                                    class="mini reject"
                                >
                                    Tolak
                                </button>
                            </form>

                        @else

                            {{ $b->admin_note ?: '—' }}

                        @endif
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="8" class="empty">
                        Belum ada pengajuan peminjaman.
                    </td>
                </tr>

            @endforelse
        </tbody>
    </table>
</div>

{{ $bookings->links() }}

@endsection
