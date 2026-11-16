
@extends('layouts.panel')

@section('panel')
@php
    $statusOptions = [
        'Pending' => 'Menunggu',
        'Disetujui' => 'Disetujui',
        'Ditolak' => 'Ditolak',
        'Dibatalkan' => 'Dibatalkan',
    ];

    $statusClasses = [
        'Pending' => 'st-pending',
        'Disetujui' => 'st-approved',
        'Ditolak' => 'st-rejected',
        'Dibatalkan' => 'st-cancelled',
    ];
@endphp

<style>
.booking-page {
    --bp-ink: #172b20;
    --bp-muted: #718078;
    --bp-line: #e4ebe6;
    color: var(--bp-ink);
}

.booking-page .bp-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 18px;
    margin: 2px 0 22px;
}

.booking-page .bp-heading h1 {
    font-size: 27px;
    letter-spacing: -.04em;
    margin: 0;
    color: #1c3527;
}

.booking-page .bp-heading p {
    margin: 7px 0 0;
    color: var(--bp-muted);
    font-size: 12px;
    line-height: 1.6;
}

.booking-page .bp-eyebrow {
    font-size: 10px;
    letter-spacing: .13em;
    text-transform: uppercase;
    font-weight: 800;
    color: #25804d;
    margin-bottom: 7px;
}

.booking-page .bp-stats {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 13px;
    margin-bottom: 18px;
}

.booking-page .bp-stat {
    background: #fff;
    border: 1px solid var(--bp-line);
    border-radius: 15px;
    padding: 17px 18px;
    display: flex;
    align-items: center;
    gap: 13px;
    min-width: 0;
}

.booking-page .bp-stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 17px;
    background: #edf6f0;
    color: #237c4d;
    flex-shrink: 0;
}

.booking-page .bp-stat small {
    display: block;
    color: var(--bp-muted);
    font-size: 10px;
    font-weight: 700;
}

.booking-page .bp-stat strong {
    display: block;
    font-size: 24px;
    line-height: 1.25;
    margin-top: 3px;
}

.booking-page .bp-card {
    background: #fff;
    border: 1px solid var(--bp-line);
    border-radius: 15px;
    overflow: hidden;
}

.booking-page .bp-toolbar {
    padding: 17px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    border-bottom: 1px solid var(--bp-line);
}

.booking-page .bp-search {
    position: relative;
    flex: 1 1 230px;
}

.booking-page .bp-search input {
    width: 100%;
    height: 39px;
    padding: 0 12px 0 36px;
    border: 1px solid #dfe8e2;
    border-radius: 10px;
    background: #fbfdfb;
    font-size: 11px;
    outline: none;
}

.booking-page .bp-search span {
    position: absolute;
    left: 12px;
    top: 10px;
    color: #809087;
}

.booking-page .bp-filter {
    height: 39px;
    padding: 0 11px;
    border: 1px solid #dfe8e2;
    border-radius: 10px;
    background: #fff;
    color: #42554a;
    font-size: 11px;
    min-width: 130px;
}

.booking-page .bp-filter:focus,
.booking-page .bp-search input:focus {
    border-color: #318456;
    box-shadow: 0 0 0 3px #31845618;
    outline: none;
}

.booking-page .bp-reset {
    font-size: 11px;
    font-weight: 700;
    color: #277b4b;
    padding: 10px 8px;
    text-decoration: none;
}

.booking-page .bp-tabs {
    display: flex;
    gap: 6px;
    overflow-x: auto;
    padding: 12px 17px 0;
}

.booking-page .bp-tab {
    display: inline-flex;
    align-items: center;
    border: 1px solid transparent;
    border-radius: 9px;
    padding: 8px 11px;
    font-size: 10px;
    font-weight: 800;
    color: #718078;
    white-space: nowrap;
    text-decoration: none;
}

.booking-page .bp-tab.active {
    background: #eaf5ee;
    color: #237c4d;
    border-color: #d7ebde;
}

.booking-page .bp-tab span {
    margin-left: 4px;
    opacity: .7;
}

.booking-page .bp-table-wrap {
    overflow-x: auto;
}

.booking-page table {
    width: 100%;
    border-collapse: collapse;
    min-width: 980px;
}

