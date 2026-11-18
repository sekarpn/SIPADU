@extends('layouts.panel')
@section('panel')
<style>
.content-page {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 10px 35px;
    color: #163626;
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

.content-heading {
    margin-bottom: 22px;
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

.content-form-card {
    background: linear-gradient(145deg, rgba(255, 255, 255, .68), rgba(255, 255, 255, .40));
    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);
    border: 1px solid rgba(255, 255, 255, .55);
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
    margin-bottom: 22px;
}

.content-form-card h3 {
    margin: 0 0 14px;
    font-size: 16px;
    font-weight: 800;
    color: #163626;
}

.content-form-card label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #294735;
    margin: 13px 0 6px;
}

.content-form-card select,
.content-form-card input,
.content-form-card textarea {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 13px;
    border: 1px solid rgba(22, 54, 38, .13);
    border-radius: 11px;
    background: rgba(255, 255, 255, .82);
    font: inherit;
    font-size: 13px;
    color: #163626;
    outline: none;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.content-form-card select:focus,
.content-form-card input:focus,
.content-form-card textarea:focus {
    background: #fff;
    border-color: #237c4d;
    box-shadow: 0 0 0 3px rgba(35, 124, 77, .14);
}

.content-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
}

.content-info-card {
    background: linear-gradient(145deg, rgba(255, 255, 255, .68), rgba(255, 255, 255, .40));
    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);
    border: 1px solid rgba(255, 255, 255, .55);
    border-radius: 16px;
    padding: 18px 20px;
    box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.content-key-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    background: rgba(35, 124, 77, .14);
    color: #1f7a49;
    margin-bottom: 8px;
}

.content-info-card h3 {
    margin: 0 0 8px;
    font-size: 16px;
    font-weight: 800;
    color: #163626;
}

.content-info-card p {
    margin: 0 0 16px;
    font-size: 12.5px;
    line-height: 1.6;
    color: #4c6456;
}

.content-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid rgba(22, 54, 38, .08);
}

.content-actions form {
    margin: 0;
}

.content-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 14px;
    border: 1px solid transparent;
    border-radius: 10px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: none !important;
    transition: background .2s ease, transform .18s ease;
}

.content-btn:hover {
    transform: translateY(-1px);
}

.content-btn.primary {
    background: #237c4d !important;
    color: #fff !important;
    border-color: #237c4d !important;
    padding: 10px 18px;
    font-size: 12.5px;
    margin-top: 14px;
}

.content-btn.primary:hover {
    background: #1b663f !important;
}

.content-btn.edit {
    background: #eab308 !important;
    color: #29220a !important;
    border-color: #eab308 !important;
}

.content-btn.edit:hover {
    background: #d99b06 !important;
}

.content-btn.danger {
    background: #dc2626 !important;
    background-color: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

.content-btn.danger:hover {
    background: #b91c1c !important;
    background-color: #b91c1c !important;
    color: #ffffff !important;
    border-color: #b91c1c !important;
}
</style>

<div class="content-page">
    <div class="content-heading">
        <div>
            <h1>Konten Informasi</h1>
            <p>Kelola alur pengajuan, SOP, FAQ, dan contact person pada landing page dan area peminjam.</p>
        </div>
    </div>

    <form class="content-form-card" id="contentForm" method="POST" action="{{ route('admin.contents.save') }}">
        @csrf
        <h3>Tambah / Perbarui Konten</h3>

        <label for="contentKey">Jenis Konten</label>
        <select id="contentKey" name="key" required>
            <option value="alur">Alur Pengajuan</option>
            <option value="sop">SOP</option>
            <option value="faq">FAQ</option>
            <option value="contact">Contact Person</option>
        </select>

        <label for="contentTitle">Judul</label>
        <input id="contentTitle" name="title" required placeholder="Masukkan judul konten informasi">

        <label for="contentBody">Isi Konten</label>
        <textarea id="contentBody" name="body" rows="5" required placeholder="Tuliskan detail informasi konten..."></textarea>

        <button class="content-btn primary" type="submit">Simpan Konten</button>
    </form>

    <div class="content-cards">
        @foreach($contents as $c)
            <article class="content-info-card">
                <div>
                    <span class="content-key-badge">{{ strtoupper($c->key) }}</span>
                    <h3>{{ $c->title }}</h3>
                    <p>{{ \Illuminate\Support\Str::limit($c->body, 220) }}</p>
                </div>

                <div class="content-actions">
                    <button
                        type="button"
                        class="content-btn edit"
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
                        <button type="submit" class="content-btn danger" data-confirm="Hapus konten ini?">
                            Hapus
                        </button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
</div>

<script>
function fillContentForm(btn) {
    const form = document.getElementById('contentForm');
    if (!form) return;
    document.getElementById('contentKey').value = btn.dataset.key || 'alur';
    document.getElementById('contentTitle').value = btn.dataset.title || '';
    document.getElementById('contentBody').value = btn.dataset.body || '';
    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    document.getElementById('contentTitle').focus();
}
</script>
@endsection