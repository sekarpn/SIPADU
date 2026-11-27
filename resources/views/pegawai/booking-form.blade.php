@extends('layouts.panel')

@section('panel')

<style>
    /* RESET CONTAINER AGAR LEBAR PENUH */
    .booking-page-wrapper {
        width: 100%;
        max-width: 100% !important;
        box-sizing: border-box;
    }

    /* LAYOUT 2 KOLOM SEJAJAR */
    .booking-layout {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: flex-start !important;
        gap: 0px;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box;
        transition: gap 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .booking-layout.has-sidebar {
        gap: 24px !important;
    }

    /* KOLOM KIRI: FORMULIR (MENGECIL HALUS SAAT SIDEBAR MUNCUL) */
    .booking-form-col {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        max-width: none !important;
        width: 100% !important;
        margin: 0 !important;
        box-sizing: border-box;
        transition: all 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }

    /* ATUR POSISI IKON DROPDOWN AGAR TIDAK TERLALU PINGGIR */
    .booking-form select#auditoriumSelect {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        padding-right: 42px !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23334155' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 16px center !important;
        background-size: 16px 16px !important;
        cursor: pointer;
    }

    /* KOLOM KANAN: SIDEBAR RINGKASAN & PREVIEW (ANIMASI MELEBAR & FADE-IN) */
    .booking-summary-col {
        flex: 0 0 0px !important;
        width: 0px !important;
        max-width: 0px !important;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateX(16px);
        position: sticky;
        top: 20px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        box-sizing: border-box;
        transition:
            flex-basis 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            width 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            max-width 0.45s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.35s ease 0.1s,
            transform 0.45s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .booking-summary-col.is-visible {
        flex: 0 0 360px !important;
        width: 360px !important;
        max-width: 38% !important;
        opacity: 1;
        overflow: visible;
        pointer-events: auto;
        transform: translateX(0);
    }

    /* INNER WRAPPER AGAR TEKS DI DALAM SIDEBAR TIDAK TERJEPIT SAAT ANIMASI MELEBAR */
    .summary-sidebar-inner {
        min-width: 320px;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* KARTU RINGKASAN PEMINJAMAN LIVE */
    .summary-card {
        background: #ffffff;
        border: 1px solid #d1fae5;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 4px 14px rgba(20, 83, 45, 0.05);
        display: none;
    }

    .summary-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 1px solid #e2e8f0;
    }

    .summary-card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #14532d;
    }

    .summary-badge {
        background: #dcfce7;
        color: #15803d;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
    }

    .summary-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
        font-size: 13.5px;
    }

    .summary-item {
        display: none;
        flex-direction: column;
        gap: 2px;
        padding-bottom: 9px;
        border-bottom: 1px dashed #e2e8f0;
    }

    .summary-item.visible:last-of-type {
        border-bottom: none;
        padding-bottom: 0;
    }

    .summary-label {
        font-size: 11.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .summary-value {
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }

    /* PREVIEW AUDITORIUM */
    .auditorium-preview {
        width: 100%;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-sizing: border-box;
        display: none;
    }

    .auditorium-preview img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .preview-info {
        padding: 16px;
        overflow-wrap: anywhere;
    }

    .preview-info h4 {
        margin: 0 0 8px;
        font-size: 15px;
        color: #0f172a;
    }

    .preview-info p {
        margin: 5px 0;
        color: #64748b;
        font-size: 13px;
    }

    .preview-title {
        padding: 14px 16px 10px;
        font-weight: 700;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }

    /* PILIHAN SESI */
    .session-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin: 10px 0 16px;
    }

    .session-card {
        position: relative;
        min-width: 0;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff !important;
        padding: 16px 10px;
        cursor: pointer;
        text-align: center;
        transition: .2s;
        color: #334155 !important;
        appearance: none !important;
        -webkit-appearance: none !important;
    }

    .session-card:hover {
        border-color: #16a34a !important;
        background: #f0fdf4 !important;
        color: #15803d !important;
    }

    .session-card.active,
    .session-card.active:hover {
        border: 2px solid #16a34a !important;
        background: #dcfce7 !important;
        color: #15803d !important;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12) !important;
    }

    .session-card strong {
        display: block;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .session-card span {
        display: block;
        font-size: 13px;
    }

    .session-card.active strong,
    .session-card.active span {
        color: #15803d !important;
    }

    .session-card .session-check {
        display: none !important;
        position: absolute;
        top: 7px;
        right: 9px;
        font-weight: bold;
        color: #15803d !important;
    }

    .session-card.active .session-check {
        display: block !important;
    }

    .session-help {
        color: #64748b;
        font-size: 13px;
        margin: 0 0 12px;
    }

    /* UPLOAD SURAT & TEMPLATE BOX */
    .letter-upload {
        margin: 22px 0;
        padding: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-sizing: border-box;
    }

    .letter-upload h3 {
        margin-top: 0;
    }

    .template-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 12px 14px;
        margin-bottom: 16px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        flex-wrap: wrap;
    }

    .template-box-text strong {
        display: block;
        font-size: 13.5px;
        color: #166534;
        margin-bottom: 2px;
    }

    .template-box-text span {
        display: block;
        font-size: 12.5px;
        color: #15803d;
    }

    .btn-template {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 14px;
        background: #16a34a;
        color: #ffffff !important;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        white-space: nowrap;
        transition: .2s;
    }

    .btn-template:hover {
        background: #15803d;
    }

    .letter-upload input[type="file"] {
        display: block;
        width: 100%;
        max-width: 100%;
        margin-top: 10px;
        padding: 10px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: white;
        box-sizing: border-box;
    }

    .letter-upload small {
        display: block;
        color: #64748b;
        margin-top: 8px;
    }

    /* HANYA TUMPUK KE BAWAH DI LAYAR HP/TABLET KECIL */
    @media (max-width: 960px) {
        .booking-layout {
            flex-direction: column !important;
            flex-wrap: wrap !important;
            gap: 20px !important;
        }

        .booking-summary-col {
            display: none !important;
        }

        .booking-summary-col.is-visible {
            display: flex !important;
            flex: 1 1 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            position: static;
            transform: none;
        }

        .summary-sidebar-inner {
            min-width: 0;
            width: 100%;
        }
    }

    @media (max-width: 520px) {
        .session-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="booking-page-wrapper">
    <div class="page-heading">
        <div>
            <h1>Ajukan Peminjaman</h1>
            <p>Isi informasi kegiatan dan jadwal auditorium yang dibutuhkan.</p>
        </div>
    </div>

    <div class="booking-layout" id="bookingLayout">

        {{-- KOLOM KIRI: FORM PENGAJUAN --}}
        <form class="form-card booking-form booking-form-col"
              method="POST"
              action="{{ route('pegawai.booking.store') }}"
              enctype="multipart/form-data">

            @csrf

            <h3>Informasi Peminjaman</h3>

            <label>Pilih Auditorium *</label>
            <select name="auditorium_id" id="auditoriumSelect" required>
                <option value="">Pilih auditorium</option>
                @foreach($auditoriums as $a)
                    <option
                        value="{{ $a->id }}"
                        data-name="{{ $a->name }}"
                        data-image="{{ $a->image_path ? asset('storage/' . $a->image_path) : '' }}"
                        data-location="{{ $a->location ?: 'Belum ditentukan' }}"
                        data-capacity="{{ $a->capacity ?: 'Belum ditentukan' }}"
                        data-facilities="{{ implode(', ', $a->facilities ?? []) ?: 'Belum tersedia' }}"
                        @selected((string) old('auditorium_id', $selected) === (string) $a->id)
                    >
                        {{ $a->name }}
                    </option>
                @endforeach
            </select>
            @error('auditorium_id')
                <p class="notice">{{ $message }}</p>
            @enderror

            <label>Tanggal Peminjaman *</label>
            <input
                type="date"
                name="booking_date"
                id="bookingDateInput"
                min="{{ date('Y-m-d', strtotime('+7 days')) }}"
                value="{{ old('booking_date') }}"
                required
            >

            {{-- PILIHAN SESI --}}
            <label>Pilih Sesi Peminjaman *</label>
            <p class="session-help">Pilih salah satu sesi waktu yang tersedia.</p>

            <div class="session-grid">
                <button type="button" class="session-card" data-label="Sesi Pagi" data-start="08:00" data-end="12:00">
                    <span class="session-check">✓</span>
                    <strong>Sesi Pagi</strong>
                    <span>08:00 - 12:00</span>
                </button>

                <button type="button" class="session-card" data-label="Sesi Siang" data-start="13:00" data-end="17:00">
                    <span class="session-check">✓</span>
                    <strong>Sesi Siang</strong>
                    <span>13:00 - 17:00</span>
                </button>

                <button type="button" class="session-card" data-label="Full Day" data-start="08:00" data-end="17:00">
                    <span class="session-check">✓</span>
                    <strong>Full Day</strong>
                    <span>08:00 - 17:00</span>
                </button>
            </div>

            <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time') }}">
            <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time') }}">

            <p id="sessionError" class="notice" style="display:none;">
                Silakan pilih salah satu sesi peminjaman.
            </p>

            @error('start_time') <p class="notice">{{ $message }}</p> @enderror
            @error('end_time') <p class="notice">{{ $message }}</p> @enderror

            <h3>Data Pemohon</h3>
            <div class="form-card" style="margin:0 0 18px;padding:14px 16px;background:#f5faf6;border:1px solid #dcebe0;border-radius:10px;">
                <p style="margin:0 0 6px;"><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                <p style="margin:0 0 6px;"><strong>Jenis Akun:</strong> {{ auth()->user()->role === 'pegawai' ? 'Dosen & Tendik' : 'Mahasiswa' }}</p>
                <p style="margin:0 0 6px;"><strong>{{ auth()->user()->role === 'pegawai' ? 'NIP' : 'NIM' }}:</strong> {{ auth()->user()->nim_nip }}</p>
                @if(auth()->user()->role === 'pegawai')
                    <p style="margin:0 0 6px;"><strong>Unit / Biro:</strong> {{ auth()->user()->employee_unit ?: 'Belum diisi pada profil' }}</p>
                    <p style="margin:0;"><strong>Jabatan:</strong> {{ auth()->user()->position ?: 'Belum diisi pada profil' }}</p>
                @else
                    <p style="margin:0 0 6px;"><strong>Fakultas:</strong> {{ auth()->user()->faculty ?: 'Belum diisi pada profil' }}</p>
                    <p style="margin:0;"><strong>Program Studi:</strong> {{ auth()->user()->study_program ?: 'Belum diisi pada profil' }}</p>
                @endif
            </div>

            <h3>Informasi Kegiatan</h3>

            <label>Nama Kegiatan *</label>
            <input name="event_name" id="eventNameInput" value="{{ old('event_name') }}" required>

            <label>Organisasi/Unit *</label>
            <input name="organization" id="organizationInput" value="{{ old('organization') }}" placeholder="Himpunan, BEM, UKM, KSM, dll." required>

            <label>Deskripsi Kegiatan</label>
            <textarea name="event_description" rows="3">{{ old('event_description') }}</textarea>

            <h3>Kontak Penanggung Jawab</h3>

            <label>Nama Penanggung Jawab *</label>
            <input name="pic_name" id="picNameInput" value="{{ old('pic_name', auth()->user()->name) }}" required>

            <label>Nomor Telepon *</label>
            <input name="pic_phone" id="picPhoneInput" value="{{ old('pic_phone') }}" required>

            {{-- UPLOAD SURAT --}}
            <div class="letter-upload">
                <h3>Dokumen Persyaratan</h3>

                <div class="template-box">
                    <div class="template-box-text">
                        <strong>Belum punya format Surat Peminjaman?</strong>
                        <span>Unduh template resmi UPNVJ, isi detail acara, dan mintakan tanda tangan sebelum diunggah.</span>
                    </div>
                    <a href="{{ route('pegawai.booking.template') }}" class="btn-template">
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

            <p class="notice">
                Pengajuan akan berstatus Pending sampai diperiksa oleh Admin.
                Jadwal bentrok diperiksa terhadap peminjaman berstatus Disetujui.
            </p>

            <button type="submit" class="btn">Kirim Pengajuan</button>
        </form>

        {{-- KOLOM KANAN: RINGKASAN PEMINJAMAN & PREVIEW AUDITORIUM --}}
        <aside class="booking-summary-col" id="summarySidebar">
            <div class="summary-sidebar-inner">

                {{-- KARTU 1: RINGKASAN PEMINJAMAN (LIVE) --}}
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
                            <span class="summary-label">Tanggal & Sesi</span>
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

                {{-- KARTU 2: PREVIEW AUDITORIUM --}}
                <div class="auditorium-preview" id="auditoriumPreviewCard">
                    <div class="preview-title">Detail Ruangan Dipilih</div>
                    <img id="previewImage" src="" alt="Foto auditorium" style="display:none;">
                    <div class="preview-info" id="previewInfo">
                        <h4 id="previewName"></h4>
                        <p id="previewLocation"></p>
                        <p id="previewCapacity"></p>
                        <p id="previewFacilities"></p>
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
                <a href="{{ route('pegawai.profile') }}" class="profile-required-button">
                    Lengkapi Profil
                </a>
            </div>
        </div>

        <style>
            /* Kunci scroll tanpa mengubah posisi body agar layout tidak bergeser */
            html.profile-modal-open {
                overflow: hidden !important;
                scrollbar-gutter: stable;
                overscroll-behavior: none !important;
            }

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
                overflow: hidden;
                overscroll-behavior: contain;
                touch-action: none;
                background: rgba(15, 23, 42, .58);
                -webkit-backdrop-filter: blur(3px);
                backdrop-filter: blur(3px);
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity .28s ease, visibility .28s ease;
                isolation: isolate;
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
                transition:
                    opacity .28s ease,
                    transform .36s cubic-bezier(.2, .75, .25, 1);
                will-change: transform, opacity;
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

            @media (prefers-reduced-motion: reduce) {
                .profile-required-overlay,
                .profile-required-modal {
                    transition: none !important;
                }
            }

            @media (max-width: 480px) {
                .profile-required-modal {
                    padding: 27px 22px;
                    border-radius: 15px;
                }

                .profile-required-modal h2 { font-size: 19px; }
                .profile-required-modal p { font-size: 12px; }
            }
        </style>

        <script>
            (function () {
                const root = document.documentElement;
                const body = document.body;
                const overlay = document.querySelector('.profile-required-overlay');

                // Kunci scroll tanpa position:fixed agar halaman tidak meloncat/repaint.
                root.classList.add('profile-modal-open');
                body.classList.add('profile-modal-open');

                // Pisahkan render awal dan animasi masuk agar blur tidak berkedip.
                if (overlay) {
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            overlay.classList.add('is-visible');
                        });
                    });
                }
            })();
        </script>
    @endif

