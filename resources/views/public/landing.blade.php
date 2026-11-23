@extends('layouts.app')

@section('content')
    @php
        $alur = $contents->get('alur');
        $sop = $contents->get('sop');
        $faq = $contents->get('faq');
        $contact = $contents->get('contact');
    @endphp

    <style>
        :root {
            --lp-ink: #10251f;
            --lp-muted: #77827f;
            --lp-green: #2f7d58;
            --lp-mint: #eaf4ef;
            --lp-line: rgba(17, 47, 38, .10);
            --nav-offset: clamp(78px, 9.5vh, 96px);
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: var(--nav-offset);
        }

        body {
            background: linear-gradient(135deg, #eef2ef, #e5ebe7) !important;
            color: var(--lp-ink);
            padding-top: var(--nav-offset);
            overflow-x: hidden;
        }

        .landing-shell {
            width: min(1480px, calc(100% - clamp(24px, 4.5vw, 72px)));
            margin: 6px auto 20px;
            border-radius: clamp(16px, 1.8vw, 24px);
            overflow: hidden;
            background: rgba(255, 255, 255, .80);
            border: 1px solid rgba(255, 255, 255, .92);
            box-shadow: 0 28px 70px rgba(18, 40, 32, .12);
            backdrop-filter: blur(18px);
        }

        /* TOPBAR FLEKSIBEL */
        .lnav {
            width: min(1360px, calc(100% - clamp(32px, 6.5vw, 110px)));
            min-height: clamp(62px, 7.5vh, 72px);
            padding: 0 clamp(18px, 3vw, 40px);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, .72);
            border: 1px solid rgba(255, 255, 255, .55);
            border-radius: 18px;
            box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
            backdrop-filter: blur(18px) saturate(150%);
            -webkit-backdrop-filter: blur(18px) saturate(150%);
            position: fixed;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            isolation: isolate;
            box-sizing: border-box;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--lp-ink);
            font-weight: 800;
        }

        .brand img {
            width: clamp(28px, 2.4vw, 34px);
            height: clamp(28px, 2.4vw, 34px);
            object-fit: contain;
        }

        .brand span {
            font-size: clamp(16px, 1.3vw, 18px);
        }

        .lnav-links {
            display: flex;
            gap: clamp(18px, 2.4vw, 32px);
        }

        .lnav-links a {
            font-size: 12.5px;
            font-weight: 700;
            color: #31413c;
            text-decoration: none;
            transition: color .2s ease;
        }

        .lnav-links a:hover,
        .lnav-links a.active,
        .lnav-links a[aria-current="location"] {
            color: #2f7d58 !important;
        }

        .lnav-actions {
            display: flex;
            gap: 10px;
        }

        .lbtn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            padding: 9px 18px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            transition: transform .2s ease, background-color .2s ease, box-shadow .2s ease;
        }

        .lbtn:hover {
            transform: translateY(-2px);
        }

        .login {
            border: 1px solid #dce5e1;
            background: rgba(255, 255, 255, .78);
            color: var(--lp-ink);
        }

        .primary {
            background: var(--lp-green);
            color: #fff;
            box-shadow: none;
        }

        .primary:hover {
            background: #276b4c;
            color: #fff;
            box-shadow: none;
        }

        /* HERO: PAS 1 LAYAR DI SEMUA RESOLUSI LAPTOP */
        .hero {
            position: relative;
            min-height: clamp(500px, calc(100svh - var(--nav-offset) - 24px), 740px);
            margin: 10px;
            padding: clamp(44px, 6vh, 72px) clamp(20px, 4vw, 48px);
            border-radius: 22px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            box-sizing: border-box;
            background: radial-gradient(
                circle at 50% 42%,
                #fff 0%,
                rgba(255, 255, 255, .92) 38%,
                rgba(239, 246, 242, .82) 100%
            );
        }

        .hero:before {
            content: "";
            position: absolute;
            inset: 0;
            background-image: radial-gradient(
                rgba(28, 73, 57, .18) .75px,
                transparent .75px
            );
            background-size: 7px 7px;
            opacity: .34;
            pointer-events: none;
        }

        /* POSISI RELATIVE AGAR TIDAK PERNAH TERPOTONG DI LAYAR PENDEK */
        .hero-copy {
            position: relative;
            z-index: 5;
            width: min(740px, 60vw);
            max-width: 740px;
            margin: 0 auto;
            text-align: center;
            box-sizing: border-box;
        }

        .eyebrow {
            display: inline-flex;
            padding: 7px 13px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .82);
            border: 1px solid var(--lp-line);
            color: var(--lp-green);
            font-size: 10px;
            font-weight: 850;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .hero h1 {
            text-align: center;
            margin: clamp(16px, 2.2vh, 24px) auto clamp(14px, 2vh, 20px);
            font-size: clamp(34px, 4.1vw, 62px);
            line-height: 1.08;
            letter-spacing: -0.03em;
            text-wrap: balance;
        }

        .hero h1 .soft {
            display: block;
            color: #9ca4a1;
            font-weight: 500;
            margin-top: 4px;
        }

        .hero p {
            max-width: min(560px, 92%);
            margin: 0 auto;
            color: var(--lp-muted);
            font-size: clamp(12.5px, 1.05vw, 14.5px);
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: clamp(20px, 3vh, 28px);
        }

        .hero-actions .lbtn {
            padding: 12px 22px;
        }

        /* FLOATING CARDS: UKURAN & JARAK PROPORSIONAL DI SEMUA LAPTOP */
        .float-card {
            position: absolute;
            z-index: 4;
            background: rgba(255, 255, 255, .78);
            border: 1px solid rgba(255, 255, 255, .95);
            box-shadow: 0 18px 42px rgba(24, 50, 42, .11);
            backdrop-filter: blur(16px);
            border-radius: 18px;
            text-align: left;
            transition: transform .3s ease;
        }

        .note {
            left: clamp(16px, 2.6vw, 44px);
            top: clamp(24px, 8%, 68px);
            width: clamp(185px, 15.5vw, 230px);
            padding: clamp(14px, 1.4vw, 20px);
            transform: rotate(-4deg);
        }

        .note b {
            font-size: clamp(11.5px, 0.95vw, 13px);
        }

        .note p {
            font-size: clamp(10px, 0.82vw, 11px);
            line-height: 1.55;
            margin: 8px 0 0;
            color: var(--lp-muted);
            max-width: 100%;
        }

        .status-card {
            right: clamp(16px, 2.6vw, 44px);
            top: clamp(24px, 8%, 68px);
            width: clamp(185px, 15.5vw, 230px);
            padding: clamp(14px, 1.3vw, 18px);
            transform: rotate(4deg);
        }

        .status-card strong {
            display: block;
            font-size: clamp(11px, 0.9vw, 12px);
            margin-bottom: 8px;
        }

        .status-row {
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .75);
            margin-top: 6px;
            font-size: 10px;
            color: var(--lp-muted);
        }

        .status-row b {
            display: block;
            color: var(--lp-ink);
            font-size: 10.5px;
        }

        .task-card {
            left: clamp(20px, 3.2vw, 56px);
            bottom: clamp(24px, 8%, 68px);
            width: clamp(205px, 17.5vw, 265px);
            padding: clamp(14px, 1.3vw, 18px);
            transform: rotate(2deg);
        }

        .task-card h4 {
            margin: 0 0 10px;
            font-size: clamp(11px, 0.9vw, 12px);
        }

        .task {
            padding: 8px 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .82);
            margin: 6px 0;
            font-size: 10px;
        }

        .task i {
            display: block;
            height: 5px;
            border-radius: 99px;
            background: linear-gradient(90deg, #2e9b73 62%, #dfe8e3 62%);
            margin-top: 6px;
        }

        .integration-card {
            right: clamp(20px, 3.2vw, 56px);
            bottom: clamp(24px, 8%, 68px);
            width: clamp(195px, 16.5vw, 245px);
            padding: clamp(14px, 1.4vw, 20px);
            transform: rotate(-3deg);
        }

        .bubbles {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .bubble {
            width: clamp(34px, 2.8vw, 42px);
            height: clamp(34px, 2.8vw, 42px);
            border-radius: 11px;
            background: #fff;
            display: grid;
            place-items: center;
            font-weight: 850;
            color: var(--lp-green);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .06);
        }

        /* SECTIONS BAWAH */
        .section {
            padding: clamp(56px, 7vw, 90px) clamp(24px, 5vw, 70px);
            scroll-margin-top: var(--nav-offset);
        }

        .section#alur {
            padding-top: clamp(52px, 6vw, 76px);
            padding-bottom: clamp(52px, 6vw, 76px);
        }

        .section-title {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 42px;
        }

        .section-title h2 {
            font-size: clamp(28px, 3.4vw, 48px);
            line-height: 1.1;
            letter-spacing: -.045em;
            margin: 14px 0;
        }

        .section-title p {
            font-size: 13px;
            line-height: 1.8;
            color: var(--lp-muted);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            padding: 10px 0;
        }

        .feature {
            min-height: 180px;
            padding: 26px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid rgba(17, 47, 38, .08);
            box-shadow: 0 8px 24px #cbe0d5;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .feature:hover {
            transform: translateY(-9px);
            box-shadow: 0 18px 35px rgba(18, 40, 32, .15);
        }

        .feature .num {
            display: block;
            margin: 0 0 16px;
            color: var(--lp-green);
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
        }

        .feature h3 {
            margin: 0 0 10px;
            color: var(--lp-ink);
            font-size: 16px;
            letter-spacing: -.02em;
        }

        .feature p {
            max-width: 420px;
            margin: 0;
            color: var(--lp-muted);
            font-size: 12px;
            line-height: 1.75;
        }

        .dark-section {
            padding: clamp(56px, 6.5vw, 78px) clamp(24px, 5vw, 70px);
            background: linear-gradient(135deg, #0d3028, #153f34);
            color: #fff;
            scroll-margin-top: var(--nav-offset);
        }

        .dark-section .eyebrow {
            color: #a7e5c6;
            background: rgba(255, 255, 255, .07);
            border-color: rgba(255, 255, 255, .10);
        }

        .dark-section h2 {
            max-width: 680px;
            font-size: clamp(28px, 3.5vw, 46px);
            line-height: 1.1;
            letter-spacing: -.045em;
            margin: 16px 0 0;
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 36px;
        }

        .step {
            padding: 28px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .08);
        }

        .step b {
            font-size: 42px;
            color: rgba(255, 255, 255, .42);
        }

        .step h3 {
            margin: 14px 0 8px;
            font-size: 16px;
        }

        .step p {
            margin: 0;
            color: #bed0ca;
            font-size: 12px;
            line-height: 1.7;
        }

        /* SOP dan FAQ */
        .panduan-intro {
            max-width: 700px;
            margin: 0 auto 38px;
            text-align: center;
        }

        .panduan-intro p {
            color: var(--lp-muted);
            font-size: 13px;
            line-height: 1.8;
        }

        .panduan-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .panduan-content > div {
            padding: clamp(20px, 2.4vw, 30px);
            background: #ffffff;
            border: 1px solid rgba(17, 47, 38, .09);
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(18, 40, 32, .07);
        }

        .panduan-heading {
            font-size: 22px;
            margin: 0 0 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #edf1ee;
            color: var(--lp-ink);
        }

        .sop-list,
        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .sop-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sop-symbol {
            width: 36px;
            height: 36px;
            min-width: 36px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            padding: 0;
            line-height: 1;
            background: #dceee3;
            color: #438767;
            border: 1px solid #cce4d5;
            border-radius: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 27px;
            font-weight: 500;
        }

        .sop-detail {
            flex: 1;
            padding: 18px 20px;
            background: #f4f8f5;
            border: 1px solid #e7eee9;
            border-radius: 14px;
            transition: transform .3s ease, background-color .3s ease, box-shadow .3s ease;
        }

        .sop-detail:hover {
            transform: translateY(-5px);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(18, 40, 32, .10);
        }

        .sop-detail p {
            margin: 0;
            color: var(--lp-muted);
            font-size: 12px;
            line-height: 1.8;
        }

        .faq-item {
            padding: 22px 24px;
            background: #f4f8f5;
            border: 1px solid #e7eee9;
            border-radius: 16px;
            transition: transform .3s ease, background-color .3s ease, box-shadow .3s ease;
        }

        .faq-item:hover {
            transform: translateY(-5px);
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(18, 40, 32, .10);
        }

        .faq-question {
            color: var(--lp-ink);
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .faq-answer {
            color: var(--lp-muted);
            font-size: 12px;
            line-height: 1.8;
        }

        .faq-answer b {
            color: var(--lp-green);
        }

        .cta {
            margin: 0 clamp(24px, 5vw, 70px) 60px;
            padding: clamp(32px, 4vw, 52px);
            border-radius: 24px;
            background: linear-gradient(135deg, #0d3028, #1c5947);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            scroll-margin-top: var(--nav-offset);
        }

        .cta h2 {
            font-size: clamp(26px, 2.8vw, 40px);
            line-height: 1.1;
            letter-spacing: -.045em;
            margin: 12px 0;
        }

        .cta p {
            max-width: 600px;
            color: #c6d6d1;
            font-size: 12px;
            line-height: 1.8;
        }

        .cta .login {
            color: #fff;
            border-color: rgba(255, 255, 255, .2);
            background: rgba(255, 255, 255, .06);
        }

        .footer {
            padding: 52px clamp(24px, 5vw, 70px) 25px;
            background: #edf3ef;
            border-top: 1px solid var(--lp-line);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.6fr repeat(3, 1fr);
            gap: 32px;
        }

        .footer h4 {
            font-size: 12px;
            margin: 0 0 12px;
        }

        .footer p,
        .footer a {
            font-size: 11px;
            color: var(--lp-muted);
            line-height: 1.8;
            text-decoration: none;
        }

        .footer a {
            display: block;
            margin: 4px 0;
        }

        .footer .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            color: var(--lp-ink) !important;
        }

        .footer .brand span {
            color: var(--lp-ink) !important;
            font-size: 18px;
            line-height: 1;
        }

        .footer .brand img {
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .footer-bottom {
            text-align: center;
            border-top: 1px solid var(--lp-line);
            padding-top: 20px;
            margin-top: 34px;
            font-size: 10px;
            color: var(--lp-muted);
        }

        .reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .6s ease, transform .6s cubic-bezier(.2, .75, .2, 1);
        }

        .reveal.in-view {
            opacity: 1;
            transform: none;
        }

        /* PENYESUAIAN LAPTOP LAYAR KECIL / SPLIT WINDOW (<= 1160px) */
        @media (max-width: 1160px) {
            .hero-copy {
                width: min(640px, 66vw);
            }

            .note,
            .status-card,
            .task-card,
            .integration-card {
                width: 175px;
                padding: 13px;
            }
        }

        /* TABLET & WINDOW SEMPIT (<= 940px) */
        @media (max-width: 940px) {
            .lnav {
                width: calc(100% - 24px);
                min-height: 64px;
                padding: 0 20px;
                top: 8px;
            }

            .lnav-links {
                display: none;
            }

            .hero {
                margin: 8px 0 0;
                min-height: auto;
                padding: 56px 24px;
                border-radius: 0;
            }

            .hero-copy {
                width: 100%;
            }

            .note,
            .status-card,
            .task-card,
            .integration-card {
                display: none;
            }

            .section,
            .dark-section {
                padding: 64px 24px;
            }

            .feature-grid,
            .steps,
            .panduan-content {
                grid-template-columns: 1fr;
            }

            .cta {
                margin: 0 18px 36px;
                padding: 36px;
                display: block;
            }

            .footer {
                padding: 44px 24px 24px;
            }

            .footer-grid {
                grid-template-columns: 1fr 1fr;
            }

            .footer-grid > div:first-child {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 560px) {
            .landing-shell {
                width: 100%;
                margin: 0;
                border-radius: 0;
            }

            .hero {
                padding: 44px 18px;
            }

            .hero h1 {
                font-size: clamp(28px, 8vw, 40px);
                line-height: 1.12;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .lbtn {
                width: 100%;
            }

            .section,
            .dark-section {
                padding: 48px 18px;
            }

            .section#alur .feature {
                min-height: 0;
            }

            .panduan-content > div {
                padding: 20px;
            }

            .cta {
                margin: 0 18px 30px;
                padding: 28px 22px;
            }

            .step {
                padding: 22px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }

            .footer-grid > div:first-child {
                grid-column: auto;
            }

            .brand span {
                font-size: 16px;
            }
        }
    </style>

    <nav class="lnav">
        <a class="brand" href="#top">
            <img src="{{ asset('images/logosipadu.png') }}" alt="SIPADU">
            <span>SIPADU</span>
        </a>

        <div class="lnav-links">
            <a href="#alur">Alur Pengajuan</a>
            <a href="#informasi">Informasi</a>
            <a href="#panduan">SOP & FAQ</a>
            <a href="#contact">Kontak</a>
        </div>

        <div class="lnav-actions">
            <a class="lbtn login" href="{{ route('login') }}">Login</a>
            <a class="lbtn primary" href="{{ route('register') }}">Register</a>
        </div>
    </nav>

    <main class="landing-shell">
        <section class="hero" id="top">
            <div class="float-card note">
                <b>Ajukan dengan lebih teratur.</b>
                <p>
                    Isi kebutuhan peminjaman, pilih jadwal, lalu pantau prosesnya
                    dalam satu sistem.
                </p>
            </div>

            <div class="float-card status-card">
                <strong>Status Pengajuan</strong>

                <div class="status-row">
                    <b>Menunggu Persetujuan</b>
                    Pengajuan sedang diproses
                </div>

                <div class="status-row">
                    <b>Disetujui</b>
                    Jadwal berhasil diamankan
                </div>
            </div>

            <div class="hero-copy">
                <span class="eyebrow">Sistem Peminjaman Auditorium</span>

                <h1>
                    Ajukan, pantau, dan kelola
                    <span class="soft">peminjaman auditorium</span>
                </h1>

                <p>
                    Satu portal untuk memudahkan proses pengajuan peminjaman dan memperoleh informasi auditorium di lingkungan Universitas Pembangunan Nasional Veteran Jakarta.
                </p>

                <div class="hero-actions">
                    <a class="lbtn primary" href="{{ route('login') }}">
                        Ajukan Sekarang
                    </a>

                    <a class="lbtn login" href="#alur">
                        Lihat Alur
                    </a>
                </div>
            </div>

            <div class="float-card task-card">
                <h4>Pengajuan Hari Ini</h4>

                <div class="task">
                    Peminjaman Auditorium
                    <i></i>
                </div>

                <div class="task">
                    Verifikasi Jadwal
                    <i style="background: linear-gradient(90deg, #2e9b73 42%, #dfe8e3 42%)"></i>
                </div>
            </div>

            <div class="float-card integration-card">
                <b style="font-size: 13px">Satu Sistem, Satu Alur</b>

                <div class="bubbles">
                    <div class="bubble">S</div>
                    <div class="bubble">✓</div>
                    <div class="bubble">⌂</div>
                </div>
            </div>
        </section>

        <section class="section reveal" id="alur">
            <div class="section-title">
                <span class="eyebrow">Alur Pengajuan</span>

                <h2>{{ $alur?->title ?: 'Alur peminjaman auditorium yang mudah dan terarah.' }}</h2>

                <p>
                    {{ $alur?->body ?: 'Peminjam mengajukan permohonan peminjaman auditorium, kemudian admin melakukan verifikasi. Setelah pengajuan disetujui, auditorium siap digunakan sesuai jadwal.' }}
                </p>
            </div>

            <div class="feature-grid">
                <article class="feature">
                    <div class="num">1</div>
                    <h3>Peminjam Mengajukan</h3>
                    <p>
                        Peminjam mengisi dan mengirim formulir pengajuan peminjaman auditorium.
                    </p>
                </article>

                <article class="feature">
                    <div class="num">2</div>
                    <h3>Verifikasi Admin</h3>
                    <p>
                        Admin memeriksa pengajuan dan memberikan keputusan persetujuan.
                    </p>
                </article>

                <article class="feature">
                    <div class="num">3</div>
                    <h3>Auditorium Siap Digunakan</h3>
                    <p>
                        Auditorium dapat digunakan sesuai jadwal setelah pengajuan disetujui.
                    </p>
                </article>
            </div>
        </section>

        <section class="dark-section reveal" id="informasi">
            <span class="eyebrow">Mengapa SIPADU</span>

            <h2>Proses peminjaman yang lebih tertata dan mudah dipantau.</h2>

            <div class="steps">
                <article class="step">
                    <b>1</b>
                    <h3>Data Terpusat</h3>
                    <p>
                        Informasi pengajuan dan proses peminjaman tersimpan dalam satu sistem.
                    </p>
                </article>

                <article class="step">
                    <b>2</b>
                    <h3>Verifikasi Terarah</h3>
                    <p>
                        Administrator dapat memproses pengajuan berdasarkan informasi yang tersedia.
                    </p>
                </article>

                <article class="step">
                    <b>3</b>
                    <h3>Status Transparan</h3>
                    <p>
                        Pengguna dapat melihat perkembangan pengajuan dengan lebih mudah.
                    </p>
                </article>
            </div>
        </section>

        <section class="section reveal" id="panduan">
            <div class="section-title">
                <span class="eyebrow">Panduan</span>

                <h2>SOP dan FAQ Peminjaman Auditorium SIPADU</h2>

                <p>
                    Pelajari prosedur peminjaman dan temukan jawaban
                    atas pertanyaan umum sebelum mengajukan auditorium.
                </p>
            </div>

            <div class="panduan-content">

                {{-- SOP --}}
                <div>
                    <h3 class="panduan-heading">SOP Peminjaman</h3>

                    <div class="sop-list">

                        <div class="sop-item">
                            <div class="sop-symbol">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-caret-right-fill"
                                    viewBox="0 0 16 16">
                                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </div>
                            <div class="sop-detail">
                                <p>
                                    UKM, BEM, atau Himpunan mengajukan proposal
                                    dan surat peminjaman kepada Koordinator
                                    Bidang Kemahasiswaan.
                                </p>
                            </div>
                        </div>

                        <div class="sop-item">
                            <div class="sop-symbol">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-caret-right-fill"
                                    viewBox="0 0 16 16">
                                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </div>
                            <div class="sop-detail">
                                <p>
                                    Apabila terdapat jadwal bersamaan dengan
                                    kegiatan pihak rektorat, kegiatan rektorat
                                    diprioritaskan.
                                </p>
                            </div>
                        </div>

                        <div class="sop-item">
                            <div class="sop-symbol">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-caret-right-fill"
                                    viewBox="0 0 16 16">
                                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </div>
                            <div class="sop-detail">
                                <p>
                                    Jika pengajuan BEM dan Himpunan memiliki
                                    jadwal yang sama, tingkat urgensi dan
                                    kepentingan kegiatan menjadi pertimbangan.
                                </p>
                            </div>
                        </div>

                        <div class="sop-item">
                            <div class="sop-symbol">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-caret-right-fill"
                                    viewBox="0 0 16 16">
                                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </div>
                            <div class="sop-detail">
                                <p>
                                    Pengajuan kegiatan eksternal dan internal
                                    UPN dinilai berdasarkan tujuan kegiatan
                                    serta asal narasumber.
                                </p>
                            </div>
                        </div>

                        <div class="sop-item">
                            <div class="sop-symbol">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    fill="currentColor"
                                    class="bi bi-caret-right-fill"
                                    viewBox="0 0 16 16">
                                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </div>
                            <div class="sop-detail">
                                <p>
                                    Lihat panduan lengkap melalui
                                    <a href="http://bit.ly/AlurPeminjamanRuanganPerFakultas"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       style="color: var(--lp-green); font-weight: 700;">
                                        tautan SOP peminjaman ruangan
                                    </a>.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- FAQ --}}
                <div>
                    <h3 class="panduan-heading">Frequently Asked Questions (FAQ)</h3>

                    <div class="faq-list">

                        <article class="faq-item">
                            <div class="faq-question">
                                Q: Siapa saja yang dapat mengajukan peminjaman auditorium?
                            </div>
                            <div class="faq-answer">
                                <b>A:</b> UKM, BEM, dan Himpunan dapat mengajukan
                                peminjaman auditorium sesuai prosedur yang berlaku.
                            </div>
                        </article>

                        <article class="faq-item">
                            <div class="faq-question">
                                Q: Bagaimana cara mengajukan peminjaman auditorium?
                            </div>
                            <div class="faq-answer">
                                <b>A:</b> Peminjam mengajukan proposal dan surat
                                peminjaman kepada Admin Biro Umum & Keuangan sesuai
                                prosedur peminjaman.
                            </div>
                        </article>

                        <article class="faq-item">
                            <div class="faq-question">
                                Q: Bagaimana jika terdapat jadwal peminjaman yang bersamaan?
                            </div>
                            <div class="faq-answer">
                                <b>A:</b> Pengajuan akan dipertimbangkan berdasarkan
                                prioritas, tingkat urgensi, dan kepentingan kegiatan.
                            </div>
                        </article>

                    </div>
                </div>

            </div>
        </section>

        <section class="cta reveal" id="contact">
            <div>
                <span class="eyebrow">Mulai Sekarang</span>

                <h2>{{ $contact?->title ?: 'Siap mengajukan peminjaman auditorium?' }}</h2>

                <p>
                    {{ $contact?->body ?? 'Masuk ke SIPADU untuk memulai pengajuan dan memantau proses peminjaman.' }}
                </p>
            </div>

            <div class="hero-actions">
                <a class="lbtn primary" href="{{ route('login') }}">
                    Ajukan Sekarang
                </a>

                <a class="lbtn login" href="{{ route('register') }}">
                    Daftar Akun
                </a>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-grid">
                <div>
                    <a class="brand" href="{{ route('landing') }}">
                        <img src="{{ asset('images/logosipadu.png') }}" alt="SIPADU">
                        <span>SIPADU</span>
                    </a>

                    <p>
                        Sistem informasi untuk mendukung proses peminjaman auditorium
                        UPN Veteran Jakarta.
                    </p>
                </div>

                <div>
                    <h4>Informasi</h4>
                    <a href="#alur">Alur Pengajuan</a>
                    <a href="#informasi">Informasi</a>
                </div>

                <div>
                    <h4>Panduan</h4>
                    <a href="#panduan">SOP</a>
                    <a href="#panduan">FAQ</a>
                </div>

                <div>
                    <h4>Akses</h4>
                    <a href="{{ route('login') }}">Login SIPADU</a>
                    <a href="https://upnvj.ac.id" target="_blank" rel="noopener noreferrer">
                        UPN Veteran Jakarta
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                © {{ date('Y') }} SIPADU · Sistem Peminjaman Auditorium
            </div>
        </footer>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var navLinks = Array.from(
                document.querySelectorAll('.lnav-links a[href^="#"]')
            );

            var navSections = navLinks
                .map(function (link) {
                    return document.querySelector(link.getAttribute('href'));
                })
                .filter(Boolean);

            var isNavScrolling = false;
            var navScrollTimer = null;

            // Mengatur menu yang sedang aktif
            function setActiveNav(sectionId) {
                navLinks.forEach(function (link) {
                    var isActive =
                        link.getAttribute('href') === '#' + sectionId;

                    link.classList.toggle('active', isActive);

                    if (isActive) {
                        link.setAttribute('aria-current', 'location');
                    } else {
                        link.removeAttribute('aria-current');
                    }
                });
            }

            // Mendeteksi section berdasarkan posisi scroll
            function updateActiveNav() {
                if (isNavScrolling) return;

                var scrollPosition =
                    window.scrollY + window.innerHeight;

                var pageHeight =
                    document.documentElement.scrollHeight;

                // Prioritaskan Kontak ketika sudah mencapai bagian bawah halaman
                if (scrollPosition >= pageHeight - 5) {
                    setActiveNav('contact');
                    return;
                }

                // Marker dibuat lebih rendah agar section Kontak
                // bisa aktif sebelum mencapai footer.
                var marker = Math.min(
                    300,
                    Math.max(160, window.innerHeight * 0.4)
                );

                var currentSection = null;

                navSections.forEach(function (section) {
                    var sectionTop =
                        section.getBoundingClientRect().top;

                    if (sectionTop <= marker) {
                        currentSection = section;
                    }
                });

                if (currentSection) {
                    setActiveNav(currentSection.id);
                } else {
                    navLinks.forEach(function (link) {
                        link.classList.remove('active');
                        link.removeAttribute('aria-current');
                    });
                }
            }

            // Navigasi saat menu topbar diklik
            navLinks.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    var targetId = this.getAttribute('href');
                    var target = document.querySelector(targetId);

                    if (!target) return;

                    isNavScrolling = true;

                    // Langsung aktifkan warna hijau pada menu yang diklik
                    setActiveNav(target.id);

                    clearTimeout(navScrollTimer);

                    var targetPosition =
                        target.getBoundingClientRect().top +
                        window.pageYOffset - 110;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    history.replaceState(null, '', targetId);

                    // Aktifkan kembali pendeteksian scroll setelah animasi
                    navScrollTimer = setTimeout(function () {
                        isNavScrolling = false;
                        updateActiveNav();
                    }, 1200);
                });
            });

            // Deteksi posisi scroll manual
            window.addEventListener('scroll', updateActiveNav, {
                passive: true
            });

            window.addEventListener('resize', updateActiveNav);

            window.addEventListener('load', updateActiveNav);

            // Jalankan saat halaman pertama kali dibuka
            updateActiveNav();

            // Animasi tampilan section
            var items = document.querySelectorAll('.reveal');

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('in-view');
                            io.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: .12
                });

                items.forEach(function (el) {
                    io.observe(el);
                });
            } else {
                items.forEach(function (el) {
                    el.classList.add('in-view');
                });
            }
        });
    </script>
@endsection