@php
    $isAdmin = auth()->user()->role === 'admin';
    $isEmployee = auth()->user()->role === 'pegawai';
    $userRoutePrefix = $isEmployee ? 'pegawai.' : 'borrower.';
    $pendingBookingsCount = $isAdmin
        ? ($pendingBookingsCount ?? \App\Models\Booking::where('status', 'Pending')->count())
        : 0;
@endphp

<style>
    /* SIDEBAR GLASSMORPHISM */
    .sidebar,
    #mainSidebar {
        display: flex !important;
        flex-direction: column !important;
        padding: 18px 14px !important;
        top: 14px !important;
        bottom: 14px !important;
        left: 14px !important;
        margin: 0 !important;
        height: auto !important;
        max-height: calc(100vh - 28px) !important;
        border-radius: 16px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        background: linear-gradient(
            145deg,
            rgba(255,255,255,.54) 0%,
            rgba(255,255,255,.30) 100%
        ) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255,255,255,.38) !important;
        box-shadow: 0 8px 28px rgba(22,54,38,.06) !important;
        box-sizing: border-box !important;
    }

    /* BRAND / LOGO */
    .sidebar .sidebar-brand {
        display: flex !important;
        align-items: center !important;
        gap: 11px !important;
        min-height: 52px !important;
        padding: 0 8px 14px !important;
        margin-bottom: 10px !important;
        text-decoration: none !important;
        border-bottom: 1px solid rgba(22,54,38,.08) !important;
    }

    .sidebar .brand-icon {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
    }

    .sidebar .brand-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: none !important;
    }

    .sidebar .brand-text {
        display: flex !important;
        flex-direction: column !important;
        color: #163626 !important;
        white-space: nowrap;
    }

    .sidebar .brand-text b {
        font-size: 17px !important;
        font-weight: 800 !important;
        color: #1f7a49 !important;
        letter-spacing: -.2px;
    }

    .sidebar .brand-text small {
        font-size: 9px !important;
        color: #4c6456 !important;
        white-space: nowrap;
    }

    /* NAVIGATION */
    .sidebar .nav-caption {
        display: block !important;
        padding: 12px 10px 6px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: .08em !important;
        color: #4c6456 !important;
        white-space: nowrap;
    }

    .sidebar .nav-link {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;
        width: 100% !important;
        min-height: 42px !important;
        margin: 3px 0 !important;
        padding: 10px 12px !important;
        border-radius: 12px !important;
        text-decoration: none !important;
        color: #3c5446 !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        white-space: nowrap;
        border: 1px solid transparent !important;
        transition: background .2s ease, color .2s ease, border-color .2s ease;
    }

    .sidebar .nav-link svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
    }

    .sidebar .nav-link span {
        display: inline !important;
    }

    .sidebar .nav-link.active {
        position: relative !important;
        background: rgba(33,128,77,.16) !important;
        color: #1f7a49 !important;
        border: 1px solid rgba(255,255,255,.35) !important;
        box-shadow: none !important;
    }

    .sidebar .nav-link.active::before {
        content: '' !important;
        position: absolute !important;
        top: 9px !important;
        bottom: 9px !important;
        left: 0 !important;
        width: 3px !important;
        border-radius: 0 4px 4px 0 !important;
        background: #238458 !important;
    }

    .sidebar .nav-link:hover:not(.active) {
        background: rgba(255,255,255,.52) !important;
        color: #163626 !important;
    }

    /* BADGE NOTIFIKASI PENGAJUAN */
    .sidebar .nav-badge {
        margin-left: auto !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 21px !important;
        height: 21px !important;
        padding: 0 6px !important;
        border-radius: 999px !important;
        background: #ef4444 !important;
        color: #ffffff !important;
        font-size: 10.5px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.32) !important;
        flex-shrink: 0 !important;
    }

    /* LOGOUT — POLOS SEPERTI MENU SIDEBAR LAIN, MERAH SAAT HOVER */
    html body .app-layout aside#mainSidebar.sidebar form.logout,
    aside#mainSidebar.sidebar form.logout {
        margin-top: auto !important;
        margin-bottom: 0 !important;
        padding-top: 14px !important;
        border-top: 1px solid rgba(22,54,38,.08) !important;
        background: transparent !important;
    }

    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"],
    aside#mainSidebar.sidebar form.logout button[type="submit"] {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;
        width: 100% !important;
        min-height: 42px !important;
        margin: 3px 0 !important;
        padding: 10px 12px !important;
        border: 1px solid transparent !important;
        border-radius: 12px !important;
        background: transparent !important;
        background-color: transparent !important;
        background-image: none !important;
        color: #3c5446 !important;
        font: 700 12.5px 'Plus Jakarta Sans', 'Instrument Sans', sans-serif !important;
        letter-spacing: normal !important;
        text-transform: none !important;
        cursor: pointer;
        white-space: nowrap;
        box-shadow: none !important;
        transform: none !important;
        transition: background .2s ease, background-color .2s ease, color .2s ease, border-color .2s ease;
    }

    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"] svg,
    aside#mainSidebar.sidebar form.logout button[type="submit"] svg {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px !important;
        color: #3c5446 !important;
        stroke: currentColor !important;
        fill: none !important;
        transition: color .2s ease;
    }

    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"] span,
    aside#mainSidebar.sidebar form.logout button[type="submit"] span {
        display: inline !important;
        color: inherit !important;
    }

    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"]:hover,
    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"]:focus-visible,
    aside#mainSidebar.sidebar form.logout button[type="submit"]:hover,
    aside#mainSidebar.sidebar form.logout button[type="submit"]:focus-visible {
        background: rgba(254, 226, 226, .92) !important;
        background-color: rgba(254, 226, 226, .92) !important;
        background-image: none !important;
        border-color: rgba(220, 38, 38, .26) !important;
        color: #dc2626 !important;
        box-shadow: none !important;
        transform: none !important;
    }

    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"]:hover svg,
    html body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"]:focus-visible svg,
    aside#mainSidebar.sidebar form.logout button[type="submit"]:hover svg,
    aside#mainSidebar.sidebar form.logout button[type="submit"]:focus-visible svg {
        color: #dc2626 !important;
        stroke: currentColor !important;
    }

    /* COLLAPSED SIDEBAR */
    .app-layout.sidebar-closed .sidebar,
    html.sipadu-sidebar-closed .app-layout .sidebar {
        padding: 16px 8px !important;
    }

    .app-layout.sidebar-closed .sidebar .sidebar-brand,
    html.sipadu-sidebar-closed .app-layout .sidebar .sidebar-brand {
        justify-content: center !important;
        gap: 0 !important;
        padding: 0 0 14px !important;
    }

    .app-layout.sidebar-closed .sidebar .brand-text,
    .app-layout.sidebar-closed .sidebar .nav-caption,
    .app-layout.sidebar-closed .sidebar .nav-link span,
    html.sipadu-sidebar-closed .app-layout .sidebar .brand-text,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-caption,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link span,
    html.sipadu-sidebar-closed body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"] span,
    html body .app-layout.sidebar-closed aside#mainSidebar.sidebar form.logout button[type="submit"] span {
        display: none !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-link,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link {
        position: relative !important;
        justify-content: center !important;
        gap: 0 !important;
        padding: 10px 0 !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-badge,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-badge {
        position: absolute !important;
        top: 5px !important;
        right: 11px !important;
        margin-left: 0 !important;
        min-width: 16px !important;
        height: 16px !important;
        padding: 0 4px !important;
        font-size: 9px !important;
    }

    html.sipadu-sidebar-closed body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"],
    html body .app-layout.sidebar-closed aside#mainSidebar.sidebar form.logout button[type="submit"] {
        justify-content: center !important;
        gap: 0 !important;
        padding: 10px 0 !important;
    }

    .app-layout.sidebar-closed .sidebar .brand-icon,
    html.sipadu-sidebar-closed .app-layout .sidebar .brand-icon {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-link.active,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link.active {
        border-radius: 12px !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-link.active::before,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link.active::before {
        top: 8px !important;
        bottom: 8px !important;
    }

    /* RESPONSIVE */
    @media (max-width: 900px) {
        .sidebar,
        #mainSidebar {
            width: 248px !important;
            min-width: 248px !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            max-height: 100vh !important;
            border-radius: 0 16px 16px 0 !important;
            background: rgba(255,255,255,.94) !important;
        }

        .app-layout.sidebar-closed .sidebar .brand-text,
        .app-layout.sidebar-closed .sidebar .nav-caption,
        .app-layout.sidebar-closed .sidebar .nav-link span,
        html.sipadu-sidebar-closed .app-layout .sidebar .brand-text,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-caption,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-link span,
        html.sipadu-sidebar-closed body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"] span,
        html body .app-layout.sidebar-closed aside#mainSidebar.sidebar form.logout button[type="submit"] span {
            display: inline !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-link,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-link {
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 10px 12px !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-badge,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-badge {
            position: static !important;
            margin-left: auto !important;
            min-width: 21px !important;
            height: 21px !important;
            padding: 0 6px !important;
            font-size: 10.5px !important;
        }

        html.sipadu-sidebar-closed body .app-layout aside#mainSidebar.sidebar form.logout button[type="submit"],
        html body .app-layout.sidebar-closed aside#mainSidebar.sidebar form.logout button[type="submit"] {
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 10px 12px !important;
        }
    }
</style>

<aside class="sidebar" id="mainSidebar">

    {{-- LOGO SIPADU --}}
    <a
        class="brand sidebar-brand"
        href="{{ $isAdmin ? route('admin.dashboard') : route($userRoutePrefix . 'dashboard') }}"
        title="SIPADU"
    >
        <span class="brand-icon">
            <img
                src="{{ asset('images/logosipadu.png') }}"
                alt="Logo SIPADU"
            >
        </span>

        <span class="brand-text">
            <b>SIPADU</b>
            <small>Sistem Peminjaman Auditorium</small>
        </span>
    </a>

    {{-- MENU TITLE --}}
    <div class="nav-caption">
        {{ $isAdmin ? 'MENU ADMIN' : ($isEmployee ? 'MENU PEGAWAI' : 'MENU MAHASISWA') }}
    </div>

    {{-- MENU ADMIN --}}
    @if ($isAdmin)

        <a
            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            href="{{ route('admin.dashboard') }}"
            title="Dashboard"
        >
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"
            href="{{ route('admin.bookings') }}"
            title="Pengajuan"
        >
            <i data-lucide="clipboard-list"></i>
            <span>Kelola Pengajuan</span>

            @if ($pendingBookingsCount > 0)
                <small class="nav-badge">
                    {{ $pendingBookingsCount > 99 ? '99+' : $pendingBookingsCount }}
                </small>
            @endif
        </a>

        <a
            class="nav-link {{ request()->routeIs('admin.auditoriums*') ? 'active' : '' }}"
            href="{{ route('admin.auditoriums') }}"
            title="Data Auditorium"
        >
            <i data-lucide="building-2"></i>
            <span>Data Auditorium</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}"
            href="{{ route('admin.users') }}"
            title="Data Pengguna"
        >
            <i data-lucide="users"></i>
            <span>Data Pengguna</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('admin.contents*') ? 'active' : '' }}"
            href="{{ route('admin.contents') }}"
            title="Konten Informasi"
        >
            <i data-lucide="file-text"></i>
            <span>Konten Informasi</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}"
            href="{{ route('admin.profile') }}"
            title="Profil Akun"
        >
            <i data-lucide="user-round"></i>
            <span>Profil Akun</span>
        </a>

    {{-- MENU PEMINJAM --}}
    @else

        <a
            class="nav-link {{ request()->routeIs($userRoutePrefix . 'dashboard') ? 'active' : '' }}"
            href="{{ route($userRoutePrefix . 'dashboard') }}"
            title="Beranda"
        >
            <i data-lucide="house"></i>
            <span>Beranda</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs($userRoutePrefix . 'booking.create') ? 'active' : '' }}"
            href="{{ route($userRoutePrefix . 'booking.create') }}"
            title="Ajukan Peminjaman"
        >
            <i data-lucide="calendar-plus"></i>
            <span>Ajukan Peminjaman</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs($userRoutePrefix . 'bookings*') ? 'active' : '' }}"
            href="{{ route($userRoutePrefix . 'bookings') }}"
            title="Peminjaman Saya"
        >
            <i data-lucide="clipboard-check"></i>
            <span>Peminjaman Saya</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs($userRoutePrefix . 'guides') ? 'active' : '' }}"
            href="{{ route($userRoutePrefix . 'guides') }}"
            title="SOP & FAQ"
        >
            <i data-lucide="book-open"></i>
            <span>SOP & FAQ</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs($userRoutePrefix . 'profile*') ? 'active' : '' }}"
            href="{{ route($userRoutePrefix . 'profile') }}"
            title="Profil Akun"
        >
            <i data-lucide="user-round"></i>
            <span>Profil Akun</span>
        </a>

    @endif

    {{-- LOGOUT --}}
    <form
        method="POST"
        action="{{ route('logout') }}"
        class="logout"
    >
        @csrf

        <button type="submit" title="Logout">
            <i data-lucide="log-out"></i>
            <span>Logout</span>
        </button>
    </form>

</aside>

{{-- LUCIDE ICON --}}
<script
    src="https://unpkg.com/lucide@latest"
    onload="if (window.lucide) { window.lucide.createIcons(); }"
></script>

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