@extends('layouts.panel')

@section('panel')

@php
    $user = auth()->user();
@endphp

<style>
    /* BACKGROUND GRADASI HALUS AGAR EFEK BLUR KACA TERLIHAT NYATA */
    body,
    .main-wrapper,
    .content-wrapper,
    main {
        background-color: #b5d6c5 !important;
        background-image: linear-gradient(135deg, #cfe6da 0%, #b5d6c5 52%, #a2c9b4 100%) !important;
        background-attachment: fixed !important;
        min-height: 100vh;
    }

    /* Cegah halaman dashboard melebar dan memunculkan scrollbar horizontal */
    html,
    body {
        max-width: 100%;
        overflow-x: clip !important;
    }

    .app-layout {
        width: 100%;
        max-width: 100%;
        overflow-x: clip !important;
    }

    .app-layout > main,
    .app-layout > main > .main-content,
    .dashboard-page {
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .dashboard-page {
        width: 100%;
        padding: 10px 10px 35px;
        overflow-x: clip;
    }

    /* HEADER */
    .dashboard-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .dashboard-welcome h1 {
        font-size: 27px;
        font-weight: 750;
        color: #163626;
        margin: 0 0 5px;
        letter-spacing: -0.3px;
    }

    .dashboard-welcome p {
        font-size: 13px;
        color: #324a3c;
        margin: 0;
    }

    .dashboard-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 10px;
        background: #237c4d;
        color: white;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        box-shadow: 0 4px 14px rgba(35, 124, 77, 0.25);
        transition: .2s ease;
        white-space: nowrap;
    }

    .dashboard-action:hover {
        background: #1a633c;
        color: white;
        transform: translateY(-2px);
    }

    .dashboard-action svg {
        width: 16px;
        height: 16px;
    }

    /* STATISTIK (GLASSMORPHISM REALISTIS & STATIS) */
    .stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.28) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        border-radius: 15px;
        padding: 17px 18px;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.05) !important;
    }

    .stat-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }

    .stat-top small {
        color: #324a3c;
        font-size: 12.5px;
        font-weight: 600;
    }

    .stat-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .stat-icon svg {
        width: 16px;
        height: 16px;
    }

    .stat-icon.blue {
        background: rgba(33, 128, 77, 0.16);
        color: #1f7a49;
    }

    .stat-icon.amber {
        background: rgba(217, 119, 6, 0.16);
        color: #d97706;
    }

    .stat-icon.green {
        background: rgba(22, 128, 74, 0.18);
        color: #16804a;
    }

    .stat-icon.red {
        background: rgba(220, 69, 69, 0.16);
        color: #dc4545;
    }

    .stat-card strong {
        display: block;
        font-size: 26px;
        font-weight: 750;
        color: #163626;
        line-height: 1.2;
    }

    .stat-card p {
        margin: 6px 0 0;
        color: #4c6456;
        font-size: 11px;
    }

    /* PANEL TENGAH */
    .dashboard-middle {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(280px, 1fr);
        gap: 16px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    .dashboard-panel {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.54) 0%, rgba(255, 255, 255, 0.28) 100%) !important;
        backdrop-filter: blur(18px) saturate(150%) !important;
        -webkit-backdrop-filter: blur(18px) saturate(150%) !important;
        border: 1px solid rgba(255, 255, 255, 0.38) !important;
        border-radius: 16px;
        padding: 14px 20px 18px 20px !important;
        min-width: 0;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.05) !important;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
    }

    .dashboard-panel .section-head {
        margin-top: 0 !important;
        padding-top: 0 !important;
        margin-bottom: 14px;
    }

    .dashboard-panel .section-head h2 {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .section-head h2 {
        font-size: 15.5px;
        font-weight: 750;
        color: #163626;
        margin: 0 0 2px;
    }

    .section-head p {
        font-size: 11px;
        color: #4c6456;
        margin: 0;
    }

    .section-head a {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: #1f7a49;
        text-decoration: none;
        font-size: 11.5px;
        font-weight: 700;
        white-space: nowrap;
        transition: gap 0.2s ease, color 0.2s ease;
    }

    .section-head a svg {
        width: 14px;
        height: 14px;
    }

    .section-head a:hover {
        color: #155a35;
        gap: 7px;
    }

    /* TABEL PENGAJUAN (SEJAJAR ATAS & BAWAH DENGAN INFORMASI SIPADU) */
    .dashboard-table-wrap {
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-top: 4px;
        background: rgba(255, 255, 255, 0.42) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-radius: 12px;
        overflow-x: hidden;
        overflow-y: hidden;
    }

    .dashboard-table {
        width: 100%;
        min-width: 0;
        max-width: 100%;
        height: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        font-size: 12px;
    }

    .dashboard-table th,
    .dashboard-table td {
        overflow-wrap: anywhere;
        word-break: normal;
    }

    .dashboard-table th {
        background: rgba(35, 124, 77, 0.14) !important;
        color: #163626 !important;
        font-size: 11px;
        font-weight: 750;
        text-align: left;
        padding: 13px 14px;
        border-bottom: none !important;
    }

    .dashboard-table td {
        color: #243b2e;
        font-weight: 500;
        padding: 14px 14px;
        border-bottom: 1px solid rgba(22, 54, 38, 0.06);
        vertical-align: middle;
        transition: background 0.2s ease;
    }

    .dashboard-table tbody tr:hover td {
        background: rgba(255, 255, 255, 0.55) !important;
    }

    .dashboard-table tr:last-child td {
        border-bottom: none;
    }

    .dashboard-table .empty {
        text-align: center;
        color: #4c6456;
        padding: 25px 10px;
    }

    .status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status.pending {
        color: #b4690e;
        background: rgba(254, 240, 213, 0.92);
    }

    .status.disetujui {
        color: #157343;
        background: rgba(220, 243, 229, 0.92);
    }

    .status.ditolak {
        color: #c53030;
        background: rgba(254, 226, 226, 0.92);
    }

    .status.dibatalkan {
        color: #475569;
        background: rgba(226, 232, 240, 0.92);
    }

    /* INFORMASI SIPADU (KOTAK GLASS SUB-ITEM SIMETRIS) */
    .guide-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 4px;
        flex: 1;
    }

    .guide-item {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        background: rgba(255, 255, 255, 0.42) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-radius: 12px;
        padding: 11px 13px;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .guide-item:hover {
        background: rgba(255, 255, 255, 0.65) !important;
        transform: translateX(2px);
    }

    .guide-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(33, 128, 77, 0.16);
        color: #1f7a49;
    }

    .guide-icon svg {
        width: 16px;
        height: 16px;
    }

    .guide-item strong {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: #163626;
        margin-bottom: 2px;
    }

    .guide-item p {
        font-size: 11px;
        color: #4c6456;
        line-height: 1.4;
        margin: 0;
    }

    /* AUDITORIUM (GLASS SUB-ITEM, SLIDER MULTI-FOTO & GAMBAR FULL BLEED) */
    .auditorium-section {
        margin-top: 0;
    }

    .auditorium-carousel-wrap {
        position: relative;
        min-width: 0;
    }

    .auditorium-carousel-controls {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .auditorium-arrow {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(35, 124, 77, .22);
        border-radius: 50%;
        background: rgba(255,255,255,.72);
        color: #237c4d;
        cursor: pointer;
        transition: background .2s ease, transform .2s ease, opacity .2s ease;
    }

    .auditorium-arrow:hover { background: #e5f3e9; transform: scale(1.06); }
    .auditorium-arrow svg { width: 17px; height: 17px; }

    .auditorium-cards {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        overflow-y: hidden;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        scrollbar-width: none;
        padding: 3px 2px 12px;
        overscroll-behavior-x: contain;
    }
    .auditorium-cards::-webkit-scrollbar { display: none; }
    .auditorium-cards > .auditorium-card {
        flex: 0 0 calc((100% - 32px) / 3);
        scroll-snap-align: start;
    }

    .auditorium-card {
        background: rgba(255, 255, 255, 0.42) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        border-radius: 14px !important;
        overflow: hidden !important;
        min-width: 0;
        box-shadow: 0 4px 16px rgba(22, 54, 38, 0.04) !important;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        padding: 0 !important;
        transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
    }

    .auditorium-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.68) !important;
        box-shadow: 0 12px 26px rgba(22, 54, 38, 0.1) !important;
    }

    .auditorium-card:focus-visible {
        outline: 2px solid #237c4d;
        outline-offset: 3px;
    }

    .auditorium-card.is-inactive-card {
        opacity: 0.92;
    }

    .auditorium-image-wrap {
        width: 100% !important;
        height: 175px !important;
        overflow: hidden !important;
        position: relative;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #eaf4ee;
    }

    /* SLIDER & SLIDESHOW FOTO AUDITORIUM (SAMA DENGAN ADMIN) */
    .aud-slider {
        width: 100%;
        height: 175px;
        position: relative;
        overflow: hidden;
    }

    .aud-slider.is-modal-slider {
        height: 250px;
    }

    .aud-slider-track {
        display: flex;
        width: 100%;
        height: 100%;
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .aud-slide {
        flex: 0 0 100%;
        width: 100%;
        height: 100%;
    }

    .aud-slide img,
    .auditorium-card img,
    .auditorium-image-wrap img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 0 !important;
        margin: 0 !important;
        transition: transform .2s ease;
    }

    .auditorium-card:hover .aud-slide img {
        transform: scale(1.04);
    }

    .aud-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.92);
        color: #163626;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.16);
        opacity: 0.88;
        transition: background .2s ease, transform .2s ease, opacity .2s ease;
    }

    .aud-slider-btn:hover {
        background: #ffffff;
        opacity: 1;
        transform: translateY(-50%) scale(1.08);
    }

    .aud-slider-btn.prev {
        left: 10px;
    }

    .aud-slider-btn.next {
        right: 10px;
    }

    .aud-slider-count {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 3;
        padding: 3px 9px;
        border-radius: 999px;
        background: rgba(15, 32, 23, 0.62);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.02em;
        backdrop-filter: blur(4px);
    }

    .aud-slider.is-modal-slider .aud-slider-count {
        right: auto;
        left: 14px;
        top: 14px;
    }

    .aud-slider-dots {
        position: absolute;
        bottom: 9px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 3;
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(15, 32, 23, 0.38);
        backdrop-filter: blur(4px);
    }

    .aud-slider-dot {
        width: 6px;
        height: 6px;
        padding: 0;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.52);
        cursor: pointer;
        transition: width .2s ease, background .2s ease;
    }

    .aud-slider-dot.is-active {
        width: 14px;
        border-radius: 999px;
        background: #ffffff;
    }

    .auditorium-no-image {
        width: 100% !important;
        height: 175px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #dff0e5, #eef7f1);
        color: #237c4d;
        border-radius: 0 !important;
        margin: 0 !important;
    }

    .auditorium-no-image svg {
        width: 32px;
        height: 32px;
    }

    .auditorium-card-content {
        padding: 14px 16px 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .auditorium-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }

    .auditorium-card-content h3 {
        margin: 0;
        color: #163626;
        font-size: 16px;
        font-weight: 750;
        line-height: 1.35;
    }

    .aud-status-pill {
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .aud-status-pill.is-active {
        color: #065f46;
        background: #bbf7d0;
        border: 1px solid #34d399;
    }

    .aud-status-pill.is-inactive {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fca5a5;
    }

    .auditorium-description {
        color: #4c6456;
        font-size: 11.5px;
        line-height: 1.5;
        margin: 0 0 10px;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 34px;
    }

    .auditorium-meta {
        display: flex;
        flex-direction: column;
        gap: 6px;
        margin-bottom: 14px;
    }

    .auditorium-meta span {
        display: flex;
        align-items: flex-start;
        gap: 7px;
        font-size: 11px;
        color: #3c5446;
        font-weight: 500;
        line-height: 1.4;
    }

    .auditorium-meta svg {
        width: 14px;
        height: 14px;
        color: #237c4d;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .aud-inactive-alert {
        margin-bottom: 12px;
        padding: 8px 10px;
        border-radius: 9px;
        background: rgba(254, 242, 242, 0.92);
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 11px;
        line-height: 1.45;
    }

    .aud-inactive-alert strong {
        color: #7f1d1d;
    }

    .auditorium-actions {
        display: grid;
        grid-template-columns: 1fr;
        margin-top: auto;
    }

    .auditorium-actions a,
    .auditorium-actions .btn-booking-disabled {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-align: center;
        text-decoration: none;
        padding: 10px 12px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        transition: .2s;
        position: relative;
        z-index: 2;
    }

    .auditorium-actions a svg,
    .auditorium-actions .btn-booking-disabled svg {
        width: 15px;
        height: 15px;
    }

    .btn-booking {
        color: white;
        background: #237c4d;
        border: 1px solid #237c4d;
    }

    .btn-booking:hover {
        background: #1a633c;
        color: white;
    }

    .btn-booking-disabled {
        color: #991b1b;
        background: rgba(254, 226, 226, 0.85);
        border: 1px solid #fca5a5;
        cursor: not-allowed;
    }

    .empty-auditorium {
        background: rgba(255, 255, 255, 0.42);
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        color: #4c6456;
        font-size: 13px;
        grid-column: 1 / -1;
    }

    /* SLIDE-OVER DETAIL DRAWER (DISAMAKAN DENGAN DRAWER ADMIN: ROUNDED FLOATING) */
    .auditorium-modal {
        position: fixed;
        inset: 0;
        z-index: 9995;
        display: flex;
        align-items: stretch;
        justify-content: flex-end;
        padding: 18px;
        background: rgba(12, 28, 19, 0.36);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.32s ease, visibility 0.32s ease;
    }

    .auditorium-modal.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .auditorium-modal-box {
        position: relative;
        width: min(490px, calc(100vw - 36px));
        max-width: 490px;
        height: 100%;
        max-height: calc(100dvh - 36px);
        margin-left: auto;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        background: #ffffff;
        border: 1px solid #e2ebe5;
        border-radius: 20px;
        box-shadow: -12px 10px 42px rgba(22, 54, 38, 0.16);
        transform: translate3d(calc(100% + 28px), 0, 0);
        transition: transform 0.38s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .auditorium-modal.show .auditorium-modal-box {
        transform: translate3d(0, 0, 0);
    }

    .modal-close {
        position: sticky;
        float: right;
        top: 14px;
        right: 14px;
        margin-right: 14px;
        margin-bottom: -42px;
        z-index: 4;
        width: 34px;
        height: 34px;
        border: 1px solid #dce5df;
        border-radius: 10px;
        background: rgba(255, 255, 255, .95);
        color: #163626;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .12);
        transition: background .18s ease;
    }

    .modal-close:hover {
        background: #e9f5ed;
    }

    .modal-close svg {
        width: 17px;
        height: 17px;
    }

    .modal-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
    }

    .modal-no-image {
        width: 100%;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #eaf5ed, #f4f8f5);
        color: #237c4d;
    }

    .modal-no-image svg {
        width: 48px;
        height: 48px;
    }

    .modal-content {
        padding: 22px 24px 24px;
    }

    .modal-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .modal-content h2 {
        color: #163626;
        font-size: 20px;
        font-weight: 800;
        margin: 0;
        line-height: 1.3;
    }

    .modal-description {
        font-size: 13px;
        color: #4c6456;
        line-height: 1.7;
        margin-bottom: 18px;
        white-space: pre-line;
    }

    .modal-deactivation-box {
        margin-bottom: 18px;
        padding: 11px 14px;
        border-radius: 12px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 12.5px;
        line-height: 1.55;
    }

    .modal-deactivation-box strong {
        display: block;
        color: #7f1d1d;
        margin-bottom: 2px;
    }

    .modal-info-title {
        color: #163626;
        font-size: 13px;
        font-weight: 800;
        margin: 18px 0 10px;
    }

    .modal-info-list {
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .modal-info-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 12.5px;
        color: #4c6456;
        line-height: 1.6;
    }

    .modal-info-item strong {
        color: #163626;
    }

    .modal-info-item svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: #237c4d;
        margin-top: 2px;
    }

    .modal-facilities {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .facility-tag {
        padding: 7px 11px;
        border-radius: 8px;
        background: #eaf5ed;
        color: #237c4d;
        font-size: 11.5px;
        font-weight: 600;
    }

    .modal-actions {
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #ebf0ed;
        display: flex;
        justify-content: flex-end;
    }

    .modal-actions a,
    .modal-actions .modal-btn-disabled {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border-radius: 10px;
        padding: 11px 18px;
        font-size: 12.5px;
        font-weight: 700;
        text-decoration: none;
    }

    .modal-actions a {
        background: #237c4d;
        color: white;
    }

    .modal-actions a:hover {
        background: #1a633c;
        color: white;
    }

    .modal-actions .modal-btn-disabled {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        cursor: not-allowed;
    }

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .auditorium-cards > .auditorium-card { flex-basis: calc((100% - 16px) / 2); }
    }

    @media (max-width: 900px) {
        .dashboard-middle {
            grid-template-columns: 1fr;
        }

        .stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .auditorium-modal {
            padding: 12px;
        }

        .auditorium-modal-box {
            width: min(440px, calc(100vw - 24px));
            max-height: calc(100dvh - 24px);
            border-radius: 18px;
        }
    }

    @media (max-width: 550px) {
        .dashboard-topbar {
            align-items: flex-start;
        }

        .dashboard-welcome h1 {
            font-size: 22px;
        }

        .dashboard-topbar .dashboard-action {
            width: 100%;
        }

        .stats {
            gap: 10px;
        }

        .stat-card {
            padding: 13px;
        }

        .stat-card strong {
            font-size: 22px;
        }

        .auditorium-cards > .auditorium-card { flex-basis: 85%; }
        .auditorium-arrow { width: 32px; height: 32px; }

        .dashboard-panel {
            padding: 12px 15px 15px 15px !important;
        }

        .auditorium-modal {
            padding: 10px;
        }

        .auditorium-modal-box {
            width: 100%;
            max-width: calc(100vw - 20px);
            max-height: calc(100dvh - 20px);
            border-radius: 18px;
        }

        .aud-slider.is-modal-slider,
        .modal-image {
            height: 210px !important;
        }

        .modal-content {
            padding: 18px;
        }
    }
