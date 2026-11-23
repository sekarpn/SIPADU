@extends('layouts.panel')

@section('panel')

<style>
    .content-page {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        padding: 10px 10px 35px;
        color: #163626;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .content-heading {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 15px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    .content-heading h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.03em;
        color: #163626;
        margin: 0 0 5px;
    }

    .content-heading p {
        font-size: 13px;
        color: #4c6456;
        margin: 0;
    }

    /* TOMBOL CLEAN & SELARAS DENGAN DATA AUDITORIUM */
    .content-btn {
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

    .content-btn:hover {
        transform: translateY(-1px);
    }

    .content-btn-primary {
        background: #237c4d !important;
        color: #fff !important;
        border-color: #237c4d !important;
        padding: 10px 18px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .content-btn-primary:hover {
        background: #1b663f !important;
    }

    .content-btn-secondary {
        background: rgba(255, 255, 255, .85) !important;
        color: #163626 !important;
        border-color: rgba(22, 54, 38, .16) !important;
        padding: 10px 16px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .content-btn-secondary:hover {
        background: #fff !important;
    }

    .content-btn-edit {
        background: #fef3c7 !important;
        color: #92400e !important;
        border-color: #fde68a !important;
    }

    .content-btn-edit:hover {
        background: #fde68a !important;
    }

    .content-btn-edit.is-submit {
        padding: 10px 18px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .content-btn-delete {
        background: #fee2e2 !important;
        color: #b91c1c !important;
        border-color: #fecaca !important;
    }

    .content-btn-delete:hover {
        background: #fecaca !important;
    }

    /* LAYOUT SPLIT: KIRI DAFTAR KONTEN, KANAN FORM PANEL */
    .content-split-layout {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: flex-start !important;
        gap: 0px;
        width: 100% !important;
        box-sizing: border-box !important;
        transition: gap 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .content-split-layout.is-open {
        gap: 24px !important;
    }

    /* KOLOM KIRI: MAKSIMAL 2 KARTU PER BARIS */
    .content-list-col {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        width: 100% !important;
        transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .content-cards {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
        align-items: stretch !important;
        width: 100% !important;
    }

    .content-info-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, .74), rgba(255, 255, 255, .46));
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 18px;
        padding: 20px 22px;
        min-height: 225px;
        box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        box-sizing: border-box !important;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .content-info-card:hover {
        transform: translateY(-2px);
    }

    .content-info-card.is-active {
        border: 2px solid #237c4d !important;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .14) !important;
    }

    .content-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .content-key-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        background: #bbf7d0;
        border: 1px solid #34d399;
        color: #065f46;
    }

    .content-info-card h3 {
        margin: 0 0 8px;
        font-size: 16.5px;
        line-height: 1.35;
        font-weight: 800;
        color: #163626;
    }

    .content-info-card p {
        margin: 0 0 16px;
        font-size: 12.8px;
        line-height: 1.65;
        color: #4c6456;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        word-break: break-word;
    }

    /* FOOTER KARTU CLEAN & SEJAJAR */
    .content-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 8px;
        padding-top: 14px;
        border-top: 1px solid rgba(22, 54, 38, .08);
    }

    .content-actions form {
        margin: 0;
    }

    /* KOLOM KANAN: PANEL FORM TAMBAH / PERBARUI KONTEN */
    .content-form-col {
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

    .content-form-col.is-open {
        flex: 0 0 clamp(440px, 46%, 540px) !important;
        width: clamp(440px, 46%, 540px) !important;
        max-width: 50% !important;
        opacity: 1 !important;
        overflow: visible !important;
        pointer-events: auto !important;
        transform: translateX(0) !important;
    }

    .content-form-card {
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

    .content-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(22, 54, 38, .08);
    }

    .content-form-header h3 {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        color: #163626;
    }

    .content-close-btn {
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

    .content-close-btn:hover {
        background: #fff;
    }

    .content-form-card label {
        display: block;
        margin: 12px 0 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: #294735;
    }

    .content-form-card select,
    .content-form-card input,
    .content-form-card textarea {
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

    .content-form-card select {
        appearance: none !important;
        -webkit-appearance: none !important;
        padding-right: 40px !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23163626' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-size: 15px 15px !important;
        cursor: pointer;
    }

    .content-form-card select:focus,
    .content-form-card input:focus,
    .content-form-card textarea:focus {
        background-color: #fff;
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .content-form-card textarea {
        min-height: 170px !important;
        resize: vertical;
        line-height: 1.65;
    }

    .content-form-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 18px;
    }

    .content-empty {
        background: linear-gradient(145deg, rgba(255, 255, 255, .64), rgba(255, 255, 255, .36));
        border: 1px dashed rgba(22, 54, 38, .22);
        border-radius: 16px;
        padding: 36px;
        text-align: center;
        color: #4c6456;
        grid-column: 1 / -1;
    }

    /* RESPONSIF LAPTOP LAYAR KECIL SAAT FORM TERBUKA */
    @media (max-width: 1220px) {
        .content-split-layout.is-open .content-cards {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 900px) {
        .content-split-layout {
            flex-direction: column-reverse !important;
            gap: 20px !important;
        }

        .content-cards {
            grid-template-columns: 1fr !important;
        }

        .content-form-col {
            display: none !important;
        }

        .content-form-col.is-open {
            display: block !important;
            flex: 1 1 100% !important;
            width: 100% !important;
            max-width: 100% !important;
            position: static !important;
            transform: none !important;
        }

        .content-form-card {
            min-width: 0 !important;
            width: 100% !important;
        }
    }
</style>

<div class="content-page">
    <div class="content-heading">
        <div>
            <h1>Konten Informasi</h1>
            <p>Kelola alur pengajuan, SOP, FAQ, dan contact person pada landing page dan area peminjam.</p>
        </div>

        <button type="button" class="content-btn content-btn-primary" onclick="openAddContentForm()">
            + Tambah Konten
        </button>
    </div>

    <div class="content-split-layout {{ $errors->any() ? 'is-open' : '' }}" id="contentSplitLayout">

        {{-- KIRI: DAFTAR KONTEN (MAKSIMAL 2 KOTAK PER BARIS) --}}
        <div class="content-list-col">
            <div class="content-cards">
                @if($contents->count() > 0)
                    @foreach($contents as $c)
                        <article class="content-info-card" id="contentCard-{{ $c->id }}">
                            <div>
                                <div class="content-card-head">
                                    <span class="content-key-badge">{{ strtoupper($c->key) }}</span>
                                </div>
                                <h3>{{ $c->title }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit($c->body, 220) }}</p>
                            </div>

                            <div class="content-actions">
                                <button
                                    type="button"
                                    class="content-btn content-btn-edit"
                                    data-card-id="contentCard-{{ $c->id }}"
                                    data-key="{{ $c->key }}"
                                    data-title="{{ $c->title }}"
                                    data-body="{{ $c->body }}"
                                    onclick="fillContentForm(this)"
                                >
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('admin.contents.delete', $c) }}" onsubmit="return confirm('Hapus konten ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="content-btn content-btn-delete" data-confirm="Hapus konten ini?">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                @else
                    <div class="content-empty">
                        <h3 style="margin:0 0 6px;color:#163626;">Belum Ada Konten Informasi</h3>
                        <p style="margin:0;font-size:13px;">Silakan klik tombol Tambah Konten untuk menambahkan data.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- KANAN: FORM TAMBAH / PERBARUI KONTEN (TERSEMBUNYI DEFAULT) --}}
        <aside class="content-form-col {{ $errors->any() ? 'is-open' : '' }}" id="contentFormPanel">
            <form class="content-form-card" id="contentForm" method="POST" action="{{ route('admin.contents.save') }}">
                @csrf
                <div class="content-form-header">
                    <h3 id="contentFormHeading">Tambah Konten Baru</h3>
                    <button type="button" class="content-close-btn" onclick="closeContentForm()" title="Tutup">✕</button>
                </div>

                <label for="contentKey">Jenis Konten *</label>
                <select id="contentKey" name="key" required>
                    <option value="alur" @selected(old('key') === 'alur')>Alur Pengajuan</option>
                    <option value="sop" @selected(old('key') === 'sop')>SOP</option>
                    <option value="faq" @selected(old('key') === 'faq')>FAQ</option>
                    <option value="contact" @selected(old('key') === 'contact')>Contact Person</option>
                </select>

                <label for="contentTitle">Judul *</label>
                <input id="contentTitle" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul konten informasi">

                <label for="contentBody">Isi Konten *</label>
                <textarea id="contentBody" name="body" rows="8" required placeholder="Tuliskan detail informasi konten...">{{ old('body') }}</textarea>

                <div class="content-form-footer">
                    <button class="content-btn content-btn-primary" type="submit" id="contentSubmitBtn">Simpan Konten</button>
                    <button class="content-btn content-btn-secondary" type="button" onclick="closeContentForm()">Batal</button>
                </div>
            </form>
        </aside>

    </div>
</div>

<script>
function clearSelectedContentCards() {
    document.querySelectorAll('.content-info-card').forEach(card => {
        card.classList.remove('is-active');
    });
}

function showContentPanel() {
    document.getElementById('contentSplitLayout').classList.add('is-open');
    document.getElementById('contentFormPanel').classList.add('is-open');
}

function closeContentForm() {
    document.getElementById('contentSplitLayout').classList.remove('is-open');
    document.getElementById('contentFormPanel').classList.remove('is-open');
    clearSelectedContentCards();
}

function openAddContentForm() {
    clearSelectedContentCards();
    document.getElementById('contentFormHeading').textContent = 'Tambah Konten Baru';
    const submitBtn = document.getElementById('contentSubmitBtn');
    submitBtn.textContent = 'Simpan Konten';
    submitBtn.className = 'content-btn content-btn-primary';

    document.getElementById('contentKey').value = 'alur';
    document.getElementById('contentTitle').value = '';
    document.getElementById('contentBody').value = '';

    showContentPanel();
    setTimeout(() => document.getElementById('contentTitle').focus(), 200);
}

function fillContentForm(btn) {
    clearSelectedContentCards();
    const card = document.getElementById(btn.dataset.cardId);
    if (card) card.classList.add('is-active');

    document.getElementById('contentFormHeading').textContent = 'Edit ' + (btn.dataset.title || 'Konten');
    const submitBtn = document.getElementById('contentSubmitBtn');
    submitBtn.textContent = 'Simpan Perubahan';
    submitBtn.className = 'content-btn content-btn-edit is-submit';

    document.getElementById('contentKey').value = btn.dataset.key || 'alur';
    document.getElementById('contentTitle').value = btn.dataset.title || '';
    document.getElementById('contentBody').value = btn.dataset.body || '';

    showContentPanel();
    if (window.innerWidth <= 900) {
        document.getElementById('contentFormPanel').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setTimeout(() => document.getElementById('contentTitle').focus(), 200);
}
</script>

@endsection