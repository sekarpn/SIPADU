@extends('layouts.panel')

@section('panel')

@php
    $isPegawai = request()->routeIs('pegawai.*');
    $createRoute = $isPegawai ? route('pegawai.booking.create') : route('borrower.booking.create');

    // Ambil data Konten Informasi yang dikelola Admin (/admin/konten)
    $dbContents = collect();
    foreach ([\App\Models\GuideContent::class, \App\Models\Content::class, \App\Models\InformationContent::class] as $modelClass) {
        if (class_exists($modelClass)) {
            try {
                $dbContents = $modelClass::all();
                break;
            } catch (\Throwable $e) {
                $dbContents = collect();
            }
        }
    }

    $findContentByType = function (array $keywords) use ($dbContents) {
        return $dbContents->first(function ($item) use ($keywords) {
            $type  = strtolower(trim((string) ($item->type ?? $item->category ?? $item->section ?? $item->key ?? '')));
            $title = strtolower(trim((string) ($item->title ?? '')));
            foreach ($keywords as $kw) {
                if ($type === $kw || str_contains($type, $kw) || str_contains($title, $kw)) {
                    return true;
                }
            }
            return false;
        });
    };

    $alurRow    = $findContentByType(['alur']);
    $sopRow     = $findContentByType(['sop']);
    $faqRow     = $findContentByType(['faq']);
    $contactRow = $findContentByType(['contact', 'kontak']);

    // 1. PARSING ALUR PENGAJUAN (Memecah "1. ... 2. ... 3. ... 4. ...")
    $defaultFlows = [
        [
            'code'  => 'TAHAP 01',
            'title' => 'Pengajuan ke Fakultas',
            'desc'  => 'Himpunan, BEM Fakultas, atau KSM mengajukan proposal dan surat peminjaman kepada pihak fakultas.',
            'actor' => 'Himpunan / BEM / KSM',
        ],
        [
            'code'  => 'TAHAP 02',
            'title' => 'Disposisi WaDek II',
            'desc'  => 'Fakultas membuat permohonan peminjaman tempat dan sarana prasarana yang ditandatangani Dekanat (WaDek II).',
            'actor' => 'Dekanat (WaDek II)',
        ],
        [
            'code'  => 'TAHAP 03',
            'title' => 'Verifikasi Ketersediaan',
            'desc'  => 'Universitas memeriksa apakah ruangan tersedia atau telah digunakan pihak lain yang lebih dahulu mengajukan.',
            'actor' => 'Pengelola Universitas',
        ],
        [
            'code'  => 'TAHAP 04',
            'title' => 'Persetujuan Peminjaman',
            'desc'  => 'Jika ruangan tersedia dan perizinan lengkap, peminjaman dapat disetujui oleh pengelola.',
            'actor' => 'Status Disetujui',
        ],
    ];

    $flowSteps = $defaultFlows;
    $alurRaw = trim((string) ($alurRow->content ?? $alurRow->body ?? $alurRow->description ?? ''));
    if ($alurRaw !== '') {
        $parsedSteps = preg_split('/(?:^|\s+)(?=\d+\.\s)/', $alurRaw, -1, PREG_SPLIT_NO_EMPTY);
        $parsedSteps = array_values(array_filter(array_map('trim', $parsedSteps)));

        if (count($parsedSteps) >= 2) {
            $flowSteps = [];
            foreach ($parsedSteps as $idx => $stepText) {
                $cleanText = preg_replace('/^\d+\.\s*/', '', $stepText);
                $preset = $defaultFlows[$idx] ?? [
                    'code'  => 'TAHAP ' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
                    'title' => 'Tahap ' . ($idx + 1),
                    'actor' => 'Pemohon / Pengelola',
                ];

                $stepTitle = $preset['title'];
                $stepDesc  = $cleanText;

                if (str_contains($cleanText, ':')) {
                    [$customTitle, $customDesc] = explode(':', $cleanText, 2);
                    if (mb_strlen(trim($customTitle)) <= 45 && trim($customDesc) !== '') {
                        $stepTitle = trim($customTitle);
                        $stepDesc  = trim($customDesc);
                    }
                }

                $flowSteps[] = [
                    'code'  => 'TAHAP ' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT),
                    'title' => $stepTitle,
                    'desc'  => $stepDesc,
                    'actor' => $preset['actor'],
                ];
            }
        }
    }

    // 2. PARSING SOP & JALUR UKM
    $defaultSopRules = [
        [
            'title'     => 'Agenda Pihak Rektorat',
            'badge'     => 'Prioritas Utama',
            'is_solid'  => true,
            'desc'      => 'Jika ada acara yang jadwalnya bersamaan dengan pihak rektorat, pihak rektorat didahulukan.',
        ],
        [
            'title'     => 'Bentrok Jadwal BEM & Himpunan',
            'badge'     => 'Penilaian Urgensi',
            'is_solid'  => false,
            'desc'      => 'Jika jadwal BEM dan Himpunan bersamaan, akan dilihat tingkat urgensi dan kepentingannya yang ditentukan oleh Kepala Biro Umum & Keuangan atas nama Rektor.',
        ],
        [
            'title'     => 'Acara Eksternal vs Internal UPN',
            'badge'     => 'Tinjauan Tujuan',
            'is_solid'  => false,
            'desc'      => 'Jika terdapat acara eksternal dan internal UPN yang jadwalnya bersamaan, akan dilihat tujuan masing-masing acara dan apakah narasumber berasal dari luar.',
        ],
    ];

    $ukmTrackDesc = 'UKM tingkat universitas dapat mengajukan proposal dan surat peminjaman ruangan langsung ke KoorBid Kemahasiswaan.';
    $sopRules = $defaultSopRules;

    $sopRaw = trim((string) ($sopRow->content ?? $sopRow->body ?? $sopRow->description ?? ''));
    if ($sopRaw !== '') {
        // Pisahkan kalimat jika diawali nomor (1., 2.) atau per baris / titik
        $rawItems = preg_match('/(?:^|\s)\d+\.\s/', $sopRaw)
            ? preg_split('/(?:^|\s+)(?=\d+\.\s)/', $sopRaw, -1, PREG_SPLIT_NO_EMPTY)
            : preg_split('/\r\n|\r|\n|(?<=\.)\s+(?=[A-Z])/', $sopRaw, -1, PREG_SPLIT_NO_EMPTY);

        $filteredRules = [];
        foreach ($rawItems as $line) {
            $line = trim(preg_replace('/^\d+\.\s*/', '', $line));
            if ($line === '') continue;

            // Jika baris membahas jalur UKM ke KoorBid Kemahasiswaan, masukkan ke switcher kanan
            if (stripos($line, 'ukm') !== false && stripos($line, 'koorbid') !== false) {
                $ukmTrackDesc = $line;
                continue;
            }

            $ruleTitle = 'Ketentuan Prioritas ' . str_pad(count($filteredRules) + 1, 2, '0', STR_PAD_LEFT);
            $ruleBadge = count($filteredRules) === 0 ? 'Prioritas Utama' : 'Ketentuan SOP';
            $isSolid   = count($filteredRules) === 0;
            $ruleDesc  = $line;

            if (str_contains($line, ':')) {
                [$t, $d] = explode(':', $line, 2);
                if (mb_strlen(trim($t)) <= 50 && trim($d) !== '') {
                    $ruleTitle = trim($t);
                    $ruleDesc  = trim($d);
                }
            } elseif (stripos($line, 'rektorat') !== false) {
                $ruleTitle = 'Agenda Pihak Rektorat';
                $ruleBadge = 'Prioritas Utama';
                $isSolid   = true;
            } elseif (stripos($line, 'bem') !== false || stripos($line, 'himpunan') !== false) {
                $ruleTitle = 'Bentrok Jadwal BEM & Himpunan';
                $ruleBadge = 'Penilaian Urgensi';
                $isSolid   = false;
            } elseif (stripos($line, 'eksternal') !== false) {
                $ruleTitle = 'Acara Eksternal vs Internal UPN';
                $ruleBadge = 'Tinjauan Tujuan';
                $isSolid   = false;
            }

            $filteredRules[] = [
                'title'    => $ruleTitle,
                'badge'    => $ruleBadge,
                'is_solid' => $isSolid,
                'desc'     => $ruleDesc,
            ];
        }

        if (count($filteredRules) > 0) {
            $sopRules = $filteredRules;
        }
    }

    // 3. PARSING FAQ (Memecah pasangan "Pertanyaan? Jawaban.")
    $defaultFaqs = [
        [
            'question' => 'Apakah pengajuan langsung disetujui setelah dikirim?',
            'answer'   => 'Tidak. Setiap pengajuan akan diperiksa terlebih dahulu oleh Admin/pengelola auditorium untuk memastikan kelengkapan surat perizinan serta ketersediaan jadwal ruangan.',
        ],
        [
            'question' => 'Bagaimana cara mengetahui jadwal auditorium yang tersedia?',
            'answer'   => 'Anda dapat melihat jadwal yang sudah disetujui pada panel Peminjaman Mendatang di Beranda, atau langsung memilih auditorium dan tanggal pada formulir Ajukan Peminjaman (sistem otomatis mendeteksi bentrok jam).',
        ],
        [
            'question' => 'Dokumen apa yang wajib diunggah saat mengajukan peminjaman?',
            'answer'   => 'Surat permohonan peminjaman ruangan dalam format PDF yang telah disahkan oleh WaDek II (untuk Ormawa Fakultas) atau KoorBid Kemahasiswaan (untuk UKM).',
        ],
        [
            'question' => 'Bagaimana jika status pengajuan saya Perlu Revisi atau Ditolak?',
            'answer'   => 'Buka menu Peminjaman Saya lalu klik pengajuan terkait untuk membaca catatan alasan dari Admin, kemudian lengkapi berkas perbaikan yang diminta atau ajukan ulang.',
        ],
        [
            'question' => 'Siapa yang diprioritaskan jika jadwal kegiatan bentrok di hari yang sama?',
            'answer'   => 'Agenda pihak rektorat didahulukan. Untuk bentrok antar organisasi mahasiswa, prioritas ditentukan berdasarkan tingkat urgensi acara oleh Kepala Biro Umum & Keuangan atas nama Rektor.',
        ],
    ];

    $faqItems = $defaultFaqs;
    $faqRaw = trim((string) ($faqRow->content ?? $faqRow->body ?? $faqRow->description ?? ''));
    if ($faqRaw !== '') {
        // Deteksi pola "Kalimat tanya? Kalimat jawaban."
        preg_match_all('/([^?]+\?)\s*([^?]+(?=(?:\s+[^?]+\?|$)))/u', $faqRaw, $matches, PREG_SET_ORDER);
        if (count($matches) > 0) {
            $parsedFaqs = [];
            foreach ($matches as $m) {
                $q = trim(preg_replace('/^\d+\.\s*/', '', $m[1]));
                $a = trim($m[2]);
                if ($q !== '' && $a !== '') {
                    $parsedFaqs[] = [
                        'question' => $q,
                        'answer'   => $a,
                    ];
                }
            }
            if (count($parsedFaqs) > 0) {
                $faqItems = $parsedFaqs;
            }
        }
    }

    // 4. CONTACT PERSON DARI DATABASE ADMIN
    $contactText = trim((string) ($contactRow->content ?? $contactRow->body ?? $contactRow->description ?? ''));
    if ($contactText === '') {
        $contactText = 'Narahubung gedung diperbarui oleh Admin';
    }