</style>

<div class="dashboard-page">

    {{-- HEADER --}}
    <div class="dashboard-topbar">
        <div class="dashboard-welcome">
            <h1>Selamat datang, {{ $user->name }}!</h1>
            <p>Temukan auditorium impian Anda untuk pengajuan peminjaman Anda.</p>
        </div>

        <a class="dashboard-action" href="{{ route('borrower.booking.create') }}">
            <i data-lucide="plus"></i>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- STATISTIK --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-top">
                <small>Total Pengajuan</small>
                <span class="stat-icon blue">
                    <i data-lucide="calendar-days"></i>
                </span>
            </div>
            <strong>{{ $stats['total'] }}</strong>
            <p>Seluruh pengajuan Anda</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Pending</small>
                <span class="stat-icon amber">
                    <i data-lucide="clock"></i>
                </span>
            </div>
            <strong>{{ $stats['pending'] }}</strong>
            <p>Menunggu keputusan Admin</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Disetujui</small>
                <span class="stat-icon green">
                    <i data-lucide="check-circle-2"></i>
                </span>
            </div>
            <strong>{{ $stats['approved'] }}</strong>
            <p>Pengajuan telah disetujui</p>
        </div>

        <div class="stat-card">
            <div class="stat-top">
                <small>Ditolak</small>
                <span class="stat-icon red">
                    <i data-lucide="x-circle"></i>
                </span>
            </div>
            <strong>{{ $stats['rejected'] }}</strong>
            <p>Lihat detail alasan</p>
        </div>
    </div>

    {{-- PENGAJUAN DAN INFORMASI --}}
    <div class="dashboard-middle">

        {{-- PENGAJUAN TERBARU (DIBATASI 3 DATA AGAR SEJAJAR) --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <h2>Pengajuan Terbaru</h2>
                    <p>Ringkasan pengajuan peminjaman Anda</p>
                </div>

                <a href="{{ route('borrower.bookings') }}">
                    Lihat semua
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div class="dashboard-table-wrap">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Auditorium</th>
                            <th>Kegiatan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($bookings->take(3) as $b)
                            <tr>
                                <td>{{ $b->auditorium->name ?? '-' }}</td>
                                <td>{{ $b->event_name }}</td>
                                <td>
                                    {{ $b->booking_date
                                        ? \Carbon\Carbon::parse($b->booking_date)->format('d M Y')
                                        : '-' }}
                                </td>
                                <td>
                                    <span class="status {{ strtolower($b->status) }}">
                                        {{ $b->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty">
                                    Belum ada pengajuan peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- INFORMASI SIPADU --}}
        <div class="dashboard-panel">
            <div class="section-head">
                <div>
                    <h2>Informasi SIPADU</h2>
                    <p>Panduan singkat peminjaman</p>
                </div>

                <a href="{{ route('borrower.guides') }}">
                    Informasi lebih lanjut
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div class="guide-list">
                <div class="guide-item">
                    <div class="guide-icon">
                        <i data-lucide="building-2"></i>
                    </div>
                    <div>
                        <strong>Pilih Auditorium</strong>
                        <p>Lihat informasi ruangan sebelum mengajukan peminjaman.</p>
                    </div>
                </div>

                <div class="guide-item">
                    <div class="guide-icon">
                        <i data-lucide="file-check"></i>
                    </div>
                    <div>
                        <strong>Lengkapi Pengajuan</strong>
                        <p>Isi informasi kegiatan dan jadwal dengan benar.</p>
                    </div>
                </div>

                <div class="guide-item">
                    <div class="guide-icon">
                        <i data-lucide="list-checks"></i>
                    </div>
                    <div>
                        <strong>Pantau Status</strong>
                        <p>Pantau perkembangan pengajuan di menu Peminjaman Saya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR AUDITORIUM --}}
    <div class="dashboard-panel auditorium-section">
        <div class="section-head">
            <div>
                <h2>Pilihan Auditorium</h2>
                <p>Geser untuk melihat auditorium lainnya</p>
            </div>
            <div class="auditorium-carousel-controls" aria-label="Navigasi pilihan auditorium">
                <button type="button" class="auditorium-arrow" aria-label="Auditorium sebelumnya" onclick="scrollAuditoriums(this, -1)"><i data-lucide="chevron-left"></i></button>
                <button type="button" class="auditorium-arrow" aria-label="Auditorium berikutnya" onclick="scrollAuditoriums(this, 1)"><i data-lucide="chevron-right"></i></button>
            </div>
        </div>

        <div class="auditorium-carousel-wrap">
        <div class="auditorium-cards" id="auditoriumCards">
            @forelse($auditoriums as $a)

                @php
                    $isActive = (bool) ($a->is_active ?? true);
                    $deactivationReason = trim((string) ($a->deactivation_reason ?? $a->keterangan_nonaktif ?? ''));

                    $description = is_string($a->description) && trim($a->description) !== ''
                        ? $a->description
                        : 'Informasi auditorium belum tersedia.';

                    $lokasiKampus = is_string($a->lokasi_kampus) && trim($a->lokasi_kampus) !== ''
                        ? trim($a->lokasi_kampus)
                        : (is_string($a->location) && trim($a->location) !== '' ? trim($a->location) : 'Lokasi kampus belum tersedia');

                    $lokasiGedung = is_string($a->lokasi_gedung) && trim($a->lokasi_gedung) !== ''
                        ? trim($a->lokasi_gedung)
                        : null;

                    $jamMulaiFmt = $a->jam_mulai
                        ? \Carbon\Carbon::parse($a->jam_mulai)->format('H:i')
                        : '07:00';

                    $jamSelesaiFmt = $a->jam_selesai
                        ? \Carbon\Carbon::parse($a->jam_selesai)->format('H:i')
                        : '17:00';

                    $jamOperasional = $jamMulaiFmt . ' - ' . $jamSelesaiFmt;

                    $capacity = is_scalar($a->capacity) && trim((string) $a->capacity) !== ''
                        ? (is_numeric($a->capacity) ? $a->capacity . ' orang' : $a->capacity)
                        : 'Belum tersedia';

                    $rawFacilities = $a->facilities;
                    if (is_string($rawFacilities)) {
                        $decodedFacilities = json_decode($rawFacilities, true);
                        $rawFacilities = is_array($decodedFacilities)
                            ? $decodedFacilities
                            : array_filter(array_map('trim', explode(',', $rawFacilities)));
                    }

                    $facilities = is_array($rawFacilities)
                        ? $rawFacilities
                        : [];

                    $facilities = array_filter($facilities, function ($item) {
                        return is_scalar($item) && trim((string) $item) !== '';
                    });

                    // Ambil seluruh foto auditorium (images array + fallback image_path lama)
                    $rawImages = $a->images;
                    if (is_string($rawImages)) {
                        $decodedImages = json_decode($rawImages, true);
                        $rawImages = is_array($decodedImages) ? $decodedImages : [];
                    }
                    $photoPaths = array_values(array_filter(
                        is_array($rawImages) ? $rawImages : [],
                        fn ($p) => is_string($p) && trim($p) !== ''
                    ));
                    if (empty($photoPaths) && is_string($a->image_path) && trim($a->image_path) !== '') {
                        $photoPaths[] = trim($a->image_path);
                    }

                    $photoItems = array_map(fn ($p) => [
                        'path' => $p,
                        'url'  => asset('storage/' . $p),
                    ], $photoPaths);

                    $shortDescription = \Illuminate\Support\Str::limit(
                        $description,
                        90
                    );
                @endphp

                {{-- KARTU AUDITORIUM --}}
                <article
                    class="auditorium-card {{ !$isActive ? 'is-inactive-card' : '' }}"
                    onclick="openAuditoriumModal('auditorium-modal-{{ $a->id }}')"
                    role="button"
                    tabindex="0"
                    aria-label="Lihat detail {{ $a->name }}"
                    onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openAuditoriumModal('auditorium-modal-{{ $a->id }}'); }"
                >

                    <div class="auditorium-image-wrap">
                        @if(count($photoItems) > 0)
                            <div class="aud-slider" data-aud-slider data-index="0" data-total="{{ count($photoItems) }}">
                                <div class="aud-slider-track">
                                    @foreach($photoItems as $idx => $item)
                                        <div class="aud-slide">
                                            <img
                                                src="{{ $item['url'] }}"
                                                alt="Foto {{ $a->name }} ({{ $idx + 1 }})"
                                                loading="lazy"
                                            >
                                        </div>
                                    @endforeach
                                </div>

                                @if(count($photoItems) > 1)
                                    <span class="aud-slider-count">1 / {{ count($photoItems) }}</span>

                                    <button type="button" class="aud-slider-btn prev" aria-label="Foto sebelumnya" onclick="event.stopPropagation(); stepAudSlider(this, -1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <button type="button" class="aud-slider-btn next" aria-label="Foto berikutnya" onclick="event.stopPropagation(); stepAudSlider(this, 1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>

                                    <div class="aud-slider-dots" onclick="event.stopPropagation()">
                                        @foreach($photoItems as $idx => $item)
                                            <button
                                                type="button"
                                                class="aud-slider-dot {{ $idx === 0 ? 'is-active' : '' }}"
                                                aria-label="Ke foto {{ $idx + 1 }}"
                                                data-slide-index="{{ $idx }}"
                                                onclick="event.stopPropagation(); goToAudSlide(this)"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="auditorium-no-image">
                                <i data-lucide="building-2"></i>
                            </div>
                        @endif
                    </div>

                    <div class="auditorium-card-content">
                        <div class="auditorium-card-head">
                            <h3>{{ $a->name }}</h3>
                            @if($isActive)
                                <span class="aud-status-pill is-active">Aktif</span>
                            @else
                                <span class="aud-status-pill is-inactive">Nonaktif</span>
                            @endif
                        </div>

                        <p class="auditorium-description">
                            {{ $shortDescription }}
                        </p>

                        <div class="auditorium-meta">
                            <span>
                                <i data-lucide="map-pin"></i>
                                {{ $lokasiKampus }}
                            </span>

                            @if($lokasiGedung)
                                <span>
                                    <i data-lucide="building"></i>
                                    {{ \Illuminate\Support\Str::limit($lokasiGedung, 55) }}
                                </span>
                            @endif

                            <span>
                                <i data-lucide="clock"></i>
                                Jam Operasional: {{ $jamOperasional }}
                            </span>

                            <span>
                                <i data-lucide="users"></i>
                                Kapasitas: {{ $capacity }}
                            </span>
                        </div>

                        @if(!$isActive && $deactivationReason !== '')
                            <div class="aud-inactive-alert">
                                <strong>Keterangan Nonaktif:</strong> {{ \Illuminate\Support\Str::limit($deactivationReason, 85) }}
                            </div>
                        @endif

                        <div class="auditorium-actions">
                            @if($isActive)
                                <a
                                    href="{{ route('borrower.booking.create', ['auditorium' => $a->id]) }}"
                                    class="btn-booking"
                                    onclick="event.stopPropagation()"
                                >
                                    <i data-lucide="calendar-plus"></i>
                                    Ajukan Peminjaman
                                </a>
                            @else
                                <span class="btn-booking-disabled" onclick="event.stopPropagation(); openAuditoriumModal('auditorium-modal-{{ $a->id }}')">
                                    <i data-lucide="ban"></i>
                                    Sedang Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>
                </article>

                {{-- MODAL / DRAWER DETAIL LENGKAP --}}
                <div
                    class="auditorium-modal"
                    id="auditorium-modal-{{ $a->id }}"
                    onclick="if(event.target === this) closeAuditoriumModal(this.id)"
                >
                    <div class="auditorium-modal-box">

                        <button
                            type="button"
                            class="modal-close"
                            onclick="closeAuditoriumModal('auditorium-modal-{{ $a->id }}')"
                            aria-label="Tutup detail"
                        >
                            <i data-lucide="x"></i>
                        </button>

                        @if(count($photoItems) > 0)
                            <div class="aud-slider is-modal-slider" data-aud-slider data-index="0" data-total="{{ count($photoItems) }}">
                                <div class="aud-slider-track">
                                    @foreach($photoItems as $idx => $item)
                                        <div class="aud-slide">
                                            <img
                                                src="{{ $item['url'] }}"
                                                alt="Foto {{ $a->name }} ({{ $idx + 1 }})"
                                            >
                                        </div>
                                    @endforeach
                                </div>

                                @if(count($photoItems) > 1)
                                    <span class="aud-slider-count">1 / {{ count($photoItems) }}</span>

                                    <button type="button" class="aud-slider-btn prev" aria-label="Foto sebelumnya" onclick="stepAudSlider(this, -1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                    </button>
                                    <button type="button" class="aud-slider-btn next" aria-label="Foto berikutnya" onclick="stepAudSlider(this, 1)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                    </button>

                                    <div class="aud-slider-dots">
                                        @foreach($photoItems as $idx => $item)
                                            <button
                                                type="button"
                                                class="aud-slider-dot {{ $idx === 0 ? 'is-active' : '' }}"
                                                aria-label="Ke foto {{ $idx + 1 }}"
                                                data-slide-index="{{ $idx }}"
                                                onclick="goToAudSlide(this)"
                                            ></button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="modal-no-image">
                                <i data-lucide="building-2"></i>
                            </div>
                        @endif

                        <div class="modal-content">
                            <div class="modal-title-row">
                                <h2>{{ $a->name }}</h2>
                                @if($isActive)
                                    <span class="aud-status-pill is-active">Aktif</span>
                                @else
                                    <span class="aud-status-pill is-inactive">Nonaktif</span>
                                @endif
                            </div>

                            @if(!$isActive)
                                <div class="modal-deactivation-box">
                                    <strong>Auditorium Sedang Nonaktif</strong>
                                    {{ $deactivationReason !== '' ? $deactivationReason : 'Auditorium saat ini tidak tersedia untuk peminjaman baru.' }}
                                </div>
                            @endif

                            <div class="modal-description">
                                {{ $description }}
                            </div>

                            <h3 class="modal-info-title">
                                Informasi Auditorium
                            </h3>

                            <div class="modal-info-list">
                                <div class="modal-info-item">
                                    <i data-lucide="map-pin"></i>
                                    <div>
                                        <strong>Lokasi Kampus</strong><br>
                                        {{ $lokasiKampus }}
                                    </div>
                                </div>

                                <div class="modal-info-item">
                                    <i data-lucide="building"></i>
                                    <div>
                                        <strong>Lokasi Gedung</strong><br>
                                        {{ $lokasiGedung ?: 'Informasi lokasi gedung belum tersedia' }}
                                    </div>
                                </div>

                                <div class="modal-info-item">
                                    <i data-lucide="clock"></i>
                                    <div>
                                        <strong>Jam Operasional</strong><br>
                                        {{ $jamOperasional }}
                                    </div>
                                </div>

                                <div class="modal-info-item">
                                    <i data-lucide="users"></i>
                                    <div>
                                        <strong>Kapasitas</strong><br>
                                        {{ $capacity }}
                                    </div>
                                </div>
                            </div>

                            <h3 class="modal-info-title">
                                Fasilitas Auditorium
                            </h3>

                            @if(count($facilities) > 0)
                                <div class="modal-facilities">
                                    @foreach($facilities as $facility)
                                        <span class="facility-tag">
                                            {{ $facility }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="auditorium-description">
                                    Informasi fasilitas belum tersedia.
                                </p>
                            @endif

                            <div class="modal-actions">
                                @if($isActive)
                                    <a
                                        href="{{ route('borrower.booking.create', ['auditorium' => $a->id]) }}"
                                    >
                                        <i data-lucide="calendar-plus"></i>
                                        Ajukan Peminjaman
                                    </a>
                                @else
                                    <span class="modal-btn-disabled">
                                        <i data-lucide="ban"></i>
                                        Tidak Dapat Dipinjam (Nonaktif)
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="empty-auditorium">
                    Data auditorium belum tersedia.
                </div>
            @endforelse
        </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT DETAIL & SLIDER AUDITORIUM --}}
