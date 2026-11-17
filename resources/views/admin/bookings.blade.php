@extends('layouts.panel')

@section('panel')
@php
    $statusOptions = [
        'Pending' => 'Menunggu',
        'Disetujui' => 'Disetujui',
        'Ditolak' => 'Ditolak',
        'Perlu Revisi' => 'Perlu Revisi',
        'Dibatalkan' => 'Dibatalkan',
    ];

    $statusClasses = [
        'Pending' => 'st-pending',
        'Disetujui' => 'st-approved',
        'Ditolak' => 'st-rejected',
        'Perlu Revisi' => 'st-revision',
        'Dibatalkan' => 'st-cancelled',
    ];

    $formatBookingCode = function ($booking) {
        $datePart = ($booking->booking_date ?? $booking->created_at)->format('Ymd');
        return '#SPD-' . $datePart . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);
    };

    $formatSessionRange = function ($booking) {
        $startRaw = substr($booking->start_time, 0, 5);
        $endRaw = substr($booking->end_time, 0, 5);
        $startDot = str_replace(':', '.', $startRaw);
        $endDot = str_replace(':', '.', $endRaw);

        if ($startRaw <= '08:00' && $endRaw >= '16:00') {
            $sessionLabel = 'Full Day';
        } elseif ($endRaw <= '12:30') {
            $sessionLabel = 'Sesi Pagi';
        } elseif ($startRaw >= '12:00') {
            $sessionLabel = 'Sesi Siang';
        } else {
            $sessionLabel = 'Sesi Acara';
        }

        return "{$sessionLabel} ({$startDot}–{$endDot})";
    };

    $drawerItems = $bookings->getCollection()
        ->concat($extraDrawerBookings ?? collect())
        ->unique('id');
@endphp

<style>
.booking-page {
    --bp-ink: #163626;
    --bp-muted: #4c6456;
    --bp-subtle: #64786c;
    --bp-line: rgba(22, 54, 38, .09);
    --bp-green: #1f7a49;
    --bp-green-mid: #237c4d;
    --bp-glass-bg: linear-gradient(
        145deg,
        rgba(255, 255, 255, .68) 0%,
        rgba(255, 255, 255, .42) 100%
    );
    --bp-glass-border: 1px solid rgba(255, 255, 255, .55);
    --bp-glass-shadow: 0 8px 28px rgba(22, 54, 38, .06);
    color: var(--bp-ink);
}

.booking-page,
.booking-page button,
.booking-page input,
.booking-page select,
.booking-page textarea {
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

/* PAGE HEADER */
.booking-page .bp-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 18px;
    margin: 4px 0 22px;
}

.booking-page .bp-heading h1 {
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -.03em;
    margin: 0;
    color: var(--bp-ink);
}

.booking-page .bp-heading p {
    margin: 6px 0 0;
    color: var(--bp-muted);
    font-size: 13px;
    line-height: 1.55;
}

/* SCORECARDS (GLASSMORPHISM MATCHING DASHBOARD) */
.booking-page .bp-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
}

.booking-page .bp-stat {
    background: var(--bp-glass-bg);
    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);
    border: var(--bp-glass-border);
    box-shadow: var(--bp-glass-shadow);
    border-radius: 16px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    transition: transform .2s ease, box-shadow .2s ease;
}

.booking-page .bp-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(22, 54, 38, .09);
}

.booking-page .bp-stat-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    background: rgba(33, 128, 77, .14);
    color: var(--bp-green);
    border: 1px solid rgba(255, 255, 255, .45);
    flex-shrink: 0;
}

.booking-page .bp-stat-icon svg {
    width: 19px;
    height: 19px;
}

.booking-page .bp-stat-icon.is-warning {
    background: rgba(217, 119, 6, .13);
    color: #b45309;
}

.booking-page .bp-stat-icon.is-danger {
    background: rgba(220, 38, 38, .12);
    color: #b91c1c;
}

.booking-page .bp-stat small {
    display: block;
    color: var(--bp-muted);
    font-size: 11px;
    font-weight: 700;
}

.booking-page .bp-stat strong {
    display: block;
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -.03em;
    color: var(--bp-ink);
    line-height: 1.2;
    margin-top: 3px;
}

/* ALERT BANNERS */
.booking-page .bp-alert {
    border-radius: 14px;
    padding: 13px 16px;
    margin-bottom: 16px;
    font-size: 12.5px;
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

.booking-page .bp-alert.is-error {
    background: rgba(254, 226, 226, .85);
    color: #991b1b;
    border: 1px solid rgba(248, 113, 113, .35);
}

.booking-page .bp-alert.is-success {
    background: rgba(220, 252, 231, .85);
    color: #166534;
    border: 1px solid rgba(74, 222, 128, .35);
    font-weight: 600;
}

/* MAIN TABLE CARD (GLASSMORPHISM) */
.booking-page .bp-card {
    background: var(--bp-glass-bg);
    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);
    border: var(--bp-glass-border);
    box-shadow: var(--bp-glass-shadow);
    border-radius: 16px;
    overflow: hidden;
}

.booking-page .bp-toolbar {
    padding: 16px 18px;
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    border-bottom: 1px solid var(--bp-line);
    background: rgba(255, 255, 255, .25);
}

.booking-page .bp-search {
    position: relative;
    flex: 1 1 240px;
}

.booking-page .bp-search input {
    width: 100%;
    height: 40px;
    padding: 0 14px 0 38px;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 12px;
    background: rgba(255, 255, 255, .78);
    color: var(--bp-ink);
    font-size: 12.5px;
    font-weight: 500;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.booking-page .bp-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--bp-subtle);
    display: inline-flex;
    pointer-events: none;
}

.booking-page .bp-search-icon svg {
    width: 16px;
    height: 16px;
}

