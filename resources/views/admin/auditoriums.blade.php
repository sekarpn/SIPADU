
@extends('layouts.panel')

@section('panel')

<style>
    .auditorium-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .auditorium-add {
        position: relative;
    }

    .auditorium-add summary {
        cursor: pointer;
        list-style: none;
    }

    .auditorium-add summary::-webkit-details-marker {
        display: none;
    }

    .auditorium-form {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-top: 16px;
    }

    .auditorium-add .auditorium-form {
        position: absolute;
        z-index: 20;
        top: 100%;
        right: 0;
        width: 350px;
        max-width: calc(100vw - 40px);
        max-height: 75vh;
        overflow-y: auto;
        box-shadow: 0 8px 25px rgba(0,0,0,.12);
    }

    .auditorium-form label {
        display: block;
        margin: 13px 0 6px;
        font-weight: 600;
    }

    .auditorium-form input:not([type="checkbox"]):not([type="file"]),
    .auditorium-form textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .auditorium-form textarea {
        min-height: 90px;
        resize: vertical;
    }

    .auditorium-form input[type="file"] {
        width: 100%;
        margin-top: 5px;
    }

    .auditorium-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 22px;
        margin-top: 24px;
    }

    .auditorium-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
    }

    .auditorium-photo {
        width: 100%;
        height: 190px;
        object-fit: cover;
        display: block;
        background: #f3f4f6;
    }

    .auditorium-content {
        padding: 18px;
    }

    .auditorium-content h3 {
        margin: 0 0 10px;
        font-size: 18px;
    }

    .auditorium-info {
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 14px;
    }

    .auditorium-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .auditorium-actions form {
        margin: 0;
    }

    .btn-edit {
        background: #2563eb;
        color: white;
    }

    .btn-delete {
        background: #dc2626;
        color: white;
    }

    .btn-status {
        background: #64748b;
        color: white;
    }

    .status-active {
        color: #15803d;
        background: #dcfce7;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-inactive {
        color: #b91c1c;
        background: #fee2e2;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .auditorium-details {
        margin-top: 15px;
    }

    .auditorium-details summary {
        cursor: pointer;
        font-weight: 600;
        color: #2563eb;
        list-style: none;
        display: inline-block;
    }

    .auditorium-details summary::-webkit-details-marker {
        display: none;
    }

    .auditorium-empty {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 35px;
        text-align: center;
        color: #64748b;
        grid-column: 1 / -1;
    }

    @media (max-width: 600px) {
        .auditorium-add .auditorium-form {
            right: 0;
            width: 290px;
        }
    }
</style>

{{-- JUDUL DAN TOMBOL TAMBAH --}}
<div class="page-heading auditorium-header">
    <div>
        <h1>Data Auditorium</h1>
        <p>Kelola informasi dan data auditorium yang dapat dipinjam.</p>
    </div>

    <details class="auditorium-add">
        <summary class="btn">
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

            <label>
                <input type="checkbox" name="is_active" value="1" checked>
                Aktif
            </label>

            <button type="submit" class="btn" style="margin-top: 15px;">
                Simpan Auditorium
            </button>
        </form>
    </details>
</div>

{{-- DAFTAR AUDITORIUM --}}
<div class="auditorium-grid">

    @forelse($auditoriums as $a)

        <div class="auditorium-card">

            @if($a->image_path)
                <img
                    src="{{ asset('storage/' . $a->image_path) }}"
                    alt="Foto {{ $a->name }}"
                    class="auditorium-photo"
                >
            @else
                <div class="auditorium-photo"
                     style="display:flex;align-items:center;justify-content:center;color:#64748b;">
                    Belum ada foto auditorium
                </div>
            @endif

            <div class="auditorium-content">

                <h3>{{ $a->name }}</h3>

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

                @if($a->is_active)
                    <span class="status-active">Aktif</span>
                @else
                    <span class="status-inactive">Nonaktif</span>
                @endif

                {{-- TOMBOL AKSI --}}
                <div class="auditorium-actions">

                    <form method="POST"
                          action="{{ route('admin.auditoriums.toggle', $a->id) }}">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn btn-status">
                            {{ $a->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>

                    <form method="POST"
                          action="{{ route('admin.auditoriums.delete', $a->id) }}"
                          onsubmit="return confirm('Yakin ingin menghapus auditorium ini?')">

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-delete">
                            Hapus
                        </button>
                    </form>

                </div>

                {{-- FORM EDIT --}}
                <details class="auditorium-details">

                    <summary>Edit Auditorium</summary>

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
                                style="width:150px;height:100px;object-fit:cover;border-radius:8px;margin-bottom:10px;"
                            >
                        @else
                            <p>Belum ada foto.</p>
                        @endif

                        <label>Ganti Foto Auditorium</label>
                        <input type="file"
                               name="image"
                               accept=".jpg,.jpeg,.png,.webp,image/*">

                        <small>
                            Kosongkan jika tidak ingin mengganti foto.
                            Maksimal 2 MB.
                        </small>

                        <label>
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ $a->is_active ? 'checked' : '' }}>
                            Aktif
                        </label>

                        <button type="submit" class="btn btn-edit" style="margin-top: 15px;">
                            Simpan Perubahan
                        </button>

                    </form>
                </details>

            </div>
        </div>

    @empty

        <div class="auditorium-empty">
            <h3>Belum Ada Auditorium</h3>
            <p>Silakan klik tombol Tambah Auditorium untuk menambahkan data.</p>
        </div>

    @endforelse

</div>

<div style="margin-top: 25px;">
    {{ $auditoriums->links() }}
</div>

@endsection
