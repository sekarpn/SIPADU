@php
    $isAdmin = auth()->user()->role === 'admin';
@endphp

<style>
    /* =========================================================
       SIDEBAR
    ========================================================= */

    .sidebar {
        display: flex !important;
        flex-direction: column !important;
        padding: 16px 14px !important;
        background: rgba(255, 255, 255, 0.88) !important;
    }

    /* =========================================================
       BRAND / LOGO
    ========================================================= */

    .sidebar .sidebar-brand {
        display: flex !important;
        align-items: center !important;
        gap: 11px !important;
        min-height: 52px !important;
        padding: 0 8px 14px !important;
        margin-bottom: 10px !important;
        text-decoration: none !important;
        border-bottom: 1px solid var(--border-hairline) !important;
    }

    .sidebar .brand-icon {
        display: flex !important;
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        align-items: center;
        justify-content: center;
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
        color: var(--brand-primary) !important;
        white-space: nowrap;
    }

    .sidebar .brand-text b {
        font-size: 17px !important;
        font-weight: 800 !important;
    }

    .sidebar .brand-text small {
        font-size: 9px !important;
        color: var(--text-muted) !important;
        white-space: nowrap;
    }

    /* =========================================================
       NAVIGATION
    ========================================================= */

    .sidebar .nav-caption {
        display: block !important;
        padding: 14px 10px 6px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: 0.08em !important;
        color: var(--text-muted) !important;
        white-space: nowrap;
    }

    .sidebar .nav-link {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;
        width: 100% !important;
        min-height: 42px !important;
        margin: 2px 0 !important;
        padding: 10px 12px !important;
        border-radius: 12px !important;
        text-decoration: none !important;
        color: var(--text-muted) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        white-space: nowrap;
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
        background: var(--bg-soft) !important;
        color: var(--brand-primary) !important;
    }

    .sidebar .nav-link:hover {
        background: #F4F8F5 !important;
        color: var(--brand-primary) !important;
    }

    /* =========================================================
       LOGOUT
    ========================================================= */

    .sidebar .logout {
        margin-top: auto !important;
        padding-top: 14px !important;
        border-top: 1px solid var(--border-hairline) !important;
    }

    .sidebar .logout button {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 12px !important;
        width: 100% !important;
        min-height: 42px !important;
        padding: 10px 12px !important;
        border: 0 !important;
        border-radius: 12px !important;
        background: transparent !important;
        color: var(--text-muted) !important;
        font: 700 12px 'Plus Jakarta Sans', sans-serif !important;
        cursor: pointer;
        white-space: nowrap;
    }

    .sidebar .logout button span {
        display: inline !important;
    }

    .sidebar .logout button:hover {
        background: #fff4f4 !important;
        color: #b91c1c !important;
    }

    /* =========================================================
       COLLAPSED ICON RAIL
    ========================================================= */

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
    .app-layout.sidebar-closed .sidebar .logout button span,
    html.sipadu-sidebar-closed .app-layout .sidebar .brand-text,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-caption,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link span,
    html.sipadu-sidebar-closed .app-layout .sidebar .logout button span {
        display: none !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-link,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link {
        justify-content: center !important;
        gap: 0 !important;
        padding: 10px 0 !important;
    }

    .app-layout.sidebar-closed .sidebar .logout button,
    html.sipadu-sidebar-closed .app-layout .sidebar .logout button {
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

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 900px) {
        .sidebar {
            width: 248px !important;
        }

        .app-layout.sidebar-closed .sidebar .brand-text,
        .app-layout.sidebar-closed .sidebar .nav-caption,
        .app-layout.sidebar-closed .sidebar .nav-link span,
        .app-layout.sidebar-closed .sidebar .logout button span,
        html.sipadu-sidebar-closed .app-layout .sidebar .brand-text,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-caption,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-link span,
        html.sipadu-sidebar-closed .app-layout .sidebar .logout button span {
            display: flex !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-link {
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 10px 12px !important;
        }

        .app-layout.sidebar-closed .sidebar .logout button {
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 10px 12px !important;
        }
    }
</style>

<aside class="sidebar" id="mainSidebar">

    {{-- =====================================================
       LOGO SIPADU
    ====================================================== --}}

    <a
        class="brand sidebar-brand"
        href="{{ $isAdmin ? route('admin.dashboard') : route('borrower.dashboard') }}"
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

    {{-- MENU CAPTION --}}
    <div class="nav-caption">
        {{ $isAdmin ? 'MENU ADMIN' : 'MENU PEMINJAM' }}
    </div>

    @if ($isAdmin)

        {{-- =====================================================
           MENU ADMIN
        ====================================================== --}}

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
            <span>Pengajuan</span>
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

    @else

        {{-- =====================================================
           MENU PEMINJAM
        ====================================================== --}}

        <a
            class="nav-link {{ request()->routeIs('borrower.dashboard') ? 'active' : '' }}"
            href="{{ route('borrower.dashboard') }}"
            title="Beranda"
        >
            <i data-lucide="house"></i>
            <span>Beranda</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('borrower.booking.create') ? 'active' : '' }}"
            href="{{ route('borrower.booking.create') }}"
            title="Ajukan Peminjaman"
        >
            <i data-lucide="calendar-plus"></i>
            <span>Ajukan Peminjaman</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('borrower.bookings*') ? 'active' : '' }}"
            href="{{ route('borrower.bookings') }}"
            title="Peminjaman Saya"
        >
            <i data-lucide="clipboard-check"></i>
            <span>Peminjaman Saya</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('borrower.guides') ? 'active' : '' }}"
            href="{{ route('borrower.guides') }}"
            title="SOP & FAQ"
        >
            <i data-lucide="book-open"></i>
            <span>SOP & FAQ</span>
        </a>

        <a
            class="nav-link {{ request()->routeIs('borrower.profile*') ? 'active' : '' }}"
            href="{{ route('borrower.profile') }}"
            title="Profil Akun"
        >
            <i data-lucide="user-round"></i>
            <span>Profil Akun</span>
        </a>

    @endif

    {{-- =====================================================
       LOGOUT
    ====================================================== --}}

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

<script
    src="https://unpkg.com/lucide@latest"
    onload="if(window.lucide){window.lucide.createIcons();}"
></script>

<script>
    (function () {
        function renderSidebarIcons() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener(
                'DOMContentLoaded',
                renderSidebarIcons
            );
        } else {
            renderSidebarIcons();
        }

        window.addEventListener(
            'load',
            renderSidebarIcons
        );
    })();
</script>