.booking-page .bp-filter {
    height: 40px;
    padding: 0 12px;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 12px;
    background: rgba(255, 255, 255, .78);
    color: var(--bp-ink);
    font-size: 12.5px;
    font-weight: 600;
    min-width: 140px;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.booking-page .bp-filter:focus,
.booking-page .bp-search input:focus {
    background: #fff;
    border-color: var(--bp-green);
    box-shadow: 0 0 0 3px rgba(31, 122, 73, .14);
    outline: none;
}

.booking-page .bp-reset {
    font-size: 12px;
    font-weight: 700;
    color: var(--bp-green);
    padding: 10px 10px;
    border-radius: 10px;
    text-decoration: none;
    transition: background .2s ease;
}

.booking-page .bp-reset:hover {
    background: rgba(33, 128, 77, .12);
}

/* STATUS FILTER TABS */
.booking-page .bp-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 14px 18px;
    border-bottom: 1px solid var(--bp-line);
}

.booking-page .bp-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid transparent;
    border-radius: 11px;
    padding: 8px 13px;
    font-size: 12px;
    font-weight: 700;
    color: var(--bp-muted);
    white-space: nowrap;
    text-decoration: none;
    transition: background .2s ease, color .2s ease, border-color .2s ease;
}

.booking-page .bp-tab:hover:not(.active) {
    background: rgba(255, 255, 255, .55);
    color: var(--bp-ink);
}

.booking-page .bp-tab.active {
    background: rgba(33, 128, 77, .16);
    color: var(--bp-green);
    border-color: rgba(33, 128, 77, .24);
}

.booking-page .bp-tab span {
    padding: 1px 7px;
    border-radius: 999px;
    background: rgba(22, 54, 38, .07);
    font-size: 11px;
    font-weight: 800;
}

.booking-page .bp-tab.active span {
    background: rgba(31, 122, 73, .16);
    color: var(--bp-green);
}

/* TABLE STYLING */
.booking-page .bp-table-wrap {
    overflow-x: auto;
}

.booking-page table {
    width: 100%;
    border-collapse: collapse;
    min-width: 780px;
    font-size: 13px;
}

.booking-page th {
    padding: 13px 18px;
    text-align: left;
    background: rgba(255, 255, 255, .36);
    border-bottom: 1px solid var(--bp-line);
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--bp-muted);
    white-space: nowrap;
}

.booking-page td {
    padding: 15px 18px;
    border-bottom: 1px solid rgba(22, 54, 38, .06);
    vertical-align: middle;
    font-size: 12.5px;
    line-height: 1.5;
    color: var(--bp-ink);
    transition: background .18s ease;
}

.booking-page tbody tr:hover td {
    background: rgba(255, 255, 255, .42);
}

.booking-page tbody tr:last-child td {
    border-bottom: 0;
}

.booking-page .bp-code {
    font-size: 11px;
    font-weight: 800;
    color: var(--bp-green);
    letter-spacing: .02em;
}

.booking-page .bp-primary {
    font-weight: 700;
    color: var(--bp-ink);
    display: block;
    max-width: 240px;
    white-space: normal;
    line-height: 1.45;
    margin-top: 2px;
}

.booking-page .bp-sub {
    display: block;
    color: var(--bp-subtle);
    font-size: 11px;
    font-weight: 500;
    margin-top: 2px;
    line-height: 1.4;
}

/* PASTEL STATUS PILLS */
.booking-page .bp-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
}

.booking-page .bp-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.booking-page .st-pending {
    background: #fef3c7;
    color: #92400e;
}

.booking-page .st-approved {
    background: #dcfce7;
    color: #166534;
}

.booking-page .st-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.booking-page .st-revision {
    background: #ffedd5;
    color: #9a3412;
}

.booking-page .st-cancelled {
    background: #f1f5f9;
    color: #475569;
}

/* BUTTONS */
.booking-page .bp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1px solid transparent;
    border-radius: 11px;
    padding: 9px 14px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
}

.booking-page .bp-btn svg {
    width: 15px;
    height: 15px;
}

.booking-page .bp-btn:hover {
    transform: translateY(-1px);
}

.booking-page .bp-btn:disabled {
    cursor: not-allowed;
    transform: none;
    opacity: .55;
}

.booking-page .bp-view {
    background: rgba(255, 255, 255, .75);
    border-color: rgba(22, 54, 38, .12);
    color: var(--bp-ink);
}

.booking-page .bp-view:hover {
    background: #fff;
    border-color: rgba(31, 122, 73, .35);
    color: var(--bp-green);
}

.booking-page .bp-approve {
    background: var(--bp-green-mid);
    color: #fff;
    box-shadow: 0 4px 12px rgba(35, 124, 77, .22);
}

.booking-page .bp-approve:hover {
    background: var(--bp-green);
}

.booking-page .bp-reject {
    background: #fee2e2;
    border-color: rgba(220, 38, 38, .25);
    color: #991b1b;
}

.booking-page .bp-reject:hover {
    background: #fecaca;
}

.booking-page .bp-revision {
    background: #fef3c7;
    border-color: rgba(217, 119, 6, .28);
    color: #92400e;
}

.booking-page .bp-revision:hover {
    background: #fde68a;
}

.booking-page .bp-empty {
    padding: 46px 20px;
    text-align: center;
    color: var(--bp-muted);
    font-size: 13px;
}

.booking-page .bp-empty-icon {
    width: 46px;
    height: 46px;
    margin: 0 auto 10px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    background: rgba(33, 128, 77, .12);
    color: var(--bp-green);
}

.booking-page .bp-pagination {
    padding: 14px 18px;
    border-top: 1px solid var(--bp-line);
    background: rgba(255, 255, 255, .25);
}

/* MODAL KEPUTUSAN */
.booking-page .bp-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(16, 34, 25, .48);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 10005;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
}

.booking-page .bp-modal-backdrop.show {
    display: flex;
}

.booking-page .bp-modal {
    width: min(520px, 100%);
    max-height: 90vh;
    overflow-y: auto;
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(255, 255, 255, .75);
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 25px 70px rgba(7, 27, 16, .22);
}

