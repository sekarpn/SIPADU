@extends('layouts.panel')

@section('panel')
@php
    $userCollection = collect($users);
    $countAll = $roleCounts['all'] ?? $userCollection->count();
    $countMahasiswa = $roleCounts['mahasiswa'] ?? $userCollection->where('role', 'mahasiswa')->count();
    $countPegawai = $roleCounts['pegawai'] ?? $userCollection->where('role', 'pegawai')->count();
    $countAdmin = $roleCounts['admin'] ?? $userCollection->where('role', 'admin')->count();
    $activeRole = request('role', '');
@endphp

<style>
.user-page {
    --up-ink: #163626;
    --up-muted: #4c6456;
    --up-subtle: #64786c;
    --up-line: rgba(22, 54, 38, .09);
    --up-green: #1f7a49;
    --up-green-mid: #237c4d;
    --up-glass-bg: linear-gradient(
        145deg,
        rgba(255, 255, 255, .68) 0%,
        rgba(255, 255, 255, .42) 100%
    );
    --up-glass-border: 1px solid rgba(255, 255, 255, .55);
    --up-glass-shadow: 0 8px 28px rgba(22, 54, 38, .06);
    color: var(--up-ink);
    width: 100%;
    box-sizing: border-box;
}

.user-page,
.user-page button,
.user-page input,
.user-page select,
.user-page textarea {
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

/* PAGE HEADER */
.user-page .up-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 18px;
    flex-wrap: wrap;
    margin: 0 0 22px;
}

.user-page .up-heading h1 {
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -.03em;
    margin: 0;
    color: var(--up-ink);
}

.user-page .up-heading p {
    margin: 6px 0 0;
    color: var(--up-muted);
    font-size: 13px;
    line-height: 1.55;
}

/* TOMBOL CRUD */
.user-page .up-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 15px;
    border: 1px solid transparent;
    border-radius: 11px;
    background: #edf1f4;
    color: #34424e;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    box-shadow: none !important;
    cursor: pointer;
    white-space: nowrap;
    transition: background .18s ease, color .18s ease, border-color .18s ease, transform .18s ease;
}

.user-page .up-btn svg {
    width: 15px;
    height: 15px;
}

.user-page .up-btn:hover {
    transform: translateY(-1px);
    box-shadow: none !important;
}

.user-page .up-btn.primary {
    background: #237c4d !important;
    color: #fff !important;
    border-color: #237c4d !important;
    padding: 10px 16px;
    font-size: 12.5px;
    box-shadow: 0 4px 12px rgba(35, 124, 77, .22) !important;
}

.user-page .up-btn.primary:hover {
    background: #1b663f !important;
    color: #fff !important;
}

.user-page .up-btn.detail {
    background: #237c4d !important;
    color: #fff !important;
    border-color: #237c4d !important;
}

.user-page .up-btn.detail:hover {
    background: #1b663f !important;
    color: #fff !important;
}

.user-page .up-btn.edit {
    background: #eab308 !important;
    color: #29220a !important;
    border-color: #eab308 !important;
}

.user-page .up-btn.edit:hover {
    background: #d99b06 !important;
    color: #29220a !important;
}

.user-page .up-btn.danger {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

.user-page .up-btn.danger:hover {
    background: #b91c1c !important;
    color: #ffffff !important;
    border-color: #b91c1c !important;
}

.user-page .up-btn.danger:disabled {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
    opacity: .45 !important;
    cursor: not-allowed;
    transform: none;
}

.user-page .up-btn.secondary {
    background: rgba(255, 255, 255, .82) !important;
    color: #294735 !important;
    border-color: rgba(22, 54, 38, .14) !important;
}

.user-page .up-btn.secondary:hover {
    background: #fff !important;
    color: #163626 !important;
}

/* ALERT BANNERS */
.user-page .up-alert {
    border-radius: 14px;
    padding: 13px 16px;
    margin-bottom: 16px;
    font-size: 12.5px;
    font-weight: 600;
}

.user-page .up-alert.success {
    background: rgba(220, 252, 231, .88);
    color: #166534;
    border: 1px solid rgba(74, 222, 128, .35);
}

.user-page .up-alert.error {
    background: rgba(254, 226, 226, .88);
    color: #991b1b;
    border: 1px solid rgba(248, 113, 113, .35);
}

/* MAIN TABLE CARD */
.user-page .up-card {
    background: var(--up-glass-bg);
    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);
    border: var(--up-glass-border);
    box-shadow: var(--up-glass-shadow);
    border-radius: 16px;
    overflow: hidden;
}

