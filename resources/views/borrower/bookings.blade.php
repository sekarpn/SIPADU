@extends('layouts.panel')

@section('panel')
@php
    $user = auth()->user();

    // Ambil seluruh pengajuan milik peminjam beserta relasi auditorium & riwayat keputusan
    $allUserBookings = \App\Models\Booking::with(['auditorium', 'histories'])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    $stats = [
        'pending'   => $allUserBookings->where('status', 'Pending')->count(),
        'approved'  => $allUserBookings->where('status', 'Disetujui')->count(),
        'rejected'  => $allUserBookings->where('status', 'Ditolak')->count(),
        'total'     => $allUserBookings->count(),
        'revision'  => $allUserBookings->where('status', 'Perlu Revisi')->count(),
        'cancelled' => $allUserBookings->where('status', 'Dibatalkan')->count(),
    ];

    $auditoriums = $allUserBookings->pluck('auditorium')
        ->filter()
        ->unique('id')
        ->sortBy('name');

    $statusClasses = [
        'Pending'      => 'st-pending',
        'Disetujui'    => 'st-approved',
        'Ditolak'      => 'st-rejected',
        'Perlu Revisi' => 'st-revision',
        'Dibatalkan'   => 'st-cancelled',
    ];

    $formatBookingCode = function ($booking) {
        $datePart = ($booking->booking_date ?? $booking->created_at)->format('Ymd');
        return '#SPD-' . $datePart . '-' . str_pad($booking->id, 3, '0', STR_PAD_LEFT);
    };

    $formatSessionRange = function ($booking) {
        $startRaw = substr((string) $booking->start_time, 0, 5);
        $endRaw   = substr((string) $booking->end_time, 0, 5);
        $startDot = str_replace(':', '.', $startRaw);
        $endDot   = str_replace(':', '.', $endRaw);

        $sessions = config('booking_sessions.sessions');
        $sessionLabel = 'Sesi Acara';
        foreach ($sessions as $session) {
            if ($startRaw === $session['start'] && $endRaw === $session['end']) {
                $sessionLabel = $session['label'];
                break;
            }
        }

        return "{$sessionLabel} ({$startDot}–{$endDot})";
    };

    // Kumpulkan semua catatan admin (Catatan Perbaikan + Catatan Bentrok/Penolakan) tanpa saling menimpa
    $collectFeedbacks = function ($booking) {
        $items = [];
        $seen  = [];

        if ($booking->relationLoaded('histories') && $booking->histories->isNotEmpty()) {
            foreach ($booking->histories->sortBy('created_at') as $h) {
                $noteText = trim((string) ($h->note ?? ''));
                if ($noteText === '') continue;

                $isConflict = str_contains($noteText, '[Ditolak otomatis karena bentrok]');
                $cleanText  = trim(str_replace('[Ditolak otomatis karena bentrok]', '', $noteText));
                $dedupKey   = mb_strtolower($cleanText);

                if (isset($seen[$dedupKey])) continue;
                $seen[$dedupKey] = true;

                if ($isConflict) {
                    $items[] = [
                        'type'  => 'is-conflict',
                        'title' => 'Ditolak (Bentrok Jadwal)',
                        'text'  => $cleanText,
                    ];
                } elseif ($h->to_status === 'Perlu Revisi') {
                    $items[] = [
                        'type'  => 'is-revision',
                        'title' => 'Catatan Perbaikan',
                        'text'  => $cleanText,
                    ];
                } elseif ($h->to_status === 'Ditolak') {
                    $items[] = [
                        'type'  => 'is-conflict',
                        'title' => 'Alasan Penolakan',
                        'text'  => $cleanText,
                    ];
                } else {
                    $items[] = [
                        'type'  => 'is-general',
                        'title' => 'Catatan Admin',
                        'text'  => $cleanText,
                    ];
                }
            }
        }

        if (!empty($booking->admin_note)) {
            $parts = preg_split("/\r\n\r\n|\n\n/", trim((string) $booking->admin_note));
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '') continue;

                $isConflict = str_contains($part, '[Ditolak otomatis karena bentrok]');
                $cleanText  = trim(str_replace('[Ditolak otomatis karena bentrok]', '', $part));
                $dedupKey   = mb_strtolower($cleanText);

                if (isset($seen[$dedupKey])) continue;
                $seen[$dedupKey] = true;

                if ($isConflict) {
                    $items[] = [
                        'type'  => 'is-conflict',
                        'title' => 'Ditolak (Bentrok Jadwal)',
                        'text'  => $cleanText,
                    ];
                } elseif ($booking->status === 'Perlu Revisi') {
                    $items[] = [
                        'type'  => 'is-revision',
                        'title' => 'Catatan Perbaikan',
                        'text'  => $cleanText,
                    ];
                } elseif ($booking->status === 'Ditolak') {
                    $items[] = [
                        'type'  => 'is-conflict',
                        'title' => 'Alasan Penolakan',
                        'text'  => $cleanText,
                    ];
                } else {
                    $items[] = [
                        'type'  => 'is-general',
                        'title' => 'Catatan Admin',
                        'text'  => $cleanText,
                    ];
                }
            }
        }

        return $items;
    };
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
    width: 100%;
    box-sizing: border-box;
}

.booking-page,
.booking-page button,
.booking-page input,
.booking-page select,
.booking-page textarea,
.sipadu-booking-panel,
.sipadu-booking-panel button {
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

/* PAGE HEADER */
.booking-page .bp-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    gap: 18px;
    margin: 0 0 22px;
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

/* TOMBOL AJUKAN PEMINJAMAN */
.booking-page .bp-primary-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 12px;
    background: #1f7a49;
    color: #ffffff !important;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none !important;
    border: 1px solid rgba(255, 255, 255, .28);
    box-shadow: 0 4px 14px rgba(31, 122, 73, .22);
    white-space: nowrap;
    flex-shrink: 0;
    transition: background .18s ease, transform .18s ease, box-shadow .18s ease;
}