.booking-page .bp-modal h3 {
    font-size: 17px;
    font-weight: 800;
    margin: 0 0 8px;
    color: var(--bp-ink);
}

.booking-page .bp-modal p {
    font-size: 12.5px;
    color: var(--bp-muted);
    line-height: 1.6;
}

.booking-page .bp-modal label {
    display: block;
    font-size: 11.5px;
    font-weight: 700;
    margin: 14px 0 6px;
    color: var(--bp-ink);
}

.booking-page .bp-modal textarea {
    width: 100%;
    border: 1px solid rgba(22, 54, 38, .15);
    border-radius: 12px;
    padding: 11px 12px;
    font-size: 12.5px;
    min-height: 86px;
    resize: vertical;
    outline: none;
    box-sizing: border-box;
}

.booking-page .bp-modal textarea:focus {
    border-color: var(--bp-green);
    box-shadow: 0 0 0 3px rgba(31, 122, 73, .14);
}

.booking-page .bp-modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 18px;
}

.booking-page .bp-modal .bp-confirm {
    background: var(--bp-green-mid);
    color: #fff;
}

.booking-page .bp-modal .bp-cancel {
    background: #eef2ef;
    color: #3c5446;
}

/* DRAWER TINJAU PENGAJUAN & PRIORITAS */
.booking-page .bp-detail-open {
    min-width: 82px;
    min-height: 36px;
    border-radius: 11px;
    font-size: 12px;
}

.booking-page .bp-conflict-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-left: 6px;
    padding: 2px 8px;
    border-radius: 999px;
    background: #fef3c7;
    color: #92400e;
    font-size: 10px;
    font-weight: 800;
}

.booking-page .bp-conflict-chip svg {
    width: 11px;
    height: 11px;
}

.booking-page .bp-drawer-backdrop {
    position: fixed;
    inset: 0;
    z-index: 10000;
    display: flex;
    justify-content: flex-end;
    background: rgba(15, 32, 23, .40);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
}

.booking-page .bp-drawer-backdrop.show {
    opacity: 1;
    visibility: visible;
}

.booking-page .bp-drawer {
    display: flex;
    flex-direction: column;
    width: min(510px, 100%);
    height: 100%;
    background: #f8fbf9;
    box-shadow: -18px 0 55px rgba(12, 32, 21, .16);
    transform: translateX(100%);
    transition: transform .28s ease;
}

.booking-page .bp-drawer-backdrop.show .bp-drawer {
    transform: translateX(0);
}

.booking-page .bp-drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 24px 16px;
    background: #fff;
    border-bottom: 1px solid rgba(22, 54, 38, .08);
    flex-shrink: 0;
}

.booking-page .bp-drawer-header h2 {
    margin: 0;
    color: var(--bp-ink);
    font-size: 18px;
    font-weight: 800;
    letter-spacing: -.02em;
}

.booking-page .bp-drawer-header p {
    margin: 4px 0 0;
    color: var(--bp-subtle);
    font-size: 12px;
}

.booking-page .bp-drawer-close,
.booking-page .bp-pdf-header button {
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    flex-shrink: 0;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 50%;
    background: #fff;
    color: var(--bp-muted);
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}

.booking-page .bp-drawer-close:hover,
.booking-page .bp-pdf-header button:hover {
    background: #f1f6f2;
    color: var(--bp-ink);
}

.booking-page .bp-drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px 24px 0;
    display: flex;
    flex-direction: column;
}

.booking-page .bp-drawer-content {
    flex: 1;
    padding-bottom: 20px;
}

/* KARTU UTAMA PENGAJUAN */
.booking-page .bp-drawer-summary {
    padding: 20px;
    border: 1px solid rgba(22, 54, 38, .10);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 4px 18px rgba(22, 54, 38, .04);
}

.booking-page .bp-drawer-summary.is-pending {
    border: 2px solid var(--bp-green-mid);
    box-shadow: 0 6px 20px rgba(35, 124, 77, .09);
}

.booking-page .bp-drawer-summary-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.booking-page .bp-drawer-summary-top .bp-code {
    font-size: 11px;
    color: var(--bp-green);
}

.booking-page .bp-drawer-summary h3 {
    margin: 13px 0 5px;
    color: var(--bp-ink);
    font-size: 17px;
    line-height: 1.38;
    font-weight: 800;
    letter-spacing: -.02em;
}

.booking-page .bp-drawer-date {
    margin: 0;
    color: var(--bp-subtle);
    font-size: 12.5px;
    font-weight: 500;
}

.booking-page .bp-drawer-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 13px;
}

.booking-page .bp-drawer-tags span {
    padding: 5px 10px;
    border-radius: 8px;
    background: rgba(33, 128, 77, .12);
    color: var(--bp-green);
    font-size: 11px;
    font-weight: 700;
}

.booking-page .bp-drawer-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px 20px;
    padding: 18px 0 16px;
}

.booking-page .bp-drawer-info-grid small,
.booking-page .bp-drawer-description small {
    display: block;
    margin-bottom: 3px;
    color: var(--bp-subtle);
    font-size: 11px;
    font-weight: 600;
}

.booking-page .bp-drawer-info-grid strong {
    display: block;
    color: var(--bp-ink);
    font-size: 12.5px;
    font-weight: 800;
    line-height: 1.45;
}

.booking-page .bp-drawer-info-grid span {
    display: block;
    color: var(--bp-subtle);
    font-size: 11px;
    margin-top: 2px;
}

/* KELENGKAPAN PERIZINAN */
.booking-page .bp-drawer-check {
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 14px;
    border: 1px solid rgba(22, 54, 38, .10);
    border-radius: 12px;
    background: #f6faf7;
}

.booking-page .bp-check-icon {
    display: grid;
    place-items: center;
    width: 22px;
    height: 22px;
    margin-top: 1px;
    flex-shrink: 0;
    border-radius: 6px;
    background: var(--bp-green-mid);
    color: #fff;
    font-size: 12px;
    font-weight: 800;
}