@endphp

<style>
    /* PALET WARNA RESMI TEMA SIPADU (SERAGAM DENGAN BERANDA) */
    :root {
        --sipadu-primary: #237c4d;
        --sipadu-primary-hover: #1a633c;
        --sipadu-primary-soft: rgba(35, 124, 77, 0.14);
        --sipadu-primary-tint: #eaf5ed;
        --sipadu-ink: #163626;
        --sipadu-text: #324a3c;
        --sipadu-muted: #4c6456;
    }

    body,
    .main-wrapper,
    .content-wrapper,
    main {
        background-color: #b5d6c5 !important;
        background-image: linear-gradient(135deg, #cfe6da 0%, #b5d6c5 52%, #a2c9b4 100%) !important;
        background-attachment: fixed !important;
        min-height: 100vh;
    }

    html,
    body {
        max-width: 100%;
        overflow-x: clip !important;
        scrollbar-width: thin;
        scrollbar-color: rgba(22, 54, 38, 0.18) transparent;
    }

    .guides-shell {
        width: 100%;
        padding: 10px 10px 44px;
        box-sizing: border-box;
    }

    /* TOPBAR */
    .guides-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
        flex-wrap: wrap;
    }

    .guides-hero-copy h1 {
        font-size: 27px;
        font-weight: 800;
        color: var(--sipadu-ink);
        margin: 0 0 5px;
        letter-spacing: -0.025em;
    }

    .guides-hero-copy p {
        font-size: 13px;
        color: var(--sipadu-text);
        margin: 0;
    }

    /* TOMBOL AJUKAN PEMINJAMAN (#237c4d) */
    .dashboard-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 18px;
        border-radius: 10px;
        background: var(--sipadu-primary);
        color: #ffffff;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid var(--sipadu-primary);
        box-shadow: 0 4px 14px rgba(35, 124, 77, 0.25);
        transition: .2s ease;
        white-space: nowrap;
    }

    .dashboard-action:hover {
        background: var(--sipadu-primary-hover);
        border-color: var(--sipadu-primary-hover);
        color: #ffffff;
        transform: translateY(-2px);
    }

    .dashboard-action svg {
        width: 16px;
        height: 16px;
    }

    /* GLASS PANEL */
    .glass-surface {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.58) 0%, rgba(255, 255, 255, 0.30) 100%);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 18px;
        padding: 20px 22px;
        box-shadow: 0 10px 28px rgba(22, 54, 38, 0.05);
        box-sizing: border-box;
    }

    .surface-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .surface-title-group {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .surface-badge-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: var(--sipadu-primary-soft);
        color: var(--sipadu-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .surface-badge-icon svg {
        width: 18px;
        height: 18px;
    }

    .surface-title-group h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--sipadu-ink);
        letter-spacing: -0.02em;
    }

    .surface-title-group p {
        margin: 2px 0 0;
        font-size: 11.8px;
        color: var(--sipadu-muted);
    }

    /* 1. ALUR PENGAJUAN (LANGSUNG TERBUKA PENUH, TINGGI SAMA RATA) */
    .flow-circuit-panel {
        margin-bottom: 18px;
    }

    .flow-track-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        align-items: stretch;
    }

    .flow-node {
        position: relative;
        text-align: left;
        height: 100%;
        border: 1px solid rgba(35, 124, 77, 0.16);
        background: rgba(255, 255, 255, 0.88);
        border-radius: 14px;
        padding: 15px 16px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-sizing: border-box;
    }

    .flow-node-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 10px;
    }

    .flow-step-code {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.06em;
        padding: 3px 9px;
        border-radius: 999px;
        background: var(--sipadu-primary);
        color: #ffffff;
        font-variant-numeric: tabular-nums;
    }

    .flow-node-title {
        font-size: 14px;
        font-weight: 800;
        color: var(--sipadu-ink);
        line-height: 1.35;
        margin-bottom: 9px;
    }

    .flow-node-desc {
        margin: 0;
        padding-top: 9px;
        border-top: 1px solid rgba(22, 54, 38, 0.09);
        font-size: 12px;
        line-height: 1.55;
        color: var(--sipadu-text);
    }

    .flow-node-actor {
        display: inline-block;
        width: fit-content;
        margin-top: 12px;
        padding: 4px 10px;
        border-radius: 7px;
        background: var(--sipadu-primary-tint);
        border: 1px solid rgba(35, 124, 77, 0.22);
        color: var(--sipadu-primary);
        font-size: 10.5px;
        font-weight: 700;
    }

    /* 2. SOP & JALUR FAKULTAS: UNIFIED POLICY LEDGER */
    .policy-ledger-shell {
        margin-bottom: 20px;
        padding: 0;
        overflow: hidden;
    }

    .policy-ledger-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.32fr) minmax(290px, 0.88fr);
        align-items: stretch;
    }

    /* KOLOM KIRI LEDGER: DAFTAR ATURAN PRIORITAS */
    .ledger-rules-pane {
        padding: 22px 24px;
    }

    .ledger-rule-list {
        display: flex;
        flex-direction: column;
    }

    .ledger-rule-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: 10px;
        align-items: flex-start;
        padding: 14px 0;
        border-bottom: 1px solid rgba(22, 54, 38, 0.09);
    }

    .ledger-rule-item:first-child {
        padding-top: 4px;
    }

    .ledger-rule-item:last-child {
        border-bottom: none;
        padding-bottom: 2px;
    }

    .ledger-index {
        min-width: 24px;
        font-size: 18px;
        font-weight: 800;
        line-height: 1.1;
        color: var(--sipadu-primary);
        font-variant-numeric: tabular-nums;
        padding-top: 2px;
    }

    .ledger-rule-head {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }

    .ledger-rule-head strong {
        font-size: 13.8px;
        font-weight: 800;
        color: var(--sipadu-ink);
    }

    .ledger-chip {
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        background: var(--sipadu-primary-tint);
        color: var(--sipadu-primary);
        border: 1px solid rgba(35, 124, 77, 0.24);
    }

    .ledger-chip.is-solid {
        background: var(--sipadu-primary);
        color: #ffffff;
        border-color: var(--sipadu-primary);
    }

    .ledger-rule-item p {
        margin: 0;
        font-size: 12.3px;
        line-height: 1.58;
        color: var(--sipadu-text);
    }

    /* KOLOM KANAN LEDGER: INTERACTIVE TRACK SWITCHER & DOKUMEN FAKULTAS */
    .ledger-track-pane {
        padding: 22px 24px;
        background: rgba(255, 255, 255, 0.62);
        border-left: 1px solid rgba(22, 54, 38, 0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 16px;
    }

    .track-pane-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--sipadu-primary);
        margin-bottom: 6px;
    }

    .track-pane-eyebrow svg {
        width: 14px;
        height: 14px;
    }

    .ledger-track-pane h3 {
        margin: 0 0 12px;
        font-size: 17px;
        font-weight: 800;
        color: var(--sipadu-ink);
        line-height: 1.3;
    }

    .track-switcher {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
        padding: 4px;
        border-radius: 11px;
        background: rgba(22, 54, 38, 0.07);
        margin-bottom: 12px;
    }

    .track-tab-btn {
        border: none;
        border-radius: 8px;
        padding: 8px 10px;
        background: transparent;
        color: var(--sipadu-text);
        font-family: inherit;
        font-size: 11.8px;
        font-weight: 700;
        cursor: pointer;
        transition: background .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .track-tab-btn.is-active {
        background: var(--sipadu-primary);
        color: #ffffff;
        box-shadow: 0 3px 10px rgba(35, 124, 77, 0.22);
    }

    .track-detail-box {
        padding: 13px 14px;
        border-radius: 12px;
        background: #ffffff;
        border: 1px solid rgba(35, 124, 77, 0.18);
    }

    .track-detail-box strong {
        display: block;
        font-size: 12.8px;
        font-weight: 800;
        color: var(--sipadu-ink);
        margin-bottom: 4px;
    }

    .track-detail-box p {
        margin: 0;
        font-size: 12px;
        line-height: 1.55;
        color: var(--sipadu-text);
    }

    .portal-doc-box {
        padding-top: 14px;
        border-top: 1px dashed rgba(22, 54, 38, 0.16);
    }

    .portal-doc-caption {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11.3px;
        font-weight: 600;
        color: var(--sipadu-muted);
        margin-bottom: 10px;
    }

    .portal-doc-caption svg {
        width: 14px;
        height: 14px;
        color: var(--sipadu-primary);
        flex-shrink: 0;
    }

    .portal-open-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        box-sizing: border-box;
        padding: 11px 14px;
        border-radius: 10px;
        background: var(--sipadu-primary);
        color: #ffffff;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 700;
        border: 1px solid var(--sipadu-primary);
        box-shadow: 0 4px 12px rgba(35, 124, 77, 0.22);
        transition: background .18s ease, transform .18s ease;
    }

    .portal-open-btn:hover {
        background: var(--sipadu-primary-hover);
        color: #ffffff;
        transform: translateY(-1px);
    }

    .portal-open-btn svg {
        width: 15px;
        height: 15px;
    }

    /* 3. SECTION FAQ + CONTACT PERSON (EDITORIAL SPLIT LAYOUT) */
    .faq-editorial-section {
        display: grid;
        grid-template-columns: minmax(270px, 0.78fr) minmax(0, 1.45fr);
        gap: 24px;
        align-items: start;
        min-height: 460px;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.56) 0%, rgba(255, 255, 255, 0.28) 100%);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 20px;
        padding: 26px;
        box-shadow: 0 10px 28px rgba(22, 54, 38, 0.05);
    }

    .faq-left-column {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .faq-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--sipadu-text);
        margin-bottom: 6px;
    }

    .faq-eyebrow-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--sipadu-primary);
        box-shadow: 0 0 0 3px rgba(35, 124, 77, 0.18);
    }

    .faq-big-title {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.16;
        letter-spacing: -0.03em;
        color: var(--sipadu-ink);
    }

    .faq-search-bar {
        position: relative;
        width: 100%;
    }

    .faq-search-bar input {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 14px 10px 36px;
        border-radius: 12px;
        border: 1px solid rgba(22, 54, 38, 0.12);
        background: rgba(255, 255, 255, 0.92);
        font-family: inherit;
        font-size: 12.5px;
        color: var(--sipadu-ink);
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .faq-search-bar input:focus {
        border-color: var(--sipadu-primary);
        box-shadow: 0 0 0 3px rgba(35, 124, 77, 0.14);
    }

    .faq-search-bar svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 15px;
        height: 15px;
        color: var(--sipadu-muted);
        pointer-events: none;
    }

    /* KARTU CONTACT PERSON (#237c4d) */
    .cp-callout-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(22, 54, 38, 0.08);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 24px rgba(22, 54, 38, 0.05);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .cp-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .cp-avatar-wrap {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        background: var(--sipadu-primary);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 6px 14px rgba(35, 124, 77, 0.22);
    }

    .cp-avatar-wrap svg {
        width: 21px;
        height: 21px;
    }

    .cp-card-header h3 {
        margin: 0;
        font-size: 16.5px;
        font-weight: 800;
        color: var(--sipadu-ink);
        letter-spacing: -0.015em;
    }

    .cp-card-header span {
        display: block;
        margin-top: 2px;
        font-size: 11.3px;
        font-weight: 700;
        color: var(--sipadu-primary);
    }

    .cp-callout-card p {
        margin: 0;
        font-size: 12.2px;
        line-height: 1.55;
        color: var(--sipadu-muted);
    }

    .cp-meta-pills {
        display: flex;
        flex-direction: column;
        gap: 7px;
        padding-top: 2px;
    }

    .cp-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11.8px;
        font-weight: 600;
        color: var(--sipadu-text);
    }

    .cp-meta-item svg {
        width: 14px;
        height: 14px;
        color: var(--sipadu-primary);
        flex-shrink: 0;
    }

    .cp-status-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        width: 100%;
        box-sizing: border-box;
        padding: 10px 14px;
        border-radius: 11px;
        background: var(--sipadu-primary-tint);
        border: 1px solid rgba(35, 124, 77, 0.24);
        color: var(--sipadu-primary);
        font-size: 11.8px;
        font-weight: 700;
        text-align: center;
        line-height: 1.4;
    }

    .cp-status-pill svg {
        width: 14px;
        height: 14px;
        color: var(--sipadu-primary);
        flex-shrink: 0;
    }

    /* KOLOM KANAN FAQ: SINGLE-OPEN ACCORDION DENGAN PANAH TANPA BACKGROUND BULET */
    .faq-pill-stack {
        display: flex;
        flex-direction: column;
        gap: 11px;
    }

    .faq-pill-card {
        width: 100%;
        text-align: left;
        padding: 17px 20px;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.90);
        border: 1px solid rgba(22, 54, 38, 0.07);
        box-shadow: 0 3px 12px rgba(22, 54, 38, 0.03);
        cursor: pointer;
        font-family: inherit;
        transition: background .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    .faq-pill-card:hover:not(.is-pinned) {
        background: rgba(255, 255, 255, 0.98);
        border-color: rgba(35, 124, 77, 0.22);
    }

    .faq-pill-card.is-pinned {
        background: #ffffff;
        border-color: rgba(35, 124, 77, 0.34);
        box-shadow: 0 10px 24px rgba(22, 54, 38, 0.07);
    }

    .faq-pill-card:focus-visible {
        outline: 2px solid var(--sipadu-primary);
        outline-offset: 2px;
    }

    .faq-pill-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .faq-pill-question {
        font-size: 14.5px;
        font-weight: 750;
        color: var(--sipadu-ink);
        line-height: 1.4;
        letter-spacing: -0.01em;
    }

    .faq-pill-symbol {
        width: 24px;
        height: 24px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--sipadu-primary);
        background: transparent !important;
        transition: transform .24s ease, color .2s ease;
    }

    .faq-pill-symbol svg {
        width: 17px;
        height: 17px;
    }

    .faq-pill-card.is-pinned .faq-pill-symbol {
        transform: rotate(180deg);
        background: transparent !important;
        color: var(--sipadu-primary);
    }

    .faq-pill-reveal {
        display: grid;
        grid-template-rows: 0fr;
        opacity: 0;
        margin-top: 0;
        transition: grid-template-rows .28s cubic-bezier(0.22, 1, 0.36, 1), opacity .22s ease, margin-top .25s ease;
    }

    .faq-pill-card.is-pinned .faq-pill-reveal {
        grid-template-rows: 1fr;
        opacity: 1;
        margin-top: 10px;
    }

    .faq-pill-reveal-inner {
        overflow: hidden;
    }

    .faq-pill-answer {
        margin: 0;
        font-size: 12.8px;
        line-height: 1.65;
        color: var(--sipadu-muted);
        padding-right: 24px;
    }

    .faq-empty-msg {
        display: none;
        text-align: center;
        padding: 26px 16px;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.75);
        border: 1px dashed rgba(22, 54, 38, 0.2);
        font-size: 12.5px;
        color: var(--sipadu-muted);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .flow-track-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .policy-ledger-grid,
        .faq-editorial-section {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .ledger-track-pane {
            border-left: none;
            border-top: 1px solid rgba(22, 54, 38, 0.08);
        }
    }

    @media (max-width: 560px) {
        .guides-hero-copy h1 {
            font-size: 22px;
        }

        .faq-big-title {
            font-size: 24px;
        }

        .dashboard-action {
            width: 100%;
        }

        .flow-track-grid {
            grid-template-columns: 1fr;
        }

        .ledger-rule-item {
            grid-template-columns: 1fr;
            gap: 4px;
        }

        .glass-surface,
        .ledger-rules-pane,
        .ledger-track-pane,
        .faq-editorial-section {
            padding: 18px 16px;
        }

        .faq-pill-card {
            padding: 15px 16px;
        }
    }
