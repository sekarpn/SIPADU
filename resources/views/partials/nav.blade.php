
@php
    $isAdmin = auth()->user()->role === 'admin';
@endphp

<style>
    /* SIDEBAR */
    .sidebar {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        box-sizing: border-box;
        overflow-x: hidden;
        overflow-y: auto;
        background: #fff !important;
        border-right: 1px solid #e8eee9;
        transition: width .3s ease, min-width .3s ease, flex-basis .3s ease;
    }

    /* LOGO SIPADU */
    .sidebar .sidebar-brand {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px;
        width: 100%;
        min-height: 76px;
        height: 76px;
        padding: 8px;
        flex-shrink: 0;
        overflow: hidden;
        text-decoration: none !important;
        border-bottom: 1px solid #f0f3f0;
        box-sizing: border-box;
    }

    .sidebar .brand-icon {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        min-width: 52px;
        flex-shrink: 0;
        background: transparent !important;
        border-radius: 0 !important;
        overflow: visible !important;
    }

    .sidebar .brand-icon img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: transparent;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.35));
    }

    /* TULISAN LOGO */
    .sidebar .brand-text {
        display: none;
        flex-direction: column;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        white-space: nowrap;
        color: #173b2b;
    }

    .sidebar.expanded .brand-text {
        display: flex !important;
    }

    .sidebar .brand-text b {
        font-size: 16px;
        font-weight: 800;
        line-height: 1.3;
        white-space: nowrap;
    }

    .sidebar .brand-text small {
        display: block;
        font-size: 9px;
        color: #84948a;
        line-height: 1.4;
        white-space: nowrap;
        letter-spacing: -0.3px;
    }

    .sidebar.expanded .sidebar-brand {
        justify-content: flex-start !important;
        padding: 8px 12px;
        gap: 9px;
    }

    /* JUDUL MENU */
    .sidebar .nav-caption {
        display: none;
        white-space: nowrap;
        padding: 18px 17px 8px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
        color: #9aa79f;
    }

    .sidebar.expanded .nav-caption {
        display: block;
    }

    /* MENU */
    .sidebar .nav-link {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 13px;
        width: calc(100% - 16px);
        min-width: 0;
        min-height: 46px;
        margin: 4px 8px;
        padding: 0;
        box-sizing: border-box;
        border-radius: 11px;
        white-space: nowrap;
        text-decoration: none !important;
        color: #78877e;
        font-size: 13px;
        font-weight: 500;
        transition: background .2s ease, color .2s ease;
    }

    .sidebar.expanded .nav-link {
        justify-content: flex-start !important;
        padding: 10px 14px;
    }

    .sidebar .nav-link svg,
    .sidebar .logout svg {
        display: block !important;
        width: 20px;
        height: 20px;
        min-width: 20px;
        flex-shrink: 0;
    }

    .sidebar .nav-link span,
    .sidebar .logout button span {
        display: none;
    }

    .sidebar.expanded .nav-link span,
    .sidebar.expanded .logout button span {
        display: inline;
    }

    /* MENU AKTIF */
    .sidebar .nav-link.active {
        background: #e8f7ed !important;
        color: #159957 !important;
        font-weight: 700;
    }

    .sidebar .nav-link.active svg {
        color: #159957;
        stroke-width: 2.2;
    }

    .sidebar .nav-link:hover {
        background: #f0f8f2;
        color: #159957;
    }

    /* LOGOUT */
    .sidebar .logout {
        margin-top: auto;
        width: 100%;
        padding: 12px 0;
        border-top: 1px solid #f0f3f0;
    }

    .sidebar .logout button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 13px;
        width: calc(100% - 16px);
        min-height: 46px;
        margin: 4px 8px;
        padding: 0;
        box-sizing: border-box;
        white-space: nowrap;
        border: none;
        border-radius: 11px;
        background: transparent;
        color: #78877e;
        font-size: 13px;
        cursor: pointer;
        transition: background .2s ease, color .2s ease;
    }

    .sidebar .logout button:hover {
        background: #fff1f1;
        color: #dc4545;
    }

    .sidebar.expanded .logout button {
        justify-content: flex-start;
        padding: 10px 14px;
    }

    /* SIDEBAR KECIL */
    .sidebar:not(.expanded) {
        align-items: center !important;
    }

    .sidebar:not(.expanded) .sidebar-brand {
        width: 76px !important;
        justify-content: center !important;
        padding: 8px 0 !important;
    }

    .sidebar:not(.expanded) .brand-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
    }

    .sidebar:not(.expanded) .nav-link {
        width: 46px !important;
        height: 46px !important;
        min-height: 46px !important;
        margin: 4px auto !important;
        padding: 0 !important;
        justify-content: center !important;
        align-items: center !important;
    }

    .sidebar:not(.expanded) .logout {
        width: 100%;
    }

    .sidebar:not(.expanded) .logout button {
        width: 46px !important;
        height: 46px !important;
        min-height: 46px !important;
        margin: 4px auto !important;
        padding: 0 !important;
        justify-content: center !important;
    }

    .sidebar.expanded {
        align-items: stretch !important;
    }