.booking-page .bp-check-icon.is-missing {
    background: #d97706;
}

.booking-page .bp-drawer-check p {
    margin: 0;
    color: #3c5446;
    font-size: 12px;
    line-height: 1.55;
}

.booking-page .bp-drawer-check p strong {
    color: var(--bp-ink);
    font-weight: 800;
}

.booking-page .bp-inline-pdf-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 9px;
    padding: 6px 11px;
    border: 1px solid rgba(31, 122, 73, .28);
    border-radius: 8px;
    background: #fff;
    color: var(--bp-green);
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s ease;
}

.booking-page .bp-inline-pdf-link:hover {
    background: rgba(33, 128, 77, .10);
}

.booking-page .bp-drawer-description {
    margin-top: 13px;
    padding: 12px 14px;
    border-radius: 12px;
    background: #f6faf7;
}

.booking-page .bp-drawer-description p {
    margin: 3px 0 0;
    color: #3c5446;
    font-size: 12px;
    line-height: 1.55;
}

/* PENGAJUAN BENTROK */
.booking-page .bp-conflict-section {
    margin-top: 20px;
}

.booking-page .bp-conflict-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
}

.booking-page .bp-conflict-title {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #9a5818;
    font-size: 12.5px;
    font-weight: 800;
}

.booking-page .bp-conflict-hint {
    color: var(--bp-subtle);
    font-size: 11px;
    font-weight: 600;
}

.booking-page .bp-conflict-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.booking-page .bp-conflict-card {
    padding: 15px 16px;
    border: 1px solid rgba(22, 54, 38, .10);
    border-radius: 14px;
    background: #fff;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.booking-page .bp-conflict-card:hover {
    border-color: rgba(31, 122, 73, .38);
    box-shadow: 0 4px 14px rgba(22, 54, 38, .06);
}

.booking-page .bp-conflict-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.booking-page .bp-conflict-code {
    color: var(--bp-muted);
    font-size: 11.5px;
    font-weight: 800;
}

.booking-page .bp-prioritize-btn {
    border: 0;
    border-radius: 9px;
    background: var(--bp-green-mid);
    color: #fff;
    padding: 6px 12px;
    font-size: 11.5px;
    font-weight: 700;
    cursor: pointer;
    transition: background .2s ease;
}

.booking-page .bp-prioritize-btn:hover {
    background: var(--bp-green);
}

.booking-page .bp-conflict-card h4 {
    margin: 10px 0 4px;
    color: var(--bp-ink);
    font-size: 14px;
    font-weight: 800;
    line-height: 1.4;
}

.booking-page .bp-conflict-meta {
    margin: 0;
    color: var(--bp-subtle);
    font-size: 12px;
}

.booking-page .bp-conflict-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.booking-page .bp-conflict-tags span {
    padding: 4px 9px;
    border: 1px solid rgba(22, 54, 38, .10);
    border-radius: 7px;
    background: #f8fbf9;
    color: var(--bp-muted);
    font-size: 10.5px;
    font-weight: 700;
}

/* RIWAYAT KEPUTUSAN */
.booking-page .bp-drawer-history {
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid rgba(22, 54, 38, .08);
}

.booking-page .bp-drawer-history > strong {
    display: block;
    margin-bottom: 8px;
    color: var(--bp-ink);
    font-size: 12.5px;
}

.booking-page .bp-history-item {
    margin-top: 8px;
    padding: 11px 13px;
    border: 1px solid rgba(22, 54, 38, .09);
    border-radius: 12px;
    background: #fff;
}

.booking-page .bp-history-item span {
    display: block;
    color: var(--bp-ink);
    font-size: 11.5px;
    font-weight: 800;
}

.booking-page .bp-history-item small {
    display: block;
    margin-top: 3px;
    color: var(--bp-subtle);
    font-size: 10.5px;
}

.booking-page .bp-history-item p {
    margin: 5px 0 0;
    color: var(--bp-muted);
    font-size: 11.5px;
    line-height: 1.5;
}

/* FOOTER TOMBOL KEPUTUSAN DRAWER */
.booking-page .bp-drawer-actions {
    position: sticky;
    bottom: 0;
    margin: 0 -24px;
    padding: 16px 24px 20px;
    border-top: 1px solid rgba(22, 54, 38, .08);
    background: rgba(255, 255, 255, .96);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    flex-shrink: 0;
}

.booking-page .bp-drawer-actions > .bp-btn {
    width: 100%;
    min-height: 44px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
}

.booking-page .bp-drawer-actions-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 10px;
}

.booking-page .bp-drawer-actions-row .bp-btn {
    min-height: 42px;
    border-radius: 12px;
    font-size: 12.5px;
    font-weight: 700;
}

/* PENAMPIL PDF */
.booking-page .bp-pdf-backdrop {
    position: fixed;
    inset: 0;
    z-index: 10002;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(10, 25, 16, .62);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}

.booking-page .bp-pdf-backdrop.show {
    display: flex;
}

.booking-page .bp-pdf-window {
    display: flex;
    flex-direction: column;
    width: min(1000px, 100%);
    height: min(92vh, 900px);
    overflow: hidden;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 25px 70px rgba(0, 0, 0, .28);
}

.booking-page .bp-pdf-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 13px 18px;
    border-bottom: 1px solid rgba(22, 54, 38, .09);
    color: var(--bp-ink);
    font-size: 13.5px;
}

.booking-page .bp-pdf-window iframe {
    flex: 1;
    width: 100%;
    min-height: 0;
    border: 0;
    background: #eef1ef;
}

body.bp-drawer-open {
    overflow: hidden;
}