</style>

<div class="guides-shell">

    {{-- HEADER --}}
    <div class="guides-hero">
        <div class="guides-hero-copy">
            <h1>SOP &amp; FAQ Peminjaman</h1>
            <p>Alur birokrasi perizinan, aturan prioritas penggunaan ruang, dan pusat bantuan SIPADU UPNVJ.</p>
        </div>

        <a href="{{ $createRoute }}" class="dashboard-action">
            <i data-lucide="plus"></i>
            Ajukan Peminjaman
        </a>
    </div>

    {{-- 1. ALUR PENGAJUAN (OTOMATIS DARI KONTEN ADMIN) --}}
    <section class="glass-surface flow-circuit-panel">
        <div class="surface-head">
            <div class="surface-title-group">
                <div class="surface-badge-icon">
                    <i data-lucide="route"></i>
                </div>
                <div>
                    <h2>{{ $alurRow->title ?? 'Alur Pengajuan Peminjaman' }}</h2>
                    <p>Tahapan proses perizinan dan verifikasi peminjaman ruang auditorium UPNVJ</p>
                </div>
            </div>
        </div>

        <div class="flow-track-grid">
            @foreach($flowSteps as $step)
                <div class="flow-node">
                    <div>
                        <div class="flow-node-top">
                            <span class="flow-step-code">{{ $step['code'] }}</span>
                        </div>
                        <div class="flow-node-title">{{ $step['title'] }}</div>
                        <p class="flow-node-desc">{{ $step['desc'] }}</p>
                    </div>
                    <span class="flow-node-actor">{{ $step['actor'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 2. UNIFIED POLICY LEDGER: SOP PRIORITAS & SWITCHER JALUR PEMOHON --}}
    <section class="glass-surface policy-ledger-shell">
        <div class="policy-ledger-grid">

            {{-- KIRI: INDEKS ATURAN PRIORITAS JADWAL --}}
            <div class="ledger-rules-pane">
                <div class="surface-head">
                    <div class="surface-title-group">
                        <div class="surface-badge-icon">
                            <i data-lucide="scale"></i>
                        </div>
                        <div>
                            <h2>{{ $sopRow->title ?? 'SOP & Ketentuan Prioritas Ruangan' }}</h2>
                            <p>Pedoman penyelesaian bentrok jadwal auditorium/aula UPN Veteran Jakarta</p>
                        </div>
                    </div>
                </div>

                <div class="ledger-rule-list">
                    @foreach($sopRules as $index => $rule)
                        <div class="ledger-rule-item">
                            <span class="ledger-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <div class="ledger-rule-head">
                                    <strong>{{ $rule['title'] }}</strong>
                                    <span class="ledger-chip {{ $rule['is_solid'] ? 'is-solid' : '' }}">
                                        {{ $rule['badge'] }}
                                    </span>
                                </div>
                                <p>{{ $rule['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- KANAN: SWITCHER JALUR ORGANISASI & DOKUMEN FAKULTAS --}}
            <aside class="ledger-track-pane">
                <div>
                    <div class="track-pane-eyebrow">
                        <i data-lucide="users"></i>
                        <span>Jalur Administrasi</span>
                    </div>
                    <h3>Pilih Kategori Organisasi Anda</h3>

                    <div class="track-switcher" role="tablist" aria-label="Kategori organisasi peminjam">
                        <button
                            type="button"
                            class="track-tab-btn is-active"
                            role="tab"
                            aria-selected="true"
                            data-track-title="Ormawa Tingkat Fakultas"
                            data-track-desc="Himpunan, BEM Fakultas, atau KSM mengajukan proposal dan surat peminjaman melalui pihak fakultas (WaDek II)."
                        >
                            Ormawa Fakultas
                        </button>
                        <button
                            type="button"
                            class="track-tab-btn"
                            role="tab"
                            aria-selected="false"
                            data-track-title="Unit Kegiatan Mahasiswa (UKM)"
                            data-track-desc="{{ $ukmTrackDesc }}"
                        >
                            UKM Universitas
                        </button>
                    </div>

                    <div class="track-detail-box">
                        <strong id="trackDetailTitle">Ormawa Tingkat Fakultas</strong>
                        <p id="trackDetailDesc">Himpunan, BEM Fakultas, atau KSM mengajukan proposal dan surat peminjaman melalui pihak fakultas (WaDek II).</p>
                    </div>
                </div>

                <div class="portal-doc-box">
                    <div class="portal-doc-caption">
                        <i data-lucide="lock"></i>
                        <span>Alur per fakultas (wajib login email UPNVJ)</span>
                    </div>

                    <a href="http://bit.ly/AlurPeminjamanRuanganPerFakultas" target="_blank" rel="noopener noreferrer" class="portal-open-btn">
                        Buka Panduan Alur Fakultas
                        <i data-lucide="external-link"></i>
                    </a>
                </div>
            </aside>
        </div>
    </section>

    {{-- 3. FAQ & CONTACT PERSON (EDITORIAL SPLIT LAYOUT) --}}
    <section class="faq-editorial-section">

        {{-- KOLOM KIRI: JUDUL BESAR + SEARCH + CONTACT PERSON CARD --}}
        <div class="faq-left-column">
            <div>
                <div class="faq-eyebrow">
                    <span class="faq-eyebrow-dot"></span>
                    <span>FAQs &amp; Bantuan</span>
                </div>
                <h2 class="faq-big-title">Frequently Asked Questions</h2>
            </div>

            <div class="faq-search-bar">
                <i data-lucide="search"></i>
                <input type="text" id="faqSearchInput" placeholder="Cari pertanyaan..." aria-label="Cari pertanyaan FAQ">
            </div>

            {{-- KARTU CONTACT PERSON (OTOMATIS DARI KONTEN ADMIN) --}}
            <div class="cp-callout-card">
                <div class="cp-card-header">
                    <div class="cp-avatar-wrap">
                        <i data-lucide="headset"></i>
                    </div>
                    <div>
                        <h3>{{ $contactRow->title ?? 'Contact Person' }}</h3>
                        <span>Layanan Pengelola Ruang</span>
                    </div>
                </div>

                <p>Butuh koordinasi terkait jadwal atau perizinan ruangan? Hubungi unit pengelola auditorium UPNVJ.</p>

                <div class="cp-meta-pills">
                    <div class="cp-meta-item">
                        <i data-lucide="building-2"></i>
                        <span>Biro Umum &amp; Keuangan UPNVJ</span>
                    </div>
                    <div class="cp-meta-item">
                        <i data-lucide="clock"></i>
                        <span>Senin – Jumat, 08.00 – 16.00 WIB</span>
                    </div>
                </div>

                <div class="cp-status-pill">
                    <i data-lucide="info"></i>
                    <span>{{ $contactText }}</span>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: SINGLE-OPEN ACCORDION (SEMUA TERTUTUP DEFAULT & PANAH TANPA BULET) --}}
        <div>
            <div class="faq-pill-stack" id="faqPillStack">
                @foreach($faqItems as $faq)
                    <button type="button" class="faq-pill-card" aria-expanded="false">
                        <div class="faq-pill-header">
                            <span class="faq-pill-question">{{ $faq['question'] }}</span>
                            <span class="faq-pill-symbol" aria-hidden="true"><i data-lucide="chevron-down"></i></span>
                        </div>
                        <div class="faq-pill-reveal">
                            <div class="faq-pill-reveal-inner">
                                <p class="faq-pill-answer">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            <div class="faq-empty-msg" id="faqEmptyMsg">
                Tidak ada pertanyaan yang cocok dengan kata kunci pencarian Anda.
            </div>
        </div>
    </section>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            window.lucide.createIcons();
        }

        // 1. Single-open accordion pada FAQ (buka 1, otomatis tutup yang lain)
        const faqCards = document.querySelectorAll('#faqPillStack .faq-pill-card');
        faqCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const willOpen = !this.classList.contains('is-pinned');

                faqCards.forEach(function (otherCard) {
                    otherCard.classList.remove('is-pinned');
                    otherCard.setAttribute('aria-expanded', 'false');
                });

                if (willOpen) {
                    this.classList.add('is-pinned');
                    this.setAttribute('aria-expanded', 'true');
                }
            });
        });

        // 2. Switcher Jalur Organisasi (Ormawa Fakultas vs UKM)
        const trackButtons = document.querySelectorAll('.track-switcher .track-tab-btn');
        const trackTitle = document.getElementById('trackDetailTitle');
        const trackDesc = document.getElementById('trackDetailDesc');

        trackButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                trackButtons.forEach(function (b) {
                    b.classList.remove('is-active');
                    b.setAttribute('aria-selected', 'false');
                });
                this.classList.add('is-active');
                this.setAttribute('aria-selected', 'true');

                if (trackTitle) trackTitle.textContent = this.dataset.trackTitle || '';
                if (trackDesc) trackDesc.textContent = this.dataset.trackDesc || '';
            });
        });

        // 3. Pencarian Live FAQ
        const searchInput = document.getElementById('faqSearchInput');
        const emptyMsg = document.getElementById('faqEmptyMsg');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase().trim();
                let visibleCount = 0;
                let firstMatchedCard = null;

                faqCards.forEach(function (card) {
                    const text = card.textContent.toLowerCase();
                    const matched = keyword === '' || text.includes(keyword);
                    card.style.display = matched ? '' : 'none';

                    if (matched) {
                        visibleCount++;
                        if (!firstMatchedCard) {
                            firstMatchedCard = card;
                        }
                    }

                    if (keyword !== '') {
                        card.classList.remove('is-pinned');
                        card.setAttribute('aria-expanded', 'false');
                    }
                });

                if (keyword !== '' && firstMatchedCard) {
                    firstMatchedCard.classList.add('is-pinned');
                    firstMatchedCard.setAttribute('aria-expanded', 'true');
                }

                if (emptyMsg) {
                    emptyMsg.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        }
    });
</script>

@endsection