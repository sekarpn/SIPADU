@extends('layouts.app')

@section('content')

<script>
    /* Apply sidebar state before the layout is painted to prevent page-change flicker. */
    (function () {
        try {
            if (localStorage.getItem('sipadu-sidebar-state') === 'closed') {
                document.documentElement.classList.add('sipadu-sidebar-closed');
            }
        } catch (e) {}
    })();
</script>

<style>
    html,
    body,
    .app-layout {
        background-color: #b5d6c5 !important;
        background-image: linear-gradient(135deg, #cfe6da 0%, #b5d6c5 52%, #a2c9b4 100%) !important;
        background-attachment: fixed !important;
        color: #163626;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .app-layout {
        display: flex;
        width: 100%;
        min-height: 100vh;
        overflow: visible;
    }

    /* SIDEBAR MENGAMBANG & GLASS REALISTIS */
    .app-layout > .sidebar {
        position: fixed !important;
        top: 14px !important;
        bottom: 14px !important;
        left: 14px !important;
        width: 248px !important;
        min-width: 248px !important;
        height: auto !important;
        max-height: calc(100vh - 28px) !important;
        border-radius: 16px !important;
        z-index: 50;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.30) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        box-shadow: 0 8px 28px rgba(22, 54, 38, 0.06) !important;
        overflow-x: hidden;
        overflow-y: auto;
        transition: width 0.24s ease, min-width 0.24s ease !important;
    }

    .app-layout > main {
        margin-left: 262px !important;
        flex: 1;
        min-width: 0;
        min-height: 100vh;
        background: transparent !important;
        overflow: visible !important;
        width: auto !important;
        transition: margin-left 0.24s ease !important;
    }

    /* Collapsed desktop sidebar */
    .app-layout.sidebar-closed > .sidebar,
    html.sipadu-sidebar-closed .app-layout > .sidebar {
        transform: none !important;
        width: 78px !important;
        min-width: 78px !important;
    }

    .app-layout.sidebar-closed > main,
    html.sipadu-sidebar-closed .app-layout > main {
        margin-left: 92px !important;
    }

    /* No animation during initial render */
    html:not(.sidebar-ready) .app-layout > .sidebar,
    html:not(.sidebar-ready) .app-layout > main {
        transition: none !important;
    }

    /* TOPBAR TRANSPARAN MINIMALIS */
    .app-layout > main > .topbar,
    main .topbar,
    .topbar {
        position: sticky;
        top: 0;
        z-index: 40;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 64px;
        height: 64px;
        padding: 14px 28px 6px;
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        border-bottom: none !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    /* TOMBOL TOGGLE SIDEBAR KACA */
    #sidebarToggle {
        display: flex !important;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        border-radius: 11px;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.30) 100%) !important;
        backdrop-filter: blur(14px) !important;
        -webkit-backdrop-filter: blur(14px) !important;
        color: #163626 !important;
        cursor: pointer;
        flex: none;
        box-shadow: 0 4px 14px rgba(22, 54, 38, 0.05) !important;
        transition: background 0.2s ease;
    }

    #sidebarToggle:hover {
        background: rgba(255, 255, 255, 0.72) !important;
        color: #1f7a49 !important;
    }

    #sidebarToggle svg {
        width: 18px;
        height: 18px;
    }

    /* BAGIAN KANAN TOPBAR (TANGGAL + AVATAR + CHEVRON) */
    .topbar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .topbar-date {
        font-size: 12px;
        font-weight: 600;
        color: #324a3c;
        white-space: nowrap;
        letter-spacing: 0.2px;
    }

    .topbar-user {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none !important;
        color: #163626 !important;
        padding: 3px 6px 3px 3px;
        border-radius: 999px;
        transition: background 0.2s ease;
    }

    .topbar-user:hover {
        background: rgba(255, 255, 255, 0.45);
    }

    .topbar-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #237c4d;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        font-weight: 800;
        box-shadow: 0 3px 10px rgba(35, 124, 77, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.45) !important;
    }

    .topbar-chevron {
        width: 14px;
        height: 14px;
        color: #324a3c;
    }

    .app-layout > main > .main-content {
        padding: 12px 28px 28px;
        max-width: 1600px;
        margin: 0 auto;
        opacity: 0;
        transform: translateY(6px);
        transition: opacity 0.18s ease, transform 0.18s ease;
    }

    html.page-ready .app-layout > main > .main-content {
        opacity: 1;
        transform: none;
    }

    @media (max-width: 900px) {
        .app-layout > main {
            margin-left: 0 !important;
        }

        .app-layout > .sidebar {
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            max-height: 100vh !important;
            border-radius: 0 16px 16px 0 !important;
            width: 248px !important;
            min-width: 248px !important;
            transform: translateX(-100%) !important;
            background: rgba(255, 255, 255, 0.92) !important;
        }

        .app-layout:not(.sidebar-closed) > .sidebar {
            transform: translateX(0) !important;
        }

        .app-layout > main .topbar {
            padding: 12px 18px 4px;
        }

        .app-layout > main > .main-content {
            padding: 12px 18px 24px;
        }

        .topbar-date {
            display: none;
        }

        .topbar-right {
            gap: 10px;
        }
    }
</style>

<div class="app-layout">
    @include('partials.nav')

    <main>
        <div class="topbar">
            <button
                type="button"
                id="sidebarToggle"
                aria-label="Buka atau tutup sidebar"
            >
                <i data-lucide="menu"></i>
            </button>

            <div class="topbar-right">
                @auth
                    @php
                        $topbarUser = auth()->user();

                        $profileRoute = $topbarUser->role === 'admin'
                            ? route('admin.profile')
                            : route('borrower.profile');
                    @endphp

                    <span class="topbar-date">
                        {{ now()->format('d/m/Y') }}
                    </span>

                    <a href="{{ $profileRoute }}" class="topbar-user" title="Profil Akun ({{ $topbarUser->name }})">
                        <span class="topbar-avatar">
                            {{ strtoupper(substr($topbarUser->name, 0, 2)) }}
                        </span>
                        <i data-lucide="chevron-down" class="topbar-chevron"></i>
                    </a>
                @endauth
            </div>
        </div>

        <div class="main-content">
            @yield('panel')
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const layout = document.querySelector('.app-layout');
        const toggle = document.getElementById('sidebarToggle');
        const storageKey = 'sipadu-sidebar-state';

        if (!layout || !toggle) return;

        const isInitiallyClosed =
            document.documentElement.classList.contains(
                'sipadu-sidebar-closed'
            );

        layout.classList.toggle(
            'sidebar-closed',
            isInitiallyClosed
        );

        const renderToggleIcon = () => {
            if (window.lucide) {
                toggle.innerHTML = '<i data-lucide="menu"></i>';
                window.lucide.createIcons();
            }
        };

        const setSidebar = (open) => {
            layout.classList.toggle(
                'sidebar-closed',
                !open
            );

            document.documentElement.classList.toggle(
                'sipadu-sidebar-closed',
                !open
            );

            try {
                localStorage.setItem(
                    storageKey,
                    open ? 'open' : 'closed'
                );
            } catch (e) {}

            renderToggleIcon();
        };

        renderToggleIcon();

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                document.documentElement.classList.add('sidebar-ready');
                document.documentElement.classList.add('page-ready');
            });
        });

        toggle.addEventListener('click', () => {
            setSidebar(layout.classList.contains('sidebar-closed'));
        });

        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>

@endsection