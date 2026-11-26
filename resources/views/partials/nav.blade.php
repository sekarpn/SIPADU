@php
    $isAdmin = auth()->user()->role === 'admin';
    $isEmployee = auth()->user()->role === 'pegawai';
    $userRoutePrefix = $isEmployee ? 'pegawai.' : 'borrower.';
    $settingsRouteName = $isAdmin ? 'admin.profile' : ($userRoutePrefix . 'profile');
    $settingsUrl = route($settingsRouteName);
    $isSettingsRoute = request()->routeIs($settingsRouteName . '*');

    $activeSettingsTab = request('tab', 'profil');
    if ($errors->hasAny(['current_password', 'password', 'password_confirmation']) || session('password_success')) {
        $activeSettingsTab = 'password';
    } elseif (session('preferences_success')) {
        $activeSettingsTab = 'notifikasi';
    }
    if (!in_array($activeSettingsTab, ['profil', 'password', 'notifikasi'], true)) {
        $activeSettingsTab = 'profil';
    }

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
        padding: 14px 12px !important;
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
        gap: 10px !important;
        min-height: 46px !important;
        padding: 0 8px 11px !important;
        margin-bottom: 6px !important;
        text-decoration: none !important;
        border-bottom: 1px solid rgba(22,54,38,.08) !important;
    }

    .sidebar .brand-icon {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 38px !important;
        height: 38px !important;
        min-width: 38px !important;
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
        font-size: 16.5px !important;
        font-weight: 800 !important;
        color: #1f7a49 !important;
        letter-spacing: -.2px;
    }

    .sidebar .brand-text small {
        font-size: 9px !important;
        color: #4c6456 !important;
        white-space: nowrap;
    }

    /* NAVIGATION (JARAK LEBIH RAPAT) */
    .sidebar .nav-caption {
        display: block !important;
        padding: 6px 10px 4px !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        letter-spacing: .08em !important;
        color: #4c6456 !important;
        white-space: nowrap;
    }

    .sidebar .nav-link,
    .sidebar .nav-dropdown-toggle {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 10px !important;
        width: 100% !important;
        min-height: 37px !important;
        margin: 1.5px 0 !important;
        padding: 8px 11px !important;
        border-radius: 10px !important;
        text-decoration: none !important;
        background: transparent !important;
        background-color: transparent !important;
        background-image: none !important;
        color: #3c5446 !important;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', sans-serif !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        line-height: 1.25 !important;
        text-align: left !important;
        white-space: nowrap;
        border: 1px solid transparent !important;
        box-shadow: none !important;
        cursor: pointer;
        transition: background .18s ease, color .18s ease, border-color .18s ease;
    }

    .sidebar .nav-link svg,
    .sidebar .nav-dropdown-toggle svg {
        width: 17px !important;
        height: 17px !important;
        min-width: 17px !important;
    }

    .sidebar .nav-link span,
    .sidebar .nav-dropdown-toggle span {
        display: inline !important;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-dropdown-toggle.active {
        position: relative !important;
        background: rgba(33,128,77,.16) !important;
        color: #1f7a49 !important;
        border: 1px solid rgba(255,255,255,.35) !important;
        box-shadow: none !important;
    }

    .sidebar .nav-link.active::before,
    .sidebar .nav-dropdown-toggle.active::before {
        content: '' !important;
        position: absolute !important;
        top: 7px !important;
        bottom: 7px !important;
        left: 0 !important;
        width: 3px !important;
        border-radius: 0 4px 4px 0 !important;
        background: #238458 !important;
    }

    .sidebar .nav-link:hover:not(.active),
    .sidebar .nav-dropdown-toggle:hover:not(.active) {
        background: rgba(33, 128, 77, .12) !important;
        color: #163626 !important;
    }

    /* DROPDOWN PENGATURAN DI SIDEBAR */
    .sidebar .nav-dropdown {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .sidebar .nav-dropdown-caret {
        margin-left: auto !important;
        width: 15px !important;
        height: 15px !important;
        min-width: 15px !important;
        transition: transform .2s ease !important;
    }

    .sidebar .nav-dropdown-toggle.is-open .nav-dropdown-caret {
        transform: rotate(180deg) !important;
    }

    .sidebar .nav-submenu {
        display: none;
        flex-direction: column;
        gap: 6px !important;
        width: calc(100% - 16px);
        margin: 5px 0 6px 16px !important;
        padding-left: 10px;
        border-left: 1.5px solid rgba(35, 124, 77, .22);
        box-sizing: border-box;
    }

    .sidebar .nav-submenu.is-open {
        display: flex;
    }

    .sidebar .nav-sublink {
        display: flex !important;
        align-items: center !important;
        gap: 9px !important;
        width: 100% !important;
        min-height: 33px !important;
        padding: 6px 10px !important;
        border-radius: 9px !important;
        text-decoration: none !important;
        color: #4c6456 !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        white-space: nowrap;
        border: 1px solid transparent !important;
        box-sizing: border-box !important;
        transition: background .18s ease, color .18s ease;
    }

    .sidebar .nav-sublink svg {
        width: 15px !important;
        height: 15px !important;
        min-width: 15px !important;
    }

    .sidebar .nav-sublink:hover:not(.active) {
        background: rgba(33, 128, 77, .10) !important;
        color: #163626 !important;
    }

    .sidebar .nav-sublink.active {
        background: rgba(33, 128, 77, .15) !important;
        color: #1f7a49 !important;
        font-weight: 700 !important;
    }

    /* BADGE NOTIFIKASI PENGAJUAN */
    .sidebar .nav-badge {
        margin-left: auto !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 20px !important;
        height: 20px !important;
        padding: 0 6px !important;
        border-radius: 999px !important;
        background: #ef4444 !important;
        color: #ffffff !important;
        font-size: 10px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.32) !important;
        flex-shrink: 0 !important;
    }

    /* COLLAPSED SIDEBAR */
    .app-layout.sidebar-closed .sidebar,
    html.sipadu-sidebar-closed .app-layout .sidebar {
        padding: 14px 8px !important;
    }

    .app-layout.sidebar-closed .sidebar .sidebar-brand,
    html.sipadu-sidebar-closed .app-layout .sidebar .sidebar-brand {
        justify-content: center !important;
        gap: 0 !important;
        padding: 0 0 11px !important;
    }

    .app-layout.sidebar-closed .sidebar .brand-text,
    .app-layout.sidebar-closed .sidebar .nav-caption,
    .app-layout.sidebar-closed .sidebar .nav-link span,
    .app-layout.sidebar-closed .sidebar .nav-dropdown-toggle span,
    .app-layout.sidebar-closed .sidebar .nav-dropdown-caret,
    .app-layout.sidebar-closed .sidebar .nav-sublink span,
    html.sipadu-sidebar-closed .app-layout .sidebar .brand-text,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-caption,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link span,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-dropdown-toggle span,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-dropdown-caret,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-sublink span {
        display: none !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-link,
    .app-layout.sidebar-closed .sidebar .nav-dropdown-toggle,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-link,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-dropdown-toggle {
        position: relative !important;
        justify-content: center !important;
        gap: 0 !important;
        padding: 9px 0 !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-submenu,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-submenu {
        width: 100% !important;
        margin: 5px 0 6px 0 !important;
        padding-left: 0 !important;
        border-left: 0 !important;
        gap: 6px !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-sublink,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-sublink {
        justify-content: center !important;
        padding: 7px 0 !important;
    }

    .app-layout.sidebar-closed .sidebar .nav-badge,
    html.sipadu-sidebar-closed .app-layout .sidebar .nav-badge {
        position: absolute !important;
        top: 4px !important;
        right: 11px !important;
        margin-left: 0 !important;
        min-width: 16px !important;
        height: 16px !important;
        padding: 0 4px !important;
        font-size: 9px !important;
    }

    .app-layout.sidebar-closed .sidebar .brand-icon,
    html.sipadu-sidebar-closed .app-layout .sidebar .brand-icon {
        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
    }

    /* RESPONSIVE (HANYA UNTUK LAYAR HP DI BAWAH 640px) */
    @media (max-width: 640px) {
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
        .app-layout.sidebar-closed .sidebar .nav-dropdown-toggle span,
        .app-layout.sidebar-closed .sidebar .nav-dropdown-caret,
        .app-layout.sidebar-closed .sidebar .nav-sublink span,
        html.sipadu-sidebar-closed .app-layout .sidebar .brand-text,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-caption,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-link span,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-dropdown-toggle span,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-dropdown-caret,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-sublink span {
            display: inline !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-link,
        .app-layout.sidebar-closed .sidebar .nav-dropdown-toggle,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-link,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-dropdown-toggle {
            justify-content: flex-start !important;
            gap: 10px !important;
            padding: 8px 11px !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-submenu,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-submenu {
            width: calc(100% - 16px) !important;
            margin: 5px 0 6px 16px !important;
            padding-left: 10px !important;
            border-left: 1.5px solid rgba(35, 124, 77, .22) !important;
            gap: 6px !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-sublink,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-sublink {
            justify-content: flex-start !important;
            padding: 6px 10px !important;
        }

        .app-layout.sidebar-closed .sidebar .nav-badge,
        html.sipadu-sidebar-closed .app-layout .sidebar .nav-badge {
            position: static !important;
            margin-left: auto !important;
            min-width: 20px !important;
            height: 20px !important;
            padding: 0 6px !important;
            font-size: 10px !important;
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

    {{-- MENU PEMINJAM (MAHASISWA & PEGAWAI) --}}
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

    @endif

    {{-- DROPDOWN PENGATURAN (SEMUA ROLE) --}}
    <div class="nav-dropdown">
        <button
            type="button"
            class="nav-link nav-dropdown-toggle {{ $isSettingsRoute ? 'active is-open' : '' }}"
            id="sidebarSettingsToggle"
            aria-expanded="{{ $isSettingsRoute ? 'true' : 'false' }}"
            title="Pengaturan"
        >
            <i data-lucide="settings"></i>
            <span>Pengaturan</span>
            <i data-lucide="chevron-down" class="nav-dropdown-caret"></i>
        </button>

        <div class="nav-submenu {{ $isSettingsRoute ? 'is-open' : '' }}" id="sidebarSettingsSubmenu">
            <a
                class="nav-sublink {{ $isSettingsRoute && $activeSettingsTab === 'profil' ? 'active' : '' }}"
                href="{{ $settingsUrl }}?tab=profil"
                data-settings-sublink="profil"
                title="Profil"
            >
                <i data-lucide="user"></i>
                <span>Profil</span>
            </a>

            <a
                class="nav-sublink {{ $isSettingsRoute && $activeSettingsTab === 'password' ? 'active' : '' }}"
                href="{{ $settingsUrl }}?tab=password"
                data-settings-sublink="password"
                title="Password"
            >
                <i data-lucide="lock"></i>
                <span>Password</span>
            </a>

            <a
                class="nav-sublink {{ $isSettingsRoute && $activeSettingsTab === 'notifikasi' ? 'active' : '' }}"
                href="{{ $settingsUrl }}?tab=notifikasi"
                data-settings-sublink="notifikasi"
                title="Notifikasi"
            >
                <i data-lucide="bell"></i>
                <span>Notifikasi</span>
            </a>
        </div>
    </div>

</aside>

{{-- LUCIDE ICON --}}
<script src="https://unpkg.com/lucide@latest" onload="if (window.lucide) { window.lucide.createIcons(); }"></script>

<script>
(function () {
    function renderSidebarIcons() {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function initSidebarDropdown() {
        const toggleBtn = document.getElementById('sidebarSettingsToggle');
        const submenu = document.getElementById('sidebarSettingsSubmenu');
        const sublinks = document.querySelectorAll('[data-settings-sublink]');

        if (toggleBtn && submenu) {
            toggleBtn.addEventListener('click', function () {
                const nextOpen = !submenu.classList.contains('is-open');
                submenu.classList.toggle('is-open', nextOpen);
                toggleBtn.classList.toggle('is-open', nextOpen);
                toggleBtn.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
            });
        }

        sublinks.forEach(function (link) {
            link.addEventListener('click', function (e) {
                const targetTab = link.dataset.settingsSublink;
                if (typeof window.sipaduActivateSettingsTab === 'function' && targetTab) {
                    e.preventDefault();
                    window.sipaduActivateSettingsTab(targetTab);
                    sublinks.forEach(function (item) {
                        item.classList.toggle('active', item.dataset.settingsSublink === targetTab);
                    });
                    if (window.history && window.history.replaceState) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('tab', targetTab);
                        window.history.replaceState({}, '', url.toString());
                    }
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            renderSidebarIcons();
            initSidebarDropdown();
        });
    } else {
        renderSidebarIcons();
        initSidebarDropdown();
    }

    window.addEventListener('load', renderSidebarIcons);
})();
</script>