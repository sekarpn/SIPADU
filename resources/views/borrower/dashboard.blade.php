
@extends('layouts.panel')

@section('panel')

@php
    $user = auth()->user();
@endphp

<style>
    .dashboard-page {
        width: 100%;
        box-sizing: border-box;
        padding-bottom: 30px;
    }

    /* HEADER */
    .dashboard-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .dashboard-welcome h1 {
        font-size: 27px;
        font-weight: 750;
        color: #183b2b;
        margin: 0 0 6px;
    }

    .dashboard-welcome p {
        font-size: 13px;
        color: #718078;
        margin: 0;
    }

    .dashboard-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 17px;
        border-radius: 8px;
        background: #21804d;
        color: white;
        text-decoration: none;
        font-size: 12px;
        font-weight: 700;
        transition: .2s;
        white-space: nowrap;
    }

    .dashboard-action:hover {
        background: #17683d;
        color: white;
        transform: translateY(-2px);
    }

    .dashboard-action svg {
        width: 17px;
        height: 17px;
    }

    /* STATISTIK */
    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e1eae3;
        border-radius: 12px;
        padding: 17px;
        box-shadow: 0 3px 12px rgba(20, 60, 35, .025);
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .stat-top small {
        color: #718078;
        font-size: 12px;
        font-weight: 500;
    }

    .stat-icon {
        width: 31px;
        height: 31px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
    }

    .stat-icon svg {
        width: 17px;
        height: 17px;
    }

    .stat-icon.blue {
        background: #eaf2f7;
        color: #47758c;
    }

    .stat-icon.amber {
        background: #fff4df;
        color: #d97706;
    }

    .stat-icon.green {
        background: #e5f5eb;
        color: #16804a;
    }

    .stat-icon.red {
        background: #fff0f0;
        color: #dc4545;
    }

    .stat-card strong {
        display: block;
        font-size: 25px;
        font-weight: 750;
        color: #183b2b;
        line-height: 1.2;
    }

    .stat-card p {
        margin: 6px 0 0;
        color: #849188;
        font-size: 11px;
    }

    /* PANEL TENGAH */
    .dashboard-middle {
        display: grid;
        grid-template-columns: minmax(0, 1.6fr) minmax(260px, 1fr);
        gap: 15px;
        margin-bottom: 22px;
        align-items: stretch;
    }

    .dashboard-panel {
        background: white;
        border: 1px solid #e1eae3;
        border-radius: 12px;
        padding: 16px 18px;
        min-width: 0;
        box-shadow: 0 3px 12px rgba(20, 60, 35, .025);
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 13px;
    }

    .section-head h2 {
        font-size: 16px;
        font-weight: 750;
        color: #244332;
        margin: 0 0 4px;
    }

    .section-head p {
        font-size: 11px;
        color: #849188;
        margin: 0;
    }

    .section-head a {
        color: #21804d;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .section-head a:hover {
        text-decoration: underline;
        color: #17683d;
    }

    /* TABEL PENGAJUAN */
    .dashboard-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        min-width: 490px;
    }

    .dashboard-table th {
        color: #718078;
        font-size: 11px;
        font-weight: 650;
        text-align: left;
        padding: 10px 8px;
        border-bottom: 1px solid #e8eee9;
    }

    .dashboard-table td {
        color: #405449;
        padding: 13px 8px;
        border-bottom: 1px solid #edf1ee;
    }

    .dashboard-table tr:last-child td {
        border-bottom: none;
    }

    .dashboard-table .empty {
        text-align: center;
        color: #849188;
        padding: 25px 10px;
    }

    .status {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status.pending {
        color: #a96808;
        background: #fff3dc;
    }

    .status.disetujui {
        color: #187447;
        background: #e5f5eb;
    }

    .status.ditolak {
        color: #c23b3b;
        background: #fff0f0;
    }

    .status.dibatalkan {
        color: #64748b;
        background: #f1f5f9;
    }

    /* INFORMASI SIPADU */
    .guide-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-top: 15px;
    }

    .guide-item {
        display: flex;
        align-items: flex-start;
        gap: 11px;
    }

    .guide-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #e9f5ed;
        color: #21804d;
    }

    .guide-icon svg {
        width: 16px;
        height: 16px;
    }

    .guide-item strong {
        display: block;
        font-size: 12px;
        color: #304638;
        margin-bottom: 4px;
    }

    .guide-item p {
        font-size: 11px;
        color: #849188;
        line-height: 1.5;
        margin: 0;
    }

    /* AUDITORIUM */
    .auditorium-section {
        margin-top: 5px;
    }

    .auditorium-cards {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .auditorium-card {
        background: #fff;
        border: 1px solid #e1eae3;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0;
        box-shadow: 0 3px 12px rgba(20, 60, 35, .025);
        display: flex;
        flex-direction: column;
        cursor: pointer;

        transition:
            transform .25s ease,
            box-shadow .25s ease,
            border-color .25s ease;
    }

    /* HOVER SELURUH KARTU */
    .auditorium-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 28px rgba(20, 80, 45, .15);
        border-color: #21804d;
    }

    .auditorium-card:focus-visible {
        outline: 2px solid #21804d;
        outline-offset: 3px;
    }

    .auditorium-image-wrap {
        overflow: hidden;
        position: relative;
    }

    .auditorium-card img {
        width: 100%;
        height: 145px;
        object-fit: cover;
        display: block;
        transition: transform .3s ease;
    }

    .auditorium-card:hover img {
        transform: scale(1.04);
    }

    .auditorium-no-image {
        width: 100%;
        height: 145px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #eaf5ed, #f4f8f5);
        color: #21804d;
    }

    .auditorium-no-image svg {
        width: 30px;
        height: 30px;
    }

    .auditorium-card-content {
        padding: 14px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .auditorium-card-content h3 {
        margin: 0 0 9px;
        color: #244332;
        font-size: 14px;
        font-weight: 750;
        line-height: 1.4;
    }

    .auditorium-description {
        color: #718078;
        font-size: 11px;
        line-height: 1.6;
        margin: 0 0 10px;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 35px;
    }

    .auditorium-meta {
        display: flex;
        flex-direction: column;
        gap: 7px;
        margin-bottom: 12px;
    }

    .auditorium-meta span {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        font-size: 10px;
        color: #849188;
    }

    .auditorium-meta svg {
        width: 13px;
        height: 13px;
        flex-shrink: 0;
    }

    .auditorium-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 7px;
        margin-top: auto;
    }

    .auditorium-actions a {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none;
        padding: 9px 6px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
        transition: .2s;
        position: relative;
        z-index: 2;
    }

    .btn-booking {
        color: white;
        background: #21804d;
        border: 1px solid #21804d;
    }

    .btn-booking:hover {
        background: #17683d;
        color: white;
    }

    .empty-auditorium {
        background: white;
        border: 1px solid #e1eae3;
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        color: #849188;
        font-size: 13px;
        grid-column: 1 / -1;
    }

    /* MODAL DETAIL AUDITORIUM */
    .auditorium-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(12, 30, 20, .62);
        backdrop-filter: blur(3px);
    }

    .auditorium-modal.show {
        display: flex;
    }

    .auditorium-modal-box {
        position: relative;
        width: 100%;
        max-width: 650px;
        max-height: 90vh;
        overflow-y: auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .2);
        animation: modalAppear .22s ease;
    }

    @keyframes modalAppear {
        from {
            opacity: 0;
            transform: translateY(15px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-close {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, .95);
        color: #244332;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
    }

    .modal-close:hover {
        background: #e9f5ed;
    }

    .modal-close svg {
        width: 18px;
        height: 18px;
    }

    .modal-image {
        width: 100%;
        height: 260px;
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
        color: #21804d;
    }

    .modal-no-image svg {
        width: 48px;
        height: 48px;
    }

    .modal-content {
        padding: 22px;
    }

    .modal-content h2 {
        color: #183b2b;
        font-size: 21px;
        font-weight: 750;
        margin: 0 0 12px;
    }

    .modal-description {
        font-size: 13px;
        color: #718078;
        line-height: 1.7;
        margin-bottom: 20px;
        white-space: pre-line;
    }

    .modal-info-title {
        color: #244332;
        font-size: 13px;
        font-weight: 750;
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
        font-size: 12px;
        color: #52665a;
        line-height: 1.6;
    }

    .modal-info-item svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: #21804d;
        margin-top: 1px;
    }

    .modal-facilities {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .facility-tag {
        padding: 7px 10px;
        border-radius: 7px;
        background: #eaf5ed;
        color: #21804d;
        font-size: 11px;
        font-weight: 600;
    }

    .modal-actions {
        margin-top: 22px;
        display: flex;
        justify-content: flex-end;
    }

    .modal-actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        background: #21804d;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        padding: 11px 18px;
        font-size: 12px;
        font-weight: 700;
    }

    .modal-actions a:hover {
        background: #17683d;
        color: white;
    }

    body.modal-open {
        overflow: hidden;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .auditorium-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 900px) {
        .dashboard-middle {
            grid-template-columns: 1fr;
        }

        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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

        .auditorium-cards {
            grid-template-columns: 1fr;
        }

        .dashboard-panel {
            padding: 14px;
        }

        .modal-image {
            height: 190px;
        }

        .modal-content {
            padding: 17px;
        }
    }

/* FIX POSISI KONTEN PANEL DASHBOARD */
.dashboard-page .dashboard-middle > .dashboard-panel {
    display: flex !important;
    flex-direction: column !important;
    justify-content: flex-start !important;
    align-items: stretch !important;
    padding-top: 15px !important;
}

.dashboard-page .dashboard-middle > .dashboard-panel > .section-head {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

</style>

<div class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-topbar">
        <div class="dashboard-welcome">
            <h1>Selamat datang, {{ $user->name }}!</h1>
            <p>Temukan auditorium dan pantau pengajuan peminjaman Anda.</p>
        </div>

        <a class="dashboard-action" href="{{ route('borrower.booking.create') }}">
            <i data-lucide="plus"></i>
            Ajukan Peminjaman
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
                    <i data-lucide="check-circle"></i>
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
            <p>Lihat detail riwayat</p>
        </div>
    </div>

    {{-- PENGAJUAN DAN INFORMASI --}}
    <div class="dashboard-middle">

        {{-- PENGAJUAN TERBARU --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <h2>Pengajuan Terbaru</h2>
                    <p>Ringkasan pengajuan peminjaman Anda</p>
                </div>

                <a href="{{ route('borrower.bookings') }}">
                    Lihat semua →
                </a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Auditorium</th>
                            <th>Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookings as $b)
                            <tr>
                                <td>{{ $b->auditorium->name ?? '-' }}</td>
                                <td>{{ $b->event_name }}</td>
                                <td>
                                    {{ $b->booking_date
                                        ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y')
                                        : '-' }}
                                </td>
                                <td>
                                    <span class="status {{ strtolower($b->status) }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    Belum ada pengajuan peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFORMASI SIPADU --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <h2>Informasi SIPADU</h2>
                    <p>Panduan singkat peminjaman</p>
                </div>

                {{-- INFORMASI LEBIH LANJUT DI ATAS --}}
                <a href="{{ route('borrower.guides') }}">
                    Informasi lebih lanjut →
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
                        <p>Periksa perkembangan pengajuan melalui menu Peminjaman Saya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR AUDITORIUM --}}
    <div class="auditorium-section">
        <div class="section-head">
            <div>
                <h2>Pilihan Auditorium</h2>
                <p>Daftar auditorium yang tercatat pada SIPADU</p>
            </div>
        </div>

        <div class="auditorium-cards">
            @forelse($auditoriums as $a)

                @php
                    $description = is_string($a->description)
                        ? $a->description
                        : 'Informasi auditorium belum tersedia.';

                    $location = is_string($a->location)
                        ? $a->location
                        : 'Lokasi belum tersedia';

                    $capacity = is_scalar($a->capacity)
                        ? $a->capacity
                        : 'Belum tersedia';

                    $facilities = is_array($a->facilities)
                        ? $a->facilities
                        : [];

                    $facilities = array_filter($facilities, function ($item) {
                        return is_scalar($item) && trim((string) $item) !== '';
                    });

                    $shortDescription = \Illuminate\Support\Str::limit(
                        $description,
                        90
                    );
                @endphp

                {{-- KARTU AUDITORIUM: DIKLIK LANGSUNG BUKA MODAL --}}
                <article
                    class="auditorium-card"
                    onclick="openAuditoriumModal('auditorium-modal-{{ $a->id }}')"
                    role="button"
                    tabindex="0"
                    aria-label="Lihat detail {{ $a->name }}"
                    onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openAuditoriumModal('auditorium-modal-{{ $a->id }}'); }"
                >

                    <div class="auditorium-image-wrap">
                        @if($a->image_path)
                            <img
                                src="{{ asset('storage/' . $a->image_path) }}"
                                alt="Foto {{ $a->name }}"
                                loading="lazy"
                            >
                        @else
                            <div class="auditorium-no-image">
                                <i data-lucide="building-2"></i>
                            </div>
                        @endif
                    </div>

                    <div class="auditorium-card-content">
                        <h3>{{ $a->name }}</h3>

                        <p class="auditorium-description">
                            {{ $shortDescription }}
                        </p>

                        <div class="auditorium-meta">
                            <span>
                                <i data-lucide="map-pin"></i>
                                {{ $location }}
                            </span>

                            <span>
                                <i data-lucide="users"></i>
                                Kapasitas: {{ $capacity }}
                            </span>
                        </div>

                        {{-- TIDAK ADA TOMBOL LIHAT DETAIL LAGI --}}
                        {{-- SELURUH KARTU BISA DIKLIK --}}

                        <div class="auditorium-actions">
                            <a
                                href="{{ route('borrower.booking.create', ['auditorium' => $a->id]) }}"
                                class="btn-booking"
                                onclick="event.stopPropagation()"
                            >
                                Ajukan Peminjaman
                            </a>
                        </div>
                    </div>
                </article>

                {{-- MODAL DETAIL LENGKAP --}}
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

                        {{-- FOTO DETAIL --}}
                        @if($a->image_path)
                            <img
                                class="modal-image"
                                src="{{ asset('storage/' . $a->image_path) }}"
                                alt="Foto {{ $a->name }}"
                            >
                        @else
                            <div class="modal-no-image">
                                <i data-lucide="building-2"></i>
                            </div>
                        @endif

                        <div class="modal-content">
                            <h2>{{ $a->name }}</h2>

                            {{-- DESKRIPSI LENGKAP --}}
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
                                        <strong>Lokasi</strong><br>
                                        {{ $location }}
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

                            {{-- FASILITAS --}}
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

                            {{-- TOMBOL PENGAJUAN --}}
                            <div class="modal-actions">
                                <a
                                    href="{{ route('borrower.booking.create', ['auditorium' => $a->id]) }}"
                                >
                                    <i data-lucide="calendar-plus"></i>
                                    Ajukan Peminjaman
                                </a>
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

{{-- JAVASCRIPT MODAL --}}
<script>
    function openAuditoriumModal(modalId) {
        const modal = document.getElementById(modalId);

        if (!modal) return;

        modal.classList.add('show');
        document.body.classList.add('modal-open');

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeAuditoriumModal(modalId) {
        const modal = document.getElementById(modalId);

        if (!modal) return;

        modal.classList.remove('show');

        if (!document.querySelector('.auditorium-modal.show')) {
            document.body.classList.remove('modal-open');
        }
    }

    // Tutup modal dengan tombol ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.auditorium-modal.show')
                .forEach(function(modal) {
                    modal.classList.remove('show');
                });

            document.body.classList.remove('modal-open');
        }
    });

    // Inisialisasi ikon
    if (window.lucide) {
        window.lucide.createIcons();
    }
</script>

@endsection
