@extends('layouts.panel')

@section('panel')

<style>
    .auditorium-page {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        padding: 10px 10px 35px;
        color: #163626;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .auditorium-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 15px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    .auditorium-header h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.03em;
        color: #163626;
        margin: 0 0 5px;
    }

    .auditorium-header p {
        font-size: 13px;
        color: #4c6456;
        margin: 0;
    }

    /* TOMBOL CLEAN & KONSISTEN */
    .aud-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        box-shadow: none !important;
        transition: background .2s ease, transform .15s ease, border-color .2s ease;
    }

    .aud-btn:hover {
        transform: translateY(-1px);
    }

    .aud-btn-primary {
        background: #237c4d !important;
        color: #fff !important;
        border-color: #237c4d !important;
        padding: 10px 18px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .aud-btn-primary:hover {
        background: #1b663f !important;
    }

    .aud-btn-secondary {
        background: rgba(255, 255, 255, .85) !important;
        color: #163626 !important;
        border-color: rgba(22, 54, 38, .16) !important;
        padding: 10px 16px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .aud-btn-secondary:hover {
        background: #fff !important;
    }

    .aud-btn-edit {
        background: #fef3c7 !important;
        color: #92400e !important;
        border-color: #fde68a !important;
    }

    .aud-btn-edit:hover {
        background: #fde68a !important;
    }

    .aud-btn-delete {
        background: #fee2e2 !important;
        color: #b91c1c !important;
        border-color: #fecaca !important;
    }

    .aud-btn-delete:hover {
        background: #fecaca !important;
    }

    /* LAYOUT SPLIT: KIRI DAFTAR AUDITORIUM, KANAN FORM PANEL */
    .aud-split-layout {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: flex-start !important;
        gap: 0px;
        width: 100% !important;
        box-sizing: border-box !important;
        transition: gap 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .aud-split-layout.is-open {
        gap: 24px !important;
    }

    /* KOLOM KIRI: MAKSIMAL 2 KARTU PER BARIS */
    .aud-list-col {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        width: 100% !important;
        transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .auditorium-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
        align-items: stretch !important;
        width: 100% !important;
    }

    .auditorium-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, .74), rgba(255, 255, 255, .46));
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
        display: flex !important;
        flex-direction: column !important;
        box-sizing: border-box !important;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .auditorium-card:hover {
        transform: translateY(-2px);
    }

    .auditorium-card.is-active {
        border: 2px solid #237c4d !important;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .14) !important;
    }

    .auditorium-photo {
        width: 100%;
        height: 185px;
        object-fit: cover;
        display: block;
        background: rgba(235, 243, 238, .65);
        border-bottom: 1px solid rgba(22, 54, 38, .06);
    }

    .auditorium-content {
        padding: 18px 20px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .auditorium-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .auditorium-content h3 {
        margin: 0;
        font-size: 16.5px;
        line-height: 1.35;
        font-weight: 800;
        color: #163626;
    }

    .auditorium-info {
        color: #4c6456;
        font-size: 12.8px;
        line-height: 1.65;
        margin-bottom: 16px;
    }

    .auditorium-info div {
        margin-bottom: 3px;
    }

    .auditorium-info strong {
        color: #163626;
        font-weight: 700;
    }

    /* FOOTER KARTU CLEAN: TOGGLE DI KIRI, TOMBOL EDIT & HAPUS DI KANAN */
    .auditorium-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 14px;
        border-top: 1px solid rgba(22, 54, 38, .08);
    }

    .aud-action-buttons {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .aud-action-buttons form {
        margin: 0;
    }

    .status-active {
        color: #065f46;
        background: #bbf7d0;
        border: 1px solid #34d399;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-inactive {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fca5a5;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* KOLOM KANAN: PANEL FORM TAMBAH / EDIT AUDITORIUM */
    .aud-form-col {
        flex: 0 0 0px !important;
        width: 0px !important;
        max-width: 0px !important;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        transform: translateX(16px);
        position: sticky;
        top: 20px;
        box-sizing: border-box !important;
        transition:
            flex-basis 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            width 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            max-width 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.3s ease 0.06s,
            transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .aud-form-col.is-open {
        flex: 0 0 clamp(440px, 46%, 540px) !important;
        width: clamp(440px, 46%, 540px) !important;
        max-width: 50% !important;
        opacity: 1 !important;
        overflow: visible !important;
        pointer-events: auto !important;
        transform: translateX(0) !important;
    }

    .auditorium-form {
        width: 100% !important;
        min-width: 360px;
        background: linear-gradient(145deg, rgba(255, 255, 255, .86), rgba(255, 255, 255, .60));
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, .72);
        border-radius: 18px;
        padding: 22px 24px;
        box-shadow: 0 8px 28px rgba(22, 54, 38, .08);
        box-sizing: border-box !important;
    }

    .aud-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(22, 54, 38, .08);
    }

    .aud-form-header h3 {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        color: #163626;
    }

    .aud-close-btn {
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid rgba(22, 54, 38, .12);
        background: rgba(255, 255, 255, .75);
        color: #163626;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .aud-close-btn:hover {
        background: #fff;
    }

    .aud-form-row {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 12px;
    }

    .auditorium-form label {
        display: block;
        margin: 12px 0 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: #294735;
    }

    .auditorium-form input:not([type="checkbox"]):not([type="file"]),
    .auditorium-form textarea {
        width: 100% !important;
        padding: 10px 13px;
        border: 1px solid rgba(22, 54, 38, .14);
        border-radius: 10px;
        background: rgba(255, 255, 255, .88);
        font-family: inherit;
        font-size: 13px;
        color: #163626;
        box-sizing: border-box !important;
        outline: none;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .auditorium-form input:focus,
    .auditorium-form textarea:focus {
        background: #fff;
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .auditorium-form textarea {
        min-height: 95px;
        resize: vertical;
        line-height: 1.6;
    }

    .auditorium-form input[type="file"] {
        width: 100%;
        margin-top: 4px;
        font-size: 12px;
    }

    .auditorium-form small {
        display: block;
        margin-top: 5px;
        color: #64786c;
        font-size: 11px;
    }

    .aud-form-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
    }

    .auditorium-empty {
        background: linear-gradient(145deg, rgba(255, 255, 255, .64), rgba(255, 255, 255, .36));
        border: 1px dashed rgba(22, 54, 38, .22);
        border-radius: 16px;
        padding: 36px;
        text-align: center;
        color: #4c6456;
        grid-column: 1 / -1;
    }

    /* SWITCH & MODAL KONFIRMASI STATUS */
    .auditorium-status-control { display: inline-flex; align-items: center; gap: 9px; color: #294735; font-size: 12.5px; font-weight: 700; }
    .auditorium-switch { position: relative; display: inline-block; width: 42px; height: 24px; cursor: pointer; }
    .auditorium-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .auditorium-switch-track { position: absolute; inset: 0; border-radius: 99px; background: #cbd5cf; transition: background .2s ease; }
    .auditorium-switch-track::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.18); transition: transform .2s ease; }
    .auditorium-switch input:checked + .auditorium-switch-track { background: #237c4d; }
    .auditorium-switch input:checked + .auditorium-switch-track::before { transform: translateX(18px); }
    .auditorium-switch input:focus-visible + .auditorium-switch-track { outline: 3px solid rgba(35,124,77,.2); outline-offset: 2px; }
    .aud-status-modal { position: fixed; inset: 0; z-index: 10000; display: none; align-items: center; justify-content: center; padding: 20px; background: rgba(12, 28, 19, .48); backdrop-filter: blur(4px); }
    .aud-status-modal.is-open { display: flex; }
    .aud-status-dialog { width: min(420px, 100%); padding: 24px; border-radius: 17px; background: #fff; box-shadow: 0 20px 60px rgba(0,0,0,.2); color: #163626; }
    .aud-status-dialog h3 { margin: 0 0 8px; font-size: 17px; }
    .aud-status-dialog p { margin: 0 0 20px; color: #64786c; font-size: 13px; line-height: 1.55; }
    .aud-status-modal-actions { display: flex; justify-content: flex-end; gap: 9px; }
    .aud-status-cancel { background: #edf1ee; color: #294735; border: 1px solid #dce5de; }
    .aud-status-confirm { background: #237c4d; color: #fff; border: 1px solid #237c4d; }
    .aud-status-confirm.is-deactivate { background: #dc2626; border-color: #dc2626; }

    /* RESPONSIF LAPTOP LAYAR KECIL SAAT FORM TERBUKA */
    @media (max-width: 1220px) {
        .aud-split-layout.is-open .auditorium-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 900px) {
        .aud-split-layout {
            flex-direction: column-reverse !important;
            gap: 20px !important;
        }

        .auditorium-grid {
            grid-template-columns: 1fr !important;
        }

        .aud-form-col {
            display: none !important;
        }

        .aud-form-col.is-open {
            display: block !important;
            flex: 1 1 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            position: static !important;
            transform: none !important;
        }

        .auditorium-form {
            min-width: 0 !important;
            width: 100% !important;
        }

        .aud-form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="auditorium-page">
    <div class="auditorium-header">
        <div>
            <h1>Data Auditorium</h1>
            <p>Kelola informasi dan data auditorium yang dapat dipinjam.</p>
        </div>

        <button type="button" class="aud-btn aud-btn-primary" onclick="openAddAuditoriumPanel()">
            + Tambah Auditorium
        </button>
    </div>

    <div class="aud-split-layout {{ $errors->any() ? 'is-open' : '' }}" id="audSplitLayout">

        {{-- KIRI: DAFTAR AUDITORIUM (MAKSIMAL 2 KOTAK PER BARIS) --}}
        <div class="aud-list-col">
            <div class="auditorium-grid">
                @if($auditoriums->count() > 0)
                    @foreach($auditoriums as $a)
                        @php
                            $facilitiesText = implode(', ', $a->facilities ?? []);
                            $photoUrl = $a->image_path ? asset('storage/' . $a->image_path) : '';
                        @endphp
                        <div class="auditorium-card" id="auditoriumCard-{{ $a->id }}">
                            <div>
                                @if($a->image_path)
                                    <img
                                        src="{{ $photoUrl }}"
                                        alt="Foto {{ $a->name }}"
                                        class="auditorium-photo"
                                    >
                                @else
                                    <div class="auditorium-photo"
                                         style="display:flex;align-items:center;justify-content:center;color:#64786c;font-size:12.5px;">
                                        Belum ada foto auditorium
                                    </div>
                                @endif

                                <div class="auditorium-content" style="padding-bottom: 0;">
                                    <div>
                                        <div class="auditorium-title-row">
                                            <h3>{{ $a->name }}</h3>
                                            @if($a->is_active)
                                                <span class="status-active">Aktif</span>
                                            @else
                                                <span class="status-inactive">Nonaktif</span>
                                            @endif
                                        </div>

                                        <div class="auditorium-info">
                                            <div><strong>Lokasi:</strong> {{ $a->location ?: 'Belum diisi' }}</div>
                                            <div>
                                                <strong>Kapasitas:</strong>
                                                {{ $a->capacity ? $a->capacity . ' orang' : 'Belum diisi' }}
                                            </div>
                                            <div>
                                                <strong>Fasilitas:</strong>
                                                {{ $facilitiesText ?: 'Belum diisi' }}
                                            </div>
                                            <div>
                                                <strong>Deskripsi:</strong>
                                                {{ $a->description ?: 'Belum ada deskripsi' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="auditorium-content" style="padding-top: 0; flex: 0;">
                                <div class="auditorium-actions">
                                    {{-- TOGGLE AKTIF / NONAKTIF --}}
                                    <div class="auditorium-status-control">
                                        <span>{{ $a->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        <label class="auditorium-switch" aria-label="Ubah status {{ $a->name }}">
                                            <input type="checkbox" data-auditorium-toggle
                                                   data-toggle-url="{{ route('admin.auditoriums.toggle', $a->id) }}"
                                                   data-auditorium-name="{{ $a->name }}"
                                                   data-current-status="{{ $a->is_active ? '1' : '0' }}"
                                                   {{ $a->is_active ? 'checked' : '' }}>
                                            <span class="auditorium-switch-track"></span>
                                        </label>
                                    </div>

                                    {{-- TOMBOL EDIT & HAPUS SEJAJAR CLEAN --}}
                                    <div class="aud-action-buttons">
                                        <button
                                            type="button"
                                            class="aud-btn aud-btn-edit"
                                            data-card-id="auditoriumCard-{{ $a->id }}"
                                            data-update-url="{{ route('admin.auditoriums.update', $a->id) }}"
                                            data-name="{{ $a->name }}"
                                            data-location="{{ $a->location }}"
                                            data-capacity="{{ $a->capacity }}"
                                            data-facilities="{{ $facilitiesText }}"
                                            data-description="{{ $a->description }}"
                                            data-photo-url="{{ $photoUrl }}"
                                            onclick="openEditAuditoriumPanel(this)"
                                        >
                                            Edit
                                        </button>

                                        <form method="POST"
                                              action="{{ route('admin.auditoriums.delete', $a->id) }}"
                                              onsubmit="return confirm('Yakin ingin menghapus auditorium ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="aud-btn aud-btn-delete">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="auditorium-empty">
                        <h3 style="margin:0 0 6px;color:#163626;">Belum Ada Auditorium</h3>
                        <p style="margin:0;font-size:13px;">Silakan klik tombol Tambah Auditorium untuk menambahkan data.</p>
                    </div>
                @endif
            </div>

            <div style="margin-top: 22px;">
                {{ $auditoriums->links() }}
            </div>
        </div>

        {{-- KANAN: PANEL FORM TAMBAH / EDIT AUDITORIUM (TERSEMBUNYI DEFAULT) --}}
        <aside class="aud-form-col {{ $errors->any() ? 'is-open' : '' }}" id="audFormPanel">
            <form class="auditorium-form"
                  id="auditoriumForm"
                  method="POST"
                  action="{{ route('admin.auditoriums.save') }}"
                  enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="audFormMethod" value="POST">

                <div class="aud-form-header">
                    <h3 id="audFormTitle">Tambah Auditorium Baru</h3>
                    <button type="button" class="aud-close-btn" onclick="closeAuditoriumPanel()" title="Tutup">✕</button>
                </div>

                <label for="audNameInput">Nama Auditorium *</label>
                <input type="text" id="audNameInput" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama auditorium">

                <div class="aud-form-row">
                    <div>
                        <label for="audLocationInput">Lokasi</label>
                        <input type="text" id="audLocationInput" name="location" value="{{ old('location') }}" placeholder="Contoh: Kampus Pondok Labu">
                    </div>

                    <div>
                        <label for="audCapacityInput">Kapasitas</label>
                        <input type="number" id="audCapacityInput" name="capacity" min="1" value="{{ old('capacity') }}" placeholder="Contoh: 300">
                    </div>
                </div>

                <label for="audFacilitiesInput">Fasilitas</label>
                <input type="text" id="audFacilitiesInput" name="facilities" value="{{ old('facilities') }}"
                       placeholder="Contoh: AC, Proyektor, Sound System">

                <label for="audDescriptionInput">Deskripsi</label>
                <textarea id="audDescriptionInput" name="description" placeholder="Tuliskan deskripsi auditorium...">{{ old('description') }}</textarea>

                {{-- PRATINJAU FOTO SAAT INI (MUNCUL SAAT EDIT) --}}
                <div id="audCurrentPhotoWrap" style="display:none; margin-top: 12px;">
                    <label style="margin-top:0;">Foto Auditorium Saat Ini</label>
                    <img id="audCurrentPhotoImg" src="" alt="Foto auditorium" style="width:160px;height:105px;object-fit:cover;border-radius:10px;border:1px solid #d8e1db;display:block;">
                    <p id="audNoPhotoText" style="font-size:12px;color:#64786c;margin:4px 0;display:none;">Belum ada foto.</p>
                </div>

                <label for="audImageInput" id="audImageLabel">Foto Auditorium</label>
                <input type="file" id="audImageInput" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">
                <small id="audImageHelp">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</small>

                <label id="audActiveCheckboxWrap" style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:14px;">
                    <input type="checkbox" id="audActiveCheckbox" name="is_active" value="1" checked style="accent-color:#237c4d;width:16px;height:16px;">
                    <span>Aktif</span>
                </label>

                <div class="aud-form-footer">
                    <button type="submit" class="aud-btn aud-btn-primary" id="audSubmitBtn">
                        Simpan Auditorium
                    </button>
                    <button type="button" class="aud-btn aud-btn-secondary" onclick="closeAuditoriumPanel()">
                        Batal
                    </button>
                </div>
            </form>
        </aside>

    </div>
</div>

<div class="aud-status-modal" id="auditoriumStatusModal" aria-hidden="true">
    <div class="aud-status-dialog" role="dialog" aria-modal="true" aria-labelledby="audStatusTitle">
        <h3 id="audStatusTitle">Konfirmasi Perubahan Status</h3>
        <p id="audStatusMessage">Apakah kamu yakin ingin mengubah status auditorium ini?</p>
        <div class="aud-status-modal-actions">
            <button type="button" class="aud-btn aud-status-cancel" id="audStatusCancel">Tidak</button>
            <button type="button" class="aud-btn aud-status-confirm" id="audStatusConfirm">Ya, Lanjutkan</button>
        </div>
    </div>
</div>
<form method="POST" id="auditoriumStatusForm" style="display:none;">
    @csrf
    @method('PATCH')
</form>

<script>
const audSaveRoute = "{{ route('admin.auditoriums.save') }}";

function clearSelectedAudCards() {
    document.querySelectorAll('.auditorium-card').forEach(card => {
        card.classList.remove('is-active');
    });
}

function showAudPanel() {
    document.getElementById('audSplitLayout').classList.add('is-open');
    document.getElementById('audFormPanel').classList.add('is-open');
}

function closeAuditoriumPanel() {
    document.getElementById('audSplitLayout').classList.remove('is-open');
    document.getElementById('audFormPanel').classList.remove('is-open');
    clearSelectedAudCards();
}

function openAddAuditoriumPanel() {
    clearSelectedAudCards();

    const form = document.getElementById('auditoriumForm');
    form.action = audSaveRoute;
    document.getElementById('audFormMethod').value = 'POST';

    document.getElementById('audFormTitle').textContent = 'Tambah Auditorium Baru';
    const submitBtn = document.getElementById('audSubmitBtn');
    submitBtn.textContent = 'Simpan Auditorium';
    submitBtn.className = 'aud-btn aud-btn-primary';

    document.getElementById('audNameInput').value = '';
    document.getElementById('audLocationInput').value = '';
    document.getElementById('audCapacityInput').value = '';
    document.getElementById('audFacilitiesInput').value = '';
    document.getElementById('audDescriptionInput').value = '';
    document.getElementById('audImageInput').value = '';

    document.getElementById('audCurrentPhotoWrap').style.display = 'none';
    document.getElementById('audImageLabel').textContent = 'Foto Auditorium';
    document.getElementById('audImageHelp').textContent = 'Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.';
    document.getElementById('audActiveCheckboxWrap').style.display = 'flex';
    document.getElementById('audActiveCheckbox').checked = true;

    showAudPanel();
    setTimeout(() => document.getElementById('audNameInput').focus(), 200);
}

function openEditAuditoriumPanel(btn) {
    clearSelectedAudCards();

    const card = document.getElementById(btn.dataset.cardId);
    if (card) card.classList.add('is-active');

    const form = document.getElementById('auditoriumForm');
    form.action = btn.dataset.updateUrl;
    document.getElementById('audFormMethod').value = 'PUT';

    document.getElementById('audFormTitle').textContent = 'Edit ' + (btn.dataset.name || 'Auditorium');
    const submitBtn = document.getElementById('audSubmitBtn');
    submitBtn.textContent = 'Simpan Perubahan';
    submitBtn.className = 'aud-btn aud-btn-edit';

    document.getElementById('audNameInput').value = btn.dataset.name || '';
    document.getElementById('audLocationInput').value = btn.dataset.location || '';
    document.getElementById('audCapacityInput').value = btn.dataset.capacity || '';
    document.getElementById('audFacilitiesInput').value = btn.dataset.facilities || '';
    document.getElementById('audDescriptionInput').value = btn.dataset.description || '';
    document.getElementById('audImageInput').value = '';

    const photoWrap = document.getElementById('audCurrentPhotoWrap');
    const photoImg = document.getElementById('audCurrentPhotoImg');
    const noPhotoText = document.getElementById('audNoPhotoText');
    photoWrap.style.display = 'block';

    if (btn.dataset.photoUrl) {
        photoImg.src = btn.dataset.photoUrl;
        photoImg.style.display = 'block';
        noPhotoText.style.display = 'none';
    } else {
        photoImg.style.display = 'none';
        noPhotoText.style.display = 'block';
    }

    document.getElementById('audImageLabel').textContent = 'Ganti Foto Auditorium';
    document.getElementById('audImageHelp').textContent = 'Kosongkan jika tidak ingin mengganti foto. Maksimal 2 MB.';
    document.getElementById('audActiveCheckboxWrap').style.display = 'none';

    showAudPanel();
    if (window.innerWidth <= 900) {
        document.getElementById('audFormPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setTimeout(() => document.getElementById('audNameInput').focus(), 200);
}

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('auditoriumStatusModal');
    const message = document.getElementById('audStatusMessage');
    const confirmButton = document.getElementById('audStatusConfirm');
    const cancelButton = document.getElementById('audStatusCancel');
    const form = document.getElementById('auditoriumStatusForm');
    let pendingSwitch = null;

    document.querySelectorAll('[data-auditorium-toggle]').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            pendingSwitch = toggle;
            const willActivate = toggle.checked;
            const auditoriumName = toggle.dataset.auditoriumName || 'auditorium ini';
            message.textContent = willActivate
                ? 'Yakin ingin mengaktifkan ' + auditoriumName + '? Auditorium akan tersedia untuk dipinjam.'
                : 'Yakin ingin menonaktifkan ' + auditoriumName + '? Auditorium tidak akan tersedia untuk peminjaman baru.';
            confirmButton.textContent = willActivate ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan';
            confirmButton.classList.toggle('is-deactivate', !willActivate);
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            confirmButton.focus();
        });
    });

    function closeModal(restoreSwitch) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (restoreSwitch && pendingSwitch) {
            pendingSwitch.checked = pendingSwitch.dataset.currentStatus === '1';
        }
        pendingSwitch = null;
    }

    cancelButton.addEventListener('click', function () { closeModal(true); });
    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal(true);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal(true);
    });
    confirmButton.addEventListener('click', function () {
        if (!pendingSwitch) return;
        form.action = pendingSwitch.dataset.toggleUrl;
        form.submit();
    });
});
</script>

@endsection