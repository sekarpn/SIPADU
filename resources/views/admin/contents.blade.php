@extends('layouts.panel')

@section('panel')

<style>
    .content-page {
        --cp-ink: #163626;
        --cp-muted: #4c6456;
        --cp-subtle: #64786c;
        --cp-green: #1f7a49;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        padding: 10px 10px 35px;
        color: var(--cp-ink);
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .content-page,
    .content-page button,
    .content-page input,
    .content-page select,
    .content-page textarea,
    .sipadu-content-panel,
    .sipadu-content-panel button,
    .sipadu-content-panel input,
    .sipadu-content-panel select,
    .sipadu-content-panel textarea {
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
        font-size: 25px;
        font-weight: 800;
        letter-spacing: -.03em;
        color: var(--cp-ink);
        margin: 0 0 5px;
    }

    .content-heading p {
        font-size: 13px;
        color: var(--cp-muted);
        margin: 0;
    }

    /* TOMBOL CLEAN & SELARAS DENGAN KELOLA PEMINJAMAN */
    .content-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 8px 15px;
        border: 1px solid transparent;
        border-radius: 11px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        flex-shrink: 0;
        cursor: pointer;
        box-shadow: none !important;
        transition: background .2s ease, transform .15s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .content-btn:hover {
        transform: translateY(-1px);
    }

    .content-btn-primary {
        background: #1f7a49 !important;
        color: #ffffff !important;
        border-color: #1f7a49 !important;
        padding: 10px 18px;
        font-size: 12.5px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(31, 122, 73, .22) !important;
    }

    .content-btn-primary:hover {
        background: #176139 !important;
        border-color: #176139 !important;
    }

    .content-btn-secondary {
        background: #ffffff !important;
        color: #294735 !important;
        border-color: rgba(22, 54, 38, .15) !important;
        padding: 10px 16px;
        font-size: 12.5px;
        border-radius: 12px;
    }

    .content-btn-secondary:hover {
        background: #f4f8f5 !important;
    }

    .content-btn-edit {
        background: #fef3c7 !important;
        color: #92400e !important;
        border-color: rgba(217, 119, 6, .24) !important;
    }

    .content-btn-edit:hover {
        background: #fde68a !important;
    }

    .content-btn-delete {
        background: #fee2e2 !important;
        color: #b91c1c !important;
        border-color: rgba(220, 38, 38, .22) !important;
    }

    .content-btn-delete:hover {
        background: #fecaca !important;
    }

    /* GRID DAFTAR KONTEN (2 KOLOM PENUH & SEIMBANG) */
    .content-cards {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
        align-items: stretch !important;
        width: 100% !important;
    }

    .content-info-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, .72), rgba(255, 255, 255, .44));
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, .60);
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
        box-shadow: 0 12px 30px rgba(22, 54, 38, .09);
    }

    .content-info-card.is-active {
        border: 1.5px solid #2b8a57 !important;
        box-shadow: 0 6px 20px rgba(31, 122, 73, .12) !important;
    }

    .content-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .content-key-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
        background: #dcfce7;
        border: 1px solid rgba(22, 163, 74, .22);
        color: #166534;
    }

    .content-key-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .content-info-card h3 {
        margin: 0 0 8px;
        font-size: 16.5px;
        line-height: 1.35;
        font-weight: 800;
        color: var(--cp-ink);
    }

    .content-info-card p {
        margin: 0 0 16px;
        font-size: 12.8px;
        line-height: 1.65;
        color: var(--cp-muted);
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

    .content-empty {
        background: linear-gradient(145deg, rgba(255, 255, 255, .64), rgba(255, 255, 255, .36));
        border: 1px dashed rgba(22, 54, 38, .22);
        border-radius: 16px;
        padding: 36px;
        text-align: center;
        color: var(--cp-muted);
        grid-column: 1 / -1;
    }

    /* ==========================================================================
       POPUP / DRAWER SAMPING KONTEN INFORMASI (IDENTIK KELOLA PEMINJAMAN)
       ========================================================================== */
    .sipadu-content-overlay {
        position: fixed;
        inset: 0;
        z-index: 99990;
        background: rgba(15, 32, 23, .36);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        display: none;
    }

    .sipadu-content-overlay.is-open {
        display: block;
    }

    .sipadu-content-panel {
        position: fixed;
        top: 12px;
        right: -540px;
        bottom: 12px;
        z-index: 99995;
        width: min(490px, calc(100vw - 24px));
        background: #ffffff;
        border-radius: 18px;
        box-shadow: -14px 0 44px rgba(12, 32, 21, .2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: right .26s cubic-bezier(.22, .61, .36, 1);
        color: #163626;
    }

    .sipadu-content-panel.is-open {
        right: 12px;
    }

    .sipadu-content-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px 15px;
        background: #ffffff;
        border-bottom: 1px solid rgba(22, 54, 38, .08);
        flex-shrink: 0;
    }

    .sipadu-content-panel-header h2 {
        margin: 0;
        color: #163626;
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.25;
    }

    .sipadu-content-panel-header p {
        margin: 4px 0 0;
        color: #6b7f73;
        font-size: 11.5px;
        font-weight: 500;
    }

    .sipadu-content-panel-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        padding: 0;
        flex-shrink: 0;
        border: 1px solid rgba(22, 54, 38, .12);
        border-radius: 999px;
        background: #ffffff;
        color: #52665a;
        line-height: 0;
        cursor: pointer;
        transition: background .2s ease, color .2s ease;
    }

    .sipadu-content-panel-close svg {
        width: 15px;
        height: 15px;
        display: block;
    }

    .sipadu-content-panel-close:hover {
        background: #f1f6f2;
        color: #163626;
    }

    .sipadu-content-panel-body {
        flex: 1;
        overflow-y: auto;
        padding: 18px 22px 22px;
        background: #ffffff;
    }

    /* KARTU FORM DI DALAM POPUP (BERBORDER HIJAU TIPIS SEPERTI KELOLA PEMINJAMAN) */
    .content-drawer-card {
        padding: 18px;
        border: 1.5px solid #2b8a57;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 4px 16px rgba(31, 122, 73, .05);
    }

    .content-drawer-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(22, 54, 38, .08);
    }

    .content-drawer-code {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #1f7a49;
    }

    .content-drawer-card label {
        display: block;
        margin: 13px 0 6px;
        font-size: 12px;
        font-weight: 700;
        color: #163626;
    }

    .content-drawer-card label .req {
        color: #dc2626;
        margin-left: 2px;
    }

    .content-drawer-card select,
    .content-drawer-card input,
    .content-drawer-card textarea {
        width: 100% !important;
        padding: 10px 13px;
        border: 1px solid #d5e0d9;
        border-radius: 11px;
        background: #ffffff;
        font-family: inherit;
        font-size: 12.5px;
        color: #163626;
        box-sizing: border-box !important;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .content-drawer-card select {
        appearance: none !important;
        -webkit-appearance: none !important;
        padding-right: 40px !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23163626' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 14px center !important;
        background-size: 15px 15px !important;
        cursor: pointer;
    }

    .content-drawer-card select:focus,
    .content-drawer-card input:focus,
    .content-drawer-card textarea:focus {
        border-color: #1f7a49;
        box-shadow: 0 0 0 3px rgba(31, 122, 73, .12);
    }

    .content-drawer-card textarea {
        min-height: 185px !important;
        resize: vertical;
        line-height: 1.65;
    }

    .content-drawer-hint {
        margin-top: 14px;
        padding: 12px 13px;
        border-radius: 12px;
        background: #f2f9f5;
        border: 1px solid rgba(31, 122, 73, .14);
        font-size: 11.5px;
        line-height: 1.5;
        color: #294735;
    }

    .content-drawer-hint strong {
        color: #163626;
    }

    /* FOOTER POPUP (TOMBOL BERTINGKAT SEPERTI KELOLA PEMINJAMAN) */
    .sipadu-content-panel-footer {
        padding: 14px 22px 18px;
        border-top: 1px solid rgba(22, 54, 38, .08);
        background: #ffffff;
        flex-shrink: 0;
    }

    .content-footer-stack {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .content-footer-stack .content-btn {
        width: 100%;
        min-height: 42px;
        border-radius: 12px;
        font-size: 12.5px;
    }

    body.content-drawer-open {
        overflow: hidden;
    }

    @media (max-width: 900px) {
        .content-cards {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 600px) {
        .sipadu-content-panel {
            top: 0;
            bottom: 0;
            width: 100vw;
            border-radius: 0;
        }

        .sipadu-content-panel.is-open {
            right: 0;
        }

        .sipadu-content-panel-header {
            padding: 16px 18px;
        }

        .sipadu-content-panel-body {
            padding: 16px 18px;
        }

        .sipadu-content-panel-footer {
            padding: 14px 18px;
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

    {{-- DAFTAR KONTEN (2 KOLOM) --}}
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

{{-- OVERLAY BLUR & POPUP DRAWER SAMPING KANAN --}}
<div class="sipadu-content-overlay {{ $errors->any() ? 'is-open' : '' }}" id="contentOverlay" onclick="closeContentForm()"></div>

<aside
    class="sipadu-content-panel {{ $errors->any() ? 'is-open' : '' }}"
    id="contentFormPanel"
    role="dialog"
    aria-modal="true"
    aria-labelledby="contentFormHeading"
>
    <header class="sipadu-content-panel-header">
        <div>
            <h2 id="contentFormHeading">Tambah Konten Baru</h2>
            <p id="contentFormSubtitle">Atur informasi yang ditampilkan pada halaman utama dan peminjam.</p>
        </div>
        <button type="button" class="sipadu-content-panel-close" onclick="closeContentForm()" aria-label="Tutup">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
            </svg>
        </button>
    </header>

    <form id="contentForm" method="POST" action="{{ route('admin.contents.save') }}" style="display:flex; flex-direction:column; flex:1; min-height:0;">
        @csrf
        <div class="sipadu-content-panel-body">
            <div class="content-drawer-card">
                <div class="content-drawer-card-top">
                    <span class="content-drawer-code" id="contentDrawerModeLabel">FORMULIR KONTEN • BARU</span>
                    <span class="content-key-badge" id="contentDrawerKeyBadge">ALUR</span>
                </div>

                <label for="contentKey">Jenis Konten <span class="req">*</span></label>
                <select id="contentKey" name="key" required onchange="syncContentKeyBadge()">
                    <option value="alur" @selected(old('key') === 'alur')>Alur Pengajuan</option>
                    <option value="sop" @selected(old('key') === 'sop')>SOP</option>
                    <option value="faq" @selected(old('key') === 'faq')>FAQ</option>
                    <option value="contact" @selected(old('key') === 'contact')>Contact Person</option>
                </select>

                <label for="contentTitle">Judul <span class="req">*</span></label>
                <input id="contentTitle" name="title" value="{{ old('title') }}" required placeholder="Masukkan judul konten informasi">

                <label for="contentBody">Isi Konten <span class="req">*</span></label>
                <textarea id="contentBody" name="body" rows="8" required placeholder="Tuliskan detail informasi konten...">{{ old('body') }}</textarea>

                <div class="content-drawer-hint">
                    <strong>Informasi Publikasi:</strong> Perubahan konten akan langsung diperbarui dan tampil pada landing page serta portal peminjam SIPADU.
                </div>
            </div>
        </div>

        <div class="sipadu-content-panel-footer">
            <div class="content-footer-stack">
                <button class="content-btn content-btn-primary" type="submit" id="contentSubmitBtn">
                    ✓ Simpan Konten
                </button>
                <button class="content-btn content-btn-secondary" type="button" onclick="closeContentForm()">
                    Batal
                </button>
            </div>
        </div>
    </form>
</aside>

<script>
function clearSelectedContentCards() {
    document.querySelectorAll('.content-info-card').forEach(card => {
        card.classList.remove('is-active');
    });
}

function syncContentKeyBadge() {
    const keySelect = document.getElementById('contentKey');
    const badge = document.getElementById('contentDrawerKeyBadge');
    if (keySelect && badge) {
        badge.textContent = (keySelect.value || 'ALUR').toUpperCase();
    }
}

function showContentPanel() {
    document.getElementById('contentOverlay')?.classList.add('is-open');
    document.getElementById('contentFormPanel')?.classList.add('is-open');

    const pageBody = document.body;
    if (!pageBody.classList.contains('content-drawer-open')) {
        pageBody.dataset.cpOriginalPaddingRight = pageBody.style.paddingRight;
        const scrollbarWidth = Math.max(0, window.innerWidth - document.documentElement.clientWidth);
        const currentPaddingRight = parseFloat(window.getComputedStyle(pageBody).paddingRight) || 0;
        pageBody.style.paddingRight = (currentPaddingRight + scrollbarWidth) + 'px';
        pageBody.classList.add('content-drawer-open');
    }
}

function closeContentForm() {
    document.getElementById('contentOverlay')?.classList.remove('is-open');
    document.getElementById('contentFormPanel')?.classList.remove('is-open');
    clearSelectedContentCards();

    const pageBody = document.body;
    if (pageBody.classList.contains('content-drawer-open')) {
        pageBody.classList.remove('content-drawer-open');
        pageBody.style.paddingRight = pageBody.dataset.cpOriginalPaddingRight || '';
        delete pageBody.dataset.cpOriginalPaddingRight;
    }
}

function openAddContentForm() {
    clearSelectedContentCards();
    document.getElementById('contentFormHeading').textContent = 'Tambah Konten Baru';
    document.getElementById('contentFormSubtitle').textContent = 'Tambahkan panduan alur, SOP, FAQ, atau kontak baru.';
    document.getElementById('contentDrawerModeLabel').textContent = 'FORMULIR KONTEN • BARU';

    const submitBtn = document.getElementById('contentSubmitBtn');
    submitBtn.textContent = '✓ Simpan Konten';
    submitBtn.className = 'content-btn content-btn-primary';

    document.getElementById('contentKey').value = 'alur';
    document.getElementById('contentTitle').value = '';
    document.getElementById('contentBody').value = '';
    syncContentKeyBadge();

    showContentPanel();
    setTimeout(() => document.getElementById('contentTitle')?.focus(), 200);
}

function fillContentForm(btn) {
    clearSelectedContentCards();
    const card = document.getElementById(btn.dataset.cardId);
    if (card) card.classList.add('is-active');

    const keyVal = btn.dataset.key || 'alur';
    document.getElementById('contentFormHeading').textContent = 'Edit Konten Informasi';
    document.getElementById('contentFormSubtitle').textContent = '#' + keyVal.toUpperCase() + ' • ' + (btn.dataset.title || 'Konten');
    document.getElementById('contentDrawerModeLabel').textContent = 'TINJAU & EDIT • #' + keyVal.toUpperCase();

    const submitBtn = document.getElementById('contentSubmitBtn');
    submitBtn.textContent = '✓ Simpan Perubahan';
    submitBtn.className = 'content-btn content-btn-primary';

    document.getElementById('contentKey').value = keyVal;
    document.getElementById('contentTitle').value = btn.dataset.title || '';
    document.getElementById('contentBody').value = btn.dataset.body || '';
    syncContentKeyBadge();

    showContentPanel();
    setTimeout(() => document.getElementById('contentTitle')?.focus(), 200);
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && document.getElementById('contentFormPanel')?.classList.contains('is-open')) {
        closeContentForm();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    syncContentKeyBadge();
    @if($errors->any())
        showContentPanel();
    @endif
});
</script>

@endsection