/* TOOLBAR SEARCH */
.user-page .up-toolbar {
    padding: 16px 18px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    border-bottom: 1px solid var(--up-line);
    background: rgba(255, 255, 255, .25);
}

.user-page .up-search {
    position: relative;
    flex: 1 1 280px;
}

.user-page .up-search input {
    width: 100%;
    height: 40px;
    padding: 0 34px 0 38px;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 12px;
    background: rgba(255, 255, 255, .78);
    color: var(--up-ink);
    font-size: 12.5px;
    font-weight: 500;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.user-page .up-search input:focus {
    background: #fff;
    border-color: var(--up-green);
    box-shadow: 0 0 0 3px rgba(31, 122, 73, .14);
}

.user-page .up-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--up-subtle);
    display: inline-flex;
    pointer-events: none;
}

.user-page .up-search-icon svg {
    width: 16px;
    height: 16px;
}

.user-page .up-search-clear {
    position: absolute;
    right: 9px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: rgba(22, 54, 38, .08);
    color: var(--up-muted);
    line-height: 0;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .18s ease, color .18s ease;
}

.user-page .up-search-clear.show {
    display: inline-flex;
}

.user-page .up-search-clear svg {
    width: 13px;
    height: 13px;
    display: block;
}

.user-page .up-search-clear:hover {
    background: rgba(31, 122, 73, .16);
    color: var(--up-green);
}

.user-page .up-reset {
    font-size: 12px;
    font-weight: 700;
    color: var(--up-green);
    padding: 10px 12px;
    border-radius: 10px;
    text-decoration: none;
    transition: background .2s ease;
}

.user-page .up-reset:hover {
    background: rgba(33, 128, 77, .12);
}

/* ROLE FILTER TABS */
.user-page .up-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding: 0 18px;
    border-bottom: 1px solid rgba(22, 54, 38, .12);
    background: rgba(255, 255, 255, .18);
}

.user-page .up-tab {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 14px 18px;
    margin: 0;
    border: 0 !important;
    border-bottom: 2.5px solid transparent !important;
    border-radius: 0 !important;
    background: transparent !important;
    box-shadow: none !important;
    font-size: 13px;
    font-weight: 500;
    color: #4c6456 !important;
    white-space: nowrap;
    text-decoration: none;
    cursor: pointer;
    transition: color .2s ease, border-color .2s ease;
}

.user-page .up-tab:hover:not(.active) {
    background: transparent !important;
    color: #163626 !important;
    transform: none !important;
}

.user-page .up-tab.active {
    background: transparent !important;
    color: #163626 !important;
    font-weight: 700;
    border-bottom: 2.5px solid #237c4d !important;
}

.user-page .up-tab span {
    font-size: 12px;
    font-weight: 600;
    color: #64786c;
}

.user-page .up-tab span::before {
    content: "(";
}

.user-page .up-tab span::after {
    content: ")";
}

.user-page .up-tab.active span {
    color: #237c4d;
    font-weight: 700;
}

/* GLASS TABLE STYLING */
.user-page .up-table-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.user-page table.user-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 860px;
    font-size: 13px;
}

.user-page table.user-table th {
    padding: 13px 18px;
    text-align: left;
    background: rgba(255, 255, 255, .36);
    border-bottom: 1px solid var(--up-line);
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--up-muted);
    white-space: nowrap;
}

.user-page table.user-table td {
    padding: 15px 18px;
    border-bottom: 1px solid rgba(22, 54, 38, .06);
    vertical-align: middle;
    font-size: 12.5px;
    line-height: 1.5;
    color: var(--up-ink);
    background: transparent;
    transition: background .18s ease;
}

.user-page table.user-table tbody tr:hover td {
    background: rgba(255, 255, 255, .42);
}

.user-page table.user-table tbody tr:last-child td {
    border-bottom: 0;
}

.user-page .up-primary {
    font-weight: 700;
    color: var(--up-ink);
    display: block;
}

