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

/* TOMBOL CRUD (SELARAS WARNA PASTEL DATA AUDITORIUM) */
.user-page .up-btn,
.sipadu-user-panel .up-btn {
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

.user-page .up-btn svg,
.sipadu-user-panel .up-btn svg {
    width: 15px;
    height: 15px;
}

.user-page .up-btn:hover,
.sipadu-user-panel .up-btn:hover {
    transform: translateY(-1px);
    box-shadow: none !important;
}

.user-page .up-btn.primary,
.sipadu-user-panel .up-btn.primary {
    background: #237c4d !important;
    color: #fff !important;
    border-color: #237c4d !important;
    padding: 10px 18px;
    font-size: 12.5px;
    box-shadow: 0 4px 12px rgba(35, 124, 77, .22) !important;
}

.user-page .up-btn.primary:hover,
.sipadu-user-panel .up-btn.primary:hover {
    background: #1b663f !important;
    color: #fff !important;
}

/* TOMBOL DETAIL (HIJAU MINT SOFT) */
.user-page .up-btn.detail {
    background: #dcfce7 !important;
    color: #166534 !important;
    border-color: rgba(22, 163, 74, .24) !important;
}

.user-page .up-btn.detail:hover {
    background: #bbf7d0 !important;
    color: #14532d !important;
}

/* TOMBOL EDIT (KUNING KREM SOFT SEPERTI DATA AUDITORIUM) */
.user-page .up-btn.edit,
.sipadu-user-panel .up-btn.edit {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: rgba(217, 119, 6, .24) !important;
}

.user-page .up-btn.edit:hover,
.sipadu-user-panel .up-btn.edit:hover {
    background: #fde68a !important;
    color: #78350f !important;
}

/* TOMBOL HAPUS (MERAH MUDA SOFT SEPERTI DATA AUDITORIUM) */
.user-page .up-btn.danger {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border-color: rgba(220, 38, 38, .22) !important;
}

.user-page .up-btn.danger:hover {
    background: #fecaca !important;
    color: #7f1d1d !important;
}

.user-page .up-btn.danger:disabled {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border-color: rgba(220, 38, 38, .18) !important;
    opacity: .45 !important;
    cursor: not-allowed;
    transform: none;
}

.user-page .up-btn.secondary,
.sipadu-user-panel .up-btn.secondary {
    background: #ffffff !important;
    color: #294735 !important;
    border-color: rgba(22, 54, 38, .15) !important;
}

.user-page .up-btn.secondary:hover,
.sipadu-user-panel .up-btn.secondary:hover {
    background: #f4f8f5 !important;
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
    background: rgba(255, 255, 255, .44);
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
    background: rgba(255, 255, 255, .52);
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
    padding: 7px 13px;
    font-size: 11.5px;
    border-radius: 10px;
}

/* ROLE PILLS (SELARAS BADGE PASTEL DATA AUDITORIUM) */
.role-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: 999px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 700;
    background: #dcfce7;
    color: #166534;
    border: 1px solid rgba(22, 163, 74, .22);
    white-space: nowrap;
}

.role-pill.mahasiswa {
    background: #dcfce7;
    color: #166534;
    border: 1px solid rgba(22, 163, 74, .22);
}

.role-pill.admin {
    background: #e0f2fe;
    color: #075985;
    border: 1px solid rgba(2, 132, 199, .22);
}

.role-pill.pegawai {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid rgba(217, 119, 6, .22);
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

/* ==========================================================================
   POPUP / DRAWER SAMPING (SELARAS DENGAN POPUP DATA AUDITORIUM)
   ========================================================================== */
.sipadu-user-overlay {
    position: fixed;
    inset: 0;
    z-index: 99990;
    background: rgba(15, 32, 23, .45);
    display: none;
}

.sipadu-user-overlay.is-open {
    display: block;
}

.sipadu-user-panel {
    position: fixed;
    top: 14px;
    right: -540px;
    bottom: 14px;
    z-index: 99995;
    width: min(490px, calc(100vw - 28px));
    background: #ffffff;
    border-radius: 20px;
    box-shadow: -12px 0 48px rgba(12, 32, 21, .22);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: right .26s ease;
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

.sipadu-user-panel.is-open {
    right: 14px;
}

.sipadu-user-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 24px 16px;
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
    margin: 3px 0 0;
    color: #718078;
    font-size: 12px;
}

.sipadu-user-panel-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    flex-shrink: 0;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 10px;
    background: #fff;
    color: #52665a;
    cursor: pointer;
    transition: background .18s ease, color .18s ease;
}

