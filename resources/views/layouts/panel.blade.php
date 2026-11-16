
@extends('layouts.app')

@section('content')
<script>
/* Apply navigation state before paint to prevent sidebar flashes. */
(function () {
    try {
        var root = document.documentElement;

        if (window.matchMedia('(max-width: 900px)').matches) {
            root.classList.add('sipadu-mobile');
        } else if (localStorage.getItem('sipadu-sidebar-state') === 'closed') {
            root.classList.add('sipadu-sidebar-closed');
        }
    } catch (e) {}
})();
</script>

<style>
html,
body,
.app-layout {
    background-color: #e8f1eb !important;
    background-image: linear-gradient(
        135deg,
        #f1f6f2 0%,
        #e8f1eb 52%,
        #dcebe1 100%
    ) !important;
    background-attachment: fixed !important;
    color: #163626;
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

/* MAIN LAYOUT */
.app-layout {
    display: flex;
    width: 100%;
    min-height: 100vh;
    overflow: visible;
}

/* FLOATING GLASS SIDEBAR */
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

    background: linear-gradient(
        145deg,
        rgba(255,255,255,.54) 0%,
        rgba(255,255,255,.30) 100%
    ) !important;

    backdrop-filter: blur(18px) saturate(150%) !important;
    -webkit-backdrop-filter: blur(18px) saturate(150%) !important;

    border: 1px solid rgba(255,255,255,.38) !important;
    box-shadow: 0 8px 28px rgba(22,54,38,.06) !important;

    overflow-x: hidden;
    overflow-y: auto;
    transition: width .24s ease, min-width .24s ease !important;
}

/* MAIN CONTENT AREA */
.app-layout > main {
    margin-left: 262px !important;
    flex: 1;
    min-width: 0;
    min-height: 100vh;
    background: transparent !important;
    overflow: visible !important;
    width: auto !important;
    transition: margin-left .24s ease !important;
}

/* COLLAPSED DESKTOP SIDEBAR */
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

/* PREVENT INITIAL ANIMATION */
html:not(.sidebar-ready) .app-layout > .sidebar,
html:not(.sidebar-ready) .app-layout > main {
    transition: none !important;
}

/* FLOATING GLASS TOPBAR, MATCHING THE SIDEBAR */
.app-layout > main > .topbar {
    position: sticky;
    top: 14px;
    z-index: 40;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: calc(100% - 28px);
    min-height: 64px;
    height: 64px;
    margin: 14px auto 0;
    padding: 10px 22px;
    border-radius: 16px !important;
    background: linear-gradient(
        145deg,
        rgba(255,255,255,.54) 0%,
        rgba(255,255,255,.30) 100%
    ) !important;
    border: 1px solid rgba(255,255,255,.38) !important;
    box-shadow: 0 8px 28px rgba(22,54,38,.06) !important;
    backdrop-filter: blur(18px) saturate(150%) !important;
    -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
}

/* SIDEBAR TOGGLE */
#sidebarToggle {
    display: flex !important;
    width: 38px;
    height: 38px;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255,255,255,.38) !important;
    border-radius: 11px;
    background: linear-gradient(
        145deg,
        rgba(255,255,255,.54) 0%,
        rgba(255,255,255,.30) 100%
    ) !important;
    backdrop-filter: blur(14px) !important;
    -webkit-backdrop-filter: blur(14px) !important;
    color: #163626 !important;
    cursor: pointer;
    flex: none;
    box-shadow: 0 4px 14px rgba(22,54,38,.05) !important;
    transition: background .2s ease;
}

#sidebarToggle:hover {
    background: rgba(255,255,255,.72) !important;
    color: #1f7a49 !important;
}

#sidebarToggle svg {
    width: 18px;
    height: 18px;
}

/* TOPBAR RIGHT */
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
    letter-spacing: .2px;
}

.topbar-user {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none !important;
    color: #163626 !important;
    padding: 3px 6px 3px 3px;
    border-radius: 999px;
    transition: background .2s ease;
}

.topbar-user:hover {
    background: rgba(255,255,255,.45);
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
    color: #64786c;
}

.topbar-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #237c4d;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11.5px;
    font-weight: 800;
    box-shadow: 0 3px 10px rgba(35,124,77,.25);
    border: 1px solid rgba(255,255,255,.45) !important;
}

.topbar-chevron {
    width: 14px;
    height: 14px;
    color: #324a3c;
}

/* PAGE CONTENT */
.app-layout > main > .main-content {
    padding: 0 28px 28px;
    max-width: 1600px;
    margin: 16px auto 0;
    zoom: .97;
    opacity: 0;
    transform: translateY(6px);
    transition: opacity .18s ease, transform .18s ease;
}

html.page-ready .app-layout > main > .main-content {
    opacity: 1;
    transform: none;
}