.booking-page th {
    padding: 12px 14px;
    text-align: left;
    background: #fafcfb;
    border-bottom: 1px solid var(--bp-line);
    font-size: 9px;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #829087;
    white-space: nowrap;
}

.booking-page td {
    padding: 14px;
    border-bottom: 1px solid #edf1ee;
    vertical-align: middle;
    font-size: 11px;
    color: #33463a;
}

.booking-page tbody tr:last-child td {
    border-bottom: 0;
}

.booking-page .bp-code {
    font-size: 9px;
    font-weight: 800;
    color: #25804d;
    letter-spacing: .02em;
}

.booking-page .bp-primary {
    font-weight: 800;
    color: #23382b;
    display: block;
    max-width: 220px;
    white-space: normal;
    line-height: 1.45;
}

.booking-page .bp-sub {
    display: block;
    color: #87938b;
    font-size: 9px;
    margin-top: 4px;
    line-height: 1.45;
}

.booking-page .bp-status {
    display: inline-flex;
    padding: 5px 8px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 800;
    white-space: nowrap;
}

.booking-page .st-pending {
    background: #fff4dc;
    color: #98650b;
}

.booking-page .st-approved {
    background: #e6f5eb;
    color: #167341;
}

.booking-page .st-rejected {
    background: #ffebeb;
    color: #b32e2e;
}

.booking-page .st-cancelled {
    background: #eff1f0;
    color: #647168;
}

.booking-page .bp-actions {
    display: flex;
    gap: 6px;
    align-items: center;
    flex-wrap: wrap;
}

.booking-page .bp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 8px;
    padding: 8px 10px;
    font-size: 10px;
    font-weight: 800;
    cursor: pointer;
    text-decoration: none;
    transition: .2s ease;
}

.booking-page .bp-btn:hover {
    filter: brightness(.96);
    transform: translateY(-1px);
}

.booking-page .bp-btn:disabled {
    cursor: not-allowed;
    transform: none;
}

.booking-page .bp-view {
    background: #eef4f0;
    color: #315b40;
}

.booking-page .bp-approve {
    background: #e5f5ea;
    color: #167341;
}

.booking-page .bp-reject {
    background: #fff0f0;
    color: #b32e2e;
}

.booking-page .bp-detail {
    display: none;
    background: #f8fbf8;
}

.booking-page .bp-detail.open {
    display: table-row;
}

.booking-page .bp-detail td {
    padding: 17px 20px;
}

.booking-page .bp-detail-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
}

.booking-page .bp-detail-item small {
    display: block;
    color: #829087;
    font-size: 9px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: 5px;
}

.booking-page .bp-detail-item strong {
    font-size: 11px;
    font-weight: 700;
    white-space: normal;
    line-height: 1.5;
}

.booking-page .bp-detail-footer {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 15px;
    padding-top: 13px;
    border-top: 1px solid #e5ece7;
}

.booking-page .bp-empty {
    padding: 40px 20px;
    text-align: center;
    color: #7b8980;
    font-size: 12px;
}

.booking-page .bp-pagination {
    padding: 14px 17px;
    border-top: 1px solid var(--bp-line);
}

.booking-page .bp-modal-backdrop {
    position: fixed;
    inset: 0;
    background: #10221977;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
}

.booking-page .bp-modal-backdrop.show {
    display: flex;
}

.booking-page .bp-modal {
    width: min(520px, 100%);
    max-height: 90vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 17px;
    padding: 22px;
    box-shadow: 0 25px 70px #071b1040;
}

.booking-page .bp-modal h3 {
    font-size: 16px;
    margin: 0 0 7px;
    color: #1c3527;
}

.booking-page .bp-modal p {
    font-size: 11px;
    color: #6f7e74;
    line-height: 1.6;
}

.booking-page .bp-modal label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    margin: 13px 0 6px;
    color: #33463a;
}

.booking-page .bp-modal textarea {
    width: 100%;
    border: 1px solid #dfe8e2;
    border-radius: 9px;
    padding: 10px;
    font-size: 11px;
    min-height: 82px;
    resize: vertical;
    outline: none;
    box-sizing: border-box;
}

.booking-page .bp-modal textarea:focus {
    border-color: #318456;
    box-shadow: 0 0 0 3px #31845618;
}

.booking-page .bp-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 16px;
}

.booking-page .bp-modal .bp-confirm {
    background: #237c4d;
    color: #fff;
}