.sipadu-user-panel-close:hover {
    background: #f1f6f2;
    color: #163626;
}

.sipadu-user-panel-close svg {
    width: 16px;
    height: 16px;
    display: block;
}

.sipadu-user-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px 22px;
}

.sipadu-user-panel-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px 24px;
    border-top: 1px solid rgba(22, 54, 38, .08);
    background: #fff;
    flex-shrink: 0;
}

.user-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 15px;
}

.user-field label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #163626;
    margin-bottom: 7px;
}

.user-field label .req {
    color: #dc2626;
    margin-left: 2px;
}

.user-field input,
.user-field select {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #d8e1db;
    border-radius: 11px;
    padding: 11px 13px;
    font-family: inherit;
    font-size: 13px;
    color: #163626;
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.user-password-wrap {
    position: relative;
    width: 100%;
}

.user-password-wrap input {
    padding-right: 42px;
}

.user-password-toggle {
    position: absolute;
    top: 50%;
    right: 9px;
    transform: translateY(-50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-radius: 7px;
    background: transparent;
    color: #718078;
    cursor: pointer;
}

.user-password-toggle:hover {
    background: rgba(35, 124, 77, .08);
    color: #237c4d;
}

.user-password-toggle svg {
    width: 17px;
    height: 17px;
}

.user-field input:focus,
.user-field select:focus {
    border-color: #237c4d;
    box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
}

.user-field.full {
    grid-column: 1 / -1;
}

/* DETAIL PENGGUNA STYLING DI DALAM POPUP */
.user-detail-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 16px 18px;
    border-radius: 14px;
    background: #f5f9f6;
    border: 1px solid rgba(22, 54, 38, .08);
    margin-bottom: 18px;
}

.user-detail-avatar {
    width: 46px;
    height: 46px;
    border-radius: 13px;
    background: #dcfce7;
    color: #166534;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 800;
    flex-shrink: 0;
}

.user-detail-hero-info {
    min-width: 0;
    flex: 1;
}

.user-detail-hero-info h3 {
    margin: 0;
    font-size: 15.5px;
    font-weight: 800;
    color: #163626;
    line-height: 1.3;
}

.user-detail-hero-info p {
    margin: 3px 0 0;
    font-size: 12px;
    color: #5c7265;
    word-break: break-all;
}

.user-detail-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.user-detail-row {
    padding: 12px 15px;
    border-radius: 12px;
    border: 1px solid rgba(22, 54, 38, .08);
    background: #ffffff;
}

.user-detail-row small {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: #64786c;
    margin-bottom: 3px;
}

.user-detail-row strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: #163626;
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
    .sipadu-user-panel {
        top: 0;
        bottom: 0;
        width: 100vw;
        border-radius: 0;
    }
    .sipadu-user-panel.is-open { right: 0; }
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
            <p>Kelola akun Mahasiswa, Dosen & Tendik, dan Administrator.</p>
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
                Dosen & Tendik <span id="roleCountPegawai">{{ $countPegawai }}</span>
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
                                $roleLower = strtolower($u->role);
                                $roleLabel = match($roleLower) {
                                    'mahasiswa' => 'Mahasiswa',
                                    'pegawai'   => 'Dosen & Tendik',
                                    'admin'     => 'Admin',
                                    default     => ucfirst($u->role),
                                };
                                $joinedDate = optional($u->created_at)->format('d M Y') ?: '-';
                                $searchIndex = strtolower(trim(
                                    ($u->name ?? '') . ' ' .
                                    ($u->email ?? '') . ' ' .
                                    ($u->phone ?? '') . ' ' .
                                    ($u->nim_nip ?? '') . ' ' .
                                    ($u->role ?? '')
                                ));
                                $isSelf = (string) $u->getAuthIdentifier() === (string) auth()->id();
                                $initials = collect(explode(' ', trim($u->name)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn($part) => strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('') ?: 'U';
                            @endphp
                            <tr class="user-data-row" data-role="{{ $roleLower }}" data-search="{{ $searchIndex }}">
                                <td>
                                    <span class="up-primary">{{ $u->name }}</span>
                                    <span class="up-sub">{{ $u->email }}</span>
                                </td>
                                <td>{{ $u->phone ?: '—' }}</td>
                                <td>{{ $u->nim_nip ?: '—' }}</td>
                                <td>
                                    <span class="role-pill {{ $roleLower }}">
                                        {{ $roleLabel }}
                                    </span>
                                </td>
                                <td>{{ $joinedDate }}</td>
                                <td>
                                    <div class="user-actions">
                                        <button
                                            type="button"
                                            class="up-btn detail js-open-detail-user"
                                            data-name="{{ $u->name }}"
                                            data-initials="{{ $initials }}"
                                            data-email="{{ $u->email }}"
                                            data-phone="{{ $u->phone ?: '—' }}"
                                            data-nim="{{ $u->nim_nip ?: '—' }}"
                                            data-role="{{ $roleLower }}"
                                            data-role-label="{{ $roleLabel }}"
                                            data-joined="{{ $joinedDate }}"
                                            data-update-url="{{ route('admin.users.update', $u) }}"
                                        >
                                            Detail
                                        </button>

                                        <button
                                            type="button"
                                            class="up-btn edit js-open-edit-user"
                                            data-name="{{ $u->name }}"
                                            data-email="{{ $u->email }}"
                                            data-phone="{{ $u->phone ?? '' }}"
                                            data-nim="{{ $u->nim_nip ?? '' }}"
                                            data-role="{{ $roleLower }}"
                                            data-update-url="{{ route('admin.users.update', $u) }}"
                                        >
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('admin.users.delete', $u) }}" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="up-btn danger" type="submit" {{ $isSelf ? 'disabled' : '' }}>
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

{{-- OVERLAY BERSAMA UNTUK SEMUA POPUP / DRAWER --}}
<div class="sipadu-user-overlay" id="userDrawerOverlay"></div>

{{-- 1. POPUP TAMBAH PENGGUNA --}}
<aside class="sipadu-user-panel" id="addUserPanel" role="dialog" aria-modal="true" aria-labelledby="addUserDrawerTitle" data-has-errors="{{ ($errors->any() && old('_form_mode') !== 'edit') ? '1' : '0' }}">
    <header class="sipadu-user-panel-header">
        <div>
            <h2 id="addUserDrawerTitle">Tambah Pengguna Baru</h2>
            <p>Masukkan informasi akun pengguna baru.</p>
        </div>
        <button type="button" class="sipadu-user-panel-close js-close-user-drawer" aria-label="Tutup">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </header>
    <form method="POST" action="{{ route('admin.users.store') }}" style="display:flex; flex-direction:column; flex:1; min-height:0;">
        @csrf
        <input type="hidden" name="_form_mode" value="create">
        <div class="sipadu-user-panel-body">
            <div class="user-form-grid">
                <div class="user-field full">
                    <label for="newUserName">Nama Lengkap <span class="req">*</span></label>
                    <input id="newUserName" name="name" placeholder="Masukkan nama lengkap" value="{{ old('_form_mode') !== 'edit' ? old('name') : '' }}" required>
                </div>
                <div class="user-field full">
                    <label for="newUserEmail">Email <span class="req">*</span></label>
                    <input id="newUserEmail" type="email" name="email" placeholder="Contoh: nama@upnvj.ac.id" value="{{ old('_form_mode') !== 'edit' ? old('email') : '' }}" required>
                </div>
                <div class="user-field full">
                    <label for="newUserPhone">Nomor Telepon</label>
                    <input id="newUserPhone" name="phone" placeholder="Contoh: 081234567890" value="{{ old('_form_mode') !== 'edit' ? old('phone') : '' }}">
                </div>
                <div class="user-field full">
                    <label for="newUserRole">Role <span class="req">*</span></label>
                    <select id="newUserRole" name="role" required>
                        <option value="mahasiswa" @selected(old('role', 'mahasiswa') === 'mahasiswa')>Mahasiswa</option>
                        <option value="pegawai" @selected(old('role') === 'pegawai')>Dosen & Tendik</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    </select>
                </div>
                <div class="user-field">
                    <label for="newUserPassword">Password <span class="req">*</span></label>
                    <div class="user-password-wrap">
                        <input id="newUserPassword" type="password" name="password" placeholder="Min. 8 karakter" required minlength="8" autocomplete="new-password">
                        <button type="button" class="user-password-toggle" data-toggle-password="newUserPassword" aria-label="Tampilkan password" aria-pressed="false">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
                <div class="user-field">
                    <label for="newUserPasswordConfirm">Konfirmasi Password <span class="req">*</span></label>
                    <div class="user-password-wrap">
                        <input id="newUserPasswordConfirm" type="password" name="password_confirmation" placeholder="Ulangi password" required minlength="8" autocomplete="new-password">
                        <button type="button" class="user-password-toggle" data-toggle-password="newUserPasswordConfirm" aria-label="Tampilkan password" aria-pressed="false">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="sipadu-user-panel-footer">
            <button class="up-btn secondary js-close-user-drawer" type="button">Batal</button>
            <button class="up-btn primary" type="submit">Simpan Pengguna</button>
        </div>
    </form>
</aside>

{{-- 2. POPUP DETAIL PENGGUNA --}}
<aside class="sipadu-user-panel" id="detailUserPanel" role="dialog" aria-modal="true" aria-labelledby="detailUserDrawerTitle">
    <header class="sipadu-user-panel-header">
        <div>
            <h2 id="detailUserDrawerTitle">Detail Pengguna</h2>
            <p>Informasi lengkap akun pengguna terdaftar.</p>
        </div>
        <button type="button" class="sipadu-user-panel-close js-close-user-drawer" aria-label="Tutup">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </header>
    <div class="sipadu-user-panel-body">
        <div class="user-detail-hero">
            <div class="user-detail-avatar" id="detailUserAvatar">U</div>
            <div class="user-detail-hero-info">
                <h3 id="detailUserName">-</h3>
                <p id="detailUserEmail">-</p>
            </div>
            <span class="role-pill" id="detailUserRoleBadge">-</span>
        </div>

        <div class="user-detail-list">
            <div class="user-detail-row">
                <small>Nama Lengkap</small>
                <strong id="detailUserFullName">-</strong>
            </div>
            <div class="user-detail-row">
                <small>Alamat Email</small>
                <strong id="detailUserEmailRow">-</strong>
            </div>
            <div class="user-detail-row">
                <small>NIM / NIP</small>
                <strong id="detailUserNim">—</strong>
            </div>
            <div class="user-detail-row">
                <small>Nomor Telepon</small>
                <strong id="detailUserPhone">—</strong>
            </div>
            <div class="user-detail-row">
                <small>Tanggal Bergabung</small>
                <strong id="detailUserJoined">-</strong>
            </div>
        </div>
    </div>
    <div class="sipadu-user-panel-footer">
        <button class="up-btn secondary js-close-user-drawer" type="button">Tutup</button>
        <button class="up-btn edit" type="button" id="detailSwitchToEditBtn">
            <i data-lucide="pencil"></i>
            Edit Pengguna
        </button>
    </div>
</aside>

{{-- 3. POPUP EDIT PENGGUNA --}}
<aside class="sipadu-user-panel" id="editUserPanel" role="dialog" aria-modal="true" aria-labelledby="editUserDrawerTitle" data-has-errors="{{ ($errors->any() && old('_form_mode') === 'edit') ? '1' : '0' }}">
    <header class="sipadu-user-panel-header">
        <div>
            <h2 id="editUserDrawerTitle">Edit Pengguna</h2>
            <p>Perbarui data profil, role, atau kata sandi pengguna.</p>
        </div>
        <button type="button" class="sipadu-user-panel-close js-close-user-drawer" aria-label="Tutup">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
    </header>
    <form method="POST" id="editUserForm" action="{{ old('_edit_action', '') }}" style="display:flex; flex-direction:column; flex:1; min-height:0;">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form_mode" value="edit">
        <input type="hidden" name="_edit_action" id="editUserActionInput" value="{{ old('_edit_action', '') }}">

        <div class="sipadu-user-panel-body">
            <div class="user-form-grid">
                <div class="user-field full">
                    <label for="editUserName">Nama Lengkap <span class="req">*</span></label>
                    <input id="editUserName" name="name" placeholder="Masukkan nama lengkap" value="{{ old('_form_mode') === 'edit' ? old('name') : '' }}" required>
                </div>
                <div class="user-field full">
                    <label for="editUserEmail">Email <span class="req">*</span></label>
                    <input id="editUserEmail" type="email" name="email" placeholder="Contoh: nama@upnvj.ac.id" value="{{ old('_form_mode') === 'edit' ? old('email') : '' }}" required>
                </div>
                <div class="user-field">
                    <label for="editUserNim">NIM / NIP</label>
                    <input id="editUserNim" name="nim_nip" placeholder="Opsional" value="{{ old('_form_mode') === 'edit' ? old('nim_nip') : '' }}">
                </div>
                <div class="user-field">
                    <label for="editUserPhone">Nomor Telepon</label>
                    <input id="editUserPhone" name="phone" placeholder="Contoh: 081234567890" value="{{ old('_form_mode') === 'edit' ? old('phone') : '' }}">
                </div>
                <div class="user-field full">
                    <label for="editUserRole">Role <span class="req">*</span></label>
                    <select id="editUserRole" name="role" required>
                        <option value="mahasiswa" @selected(old('role') === 'mahasiswa')>Mahasiswa</option>
                        <option value="pegawai" @selected(old('role') === 'pegawai')>Dosen & Tendik</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    </select>
                </div>
                <div class="user-field">
                    <label for="editUserPassword">Password Baru</label>
                    <div class="user-password-wrap">
                        <input id="editUserPassword" type="password" name="password" placeholder="Kosongkan jika tetap" minlength="8" autocomplete="new-password">
                        <button type="button" class="user-password-toggle" data-toggle-password="editUserPassword" aria-label="Tampilkan password" aria-pressed="false">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
                <div class="user-field">
                    <label for="editUserPasswordConfirm">Konfirmasi Password</label>
                    <div class="user-password-wrap">
                        <input id="editUserPasswordConfirm" type="password" name="password_confirmation" placeholder="Ulangi password baru" minlength="8" autocomplete="new-password">
                        <button type="button" class="user-password-toggle" data-toggle-password="editUserPasswordConfirm" aria-label="Tampilkan password" aria-pressed="false">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="sipadu-user-panel-footer">
            <button class="up-btn secondary js-close-user-drawer" type="button">Batal</button>
            <button class="up-btn primary" type="submit">Simpan Perubahan</button>
        </div>
    </form>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* MANAJEMEN POPUP / DRAWER SAMPING (TAMBAH, DETAIL, EDIT) */
    const overlay = document.getElementById('userDrawerOverlay');
    const addPanel = document.getElementById('addUserPanel');
    const detailPanel = document.getElementById('detailUserPanel');
    const editPanel = document.getElementById('editUserPanel');
    const allPanels = [addPanel, detailPanel, editPanel].filter(Boolean);

    function closeAllUserDrawers() {
        overlay?.classList.remove('is-open');
        allPanels.forEach(function (p) {
            p.classList.remove('is-open');
        });
        document.body.classList.remove('user-drawer-open');
    }

    function openSpecificDrawer(targetPanel, focusSelector) {
        if (!overlay || !targetPanel) return;
        allPanels.forEach(function (p) {
            p.classList.toggle('is-open', p === targetPanel);
        });
        overlay.classList.add('is-open');
        document.body.classList.add('user-drawer-open');

        if (focusSelector) {
            setTimeout(function () {
                targetPanel.querySelector(focusSelector)?.focus();
            }, 180);
        }
    }

    // 1. Buka Popup Tambah Pengguna
    document.getElementById('openAddUserDrawer')?.addEventListener('click', function () {
        openSpecificDrawer(addPanel, '#newUserName');
    });

    // 2. Buka Popup Detail Pengguna
    let currentDetailPayload = null;
    document.querySelectorAll('.js-open-detail-user').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentDetailPayload = {
                name: btn.dataset.name || '-',
                initials: btn.dataset.initials || 'U',
                email: btn.dataset.email || '-',
                phone: btn.dataset.phone || '—',
                nim: btn.dataset.nim || '—',
                role: btn.dataset.role || 'mahasiswa',
                roleLabel: btn.dataset.roleLabel || 'Mahasiswa',
                joined: btn.dataset.joined || '-',
                updateUrl: btn.dataset.updateUrl || ''
            };

            document.getElementById('detailUserAvatar').textContent = currentDetailPayload.initials;
            document.getElementById('detailUserName').textContent = currentDetailPayload.name;
            document.getElementById('detailUserEmail').textContent = currentDetailPayload.email;
            document.getElementById('detailUserFullName').textContent = currentDetailPayload.name;
            document.getElementById('detailUserEmailRow').textContent = currentDetailPayload.email;
            document.getElementById('detailUserNim').textContent = currentDetailPayload.nim;
            document.getElementById('detailUserPhone').textContent = currentDetailPayload.phone;
            document.getElementById('detailUserJoined').textContent = currentDetailPayload.joined;

            const badgeEl = document.getElementById('detailUserRoleBadge');
            badgeEl.textContent = currentDetailPayload.roleLabel;
            badgeEl.className = 'role-pill ' + currentDetailPayload.role;

            openSpecificDrawer(detailPanel);
        });
    });

    // 3. Buka Popup Edit Pengguna
    const editForm = document.getElementById('editUserForm');
    const editActionInput = document.getElementById('editUserActionInput');
    const editNameInput = document.getElementById('editUserName');
    const editEmailInput = document.getElementById('editUserEmail');
    const editNimInput = document.getElementById('editUserNim');
    const editPhoneInput = document.getElementById('editUserPhone');
    const editRoleSelect = document.getElementById('editUserRole');
    const editPasswordInput = document.getElementById('editUserPassword');
    const editPasswordConfirmInput = document.getElementById('editUserPasswordConfirm');

    function populateAndOpenEdit(data) {
        if (!editForm) return;
        editForm.action = data.updateUrl || '';
        if (editActionInput) editActionInput.value = data.updateUrl || '';
        if (editNameInput) editNameInput.value = data.name || '';
        if (editEmailInput) editEmailInput.value = data.email || '';
        if (editNimInput) editNimInput.value = (data.nim && data.nim !== '—') ? data.nim : '';
        if (editPhoneInput) editPhoneInput.value = (data.phone && data.phone !== '—') ? data.phone : '';
        if (editRoleSelect) editRoleSelect.value = data.role || 'mahasiswa';
        if (editPasswordInput) editPasswordInput.value = '';
        if (editPasswordConfirmInput) editPasswordConfirmInput.value = '';

        openSpecificDrawer(editPanel, '#editUserName');
    }

    document.querySelectorAll('.js-open-edit-user').forEach(function (btn) {
        btn.addEventListener('click', function () {
            populateAndOpenEdit({
                name: btn.dataset.name,
                email: btn.dataset.email,
                nim: btn.dataset.nim,
                phone: btn.dataset.phone,
                role: btn.dataset.role,
                updateUrl: btn.dataset.updateUrl
            });
        });
    });

    document.getElementById('detailSwitchToEditBtn')?.addEventListener('click', function () {
        if (currentDetailPayload) {
            populateAndOpenEdit(currentDetailPayload);
        }
    });

    // Tutup popup
    document.querySelectorAll('.js-close-user-drawer').forEach(function (btn) {
        btn.addEventListener('click', closeAllUserDrawers);
    });
    overlay?.addEventListener('click', closeAllUserDrawers);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay?.classList.contains('is-open')) {
            closeAllUserDrawers();
        }
    });

    if (addPanel?.dataset.hasErrors === '1') {
        openSpecificDrawer(addPanel, '#newUserName');
    } else if (editPanel?.dataset.hasErrors === '1') {
        openSpecificDrawer(editPanel, '#editUserName');
    }

    /* TOGGLE SHOW / HIDE PASSWORD */
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-toggle-password');
            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
            btn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
            btn.innerHTML = isPassword
                ? '<i data-lucide="eye-off"></i>'
                : '<i data-lucide="eye"></i>';

            if (window.lucide) {
                lucide.createIcons();
            }
        });
    });

    /* SEARCH & TAB FILTER TIAP ROLE */
    const filterForm = document.getElementById('userFilterForm');
    const searchInput = document.getElementById('userSearchInput');
    const clearSearchBtn = document.getElementById('clearUserSearch');
    const resetFilterBtn = document.getElementById('resetUserFilter');
    const roleTabsContainer = document.getElementById('userRoleTabs');
    const roleTabs = document.querySelectorAll('[data-role-tab]');
    const userRows = Array.from(document.querySelectorAll('.user-data-row'));
    const emptyFilterRow = document.getElementById('userFilterEmptyRow');

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

        syncUrlParams(query, activeRole);
    }

    searchInput?.addEventListener('input', applyUserFilter);

    filterForm?.addEventListener('submit', function (e) {
        e.preventDefault();
        applyUserFilter();
    });

    clearSearchBtn?.addEventListener('click', function () {
        if (!searchInput) return;
        searchInput.value = '';
        applyUserFilter();
        searchInput.focus();
    });

    resetFilterBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        if (searchInput) searchInput.value = '';
        activeRole = '';
        applyUserFilter();
    });

    roleTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            activeRole = (tab.dataset.roleTab || '').toLowerCase();
            applyUserFilter();
        });
    });

    applyUserFilter();

    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endsection