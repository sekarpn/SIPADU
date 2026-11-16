
@extends('layouts.panel')

@section('panel')

<style>
    /* LAYOUT UTAMA */
    .booking-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 0.9fr);
        gap: 24px;
        align-items: start;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .booking-layout > * {
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .booking-form {
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    /* PREVIEW AUDITORIUM */
    .auditorium-preview {
        position: sticky;
        top: 20px;
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }

    .auditorium-preview img {
        width: 100%;
        height: 230px;
        object-fit: cover;
        display: block;
    }

    .preview-placeholder {
        height: 230px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
        background: #f1f5f9;
        color: #64748b;
    }

    .preview-info {
        padding: 18px;
        overflow-wrap: anywhere;
    }

    .preview-info h3 {
        margin: 0 0 12px;
    }

    .preview-info p {
        margin: 8px 0;
        color: #64748b;
        font-size: 14px;
    }

    .preview-title {
        padding: 16px 18px 0;
        font-weight: 600;
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
        background: #fff;
        padding: 16px 10px;
        cursor: pointer;
        text-align: center;
        transition: .2s;
        color: #334155;
    }

    .session-card:hover {
        border-color: #16a34a;
        background: #f0fdf4;
    }

    .session-card.active {
        border: 2px solid #16a34a;
        background: #f0fdf4;
        color: #15803d;
    }

    .session-card strong {
        display: block;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .session-card span {
        display: block;
        font-size: 13px;
    }

    /* CENTANG HANYA MUNCUL SETELAH DIKLIK */
    .session-card .session-check {
        display: none !important;
        position: absolute;
        top: 7px;
        right: 9px;
        font-weight: bold;
        color: #16a34a;
    }

    .session-card.active .session-check {
        display: block !important;
    }

    .session-help {
        color: #64748b;
        font-size: 13px;
        margin: 0 0 12px;
    }

    /* UPLOAD SURAT */
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

    /* RESPONSIVE TABLET DAN HP */
    @media (max-width: 900px) {
        .booking-layout {
            grid-template-columns: minmax(0, 1fr);
            gap: 20px;
        }

        .auditorium-preview {
            position: static;
            grid-row: 1;
        }

        .session-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 520px) {
        .session-grid {
            grid-template-columns: 1fr;
        }
    }
    /* FIX WARNA PILIHAN SESI */
.booking-form .session-grid .session-card {
    appearance: none !important;
    -webkit-appearance: none !important;
    background: #ffffff !important;
    color: #334155 !important;
    border: 1.5px solid #e2e8f0 !important;
    box-shadow: none !important;
}

.booking-form .session-grid .session-card:hover {
    background: #f0fdf4 !important;
    color: #15803d !important;
    border-color: #16a34a !important;
}

.booking-form .session-grid .session-card.active,
.booking-form .session-grid .session-card.active:hover {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 2px solid #16a34a !important;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.12) !important;
}

.booking-form .session-grid .session-card.active strong,
.booking-form .session-grid .session-card.active span {
    color: #15803d !important;
}

.booking-form .session-grid .session-card .session-check {
    display: none !important;
}

.booking-form .session-grid .session-card.active .session-check {
    display: block !important;
    color: #15803d !important;
}
/* UPLOAD SURAT */
.letter-upload {
        margin: 22px 0;
        padding: 18px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-sizing: border-box;
    }

    /* <--- TAMBAHKAN CSS INI ---> */
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
    /* <--- BATAS TAMBAHAN CSS ---> */

</style>

<div class="page-heading">
    <div>
        <h1>Ajukan Peminjaman</h1>
        <p>Isi informasi kegiatan dan jadwal auditorium yang dibutuhkan.</p>
    </div>
</div>

<div class="booking-layout">

    {{-- FORM PENGAJUAN --}}
    <form class="form-card booking-form"
          method="POST"
          action="{{ route('borrower.booking.store') }}"
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
                    data-description="{{ $a->description ?: 'Tidak ada deskripsi.' }}"
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
            min="{{ date('Y-m-d') }}"
            value="{{ old('booking_date') }}"
            required
        >

        {{-- PILIHAN SESI --}}
        <label>Pilih Sesi Peminjaman *</label>

        <p class="session-help">
            Pilih salah satu sesi waktu yang tersedia.
        </p>

        <div class="session-grid">

            <button
                type="button"
                class="session-card"
                data-start="08:00"
                data-end="12:00"
            >
                <span class="session-check">✓</span>
                <strong>Sesi Pagi</strong>
                <span>08:00 - 12:00</span>
            </button>

            <button
                type="button"
                class="session-card"
                data-start="13:00"
                data-end="17:00"
            >
                <span class="session-check">✓</span>
                <strong>Sesi Siang</strong>
                <span>13:00 - 17:00</span>
            </button>

            <button
                type="button"
                class="session-card"
                data-start="08:00"
                data-end="17:00"
            >
                <span class="session-check">✓</span>
                <strong>Full Day</strong>
                <span>08:00 - 17:00</span>
            </button>

        </div>

        <input
            type="hidden"
            name="start_time"
            id="start_time"
            value="{{ old('start_time') }}"
        >

        <input
            type="hidden"
            name="end_time"
            id="end_time"
            value="{{ old('end_time') }}"
        >

        <p id="sessionError" class="notice" style="display:none;">
            Silakan pilih salah satu sesi peminjaman.
        </p>

        @error('start_time')
            <p class="notice">{{ $message }}</p>
        @enderror

        @error('end_time')
            <p class="notice">{{ $message }}</p>
        @enderror

        <h3>Informasi Kegiatan</h3>

        <label>Nama Kegiatan *</label>

        <input
            name="event_name"
            value="{{ old('event_name') }}"
            required
        >

        <label>Organisasi/Unit *</label>

        <input
            name="organization"
            value="{{ old('organization') }}"
            placeholder="Himpunan, BEM, UKM, KSM, dll."
            required
        >

        <label>Deskripsi Kegiatan</label>

        <textarea name="event_description" rows="3">{{ old('event_description') }}</textarea>

        <h3>Kontak Penanggung Jawab</h3>

        <label>Nama Penanggung Jawab *</label>

        <input
            name="pic_name"
            value="{{ old('pic_name', auth()->user()->name) }}"
            required
        >

        <label>Nomor Telepon *</label>

        <input
            name="pic_phone"
            value="{{ old('pic_phone') }}"
            required
        >

        {{-- UPLOAD SURAT --}}
        <div class="letter-upload">

            <h3>Dokumen Persyaratan</h3>

            {{-- KOTAK DOWNLOAD TEMPLATE SURAT --}}
            <div class="template-box">
                <div class="template-box-text">
                    <strong>Belum punya format Surat Peminjaman?</strong>
                    <span>Unduh template resmi UPNVJ, isi detail acara, dan mintakan tanda tangan sebelum diunggah.</span>
                </div>
                <a href="{{ route('borrower.booking.template') }}" class="btn-template">
                    ↓ Download Template Surat (PDF)
                </a>
            </div>

            <label for="surat">Upload Surat Peminjaman (PDF) *</label>

            <input
                type="file"
                name="surat"
                id="surat"
                accept=".pdf,application/pdf"
                required
            >

            <small>
                Format file PDF dengan ukuran maksimal 5 MB.
            </small>

            @error('surat')
                <p class="notice">{{ $message }}</p>
            @enderror

        </div>

        </div>

        <p class="notice">
            Pengajuan akan berstatus Pending sampai diperiksa oleh Admin.
            Jadwal bentrok diperiksa terhadap peminjaman berstatus Disetujui.
        </p>

        <button type="submit" class="btn">
            Kirim Pengajuan
        </button>

    </form>

    {{-- PREVIEW AUDITORIUM --}}
    <div class="auditorium-preview">

        <div class="preview-title">
            Auditorium yang Dipilih
        </div>

        <div id="previewPlaceholder" class="preview-placeholder">
            Pilih auditorium untuk melihat foto.
        </div>

        <img
            id="previewImage"
            src=""
            alt="Foto auditorium"
            style="display:none;"
        >

        <div class="preview-info" id="previewInfo" style="display:none;">
            <h3 id="previewName"></h3>
            <p id="previewLocation"></p>
            <p id="previewCapacity"></p>
            <p id="previewFacilities"></p>
            <p id="previewDescription"></p>
        </div>

    </div>

</div>

<script>
    // PREVIEW AUDITORIUM
    const auditoriumSelect = document.getElementById('auditoriumSelect');

    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const previewImage = document.getElementById('previewImage');
    const previewInfo = document.getElementById('previewInfo');

    const previewName = document.getElementById('previewName');
    const previewLocation = document.getElementById('previewLocation');
    const previewCapacity = document.getElementById('previewCapacity');
    const previewFacilities = document.getElementById('previewFacilities');
    const previewDescription = document.getElementById('previewDescription');

    function updateAuditoriumPreview() {
        const selectedOption =
            auditoriumSelect.options[auditoriumSelect.selectedIndex];

        if (!selectedOption || !selectedOption.value) {
            previewPlaceholder.style.display = 'flex';
            previewImage.style.display = 'none';
            previewInfo.style.display = 'none';
            return;
        }

        const image = selectedOption.dataset.image;

        previewPlaceholder.style.display = image ? 'none' : 'flex';
        previewPlaceholder.textContent = image
            ? ''
            : 'Foto auditorium belum tersedia.';

        if (image) {
            previewImage.src = image;
            previewImage.style.display = 'block';
        } else {
            previewImage.style.display = 'none';
        }

        previewName.textContent = selectedOption.dataset.name;
        previewLocation.textContent =
            'Lokasi: ' + selectedOption.dataset.location;
        previewCapacity.textContent =
            'Kapasitas: ' + selectedOption.dataset.capacity + ' orang';
        previewFacilities.textContent =
            'Fasilitas: ' + selectedOption.dataset.facilities;
        previewDescription.textContent =
            selectedOption.dataset.description;

        previewInfo.style.display = 'block';
    }

    auditoriumSelect.addEventListener('change', updateAuditoriumPreview);
    updateAuditoriumPreview();


    // PILIHAN SESI PEMINJAMAN
    const sessionCards = document.querySelectorAll('.session-card');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const sessionError = document.getElementById('sessionError');
    const bookingForm = document.querySelector('.booking-form');

    function selectSession(card) {
        sessionCards.forEach(item => {
            item.classList.remove('active');
        });

        card.classList.add('active');

        startTimeInput.value = card.dataset.start;
        endTimeInput.value = card.dataset.end;

        sessionError.style.display = 'none';
    }

    sessionCards.forEach(card => {
        card.addEventListener('click', function () {
            selectSession(this);
        });
    });

    // Pulihkan pilihan sesi jika validasi form sebelumnya gagal
    const oldStart = startTimeInput.value;
    const oldEnd = endTimeInput.value;

    if (oldStart && oldEnd) {
        const oldCard = Array.from(sessionCards).find(card =>
            card.dataset.start === oldStart &&
            card.dataset.end === oldEnd
        );

        if (oldCard) {
            selectSession(oldCard);
        }
    }

    // VALIDASI SEBELUM FORM DIKIRIM
    bookingForm.addEventListener('submit', function (event) {
        if (!startTimeInput.value || !endTimeInput.value) {
            event.preventDefault();

            sessionError.style.display = 'block';

            sessionError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    });
</script>

@endsection