</style>

<aside class="sidebar" id="mainSidebar">

    {{-- LOGO SIPADU --}}
    <a class="brand sidebar-brand"
       href="{{ $isAdmin ? route('admin.dashboard') : route('borrower.dashboard') }}"
       title="SIPADU">

        <span class="brand-icon">
            <img src="{{ asset('images/logosipadu.png') }}"
                 alt="Logo SIPADU">
        </span>

        <span class="brand-text">
            <b>SIPADU</b>
            <small>Sistem Peminjaman Auditorium</small>
        </span>
    </a>

    <div class="nav-caption">
        {{ $isAdmin ? 'MENU ADMIN' : 'MENU PEMINJAM' }}
    </div>

    @if($isAdmin)

        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
           href="{{ route('admin.dashboard') }}" title="Dashboard">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"
           href="{{ route('admin.bookings') }}" title="Pengajuan">
            <i data-lucide="clipboard-list"></i>
            <span>Pengajuan</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.auditoriums*') ? 'active' : '' }}"
           href="{{ route('admin.auditoriums') }}" title="Data Auditorium">
            <i data-lucide="building-2"></i>
            <span>Data Auditorium</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
           href="{{ route('admin.users') }}" title="Data Pengguna">
            <i data-lucide="users"></i>
            <span>Data Pengguna</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.contents*') ? 'active' : '' }}"
           href="{{ route('admin.contents') }}" title="Konten Informasi">
            <i data-lucide="file-text"></i>
            <span>Konten Informasi</span>
        </a>

        <a class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}"
           href="{{ route('admin.profile') }}" title="Profil Akun">
            <i data-lucide="user-round"></i>
            <span>Profil Akun</span>
        </a>

    @else

        <a class="nav-link {{ request()->routeIs('borrower.dashboard') ? 'active' : '' }}"
           href="{{ route('borrower.dashboard') }}" title="Beranda">
            <i data-lucide="house"></i>
            <span>Beranda</span>
        </a>

        <a class="nav-link {{ request()->routeIs('borrower.booking.create') ? 'active' : '' }}"
           href="{{ route('borrower.booking.create') }}" title="Ajukan Peminjaman">
            <i data-lucide="calendar-plus"></i>
            <span>Ajukan Peminjaman</span>
        </a>

        <a class="nav-link {{ request()->routeIs('borrower.bookings*') ? 'active' : '' }}"
           href="{{ route('borrower.bookings') }}" title="Peminjaman Saya">
            <i data-lucide="clipboard-check"></i>
            <span>Peminjaman Saya</span>
        </a>

        <a class="nav-link {{ request()->routeIs('borrower.guides') ? 'active' : '' }}"
           href="{{ route('borrower.guides') }}" title="SOP & FAQ">
            <i data-lucide="book-open"></i>
            <span>SOP & FAQ</span>
        </a>

        <a class="nav-link {{ request()->routeIs('borrower.profile*') ? 'active' : '' }}"
           href="{{ route('borrower.profile') }}" title="Profil Akun">
            <i data-lucide="user-round"></i>
            <span>Profil Akun</span>
        </a>

    @endif

    {{-- LOGOUT --}}
    <form method="POST" action="{{ route('logout') }}" class="logout">
        @csrf

        <button type="submit" title="Logout">
            <i data-lucide="log-out"></i>
            <span>Logout</span>
        </button>
    </form>

</aside>

<script src="https://unpkg.com/lucide@latest"
        onload="if(window.lucide){window.lucide.createIcons();}">
</script>

<script>
(function () {
    function renderSidebarIcons() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', renderSidebarIcons);
    } else {
        renderSidebarIcons();
    }

    window.addEventListener('load', renderSidebarIcons);
})();
</script>