.booking-page .bp-primary-cta:hover {
    background: #176139;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(31, 122, 73, .28);
}

.booking-page .bp-primary-cta svg {
    width: 16px;
    height: 16px;
}

/* SCORECARDS */
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
    cursor: pointer;
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
    background: rgba(31, 122, 73, .14);
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
}

.booking-page .bp-alert.is-success {
    background: rgba(220, 252, 231, .88);
    color: #166534;
    border: 1px solid rgba(74, 222, 128, .35);
    font-weight: 600;
}

/* MAIN TABLE CARD */
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
    padding: 0 34px 0 38px;
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

.booking-page .bp-search-clear {
    position: absolute;
    right: 9px;
    top: 50%;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: rgba(22, 54, 38, .08);
    color: var(--bp-muted);
    line-height: 0;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .18s ease, color .18s ease;
}

.booking-page .bp-search-clear.show {
    display: inline-flex;
}

.booking-page .bp-search-clear svg {
    width: 13px;
    height: 13px;
    display: block;
}

.booking-page .bp-search-clear:hover {
    background: rgba(31, 122, 73, .16);
    color: var(--bp-green);
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
    min-width: 145px;
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
    border: 0;
    background: transparent;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s ease;
}

.booking-page .bp-reset:hover {
    background: rgba(31, 122, 73, .12);
}

/* STATUS FILTER TABS */
.booking-page .bp-tabs {
    display: flex;
    align-items: center;
    gap: 8px;
    overflow-x: auto;
    padding: 0 18px;
    border-bottom: 1px solid rgba(22, 54, 38, .12);
    background: rgba(255, 255, 255, .18);
}

.booking-page .bp-tab {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 14px 16px;
    margin: 0;
    border: 0;
    border-bottom: 2.5px solid transparent;
    background: transparent;
    font-size: 13px;
    font-weight: 500;
    color: var(--bp-muted);
    white-space: nowrap;
    cursor: pointer;
    transition: color .2s ease, border-color .2s ease;
}

.booking-page .bp-tab:hover:not(.active) {
    color: var(--bp-ink);
}

.booking-page .bp-tab.active {
    color: var(--bp-ink);
    font-weight: 700;
    border-bottom-color: #237c4d;
}

.booking-page .bp-tab span {
    font-size: 12px;
    font-weight: 600;
    color: var(--bp-subtle);
}

.booking-page .bp-tab span::before {
    content: "(";
}

.booking-page .bp-tab span::after {
    content: ")";
}

.booking-page .bp-tab.active span {
    color: #237c4d;
    font-weight: 700;
}

/* TABLE STYLING (HEADER & ISI SEJAJAR RAPI + OVERRIDE DATATABLES) */
.booking-page .bp-table-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.booking-page table,
.booking-page table.dataTable {
    width: 100% !important;
    border-collapse: collapse !important;
    table-layout: fixed !important;
    min-width: 900px;
    font-size: 13px;
}

.booking-page thead th,
.booking-page table.dataTable thead th {
    padding: 13px 20px !important;
    background: rgba(255, 255, 255, .44) !important;
    border-bottom: 1px solid var(--bp-line) !important;
    font-size: 10.5px !important;
    font-weight: 800 !important;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--bp-muted) !important;
    white-space: nowrap;
    vertical-align: middle !important;
    box-sizing: border-box !important;
}

.booking-page tbody td,
.booking-page table.dataTable tbody td {
    padding: 16px 20px !important;
    border-bottom: 1px solid rgba(22, 54, 38, .06) !important;
    vertical-align: middle !important;
    font-size: 12.5px;
    line-height: 1.5;
    color: var(--bp-ink);
    box-sizing: border-box !important;
    transition: background .18s ease;
}

/* 1. PENGAJUAN (RATA KIRI) */
.booking-page thead th:nth-child(1),
.booking-page thead th:nth-child(1) .dt-column-title,
.booking-page thead th:nth-child(1) > div,
.booking-page tbody td:nth-child(1) {
    width: 22% !important;
    text-align: left !important;
}

/* 2. AUDITORIUM & JADWAL (RATA KIRI) */
.booking-page thead th:nth-child(2),
.booking-page thead th:nth-child(2) .dt-column-title,
.booking-page thead th:nth-child(2) > div,
.booking-page tbody td:nth-child(2) {
    width: 26% !important;
    text-align: left !important;
}

/* 3. STATUS (HEADER & PILL RATA TENGAH) */
.booking-page thead th:nth-child(3),
.booking-page thead th:nth-child(3) .dt-column-title,
.booking-page thead th:nth-child(3) > div,
.booking-page tbody td:nth-child(3) {
    width: 13% !important;
    text-align: center !important;
    justify-content: center !important;
    white-space: nowrap;
}

/* 4. CATATAN ADMIN (RATA KIRI) */
.booking-page thead th:nth-child(4),
.booking-page thead th:nth-child(4) .dt-column-title,
.booking-page thead th:nth-child(4) > div,
.booking-page tbody td:nth-child(4) {
    width: 21% !important;
    text-align: left !important;
}

/* 5. AKSI (HEADER & TOMBOL SEJAJAR RATA KANAN) */
.booking-page thead th:nth-child(5),
.booking-page thead th:nth-child(5) .dt-column-title,
.booking-page thead th:nth-child(5) > div,
.booking-page tbody td:nth-child(5) {
    width: 18% !important;
    text-align: right !important;
    justify-content: flex-end !important;
    white-space: nowrap;
    padding-right: 20px !important;
}

