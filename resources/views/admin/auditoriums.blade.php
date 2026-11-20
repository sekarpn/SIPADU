@extends('layouts.panel')

@section('panel')

<style>
    .auditorium-page {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 10px 35px;
        color: #163626;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .auditorium-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
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

    .aud-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 14px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        box-shadow: none !important;
        transition: background .2s ease, transform .18s ease;
    }

    .aud-btn:hover {
        transform: translateY(-1px);
    }

    .aud-btn-primary {
        background: #237c4d !important;
        color: #fff !important;
        border-color: #237c4d !important;
        padding: 10px 17px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .aud-btn-primary:hover {
        background: #1b663f !important;
    }

    .aud-btn-edit {
        background: #eab308 !important;
        color: #29220a !important;
        border-color: #eab308 !important;
    }

    .aud-btn-edit:hover {
        background: #d99b06 !important;
    }

    .aud-btn-delete {
        background: #dc2626 !important;
        background-color: #dc2626 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
    }

    .aud-btn-delete:hover {
        background: #b91c1c !important;
        background-color: #b91c1c !important;
        color: #ffffff !important;
        border-color: #b91c1c !important;
    }

    .aud-btn-status {
        background: rgba(255, 255, 255, .82) !important;
        border-color: rgba(22, 54, 38, .14) !important;
        color: #294735 !important;
    }

    .aud-btn-status:hover {
        background: #fff !important;
    }

    .auditorium-add {
        position: relative;
    }

    .auditorium-add summary {
        list-style: none;
    }

    .auditorium-add summary::-webkit-details-marker {
        display: none;
    }

    .auditorium-form {
        background: #fff;
        border: 1px solid rgba(22, 54, 38, .12);
        border-radius: 14px;
        padding: 20px;
        margin-top: 14px;
    }

    .auditorium-add .auditorium-form {
        position: absolute;
        z-index: 20;
        top: calc(100% + 8px);
        right: 0;
        width: 360px;
        max-width: calc(100vw - 40px);
        max-height: 75vh;
        overflow-y: auto;
        margin-top: 0;
        box-shadow: 0 18px 45px rgba(12, 32, 21, .16);
    }

    .auditorium-form h3 {
        margin: 0 0 8px;
        font-size: 16px;
        font-weight: 800;
        color: #163626;
    }

    .auditorium-form label {
        display: block;
        margin: 12px 0 6px;
        font-size: 12px;
        font-weight: 700;
        color: #294735;
    }

    .auditorium-form input:not([type="checkbox"]):not([type="file"]),
    .auditorium-form textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d8e1db;
        border-radius: 10px;
        font-family: inherit;
        font-size: 13px;
        color: #163626;
        box-sizing: border-box;
        outline: none;
    }

    .auditorium-form input:focus,
    .auditorium-form textarea:focus {
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .auditorium-form textarea {
        min-height: 85px;
        resize: vertical;
    }

    .auditorium-form input[type="file"] {
        width: 100%;
        margin-top: 5px;
        font-size: 12px;
    }

    .auditorium-form small {
        display: block;
        margin-top: 5px;
        color: #64786c;
        font-size: 11px;
    }

    .auditorium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 10px;
    }

    .auditorium-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, .68), rgba(255, 255, 255, .40));
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, .55);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
        display: flex;
        flex-direction: column;
    }

    .auditorium-photo {
        width: 100%;
        height: 190px;
        object-fit: cover;
        display: block;
        background: rgba(255, 255, 255, .5);
    }

    .auditorium-content {
        padding: 18px 20px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
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
        font-size: 17px;
        font-weight: 800;
        color: #163626;
    }

    .auditorium-info {
        color: #4c6456;
        font-size: 12.5px;
        line-height: 1.65;
        margin-bottom: 16px;
        flex: 1;
    }

    .auditorium-info strong {
        color: #163626;
    }

    .auditorium-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        padding-top: 14px;
        border-top: 1px solid rgba(22, 54, 38, .08);
    }

    .auditorium-actions form {
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

    .auditorium-details {
        width: 100%;
    }

    .auditorium-details summary {
        list-style: none;
    }

    .auditorium-details summary::-webkit-details-marker {
        display: none;
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

    @media (max-width: 600px) {
        .auditorium-page { padding: 8px 4px 28px; }
        .auditorium-add .auditorium-form {
            right: 0;
            width: 290px;
        }
    }

    .auditorium-status-control { display: inline-flex; align-items: center; gap: 9px; color: #294735; font-size: 12px; font-weight: 600; }
    .auditorium-switch { position: relative; display: inline-block; width: 46px; height: 26px; cursor: pointer; }
    .auditorium-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .auditorium-switch-track { position: absolute; inset: 0; border-radius: 99px; background: #cbd5cf; transition: background .2s ease; }
    .auditorium-switch-track::before { content: ''; position: absolute; width: 20px; height: 20px; left: 3px; top: 3px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.18); transition: transform .2s ease; }
    .auditorium-switch input:checked + .auditorium-switch-track { background: #237c4d; }
    .auditorium-switch input:checked + .auditorium-switch-track::before { transform: translateX(20px); }
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

</style>

<div class="auditorium-page">
    <div class="auditorium-header">
        <div>
            <h1>Data Auditorium</h1>
            <p>Kelola informasi dan data auditorium yang dapat dipinjam.</p>
        </div>

        <details class="auditorium-add">
            <summary class="aud-btn aud-btn-primary">
                + Tambah Auditorium
            </summary>

            <form class="auditorium-form"
                  method="POST"
                  action="{{ route('admin.auditoriums.save') }}"
                  enctype="multipart/form-data">
                @csrf

                <h3>Tambah Auditorium Baru</h3>

                <label>Nama Auditorium *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>

                <label>Lokasi</label>
                <input type="text" name="location" value="{{ old('location') }}">

                <label>Kapasitas</label>
                <input type="number" name="capacity" min="1" value="{{ old('capacity') }}">

                <label>Fasilitas</label>
                <input type="text" name="facilities" value="{{ old('facilities') }}"
                       placeholder="Contoh: AC, Proyektor, Sound System">

                <label>Deskripsi</label>
                <textarea name="description">{{ old('description') }}</textarea>

                <label>Foto Auditorium</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/*">
                <small>Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</small>

                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    Aktif
                </label>

                <button type="submit" class="aud-btn aud-btn-primary" style="margin-top: 14px; width: 100%;">
                    Simpan Auditorium
                </button>
            </form>
        </details>
    </div>

    <div class="auditorium-grid">
        @if($auditoriums->count() > 0)
            @foreach($auditoriums as $a)
                <div class="auditorium-card">
                    @if($a->image_path)
                        <img
                            src="{{ asset('storage/' . $a->image_path) }}"
                            alt="Foto {{ $a->name }}"
                            class="auditorium-photo"
                        >
                    @else
                        <div class="auditorium-photo"
                             style="display:flex;align-items:center;justify-content:center;color:#64786c;font-size:12.5px;">
                            Belum ada foto auditorium
                        </div>
                    @endif

                    <div class="auditorium-content">
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
                                {{ implode(', ', $a->facilities ?? []) ?: 'Belum diisi' }}
                            </div>
                            <div>
                                <strong>Deskripsi:</strong>
                                {{ $a->description ?: 'Belum ada deskripsi' }}
                            </div>
                        </div>

                        <div class="auditorium-actions">
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

                            <form method="POST"
                                  action="{{ route('admin.auditoriums.delete', $a->id) }}"
                                  onsubmit="return confirm('Yakin ingin menghapus auditorium ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="aud-btn aud-btn-delete">
                                    Hapus
                                </button>
                            </form>

                            <details class="auditorium-details">
                                <summary class="aud-btn aud-btn-edit">Edit</summary>

                                <form class="auditorium-form"
                                      method="POST"
                                      action="{{ route('admin.auditoriums.update', $a->id) }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <h3>Edit {{ $a->name }}</h3>

                                    <label>Nama Auditorium *</label>
                                    <input type="text" name="name" value="{{ $a->name }}" required>

                                    <label>Lokasi</label>
                                    <input type="text" name="location" value="{{ $a->location }}">

                                    <label>Kapasitas</label>
                                    <input type="number" name="capacity" min="1" value="{{ $a->capacity }}">

                                    <label>Fasilitas</label>
                                    <input type="text"
                                           name="facilities"
                                           value="{{ implode(', ', $a->facilities ?? []) }}">

                                    <label>Deskripsi</label>
                                    <textarea name="description">{{ $a->description }}</textarea>

                                    <label>Foto Auditorium Saat Ini</label>
                                    @if($a->image_path)
                                        <img
                                            src="{{ asset('storage/' . $a->image_path) }}"
                                            alt="Foto {{ $a->name }}"
                                            style="width:150px;height:100px;object-fit:cover;border-radius:8px;margin-bottom:8px;display:block;"
                                        >
                                    @else
                                        <p style="font-size:12px;color:#64786c;margin:4px 0;">Belum ada foto.</p>
                                    @endif

                                    <label>Ganti Foto Auditorium</label>
                                    <input type="file"
                                           name="image"
                                           accept=".jpg,.jpeg,.png,.webp,image/*">
                                    <small>Kosongkan jika tidak ingin mengganti foto. Maksimal 2 MB.</small>

                                    <button type="submit" class="aud-btn aud-btn-edit" style="margin-top: 14px; width: 100%;">
                                        Simpan Perubahan
                                    </button>
                                </form>
                            </details>
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