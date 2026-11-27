@extends('layouts.panel')

@section('panel')

<style>
    .booking-page-wrapper {
        --bp-ink: #163626;
        --bp-muted: #4c6456;
        --bp-subtle: #64786c;
        --bp-green: #237c4d;
        --bp-green-dark: #19613b;
        --bp-line: rgba(22, 54, 38, 0.10);
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        color: var(--bp-ink);
    }

    /* HEADER */
    .bp-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin: 0 0 20px;
    }

    .bp-page-head h1 {
        margin: 0;
        font-size: 25px;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: var(--bp-ink);
    }

    .bp-page-head p {
        margin: 5px 0 0;
        font-size: 13px;
        color: var(--bp-muted);
    }

    /* LAYOUT 2 KOLOM */
    .booking-layout {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: flex-start;
        gap: 0px;
        width: 100%;
        box-sizing: border-box;
        transition: gap 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .booking-layout.has-sidebar {
        gap: 22px;
    }

    /* KOLOM KIRI: FORMULIR GLASS */
    .booking-form-col {
        flex: 1 1 auto;
        min-width: 0;
        width: 100%;
        margin: 0;
        padding: 26px 28px;
        border-radius: 18px;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.78) 0%, rgba(255, 255, 255, 0.56) 100%);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, 0.65);
        box-shadow: 0 10px 30px rgba(22, 54, 38, 0.06);
        box-sizing: border-box;
        transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    /* SECTION HEADER DI DALAM FORM */
    .form-section {
        padding-bottom: 22px;
        margin-bottom: 22px;
        border-bottom: 1px solid var(--bp-line);
    }

    .form-section:last-of-type {
        padding-bottom: 0;
        margin-bottom: 18px;
        border-bottom: none;
    }

    .section-title-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .section-step-badge {
        width: 26px;
        height: 26px;
        border-radius: 8px;
        background: rgba(35, 124, 77, 0.14);
        color: var(--bp-green);
        font-size: 12px;
        font-weight: 800;
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .section-title-row h3 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 800;
        color: var(--bp-ink);
        letter-spacing: -0.015em;
    }

    /* GRID INPUT 2 KOLOM */
    .form-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 16px;
    }

    .form-group {
        margin-bottom: 14px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .booking-form label {
        display: block;
        margin: 0 0 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--bp-ink);
    }

    .booking-form input[type="text"],
    .booking-form input[type="date"],
    .booking-form input:not([type]),
    .booking-form select,
    .booking-form textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid rgba(22, 54, 38, 0.15);
        border-radius: 11px;
        background: rgba(255, 255, 255, 0.92);
        color: var(--bp-ink);
        font-size: 13px;
        font-weight: 500;
        box-sizing: border-box;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .booking-form input:focus,
    .booking-form select:focus,
    .booking-form textarea:focus {
        outline: none;
        background: #ffffff;
        border-color: var(--bp-green);
        box-shadow: 0 0 0 3px rgba(35, 124, 77, 0.14);
    }

    .booking-form select#auditoriumSelect {
        appearance: none;
        -webkit-appearance: none;
        padding-right: 42px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23163626' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        background-size: 16px 16px;
        cursor: pointer;
    }

    /* KARTU DATA PEMOHON (COMPACT GRID) */
    .applicant-card {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 18px;
        padding: 15px 18px;
        background: rgba(242, 249, 245, 0.85);
        border: 1px solid rgba(35, 124, 77, 0.16);
        border-radius: 13px;
    }

    .applicant-item small {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: var(--bp-subtle);
        margin-bottom: 2px;
    }

    .applicant-item strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--bp-ink);
    }

    /* PILIHAN SESI */
    .session-help {
        color: var(--bp-subtle);
        font-size: 12px;
        margin: -2px 0 10px;
    }

    .session-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .session-card {
        position: relative;
        min-width: 0;
        border: 1.5px solid rgba(22, 54, 38, 0.14);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.92);
        padding: 15px 10px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
        color: var(--bp-ink);
        appearance: none;
        -webkit-appearance: none;
    }

    .session-card:hover {
        border-color: var(--bp-green);
        background: #f2fbf6;
        transform: translateY(-1px);
    }

    .session-card.active,
    .session-card.active:hover {
        border: 2px solid var(--bp-green);
        background: #dcfce7;
        color: #14532d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, 0.14);
    }

    .session-card strong {
        display: block;
        font-size: 13.5px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .session-card span {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--bp-muted);
    }

    .session-card.active strong,
    .session-card.active span {
        color: #14532d;
    }

    .session-card .session-check {
        display: none;
        position: absolute;
        top: 7px;
        right: 9px;
        font-size: 12px;
        font-weight: 800;
        color: #15803d;
    }

    .session-card.active .session-check {
        display: block;
    }

    /* UPLOAD SURAT & TEMPLATE */
    .letter-upload {
        padding: 16px 18px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(22, 54, 38, 0.12);
        border-radius: 13px;
    }

    .template-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 14px;
        margin-bottom: 14px;
        background: #eef9f2;
        border: 1px solid #bfe7ce;
        border-radius: 10px;
        flex-wrap: wrap;
    }

    .template-box-text strong {
        display: block;
        font-size: 13px;
        font-weight: 750;
        color: #14532d;
        margin-bottom: 2px;
    }

    .template-box-text span {
        display: block;
        font-size: 12px;
        color: #1f6b43;
    }

    .btn-template {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--bp-green);
        color: #ffffff !important;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 9px;
        white-space: nowrap;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .btn-template:hover {
        background: var(--bp-green-dark);
        transform: translateY(-1px);
    }

    .letter-upload input[type="file"] {
        display: block;
        width: 100%;
        padding: 9px 11px;
        border: 1px dashed rgba(35, 124, 77, 0.4);
        border-radius: 10px;
        background: #ffffff;
        font-size: 12.5px;
        box-sizing: border-box;
        cursor: pointer;
    }

    .letter-upload small {
        display: block;
        color: var(--bp-subtle);
        font-size: 11.5px;
        margin-top: 6px;
    }

    .submit-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid var(--bp-line);
    }

    .submit-note {
        flex: 1 1 260px;
        min-width: 0;
        margin: 0;
        font-size: 12px;
        color: var(--bp-muted);
        line-height: 1.5;
    }

    .btn-submit-booking {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 22px;
        border-radius: 11px;
        border: none;
        background: var(--bp-green);
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(35, 124, 77, 0.25);
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .btn-submit-booking:hover {
        background: var(--bp-green-dark);
        transform: translateY(-1px);
    }

    /* KOLOM KANAN: PREVIEW FOTO AUDITORIUM & RINGKASAN LIVE */
    .booking-summary-col {
        flex: 0 0 0px;
        width: 0px;
        max-width: 0px;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateX(16px);
        position: sticky;
        top: 90px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        box-sizing: border-box;
        transition:
            flex-basis 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            width 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            max-width 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.3s ease 0.08s,
            transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .booking-summary-col.is-visible {
        flex: 0 0 365px;
        width: 365px;
        max-width: 38%;
        opacity: 1;
        overflow: visible;
        pointer-events: auto;
        transform: translateX(0);
    }

    .summary-sidebar-inner {
        min-width: 320px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* KARTU FOTO & DETAIL AUDITORIUM */
    .auditorium-preview {
        width: 100%;
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(255, 255, 255, 0.7);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 26px rgba(22, 54, 38, 0.08);
        box-sizing: border-box;
        display: none;
    }

    .preview-media {
        position: relative;
        width: 100%;
        height: 195px;
        background: linear-gradient(135deg, #d8ede1 0%, #b7dec8 100%);
        overflow: hidden;
    }

    .preview-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.35s ease;
    }

    .auditorium-preview:hover .preview-media img {
        transform: scale(1.03);
    }

    .preview-media-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #1f6b43;
        font-size: 12px;
        font-weight: 700;
    }

    .preview-media-overlay {
        position: absolute;
        inset: auto 0 0 0;
        padding: 28px 16px 12px;
        background: linear-gradient(to top, rgba(13, 35, 24, 0.84) 0%, rgba(13, 35, 24, 0) 100%);
        color: #ffffff;
    }

    .preview-media-overlay h4 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.3;
    }

    .preview-media-overlay span {
        display: block;
        margin-top: 2px;
        font-size: 11.5px;
        color: rgba(255, 255, 255, 0.88);
    }

    .preview-info {
        padding: 14px 16px 16px;
    }

    .preview-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding-bottom: 10px;
        margin-bottom: 10px;
        border-bottom: 1px dashed rgba(22, 54, 38, 0.12);
        font-size: 12.5px;
    }

    .preview-meta-row span {
        color: var(--bp-subtle);
        font-weight: 600;
    }

    .preview-meta-row strong {
        color: var(--bp-green);
        font-weight: 800;
        background: #eaf7ef;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
    }

    .preview-facilities-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--bp-subtle);
        margin-bottom: 7px;
    }

    .preview-facility-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .preview-facility-tag {
        padding: 4px 9px;
        border-radius: 7px;
        background: #f1f6f3;
        color: #294735;
        font-size: 11.5px;
        font-weight: 600;
    }

    /* KARTU RINGKASAN PEMINJAMAN LIVE */
    .summary-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(35, 124, 77, 0.22);
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.06);
        display: none;
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 11px;
        margin-bottom: 12px;
        border-bottom: 1px solid rgba(22, 54, 38, 0.09);
    }

    .summary-card-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 800;
        color: var(--bp-ink);
    }

    .summary-badge {
        background: #dcfce7;
        color: #15803d;
        font-size: 10.5px;
        font-weight: 800;
        padding: 3px 9px;
        border-radius: 999px;
    }

    .summary-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        font-size: 13px;
    }

    .summary-item {
        display: none;
        flex-direction: column;
        gap: 2px;
        padding-bottom: 8px;
        border-bottom: 1px dashed rgba(22, 54, 38, 0.12);
    }

    .summary-label {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--bp-subtle);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .summary-value {
        font-weight: 700;
        color: var(--bp-ink);
        word-break: break-word;
    }

    @media (max-width: 980px) {
        .booking-layout {
            flex-direction: column;
            flex-wrap: wrap;
            gap: 20px;
        }

        .booking-summary-col {
            display: none;
        }

        .booking-summary-col.is-visible {
            display: flex;
            flex: 1 1 100%;
            width: 100%;
            max-width: 100%;
            position: static;
            transform: none;
        }

        .summary-sidebar-inner {
            min-width: 0;
            width: 100%;
        }
    }

    @media (max-width: 640px) {
        .booking-form-col {
            padding: 20px 18px;
        }

        .form-grid-2,
        .applicant-card,
        .session-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="booking-page-wrapper">
    <div class="bp-page-head">
        <div>
            <h1>Ajukan Peminjaman</h1>
            <p>Isi informasi kegiatan dan jadwal auditorium yang dibutuhkan.</p>
        </div>
    </div>

    <div class="booking-layout" id="bookingLayout">

        {{-- KOLOM KIRI: FORM PENGAJUAN --}}
        <form class="booking-form booking-form-col"
              method="POST"
              action="{{ route('borrower.booking.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- BAGIAN 1: INFORMASI PEMINJAMAN --}}
            <div class="form-section">
                <div class="section-title-row">
                    <span class="section-step-badge">1</span>
                    <h3>Informasi Peminjaman</h3>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="auditoriumSelect">Pilih Auditorium *</label>
                        <select name="auditorium_id" id="auditoriumSelect" required>
                            <option value="">-- Pilih auditorium --</option>
                            @foreach($auditoriums as $a)
                                @php
                                    $rawImg = trim((string) ($a->image_path ?? ''));
                                    $cleanImg = ltrim(preg_replace('#^(public/|storage/)#', '', str_replace('\\', '/', $rawImg)), '/');
                                    $imgUrl = $cleanImg !== ''
                                        ? (\Illuminate\Support\Str::startsWith($rawImg, ['http://', 'https://', 'data:']) ? $rawImg : asset('storage/' . $cleanImg))
                                        : '';
                                @endphp
                                <option
                                    value="{{ $a->id }}"
                                    data-name="{{ $a->name }}"
                                    data-image="{{ $imgUrl }}"
                                    data-location="{{ $a->location ?: 'Lokasi belum ditentukan' }}"
                                    data-capacity="{{ $a->capacity ?: 'Belum ditentukan' }}"
                                    data-facilities="{{ implode('|', $a->facilities ?? []) }}"
                                    @selected((string) old('auditorium_id', $selected) === (string) $a->id)
                                >
                                    {{ $a->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('auditorium_id')
                            <p class="notice">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="bookingDateInput">Tanggal Peminjaman *</label>
                        <input
                            type="date"
                            name="booking_date"
                            id="bookingDateInput"
                            min="{{ \Carbon\Carbon::now('Asia/Jakarta')->addDays(7)->toDateString() }}"
                            value="{{ old('booking_date') }}"
                            required
                        >
                        @error('booking_date')
                            <p class="notice">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <p id="bookingDateWarning" class="notice" style="display:none;margin-top:8px;" role="alert">
                    Tanggal acara harus minimal 7 hari dari tanggal pengajuan. Silakan pilih tanggal yang memenuhi batas H-7.
                </p>

                <div class="form-group" style="margin-top:14px;">
                    <label>Pilih Sesi Peminjaman *</label>
                    <p class="session-help">Pilih salah satu sesi waktu yang tersedia (maksimal H-7 sebelum acara).</p>

                    <div class="session-grid">
                        @foreach (config('booking_sessions.sessions') as $session)
                            <button type="button" class="session-card" data-label="{{ $session['label'] }}" data-start="{{ $session['start'] }}" data-end="{{ $session['end'] }}">
                                <span class="session-check">✓</span>
                                <strong>{{ $session['label'] }}</strong>
                                <span>{{ $session['start'] }} - {{ $session['end'] }}</span>
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
                    <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time') }}">

                    <p id="sessionError" class="notice" style="display:none;margin-top:8px;">
                        Silakan pilih salah satu sesi peminjaman.
                    </p>

                    @error('start_time') <p class="notice">{{ $message }}</p> @enderror
                    @error('end_time') <p class="notice">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- BAGIAN 2: DATA PEMOHON --}}
            <div class="form-section">
                <div class="section-title-row">
                    <span class="section-step-badge">2</span>
                    <h3>Data Pemohon</h3>
                </div>

                <div class="applicant-card">
                    <div class="applicant-item">
                        <small>Nama Lengkap</small>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                    <div class="applicant-item">
                        <small>Jenis Akun &amp; {{ auth()->user()->role === 'pegawai' ? 'NIP' : 'NIM' }}</small>
                        <strong>{{ auth()->user()->role === 'pegawai' ? 'Dosen & Tendik' : 'Mahasiswa' }} · {{ auth()->user()->nim_nip }}</strong>
                    </div>
                    @if(auth()->user()->role === 'pegawai')
                        <div class="applicant-item">
                            <small>Unit / Biro</small>
                            <strong>{{ auth()->user()->employee_unit ?: 'Belum diisi pada profil' }}</strong>
                        </div>
                        <div class="applicant-item">
                            <small>Jabatan</small>
                            <strong>{{ auth()->user()->position ?: 'Belum diisi pada profil' }}</strong>
                        </div>
                    @else
                        <div class="applicant-item">
                            <small>Fakultas</small>
                            <strong>{{ auth()->user()->faculty ?: 'Belum diisi pada profil' }}</strong>
                        </div>
                        <div class="applicant-item">
                            <small>Program Studi</small>
                            <strong>{{ auth()->user()->study_program ?: 'Belum diisi pada profil' }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            {{-- BAGIAN 3: INFORMASI KEGIATAN --}}
            <div class="form-section">
                <div class="section-title-row">
                    <span class="section-step-badge">3</span>
                    <h3>Informasi Kegiatan</h3>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="eventNameInput">Nama Kegiatan *</label>
                        <input name="event_name" id="eventNameInput" value="{{ old('event_name') }}" placeholder="Contoh: Seminar Nasional Teknologi" required>
                    </div>

                    <div class="form-group">
                        <label for="organizationInput">Organisasi / Unit *</label>
                        <input name="organization" id="organizationInput" value="{{ old('organization') }}" placeholder="Himpunan, BEM, UKM, KSM, dll." required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="eventDescriptionInput">Deskripsi Kegiatan</label>
                    <textarea name="event_description" id="eventDescriptionInput" rows="3" placeholder="Jelaskan secara singkat tujuan dan rangkaian kegiatan...">{{ old('event_description') }}</textarea>
                </div>
            </div>

            {{-- BAGIAN 4: KONTAK PJ & DOKUMEN --}}
            <div class="form-section">
                <div class="section-title-row">
                    <span class="section-step-badge">4</span>
                    <h3>Penanggung Jawab &amp; Dokumen</h3>
                </div>

                <div class="form-grid-2" style="margin-bottom:16px;">
                    <div class="form-group">
                        <label for="picNameInput">Nama Penanggung Jawab *</label>
                        <input name="pic_name" id="picNameInput" value="{{ old('pic_name', auth()->user()->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="picPhoneInput">Nomor WhatsApp / Telepon *</label>
                        <input name="pic_phone" id="picPhoneInput" value="{{ old('pic_phone') }}" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <div class="letter-upload">
                    <div class="template-box">
                        <div class="template-box-text">
                            <strong>Belum punya format Surat Peminjaman?</strong>
                            <span>Unduh template resmi UPNVJ, isi detail acara, dan mintakan tanda tangan sebelum diunggah.</span>
                        </div>
                        <a href="{{ route('borrower.booking.template') }}" class="btn-template">
                            ↓ Download Template Surat
                        </a>
                    </div>

                    <label for="surat">Upload Surat Peminjaman (PDF) *</label>
                    <input type="file" name="surat" id="surat" accept=".pdf,application/pdf" required>
                    <small>Format file PDF dengan ukuran maksimal 5 MB.</small>

                    @error('surat')
                        <p class="notice">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="submit-footer">
                <p class="submit-note">
                    Pengajuan berstatus <strong>Pending</strong> hingga diverifikasi Admin. Jadwal dianggap bentrok jika sudah ada peminjaman lain yang <strong>Disetujui</strong> pada waktu yang sama.
                </p>
                <button type="submit" class="btn-submit-booking">Kirim Pengajuan</button>
            </div>
        </form>

        {{-- KOLOM KANAN: PREVIEW FOTO AUDITORIUM & RINGKASAN LIVE --}}
        <aside class="booking-summary-col" id="summarySidebar">
            <div class="summary-sidebar-inner">

                {{-- KARTU 1: FOTO & DETAIL AUDITORIUM DIPILIH --}}
                <div class="auditorium-preview" id="auditoriumPreviewCard">
                    <div class="preview-media">
                        <img id="previewImage" src="" alt="Foto auditorium" style="display:none;">
                        <div class="preview-media-fallback" id="previewFallback" style="display:none;">
                            <i data-lucide="building-2" style="width:28px;height:28px;"></i>
                            <span>Foto auditorium belum tersedia</span>
                        </div>
                        <div class="preview-media-overlay">
                            <h4 id="previewName"></h4>
                            <span id="previewLocation"></span>
                        </div>
                    </div>

                    <div class="preview-info">
                        <div class="preview-meta-row">
                            <span>Kapasitas Ruangan</span>
                            <strong id="previewCapacity"></strong>
                        </div>
                        <span class="preview-facilities-label">Fasilitas Tersedia</span>
                        <div class="preview-facility-tags" id="previewFacilities"></div>
                    </div>
                </div>

                {{-- KARTU 2: RINGKASAN PEMINJAMAN (LIVE) --}}
                <div class="summary-card" id="summaryCard">
                    <div class="summary-card-header">
                        <h3>Ringkasan Peminjaman</h3>
                        <span class="summary-badge">Draft</span>
                    </div>

                    <div class="summary-list">
                        <div class="summary-item" id="rowAuditorium">
                            <span class="summary-label">Auditorium</span>
                            <span class="summary-value" id="sumAuditorium"></span>
                        </div>
                        <div class="summary-item" id="rowDateTime">
                            <span class="summary-label">Tanggal &amp; Sesi</span>
                            <span class="summary-value" id="sumDateTime"></span>
                        </div>
                        <div class="summary-item" id="rowEventName">
                            <span class="summary-label">Nama Kegiatan</span>
                            <span class="summary-value" id="sumEventName"></span>
                        </div>
                        <div class="summary-item" id="rowOrganization">
                            <span class="summary-label">Organisasi / Unit</span>
                            <span class="summary-value" id="sumOrganization"></span>
                        </div>
                        <div class="summary-item" id="rowPic">
                            <span class="summary-label">Penanggung Jawab</span>
                            <span class="summary-value" id="sumPic"></span>
                        </div>
                        <div class="summary-item" id="rowFile">
                            <span class="summary-label">Berkas Surat (PDF)</span>
                            <span class="summary-value" id="sumFile"></span>
                        </div>
                    </div>
                </div>

            </div>
        </aside>

    </div>

    @if(!$profileComplete)
        <div class="profile-required-overlay" role="dialog" aria-modal="true" aria-labelledby="profileRequiredTitle">
            <div class="profile-required-modal">
                <div class="profile-required-icon">
                    <i data-lucide="user-round-check"></i>
                </div>
                <h2 id="profileRequiredTitle">Lengkapi Profil Terlebih Dahulu</h2>
                <p>
                    Sebelum mengajukan peminjaman auditorium, lengkapi data identitas pada profil akun Anda terlebih dahulu.
                </p>
                <a href="{{ route('borrower.profile') }}" class="profile-required-button">
                    Lengkapi Profil
                </a>
            </div>
        </div>

        <style>
            html.profile-modal-open,
            body.profile-modal-open {
                overflow: hidden !important;
                overscroll-behavior: none !important;
            }

            .profile-required-overlay {
                position: fixed;
                inset: 0;
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
                box-sizing: border-box;
                background: rgba(15, 23, 42, .58);
                -webkit-backdrop-filter: blur(3px);
                backdrop-filter: blur(3px);
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity .28s ease, visibility .28s ease;
            }

            .profile-required-overlay.is-visible {
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }

            .profile-required-modal {
                width: min(100%, 430px);
                padding: 32px 30px;
                box-sizing: border-box;
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                background: #fff;
                box-shadow: 0 24px 70px rgba(15, 23, 42, .20);
                text-align: center;
                opacity: 0;
                transform: translate3d(0, 12px, 0) scale(.985);
                transition: opacity .28s ease, transform .36s cubic-bezier(.2, .75, .25, 1);
            }

            .profile-required-overlay.is-visible .profile-required-modal {
                opacity: 1;
                transform: translate3d(0, 0, 0) scale(1);
            }

            .profile-required-icon {
                display: grid;
                place-items: center;
                width: 58px;
                height: 58px;
                margin: 0 auto 17px;
                border-radius: 50%;
                background: #eaf5ee;
                color: #16834a;
            }

            .profile-required-icon svg {
                width: 27px;
                height: 27px;
            }

            .profile-required-modal h2 {
                margin: 0 0 10px;
                color: #172b20;
                font-size: 21px;
                font-weight: 750;
                line-height: 1.35;
            }

            .profile-required-modal p {
                margin: 0 0 23px;
                color: #64736a;
                font-size: 13px;
                line-height: 1.7;
            }

            .profile-required-button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 100%;
                min-height: 44px;
                padding: 10px 18px;
                box-sizing: border-box;
                border: 1px solid #11643a;
                border-radius: 9px;
                background: #16834a;
                color: #fff !important;
                font-size: 13px;
                font-weight: 700;
                text-decoration: none !important;
                transition: background .2s ease, transform .2s ease;
            }

            .profile-required-button:hover {
                background: #11643a;
                transform: translateY(-1px);
            }
        </style>

        <script>
            (function () {
                document.documentElement.classList.add('profile-modal-open');
                document.body.classList.add('profile-modal-open');
                const overlay = document.querySelector('.profile-required-overlay');
                if (overlay) {
                    requestAnimationFrame(() => requestAnimationFrame(() => overlay.classList.add('is-visible')));
                }
            })();
        </script>
    @endif
</div>

<script>
    const bookingLayout = document.getElementById('bookingLayout');
    const auditoriumSelect = document.getElementById('auditoriumSelect');
    const bookingDateInput = document.getElementById('bookingDateInput');
    const eventNameInput = document.getElementById('eventNameInput');
    const organizationInput = document.getElementById('organizationInput');
    const picNameInput = document.getElementById('picNameInput');
    const picPhoneInput = document.getElementById('picPhoneInput');
    const suratInput = document.getElementById('surat');

    const summarySidebar = document.getElementById('summarySidebar');
    const summaryCard = document.getElementById('summaryCard');
    const auditoriumPreviewCard = document.getElementById('auditoriumPreviewCard');

    const sumAuditorium = document.getElementById('sumAuditorium');
    const sumDateTime = document.getElementById('sumDateTime');
    const sumEventName = document.getElementById('sumEventName');
    const sumOrganization = document.getElementById('sumOrganization');
    const sumPic = document.getElementById('sumPic');
    const sumFile = document.getElementById('sumFile');

    const previewImage = document.getElementById('previewImage');
    const previewFallback = document.getElementById('previewFallback');
    const previewName = document.getElementById('previewName');
    const previewLocation = document.getElementById('previewLocation');
    const previewCapacity = document.getElementById('previewCapacity');
    const previewFacilities = document.getElementById('previewFacilities');

    let selectedSessionLabel = '';

    function toggleSummaryItem(rowId, valueEl, text) {
        const row = document.getElementById(rowId);
        if (text && text.trim() !== '') {
            valueEl.textContent = text;
            row.style.display = 'flex';
            row.classList.add('visible');
        } else {
            valueEl.textContent = '';
            row.style.display = 'none';
            row.classList.remove('visible');
        }
        refreshSidebarVisibility();
    }

    function refreshSidebarVisibility() {
        const visibleItems = document.querySelectorAll('#summaryCard .summary-item.visible');
        const hasSummaryItems = visibleItems.length > 0;
        const hasAuditoriumPreview = auditoriumPreviewCard.style.display === 'block';
        const shouldShow = hasSummaryItems || hasAuditoriumPreview;

        summaryCard.style.display = hasSummaryItems ? 'block' : 'none';

        if (shouldShow) {
            bookingLayout.classList.add('has-sidebar');
            summarySidebar.classList.add('is-visible');
        } else {
            bookingLayout.classList.remove('has-sidebar');
            summarySidebar.classList.remove('is-visible');
        }

        visibleItems.forEach((item, index) => {
            item.style.borderBottom = (index === visibleItems.length - 1) ? 'none' : '1px dashed rgba(22, 54, 38, 0.12)';
            item.style.paddingBottom = (index === visibleItems.length - 1) ? '0' : '8px';
        });
    }

    function updateSummarySchedule() {
        const rawDate = bookingDateInput.value;
        let formattedDate = '';
        if (rawDate) {
            const parts = rawDate.split('-');
            formattedDate = `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        const start = startTimeInput.value;
        const end = endTimeInput.value;
        const sessionText = (start && end) ? `${selectedSessionLabel} (${start} - ${end})` : '';

        const combined = [formattedDate, sessionText].filter(Boolean).join(' • ');
        toggleSummaryItem('rowDateTime', sumDateTime, combined);
    }

    function updateSummaryInputs(includePic = true) {
        toggleSummaryItem('rowEventName', sumEventName, eventNameInput.value);
        toggleSummaryItem('rowOrganization', sumOrganization, organizationInput.value);

        if (includePic) {
            const picName = picNameInput.value.trim();
            const picPhone = picPhoneInput.value.trim();
            const picCombined = picName ? (picPhone ? `${picName} (${picPhone})` : picName) : '';
            toggleSummaryItem('rowPic', sumPic, picCombined);
        }
    }

    function updateAuditoriumPreview() {
        const selectedOption = auditoriumSelect.options[auditoriumSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            auditoriumPreviewCard.style.display = 'none';
            previewImage.style.display = 'none';
            previewFallback.style.display = 'none';
            toggleSummaryItem('rowAuditorium', sumAuditorium, '');
            return;
        }

        const image = selectedOption.dataset.image;
        if (image) {
            previewImage.src = image;
            previewImage.style.display = 'block';
            previewFallback.style.display = 'none';
            previewImage.onerror = function () {
                previewImage.style.display = 'none';
                previewFallback.style.display = 'flex';
            };
        } else {
            previewImage.style.display = 'none';
            previewFallback.style.display = 'flex';
        }

        previewName.textContent = selectedOption.dataset.name;
        previewLocation.textContent = selectedOption.dataset.location;
        previewCapacity.textContent = selectedOption.dataset.capacity + ' Orang';

        const facilitiesRaw = (selectedOption.dataset.facilities || '').split('|').filter(Boolean);
        previewFacilities.innerHTML = '';
        if (facilitiesRaw.length > 0) {
            facilitiesRaw.forEach(item => {
                const chip = document.createElement('span');
                chip.className = 'preview-facility-tag';
                chip.textContent = item;
                previewFacilities.appendChild(chip);
            });
        } else {
            const emptyChip = document.createElement('span');
            emptyChip.className = 'preview-facility-tag';
            emptyChip.textContent = 'Fasilitas standar';
            previewFacilities.appendChild(emptyChip);
        }

        auditoriumPreviewCard.style.display = 'block';
        toggleSummaryItem('rowAuditorium', sumAuditorium, selectedOption.dataset.name);
    }

    auditoriumSelect.addEventListener('change', updateAuditoriumPreview);

    const bookingDateWarning = document.getElementById('bookingDateWarning');
    function validateBookingDateSelection() {
        const selectedDate = bookingDateInput.value;
        const minimumDate = bookingDateInput.min;
        if (selectedDate && minimumDate && selectedDate < minimumDate) {
            bookingDateInput.value = '';
            bookingDateWarning.style.display = 'block';
            bookingDateWarning.textContent = 'Tanggal acara terlalu dekat. Pengajuan wajib dilakukan minimal H-7 dari tanggal acara. Pilih tanggal ' + minimumDate + ' atau setelahnya.';
            updateSummarySchedule();
            return false;
        }
        bookingDateWarning.style.display = 'none';
        updateSummarySchedule();
        return true;
    }

    bookingDateInput.addEventListener('change', validateBookingDateSelection);
    bookingDateInput.addEventListener('input', validateBookingDateSelection);
    eventNameInput.addEventListener('input', () => updateSummaryInputs(true));
    organizationInput.addEventListener('input', () => updateSummaryInputs(true));
    picNameInput.addEventListener('input', () => updateSummaryInputs(true));
    picPhoneInput.addEventListener('input', () => updateSummaryInputs(true));

    suratInput.addEventListener('change', function () {
        const fileName = this.files && this.files[0] ? this.files[0].name : '';
        toggleSummaryItem('rowFile', sumFile, fileName);
    });

    const sessionCards = document.querySelectorAll('.session-card');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const sessionError = document.getElementById('sessionError');
    const bookingForm = document.querySelector('.booking-form');

    function selectSession(card) {
        sessionCards.forEach(item => item.classList.remove('active'));
        card.classList.add('active');

        startTimeInput.value = card.dataset.start;
        endTimeInput.value = card.dataset.end;
        selectedSessionLabel = card.dataset.label;

        sessionError.style.display = 'none';
        updateSummarySchedule();
    }

    sessionCards.forEach(card => {
        card.addEventListener('click', function () {
            selectSession(this);
        });
    });

    const oldStart = startTimeInput.value;
    const oldEnd = endTimeInput.value;
    if (oldStart && oldEnd) {
        const oldCard = Array.from(sessionCards).find(card =>
            card.dataset.start === oldStart && card.dataset.end === oldEnd
        );
        if (oldCard) selectSession(oldCard);
    }

    updateAuditoriumPreview();
    updateSummarySchedule();
    updateSummaryInputs(false);

    bookingForm.addEventListener('submit', function (event) {
        if (!validateBookingDateSelection() || !bookingDateInput.value) {
            event.preventDefault();
            bookingDateWarning.style.display = 'block';
            bookingDateWarning.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (!startTimeInput.value || !endTimeInput.value) {
            event.preventDefault();
            sessionError.style.display = 'block';
            sessionError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>

@endsection