</div>

<script>
    // ELEMEN LAYOUT & FORM
    const bookingLayout = document.getElementById('bookingLayout');
    const auditoriumSelect = document.getElementById('auditoriumSelect');
    const bookingDateInput = document.getElementById('bookingDateInput');
    const eventNameInput = document.getElementById('eventNameInput');
    const organizationInput = document.getElementById('organizationInput');
    const picNameInput = document.getElementById('picNameInput');
    const picPhoneInput = document.getElementById('picPhoneInput');
    const suratInput = document.getElementById('surat');

    // ELEMEN SIDEBAR & RINGKASAN
    const summarySidebar = document.getElementById('summarySidebar');
    const summaryCard = document.getElementById('summaryCard');
    const auditoriumPreviewCard = document.getElementById('auditoriumPreviewCard');

    const sumAuditorium = document.getElementById('sumAuditorium');
    const sumDateTime = document.getElementById('sumDateTime');
    const sumEventName = document.getElementById('sumEventName');
    const sumOrganization = document.getElementById('sumOrganization');
    const sumPic = document.getElementById('sumPic');
    const sumFile = document.getElementById('sumFile');

    // ELEMEN PREVIEW AUDITORIUM
    const previewImage = document.getElementById('previewImage');
    const previewName = document.getElementById('previewName');
    const previewLocation = document.getElementById('previewLocation');
    const previewCapacity = document.getElementById('previewCapacity');
    const previewFacilities = document.getElementById('previewFacilities');

    let selectedSessionLabel = '';

    // Mengatur tampil/sembunyi tiap baris ringkasan & kotak utama
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

        // Hilangkan garis bawah pada item terakhir yang tampil
        visibleItems.forEach((item, index) => {
            item.style.borderBottom = (index === visibleItems.length - 1) ? 'none' : '1px dashed #e2e8f0';
            item.style.paddingBottom = (index === visibleItems.length - 1) ? '0' : '9px';
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
            toggleSummaryItem('rowAuditorium', sumAuditorium, '');
            return;
        }

        const image = selectedOption.dataset.image;
        if (image) {
            previewImage.src = image;
            previewImage.style.display = 'block';
        } else {
            previewImage.style.display = 'none';
        }

        previewName.textContent = selectedOption.dataset.name;
        previewLocation.textContent = 'Lokasi: ' + selectedOption.dataset.location;
        previewCapacity.textContent = 'Kapasitas: ' + selectedOption.dataset.capacity + ' orang';
        previewFacilities.textContent = 'Fasilitas: ' + selectedOption.dataset.facilities;
        auditoriumPreviewCard.style.display = 'block';

        toggleSummaryItem('rowAuditorium', sumAuditorium, selectedOption.dataset.name);
    }

    // EVENT LISTENERS LIVE UPDATE
    auditoriumSelect.addEventListener('change', updateAuditoriumPreview);
    bookingDateInput.addEventListener('change', updateSummarySchedule);
    eventNameInput.addEventListener('input', () => updateSummaryInputs(true));
    organizationInput.addEventListener('input', () => updateSummaryInputs(true));
    picNameInput.addEventListener('input', () => updateSummaryInputs(true));
    picPhoneInput.addEventListener('input', () => updateSummaryInputs(true));

    suratInput.addEventListener('change', function () {
        const fileName = this.files && this.files[0] ? this.files[0].name : '';
        toggleSummaryItem('rowFile', sumFile, fileName);
    });

    // PILIHAN SESI PEMINJAMAN
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

    // Pulihkan pilihan sesi jika validasi gagal
    const oldStart = startTimeInput.value;
    const oldEnd = endTimeInput.value;
    if (oldStart && oldEnd) {
        const oldCard = Array.from(sessionCards).find(card =>
            card.dataset.start === oldStart && card.dataset.end === oldEnd
        );
        if (oldCard) selectSession(oldCard);
    }

    // Inisialisasi awal: jangan munculkan ringkasan jika user belum memilih/mengisi apapun
    updateAuditoriumPreview();
    updateSummarySchedule();
    updateSummaryInputs(false);

    // VALIDASI SEBELUM FORM DIKIRIM
    bookingForm.addEventListener('submit', function (event) {
        if (!startTimeInput.value || !endTimeInput.value) {
            event.preventDefault();
            sessionError.style.display = 'block';
            sessionError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>

@endsection