.booking-page tbody tr:hover td {
    background: rgba(255, 255, 255, .52);
}

.booking-page tbody tr:last-child td {
    border-bottom: 0 !important;
}

.booking-page .bp-code,
.sipadu-booking-panel .bp-code {
    font-size: 11px;
    font-weight: 800;
    color: #1f7a49;
    letter-spacing: .02em;
}

.booking-page .bp-primary {
    font-weight: 700;
    color: var(--bp-ink);
    display: block;
    max-width: 260px;
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

/* CATATAN ADMIN (MENDUKUNG CATATAN PERBAIKAN & BENTROK SEKALIGUS) */
.bp-note-stack {
    display: flex;
    flex-direction: column;
    gap: 7px;
    min-width: 200px;
    max-width: 290px;
}

.bp-note-box {
    padding: 8px 11px;
    border-radius: 10px;
    font-size: 11.5px;
    line-height: 1.45;
}

.bp-note-box strong {
    display: block;
    font-size: 10.5px;
    font-weight: 800;
    margin-bottom: 2px;
}

.bp-note-box p {
    margin: 0;
    white-space: pre-line;
    overflow-wrap: anywhere;
}

.bp-note-box.is-revision {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-left: 3.5px solid #d97706;
    color: #92400e;
}

.bp-note-box.is-conflict {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 3.5px solid #dc2626;
    color: #991b1b;
}

.bp-note-box.is-general {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-left: 3.5px solid #16a34a;
    color: #166534;
}

/* PASTEL STATUS PILLS */
.bp-status {
    display: inline-flex;
    align-items: center;
    flex-wrap: nowrap;
    flex-shrink: 0;
    width: max-content;
    white-space: nowrap;
    gap: 6px;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
}

.bp-status > span:not(.bp-status-dot) {
    white-space: nowrap;
    flex-shrink: 0;
}

.bp-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.st-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid rgba(217, 119, 6, .2);
}

.st-approved {
    background: #dcfce7;
    color: #166534;
    border: 1px solid rgba(22, 163, 74, .2);
}

.st-rejected {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid rgba(220, 38, 38, .2);
}

.st-revision {
    background: #ffedd5;
    color: #9a3412;
    border: 1px solid rgba(234, 88, 12, .2);
}

.st-cancelled {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid rgba(71, 85, 105, .18);
}

/* BUTTONS */
.bp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    border: 1px solid transparent;
    border-radius: 11px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    flex-shrink: 0;
    transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
}

.bp-btn svg {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

.bp-btn:hover {
    transform: translateY(-1px);
}

.bp-view {
    background: #e3f5ea;
    border-color: rgba(31, 122, 73, .22);
    color: #1f7a49;
}

.bp-view:hover {
    background: #d1eedc;
    border-color: rgba(31, 122, 73, .34);
    color: #155934;
}

.bp-reject {
    background: #fee2e2;
    border-color: rgba(220, 38, 38, .22);
    color: #b91c1c;
}

.bp-reject:hover {
    background: #fecaca;
}

.bp-action-row {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 7px;
    white-space: nowrap;
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
    background: rgba(31, 122, 73, .12);
    color: var(--bp-green);
}

/* FOOTER PAGINATION (KONTROL HALAMAN JIKA DATA > 10) */
.booking-page .bp-pagination {
    padding: 12px 18px;
    border-top: 1px solid var(--bp-line);
    background: rgba(255, 255, 255, .25);
    display: none;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
}

.booking-page .bp-pagination.has-pages {
    display: flex;
}

.bp-page-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 9px;
    border-radius: 9px;
    border: 1px solid rgba(22, 54, 38, .12);
    background: rgba(255, 255, 255, .82);
    color: var(--bp-ink);
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .16s ease;
}

.bp-page-btn:hover:not(:disabled) {
    background: rgba(31, 122, 73, .12);
    border-color: var(--bp-green);
    color: var(--bp-green);
}

.bp-page-btn.active {
    background: #1f7a49;
    border-color: #1f7a49;
    color: #ffffff;
}

.bp-page-btn:disabled {
    opacity: .45;
    cursor: not-allowed;
}

/* ==========================================================================
   POPUP / DRAWER DETAIL PENGAJUAN + BACKDROP BLUR
   ========================================================================== */
.sipadu-booking-overlay {
    position: fixed;
    inset: 0;
    z-index: 99990;
    background: rgba(15, 32, 23, .36);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    display: none;
}

.sipadu-booking-overlay.is-open {
    display: block;
}

.sipadu-booking-panel {
    position: fixed;
    top: 12px;
    right: -540px;
    bottom: 12px;
    z-index: 99995;
    width: min(490px, calc(100vw - 24px));
    background: #ffffff;
    border-radius: 18px;
    box-shadow: -14px 0 44px rgba(12, 32, 21, .2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: right .26s cubic-bezier(.22, .61, .36, 1);
    color: #163626;
}

.sipadu-booking-panel.is-open {
    right: 12px;
}

.sipadu-booking-panel-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    padding: 20px 22px 15px;
    background: #ffffff;
    border-bottom: 1px solid rgba(22, 54, 38, .08);
    flex-shrink: 0;
}

.sipadu-booking-panel-header h2 {
    margin: 0;
    color: #163626;
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -.02em;
    line-height: 1.25;
}

.sipadu-booking-panel-header p {
    margin: 4px 0 0;
    color: #6b7f73;
    font-size: 11.5px;
    font-weight: 500;
}