<script>
    const AUD_SLIDE_INTERVAL = 4000;

    function scrollAuditoriums(button, direction) {
        const track = document.getElementById('auditoriumCards');
        if (!track) return;
        const card = track.querySelector('.auditorium-card');
        const distance = card ? card.getBoundingClientRect().width + 16 : track.clientWidth * 0.8;
        track.scrollBy({ left: distance * direction, behavior: 'smooth' });
    }

    function setAudSliderIndex(slider, nextIndex) {
        const total = parseInt(slider.dataset.total || '1', 10);
        if (total <= 1) return;

        const normalized = ((nextIndex % total) + total) % total;
        slider.dataset.index = String(normalized);

        const track = slider.querySelector('.aud-slider-track');
        if (track) {
            track.style.transform = 'translateX(-' + (normalized * 100) + '%)';
        }

        const counter = slider.querySelector('.aud-slider-count');
        if (counter) {
            counter.textContent = (normalized + 1) + ' / ' + total;
        }

        slider.querySelectorAll('.aud-slider-dot').forEach((dot, idx) => {
            dot.classList.toggle('is-active', idx === normalized);
        });
    }

    function startAudSliderAutoplay(slider) {
        const total = parseInt(slider.dataset.total || '1', 10);
        if (total <= 1) return;

        stopAudSliderAutoplay(slider);
        slider._autoTimer = setInterval(() => {
            if (document.visibilityState === 'visible') {
                const current = parseInt(slider.dataset.index || '0', 10);
                setAudSliderIndex(slider, current + 1);
            }
        }, AUD_SLIDE_INTERVAL);
    }

    function stopAudSliderAutoplay(slider) {
        if (slider._autoTimer) {
            clearInterval(slider._autoTimer);
            slider._autoTimer = null;
        }
    }

    function resetAudSliderAutoplay(slider) {
        stopAudSliderAutoplay(slider);
        if (!slider.matches(':hover')) {
            startAudSliderAutoplay(slider);
        }
    }

    function stepAudSlider(btn, delta) {
        const slider = btn.closest('[data-aud-slider]');
        if (!slider) return;
        const current = parseInt(slider.dataset.index || '0', 10);
        setAudSliderIndex(slider, current + delta);
        resetAudSliderAutoplay(slider);
    }

    function goToAudSlide(dotBtn) {
        const slider = dotBtn.closest('[data-aud-slider]');
        if (!slider) return;
        const index = parseInt(dotBtn.dataset.slideIndex || '0', 10);
        setAudSliderIndex(slider, index);
        resetAudSliderAutoplay(slider);
    }

    function openAuditoriumModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.classList.add('show');

        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function closeAuditoriumModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // Pindahkan seluruh modal ke root <body> dan kunci scroll chaining agar backdrop blur menutupi layar tanpa menggeser layout
        document.querySelectorAll('.auditorium-modal').forEach(modal => {
            document.body.appendChild(modal);

            modal.addEventListener('wheel', function (e) {
                const box = modal.querySelector('.auditorium-modal-box');
                if (!box || !box.contains(e.target)) {
                    e.preventDefault();
                    return;
                }
                const atTop = box.scrollTop <= 0 && e.deltaY < 0;
                const atBottom = Math.ceil(box.scrollTop + box.clientHeight) >= box.scrollHeight && e.deltaY > 0;
                if (atTop || atBottom) {
                    e.preventDefault();
                }
            }, { passive: false });
        });

        // Inisialisasi slideshow otomatis + kontrol swipe pada kartu dan drawer detail
        document.querySelectorAll('[data-aud-slider]').forEach(slider => {
            let startX = 0;

            slider.addEventListener('mouseenter', () => stopAudSliderAutoplay(slider));
            slider.addEventListener('mouseleave', () => startAudSliderAutoplay(slider));

            slider.addEventListener('touchstart', e => {
                startX = e.touches[0].clientX;
                stopAudSliderAutoplay(slider);
            }, { passive: true });

            slider.addEventListener('touchend', e => {
                const diff = e.changedTouches[0].clientX - startX;
                if (Math.abs(diff) > 35) {
                    e.stopPropagation();
                    const current = parseInt(slider.dataset.index || '0', 10);
                    setAudSliderIndex(slider, current + (diff < 0 ? 1 : -1));
                }
                startAudSliderAutoplay(slider);
            }, { passive: true });

            startAudSliderAutoplay(slider);
        });
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.auditorium-modal.show')
                .forEach(function(modal) {
                    modal.classList.remove('show');
                });
        }
    });
</script>

@endsection