/* MOBILE */
@media (max-width: 900px) {
    html,
    body {
        overflow-x: hidden !important;
        width: 100% !important;
    }

    .app-layout {
        display: block !important;
        position: relative;
        overflow-x: hidden !important;
    }

    .app-layout > main {
        margin-left: 0 !important;
        width: 100% !important;
        min-width: 0 !important;
    }

    .app-layout > .sidebar {
        top: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        height: 100vh !important;
        max-height: 100vh !important;
        width: min(84vw, 300px) !important;
        min-width: min(84vw, 300px) !important;
        border-radius: 0 16px 16px 0 !important;
        transform: translateX(-110%) !important;
        z-index: 1000 !important;
        background: rgba(255,255,255,.94) !important;
        box-shadow: 18px 0 40px rgba(15,35,28,.12);
        transition: transform .24s ease !important;
    }

    html.sipadu-mobile .app-layout.mobile-sidebar-open > .sidebar {
        transform: translateX(0) !important;
    }

    .sidebar-backdrop {
        position: fixed !important;
        inset: 0 !important;
        z-index: 990 !important;
        display: block !important;
        background: rgba(13,31,23,.30) !important;
        opacity: 0 !important;
        pointer-events: none !important;
        transition: opacity .2s ease !important;
    }

    html.sipadu-mobile .app-layout.mobile-sidebar-open .sidebar-backdrop {
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    .app-layout > main .topbar {
        width: calc(100% - 28px) !important;
        min-height: 60px !important;
        height: 60px !important;
        margin: 12px auto 0 !important;
        padding: 10px 16px !important;
        top: 10px !important;
        border-radius: 16px !important;
    }

    .app-layout > main > .main-content {
        margin-top: 14px !important;
        padding: 0 18px 24px !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .topbar-date {
        display: none !important;
    }

    .topbar-right {
        gap: 10px !important;
    }
}

@media (max-width: 520px) {
    .app-layout > main > .main-content {
        padding: 0 14px 20px !important;
        margin-top: 12px !important;
    }

    .app-layout > main .topbar {
        width: calc(100% - 20px) !important;
        padding: 8px 12px !important;
        margin-top: 10px !important;
        min-height: 58px !important;
        height: 58px !important;
        border-radius: 15px !important;
    }

    .topbar-user-info {
        display: none !important;
    }

    .topbar-right {
        gap: 6px !important;
    }

    #sidebarToggle {
        width: 36px !important;
        height: 36px !important;
    }
}
</style>

<div class="app-layout">
    @include('partials.nav')

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

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

                    <a
                        href="{{ $profileRoute }}"
                        class="topbar-user"
                        title="Profil Akun ({{ $topbarUser->name }})"
                    >
                        <span class="topbar-user-info">
                            <strong>{{ $topbarUser->name }}</strong>
                            <small>
                                {{ $topbarUser->role === 'admin' ? 'Administrator' : 'Peminjam' }}
                            </small>
                        </span>

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
    const backdrop = document.getElementById('sidebarBackdrop');
    const storageKey = 'sipadu-sidebar-state';

    if (!layout || !toggle) return;

    const mobile = () => window.matchMedia('(max-width: 900px)').matches;

    function getSavedState() {
        try {
            return localStorage.getItem(storageKey) || 'open';
        } catch (e) {
            return 'open';
        }
    }

    function saveState(open) {
        try {
            localStorage.setItem(storageKey, open ? 'open' : 'closed');
        } catch (e) {}
    }

    function renderIcon(open) {
        if (mobile()) {
            toggle.innerHTML = '<i data-lucide="menu"></i>';
        } else {
            toggle.innerHTML = open
                ? '<i data-lucide="panel-left-close"></i>'
                : '<i data-lucide="panel-left-open"></i>';
        }

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function desktopOpen(open, persist = true) {
        layout.classList.toggle('sidebar-closed', !open);

        document.documentElement.classList.toggle(
            'sipadu-sidebar-closed',
            !open
        );

        if (persist) {
            saveState(open);
        }

        renderIcon(open);
    }

    function mobileOpen(open) {
        layout.classList.toggle('mobile-sidebar-open', open);
        renderIcon(open);
    }

    function sync() {
        if (mobile()) {
            document.documentElement.classList.add('sipadu-mobile');

            layout.classList.remove('mobile-sidebar-open');
            layout.classList.remove('sidebar-closed');

            renderIcon(false);
        } else {
            document.documentElement.classList.remove('sipadu-mobile');

            const open = getSavedState() !== 'closed';

            desktopOpen(open, false);
        }
    }

    sync();

    requestAnimationFrame(() => {
        document.documentElement.classList.add('sidebar-ready');
        document.documentElement.classList.add('page-ready');
    });

    toggle.addEventListener('click', function () {
        if (mobile()) {
            mobileOpen(!layout.classList.contains('mobile-sidebar-open'));
        } else {
            desktopOpen(layout.classList.contains('sidebar-closed'));
        }
    });

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            mobileOpen(false);
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobile()) {
            mobileOpen(false);
        }
    });

    window.addEventListener('resize', sync);

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endsection
