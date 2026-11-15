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
    .app-layout {
        display: flex;
        width: 100%;
        min-height: 100vh;
        background: var(--bg-canvas);
        overflow: visible;
    }

    .app-layout > .sidebar {
        position: fixed !important;
        inset: 0 auto 0 0 !important;
        width: 248px !important;
        min-width: 248px !important;
        height: 100vh !important;
        z-index: 50;
        background: rgba(255, 255, 255, 0.88) !important;
        border-right: 1px solid rgba(220, 229, 224, 0.82) !important;
        overflow-x: hidden;
        overflow-y: auto;
        transition: width 0.24s ease, min-width 0.24s ease !important;
    }

    .app-layout > main {
        margin-left: 248px !important;
        flex: 1;
        min-width: 0;
        min-height: 100vh;
        background: var(--bg-canvas) !important;
        overflow: visible !important;
        width: auto !important;
        transition: margin-left 0.24s ease !important;
    }

    /* Collapsed desktop sidebar: icon rail remains visible */
    .app-layout.sidebar-closed > .sidebar,
    html.sipadu-sidebar-closed .app-layout > .sidebar {
        transform: none !important;
        width: 78px !important;
        min-width: 78px !important;
    }

    .app-layout.sidebar-closed > main,
    html.sipadu-sidebar-closed .app-layout > main {
        margin-left: 78px !important;
    }

    /* No animation during initial render, so the sidebar never flashes open/closed when changing pages. */
    html:not(.sidebar-ready) .app-layout > .sidebar,
    html:not(.sidebar-ready) .app-layout > main {
        transition: none !important;
    }

    .app-layout > main .topbar {
        position: sticky;
        top: 0;
        z-index: 40;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 60px;
        height: 60px;
        padding: 0 22px;
        background: rgba(245, 249, 247, 0.72) !important;
        border-bottom: 1px solid rgba(220, 229, 224, 0.68) !important;
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        box-shadow: 0 8px 28px rgba(20, 32, 26, 0.025);
    }

    #sidebarToggle {
        display: flex !important;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(220, 229, 224, 0.85) !important;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.78) !important;
        color: var(--text-main) !important;
        cursor: pointer;
        flex: none;
        box-shadow: 0 2px 8px rgba(20, 32, 26, 0.035);
    }

    #sidebarToggle:hover {
        background: var(--bg-soft) !important;
        color: var(--brand-primary) !important;
    }

    .topbar-right {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .topbar-user {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none !important;
        color: var(--text-main) !important;
    }

    .topbar-date {
        font-size: 11px;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .topbar-user-info {
        text-align: right;
    }

    .topbar-user-info strong {
        display: block;
        font-size: 12px;
    }

    .topbar-user-info small {
        font-size: 10px;
        color: var(--text-muted);
    }

    .topbar-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--brand-primary);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 800;
    }

    .app-layout > main > .main-content {
        padding: 28px;
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
            width: 248px !important;
            min-width: 248px !important;
            transform: translateX(-100%) !important;
        }

        .app-layout:not(.sidebar-closed) > .sidebar {
            transform: translateX(0) !important;
        }

        .app-layout > main .topbar {
            padding: 0 16px;
        }

        .app-layout > main > .main-content {
            padding: 18px;
        }

        .topbar-date {
            display: none;
        }

        .topbar-right {
            gap: 8px;
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
                <i data-lucide="panel-left-open"></i>
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

                    <a href="{{ $profileRoute }}" class="topbar-user">
                        <span class="topbar-user-info">
                            <strong>{{ $topbarUser->name }}</strong>

                            <small>
                                {{ $topbarUser->role === 'admin'
                                    ? 'Administrator'
                                    : 'Peminjam' }}
                            </small>
                        </span>

                        <span class="topbar-avatar">
                            {{ strtoupper(substr($topbarUser->name, 0, 2)) }}
                        </span>
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

        const renderToggleIcon = (open) => {
            if (window.lucide) {
                toggle.innerHTML = open
                    ? '<i data-lucide="panel-left-close"></i>'
                    : '<i data-lucide="panel-left-open"></i>';

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

            renderToggleIcon(open);
        };

        renderToggleIcon(!isInitiallyClosed);

        /* Enable animation only after the initial state has been applied. */
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                document.documentElement.classList.add(
                    'sidebar-ready'
                );

                document.documentElement.classList.add(
                    'page-ready'
                );
            });
        });

        toggle.addEventListener('click', () => {
            setSidebar(
                layout.classList.contains('sidebar-closed')
            );
        });

        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>

@endsection