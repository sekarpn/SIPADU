
@extends('layouts.app')

@section('content')

<style>
    * {
        box-sizing: border-box;
    }

    .app-layout {
        display: flex;
        width: 100%;
        height: 100vh;
        min-height: 100vh;
        overflow: hidden;
        background: #f5f7f6;
    }

    /* SIDEBAR */
    .app-layout > .sidebar {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        width: 76px;
        min-width: 76px;
        flex: 0 0 76px;
        height: 100vh;
        overflow-x: hidden;
        overflow-y: auto;
        transition: width .3s ease, min-width .3s ease, flex-basis .3s ease;
        z-index: 10;
    }

    .app-layout > .sidebar.expanded {
        width: 260px;
        min-width: 260px;
        flex: 0 0 260px;
    }

    /* AREA UTAMA */
    .app-layout > main {
        flex: 1 1 0;
        min-width: 0;
        width: 0;
        height: 100vh;
        margin: 0 !important;
        overflow-y: auto;
        overflow-x: hidden;
        background: #f5f7f6;
    }

    /* TOPBAR */
    .app-layout > main .topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 72px;
        height: 72px;
        padding: 0 28px;
        background: #fff;
        border-bottom: 1px solid #e8eee9;
        position: sticky;
        top: 0;
        z-index: 5;
    }

    /* TOMBOL SIDEBAR */
    #sidebarToggle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        padding: 0;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #34443a;
        cursor: pointer;
        transition: background .2s ease;
        flex-shrink: 0;
    }

    #sidebarToggle:hover {
        background: #edf5ef;
    }

    #sidebarToggle svg {
        width: 25px;
        height: 25px;
    }

    /* BAGIAN KANAN TOPBAR */
    .topbar-user {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #244332;
        min-width: 0;
    }

    .topbar-date {
        font-size: 12px;
        color: #718078;
        margin-right: 12px;
        white-space: nowrap;
    }

    .topbar-user-info {
        text-align: right;
        min-width: 0;
    }

    .topbar-user-info strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #244332;
    }

    .topbar-user-info small {
        display: block;
        font-size: 11px;
        color: #849188;
        margin-top: 3px;
    }

    .topbar-avatar {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 50%;
        background: #e8f5ec;
        color: #187345;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
    }

    /* KONTEN */
    .app-layout > main > .main-content {
        width: 100%;
        min-width: 0;
        padding: 24px;
    }

    @media (max-width: 768px) {
        .app-layout > .sidebar.expanded {
            width: 220px;
            min-width: 220px;
            flex-basis: 220px;
        }

        .app-layout > main .topbar {
            padding: 0 16px;
        }

        .app-layout > main > .main-content {
            padding: 16px;
        }

        .topbar-date {
            display: none;
        }
    }

    @media (max-width: 420px) {
        .topbar-user-info strong {
            font-size: 11px;
        }

        .topbar-user-info small {
            font-size: 10px;
        }

        .topbar-avatar {
            width: 34px;
            height: 34px;
            min-width: 34px;
        }
    }
</style>

<div class="app-layout">

    @include('partials.nav')

    <main>

        <div class="topbar">

            {{-- TOMBOL SIDEBAR --}}
            <button type="button"
                    id="sidebarToggle"
                    aria-label="Buka atau tutup sidebar">
                <i data-lucide="ellipsis"></i>
            </button>

            {{-- TANGGAL DAN PROFIL --}}
            @auth
                @php
                    $topbarUser = auth()->user();
                    $profileRoute = $topbarUser->role === 'admin'
                        ? route('admin.profile')
                        : route('borrower.profile');
                @endphp

                <a href="{{ $profileRoute }}" class="topbar-user">

                    <span class="topbar-date">
                        {{ now()->format('d/m/Y') }}
                    </span>

                    <span class="topbar-user-info">
                        <strong>{{ $topbarUser->name }}</strong>
                        <small>
                            {{ $topbarUser->role === 'admin' ? 'Administrator' : 'Peminjam' }}
                        </small>
                    </span>

                    <span class="topbar-avatar">
                        {{ strtoupper(substr($topbarUser->name, 0, 2)) }}
                    </span>

                </a>
            @endauth

        </div>

        <div class="main-content">
            @yield('panel')
        </div>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('mainSidebar');
    const toggle = document.getElementById('sidebarToggle');

    if (sidebar && toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('expanded');
        });
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }

});
</script>

@endsection