/* RESPONSIVE */
@media (max-width: 1120px) {
    .booking-page .bp-stats {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .booking-page .bp-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .booking-page .bp-heading {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 600px) {
    .booking-page .bp-drawer {
        width: 100%;
    }

    .booking-page .bp-drawer-header {
        padding: 15px 17px;
    }

    .booking-page .bp-drawer-body {
        padding: 16px 16px 0;
    }

    .booking-page .bp-drawer-actions {
        margin: 0 -16px;
        padding: 13px 16px 18px;
    }

    .booking-page .bp-pdf-backdrop {
        padding: 8px;
    }

    .booking-page .bp-pdf-window {
        height: 96vh;
        border-radius: 12px;
    }
}

@media (max-width: 520px) {
    .booking-page .bp-stats {
        gap: 10px;
    }

    .booking-page .bp-stat {
        padding: 14px;
    }

    .booking-page .bp-stat strong {
        font-size: 21px;
    }

    .booking-page .bp-stat-icon {
        width: 36px;
        height: 36px;
    }

    .booking-page .bp-heading h1 {
        font-size: 21px;
    }

    .booking-page .bp-toolbar {
        padding: 12px;
    }

    .booking-page .bp-filter {
        flex: 1 1 100%;
        width: 100%;
    }
}
</style>

<div class="booking-page">

    {{-- HEADER --}}
    <div class="bp-heading">
        <div>
            <h1>Kelola Peminjaman Auditorium</h1>
            <p>Kelola pengajuan peminjaman, periksa jadwal, dan tentukan keputusan untuk setiap permohonan.</p>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="bp-stats">
        <div class="bp-stat">
            <div class="bp-stat-icon is-warning">
                <i data-lucide="clock"></i>
            </div>
            <div>
                <small>Menunggu Verifikasi</small>
                <strong>{{ $stats['pending'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon">
                <i data-lucide="check-circle-2"></i>
            </div>
            <div>
                <small>Disetujui</small>
                <strong>{{ $stats['approved'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon is-danger">
                <i data-lucide="x-circle"></i>
            </div>
            <div>
                <small>Ditolak</small>
                <strong>{{ $stats['rejected'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon">
                <i data-lucide="clipboard-list"></i>
            </div>
            <div>
                <small>Total Pengajuan</small>
                <strong>{{ $stats['total'] }}</strong>
            </div>
        </div>

        <div class="bp-stat">
            <div class="bp-stat-icon is-warning">
                <i data-lucide="rotate-ccw"></i>
            </div>
            <div>
                <small>Perlu Revisi</small>
                <strong>{{ $stats['revision'] }}</strong>
            </div>
        </div>
    </div>

    {{-- PESAN VALIDASI --}}
    @if($errors->any())
        <div class="bp-alert is-error">
            <strong>Pengajuan belum dapat diproses.</strong>
            <ul style="margin:6px 0 0 18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bp-alert is-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABEL PENGELOLAAN --}}
    <div class="bp-card">

        {{-- FILTER DAN PENCARIAN --}}
        <form method="GET" action="{{ route('admin.bookings') }}" class="bp-toolbar">
            <div class="bp-search">
                <span class="bp-search-icon">
                    <i data-lucide="search"></i>
                </span>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari kode, kegiatan, peminjam, atau organisasi..."
                >
            </div>

            <select class="bp-filter" name="status">
                <option value="">Semua Status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select class="bp-filter" name="auditorium">
                <option value="">Semua Auditorium</option>
                @foreach($auditoriums as $auditorium)
                    <option
                        value="{{ $auditorium->id }}"
                        @selected((string) request('auditorium') === (string) $auditorium->id)
                    >
                        {{ $auditorium->name }}
                    </option>
                @endforeach
            </select>

            <input
                class="bp-filter"
                type="date"
                name="date"
                value="{{ request('date') }}"
                aria-label="Filter tanggal"
            >

            <button class="bp-btn bp-approve" type="submit">
                <i data-lucide="sliders-horizontal"></i>
                Terapkan Filter
            </button>

            <a class="bp-reset" href="{{ route('admin.bookings') }}">
                Reset
            </a>
        </form>

        {{-- TAB STATUS --}}
        <div class="bp-tabs">
            <a
                class="bp-tab {{ !request('status') ? 'active' : '' }}"
                href="{{ route('admin.bookings', request()->except('status', 'page')) }}"
            >
                Semua <span>{{ $stats['total'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Pending' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Pending'])) }}"
            >
                Menunggu <span>{{ $stats['pending'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Disetujui' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Disetujui'])) }}"
            >
                Disetujui <span>{{ $stats['approved'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Ditolak' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Ditolak'])) }}"
            >
                Ditolak <span>{{ $stats['rejected'] }}</span>
            </a>

            <a
                class="bp-tab {{ request('status') === 'Perlu Revisi' ? 'active' : '' }}"
                href="{{ route('admin.bookings', array_merge(request()->except('page'), ['status' => 'Perlu Revisi'])) }}"
            >
                Perlu Revisi <span>{{ $stats['revision'] }}</span>
            </a>
        </div>

        {{-- DAFTAR PENGAJUAN --}}
        <div class="bp-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Pengajuan</th>
                        <th>Peminjam / Pengusul</th>
                        <th>Auditorium & Jadwal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($bookings as $b)
                    @php
                        $statusClass = $statusClasses[$b->status] ?? 'st-cancelled';
                        $bookingCode = $formatBookingCode($b);
                        $conflicts = $b->relationLoaded('conflicts') ? $b->conflicts : collect();
                        $conflictCount = $conflicts->count();
                    @endphp

                    <tr>
                        <td>
                            <span class="bp-code">{{ $bookingCode }}</span>
                            @if($conflictCount > 0)
                                <span class="bp-conflict-chip">
                                    <i data-lucide="alert-triangle"></i>
                                    {{ $conflictCount + 1 }} Bentrok
                                </span>
                            @endif
                            <span class="bp-sub">
                                {{ $b->created_at->format('d M Y, H:i') }}
                            </span>
                            <span class="bp-primary">{{ $b->event_name }}</span>
                        </td>

                        <td>
                            <span class="bp-primary">{{ $b->user->name }}</span>
                            <span class="bp-sub">{{ $b->organization }}</span>
                        </td>

                        <td>
                            <span class="bp-primary">{{ $b->auditorium->name }}</span>
                            <span class="bp-sub">
                                {{ $b->booking_date->translatedFormat('d M Y') }}
                                · {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}
                            </span>
                        </td>

                        <td>
                            <span class="bp-status {{ $statusClass }}">
                                <span class="bp-status-dot"></span>
                                {{ $b->status === 'Pending' ? 'Sedang Diproses' : $b->status }}
                            </span>
                        </td>

                        <td>
                            <button
                                type="button"
                                class="bp-btn bp-view bp-detail-open"
                                data-open-drawer="{{ $b->id }}"
                                data-booking-subtitle="{{ $bookingCode }} • {{ $b->auditorium->name }}"
                            >
                                <i data-lucide="eye"></i>
                                Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="bp-empty">
                                <div class="bp-empty-icon">
                                    <i data-lucide="inbox"></i>
                                </div>
                                <strong>Tidak ada pengajuan ditemukan</strong>
                                <div style="margin-top:5px">
                                    Coba ubah kata kunci atau filter pencarian.
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="bp-pagination">
            {{ $bookings->withQueryString()->links() }}
        </div>
    </div>

    {{-- TEMPLATE PANEL TINJAUAN & PRIORITAS --}}
    @foreach($drawerItems as $item)
        @php
            $itemStatusClass = $statusClasses[$item->status] ?? 'st-cancelled';
            $itemCode = $formatBookingCode($item);
            $itemSession = $formatSessionRange($item);
            $itemConflicts = $item->relationLoaded('conflicts') ? $item->conflicts : collect();
            $approvedConflicts = $itemConflicts->where('status', 'Disetujui')->count();
            $pendingConflicts = $itemConflicts->where('status', 'Pending')->count();
            $totalConflicts = $itemConflicts->count();
        @endphp

        <template id="booking-drawer-{{ $item->id }}">
            <div class="bp-drawer-content">
                {{-- KARTU UTAMA PENGAJUAN --}}
                <div class="bp-drawer-summary {{ $item->status === 'Pending' ? 'is-pending' : '' }}">
                    <div class="bp-drawer-summary-top">
                        <span class="bp-code">
                            {{ $itemCode }} • {{ strtoupper($item->status === 'Pending' ? 'SEDANG DITINJAU' : $item->status) }}
                        </span>
                        <span class="bp-status {{ $itemStatusClass }}">
                            <span class="bp-status-dot"></span>
                            {{ $item->status === 'Pending' ? 'Sedang Diproses' : $item->status }}
                        </span>
                    </div>

                    <h3>{{ $item->event_name }}</h3>
                    <p class="bp-drawer-date">
                        {{ $item->booking_date->translatedFormat('d M Y') }} • {{ $itemSession }}
                    </p>

                    <div class="bp-drawer-tags">
                        <span>#{{ preg_replace('/\s+/', '', $item->organization) }}</span>
                        <span>#{{ preg_replace('/\s+/', '', $item->auditorium->name) }}</span>
                        @if($item->event_description)
                            <span>#Kegiatan</span>
                        @endif
                    </div>

                    <div class="bp-drawer-info-grid">
                        <div>
                            <small>Pengusul</small>
                            <strong>{{ $item->organization }}</strong>
                            <span>{{ $item->user->name }}</span>
                        </div>
                        <div>
                            <small>Peserta</small>
                            <strong>
                                {{ !empty($item->participant_count) ? $item->participant_count . ' pax' : 'Sesuai surat' }}
                            </strong>
                        </div>
                        <div>
                            <small>Penanggung Jawab</small>
                            <strong>{{ $item->pic_name }}</strong>
                            <span>{{ $item->pic_phone }}</span>
                        </div>
                        <div>
                            <small>Waktu Masuk</small>
                            <strong>{{ $item->created_at->translatedFormat('d M Y, H:i') }}</strong>
                        </div>
                    </div>

                    <div class="bp-drawer-check">
                        <span class="bp-check-icon {{ $item->surat_path ? '' : 'is-missing' }}">
                            {{ $item->surat_path ? '✓' : '!' }}
                        </span>
                        <div>
                            @if($item->surat_path)
                                <p>
                                    <strong>Kelengkapan Perizinan:</strong> Surat Permohonan peminjaman auditorium telah dilampirkan dan siap diverifikasi.
                                </p>
                                <button
                                    type="button"
                                    class="bp-inline-pdf-link"
                                    data-preview-pdf="{{ route('admin.booking.letter', $item) }}"
                                >
                                    Lihat Surat Permohonan (PDF) ↗
                                </button>
                            @else
                                <p>
                                    <strong>Kelengkapan Perizinan:</strong> Dokumen surat permohonan belum diunggah oleh pemohon.
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($item->event_description)
                        <div class="bp-drawer-description">
                            <small>Deskripsi Kegiatan</small>
                            <p>{{ $item->event_description }}</p>
                        </div>
                    @endif

                    @if($item->admin_note)
                        <div class="bp-drawer-description">
                            <small>Catatan Admin</small>
                            <p>{{ $item->admin_note }}</p>
                        </div>
                    @endif
                </div>

                {{-- DAFTAR PENGAJUAN BENTROK PADA SLOT INI --}}
                @if($totalConflicts > 0)
                    <div class="bp-conflict-section">
                        <div class="bp-conflict-header">
                            <span class="bp-conflict-title">
                                ⚡ Total Pengajuan Bentrok di Slot Ini ({{ $totalConflicts + 1 }})
                            </span>
                            <span class="bp-conflict-hint">Pilih yang diprioritaskan</span>
                        </div>

                        <div class="bp-conflict-list">
                            @foreach($itemConflicts as $conflict)
                                @php
                                    $conflictCode = $formatBookingCode($conflict);
                                    $conflictSession = $formatSessionRange($conflict);
                                @endphp
                                <div class="bp-conflict-card">
                                    <div class="bp-conflict-card-top">
                                        <span class="bp-conflict-code">{{ $conflictCode }}</span>

                                        @if($conflict->status === 'Disetujui')
                                            <span class="bp-status st-approved">
                                                <span class="bp-status-dot"></span>
                                                Sudah Disetujui
                                            </span>
                                        @else
                                            <button
                                                type="button"
                                                class="bp-prioritize-btn"
                                                data-open-drawer="{{ $conflict->id }}"
                                                data-booking-subtitle="{{ $conflictCode }} • {{ $conflict->auditorium->name }}"
                                            >
                                                Prioritaskan Ini
                                            </button>
                                        @endif
                                    </div>

                                    <h4>{{ $conflict->event_name }}</h4>
                                    <p class="bp-conflict-meta">
                                        {{ $conflict->organization }} • {{ $conflictSession }}
                                    </p>

                                    <div class="bp-conflict-tags">
                                        <span>#{{ preg_replace('/\s+/', '', $conflict->organization) }}</span>
                                        <span>#{{ preg_replace('/\s+/', '', $conflict->auditorium->name) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- RIWAYAT KEPUTUSAN --}}
                @if($item->histories->isNotEmpty())
                    <div class="bp-drawer-history">
                        <strong>Riwayat Keputusan</strong>
                        @foreach($item->histories->sortByDesc('created_at') as $history)
                            <div class="bp-history-item">
                                <span>{{ $history->from_status ?: 'Pengajuan' }} → {{ $history->to_status }}</span>
                                <small>{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                @if($history->note)
                                    <p>{{ $history->note }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- TOMBOL KEPUTUSAN BAWAH --}}
            @if($item->status === 'Pending')
                <div class="bp-drawer-actions">
                    <button
                        type="button"
                        class="bp-btn bp-approve"
                        data-decision-id="{{ $item->id }}"
                        data-decision-status="Disetujui"
                        data-approved-conflicts="{{ $approvedConflicts }}"
                        data-pending-conflicts="{{ $pendingConflicts }}"
                        @disabled($approvedConflicts > 0)
                    >
                        @if($approvedConflicts > 0)
                            Jadwal Sudah Terkunci ({{ $approvedConflicts }} Disetujui)
                        @elseif($pendingConflicts > 0)
                            Setujui & Tolak Otomatis ({{ $pendingConflicts }} Pengajuan Lain)
                        @else
                            ✓ Setujui Pengajuan
                        @endif
                    </button>

                    <div class="bp-drawer-actions-row">
                        <button
                            type="button"
                            class="bp-btn bp-revision"
                            data-decision-id="{{ $item->id }}"
                            data-decision-status="Perlu Revisi"
                            data-approved-conflicts="0"
                            data-pending-conflicts="0"
                        >
                            Minta Revisi
                        </button>
                        <button
                            type="button"
                            class="bp-btn bp-reject"
                            data-decision-id="{{ $item->id }}"
                            data-decision-status="Ditolak"
                            data-approved-conflicts="0"
                            data-pending-conflicts="0"
                        >
                            Tolak Pengajuan
                        </button>
                    </div>
                </div>
            @endif
        </template>
    @endforeach

    {{-- PANEL DETAIL DARI SISI KANAN --}}
    <div class="bp-drawer-backdrop" id="bookingDrawerBackdrop" aria-hidden="true">
        <aside class="bp-drawer" id="bookingDrawer" aria-labelledby="bookingDrawerTitle">
            <header class="bp-drawer-header">
                <div>
                    <h2 id="bookingDrawerTitle">Tinjau Pengajuan & Prioritas</h2>
                    <p id="bookingDrawerSubtitle">Periksa detail pengajuan sebelum mengambil keputusan.</p>
                </div>
                <button type="button" class="bp-drawer-close" id="closeBookingDrawer" aria-label="Tutup">×</button>
            </header>
            <div class="bp-drawer-body" id="bookingDrawerBody"></div>
        </aside>
    </div>

    {{-- PENAMPIL PDF --}}
    <div class="bp-pdf-backdrop" id="pdfPreviewModal" aria-hidden="true">
        <div class="bp-pdf-window">
            <header class="bp-pdf-header">
                <strong>Pratinjau Surat Permohonan</strong>
                <button type="button" id="closePdfPreview" aria-label="Tutup">×</button>
            </header>
            <iframe id="pdfPreviewFrame" title="Pratinjau dokumen PDF"></iframe>
        </div>
    </div>

    {{-- MODAL KEPUTUSAN --}}
    <div class="bp-modal-backdrop" id="decisionModal" role="dialog" aria-modal="true" aria-labelledby="decisionTitle">
        <div class="bp-modal">
            <h3 id="decisionTitle">Konfirmasi Keputusan</h3>

            <p id="decisionDescription">
                Pastikan data pengajuan telah diperiksa sebelum keputusan disimpan.
            </p>

            <form
                method="POST"
                id="decisionForm"
                data-action-base="{{ url('/admin/pengajuan') }}"
            >
                @csrf
                @method('PATCH')

                <input type="hidden" name="status" id="decisionStatus">

                <div id="normalNote">
                    <label for="adminNote">Catatan Admin (opsional)</label>
                    <textarea
                        name="admin_note"
                        id="adminNote"
                        placeholder="Tambahkan catatan untuk peminjam..."
                    ></textarea>
                </div>

                <div id="conflictReason" style="display:none">
                    <label for="conflictNote">
                        Alasan penolakan otomatis pengajuan yang bentrok (wajib)
                    </label>

                    <textarea
                        name="conflict_rejection_reason"
                        id="conflictNote"
                        placeholder="Jelaskan alasan prioritas atau keputusan BUK..."
                    ></textarea>

                    <p style="margin-top:6px">
                        Pengajuan lain yang menggunakan auditorium dan waktu yang beririsan
                        akan ditolak otomatis setelah pengajuan ini disetujui.
                        Pastikan pertimbangan prioritas sudah dilakukan.
                    </p>
                </div>

                <div class="bp-modal-actions">
                    <button
                        type="button"
                        class="bp-btn bp-cancel"
                        onclick="closeDecisionModal()"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="bp-btn bp-confirm"
                        id="decisionSubmit"
                    >
                        Simpan Keputusan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openBookingDrawer(id, button) {
        const template = document.getElementById('booking-drawer-' + id);
        const backdrop = document.getElementById('bookingDrawerBackdrop');
        const body = document.getElementById('bookingDrawerBody');
        const subtitle = document.getElementById('bookingDrawerSubtitle');

        if (!template || !backdrop || !body) return;

        body.innerHTML = template.innerHTML;
        body.scrollTop = 0;
        subtitle.textContent = (button && button.dataset.bookingSubtitle)
            ? button.dataset.bookingSubtitle
            : 'Periksa detail pengajuan sebelum mengambil keputusan.';

        if (window.lucide) {
            window.lucide.createIcons();
        }

        backdrop.classList.add('show');
        backdrop.setAttribute('aria-hidden', 'false');
        document.body.classList.add('bp-drawer-open');
    }

    function closeBookingDrawer() {
        const backdrop = document.getElementById('bookingDrawerBackdrop');
        if (!backdrop) return;

        backdrop.classList.remove('show');
        backdrop.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('bp-drawer-open');
    }

    function openPdfPreview(url) {
        const modal = document.getElementById('pdfPreviewModal');
        const frame = document.getElementById('pdfPreviewFrame');

        frame.src = url;
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closePdfPreview() {
        const modal = document.getElementById('pdfPreviewModal');
        const frame = document.getElementById('pdfPreviewFrame');

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        frame.src = 'about:blank';
    }

    function openDecisionModal(id, status, approvedConflicts, pendingConflicts) {
        const modal = document.getElementById('decisionModal');
        const form = document.getElementById('decisionForm');

        const isApprove = status === 'Disetujui';
        const isRevision = status === 'Perlu Revisi';
        const slotLocked = approvedConflicts > 0;

        form.action = form.dataset.actionBase + '/' + id + '/keputusan';

        document.getElementById('decisionStatus').value = status;

        document.getElementById('decisionTitle').textContent = isApprove
            ? (pendingConflicts > 0 ? 'Setujui & Tolak Otomatis (' + pendingConflicts + ' Pengajuan Lain)' : 'Setujui Pengajuan')
            : (isRevision ? 'Minta Revisi Pengajuan' : 'Tolak Pengajuan');

        document.getElementById('decisionDescription').textContent = isRevision
            ? 'Pengajuan akan dikembalikan kepada peminjam untuk diperbaiki. Tuliskan bagian yang perlu direvisi pada catatan admin.'
            : (!isApprove
                ? 'Pengajuan akan ditetapkan sebagai Ditolak. Tambahkan catatan agar peminjam memahami keputusan BUK.'
                : (
                slotLocked
                    ? 'Pengajuan tidak dapat disetujui karena terdapat peminjaman yang sudah disetujui pada waktu yang beririsan.'
                    : (
                        pendingConflicts > 0
                            ? 'Ada ' + pendingConflicts + ' pengajuan lain yang jadwalnya beririsan. Jika dilanjutkan, pengajuan lain tersebut akan ditolak otomatis.'
                            : 'Pengajuan akan ditetapkan sebagai Disetujui dan jadwal auditorium dikunci.'
                    )
            ));

        const conflictReason = document.getElementById('conflictReason');
        const conflictNote = document.getElementById('conflictNote');
        const decisionSubmit = document.getElementById('decisionSubmit');

        document.getElementById('normalNote').style.display = 'block';
        document.getElementById('adminNote').required = isRevision;

        conflictReason.style.display =
            isApprove && pendingConflicts > 0 && !slotLocked
                ? 'block'
                : 'none';

        conflictNote.required =
            isApprove && pendingConflicts > 0 && !slotLocked;

        decisionSubmit.disabled = slotLocked;
        decisionSubmit.style.opacity = slotLocked ? '.45' : '1';

        decisionSubmit.textContent = slotLocked
            ? 'Jadwal Sudah Terkunci'
            : (isRevision ? 'Kirim Permintaan Revisi' : 'Simpan Keputusan');

        document.getElementById('adminNote').value = '';
        conflictNote.value = '';

        modal.classList.add('show');
    }

    function closeDecisionModal() {
        document.getElementById('decisionModal').classList.remove('show');
    }

    document.addEventListener('click', function(event) {
        const detailButton = event.target.closest('[data-open-drawer]');
        if (detailButton) {
            openBookingDrawer(detailButton.dataset.openDrawer, detailButton);
            return;
        }

        const pdfButton = event.target.closest('[data-preview-pdf]');
        if (pdfButton) {
            openPdfPreview(pdfButton.dataset.previewPdf);
            return;
        }

        if (event.target.closest('#closeBookingDrawer')) {
            closeBookingDrawer();
            return;
        }

        if (event.target.closest('#closePdfPreview')) {
            closePdfPreview();
            return;
        }

        const decisionButton = event.target.closest('[data-decision-id]');
        if (decisionButton && !decisionButton.disabled) {
            openDecisionModal(
                Number(decisionButton.dataset.decisionId),
                decisionButton.dataset.decisionStatus,
                Number(decisionButton.dataset.approvedConflicts),
                Number(decisionButton.dataset.pendingConflicts)
            );
            return;
        }

        if (event.target.id === 'bookingDrawerBackdrop') {
            closeBookingDrawer();
        }

        if (event.target.id === 'pdfPreviewModal') {
            closePdfPreview();
        }

        if (event.target.id === 'decisionModal') {
            closeDecisionModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeDecisionModal();
            closePdfPreview();
            closeBookingDrawer();
        }
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
</script>
@endsection