.booking-page .bp-modal .bp-cancel {
    background: #eef2ef;
    color: #45564b;
}

@media(max-width: 900px) {
    .booking-page .bp-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .booking-page .bp-heading {
        flex-direction: column;
    }
}

@media(max-width: 560px) {
    .booking-page .bp-stats {
        gap: 8px;
    }

    .booking-page .bp-stat {
        padding: 12px;
    }

    .booking-page .bp-stat strong {
        font-size: 20px;
    }

    .booking-page .bp-stat-icon {
        width: 32px;
        height: 32px;
    }

    .booking-page .bp-detail-grid {
        grid-template-columns: 1fr 1fr;
    }

    .booking-page .bp-heading h1 {
        font-size: 23px;
    }

    .booking-page .bp-toolbar {
        padding: 12px;
    }

    .booking-page .bp-filter {
        flex: 1 1 100%;
        width: 100%;
    }
}
</style>

<div class="booking-page">

    {{-- HEADER --}}
    <div class="bp-heading">
        <div>
            <h1>Kelola Peminjaman</h1>
            <p>Verifikasi pengajuan, periksa dokumen, tinjau jadwal bentrok, dan tetapkan keputusan peminjaman auditorium.</p>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="bp-stats">
        <div class="bp-stat">
            <div class="bp-stat-icon">◷</div>
            <div>
                <small>Menunggu Verifikasi</small>
                <strong>{{ $stats['pending'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon">✓</div>
            <div>
                <small>Disetujui</small>
                <strong>{{ $stats['approved'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon">×</div>
            <div>
                <small>Ditolak</small>
                <strong>{{ $stats['rejected'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon">▤</div>
            <div>
                <small>Total Pengajuan</small>
                <strong>{{ $stats['total'] }}</strong>
            </div>
        </div>
    </div>

    {{-- PESAN VALIDASI --}}
    @if($errors->any())
        <div style="background:#fff0f0;color:#a22;border:1px solid #f5cccc;border-radius:10px;padding:12px 15px;margin-bottom:14px;font-size:11px">
            <strong>Pengajuan belum dapat diproses.</strong>
            <ul style="margin:5px 0 0 17px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div style="background:#eaf7ee;color:#176b3b;border:1px solid #d2ebda;border-radius:10px;padding:12px 15px;margin-bottom:14px;font-size:11px">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABEL PENGELOLAAN --}}
    <div class="bp-card">

        {{-- FILTER DAN PENCARIAN --}}
        <form method="GET" action="{{ route('admin.bookings') }}" class="bp-toolbar">

            <div class="bp-search">
                <span>⌕</span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari kode, kegiatan, peminjam, atau organisasi..."
                >
            </div>

            <select class="bp-filter" name="status">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select class="bp-filter" name="auditorium">
                <option value="">Semua Auditorium</option>
                @foreach($auditoriums as $auditorium)
                    <option
                        value="{{ $auditorium->id }}"
                        @selected((string) request('auditorium') === (string) $auditorium->id)
                    >
                        {{ $auditorium->name }}
                    </option>
                @endforeach
            </select>

            <input
                class="bp-filter"
                type="date"
                name="date"
                value="{{ request('date') }}"
                aria-label="Filter tanggal"
            >

            <button class="bp-btn bp-approve" type="submit">
                Terapkan Filter
            </button>

            <a class="bp-reset" href="{{ route('admin.bookings') }}">
                Reset
            </a>
        </form>

        {{-- TAB STATUS --}}
        <div class="bp-tabs">
            <a
                class="bp-tab {{ !request('status') ? 'active' : '' }}"
                href="{{ route('admin.bookings', request()->except('status', 'page')) }}"
            >
                Semua <span>{{ $stats['total'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Pending' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Pending'])) }}"
            >
                Menunggu <span>{{ $stats['pending'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Disetujui' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Disetujui'])) }}"
            >
                Disetujui <span>{{ $stats['approved'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Ditolak' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Ditolak'])) }}"
            >
                Ditolak <span>{{ $stats['rejected'] }}</span>
            </a>
        </div>

        {{-- DAFTAR PENGAJUAN --}}
        <div class="bp-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengajuan</th>
                        <th>Peminjam / Pengusul</th>
                        <th>Auditorium & Jadwal</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th>Keputusan / Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($bookings as $b)

                    @php
                        $statusClass = $statusClasses[$b->status] ?? 'st-cancelled';

                        $overlapBookings = $b->status === 'Pending'
                            ? $b->auditorium->bookings()
                                ->where('id', '!=', $b->id)
                                ->whereDate('booking_date', $b->booking_date)
                                ->where('start_time', '<', $b->end_time)
                                ->where('end_time', '>', $b->start_time)
                            : null;

                        $approvedConflicts = $overlapBookings
                            ? (clone $overlapBookings)->where('status', 'Disetujui')->count()
                            : 0;

                        $pendingConflicts = $overlapBookings
                            ? (clone $overlapBookings)->where('status', 'Pending')->count()
                            : 0;
                    @endphp

                    {{-- BARIS UTAMA --}}
                    <tr>
                        <td>
                            <span class="bp-code">
                                SPD-{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }}
                            </span>

                            <span class="bp-sub">
                                Diajukan {{ $b->created_at->format('d/m/Y · H:i') }}
                            </span>
                        </td>

                        <td>
                            <span class="bp-primary">{{ $b->user->name }}</span>
                            <span class="bp-sub">{{ $b->organization }}</span>
                        </td>

                        <td>
                            <span class="bp-primary">{{ $b->auditorium->name }}</span>

                            <span class="bp-sub">
                                {{ $b->booking_date->translatedFormat('d M Y') }}
                                ·
                                {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}
                            </span>

                            @if($approvedConflicts > 0)
                                <span class="bp-sub" style="color:#b32e2e;font-weight:800">
                                    ⚠ Slot terkunci oleh {{ $approvedConflicts }} peminjaman disetujui
                                </span>
                            @elseif($pendingConflicts > 0)
                                <span class="bp-sub" style="color:#b7791f;font-weight:800">
                                    ⚡ {{ $pendingConflicts }} pengajuan lain memiliki jadwal beririsan
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($b->surat_path)
                                <a class="bp-btn bp-view" href="{{ route('admin.booking.letter', $b) }}">
                                    ↓ Unduh PDF
                                </a>
                            @else
                                <span class="bp-sub" style="color:#b32e2e">
                                    Belum tersedia
                                </span>
                            @endif
                        </td>

                        <td>
                            <span class="bp-status {{ $statusClass }}">
                                {{ $b->status === 'Pending' ? 'Menunggu Verifikasi' : $b->status }}
                            </span>
                        </td>

                        <td>
                            <div class="bp-actions">
                                <button
                                    type="button"
                                    class="bp-btn bp-view"
                                    data-toggle-detail="{{ $b->id }}"
                                >
                                    Detail
                                </button>
                                @if($b->status === 'Pending')
                                    <button
                                        type="button"
                                        class="bp-btn bp-approve"
                                        data-decision-id="{{ $b->id }}"
                                        data-decision-status="Disetujui"
                                        data-approved-conflicts="{{ $approvedConflicts }}"
                                        data-pending-conflicts="{{ $pendingConflicts }}"
                                    >
                                        Setujui
                                    </button>

                                    <button
                                        type="button"
                                        class="bp-btn bp-reject"
                                        data-decision-id="{{ $b->id }}"
                                        data-decision-status="Ditolak"
                                        data-approved-conflicts="0"
                                        data-pending-conflicts="0"
                                    >
                                        Tolak
                                    </button>
                                @endif
                            </div>

                            @if($b->admin_note)
                                <span class="bp-sub" style="max-width:220px;white-space:normal">
                                    Catatan: {{ $b->admin_note }}
                                </span>
                            @endif
                        </td>
                    </tr>

                    {{-- DETAIL PENGAJUAN --}}
                    <tr id="booking-detail-{{ $b->id }}" class="bp-detail">
                        <td colspan="6">

                            <div class="bp-detail-grid">

                                <div class="bp-detail-item">
                                    <small>Nama Kegiatan</small>
                                    <strong>{{ $b->event_name }}</strong>
                                </div>

                                <div class="bp-detail-item">
                                    <small>Penanggung Jawab</small>
                                    <strong>{{ $b->pic_name }} · {{ $b->pic_phone }}</strong>
                                </div>

                                <div class="bp-detail-item">
                                    <small>Auditorium</small>
                                    <strong>
                                        {{ $b->auditorium->name }}
                                        <br>
                                        {{ $b->auditorium->location }}
                                    </strong>
                                </div>

                                <div class="bp-detail-item">
                                    <small>Deskripsi Kegiatan</small>
                                    <strong>
                                        {{ $b->event_description ?: 'Tidak ada deskripsi.' }}
                                    </strong>
                                </div>

                                <div class="bp-detail-item">
                                    <small>Waktu Peminjaman</small>
                                    <strong>
                                        {{ $b->booking_date->translatedFormat('l, d F Y') }}
                                        <br>
                                        {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }} WIB
                                    </strong>
                                </div>

                                <div class="bp-detail-item">
                                    <small>Kontak Peminjam</small>
                                    <strong>
                                        {{ $b->user->email }}
                                        <br>
                                        {{ $b->user->phone ?: 'Nomor telepon tidak tersedia' }}
                                    </strong>
                                </div>

                            </div>

                            {{-- RIWAYAT KEPUTUSAN --}}
                            @if($b->histories->isNotEmpty())
                                <div style="margin-top:15px;padding-top:13px;border-top:1px solid #e5ece7">

                                    <div style="font-size:9px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#829087;margin-bottom:8px">
                                        Riwayat Keputusan
                                    </div>

                                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                                        @foreach($b->histories->sortByDesc('created_at') as $history)

                                            <div style="background:#fff;border:1px solid #e5ece7;border-radius:9px;padding:9px 11px;min-width:180px">

                                                <strong style="font-size:10px">
                                                    {{ $history->from_status ?: 'Pengajuan' }}
                                                    →
                                                    {{ $history->to_status }}
                                                </strong>

                                                <span style="display:block;font-size:9px;color:#87938b;margin-top:3px">
                                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                                </span>

                                                @if($history->note)
                                                    <span style="display:block;font-size:9px;color:#596a5f;margin-top:5px;white-space:normal">
                                                        {{ $history->note }}
                                                    </span>
                                                @endif

                                            </div>

                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- AKSI DETAIL --}}
                            <div class="bp-detail-footer">

                                @if($b->surat_path)
                                    <a class="bp-btn bp-view" href="{{ route('admin.booking.letter', $b) }}">
                                        Unduh Surat Permohonan (PDF)
                                    </a>
                                @endif

                                @if($b->status === 'Pending')
                                    <button
                                        type="button"
                                        class="bp-btn bp-approve"
                                        data-decision-id="{{ $b->id }}"
                                        data-decision-status="Disetujui"
                                        data-approved-conflicts="{{ $approvedConflicts }}"
                                        data-pending-conflicts="{{ $pendingConflicts }}"
                                    >
                                        Setujui
                                    </button>

                                    <button
                                        type="button"
                                        class="bp-btn bp-reject"
                                        data-decision-id="{{ $b->id }}"
                                        data-decision-status="Ditolak"
                                        data-approved-conflicts="0"
                                        data-pending-conflicts="0"
                                    >
                                        Tolak
                                    </button>
                                @endif

                            </div>

                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6">
                            <div class="bp-empty">
                                <div style="font-size:28px;margin-bottom:8px">▤</div>
                                <strong>Tidak ada pengajuan ditemukan</strong>
                                <div style="margin-top:5px">
                                    Coba ubah kata kunci atau filter pencarian.
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="bp-pagination">
            {{ $bookings->withQueryString()->links() }}
        </div>

    </div>

    {{-- MODAL KEPUTUSAN --}}
    <div class="bp-modal-backdrop" id="decisionModal" role="dialog" aria-modal="true" aria-labelledby="decisionTitle">

        <div class="bp-modal">

            <h3 id="decisionTitle">Konfirmasi Keputusan</h3>

            <p id="decisionDescription">
                Pastikan data pengajuan telah diperiksa sebelum keputusan disimpan.
            </p>

            <form
                method="POST"
                id="decisionForm"
                data-action-base="{{ url('/admin/pengajuan') }}"
            >
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" id="decisionStatus">

                <div id="normalNote">
                    <label for="adminNote">Catatan Admin (opsional)</label>
                    <textarea
                        name="admin_note"
                        id="adminNote"
                        placeholder="Tambahkan catatan untuk peminjam..."
                    ></textarea>
                </div>

                <div id="conflictReason" style="display:none">
                    <label for="conflictNote">
                        Alasan penolakan otomatis pengajuan yang bentrok (wajib)
                    </label>

                    <textarea
                        name="conflict_rejection_reason"
                        id="conflictNote"
                        placeholder="Jelaskan alasan prioritas atau keputusan BUK..."
                    ></textarea>

                    <p style="margin-top:6px">
                        Pengajuan lain yang menggunakan auditorium dan waktu yang beririsan
                        akan ditolak otomatis setelah pengajuan ini disetujui.
                        Pastikan pertimbangan prioritas sudah dilakukan.
                    </p>
                </div>

                <div class="bp-modal-actions">
                    <button
                        type="button"
                        class="bp-btn bp-cancel"
                        onclick="closeDecisionModal()"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="bp-btn bp-confirm"
                        id="decisionSubmit"
                    >
                        Simpan Keputusan
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
    function toggleBookingDetail(id) {
        const row = document.getElementById('booking-detail-' + id);

        if (row) {
            row.classList.toggle('open');
        }
    }

    function openDecisionModal(id, status, approvedConflicts, pendingConflicts) {
        const modal = document.getElementById('decisionModal');
        const form = document.getElementById('decisionForm');

        const isApprove = status === 'Disetujui';
        const slotLocked = approvedConflicts > 0;

        form.action = form.dataset.actionBase + '/' + id + '/keputusan';

        document.getElementById('decisionStatus').value = status;

        document.getElementById('decisionTitle').textContent = isApprove
            ? 'Setujui Pengajuan'
            : 'Tolak Pengajuan';

        document.getElementById('decisionDescription').textContent = !isApprove
            ? 'Pengajuan akan ditetapkan sebagai Ditolak. Tambahkan catatan agar peminjam memahami keputusan BUK.'
            : (
                slotLocked
                    ? 'Pengajuan tidak dapat disetujui karena terdapat peminjaman yang sudah disetujui pada waktu yang beririsan.'
                    : (
                        pendingConflicts > 0
                            ? 'Ada ' + pendingConflicts + ' pengajuan lain yang jadwalnya beririsan. Jika dilanjutkan, pengajuan lain tersebut akan ditolak otomatis.'
                            : 'Pengajuan akan ditetapkan sebagai Disetujui dan jadwal auditorium dikunci.'
                    )
            );

        const conflictReason = document.getElementById('conflictReason');
        const conflictNote = document.getElementById('conflictNote');
        const decisionSubmit = document.getElementById('decisionSubmit');

        document.getElementById('normalNote').style.display = 'block';

        conflictReason.style.display =
            isApprove && pendingConflicts > 0 && !slotLocked
                ? 'block'
                : 'none';

        conflictNote.required =
            isApprove && pendingConflicts > 0 && !slotLocked;

        decisionSubmit.disabled = slotLocked;
        decisionSubmit.style.opacity = slotLocked ? '.45' : '1';

        decisionSubmit.textContent = slotLocked
            ? 'Jadwal Sudah Terkunci'
            : 'Simpan Keputusan';

        document.getElementById('adminNote').value = '';
        conflictNote.value = '';

        modal.classList.add('show');
    }

    function closeDecisionModal() {
        document.getElementById('decisionModal').classList.remove('show');
    }

    // Event delegation untuk tombol Detail dan Keputusan
    document.addEventListener('click', function(event) {
        const detailButton = event.target.closest('[data-toggle-detail]');

        if (detailButton) {
            toggleBookingDetail(detailButton.dataset.toggleDetail);
            return;
        }

        const decisionButton = event.target.closest('[data-decision-id]');

        if (decisionButton) {
            openDecisionModal(
                Number(decisionButton.dataset.decisionId),
                decisionButton.dataset.decisionStatus,
                Number(decisionButton.dataset.approvedConflicts),
                Number(decisionButton.dataset.pendingConflicts)
            );
            return;
        }

        const modal = document.getElementById('decisionModal');

        if (event.target === modal) {
            closeDecisionModal();
        }
    });

    document.addEventListener('click', function(event) {
        const button = event.target.closest('[data-decision-id]');

        if (!button) return;

        openDecisionModal(
            Number(button.dataset.decisionId),
            button.dataset.decisionStatus,
            Number(button.dataset.approvedConflicts),
            Number(button.dataset.pendingConflicts)
        );
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDecisionModal();
        }
    });
</script>
@endsection