.sipadu-booking-panel-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    flex-shrink: 0;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 999px;
    background: #ffffff;
    color: #52665a;
    line-height: 0;
    cursor: pointer;
    transition: background .2s ease, color .2s ease;
}

.sipadu-booking-panel-close svg {
    width: 15px;
    height: 15px;
    display: block;
}

.sipadu-booking-panel-close:hover {
    background: #f1f6f2;
    color: #163626;
}

.sipadu-booking-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 18px 22px 0;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}

.bp-drawer-content {
    flex: 1;
    padding-bottom: 20px;
}

/* KARTU UTAMA PENGAJUAN */
.bp-drawer-summary {
    padding: 18px 18px 16px;
    border: 1px solid rgba(22, 54, 38, .12);
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(31, 122, 73, .05);
}

.bp-drawer-summary.is-pending {
    border: 1.5px solid #2b8a57;
    box-shadow: 0 6px 20px rgba(31, 122, 73, .08);
}

.bp-drawer-summary-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.bp-drawer-summary h3 {
    margin: 11px 0 4px;
    color: #163626;
    font-size: 17px;
    line-height: 1.35;
    font-weight: 800;
    letter-spacing: -.015em;
}

.bp-drawer-date {
    margin: 0;
    color: #52665a;
    font-size: 12px;
    font-weight: 500;
}

.bp-drawer-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
    margin-top: 12px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(22, 54, 38, .08);
}

.bp-drawer-tags span {
    padding: 4px 10px;
    border-radius: 8px;
    background: #f1f6f3;
    color: #2c5e43;
    font-size: 11px;
    font-weight: 700;
}

.bp-drawer-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px 18px;
    padding: 14px 0 16px;
}

.bp-drawer-info-grid small,
.bp-drawer-description small {
    display: block;
    margin-bottom: 2px;
    color: #6b7f73;
    font-size: 11px;
    font-weight: 500;
}

.bp-drawer-info-grid strong {
    display: block;
    color: #163626;
    font-size: 12.5px;
    font-weight: 700;
    line-height: 1.4;
}

.bp-drawer-info-grid span {
    display: block;
    color: #52665a;
    font-size: 11.5px;
    font-weight: 500;
    margin-top: 1px;
}

/* KELENGKAPAN PERIZINAN */
.bp-drawer-check {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 13px 14px;
    border: 1px solid rgba(31, 122, 73, .14);
    border-radius: 12px;
    background: #f2f9f5;
}

.bp-check-icon {
    display: grid;
    place-items: center;
    width: 20px;
    height: 20px;
    margin-top: 1px;
    flex-shrink: 0;
    border-radius: 6px;
    background: #1f7a49;
    color: #ffffff;
    font-size: 11.5px;
    font-weight: 800;
}

.bp-check-icon.is-missing {
    background: #d97706;
}

.bp-drawer-check p {
    margin: 0;
    color: #294735;
    font-size: 11.5px;
    line-height: 1.5;
}

.bp-drawer-check p strong {
    color: #163626;
    font-weight: 700;
}

.bp-drawer-description {
    margin-top: 12px;
    padding: 12px 14px;
    border-radius: 12px;
    background: #f6faf7;
    border: 1px solid rgba(22, 54, 38, .08);
}

.bp-drawer-description p {
    margin: 3px 0 0;
    color: #3c5446;
    font-size: 12px;
    line-height: 1.55;
}

/* RIWAYAT KEPUTUSAN */
.bp-drawer-history {
    margin-top: 18px;
    padding-top: 14px;
    border-top: 1px solid rgba(22, 54, 38, .08);
}

.bp-drawer-history > strong {
    display: block;
    margin-bottom: 8px;
    color: #163626;
    font-size: 12.5px;
}

.bp-history-item {
    margin-top: 8px;
    padding: 11px 13px;
    border: 1px solid rgba(22, 54, 38, .09);
    border-radius: 12px;
    background: #fcfefd;
}

.bp-history-item span {
    display: block;
    color: #163626;
    font-size: 11.5px;
    font-weight: 800;
}

.bp-history-item small {
    display: block;
    margin-top: 3px;
    color: #64786c;
    font-size: 10.5px;
}

.bp-history-item p {
    margin: 5px 0 0;
    color: #4c6456;
    font-size: 11.5px;
    line-height: 1.5;
}

/* FOOTER TOMBOL BATALKAN DRAWER */
.bp-drawer-actions {
    position: sticky;
    bottom: 0;
    margin: 0 -22px;
    padding: 14px 22px 18px;
    border-top: 1px solid rgba(22, 54, 38, .08);
    background: #ffffff;
    flex-shrink: 0;
}

