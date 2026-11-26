@extends('layouts.panel')

@section('panel')

<style>
    .auditorium-page {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        padding: 10px 10px 35px;
        color: #163626;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .auditorium-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 15px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    .auditorium-header h1 {
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -.03em;
        color: #163626;
        margin: 0 0 5px;
    }

    .auditorium-header p {
        font-size: 13px;
        color: #4c6456;
        margin: 0;
    }

    /* TOMBOL CLEAN & KONSISTEN */
    .aud-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        box-shadow: none !important;
        transition: background .2s ease, transform .15s ease, border-color .2s ease;
    }

    .aud-btn:hover {
        transform: translateY(-1px);
    }

    .aud-btn-primary {
        background: #237c4d !important;
        color: #fff !important;
        border-color: #237c4d !important;
        padding: 10px 18px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .aud-btn-primary:hover {
        background: #1b663f !important;
    }

    .aud-btn-secondary {
        background: #f4f7f5 !important;
        color: #163626 !important;
        border-color: #d6e2db !important;
        padding: 10px 16px;
        font-size: 12.5px;
        border-radius: 11px;
    }

    .aud-btn-secondary:hover {
        background: #e8efe9 !important;
    }

    .aud-btn-edit {
        background: #fef3c7 !important;
        color: #92400e !important;
        border-color: #fde68a !important;
    }

    .aud-btn-edit:hover {
        background: #fde68a !important;
    }

    .aud-btn-delete {
        background: #fee2e2 !important;
        color: #b91c1c !important;
        border-color: #fecaca !important;
    }

    .aud-btn-delete:hover {
        background: #fecaca !important;
    }

    /* DAFTAR AUDITORIUM TETAP FULL WIDTH (2 KARTU PER BARIS) */
    .aud-list-col {
        width: 100% !important;
        min-width: 0 !important;
    }

    .auditorium-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 20px !important;
        align-items: stretch !important;
        width: 100% !important;
    }

    /* KONTAINER KARTU PUTIH SOLID (NON-GLASS) */
    .auditorium-card {
        background: #ffffff;
        border: 1px solid #e2ebe5;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(22, 54, 38, .05);
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        box-sizing: border-box !important;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .auditorium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(22, 54, 38, .08);
    }

    .auditorium-card.is-active {
        border: 2px solid #237c4d !important;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .14) !important;
    }

    /* BAGIAN FOTO SEPERTI DASHBOARD MAHASISWA + SLIDER */
    .auditorium-image-wrap {
        width: 100% !important;
        height: 175px !important;
        overflow: hidden !important;
        position: relative;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        border-bottom: 1px solid #e2ebe5;
        background: #eaf4ee;
    }

    .aud-slider {
        width: 100%;
        height: 175px;
        position: relative;
        overflow: hidden;
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
        height: 175px;
    }

    .aud-slide img {
        width: 100% !important;
        height: 175px !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 0 !important;
        margin: 0 !important;
        transition: transform .2s ease;
    }

    .auditorium-card:hover .aud-slide img {
        transform: scale(1.04);
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

    /* TOMBOL & INDIKATOR SLIDER FOTO */
    .aud-slider-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 3;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
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

    /* KONTEN KARTU PUTIH */
    .auditorium-content {
        padding: 18px 20px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .auditorium-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .auditorium-content h3 {
        margin: 0;
        font-size: 16.5px;
        line-height: 1.35;
        font-weight: 800;
        color: #163626;
    }

    .auditorium-info {
        color: #4c6456;
        font-size: 12.8px;
        line-height: 1.65;
        margin-bottom: 16px;
    }

    .auditorium-info div {
        margin-bottom: 3px;
    }

    .auditorium-info strong {
        color: #163626;
        font-weight: 700;
    }

    .aud-deactivation-note {
        margin-top: 8px !important;
        padding: 8px 11px;
        border-radius: 10px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        font-size: 12px;
        line-height: 1.5;
    }

    .aud-deactivation-note strong {
        color: #7f1d1d;
    }

    /* FOOTER KARTU CLEAN: TOGGLE DI KIRI, TOMBOL EDIT & HAPUS DI KANAN */
    .auditorium-actions {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 14px;
        border-top: 1px solid #ebf0ed;
    }

    .aud-action-buttons {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .aud-action-buttons form {
        margin: 0;
    }

    .status-active {
        color: #065f46;
        background: #bbf7d0;
        border: 1px solid #34d399;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-inactive {
        color: #991b1b;
        background: #fee2e2;
        border: 1px solid #fca5a5;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    /* BACKDROP & SLIDE-OVER DRAWER DARI KANAN KE KIRI (UJUNG TETAP ROUNDED) */
    .aud-drawer-backdrop {
        position: fixed;
        inset: 0;
        z-index: 9990;
        background: rgba(12, 28, 19, 0.36);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        touch-action: none;
        overscroll-behavior: none;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.32s ease, visibility 0.32s ease;
    }

    .aud-drawer-backdrop.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .aud-form-drawer {
        position: fixed;
        top: 18px;
        right: 18px;
        bottom: 18px;
        z-index: 9995;
        width: min(490px, calc(100vw - 36px));
        max-height: calc(100dvh - 36px);
        background: #ffffff;
        border: 1px solid #e2ebe5;
        border-radius: 20px;
        box-shadow: -12px 10px 42px rgba(22, 54, 38, 0.16);
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        overscroll-behavior: contain;
        transform: translate3d(calc(100% + 28px), 0, 0);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition:
            transform 0.38s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.28s ease,
            visibility 0s linear 0.38s;
    }

    .aud-form-drawer.is-open {
        transform: translate3d(0, 0, 0);
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition:
            transform 0.38s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.28s ease,
            visibility 0s linear 0s;
    }

    .auditorium-form {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 0;
        width: 100%;
        margin: 0;
        background: #ffffff;
        box-sizing: border-box;
    }

    .aud-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 20px 24px 14px;
        border-bottom: 1px solid #ebf0ed;
        background: #ffffff;
        flex-shrink: 0;
        touch-action: none;
    }

    .aud-form-header h3 {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        color: #163626;
    }

    .aud-close-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: 1px solid #dce5df;
        background: #f6f9f7;
        color: #163626;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background .18s ease;
    }

    .aud-close-btn:hover {
        background: #e9f0ec;
    }

    .aud-form-body {
        padding: 14px 24px 20px;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        flex: 1;
        min-height: 0;
    }

    .aud-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .aud-sub-label {
        display: block;
        margin: 0 0 5px !important;
        font-size: 11.5px !important;
        font-weight: 600 !important;
        color: #5b7265 !important;
    }

    .auditorium-form label {
        display: block;
        margin: 12px 0 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: #294735;
    }

    .aud-req-star {
        color: #dc2626;
        font-weight: 800;
    }

    .auditorium-form input:not([type="checkbox"]):not([type="file"]),
    .auditorium-form textarea {
        width: 100% !important;
        padding: 10px 13px;
        border: 1px solid #d5e1da;
        border-radius: 10px;
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        color: #163626;
        box-sizing: border-box !important;
        outline: none;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .auditorium-form input:focus,
    .auditorium-form textarea:focus {
        background: #ffffff;
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .auditorium-form textarea {
        min-height: 85px;
        resize: vertical;
        line-height: 1.6;
    }

    .auditorium-form textarea.aud-textarea-sm {
        min-height: 64px;
    }

    /* CUSTOM GLASSMORPHISM DROPDOWN (DISAMAKAN DENGAN HALAMAN PROFILE) */
    .glass-select {
        position: relative;
        width: 100%;
    }

    .glass-select-trigger {
        width: 100%;
        height: 44px;
        box-sizing: border-box;
        padding: 0 38px 0 13px;
        border-radius: 8px;
        border: 1px solid #dce5e0;
        background: #ffffff;
        color: #172b20;
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
        font-size: 13px;
        font-weight: 400;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
    }

    .glass-select-trigger.is-placeholder {
        color: #8c9991;
    }

    .glass-select:not(.is-locked) .glass-select-trigger:hover {
        border-color: #2f7d58;
        background: #ffffff;
    }

    .glass-select.is-open .glass-select-trigger,
    .glass-select:not(.is-locked) .glass-select-trigger:focus-visible {
        background: #ffffff;
        border-color: #2f7d58;
        box-shadow: 0 0 0 3px rgba(47, 125, 88, .14);
    }

    .glass-select-value {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .glass-select-chevron {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: #68736c;
        pointer-events: none;
        transition: transform .2s ease, color .2s ease;
    }

    .glass-select.is-open .glass-select-chevron {
        transform: translateY(-50%) rotate(180deg);
        color: #2f7d58;
    }

    .glass-select-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 80;
        max-height: 236px;
        overflow-y: auto;
        padding: 6px;
        border-radius: 12px;
        background: linear-gradient(155deg, rgba(255, 255, 255, .90) 0%, rgba(244, 249, 246, .84) 100%);
        backdrop-filter: blur(22px) saturate(160%);
        -webkit-backdrop-filter: blur(22px) saturate(160%);
        border: 1px solid rgba(255, 255, 255, .85);
        box-shadow: 0 12px 28px rgba(15, 23, 42, .10), inset 0 1px 0 rgba(255, 255, 255, .9);
        box-sizing: border-box;
        scrollbar-width: thin;
        scrollbar-color: #2f7d58 rgba(47, 125, 88, .10);
    }

    .glass-select-menu[hidden] {
        display: none !important;
    }

    .glass-select-menu::-webkit-scrollbar {
        width: 7px;
    }

    .glass-select-menu::-webkit-scrollbar-track {
        background: rgba(47, 125, 88, .10);
        border-radius: 999px;
    }

    .glass-select-menu::-webkit-scrollbar-thumb {
        background: #2f7d58;
        border-radius: 999px;
    }

    .glass-select-menu::-webkit-scrollbar-thumb:hover {
        background: #1f7a49;
    }

    .glass-select-option {
        width: 100%;
        box-sizing: border-box;
        padding: 9px 11px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #172b20;
        font-family: inherit;
        font-size: 12.5px;
        font-weight: 500;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .glass-select-option:hover,
    .glass-select-option:focus-visible {
        background: #eaf4ef;
        color: #2f7d58;
        outline: none;
    }

    .glass-select-option.is-selected {
        background: rgba(47, 125, 88, .14);
        color: #2f7d58;
        font-weight: 700;
    }

    .glass-select-option svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        color: #2f7d58;
    }

    /* HEADER LABEL FOTO & COUNTER */
    .aud-photo-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin: 14px 0 7px;
    }

    .aud-photo-label-row label {
        margin: 0 !important;
    }

    .aud-staged-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        background: #e3f4ea;
        color: #1b663f;
        font-size: 11px;
        font-weight: 700;
    }

    /* AREA UPLOAD FOTO (BORDER PUTUS-PUTUS, LAYOUT TEKS RAPI) */
    .aud-upload-zone {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 22px 20px;
        border: 1.5px dashed #3e9b6b !important;
        border-radius: 16px;
        background: #f2f8f4;
        color: #163626;
        cursor: pointer;
        user-select: none;
        box-sizing: border-box;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease, transform .15s ease, padding .2s ease;
    }

    .aud-upload-zone:hover,
    .aud-upload-zone:focus-visible {
        background: #e7f3ec;
        border-color: #237c4d !important;
        box-shadow: 0 4px 16px rgba(35, 124, 77, 0.08);
        outline: none;
    }

    .aud-upload-zone.is-dragover {
        background: #dff1e7;
        border-color: #1b663f !important;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, 0.18);
        transform: scale(0.995);
    }

    .aud-upload-icon {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: #dcefe4;
        color: #237c4d;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 11px;
        flex-shrink: 0;
        transition: transform .2s ease, background .2s ease;
    }

    .aud-upload-zone:hover .aud-upload-icon {
        background: #cde7d8;
        transform: translateY(-2px);
    }

    .aud-upload-text-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }

    .aud-upload-title {
        font-size: 13px;
        font-weight: 700;
        color: #163626;
        line-height: 1.4;
    }

    .aud-upload-title span {
        color: #237c4d;
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    .aud-upload-sub {
        margin-top: 5px;
        font-size: 11.5px;
        font-weight: 500;
        color: #566e60;
        line-height: 1.5;
    }

    /* Mode ringkas otomatis ketika sudah ada foto terpilih agar mudah menambah foto lagi */
    .aud-upload-zone.is-compact {
        flex-direction: row;
        justify-content: flex-start;
        text-align: left;
        padding: 12px 15px;
        gap: 12px;
        border-radius: 14px;
    }

    .aud-upload-zone.is-compact .aud-upload-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        margin-bottom: 0;
    }

    .aud-upload-zone.is-compact .aud-upload-text-wrap {
        align-items: flex-start;
    }

    .aud-upload-zone.is-compact .aud-upload-sub {
        display: none;
    }

    .aud-photo-warning {
        display: none;
        margin-top: 8px;
        padding: 8px 11px;
        border-radius: 10px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 11.5px;
        line-height: 1.45;
    }

    /* GRID THUMBNAIL FOTO DI PANEL FORM */
    .aud-photo-thumbs {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 9px;
        margin-top: 10px;
    }

    .aud-photo-thumb {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #d8e1db;
        height: 82px;
        background: #eef4f0;
        box-shadow: 0 2px 8px rgba(22, 54, 38, 0.08);
    }

    .aud-photo-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: opacity .2s ease, filter .2s ease, transform .25s ease;
    }

    .aud-photo-thumb:hover img {
        transform: scale(1.04);
    }

    .aud-photo-thumb.is-removed img {
        opacity: 0.3;
        filter: grayscale(100%);
    }

    .aud-photo-thumb-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 22px;
        height: 22px;
        border: none;
        border-radius: 50%;
        background: rgba(15, 32, 23, 0.72);
        backdrop-filter: blur(4px);
        color: #ffffff;
        font-size: 10.5px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background .18s ease, transform .15s ease;
    }

    .aud-photo-thumb-remove:hover {
        background: #dc2626;
        transform: scale(1.08);
    }

    .aud-photo-thumb.is-removed .aud-photo-thumb-remove {
        background: #237c4d;
    }

    .aud-photo-thumb-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        right: 5px;
        text-align: center;
        font-size: 9.5px;
        font-weight: 700;
        padding: 2px 5px;
        border-radius: 6px;
        background: rgba(185, 28, 28, 0.9);
        color: #fff;
        display: none;
    }

    .aud-photo-thumb.is-removed .aud-photo-thumb-badge {
        display: block;
    }

    .aud-photo-thumb-new-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        font-size: 9.5px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 6px;
        background: rgba(35, 124, 77, 0.92);
        color: #ffffff;
    }

    .aud-error-box {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 10px;
        padding: 10px 13px;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .aud-error-box ul {
        margin: 0;
        padding-left: 16px;
    }

    .aud-form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 24px 18px;
        border-top: 1px solid #ebf0ed;
        background: #ffffff;
        flex-shrink: 0;
        touch-action: none;
    }

    .auditorium-empty {
        background: #ffffff;
        border: 1px dashed #cbd8cf;
        border-radius: 16px;
        padding: 36px;
        text-align: center;
        color: #4c6456;
        grid-column: 1 / -1;
    }

    /* SWITCH & MODAL KONFIRMASI STATUS (BLUR SAMA SEPERTI DRAWER TAMBAH AUDITORIUM) */
    .auditorium-status-control { display: inline-flex; align-items: center; gap: 9px; color: #294735; font-size: 12.5px; font-weight: 700; }
    .auditorium-switch { position: relative; display: inline-block; width: 42px; height: 24px; cursor: pointer; }
    .auditorium-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
    .auditorium-switch-track { position: absolute; inset: 0; border-radius: 99px; background: #cbd5cf; transition: background .2s ease; }
    .auditorium-switch-track::before { content: ''; position: absolute; width: 18px; height: 18px; left: 3px; top: 3px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.18); transition: transform .2s ease; }
    .auditorium-switch input:checked + .auditorium-switch-track { background: #237c4d; }
    .auditorium-switch input:checked + .auditorium-switch-track::before { transform: translateX(18px); }
    .auditorium-switch input:focus-visible + .auditorium-switch-track { outline: 3px solid rgba(35,124,77,.2); outline-offset: 2px; }

    .aud-status-modal {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(12, 28, 19, 0.36);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        touch-action: none;
        overscroll-behavior: none;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.28s ease, visibility 0.28s ease;
    }

    .aud-status-modal.is-open {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .aud-status-dialog {
        width: min(440px, 100%);
        padding: 24px;
        border-radius: 18px;
        border: 1px solid #e2ebe5;
        background: #ffffff;
        box-shadow: 0 20px 54px rgba(22, 54, 38, .18);
        color: #163626;
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        box-sizing: border-box;
        transform: translateY(8px) scale(0.97);
        opacity: 0;
        transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.24s ease;
    }

    .aud-status-modal.is-open .aud-status-dialog {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .aud-status-dialog h3 { margin: 0 0 8px; font-size: 17px; font-weight: 800; color: #163626; }
    .aud-status-dialog p { margin: 0 0 18px; color: #4c6456; font-size: 13px; line-height: 1.55; }

    .aud-status-reason-box {
        margin-bottom: 18px;
    }

    .aud-status-reason-box[hidden] {
        display: none !important;
    }

    .aud-status-reason-box label {
        display: block;
        margin: 0 0 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: #294735;
    }

    .aud-status-reason-box textarea {
        width: 100%;
        min-height: 92px;
        padding: 10px 13px;
        border: 1px solid #d5e1da;
        border-radius: 11px;
        background: #ffffff;
        font-family: inherit;
        font-size: 13px;
        color: #163626;
        line-height: 1.55;
        resize: vertical;
        box-sizing: border-box;
        outline: none;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .aud-status-reason-box textarea:focus {
        border-color: #237c4d;
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .12);
    }

    .aud-status-reason-box textarea.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
    }

    .aud-status-reason-err {
        margin-top: 6px;
        font-size: 11.5px;
        font-weight: 600;
        color: #dc2626;
    }

    .aud-status-reason-err[hidden] {
        display: none !important;
    }

    .aud-status-modal-actions { display: flex; justify-content: flex-end; gap: 9px; }
    .aud-status-cancel { background: #edf1ee; color: #294735; border: 1px solid #dce5de; padding: 9px 15px; border-radius: 10px; }
    .aud-status-confirm { background: #237c4d; color: #fff; border: 1px solid #237c4d; padding: 9px 16px; border-radius: 10px; }
    .aud-status-confirm.is-deactivate { background: #dc2626; border-color: #dc2626; }

    /* RESPONSIF TABLET */
    @media (max-width: 900px) {
        .auditorium-grid {
            grid-template-columns: 1fr !important;
        }

        .aud-form-drawer {
            top: 12px;
            right: 12px;
            bottom: 12px;
            width: min(440px, calc(100vw - 24px));
            max-height: calc(100dvh - 24px);
            border-radius: 18px;
        }

        .aud-form-row {
            grid-template-columns: 1fr;
        }
    }

    /* RESPONSIF PONSEL */
    @media (max-width: 540px) {
        .aud-form-drawer {
            top: 10px;
            right: 10px;
            left: 10px;
            bottom: 10px;
            width: auto;
            max-width: calc(100vw - 20px);
            max-height: calc(100dvh - 20px);
            border-radius: 18px;
        }

        .aud-form-header {
            padding: 16px 18px 12px;
        }

        .aud-form-body {
            padding: 12px 18px 18px;
        }

        .aud-form-footer {
            padding: 12px 18px 14px;
        }

        .aud-photo-thumbs {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

@php
    $oldLokasiKampus = (string) old('lokasi_kampus', '');
    $campusLocations = [
        'UPNVJ - Kampus Pondok Labu',
        'UPNVJ - Kampus Limo',
    ];
@endphp

<div class="auditorium-page">
    <div class="auditorium-header">
        <div>
            <h1>Data Auditorium</h1>
            <p>Kelola informasi dan data auditorium yang dapat dipinjam.</p>
        </div>

        <button type="button" class="aud-btn aud-btn-primary" onclick="openAddAuditoriumPanel()">
            + Tambah Auditorium
        </button>
    </div>

    {{-- DAFTAR AUDITORIUM --}}
    <div class="aud-list-col">
        <div class="auditorium-grid">
            @if($auditoriums->count() > 0)
                @foreach($auditoriums as $a)
                    @php
                        $rawFacilities = $a->facilities;
                        if (is_string($rawFacilities)) {
                            $decodedFacilities = json_decode($rawFacilities, true);
                            $rawFacilities = is_array($decodedFacilities)
                                ? $decodedFacilities
                                : array_filter(array_map('trim', explode(',', $rawFacilities)));
                        }
                        $facilitiesList = array_filter(
                            is_array($rawFacilities) ? $rawFacilities : [],
                            fn ($item) => is_scalar($item) && trim((string) $item) !== ''
                        );
                        $facilitiesText = implode(', ', $facilitiesList);

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

                        $jamMulaiFmt = $a->jam_mulai ? \Carbon\Carbon::parse($a->jam_mulai)->format('H:i') : '';
                        $jamSelesaiFmt = $a->jam_selesai ? \Carbon\Carbon::parse($a->jam_selesai)->format('H:i') : '';
                        $jamOperasionalText = ($jamMulaiFmt && $jamSelesaiFmt)
                            ? ($jamMulaiFmt . ' - ' . $jamSelesaiFmt)
                            : 'Belum diisi';

                        $lokasiKampusDisplay = $a->lokasi_kampus ?: ($a->location ?: '');
                        $deactivationReasonText = trim((string) ($a->deactivation_reason ?? $a->keterangan_nonaktif ?? ''));
                    @endphp
                    <div class="auditorium-card" id="auditoriumCard-{{ $a->id }}">
                        <div>
                            {{-- BAGIAN FOTO SEPERTI DASHBOARD MAHASISWA + SLIDESHOW --}}
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
                                    <div class="auditorium-no-image" aria-hidden="true">
                                        <i data-lucide="building-2"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- KONTEN PUTIH --}}
                            <div class="auditorium-content" style="padding-bottom: 0;">
                                <div>
                                    <div class="auditorium-title-row">
                                        <h3>{{ $a->name }}</h3>
                                        @if($a->is_active)
                                            <span class="status-active">Aktif</span>
                                        @else
                                            <span class="status-inactive">Nonaktif</span>
                                        @endif
                                    </div>

                                    <div class="auditorium-info">
                                        <div>
                                            <strong>Lokasi Kampus:</strong>
                                            {{ $lokasiKampusDisplay ?: 'Belum diisi' }}
                                        </div>
                                        <div>
                                            <strong>Lokasi Gedung:</strong>
                                            {{ $a->lokasi_gedung ?: 'Belum diisi' }}
                                        </div>
                                        <div>
                                            <strong>Jam Operasional:</strong>
                                            {{ $jamOperasionalText }}
                                        </div>
                                        <div>
                                            <strong>Kapasitas:</strong>
                                            {{ $a->capacity ? $a->capacity . ' orang' : 'Belum diisi' }}
                                        </div>
                                        <div>
                                            <strong>Fasilitas:</strong>
                                            {{ $facilitiesText ?: 'Belum diisi' }}
                                        </div>
                                        <div>
                                            <strong>Deskripsi:</strong>
                                            {{ $a->description ?: 'Belum ada deskripsi' }}
                                        </div>
                                        @if(!$a->is_active && $deactivationReasonText !== '')
                                            <div class="aud-deactivation-note">
                                                <strong>Keterangan Nonaktif:</strong> {{ $deactivationReasonText }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="auditorium-content" style="padding-top: 0; flex: 0;">
                            <div class="auditorium-actions">
                                {{-- TOGGLE AKTIF / NONAKTIF --}}
                                <div class="auditorium-status-control">
                                    <span>{{ $a->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    <label class="auditorium-switch" aria-label="Ubah status {{ $a->name }}">
                                        <input type="checkbox" data-auditorium-toggle
                                               data-toggle-url="{{ route('admin.auditoriums.toggle', $a->id) }}"
                                               data-auditorium-name="{{ $a->name }}"
                                               data-current-status="{{ $a->is_active ? '1' : '0' }}"
                                               {{ $a->is_active ? 'checked' : '' }}>
                                        <span class="auditorium-switch-track"></span>
                                    </label>
                                </div>

                                {{-- TOMBOL EDIT & HAPUS SEJAJAR CLEAN --}}
                                <div class="aud-action-buttons">
                                    <button
                                        type="button"
                                        class="aud-btn aud-btn-edit"
                                        data-card-id="auditoriumCard-{{ $a->id }}"
                                        data-update-url="{{ route('admin.auditoriums.update', $a->id) }}"
                                        data-name="{{ $a->name }}"
                                        data-lokasi-kampus="{{ $lokasiKampusDisplay }}"
                                        data-lokasi-gedung="{{ $a->lokasi_gedung }}"
                                        data-jam-mulai="{{ $jamMulaiFmt }}"
                                        data-jam-selesai="{{ $jamSelesaiFmt }}"
                                        data-capacity="{{ $a->capacity }}"
                                        data-facilities="{{ $facilitiesText }}"
                                        data-description="{{ $a->description }}"
                                        data-photos='@json($photoItems)'
                                        onclick="openEditAuditoriumPanel(this)"
                                    >
                                        Edit
                                    </button>

                                    <form method="POST"
                                          action="{{ route('admin.auditoriums.delete', $a->id) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus auditorium ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="aud-btn aud-btn-delete">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="auditorium-empty">
                    <h3 style="margin:0 0 6px;color:#163626;">Belum Ada Auditorium</h3>
                    <p style="margin:0;font-size:13px;">Silakan klik tombol Tambah Auditorium untuk menambahkan data.</p>
                </div>
            @endif
        </div>

        <div style="margin-top: 22px;">
            {{ $auditoriums->links() }}
        </div>
    </div>
</div>

{{-- BACKDROP & SLIDE-OVER DRAWER FORM TAMBAH / EDIT AUDITORIUM (KANAN KE KIRI, ROUNDED) --}}
<div class="aud-drawer-backdrop {{ $errors->any() ? 'is-open' : '' }}" id="audDrawerBackdrop" onclick="closeAuditoriumPanel()"></div>

<aside class="aud-form-drawer {{ $errors->any() ? 'is-open' : '' }}" id="audFormPanel" aria-label="Form Auditorium">
    <form class="auditorium-form"
          id="auditoriumForm"
          method="POST"
          action="{{ route('admin.auditoriums.save') }}"
          enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_method" id="audFormMethod" value="POST">
        <div id="audRemovedInputs"></div>

        <div class="aud-form-header">
            <h3 id="audFormTitle">Tambah Auditorium Baru</h3>
            <button type="button" class="aud-close-btn" onclick="closeAuditoriumPanel()" title="Tutup">✕</button>
        </div>

        <div class="aud-form-body">
            @if($errors->any())
                <div class="aud-error-box">
                    <ul>
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <label for="audNameInput" style="margin-top: 2px;">Nama Auditorium <span class="aud-req-star">*</span></label>
            <input type="text" id="audNameInput" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama auditorium">

            <label id="labelAudLokasiKampus">Lokasi Kampus <span class="aud-req-star">*</span></label>
            <div class="glass-select" data-glass-select="lokasi_kampus">
                <input type="hidden" id="audLokasiKampusInput" name="lokasi_kampus" value="{{ $oldLokasiKampus }}" required data-glass-input>
                <button type="button"
                        class="glass-select-trigger {{ $oldLokasiKampus === '' ? 'is-placeholder' : '' }}"
                        id="audLokasiKampusTrigger"
                        data-glass-trigger
                        data-placeholder="Pilih lokasi kampus"
                        aria-haspopup="listbox"
                        aria-expanded="false"
                        aria-labelledby="labelAudLokasiKampus">
                    <span class="glass-select-value" data-glass-label>{{ $oldLokasiKampus !== '' ? $oldLokasiKampus : 'Pilih lokasi kampus' }}</span>
                    <i data-lucide="chevron-down" class="glass-select-chevron"></i>
                </button>
                <div class="glass-select-menu" role="listbox" data-glass-menu hidden>
                    @foreach($campusLocations as $campusOption)
                        <button type="button"
                                class="glass-select-option {{ $oldLokasiKampus === $campusOption ? 'is-selected' : '' }}"
                                role="option"
                                aria-selected="{{ $oldLokasiKampus === $campusOption ? 'true' : 'false' }}"
                                data-value="{{ $campusOption }}">
                            <span>{{ $campusOption }}</span>
                            @if($oldLokasiKampus === $campusOption)
                                <i data-lucide="check"></i>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <label for="audLokasiGedungInput">Lokasi Gedung <span class="aud-req-star">*</span></label>
            <textarea id="audLokasiGedungInput" name="lokasi_gedung" class="aud-textarea-sm" placeholder="Contoh: Gedung Rektorat Lantai 4, Sayap Timur">{{ old('lokasi_gedung') }}</textarea>

            <label>Jam Operasional <span class="aud-req-star">*</span></label>
            <div class="aud-form-row">
                <div>
                    <label for="audJamMulaiInput" class="aud-sub-label">Jam Mulai <span class="aud-req-star">*</span></label>
                    <input type="time" id="audJamMulaiInput" name="jam_mulai" value="{{ old('jam_mulai', '07:00') }}" required>
                </div>
                <div>
                    <label for="audJamSelesaiInput" class="aud-sub-label">Jam Selesai <span class="aud-req-star">*</span></label>
                    <input type="time" id="audJamSelesaiInput" name="jam_selesai" value="{{ old('jam_selesai', '17:00') }}" required>
                </div>
            </div>

            <label for="audCapacityInput">Kapasitas <span class="aud-req-star">*</span></label>
            <input type="number" id="audCapacityInput" name="capacity" min="1" value="{{ old('capacity') }}" placeholder="Contoh: 300">

            <label for="audFacilitiesInput">Fasilitas <span class="aud-req-star">*</span></label>
            <input type="text" id="audFacilitiesInput" name="facilities" value="{{ old('facilities') }}"
                   placeholder="Contoh: AC, Proyektor, Sound System">

            <label for="audDescriptionInput">Deskripsi <span class="aud-req-star">*</span></label>
            <textarea id="audDescriptionInput" name="description" placeholder="Tuliskan deskripsi auditorium...">{{ old('description') }}</textarea>

            {{-- DAFTAR FOTO SAAT INI (MUNCUL SAAT EDIT, BISA HAPUS PER FOTO) --}}
            <div id="audCurrentPhotoWrap" style="display:none; margin-top: 14px;">
                <label style="margin-top:0;">Foto Auditorium Saat Ini</label>
                <div class="aud-photo-thumbs" id="audCurrentPhotoList"></div>
                <p id="audNoPhotoText" style="font-size:12px;color:#64786c;margin:4px 0;display:none;">Belum ada foto.</p>
            </div>

            {{-- AREA UNGGAH FOTO (BORDER PUTUS-PUTUS, BISA TAMBAH BERKALI-KALI TANPA SAVE DULU) --}}
            <div class="aud-photo-label-row">
                <label for="audImageInput" id="audImageLabel">Foto Auditorium <span class="aud-req-star">*</span></label>
                <span class="aud-staged-pill" id="audStagedCountBadge" style="display:none;">0 foto terpilih</span>
            </div>

            <input type="file"
                   id="audImageInput"
                   name="images[]"
                   accept=".jpg,.jpeg,.png,.webp,image/*"
                   multiple
                   style="display:none;"
                   onchange="handleAudFileInputChange(this)">

            <div class="aud-upload-zone"
                 id="audDropzone"
                 role="button"
                 tabindex="0"
                 aria-label="Unggah foto auditorium"
                 onclick="document.getElementById('audImageInput').click()">
                <div class="aud-upload-icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                </div>
                <div class="aud-upload-text-wrap">
                    <div class="aud-upload-title" id="audUploadTitle">Seret &amp; lepas foto di sini, atau <span>pilih file</span></div>
                    <div class="aud-upload-sub" id="audDropzoneSub">Unggah banyak foto sekaligus, maksimal 2 MB per foto (JPG, JPEG, PNG, WEBP)</div>
                </div>
            </div>

            <div class="aud-photo-warning" id="audPhotoWarning"></div>
            <div class="aud-photo-thumbs" id="audNewPhotoPreview" style="display:none;"></div>

            <label id="audActiveCheckboxWrap" style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:16px;">
                <input type="checkbox" id="audActiveCheckbox" name="is_active" value="1" checked style="accent-color:#237c4d;width:16px;height:16px;">
                <span>Aktif <span class="aud-req-star">*</span></span>
            </label>
        </div>

        <div class="aud-form-footer">
            <button type="button" class="aud-btn aud-btn-secondary" onclick="closeAuditoriumPanel()">
                Batal
            </button>
            <button type="submit" class="aud-btn aud-btn-primary" id="audSubmitBtn">
                Simpan Auditorium
            </button>
        </div>
    </form>
</aside>

<div class="aud-status-modal" id="auditoriumStatusModal" aria-hidden="true">
    <div class="aud-status-dialog" role="dialog" aria-modal="true" aria-labelledby="audStatusTitle">
        <h3 id="audStatusTitle">Konfirmasi Perubahan Status</h3>
        <p id="audStatusMessage">Apakah kamu yakin ingin mengubah status auditorium ini?</p>

        <div class="aud-status-reason-box" id="audStatusReasonBox" hidden>
            <label for="audStatusReasonInput">Keterangan Nonaktif <span class="aud-req-star">*</span></label>
            <textarea id="audStatusReasonInput" placeholder="Tuliskan alasan mengapa auditorium ini dinonaktifkan (contoh: Renovasi ruang, perbaikan AC, dll.)..."></textarea>
            <div class="aud-status-reason-err" id="audStatusReasonErr" hidden>Keterangan penonaktifan wajib diisi.</div>
        </div>

        <div class="aud-status-modal-actions">
            <button type="button" class="aud-btn aud-status-cancel" id="audStatusCancel">Tidak</button>
            <button type="button" class="aud-btn aud-status-confirm" id="audStatusConfirm">Ya, Lanjutkan</button>
        </div>
    </div>
</div>
<form method="POST" id="auditoriumStatusForm" style="display:none;">
    @csrf
    @method('PATCH')
    <input type="hidden" name="deactivation_reason" id="audDeactivationReasonHidden" value="">
</form>

<script>
const audSaveRoute = "{{ route('admin.auditoriums.save') }}";
const AUD_SLIDE_INTERVAL = 4000;
const AUD_MAX_FILE_BYTES = 2 * 1024 * 1024; // 2 MB per foto
let audStagedFiles = []; // Menyimpan seluruh foto baru yang ditambahkan secara bertahap

function normalizeCampusLocation(val) {
    const raw = (val || '').trim();
    if (!raw) return '';
    if (/pondok\s*labu/i.test(raw)) return 'UPNVJ - Kampus Pondok Labu';
    if (/limo/i.test(raw)) return 'UPNVJ - Kampus Limo';
    return raw;
}

/* CUSTOM GLASSMORPHISM DROPDOWN (SAMA DENGAN HALAMAN PROFILE) */
function closeAllGlassSelects(exceptSelect) {
    document.querySelectorAll('[data-glass-select].is-open').forEach(function (sel) {
        if (exceptSelect && sel === exceptSelect) return;
        sel.classList.remove('is-open');
        const trig = sel.querySelector('[data-glass-trigger]');
        const menu = sel.querySelector('[data-glass-menu]');
        if (trig) trig.setAttribute('aria-expanded', 'false');
        if (menu) menu.hidden = true;
    });
}

function setGlassSelectValue(selectRoot, newValue) {
    if (!selectRoot) return;
    const hiddenInput = selectRoot.querySelector('[data-glass-input]');
    const trigger = selectRoot.querySelector('[data-glass-trigger]');
    const labelEl = selectRoot.querySelector('[data-glass-label]');
    const menu = selectRoot.querySelector('[data-glass-menu]');
    const placeholder = trigger ? (trigger.dataset.placeholder || 'Pilih lokasi kampus') : 'Pilih lokasi kampus';

    if (hiddenInput) {
        hiddenInput.value = newValue || '';
    }

    if (labelEl && trigger) {
        if (newValue) {
            labelEl.textContent = newValue;
            trigger.classList.remove('is-placeholder');
        } else {
            labelEl.textContent = placeholder;
            trigger.classList.add('is-placeholder');
        }
    }

    if (menu) {
        menu.querySelectorAll('.glass-select-option').forEach(function (opt) {
            const isMatch = opt.dataset.value === newValue;
            opt.classList.toggle('is-selected', isMatch);
            opt.setAttribute('aria-selected', isMatch ? 'true' : 'false');
            const oldCheck = opt.querySelector('svg');
            if (oldCheck) oldCheck.remove();
            if (isMatch) {
                const icon = document.createElement('i');
                icon.setAttribute('data-lucide', 'check');
                opt.appendChild(icon);
            }
        });
        if (window.lucide) window.lucide.createIcons();
    }
}

/* SLIDER & SLIDESHOW FOTO AUDITORIUM */
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

/* SLIDE-OVER DRAWER TAMBAH / EDIT (KANAN KE KIRI) */
function clearSelectedAudCards() {
    document.querySelectorAll('.auditorium-card').forEach(card => {
        card.classList.remove('is-active');
    });
}

function showAudPanel() {
    closeAllGlassSelects();
    document.getElementById('audDrawerBackdrop').classList.add('is-open');
    document.getElementById('audFormPanel').classList.add('is-open');
    const body = document.querySelector('#audFormPanel .aud-form-body');
    if (body) body.scrollTop = 0;
}

function closeAuditoriumPanel() {
    closeAllGlassSelects();
    document.getElementById('audDrawerBackdrop').classList.remove('is-open');
    document.getElementById('audFormPanel').classList.remove('is-open');
    clearSelectedAudCards();
}

/* KELOLA MULTIPLE FOTO SECARA BERTAHAP TANPA PERLU KLIK SAVE DULU */
function syncAudFileInput() {
    const input = document.getElementById('audImageInput');
    if (!input) return;

    const dt = new DataTransfer();
    audStagedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

function renderAudStagedPreviews() {
    const container = document.getElementById('audNewPhotoPreview');
    const badge = document.getElementById('audStagedCountBadge');
    const dropzone = document.getElementById('audDropzone');
    const title = document.getElementById('audUploadTitle');
    const sub = document.getElementById('audDropzoneSub');

    container.innerHTML = '';

    if (audStagedFiles.length === 0) {
        container.style.display = 'none';
        badge.style.display = 'none';
        dropzone.classList.remove('is-compact');
        title.innerHTML = 'Seret &amp; lepas foto di sini, atau <span>pilih file</span>';
        sub.style.display = '';
        sub.textContent = 'Unggah banyak foto sekaligus, maksimal 2 MB per foto (JPG, JPEG, PNG, WEBP)';
        return;
    }

    container.style.display = 'grid';
    badge.style.display = 'inline-flex';
    badge.textContent = audStagedFiles.length + ' foto terpilih';
    dropzone.classList.add('is-compact');
    title.innerHTML = '<span>+ Tambah foto lagi</span> atau seret ke sini';
    sub.style.display = 'none';
    sub.textContent = '';

    audStagedFiles.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'aud-photo-thumb';
        item.title = file.name;

        const img = document.createElement('img');
        const objectUrl = URL.createObjectURL(file);
        img.src = objectUrl;
        img.alt = file.name;
        img.onload = () => URL.revokeObjectURL(objectUrl);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'aud-photo-thumb-remove';
        removeBtn.textContent = '✕';
        removeBtn.title = 'Hapus foto ini';
        removeBtn.onclick = function (e) {
            e.stopPropagation();
            removeAudStagedPhoto(index);
        };

        const newBadge = document.createElement('span');
        newBadge.className = 'aud-photo-thumb-new-badge';
        newBadge.textContent = 'Baru';

        item.appendChild(img);
        item.appendChild(removeBtn);
        item.appendChild(newBadge);
        container.appendChild(item);
    });
}

function appendAudPhotos(fileList) {
    const warningEl = document.getElementById('audPhotoWarning');
    const incoming = Array.from(fileList || []);
    if (incoming.length === 0) {
        syncAudFileInput();
        return;
    }

    const rejectedMessages = [];

    incoming.forEach(file => {
        const isValidType = /^image\/(jpeg|jpg|png|webp)$/i.test(file.type)
            || /\.(jpe?g|png|webp)$/i.test(file.name);

        if (!isValidType) {
            rejectedMessages.push(file.name + ' (format harus JPG, JPEG, PNG, atau WEBP)');
            return;
        }

        if (file.size > AUD_MAX_FILE_BYTES) {
            rejectedMessages.push(file.name + ' (ukuran melebihi 2 MB)');
            return;
        }

        const alreadyAdded = audStagedFiles.some(
            existing => existing.name === file.name && existing.size === file.size && existing.lastModified === file.lastModified
        );

        if (!alreadyAdded) {
            audStagedFiles.push(file);
        }
    });

    syncAudFileInput();
    renderAudStagedPreviews();

    if (rejectedMessages.length > 0) {
        warningEl.style.display = 'block';
        warningEl.textContent = 'Beberapa file tidak ditambahkan: ' + rejectedMessages.join(', ') + '.';
    } else {
        warningEl.style.display = 'none';
        warningEl.textContent = '';
    }
}

function removeAudStagedPhoto(index) {
    audStagedFiles.splice(index, 1);
    syncAudFileInput();
    renderAudStagedPreviews();
}

function handleAudFileInputChange(input) {
    appendAudPhotos(input.files);
}

function resetPhotoInputsAndPreviews() {
    audStagedFiles = [];
    const input = document.getElementById('audImageInput');
    if (input) input.value = '';
    document.getElementById('audRemovedInputs').innerHTML = '';

    const warningEl = document.getElementById('audPhotoWarning');
    if (warningEl) {
        warningEl.style.display = 'none';
        warningEl.textContent = '';
    }

    renderAudStagedPreviews();
}

function toggleRemoveExistingPhoto(btn, path) {
    const thumb = btn.closest('.aud-photo-thumb');
    const removedWrap = document.getElementById('audRemovedInputs');
    const isRemoved = thumb.classList.toggle('is-removed');

    if (isRemoved) {
        btn.textContent = '↺';
        btn.title = 'Batalkan hapus';
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'remove_images[]';
        hidden.value = path;
        hidden.dataset.path = path;
        removedWrap.appendChild(hidden);
    } else {
        btn.textContent = '✕';
        btn.title = 'Hapus foto ini';
        removedWrap.querySelectorAll('input[name="remove_images[]"]').forEach(el => {
            if (el.dataset.path === path) el.remove();
        });
    }
}

function openAddAuditoriumPanel() {
    clearSelectedAudCards();
    resetPhotoInputsAndPreviews();

    const form = document.getElementById('auditoriumForm');
    form.action = audSaveRoute;
    document.getElementById('audFormMethod').value = 'POST';

    document.getElementById('audFormTitle').textContent = 'Tambah Auditorium Baru';
    const submitBtn = document.getElementById('audSubmitBtn');
    submitBtn.textContent = 'Simpan Auditorium';
    submitBtn.className = 'aud-btn aud-btn-primary';

    document.getElementById('audNameInput').value = '';
    setGlassSelectValue(document.querySelector('[data-glass-select="lokasi_kampus"]'), '');
    document.getElementById('audLokasiGedungInput').value = '';
    document.getElementById('audJamMulaiInput').value = '07:00';
    document.getElementById('audJamSelesaiInput').value = '17:00';
    document.getElementById('audCapacityInput').value = '';
    document.getElementById('audFacilitiesInput').value = '';
    document.getElementById('audDescriptionInput').value = '';

    document.getElementById('audCurrentPhotoWrap').style.display = 'none';
    document.getElementById('audCurrentPhotoList').innerHTML = '';
    document.getElementById('audImageLabel').innerHTML = 'Foto Auditorium <span class="aud-req-star">*</span>';
    document.getElementById('audActiveCheckboxWrap').style.display = 'flex';
    document.getElementById('audActiveCheckbox').checked = true;

    showAudPanel();
    if (window.innerWidth > 900) {
        setTimeout(() => {
            const nameInput = document.getElementById('audNameInput');
            if (nameInput) nameInput.focus({ preventScroll: true });
        }, 400);
    }
}

function openEditAuditoriumPanel(btn) {
    clearSelectedAudCards();
    resetPhotoInputsAndPreviews();

    const card = document.getElementById(btn.dataset.cardId);
    if (card) card.classList.add('is-active');

    const form = document.getElementById('auditoriumForm');
    form.action = btn.dataset.updateUrl;
    document.getElementById('audFormMethod').value = 'PUT';

    document.getElementById('audFormTitle').textContent = 'Edit ' + (btn.dataset.name || 'Auditorium');
    const submitBtn = document.getElementById('audSubmitBtn');
    submitBtn.textContent = 'Simpan Perubahan';
    submitBtn.className = 'aud-btn aud-btn-edit';

    document.getElementById('audNameInput').value = btn.dataset.name || '';
    setGlassSelectValue(
        document.querySelector('[data-glass-select="lokasi_kampus"]'),
        normalizeCampusLocation(btn.dataset.lokasiKampus)
    );
    document.getElementById('audLokasiGedungInput').value = btn.dataset.lokasiGedung || '';
    document.getElementById('audJamMulaiInput').value = btn.dataset.jamMulai || '07:00';
    document.getElementById('audJamSelesaiInput').value = btn.dataset.jamSelesai || '17:00';
    document.getElementById('audCapacityInput').value = btn.dataset.capacity || '';
    document.getElementById('audFacilitiesInput').value = btn.dataset.facilities || '';
    document.getElementById('audDescriptionInput').value = btn.dataset.description || '';

    const photoWrap = document.getElementById('audCurrentPhotoWrap');
    const photoList = document.getElementById('audCurrentPhotoList');
    const noPhotoText = document.getElementById('audNoPhotoText');
    photoWrap.style.display = 'block';
    photoList.innerHTML = '';

    let photos = [];
    try {
        photos = JSON.parse(btn.dataset.photos || '[]');
    } catch (e) {
        photos = [];
    }

    if (photos.length > 0) {
        photoList.style.display = 'grid';
        noPhotoText.style.display = 'none';
        photos.forEach(item => {
            const thumb = document.createElement('div');
            thumb.className = 'aud-photo-thumb';

            const img = document.createElement('img');
            img.src = item.url;
            img.alt = 'Foto auditorium';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'aud-photo-thumb-remove';
            removeBtn.textContent = '✕';
            removeBtn.title = 'Hapus foto ini';
            removeBtn.onclick = () => toggleRemoveExistingPhoto(removeBtn, item.path);

            const badge = document.createElement('span');
            badge.className = 'aud-photo-thumb-badge';
            badge.textContent = 'Dihapus';

            thumb.appendChild(img);
            thumb.appendChild(removeBtn);
            thumb.appendChild(badge);
            photoList.appendChild(thumb);
        });
    } else {
        photoList.style.display = 'none';
        noPhotoText.style.display = 'block';
    }

    document.getElementById('audImageLabel').innerHTML = 'Tambah Foto Auditorium <span class="aud-req-star">*</span>';
    document.getElementById('audActiveCheckboxWrap').style.display = 'none';

    showAudPanel();
    if (window.innerWidth > 900) {
        setTimeout(() => {
            const nameInput = document.getElementById('audNameInput');
            if (nameInput) nameInput.focus({ preventScroll: true });
        }, 400);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    // Validasi dropdown lokasi kampus saat form disubmit
    const auditoriumForm = document.getElementById('auditoriumForm');
    if (auditoriumForm) {
        auditoriumForm.addEventListener('submit', function (e) {
            const campusInput = document.getElementById('audLokasiKampusInput');
            if (campusInput && !campusInput.value.trim()) {
                e.preventDefault();
                const campusSelect = document.querySelector('[data-glass-select="lokasi_kampus"]');
                if (campusSelect) {
                    closeAllGlassSelects(campusSelect);
                    campusSelect.classList.add('is-open');
                    const trig = campusSelect.querySelector('[data-glass-trigger]');
                    const menu = campusSelect.querySelector('[data-glass-menu]');
                    if (trig) {
                        trig.setAttribute('aria-expanded', 'true');
                        trig.focus({ preventScroll: true });
                    }
                    if (menu) menu.hidden = false;
                }
            }
        });
    }

    // Inisialisasi Custom Glass Dropdown
    document.querySelectorAll('[data-glass-select]').forEach(function (selectRoot) {
        const trigger = selectRoot.querySelector('[data-glass-trigger]');
        const menu = selectRoot.querySelector('[data-glass-menu]');

        if (trigger && menu) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                if (selectRoot.classList.contains('is-locked')) return;

                const willOpen = !selectRoot.classList.contains('is-open');
                closeAllGlassSelects(selectRoot);

                selectRoot.classList.toggle('is-open', willOpen);
                trigger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
                menu.hidden = !willOpen;
            });

            menu.addEventListener('click', function (e) {
                const option = e.target.closest('.glass-select-option');
                if (!option) return;
                e.stopPropagation();
                setGlassSelectValue(selectRoot, option.dataset.value || '');
                closeAllGlassSelects();
                trigger.focus({ preventScroll: true });
            });
        }
    });

    document.addEventListener('click', function () {
        closeAllGlassSelects();
    });

    // Pindahkan backdrop & drawer ke root <body> agar posisi fixed tidak terpengaruh transform/filter layout induk
    const backdrop = document.getElementById('audDrawerBackdrop');
    const drawer = document.getElementById('audFormPanel');
    if (backdrop && drawer) {
        document.body.appendChild(backdrop);
        document.body.appendChild(drawer);

        backdrop.addEventListener('touchmove', function (e) {
            e.preventDefault();
        }, { passive: false });

        backdrop.addEventListener('wheel', function (e) {
            e.preventDefault();
        }, { passive: false });

        // Cegah scroll chaining dari dalam drawer ke halaman belakang saat batas atas/bawah tercapai
        drawer.addEventListener('wheel', function (e) {
            const glassMenu = e.target.closest('.glass-select-menu');
            if (glassMenu) return;

            const formBody = drawer.querySelector('.aud-form-body');
            if (!formBody || !formBody.contains(e.target)) {
                e.preventDefault();
                return;
            }

            const atTop = formBody.scrollTop <= 0 && e.deltaY < 0;
            const atBottom = Math.ceil(formBody.scrollTop + formBody.clientHeight) >= formBody.scrollHeight && e.deltaY > 0;
            if (atTop || atBottom) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    // Inisialisasi Drag & Drop pada area unggah foto auditorium
    const dropzone = document.getElementById('audDropzone');
    const fileInput = document.getElementById('audImageInput');
    if (dropzone && fileInput) {
        ['dragenter', 'dragover'].forEach(evtName => {
            dropzone.addEventListener(evtName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'dragend'].forEach(evtName => {
            dropzone.addEventListener(evtName, function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (!dropzone.contains(e.relatedTarget)) {
                    dropzone.classList.remove('is-dragover');
                }
            });
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.remove('is-dragover');
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                appendAudPhotos(e.dataTransfer.files);
            }
        });

        dropzone.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
            }
        });
    }

    // Slideshow otomatis + kontrol geser manual (hover pause & swipe)
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
                const current = parseInt(slider.dataset.index || '0', 10);
                setAudSliderIndex(slider, current + (diff < 0 ? 1 : -1));
            }
            startAudSliderAutoplay(slider);
        }, { passive: true });

        startAudSliderAutoplay(slider);
    });

    // MODAL KONFIRMASI STATUS + FORM KETERANGAN PENONAKTIFAN
    const modal = document.getElementById('auditoriumStatusModal');
    const titleEl = document.getElementById('audStatusTitle');
    const message = document.getElementById('audStatusMessage');
    const reasonBox = document.getElementById('audStatusReasonBox');
    const reasonInput = document.getElementById('audStatusReasonInput');
    const reasonErr = document.getElementById('audStatusReasonErr');
    const reasonHidden = document.getElementById('audDeactivationReasonHidden');
    const confirmButton = document.getElementById('audStatusConfirm');
    const cancelButton = document.getElementById('audStatusCancel');
    const form = document.getElementById('auditoriumStatusForm');
    let pendingSwitch = null;
    let statusStep = 'confirm'; // 'confirm' | 'reason'

    // Pindahkan modal ke root <body> agar backdrop blur menutupi seluruh layar (termasuk topbar) tanpa menggeser layout
    if (modal) {
        document.body.appendChild(modal);

        modal.addEventListener('wheel', function (e) {
            if (e.target !== reasonInput) {
                e.preventDefault();
            }
        }, { passive: false });

        modal.addEventListener('touchmove', function (e) {
            if (e.target !== reasonInput) {
                e.preventDefault();
            }
        }, { passive: false });
    }

    if (reasonInput) {
        reasonInput.addEventListener('input', function () {
            if (reasonInput.value.trim() !== '') {
                reasonInput.classList.remove('is-invalid');
                if (reasonErr) reasonErr.hidden = true;
            }
        });
    }

    function resetStatusModalState() {
        statusStep = 'confirm';
        if (titleEl) titleEl.textContent = 'Konfirmasi Perubahan Status';
        if (reasonBox) reasonBox.hidden = true;
        if (reasonInput) {
            reasonInput.value = '';
            reasonInput.classList.remove('is-invalid');
        }
        if (reasonErr) reasonErr.hidden = true;
        if (reasonHidden) reasonHidden.value = '';
        if (cancelButton) cancelButton.textContent = 'Tidak';
    }

    document.querySelectorAll('[data-auditorium-toggle]').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            pendingSwitch = toggle;
            resetStatusModalState();

            const willActivate = toggle.checked;
            const auditoriumName = toggle.dataset.auditoriumName || 'auditorium ini';
            message.textContent = willActivate
                ? 'Yakin ingin mengaktifkan ' + auditoriumName + '? Auditorium akan tersedia untuk dipinjam.'
                : 'Yakin ingin menonaktifkan ' + auditoriumName + '? Auditorium tidak akan tersedia untuk peminjaman baru.';
            confirmButton.textContent = willActivate ? 'Ya, Aktifkan' : 'Ya, Nonaktifkan';
            confirmButton.classList.toggle('is-deactivate', !willActivate);
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            confirmButton.focus({ preventScroll: true });
        });
    });

    function closeModal(restoreSwitch) {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (restoreSwitch && pendingSwitch) {
            pendingSwitch.checked = pendingSwitch.dataset.currentStatus === '1';
        }
        pendingSwitch = null;
        resetStatusModalState();
    }

    cancelButton.addEventListener('click', function () { closeModal(true); });
    modal.addEventListener('click', function (event) {
        if (event.target === modal) closeModal(true);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllGlassSelects();
            if (modal.classList.contains('is-open')) {
                closeModal(true);
            } else if (document.getElementById('audFormPanel').classList.contains('is-open')) {
                closeAuditoriumPanel();
            }
        }
    });
    confirmButton.addEventListener('click', function () {
        if (!pendingSwitch) return;

        const willActivate = pendingSwitch.checked;
        const auditoriumName = pendingSwitch.dataset.auditoriumName || 'auditorium ini';

        // Langkah 1 -> Langkah 2 khusus saat menonaktifkan: tampilkan input keterangan wajib
        if (!willActivate && statusStep === 'confirm') {
            statusStep = 'reason';
            titleEl.textContent = 'Keterangan Penonaktifan';
            message.textContent = 'Berikan alasan mengapa ' + auditoriumName + ' dinonaktifkan agar dapat diketahui pengguna.';
            reasonBox.hidden = false;
            reasonInput.value = '';
            reasonInput.classList.remove('is-invalid');
            reasonErr.hidden = true;
            cancelButton.textContent = 'Batal';
            confirmButton.textContent = 'Simpan & Nonaktifkan';
            confirmButton.classList.add('is-deactivate');
            setTimeout(() => reasonInput.focus({ preventScroll: true }), 60);
            return;
        }

        // Validasi & submit keterangan penonaktifan
        if (!willActivate && statusStep === 'reason') {
            const reasonVal = (reasonInput.value || '').trim();
            if (!reasonVal) {
                reasonInput.classList.add('is-invalid');
                reasonErr.hidden = false;
                reasonInput.focus({ preventScroll: true });
                return;
            }
            reasonHidden.value = reasonVal;
        } else {
            reasonHidden.value = '';
        }

        form.action = pendingSwitch.dataset.toggleUrl;
        form.submit();
    });
});
</script>

@endsection