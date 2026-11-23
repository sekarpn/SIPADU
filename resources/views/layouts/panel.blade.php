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
    font-size: 13px;
}

/* Konsistensi tipografi seluruh halaman setelah login: Admin, Pegawai, dan Mahasiswa */
.app-layout,
.app-layout *,
.app-layout *::before,
.app-layout *::after {
    font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif !important;
}

/* Elemen ikon tetap menggunakan bentuk SVG, bukan font glyph */
.app-layout svg,
.app-layout svg * {
    font-family: inherit !important;
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

/* SIDEBAR NAVIGATION HOVER (SOFT GREEN ACCENT) */
.app-layout > .sidebar a,
.app-layout > .sidebar .nav-link,
.app-layout > .sidebar .nav-item,
.app-layout > .sidebar .side-link {
    transition: background .2s ease, color .2s ease, border-color .2s ease, transform .18s ease !important;
}

.app-layout > .sidebar a:not(.active):not(.is-active):not([aria-current="page"]):not(.sidebar-brand):not(.brand):not(.logo):hover,
.app-layout > .sidebar .nav-link:not(.active):not(.is-active):not([aria-current="page"]):hover,
.app-layout > .sidebar .nav-item:not(.active):not(.is-active):not([aria-current="page"]):hover,
.app-layout > .sidebar .side-link:not(.active):not(.is-active):not([aria-current="page"]):hover {
    background: rgba(33, 128, 77, .14) !important;
    background-color: rgba(33, 128, 77, .14) !important;
    color: #163626 !important;
    border-color: rgba(33, 128, 77, .22) !important;
}

.app-layout > .sidebar a:not(.active):not(.is-active):not([aria-current="page"]):not(.sidebar-brand):not(.brand):not(.logo):hover svg,
.app-layout > .sidebar .nav-link:not(.active):not(.is-active):not([aria-current="page"]):hover svg,
.app-layout > .sidebar .nav-item:not(.active):not(.is-active):not([aria-current="page"]):hover svg,
.app-layout > .sidebar .side-link:not(.active):not(.is-active):not([aria-current="page"]):hover svg {
    color: #1f7a49 !important;
    stroke: #1f7a49 !important;
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
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(35,124,77,.25);
    border: 1px solid rgba(255,255,255,.45) !important;
}

.topbar-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
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

/* SAMAKAN JARAK TOPBAR KE JUDUL HALAMAN MENGIKUTI DASHBOARD ADMIN (padding-top: 10px) */
.app-layout > main > .main-content > div:first-of-type:not([class*="card"]):not([class*="panel"]),
.app-layout > main > .main-content > section:first-of-type {
    margin-top: 0 !important;
    padding-top: 10px !important;
}

.app-layout > main > .main-content > div:first-of-type > [class*="head"]:first-of-type,
.app-layout > main > .main-content > div:first-of-type > [class*="topbar"]:first-of-type,
.app-layout > main > .main-content > div:first-of-type > header:first-of-type,
.app-layout > main > .main-content > div:first-of-type > div:first-of-type:has(> h1, > div > h1) {
    margin-top: 0 !important;
}

.app-layout > main > .main-content h1 {
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.app-layout > main > .main-content > h1:first-child {
    margin-top: 10px !important;
}

/* UNDERLINE TABS SERAGAM DI SELURUH HALAMAN */
.app-layout > main > .main-content .sipadu-underline-tabs,
.app-layout > main > .main-content .up-tabs,
.app-layout > main > .main-content .bp-tabs,
.app-layout > main > .main-content .ap-tabs,
.app-layout > main > .main-content .cp-tabs,
.app-layout > main > .main-content .hp-tabs,
.app-layout > main > .main-content .mp-tabs,
.app-layout > main > .main-content .rp-tabs {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    overflow-x: auto !important;
    padding: 0 18px !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 1px solid rgba(22, 54, 38, .12) !important;
    border-radius: 0 !important;
    background: rgba(255, 255, 255, .18) !important;
    box-shadow: none !important;
}

.app-layout > main > .main-content .sipadu-underline-tab,
.app-layout > main > .main-content .up-tab,
.app-layout > main > .main-content .bp-tab,
.app-layout > main > .main-content .ap-tab,
.app-layout > main > .main-content .cp-tab,
.app-layout > main > .main-content .hp-tab,
.app-layout > main > .main-content .mp-tab,
.app-layout > main > .main-content .rp-tab {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    padding: 14px 18px !important;
    margin: 0 !important;
    border: 0 !important;
    border-bottom: 2.5px solid transparent !important;
    border-radius: 0 !important;
    background: transparent !important;
    background-color: transparent !important;
    box-shadow: none !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #4c6456 !important;
    white-space: nowrap !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transform: none !important;
    transition: color .2s ease, border-color .2s ease !important;
}

.app-layout > main > .main-content .sipadu-underline-tab:hover:not(.active):not(.is-active):not([aria-selected="true"]):not([aria-current="page"]),
.app-layout > main > .main-content .up-tab:hover:not(.active),
.app-layout > main > .main-content .bp-tab:hover:not(.active),
.app-layout > main > .main-content .ap-tab:hover:not(.active),
.app-layout > main > .main-content .cp-tab:hover:not(.active),
.app-layout > main > .main-content .hp-tab:hover:not(.active),
.app-layout > main > .main-content .mp-tab:hover:not(.active),
.app-layout > main > .main-content .rp-tab:hover:not(.active) {
    background: transparent !important;
    background-color: transparent !important;
    color: #163626 !important;
    transform: none !important;
}

.app-layout > main > .main-content .sipadu-underline-tab.active,
.app-layout > main > .main-content .sipadu-underline-tab.is-active,
.app-layout > main > .main-content .sipadu-underline-tab[aria-selected="true"],
.app-layout > main > .main-content .sipadu-underline-tab[aria-current="page"],
.app-layout > main > .main-content .up-tab.active,
.app-layout > main > .main-content .bp-tab.active,
.app-layout > main > .main-content .ap-tab.active,
.app-layout > main > .main-content .cp-tab.active,
.app-layout > main > .main-content .hp-tab.active,
.app-layout > main > .main-content .mp-tab.active,
.app-layout > main > .main-content .rp-tab.active {
    background: transparent !important;
    background-color: transparent !important;
    color: #163626 !important;
    font-weight: 700 !important;
    border-bottom: 2.5px solid #237c4d !important;
}

.app-layout > main > .main-content .sipadu-underline-tab span,
.app-layout > main > .main-content .up-tab span,
.app-layout > main > .main-content .bp-tab span,
.app-layout > main > .main-content .ap-tab span,
.app-layout > main > .main-content .cp-tab span,
.app-layout > main > .main-content .hp-tab span,
.app-layout > main > .main-content .mp-tab span,
.app-layout > main > .main-content .rp-tab span {
    padding: 0 !important;
    border-radius: 0 !important;
    background: transparent !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #64786c !important;
}

.app-layout > main > .main-content .sipadu-underline-tab span::before,
.app-layout > main > .main-content .up-tab span::before,
.app-layout > main > .main-content .bp-tab span::before,
.app-layout > main > .main-content .ap-tab span::before,
.app-layout > main > .main-content .cp-tab span::before,
.app-layout > main > .main-content .hp-tab span::before,
.app-layout > main > .main-content .mp-tab span::before,
.app-layout > main > .main-content .rp-tab span::before {
    content: "(";
}

.app-layout > main > .main-content .sipadu-underline-tab span::after,
.app-layout > main > .main-content .up-tab span::after,
.app-layout > main > .main-content .bp-tab span::after,
.app-layout > main > .main-content .ap-tab span::after,
.app-layout > main > .main-content .cp-tab span::after,
.app-layout > main > .main-content .hp-tab span::after,
.app-layout > main > .main-content .mp-tab span::after,
.app-layout > main > .main-content .rp-tab span::after {
    content: ")";
}

.app-layout > main > .main-content .sipadu-underline-tab.active span,
.app-layout > main > .main-content .sipadu-underline-tab.is-active span,
.app-layout > main > .main-content .sipadu-underline-tab[aria-selected="true"] span,
.app-layout > main > .main-content .sipadu-underline-tab[aria-current="page"] span,
.app-layout > main > .main-content .up-tab.active span,
.app-layout > main > .main-content .bp-tab.active span,
.app-layout > main > .main-content .ap-tab.active span,
.app-layout > main > .main-content .cp-tab.active span,
.app-layout > main > .main-content .hp-tab.active span,
.app-layout > main > .main-content .mp-tab.active span,
.app-layout > main > .main-content .rp-tab.active span {
    color: #237c4d !important;
    font-weight: 700 !important;
}

/* =====================================================================
   KARTU & TABEL GLASSMORPHISM SERAGAM SEPERTI HALAMAN DATA PENGGUNA
   ===================================================================== */
.app-layout > main > .main-content .sipadu-glass-card {
    background: linear-gradient(
        145deg,
        rgba(255, 255, 255, .68) 0%,
        rgba(255, 255, 255, .42) 100%
    ) !important;
    background-color: transparent !important;
    backdrop-filter: blur(18px) saturate(150%) !important;
    -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
    border: 1px solid rgba(255, 255, 255, .55) !important;
    box-shadow: 0 8px 28px rgba(22, 54, 38, .06) !important;
    border-radius: 16px !important;
    overflow: hidden !important;
}

/* TOP BAR TABEL: TAMPILKAN [10 v] DATA */
.sipadu-table-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding: 12px 18px;
    border-bottom: 1px solid rgba(22, 54, 38, .09);
    background: rgba(255, 255, 255, .25);
    font-size: 12.5px;
    color: #4c6456;
    box-sizing: border-box;
}

.sipadu-pager-size {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-weight: 500;
    color: #294735;
}

.sipadu-pager-select {
    appearance: auto;
    height: 34px;
    padding: 0 10px;
    border: 1px solid rgba(22, 54, 38, .15);
    border-radius: 9px;
    background: rgba(255, 255, 255, .86);
    color: #163626;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.sipadu-pager-select:focus {
    border-color: #237c4d;
    box-shadow: 0 0 0 3px rgba(35, 124, 77, .14);
}

/* SCROLL HORIZONTAL TABEL AGAR BISA DIGESER */
.sipadu-table-scroll {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* GAYA TABEL GLASS SERAGAM DI SELURUH HALAMAN (IDENTIK DENGAN DATA PENGGUNA) */
.app-layout > main > .main-content table:not([class*="fc-"]) {
    width: 100% !important;
    border-collapse: collapse !important;
    min-width: 780px;
    font-size: 13px !important;
    background: transparent !important;
    background-color: transparent !important;
}

.app-layout > main > .main-content .dash-tables-grid table:not([class*="fc-"]) {
    min-width: 560px;
}

.app-layout > main > .main-content table:not([class*="fc-"]) thead,
.app-layout > main > .main-content table:not([class*="fc-"]) thead tr {
    background: transparent !important;
    background-color: transparent !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) th {
    padding: 13px 18px !important;
    text-align: left !important;
    background: rgba(255, 255, 255, .36) !important;
    background-color: rgba(255, 255, 255, .36) !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 1px solid rgba(22, 54, 38, .09) !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    letter-spacing: .07em !important;
    text-transform: uppercase !important;
    color: #4c6456 !important;
    white-space: nowrap !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) tbody,
.app-layout > main > .main-content table:not([class*="fc-"]) tbody tr {
    background: transparent !important;
    background-color: transparent !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) td {
    padding: 15px 18px !important;
    border-top: 0 !important;
    border-left: 0 !important;
    border-right: 0 !important;
    border-bottom: 1px solid rgba(22, 54, 38, .06) !important;
    vertical-align: middle !important;
    font-size: 12.5px !important;
    line-height: 1.5 !important;
    color: #163626 !important;
    background: transparent !important;
    background-color: transparent !important;
    transition: background .18s ease !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) tbody tr:hover td {
    background: rgba(255, 255, 255, .42) !important;
    background-color: rgba(255, 255, 255, .42) !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) tbody tr:last-child td {
    border-bottom: 0 !important;
}

/* BOTTOM BAR TABEL: KALIMAT "MENAMPILKAN ..." (TIDAK BOLD) & NAVIGASI HALAMAN */
.sipadu-pager-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-wrap: wrap;
    padding: 13px 18px;
    border-top: 1px solid rgba(22, 54, 38, .09);
    background: rgba(255, 255, 255, .25);
    font-size: 12.5px;
    color: #4c6456;
    box-sizing: border-box;
}

.sipadu-pager-info {
    font-weight: 400 !important;
    font-size: 12.5px;
    color: #4c6456;
}

.sipadu-pager-nav {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

/* SLIDER NAVIGASI HALAMAN TABEL, TANPA TOMBOL NOMOR */
.sipadu-pager-nav {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: min(340px, 100%);
    flex: 0 1 340px;
}

.sipadu-page-range {
    width: 100%;
    min-width: 120px;
    height: 6px;
    margin: 0;
    appearance: none;
    -webkit-appearance: none;
    border-radius: 999px;
    background: linear-gradient(to right, #237c4d 0%, #dce8df 0%);
    outline: none;
    cursor: pointer;
}

.sipadu-page-range::-webkit-slider-thumb {
    appearance: none;
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    border: 3px solid #ffffff;
    border-radius: 50%;
    background: #237c4d;
    box-shadow: 0 1px 5px rgba(22, 54, 38, .2);
}

.sipadu-page-range::-moz-range-thumb {
    width: 11px;
    height: 11px;
    border: 3px solid #ffffff;
    border-radius: 50%;
    background: #237c4d;
    box-shadow: 0 1px 5px rgba(22, 54, 38, .2);
}

.sipadu-page-indicator {
    flex: 0 0 auto;
    min-width: 76px;
    text-align: right;
    color: #4c6456;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
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

@media (max-width: 640px) {
    .app-layout > main > .main-content .sipadu-underline-tabs,
    .app-layout > main > .main-content .up-tabs,
    .app-layout > main > .main-content .bp-tabs,
    .app-layout > main > .main-content .ap-tabs,
    .app-layout > main > .main-content .cp-tabs,
    .app-layout > main > .main-content .hp-tabs,
    .app-layout > main > .main-content .mp-tabs,
    .app-layout > main > .main-content .rp-tabs {
        padding: 0 12px !important;
    }

    .app-layout > main > .main-content .sipadu-underline-tab,
    .app-layout > main > .main-content .up-tab,
    .app-layout > main > .main-content .bp-tab,
    .app-layout > main > .main-content .ap-tab,
    .app-layout > main > .main-content .cp-tab,
    .app-layout > main > .main-content .hp-tab,
    .app-layout > main > .main-content .mp-tab,
    .app-layout > main > .main-content .rp-tab {
        padding: 12px 14px !important;
        font-size: 12.5px !important;
    }

    .sipadu-table-top {
        padding: 11px 14px;
    }

    .sipadu-pager-bar {
        padding: 12px 14px;
        flex-direction: column;
        align-items: flex-start;
    }

    .sipadu-pager-nav {
        width: 100%;
        justify-content: flex-end;
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
                            : ($topbarUser->role === 'pegawai' ? route('pegawai.profile') : route('borrower.profile'));
                        $topbarPhotoPath = data_get($topbarUser, 'profile_photo_path');
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
                                {{ $topbarUser->role === 'admin' ? 'Administrator' : ($topbarUser->role === 'pegawai' ? 'Pegawai' : 'Mahasiswa') }}
                            </small>
                        </span>

                        <span class="topbar-avatar">
                            @if(!empty($topbarPhotoPath))
                                <img
                                    src="{{ asset('storage/' . $topbarPhotoPath) }}"
                                    alt="{{ $topbarUser->name }}"
                                >
                            @else
                                {{ strtoupper(substr($topbarUser->name, 0, 2)) }}
                            @endif
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

    /* SERAGAMKAN SEMUA TAB FILTER MENJADI UNDERLINE TAB DI SELURUH HALAMAN */
    document.querySelectorAll('.main-content [class*="-tabs"], .main-content .tabs, .main-content [role="tablist"]').forEach(function (tabBar) {
        if (tabBar.closest('table') || tabBar.closest('.bp-drawer') || tabBar.closest('form')) return;
        const items = Array.from(tabBar.children).filter(function (el) {
            return el.matches('a, button, [role="tab"]');
        });
        if (items.length < 2) return;

        tabBar.classList.add('sipadu-underline-tabs');
        items.forEach(function (item) {
            item.classList.add('sipadu-underline-tab');
        });
    });

    /* PAGINASI, GLASS CARD & SCROLL HORIZONTAL OTOMATIS UNTUK SEMUA TABEL */
    function initTablePagination(table) {
        if (!table || table.dataset.sipaduPaginated === '1') return;
        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        table.dataset.sipaduPaginated = '1';

        /* Bungkus tabel dengan container scroll horizontal jika belum ada */
        let scrollWrap = table.parentElement;
        if (!scrollWrap || getComputedStyle(scrollWrap).overflowX === 'visible') {
            const wrapper = document.createElement('div');
            wrapper.className = 'sipadu-table-scroll';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
            scrollWrap = wrapper;
        } else {
            scrollWrap.style.webkitOverflowScrolling = 'touch';
        }

        /* Samakan pembungkus kartu menjadi glassmorphism seperti halaman Data Pengguna */
        let cardContainer = scrollWrap.parentElement;
        if (cardContainer && !cardContainer.classList.contains('main-content')) {
            cardContainer.classList.add('sipadu-glass-card');
        } else {
            const glassBox = document.createElement('div');
            glassBox.className = 'sipadu-glass-card';
            scrollWrap.parentNode.insertBefore(glassBox, scrollWrap);
            glassBox.appendChild(scrollWrap);
            cardContainer = glassBox;
        }

        /* 1. TOP BAR: Dropdown "Tampilkan [10 v] data" di atas tabel */
        const topBar = document.createElement('div');
        topBar.className = 'sipadu-table-top';
        topBar.innerHTML =
            '<label class="sipadu-pager-size">' +
                '<span>Tampilkan</span>' +
                '<select class="sipadu-pager-select" aria-label="Jumlah data per halaman">' +
                    '<option value="10" selected>10</option>' +
                    '<option value="25">25</option>' +
                    '<option value="50">50</option>' +
                    '<option value="100">100</option>' +
                '</select>' +
                '<span>data</span>' +
            '</label>';

        scrollWrap.insertAdjacentElement('beforebegin', topBar);

        /* 2. BOTTOM BAR: jumlah data + slider untuk berpindah halaman */
        const pagerBar = document.createElement('div');
        pagerBar.className = 'sipadu-pager-bar';
        pagerBar.innerHTML =
            '<span class="sipadu-pager-info"></span>' +
            '<div class="sipadu-pager-nav" aria-label="Navigasi halaman tabel">' +
                '<input class="sipadu-page-range" type="range" min="1" max="1" value="1" step="1" aria-label="Geser untuk memilih halaman">' +
                '<span class="sipadu-page-indicator">Halaman 1 / 1</span>' +
            '</div>';

        const existingPagination = cardContainer.querySelector('.up-pagination, .bp-pagination, [class*="-pagination"]');
        if (existingPagination) {
            existingPagination.innerHTML = '';
            existingPagination.style.padding = '0';
            existingPagination.style.borderTop = '0';
            existingPagination.style.background = 'transparent';
            existingPagination.appendChild(pagerBar);
        } else {
            scrollWrap.insertAdjacentElement('afterend', pagerBar);
            const nextEl = pagerBar.nextElementSibling;
            if (nextEl && (nextEl.matches('nav[role="navigation"]') || nextEl.classList.contains('pagination'))) {
                nextEl.style.display = 'none';
            }
        }

        const perPageSelect = topBar.querySelector('.sipadu-pager-select');
        const infoEl = pagerBar.querySelector('.sipadu-pager-info');
        const pageRange = pagerBar.querySelector('.sipadu-page-range');
        const pageIndicator = pagerBar.querySelector('.sipadu-page-indicator');

        let currentPage = 1;
        let perPage = parseInt(perPageSelect.value, 10) || 10;

        function getDataRows() {
            return Array.from(tbody.querySelectorAll('tr')).filter(function (tr) {
                if (tr.dataset.emptyRow === '1') return false;
                if (tr.id === 'userFilterEmptyRow') return false;
                if (tr.querySelector('td[colspan]')) return false;
                return true;
            });
        }

        function render() {
            const allRows = getDataRows();
            const activeRows = allRows.filter(function (tr) {
                return tr.dataset.filterHidden !== '1';
            });

            const totalItems = activeRows.length;
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const startIdx = (currentPage - 1) * perPage;
            const endIdx = Math.min(startIdx + perPage, totalItems);

            allRows.forEach(function (tr) {
                if (tr.dataset.filterHidden === '1') {
                    tr.style.display = 'none';
                }
            });

            activeRows.forEach(function (tr, idx) {
                tr.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
            });

            const fromNum = totalItems === 0 ? 0 : startIdx + 1;
            const toNum = totalItems === 0 ? 0 : endIdx;
            infoEl.textContent = 'Menampilkan ' + fromNum + ' - ' + toNum + ' dari ' + totalItems + ' data';

            pageRange.max = String(totalPages);
            pageRange.value = String(currentPage);
            pageRange.disabled = totalPages <= 1 || totalItems === 0;
            pageIndicator.textContent = 'Halaman ' + currentPage + ' / ' + totalPages;

            const progress = totalPages <= 1 ? 0 : ((currentPage - 1) / (totalPages - 1)) * 100;
            pageRange.style.background = 'linear-gradient(to right, #237c4d ' + progress + '%, #dce8df ' + progress + '%)';
        }

        pageRange.addEventListener('input', function () {
            currentPage = parseInt(this.value, 10) || 1;
            render();
        });

        perPageSelect.addEventListener('change', function () {
            perPage = parseInt(this.value, 10) || 10;
            currentPage = 1;
            render();
        });

        table.sipaduRefreshPagination = function (resetPage) {
            if (resetPage) currentPage = 1;
            render();
        };

        render();
    }

    document.querySelectorAll('.main-content table:not([class*="fc-"])').forEach(initTablePagination);

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@endsection