.bp-drawer-actions .bp-btn {
    width: 100%;
    min-height: 42px;
    border-radius: 12px;
    font-size: 12.5px;
    font-weight: 700;
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
    .sipadu-booking-panel {
        top: 0;
        bottom: 0;
        width: 100vw;
        border-radius: 0;
    }

    .sipadu-booking-panel.is-open {
        right: 0;
    }

    .sipadu-booking-panel-header {
        padding: 15px 17px;
    }

    .sipadu-booking-panel-body {
        padding: 16px 16px 0;
    }

    .bp-drawer-actions {
        margin: 0 -16px;
        padding: 13px 16px 18px;
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
            <h1>Peminjaman Saya</h1>
            <p>Riwayat pengajuan, pantau jadwal pemakaian, dan cek catatan verifikasi admin untuk setiap permohonan.</p>
        </div>

        <a
            href="{{ route('borrower.booking.create') }}"
            class="bp-primary-cta"
        >
            <i data-lucide="plus"></i>
            <span>Ajukan Peminjaman</span>
        </a>
    </div>

    {{-- STATISTIK (5 SCORECARDS) --}}
    <div class="bp-stats">
        <div class="bp-stat" onclick="setBorrowerStatusTab('Pending')">
            <div class="bp-stat-icon is-warning">
                <i data-lucide="clock"></i>
            </div>
            <div>
                <small>Pending</small>
                <strong>{{ $stats['pending'] }}</strong>
            </div>
        </div>

        <div class="bp-stat" onclick="setBorrowerStatusTab('Disetujui')">
            <div class="bp-stat-icon">
                <i data-lucide="check-circle-2"></i>
            </div>
            <div>
                <small>Disetujui</small>
                <strong>{{ $stats['approved'] }}</strong>
            </div>
        </div>

        <div class="bp-stat" onclick="setBorrowerStatusTab('Ditolak')">
            <div class="bp-stat-icon is-danger">
                <i data-lucide="x-circle"></i>
            </div>
            <div>
                <small>Ditolak</small>
                <strong>{{ $stats['rejected'] }}</strong>
            </div>
        </div>

        <div class="bp-stat" onclick="setBorrowerStatusTab('')">
            <div class="bp-stat-icon">
                <i data-lucide="clipboard-list"></i>
            </div>
            <div>
                <small>Total Pengajuan</small>
                <strong>{{ $stats['total'] }}</strong>
            </div>
        </div>

        <div class="bp-stat" onclick="setBorrowerStatusTab('Perlu Revisi')">
            <div class="bp-stat-icon is-warning">
                <i data-lucide="rotate-ccw"></i>
            </div>
            <div>
                <small>Perlu Revisi</small>
                <strong>{{ $stats['revision'] }}</strong>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bp-alert is-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABEL RIWAYAT PEMINJAMAN --}}
    <div class="bp-card">

        {{-- FILTER DAN PENCARIAN --}}
        <div class="bp-toolbar">
            <div class="bp-search">
                <span class="bp-search-icon">
                    <i data-lucide="search"></i>
                </span>
                <input
                    type="text"
                    id="borrowerSearchInput"
                    placeholder="Cari kode, kegiatan, auditorium, atau organisasi..."
                    autocomplete="off"
                >
                <button
                    type="button"
                    class="bp-search-clear"
                    id="clearBorrowerSearch"
                    aria-label="Hapus pencarian"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            <select class="bp-filter" id="borrowerAuditoriumFilter" aria-label="Filter auditorium">
                <option value="">Semua Auditorium</option>
                @foreach($auditoriums as $auditorium)
                    <option value="{{ $auditorium->id }}">
                        {{ $auditorium->name }}
                    </option>
                @endforeach
            </select>

            <input
                class="bp-filter"
                type="date"
                id="borrowerDateFilter"
                aria-label="Filter tanggal"
            >

            <button type="button" class="bp-reset" id="resetBorrowerFilter">
                Reset
            </button>
        </div>

        {{-- TAB STATUS --}}
        <div class="bp-tabs" id="borrowerStatusTabs">
            <button type="button" class="bp-tab active" data-status-tab="" onclick="setBorrowerStatusTab('')">
                Semua <span>{{ $stats['total'] }}</span>
            </button>

            <button type="button" class="bp-tab" data-status-tab="Pending" onclick="setBorrowerStatusTab('Pending')">
                Pending <span>{{ $stats['pending'] }}</span>
            </button>

            <button type="button" class="bp-tab" data-status-tab="Disetujui" onclick="setBorrowerStatusTab('Disetujui')">
                Disetujui <span>{{ $stats['approved'] }}</span>
            </button>

            <button type="button" class="bp-tab" data-status-tab="Ditolak" onclick="setBorrowerStatusTab('Ditolak')">
                Ditolak <span>{{ $stats['rejected'] }}</span>
            </button>

            <button type="button" class="bp-tab" data-status-tab="Perlu Revisi" onclick="setBorrowerStatusTab('Perlu Revisi')">
                Perlu Revisi <span>{{ $stats['revision'] }}</span>
            </button>

            <button type="button" class="bp-tab" data-status-tab="Dibatalkan" onclick="setBorrowerStatusTab('Dibatalkan')">
                Dibatalkan <span>{{ $stats['cancelled'] }}</span>
            </button>
        </div>

        {{-- DAFTAR PENGAJUAN --}}
        <div class="bp-table-wrap">
            <table id="borrowerDataTable">
                <thead>
                    <tr>
                        <th style="width: 22%;">Pengajuan</th>
                        <th style="width: 26%;">Auditorium & Jadwal</th>
                        <th style="width: 13%;" class="dt-center">
                            <div style="width: 100%; text-align: center;">Status</div>
                        </th>
                        <th style="width: 21%;">Catatan Admin</th>
                        <th style="width: 18%;" class="dt-right">
                            <div style="width: 100%; text-align: right;">Aksi</div>
                        </th>
                    </tr>
                </thead>

                <tbody id="borrowerTableBody">
                @forelse($allUserBookings as $b)
                    @php
                        $statusClass = $statusClasses[$b->status] ?? 'st-cancelled';
                        $bookingCode = $formatBookingCode($b);
                        $feedbackItems = $collectFeedbacks($b);
                        $dateIso = $b->booking_date ? $b->booking_date->format('Y-m-d') : '';
                        $searchBlob = strtolower(implode(' ', [
                            $bookingCode,
                            $b->event_name,
                            $b->organization,
                            $b->auditorium->name ?? '',
                            $b->booking_date ? $b->booking_date->translatedFormat('d M Y') : '',
                            $b->status,
                        ]));
                    @endphp

                    <tr
                        class="borrower-booking-row"
                        data-booking-id="{{ $b->id }}"
                        data-status="{{ $b->status }}"
                        data-auditorium="{{ $b->auditorium_id }}"
                        data-date="{{ $dateIso }}"
                        data-search="{{ $searchBlob }}"
                    >
                        <td style="text-align: left;">
                            <span class="bp-code">{{ $bookingCode }}</span>
                            <span class="bp-sub">
                                {{ $b->created_at->format('d M Y, H:i') }}
                            </span>
                            <span class="bp-primary">{{ $b->event_name }}</span>
                            @if(!empty($b->organization))
                                <span class="bp-sub">{{ $b->organization }}</span>
                            @endif
                        </td>

                        <td style="text-align: left;">
                            <span class="bp-primary">{{ $b->auditorium->name ?? '-' }}</span>
                            <span class="bp-sub">
                                {{ $b->booking_date ? $b->booking_date->translatedFormat('d M Y') : '-' }}
                                · {{ substr((string) $b->start_time, 0, 5) }}–{{ substr((string) $b->end_time, 0, 5) }}
                            </span>
                        </td>

                        <td style="text-align: center;">
                            <span class="bp-status {{ $statusClass }}">
                                <span class="bp-status-dot"></span>
                                {{ $b->status === 'Pending' ? 'Pending' : $b->status }}
                            </span>
                        </td>

                        <td style="text-align: left;">
                            @if(count($feedbackItems) > 0)
                                <div class="bp-note-stack">
                                    @foreach($feedbackItems as $fb)
                                        <div class="bp-note-box {{ $fb['type'] }}">
                                            <strong>{{ $fb['title'] }}</strong>
                                            <p>{{ $fb['text'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="bp-sub">Belum ada catatan</span>
                            @endif
                        </td>

                        <td style="text-align: right;">
                            <div class="bp-action-row">
                                <button
                                    type="button"
                                    class="bp-btn bp-view"
                                    data-open-drawer="{{ $b->id }}"
                                    data-booking-subtitle="{{ $bookingCode }} • {{ $b->auditorium->name ?? '-' }}"
                                >
                                    <i data-lucide="eye"></i>
                                    Detail
                                </button>

                                @if($b->status === 'Pending')
                                    <form method="POST" action="{{ route('borrower.booking.cancel', $b) }}" style="margin:0;">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="bp-btn bp-reject"
                                            data-confirm="Batalkan pengajuan ini?"
                                        >
                                            <i data-lucide="x"></i>
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <div class="bp-empty">
                                <div class="bp-empty-icon">
                                    <i data-lucide="inbox"></i>
                                </div>
                                <strong>Belum ada pengajuan peminjaman</strong>
                                <div style="margin-top:5px">
                                    Klik tombol <strong>Ajukan Peminjaman</strong> untuk membuat permohonan baru.
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforelse

                @if($allUserBookings->isNotEmpty())
                    <tr id="borrowerFilterEmptyRow" style="display: none;">
                        <td colspan="5" style="text-align: center;">
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
                @endif
                </tbody>
            </table>
        </div>

        {{-- PAGINATION HALAMAN (MUNCUL OTOMATIS JIKA DATA > 10) --}}
        <div class="bp-pagination" id="borrowerPagination"></div>
    </div>

    {{-- TEMPLATE DRAWER DETAIL PENGAJUAN --}}
    <div id="bpDrawerTemplates">
        @foreach($allUserBookings as $item)
            @php
                $itemStatusClass = $statusClasses[$item->status] ?? 'st-cancelled';
                $itemCode = $formatBookingCode($item);
                $itemSession = $formatSessionRange($item);
                $itemFeedbacks = $collectFeedbacks($item);
            @endphp

            <template id="booking-drawer-{{ $item->id }}">
                <div class="bp-drawer-content">
                    {{-- KARTU UTAMA PENGAJUAN --}}
                    <div class="bp-drawer-summary {{ $item->status === 'Pending' ? 'is-pending' : '' }}">
                        <div class="bp-drawer-summary-top">
                            <span class="bp-code">
                                {{ $itemCode }} • {{ strtoupper($item->status === 'Pending' ? 'SEDANG DIPROSES' : $item->status) }}
                            </span>
                            <span class="bp-status {{ $itemStatusClass }}">
                                <span class="bp-status-dot"></span>
                                {{ $item->status === 'Pending' ? 'Pending' : $item->status }}
                            </span>
                        </div>

                        <h3>{{ $item->event_name }}</h3>
                        <p class="bp-drawer-date">
                            {{ $item->booking_date ? $item->booking_date->translatedFormat('d M Y') : '-' }} • {{ $itemSession }}
                        </p>

                        <div class="bp-drawer-tags">
                            @if($item->organization)
                                <span>#{{ preg_replace('/\s+/', '', $item->organization) }}</span>
                            @endif
                            @if($item->auditorium)
                                <span>#{{ preg_replace('/\s+/', '', $item->auditorium->name) }}</span>
                            @endif
                            @if($item->event_description)
                                <span>#Kegiatan</span>
                            @endif
                        </div>

                        <div class="bp-drawer-info-grid">
                            <div>
                                <small>Pengusul</small>
                                <strong>{{ $item->organization ?: '-' }}</strong>
                                <span>{{ $user->name }}</span>
                            </div>
                            <div>
                                <small>Auditorium</small>
                                <strong>{{ $item->auditorium->name ?? '-' }}</strong>
                            </div>
                            <div>
                                <small>Penanggung Jawab</small>
                                <strong>{{ $item->pic_name ?: '-' }}</strong>
                                <span>{{ $item->pic_phone ?: '' }}</span>
                            </div>
                            <div>
                                <small>Waktu Diajukan</small>
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
                                        <strong>Kelengkapan Perizinan:</strong> Surat Permohonan peminjaman auditorium telah dilampirkan.
                                    </p>
                                @else
                                    <p>
                                        <strong>Kelengkapan Perizinan:</strong> Dokumen surat permohonan belum terlampir.
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

                        {{-- CATATAN ADMIN (MUNCUL SEMUA: REVISI & BENTROK/PENOLAKAN) --}}
                        <div style="margin-top: 14px;">
                            <small style="display:block; margin-bottom:6px; color:#6b7f73; font-size:11px; font-weight:700;">
                                Catatan dari Admin
                            </small>

                            @if(count($itemFeedbacks) > 0)
                                <div class="bp-note-stack" style="max-width: 100%;">
                                    @foreach($itemFeedbacks as $fb)
                                        <div class="bp-note-box {{ $fb['type'] }}">
                                            <strong>{{ $fb['title'] }}</strong>
                                            <p>{{ $fb['text'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="bp-drawer-description" style="margin-top:0;">
                                    <p>Belum ada catatan dari admin.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- RIWAYAT KEPUTUSAN --}}
                    @if($item->histories->isNotEmpty())
                        <div class="bp-drawer-history">
                            <strong>Riwayat Status & Keputusan</strong>
                            @foreach($item->histories->sortByDesc('created_at') as $history)
                                <div class="bp-history-item">
                                    <span>{{ $history->from_status ?: 'Pengajuan' }} → {{ $history->to_status }}</span>
                                    <small>{{ $history->created_at->format('d/m/Y H:i') }}</small>
                                    @if($history->note)
                                        <p>{{ str_replace('[Ditolak otomatis karena bentrok]', '[Ditolak karena bentrok]', $history->note) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- FOOTER TOMBOL BATALKAN JIKA MASIH PENDING --}}
                @if($item->status === 'Pending')
                    <div class="bp-drawer-actions">
                        <form method="POST" action="{{ route('borrower.booking.cancel', $item) }}" style="margin:0;">
                            @csrf
                            @method('PATCH')
                            <button
                                type="submit"
                                class="bp-btn bp-reject"
                                data-confirm="Batalkan pengajuan ini?"
                            >
                                Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                @endif
            </template>
        @endforeach
    </div>
</div>

{{-- PANEL DETAIL DARI SISI KANAN --}}
<div class="sipadu-booking-overlay" id="bookingOverlay"></div>
<aside class="sipadu-booking-panel" id="bookingPanel" role="dialog" aria-modal="true" aria-labelledby="bookingDrawerTitle">
    <header class="sipadu-booking-panel-header">
        <div>
            <h2 id="bookingDrawerTitle">Detail Pengajuan Peminjaman</h2>
            <p id="bookingDrawerSubtitle">Rincian jadwal, kelengkapan dokumen, dan catatan verifikasi.</p>
        </div>
        <button type="button" class="sipadu-booking-panel-close" id="closeBookingDrawer" aria-label="Tutup">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
            </svg>
        </button>
    </header>
    <div class="sipadu-booking-panel-body" id="bookingDrawerBody"></div>
</aside>

<script>
    let borrowerStatusTab = '';
    let borrowerPage = 1;
    const borrowerPerPage = 10;

    function openBookingDrawer(id, button) {
        const template = document.getElementById('booking-drawer-' + id);
        const overlay  = document.getElementById('bookingOverlay');
        const panel    = document.getElementById('bookingPanel');
        const body     = document.getElementById('bookingDrawerBody');
        const subtitle = document.getElementById('bookingDrawerSubtitle');

        if (!template || !overlay || !panel || !body) return;

        body.innerHTML = template.innerHTML;
        body.scrollTop = 0;
        subtitle.textContent = (button && button.dataset.bookingSubtitle)
            ? button.dataset.bookingSubtitle
            : 'Rincian jadwal, kelengkapan dokumen, dan catatan verifikasi.';

        if (window.lucide) {
            window.lucide.createIcons();
        }

        overlay.classList.add('is-open');
        panel.classList.add('is-open');

        const pageBody = document.body;
        if (!pageBody.classList.contains('bp-drawer-open')) {
            pageBody.dataset.bpOriginalPaddingRight = pageBody.style.paddingRight;
            const scrollbarWidth = Math.max(0, window.innerWidth - document.documentElement.clientWidth);
            const currentPaddingRight = parseFloat(window.getComputedStyle(pageBody).paddingRight) || 0;
            pageBody.style.paddingRight = (currentPaddingRight + scrollbarWidth) + 'px';
            pageBody.classList.add('bp-drawer-open');
        }
    }

    function closeBookingDrawer() {
        const overlay  = document.getElementById('bookingOverlay');
        const panel    = document.getElementById('bookingPanel');
        const pageBody = document.body;

        overlay?.classList.remove('is-open');
        panel?.classList.remove('is-open');

        if (pageBody.classList.contains('bp-drawer-open')) {
            pageBody.classList.remove('bp-drawer-open');
            pageBody.style.paddingRight = pageBody.dataset.bpOriginalPaddingRight || '';
            delete pageBody.dataset.bpOriginalPaddingRight;
        }
    }

    function setBorrowerStatusTab(status) {
        borrowerStatusTab = status;
        borrowerPage = 1;

        document.querySelectorAll('#borrowerStatusTabs .bp-tab').forEach(tab => {
            tab.classList.toggle('active', tab.dataset.statusTab === status);
        });

        renderBorrowerBookings();
    }

    function renderBorrowerBookings() {
        const rows      = Array.from(document.querySelectorAll('.borrower-booking-row'));
        const emptyRow  = document.getElementById('borrowerFilterEmptyRow');
        const buttonsEl = document.getElementById('borrowerPagination');

        if (rows.length === 0) {
            if (buttonsEl) {
                buttonsEl.innerHTML = '';
                buttonsEl.classList.remove('has-pages');
            }
            return;
        }

        const q          = (document.getElementById('borrowerSearchInput')?.value || '').trim().toLowerCase();
        const auditorium = (document.getElementById('borrowerAuditoriumFilter')?.value || '').trim();
        const dateVal    = (document.getElementById('borrowerDateFilter')?.value || '').trim();

        const matched = rows.filter(row => {
            if (borrowerStatusTab && row.dataset.status !== borrowerStatusTab) return false;
            if (auditorium && row.dataset.auditorium !== auditorium) return false;
            if (dateVal && row.dataset.date !== dateVal) return false;
            if (q && !(row.dataset.search || '').includes(q)) return false;
            return true;
        });

        const total      = matched.length;
        const totalPages = Math.max(1, Math.ceil(total / borrowerPerPage));
        if (borrowerPage > totalPages) borrowerPage = totalPages;

        const start = (borrowerPage - 1) * borrowerPerPage;
        const end   = start + borrowerPerPage;

        rows.forEach(r => { r.style.display = 'none'; });
        matched.slice(start, end).forEach(r => { r.style.display = ''; });

        if (emptyRow) {
            emptyRow.style.display = total === 0 ? '' : 'none';
        }

        if (buttonsEl) {
            buttonsEl.innerHTML = '';
            if (total > borrowerPerPage) {
                buttonsEl.classList.add('has-pages');

                const prev = document.createElement('button');
                prev.type = 'button';
                prev.className = 'bp-page-btn';
                prev.textContent = '‹';
                prev.disabled = borrowerPage === 1;
                prev.onclick = () => { borrowerPage--; renderBorrowerBookings(); };
                buttonsEl.appendChild(prev);

                for (let p = 1; p <= totalPages; p++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'bp-page-btn' + (p === borrowerPage ? ' active' : '');
                    btn.textContent = String(p);
                    btn.onclick = () => { borrowerPage = p; renderBorrowerBookings(); };
                    buttonsEl.appendChild(btn);
                }

                const next = document.createElement('button');
                next.type = 'button';
                next.className = 'bp-page-btn';
                next.textContent = '›';
                next.disabled = borrowerPage === totalPages;
                next.onclick = () => { borrowerPage++; renderBorrowerBookings(); };
                buttonsEl.appendChild(next);
            } else {
                buttonsEl.classList.remove('has-pages');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            window.lucide.createIcons();
        }

        const searchInput = document.getElementById('borrowerSearchInput');
        const clearBtn    = document.getElementById('clearBorrowerSearch');
        const audSelect   = document.getElementById('borrowerAuditoriumFilter');
        const dateInput   = document.getElementById('borrowerDateFilter');
        const resetBtn    = document.getElementById('resetBorrowerFilter');

        function toggleClear() {
            if (!clearBtn || !searchInput) return;
            clearBtn.classList.toggle('show', searchInput.value.trim().length > 0);
        }

        searchInput?.addEventListener('input', function () {
            toggleClear();
            borrowerPage = 1;
            renderBorrowerBookings();
        });

        clearBtn?.addEventListener('click', function () {
            searchInput.value = '';
            toggleClear();
            searchInput.focus();
            borrowerPage = 1;
            renderBorrowerBookings();
        });

        audSelect?.addEventListener('change', function () {
            borrowerPage = 1;
            renderBorrowerBookings();
        });

        dateInput?.addEventListener('change', function () {
            borrowerPage = 1;
            renderBorrowerBookings();
        });

        resetBtn?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (audSelect) audSelect.value = '';
            if (dateInput) dateInput.value = '';
            toggleClear();
            setBorrowerStatusTab('');
        });

        renderBorrowerBookings();

        // Buka otomatis detail pengajuan spesifik jika diakses dari Dashboard (?booking_id=...)
        const urlParams = new URLSearchParams(window.location.search);
        const targetBookingId = urlParams.get('booking_id');

        if (targetBookingId) {
            const targetBtn = document.querySelector(`[data-open-drawer="${CSS.escape(targetBookingId)}"]`);
            if (targetBtn) {
                const targetRow = targetBtn.closest('tr');
                if (targetRow) {
                    const allRows = Array.from(document.querySelectorAll('.borrower-booking-row'));
                    const rowIndex = allRows.indexOf(targetRow);
                    if (rowIndex >= 0) {
                        borrowerPage = Math.floor(rowIndex / borrowerPerPage) + 1;
                        renderBorrowerBookings();
                    }
                    targetRow.style.backgroundColor = 'rgba(31, 122, 73, 0.12)';
                    targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                openBookingDrawer(targetBookingId, targetBtn);

                // Bersihkan query booking_id dari URL setelah dibuka
                urlParams.delete('booking_id');
                const cleanUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, '', cleanUrl);
            }
        }
    });

    document.addEventListener('click', function (event) {
        const detailButton = event.target.closest('[data-open-drawer]');
        if (detailButton) {
            openBookingDrawer(detailButton.dataset.openDrawer, detailButton);
            return;
        }

        if (event.target.closest('#closeBookingDrawer') || event.target.id === 'bookingOverlay') {
            closeBookingDrawer();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeBookingDrawer();
        }
    });
</script>
@endsection