.user-page .up-sub {
    display: block;
    color: var(--up-subtle);
    font-size: 11.5px;
    font-weight: 500;
    margin-top: 2px;
}

.user-actions {
    display: flex;
    align-items: center;
    gap: 7px;
}

.user-actions form {
    margin: 0;
}

.user-actions .up-btn {
    padding: 7px 12px;
    font-size: 11.5px;
    border-radius: 9px;
}

/* ROLE PILLS */
.role-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 5px 11px;
    font-size: 11px;
    font-weight: 800;
    background: #bbf7d0;
    color: #065f46;
    border: 1px solid #34d399;
    white-space: nowrap;
}

.role-pill.mahasiswa {
    background: #bbf7d0;
    color: #065f46;
    border: 1px solid #34d399;
}

.role-pill.admin {
    background: #ede9fe;
    color: #5b21b6;
    border: 1px solid #c4b5fd;
}

.role-pill.pegawai {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.user-page .up-empty {
    padding: 46px 20px;
    text-align: center;
    color: var(--up-muted);
    font-size: 13px;
}

.user-page .up-empty-icon {
    width: 46px;
    height: 46px;
    margin: 0 auto 10px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: rgba(33, 128, 77, .12);
    color: var(--up-green);
}

.user-page .up-pagination {
    padding: 14px 18px;
    border-top: 1px solid var(--up-line);
    background: rgba(255, 255, 255, .25);
}

/* DRAWER TAMBAH PENGGUNA (ISOLATED FIXED LAYER) */
.sipadu-user-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 99990;
    background: rgba(15, 32, 23, .45);
    display: none;
}

.sipadu-user-overlay.is-open {
    display: block;
}

.sipadu-user-panel {
    position: fixed;
    top: 0;
    right: -540px;
    bottom: 0;
    z-index: 99995;
    width: min(510px, 100vw);
    background: #f8fbf9;
    box-shadow: -18px 0 55px rgba(12, 32, 21, .22);
    display: flex;
    flex-direction: column;
    transition: right .26s ease;
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

.sipadu-user-panel.is-open {
    right: 0;
}

.sipadu-user-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 24px 16px;
    background: #fff;
    border-bottom: 1px solid rgba(22, 54, 38, .08);
    flex-shrink: 0;
}

.sipadu-user-panel-header h2 {
    margin: 0;
    color: #163626;
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
}

.sipadu-user-panel-header p {
    margin: 4px 0 0;
    color: #718078;
    font-size: 12px;
}

.sipadu-user-panel-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    padding: 0;
    flex-shrink: 0;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 50%;
    background: #fff;
    color: #52665a;
    cursor: pointer;
}

.sipadu-user-panel-close:hover {
    background: #f1f6f2;
    color: #163626;
}

.sipadu-user-panel-close svg {
    width: 17px;
    height: 17px;
    display: block;
}

.sipadu-user-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 22px 24px 26px;
}

.user-form-intro {
    font-size: 12px;
    color: #64776b;
    margin: 0 0 18px;
    line-height: 1.6;
}

.user-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
}

.user-field label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #294735;
    margin-bottom: 7px;
}

.user-field input,
.user-field select {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #d8e1db;
    border-radius: 10px;
    padding: 11px 12px;
    font-family: inherit;
    font-size: 13px;
    color: #243b2e;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.user-field input:focus,
.user-field select:focus {
    border-color: #237c4d;
    box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
}

.user-field.full {
    grid-column: 1 / -1;
}

.user-form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 9px;
    margin-top: 23px;
    padding-top: 18px;
    border-top: 1px solid #e5ece7;
}

.user-form-actions .up-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 16px;
    border-radius: 11px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    border: 1px solid transparent;
}

.user-form-actions .up-btn.secondary {
    background: #fff;
    color: #294735;
    border-color: rgba(22, 54, 38, .15);
}

.user-form-actions .up-btn.primary {
    background: #237c4d;
    color: #fff;
}

body.user-drawer-open {
    overflow: hidden;
}

