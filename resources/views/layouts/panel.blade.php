@extends('layouts.app')

@section('content')
<script>
/* Apply navigation state before paint to prevent sidebar flashes. */
(function () {
    try {
        var root = document.documentElement;

        if (window.matchMedia('(max-width: 640px)').matches) {
            root.classList.add('sipadu-mobile');
        } else if (localStorage.getItem('sipadu-sidebar-state') === 'closed' || window.innerWidth <= 1024) {
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

/* TOPBAR RIGHT & DROPDOWN PROFIL + LOGOUT */
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

.topbar-profile-dropdown {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.topbar-user {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: transparent !important;
    background-color: transparent !important;
    box-shadow: none !important;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    color: #163626 !important;
    padding: 4px 8px 4px 6px;
    border-radius: 999px;
    transition: background .2s ease;
}

.topbar-user:hover,
.topbar-profile-dropdown.open .topbar-user {
    background: rgba(255,255,255,.52) !important;
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
    transition: transform .2s ease;
}

.topbar-profile-dropdown.open .topbar-chevron {
    transform: rotate(180deg);
}

.topbar-dropdown-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 225px;
    padding: 8px;
    border-radius: 15px;
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid #e2ece6;
    box-shadow: 0 12px 32px rgba(22, 54, 38, 0.14);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px) scale(.98);
    transform-origin: top right;
    transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
    z-index: 100;
}

.topbar-profile-dropdown.open .topbar-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.dropdown-user-header {
    padding: 8px 10px 10px;
    border-bottom: 1px solid #edf2ef;
    margin-bottom: 6px;
}

.dropdown-user-header strong {
    display: block;
    font-size: 12.5px;
    font-weight: 750;
    color: #163626;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.dropdown-user-header small {
    display: block;
    font-size: 11px;
    color: #64786c;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-top: 1px;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 9px 10px;
    border: none !important;
    border-radius: 10px !important;
    background: #ffffff !important;
    background-color: #ffffff !important;
    background-image: none !important;
    box-shadow: none !important;
    color: #163626 !important;
    font-size: 12.5px;
    font-weight: 650;
    text-decoration: none !important;
    cursor: pointer;
    text-align: left;
    transition: background .18s ease, color .18s ease;
    box-sizing: border-box;
}

.dropdown-item svg {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    color: #237c4d;
    transition: color .18s ease;
}

.dropdown-item:hover {
    background: #f2f7f4 !important;
    background-color: #f2f7f4 !important;
    color: #1f7a49 !important;
}

.dropdown-divider {
    height: 1px;
    background: #edf2ef;
    margin: 6px 0;
}

/* LOGOUT: TEKS DEFAULT SAMA SEPERTI PROFIL AKUN, IKON MERAH, HOVER MERAH */
.topbar-dropdown-menu a.dropdown-item.logout-item {
    background: #ffffff !important;
    background-color: #ffffff !important;
    color: #163626 !important;
}

.topbar-dropdown-menu a.dropdown-item.logout-item svg {
    color: #dc2626 !important;
    stroke: #dc2626 !important;
}

.topbar-dropdown-menu a.dropdown-item.logout-item:hover {
    background: #fef2f2 !important;
    background-color: #fef2f2 !important;
    color: #dc2626 !important;
}

.topbar-dropdown-menu a.dropdown-item.logout-item:hover svg {
    color: #b91c1c !important;
    stroke: #b91c1c !important;
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

/* =====================================================================
   POP-UP LENGKAPI DATA PROFIL (ANTI-GLITCH & BLUR SAMA DENGAN AUDITORIUM)
   ===================================================================== */
.sipadu-profile-modal-host {
    position: fixed !important;
    inset: 0 !important;
    width: 100vw !important;
    height: 100dvh !important;
    z-index: 9998 !important;
    zoom: 1 !important;
    background: rgba(12, 28, 19, 0.36) !important;
    background-color: rgba(12, 28, 19, 0.36) !important;
    backdrop-filter: blur(2px) !important;
    -webkit-backdrop-filter: blur(2px) !important;
    transform: translateZ(0) !important;
    -webkit-transform: translateZ(0) !important;
    backface-visibility: hidden !important;
    will-change: opacity !important;
    font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif !important;
}

.sipadu-profile-modal-host > div,
.sipadu-profile-modal-host > form,
.sipadu-profile-modal-host [class*="modal-box"],
.sipadu-profile-modal-host [class*="modal-card"],
.sipadu-profile-modal-host [class*="modal-content"],
.sipadu-profile-modal-host [class*="dialog"] {
    transform: translateZ(0);
    backface-visibility: hidden;
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

/* GAYA TABEL GLASS SERAGAM DI SELURUH HALAMAN */
.app-layout > main > .main-content table:not([class*="fc-"]) {
    width: 100% !important;
    table-layout: auto !important;
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
    white-space: normal !important;
    word-break: break-word !important;
    background: transparent !important;
    background-color: transparent !important;
    transition: background .18s ease !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) td > * {
    max-width: 100%;
    white-space: normal !important;
}

/* RAMPINGKAN KOLOM PERTAMA (AUDITORIUM) AGAR JARAK ANTAR KOLOM LEBIH PROPORSIONAL */
.app-layout > main > .main-content table:not([class*="fc-"]) th:first-child,
.app-layout > main > .main-content table:not([class*="fc-"]) td:first-child {
    width: 240px !important;
    max-width: 240px !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) tbody tr:hover td {
    background: rgba(255, 255, 255, .42) !important;
    background-color: rgba(255, 255, 255, .42) !important;
}

.app-layout > main > .main-content table:not([class*="fc-"]) tbody tr:last-child td {
    border-bottom: 0 !important;
}

/* BOTTOM BAR TABEL: KALIMAT "MENAMPILKAN ..." & NAVIGASI HALAMAN */
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
    justify-content: center;
    gap: 12px;
    min-width: 150px;
    flex: 0 0 auto;
}

.sipadu-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid #d8e4dc;
    border-radius: 8px;
    background: #ffffff;
    color: #237c4d;
    cursor: pointer;
    transition: background .18s ease, border-color .18s ease, color .18s ease, transform .18s ease;
}

.sipadu-page-btn:hover:not(:disabled) {
    background: #edf5ef;
    border-color: #237c4d;
    transform: translateY(-1px);
}

.sipadu-page-btn:disabled {
    color: #aab8af;
    background: #f5f7f5;
    border-color: #e7ece8;
    cursor: not-allowed;
    opacity: .7;
}

.sipadu-page-btn i,
.sipadu-page-btn svg {
    width: 16px;
    height: 16px;
}

.sipadu-page-indicator {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 32px;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid #d8e4dc;
    border-radius: 8px;
    background: #ffffff;
    color: #237c4d;
    font-size: 12px;
    font-weight: 500;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    box-sizing: border-box;
}

@media (max-width: 640px) {
    .sipadu-pager-bar {
        gap: 10px;
    }

    .sipadu-pager-nav {
        order: 3;
        width: 100%;
        justify-content: center;
    }
}

/* TOMBOL GESER HORIZONTAL TABEL DI KANAN BAWAH */
.sipadu-table-scroll-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    width: 100%;
    padding: 0 16px 12px;
    box-sizing: border-box;
}

.sipadu-table-scroll-controls {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    margin-left: auto;
}

.sipadu-table-scroll-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid rgba(35, 124, 77, .22);
    border-radius: 9px;
    background: rgba(255, 255, 255, .8);
    color: #237c4d;
    cursor: pointer;
    transition: background .18s ease, border-color .18s ease, transform .18s ease;
}

.sipadu-table-scroll-btn:hover:not(:disabled) {
    background: rgba(35, 124, 77, .1);
    border-color: rgba(35, 124, 77, .4);
    transform: translateY(-1px);
}

.sipadu-table-scroll-btn:disabled {
    opacity: .35;
    cursor: not-allowed;
}

.sipadu-table-scroll-btn svg {
    width: 16px;
    height: 16px;
}

/* PENYESUAIAN JARAK HALAMAN SAAT LAYAR DIBAGI DUA (SPLIT SCREEN) */
@media (max-width: 1024px) {
    .app-layout > main > .main-content {
        padding: 0 18px 24px !important;
    }

    .app-layout > main > .topbar {
        width: calc(100% - 24px) !important;
        padding: 10px 16px !important;
    }

    .topbar-date {
        display: none !important;
    }
}

/* MOBILE (HANYA DI BAWAH 640px, BUKAN SAAT LAYAR LAPTOP DIBAGI DUA) */
@media (max-width: 640px) {
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

    .topbar-right {
        gap: 10px !important;
    }

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
                        $topbarRole = strtolower((string) data_get($topbarUser, 'role', 'mahasiswa'));
                        $profileRoute = $topbarRole === 'admin'
                            ? route('admin.profile')
                            : ($topbarRole === 'pegawai' ? route('pegawai.profile') : route('borrower.profile'));

                        $topbarName = (string) data_get($topbarUser, 'name', 'Pengguna');
                        $topbarInitials = mb_strtoupper(mb_substr(trim($topbarName) !== '' ? trim($topbarName) : 'U', 0, 2));

                        $rawPhotoPath = trim((string) data_get($topbarUser, 'profile_photo_path', ''));
                        $cleanPhotoPath = ltrim(preg_replace('#^(public/|storage/)#', '', str_replace('\\', '/', $rawPhotoPath)), '/');

                        $topbarPhotoUrl = null;
                        if ($cleanPhotoPath !== '') {
                            if (\Illuminate\Support\Str::startsWith($rawPhotoPath, ['http://', 'https://', 'data:'])) {
                                $topbarPhotoUrl = $rawPhotoPath;
                            } else {
                                $publicStorageFile = public_path('storage/' . $cleanPhotoPath);
                                $appStorageFile = storage_path('app/public/' . $cleanPhotoPath);

                                if (is_file($publicStorageFile)) {
                                    $topbarPhotoUrl = asset('storage/' . $cleanPhotoPath) . '?v=' . filemtime($publicStorageFile);
                                } elseif (is_file($appStorageFile)) {
                                    $mime = @mime_content_type($appStorageFile) ?: 'image/jpeg';
                                    $topbarPhotoUrl = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($appStorageFile));
                                }
                            }
                        }
                    @endphp

                    <span class="topbar-date">
                        {{ now()->format('d/m/Y') }}
                    </span>

                    <div class="topbar-profile-dropdown" id="topbarProfileDropdown">
                        <button
                            type="button"
                            class="topbar-user"
                            id="topbarProfileBtn"
                            aria-expanded="false"
                            aria-haspopup="true"
                            title="Menu Akun ({{ $topbarName }})"
                        >
                            <span class="topbar-user-info">
                                <strong>{{ $topbarName }}</strong>
                                <small>
                                    {{ $topbarRole === 'admin' ? 'Administrator' : ($topbarRole === 'pegawai' ? 'Dosen & Tendik' : 'Mahasiswa') }}
                                </small>
                            </span>

                            <span class="topbar-avatar">
                                @if(!empty($topbarPhotoUrl))
                                    <img
                                        src="{{ $topbarPhotoUrl }}"
                                        alt="{{ $topbarName }}"
                                    >
                                @else
                                    {{ $topbarInitials }}
                                @endif
                            </span>

                            <i data-lucide="chevron-down" class="topbar-chevron"></i>
                        </button>

                        <div class="topbar-dropdown-menu" role="menu">
                            <div class="dropdown-user-header">
                                <strong>{{ $topbarName }}</strong>
                                <small>{{ $topbarUser->email }}</small>
                            </div>

                            <a href="{{ $profileRoute }}" class="dropdown-item" role="menuitem">
                                <i data-lucide="user-round"></i>
                                <span>Profil Akun</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <a
                                href="{{ route('logout') }}"
                                class="dropdown-item logout-item"
                                role="menuitem"
                                onclick="event.preventDefault(); document.getElementById('topbar-logout-form').submit();"
                            >
                                <i data-lucide="log-out"></i>
                                <span>Logout</span>
                            </a>

                            <form id="topbar-logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>
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

    const mobile = () => window.matchMedia('(max-width: 640px)').matches;

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

            const isHalfScreen = window.innerWidth <= 1024;
            const saved = getSavedState();
            const open = saved === 'closed' ? false : !isHalfScreen;

            desktopOpen(open, false);
        }
    }

    sync();

    requestAnimationFrame(function () {
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

    /* DROPDOWN PROFIL & LOGOUT DI TOPBAR */
    const profileDropdown = document.getElementById('topbarProfileDropdown');
    const profileBtn = document.getElementById('topbarProfileBtn');

    if (profileDropdown && profileBtn) {
        profileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = profileDropdown.classList.toggle('open');
            profileBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (e) {
            if (!profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('open');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (mobile()) {
                mobileOpen(false);
            }
            if (profileDropdown && profileDropdown.classList.contains('open')) {
                profileDropdown.classList.remove('open');
                profileBtn.setAttribute('aria-expanded', 'false');
            }
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
        if (!table || table.dataset.sipaduPaginated === '1' || table.dataset.noPagination === '1' || table.closest('[class*="fc-"]')) return;
        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const allRows = Array.from(tbody.querySelectorAll('tr'));
        const dataRows = allRows.filter(function (tr) {
            return !(tr.children.length === 1 && tr.children[0].hasAttribute('colspan'));
        });

        table.dataset.sipaduPaginated = '1';

        let card = table.closest('.sipadu-glass-card, [class*="-card"], .table-wrap, .dashboard-table-wrap, .panel');
        let scrollWrap = table.parentElement;

        if (!scrollWrap || (!scrollWrap.classList.contains('sipadu-table-scroll') && !scrollWrap.classList.contains('table-wrap') && !scrollWrap.classList.contains('dashboard-table-wrap'))) {
            const wrapper = document.createElement('div');
            wrapper.className = 'sipadu-table-scroll';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
            scrollWrap = wrapper;
        } else {
            scrollWrap.classList.add('sipadu-table-scroll');
        }

        if (!card) {
            card = document.createElement('div');
            card.className = 'sipadu-glass-card';
            scrollWrap.parentNode.insertBefore(card, scrollWrap);
            card.appendChild(scrollWrap);
        } else {
            card.classList.add('sipadu-glass-card');
        }

        if (dataRows.length === 0) return;

        let pageSize = 10;
        let currentPage = 1;

        const topBar = document.createElement('div');
        topBar.className = 'sipadu-table-top';
        topBar.innerHTML =
            '<label class="sipadu-pager-size">Tampilkan ' +
            '<select class="sipadu-pager-select" aria-label="Jumlah data per halaman">' +
            '<option value="5">5</option>' +
            '<option value="10" selected>10</option>' +
            '<option value="25">25</option>' +
            '<option value="50">50</option>' +
            '</select> data</label>';

        const bottomBar = document.createElement('div');
        bottomBar.className = 'sipadu-pager-bar';
        bottomBar.innerHTML =
            '<span class="sipadu-pager-info"></span>' +
            '<div class="sipadu-pager-nav">' +
            '<button type="button" class="sipadu-page-btn" data-dir="prev" aria-label="Halaman sebelumnya"><i data-lucide="chevron-left"></i></button>' +
            '<span class="sipadu-page-indicator">1</span>' +
            '<button type="button" class="sipadu-page-btn" data-dir="next" aria-label="Halaman berikutnya"><i data-lucide="chevron-right"></i></button>' +
            '</div>';

        scrollWrap.parentNode.insertBefore(topBar, scrollWrap);
        scrollWrap.parentNode.insertBefore(bottomBar, scrollWrap.nextSibling);

        const select = topBar.querySelector('.sipadu-pager-select');
        const info = bottomBar.querySelector('.sipadu-pager-info');
        const indicator = bottomBar.querySelector('.sipadu-page-indicator');
        const prevBtn = bottomBar.querySelector('[data-dir="prev"]');
        const nextBtn = bottomBar.querySelector('[data-dir="next"]');

        function renderPage() {
            const total = dataRows.length;
            const totalPages = Math.max(1, Math.ceil(total / pageSize));
            if (currentPage > totalPages) currentPage = totalPages;

            const startIdx = (currentPage - 1) * pageSize;
            const endIdx = Math.min(startIdx + pageSize, total);

            dataRows.forEach(function (row, idx) {
                row.style.display = (idx >= startIdx && idx < endIdx) ? '' : 'none';
            });

            info.textContent = 'Menampilkan ' + (startIdx + 1) + ' - ' + endIdx + ' dari ' + total + ' data';
            indicator.textContent = currentPage;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
        }

        select.addEventListener('change', function () {
            pageSize = parseInt(this.value, 10) || 10;
            currentPage = 1;
            renderPage();
        });

        prevBtn.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
            }
        });

        nextBtn.addEventListener('click', function () {
            const totalPages = Math.max(1, Math.ceil(dataRows.length / pageSize));
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
            }
        });

        renderPage();
    }

    document.querySelectorAll('.main-content table').forEach(initTablePagination);

    /* HOIST POP-UP LENGKAPI DATA PROFIL KE ROOT BODY AGAR BEBAS GLITCH & BLUR SAMA DENGAN AUDITORIUM */
    function stabilizeProfileModals() {
        const candidates = document.querySelectorAll(
            '.main-content [id*="profil" i], .main-content [id*="profile" i], .main-content [class*="profil" i], .main-content [class*="profile" i], .main-content [role="dialog"]'
        );

        candidates.forEach(function (el) {
            if (
                el.dataset.sipaduProfileHoisted === '1' ||
                el.classList.contains('auditorium-modal') ||
                el.classList.contains('topbar-profile-dropdown')
            ) {
                return;
            }

            const style = window.getComputedStyle(el);
            const text = (el.textContent || '').toLowerCase();
            const isFixedOverlay = style.position === 'fixed' && (
                text.includes('lengkapi data profil') ||
                text.includes('lengkapi profil') ||
                /modal|overlay|backdrop|popup/.test((el.id + ' ' + el.className).toLowerCase())
            );

            if (isFixedOverlay) {
                el.dataset.sipaduProfileHoisted = '1';
                el.classList.add('sipadu-profile-modal-host');
                document.body.appendChild(el);
            }
        });
    }

    stabilizeProfileModals();

    const mainContent = document.querySelector('.main-content');
    if (mainContent && window.MutationObserver) {
        const profileModalObserver = new MutationObserver(function () {
            stabilizeProfileModals();
        });
        profileModalObserver.observe(mainContent, { childList: true, subtree: true });
    }

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>
@include('components.sipadu-chatbot')
@endsection