@media (max-width: 768px) {
    .user-page .up-heading {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 650px) {
    .user-page .up-heading h1 { font-size: 21px; }
    .user-page .up-toolbar { padding: 12px; }
    .user-page .up-tabs { padding: 0 12px; }
    .user-page .up-tab { padding: 12px 14px; font-size: 12.5px; }
    .sipadu-user-panel { width: 100vw; }
    .sipadu-user-panel-header { padding: 16px 18px; }
    .sipadu-user-panel-body { padding: 18px; }
    .user-form-grid { grid-template-columns: 1fr; }
    .user-field.full { grid-column: auto; }
}
</style>

<div class="user-page">
    {{-- HEADER --}}
    <div class="up-heading">
        <div>
            <h1>Data Pengguna</h1>
            <p>Kelola akun Mahasiswa, Pegawai, dan Administrator.</p>
        </div>
        <button class="up-btn primary" type="button" id="openAddUserDrawer">
            <i data-lucide="user-plus"></i>
            Tambah Pengguna
        </button>
    </div>

    @if(session('success'))<div class="up-alert success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="up-alert error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="up-alert error">Periksa kembali data yang dimasukkan. {{ $errors->first() }}</div>@endif

    {{-- KARTU GLASS UTAMA --}}
    <section class="up-card">
        {{-- SEARCH BAR --}}
        <form method="GET" action="{{ route('admin.users') }}" class="up-toolbar" id="userFilterForm">
            <div class="up-search">
                <span class="up-search-icon">
                    <i data-lucide="search"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    id="userSearchInput"
                    value="{{ request('q') }}"
                    placeholder="Cari nama, email, no. telepon, atau NIM/NIP..."
                    autocomplete="off"
                >
                <button
                    type="button"
                    class="up-search-clear {{ request('q') ? 'show' : '' }}"
                    id="clearUserSearch"
                    aria-label="Hapus pencarian"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            <a class="up-reset" href="{{ route('admin.users') }}" id="resetUserFilter">
                Reset
            </a>
        </form>

        {{-- TAB PEMBAGIAN ROLE --}}
        <div class="up-tabs" id="userRoleTabs" data-active-role="{{ $activeRole }}">
            <button type="button" class="up-tab {{ $activeRole === '' ? 'active' : '' }}" data-role-tab="">
                Semua <span id="roleCountAll">{{ $countAll }}</span>
            </button>
            <button type="button" class="up-tab {{ $activeRole === 'mahasiswa' ? 'active' : '' }}" data-role-tab="mahasiswa">
                Mahasiswa <span id="roleCountMahasiswa">{{ $countMahasiswa }}</span>
            </button>
            <button type="button" class="up-tab {{ $activeRole === 'pegawai' ? 'active' : '' }}" data-role-tab="pegawai">
                Pegawai <span id="roleCountPegawai">{{ $countPegawai }}</span>
            </button>
            <button type="button" class="up-tab {{ $activeRole === 'admin' ? 'active' : '' }}" data-role-tab="admin">
                Admin <span id="roleCountAdmin">{{ $countAdmin }}</span>
            </button>
        </div>

        {{-- TABEL DATA PENGGUNA --}}
        <div class="up-table-wrap">
            <table class="user-table" id="usersDataTable">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>No. Telepon</th>
                        <th>NIM / NIP</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    @if($users->count() > 0)
                        @foreach($users as $u)
                            @php
                                $searchIndex = strtolower(trim(
                                    ($u->name ?? '') . ' ' .
                                    ($u->email ?? '') . ' ' .
                                    ($u->phone ?? '') . ' ' .
                                    ($u->nim_nip ?? '') . ' ' .
                                    ($u->role ?? '')
                                ));
                            @endphp
                            <tr class="user-data-row" data-role="{{ strtolower($u->role) }}" data-search="{{ $searchIndex }}">
                                <td>
                                    <span class="up-primary">{{ $u->name }}</span>
                                    <span class="up-sub">{{ $u->email }}</span>
                                </td>
                                <td>{{ $u->phone ?: '—' }}</td>
                                <td>{{ $u->nim_nip ?: '—' }}</td>
                                <td>
                                    <span class="role-pill {{ strtolower($u->role) }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td>{{ optional($u->created_at)->format('d M Y') }}</td>
                                <td>
                                    <div class="user-actions">
                                        <a class="up-btn detail" href="{{ route('admin.users.show', $u) }}">Detail</a>
                                        <a class="up-btn edit" href="{{ route('admin.users.edit', $u) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.delete', $u) }}" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="up-btn danger" type="submit" {{ (string)$u->getAuthIdentifier() === (string)auth()->id() ? 'disabled' : '' }}>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <tr id="userFilterEmptyRow" style="display: none;">
                            <td colspan="6">
                                <div class="up-empty">
                                    <div class="up-empty-icon">
                                        <i data-lucide="users"></i>
                                    </div>
                                    <strong>Tidak ada pengguna ditemukan</strong>
                                    <div style="margin-top:5px">
                                        Coba ubah kata kunci pencarian atau pilih tab role lainnya.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="6">
                                <div class="up-empty">
                                    <div class="up-empty-icon">
                                        <i data-lucide="users"></i>
                                    </div>
                                    <strong>Belum ada data pengguna</strong>
                                    <div style="margin-top:5px">
                                        Klik tombol Tambah Pengguna untuk membuat akun baru.
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

    </section>
</div>

{{-- OVERLAY & DRAWER TAMBAH PENGGUNA (FIELD NIM/NIP DIHAPUS) --}}
<div class="sipadu-user-overlay" id="addUserOverlay"></div>
<aside class="sipadu-user-panel" id="addUserPanel" role="dialog" aria-modal="true" aria-labelledby="addUserDrawerTitle" data-has-errors="{{ $errors->any() ? '1' : '0' }}">
    <header class="sipadu-user-panel-header">
        <div>
            <h2 id="addUserDrawerTitle">Tambah Pengguna</h2>
            <p>Masukkan informasi akun pengguna baru.</p>
        </div>
        <button type="button" class="sipadu-user-panel-close" id="closeAddUserDrawer" aria-label="Tutup">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </header>
    <div class="sipadu-user-panel-body">
        <p class="user-form-intro">Lengkapi data berikut untuk membuat akun baru di SIPADU. Pastikan role yang dipilih sudah sesuai.</p>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="user-form-grid">
                <div class="user-field full"><label for="newUserName">Nama Lengkap</label><input id="newUserName" name="name" value="{{ old('name') }}" required></div>
                <div class="user-field full"><label for="newUserEmail">Email</label><input id="newUserEmail" type="email" name="email" value="{{ old('email') }}" required></div>
                <div class="user-field full"><label for="newUserPhone">Nomor Telepon</label><input id="newUserPhone" name="phone" value="{{ old('phone') }}"></div>
                <div class="user-field full">
                    <label for="newUserRole">Role</label>
                    <select id="newUserRole" name="role" required>
                        <option value="mahasiswa" @selected(old('role', 'mahasiswa') === 'mahasiswa')>Mahasiswa</option>
                        <option value="pegawai" @selected(old('role') === 'pegawai')>Pegawai</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    </select>
                </div>
                <div class="user-field"><label for="newUserPassword">Password</label><input id="newUserPassword" type="password" name="password" required minlength="8" autocomplete="new-password"></div>
                <div class="user-field"><label for="newUserPasswordConfirm">Konfirmasi Password</label><input id="newUserPasswordConfirm" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></div>
            </div>
            <div class="user-form-actions">
                <button class="up-btn secondary" type="button" id="cancelAddUserDrawer">Batal</button>
                <button class="up-btn primary" type="submit">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* DRAWER TAMBAH PENGGUNA */
    const overlay = document.getElementById('addUserOverlay');
    const panel = document.getElementById('addUserPanel');
    const openButton = document.getElementById('openAddUserDrawer');
    const closeButton = document.getElementById('closeAddUserDrawer');
    const cancelButton = document.getElementById('cancelAddUserDrawer');

    function openUserPanel() {
        if (!overlay || !panel) return;
        overlay.classList.add('is-open');
        panel.classList.add('is-open');
        document.body.classList.add('user-drawer-open');
        setTimeout(function () {
            document.getElementById('newUserName')?.focus();
        }, 180);
    }

    function closeUserPanel() {
        if (!overlay || !panel) return;
        overlay.classList.remove('is-open');
        panel.classList.remove('is-open');
        document.body.classList.remove('user-drawer-open');
    }

    openButton?.addEventListener('click', openUserPanel);
    closeButton?.addEventListener('click', closeUserPanel);
    cancelButton?.addEventListener('click', closeUserPanel);
    overlay?.addEventListener('click', closeUserPanel);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel?.classList.contains('is-open')) {
            closeUserPanel();
        }
    });

    if (panel?.dataset.hasErrors === '1') {
        openUserPanel();
    }

    /* SEARCH & TAB FILTER TIAP ROLE */
    const filterForm = document.getElementById('userFilterForm');
    const searchInput = document.getElementById('userSearchInput');
    const clearSearchBtn = document.getElementById('clearUserSearch');
    const resetFilterBtn = document.getElementById('resetUserFilter');
    const roleTabsContainer = document.getElementById('userRoleTabs');
    const roleTabs = document.querySelectorAll('[data-role-tab]');
    const userRows = Array.from(document.querySelectorAll('.user-data-row'));
    const emptyFilterRow = document.getElementById('userFilterEmptyRow');
    const usersTable = document.getElementById('usersDataTable');

    const countAllEl = document.getElementById('roleCountAll');
    const countMahasiswaEl = document.getElementById('roleCountMahasiswa');
    const countPegawaiEl = document.getElementById('roleCountPegawai');
    const countAdminEl = document.getElementById('roleCountAdmin');

    const urlParams = new URLSearchParams(window.location.search);
    const serverInitialRole = roleTabsContainer ? (roleTabsContainer.dataset.activeRole || '') : '';
    let activeRole = (urlParams.get('role') || serverInitialRole).toLowerCase();

    if (urlParams.get('q') && searchInput && !searchInput.value) {
        searchInput.value = urlParams.get('q');
    }

    function syncUrlParams(query, role) {
        const nextUrl = new URL(window.location.href);
        if (query) {
            nextUrl.searchParams.set('q', query);
        } else {
            nextUrl.searchParams.delete('q');
        }

        if (role) {
            nextUrl.searchParams.set('role', role);
        } else {
            nextUrl.searchParams.delete('role');
        }

        window.history.replaceState({}, '', nextUrl.toString());
    }

    function applyUserFilter() {
        const query = (searchInput?.value || '').trim().toLowerCase();

        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('show', query.length > 0);
        }

        roleTabs.forEach(function (tab) {
            const tabRole = (tab.dataset.roleTab || '').toLowerCase();
            tab.classList.toggle('active', tabRole === activeRole);
        });

        let matchedAll = 0;
        let matchedMahasiswa = 0;
        let matchedPegawai = 0;
        let matchedAdmin = 0;
        let visibleCount = 0;

        userRows.forEach(function (row) {
            const rowRole = (row.dataset.role || '').toLowerCase();
            const rowText = (row.dataset.search || '').toLowerCase();
            const matchesSearch = query === '' || rowText.includes(query);

            if (matchesSearch) {
                matchedAll++;
                if (rowRole === 'mahasiswa') matchedMahasiswa++;
                if (rowRole === 'pegawai') matchedPegawai++;
                if (rowRole === 'admin') matchedAdmin++;
            }

            const matchesRole = activeRole === '' || rowRole === activeRole;
            const isVisible = matchesSearch && matchesRole;

            row.dataset.filterHidden = isVisible ? '0' : '1';
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        if (countAllEl) countAllEl.textContent = matchedAll;
        if (countMahasiswaEl) countMahasiswaEl.textContent = matchedMahasiswa;
        if (countPegawaiEl) countPegawaiEl.textContent = matchedPegawai;
        if (countAdminEl) countAdminEl.textContent = matchedAdmin;

        if (emptyFilterRow) {
            emptyFilterRow.style.display = (userRows.length > 0 && visibleCount === 0) ? '' : 'none';
        }

        if (usersTable && typeof usersTable.sipaduRefreshPagination === 'function') {
            usersTable.sipaduRefreshPagination(true);
        }

        syncUrlParams(query, activeRole);
    }

    roleTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activeRole = (tab.dataset.roleTab || '').toLowerCase();
            applyUserFilter();
        });
    });

    searchInput?.addEventListener('input', applyUserFilter);

    clearSearchBtn?.addEventListener('click', function () {
        if (!searchInput) return;
        searchInput.value = '';
        searchInput.focus();
        applyUserFilter();
    });

    filterForm?.addEventListener('submit', function (e) {
        e.preventDefault();
        applyUserFilter();
    });

    resetFilterBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        if (searchInput) searchInput.value = '';
        activeRole = '';
        applyUserFilter();
    });

    applyUserFilter();

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endsection