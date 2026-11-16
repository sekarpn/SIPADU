
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
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: linear-gradient(135deg, #eef2ef, #e5ebe7) !important;
            color: var(--lp-ink);
        }

        .landing-shell {
            width: min(1680px, calc(100% - 80px));
            margin: 0 auto 24px;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, .80);
            border: 1px solid rgba(255, 255, 255, .92);
            box-shadow: 0 28px 70px rgba(18, 40, 32, .12);
            backdrop-filter: blur(18px);
        }

        .lnav {
            width: min(1680px, calc(100% - 80px));
            min-height: 76px;
            padding: 0 44px;
            margin: 18px auto 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, .95);
            border: 1px solid rgba(255, 255, 255, .95);
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(18, 40, 32, .08);
            -webkit-backdrop-filter: blur(22px) saturate(150%);
            backdrop-filter: blur(22px) saturate(150%);
            position: sticky;
            top: 12px;
            z-index: 1100;
            isolation: isolate;
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
            width: 34px;
            height: 34px;
            object-fit: contain;
        }

        .brand span {
            font-size: 18px;
        }

        .lnav-links {
            display: flex;
            gap: 32px;
        }

        .lnav-links a {
            font-size: 12px;
            font-weight: 700;
            color: #31413c;
            text-decoration: none;
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
            padding: 10px 18px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            transition: transform .2s ease, box-shadow .2s ease;
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
            background: linear-gradient(135deg, #2e9b73, #1d7050);
            color: #fff;
            box-shadow: 0 10px 24px rgba(35, 123, 84, .18);
        }

        .hero {
            position: relative;
            min-height: 690px;
            margin: 0 10px 10px;
            border-radius: 26px;
            overflow: hidden;
            display: grid;
            place-items: center;
            text-align: center;
            background: radial-gradient(
                circle at 50% 34%,
                #fff 0%,
                rgba(255, 255, 255, .92) 34%,
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
        }

        .hero-copy {
            position: absolute;
            z-index: 5;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            width: min(850px, 75%);
            max-width: 850px;
            padding: 0;
            margin: 0;
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .76);
            border: 1px solid var(--lp-line);
            color: var(--lp-green);
            font-size: 10px;
            font-weight: 850;
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .hero h1 {
            text-align: center;
            margin: 24px auto 20px;
            max-width: 850px;
            font-size: clamp(48px, 5.5vw, 76px);
            line-height: 1.05;
        }

        .hero h1 .soft {
            display: block;
            color: #9ca4a1;
            font-weight: 500;
        }

        .hero p {
            max-width: 590px;
            margin: 0 auto;
            color: var(--lp-muted);
            font-size: 14px;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 28px;
        }

        .hero-actions .lbtn {
            padding: 13px 22px;
        }

        .float-card {
            position: absolute;
            z-index: 5;
            background: rgba(255, 255, 255, .70);
            border: 1px solid rgba(255, 255, 255, .95);
            box-shadow: 0 22px 50px rgba(24, 50, 42, .13);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            text-align: left;
        }

        .note {
            left: 4%;
            top: 13%;
            width: 230px;
            padding: 20px;
            transform: rotate(-5deg);
        }

        .note b {
            font-size: 13px;
        }

        .note p {
            font-size: 11px;
            line-height: 1.6;
            margin: 10px 0 0;
            color: var(--lp-muted);
        }

        .status-card {
            right: 4%;
            top: 13%;
            width: 230px;
            padding: 18px;
            transform: rotate(5deg);
        }

        .status-card strong {
            display: block;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .status-row {
            padding: 10px;
            border-radius: 11px;
            background: rgba(255, 255, 255, .68);
            margin-top: 7px;
            font-size: 10px;
            color: var(--lp-muted);
        }

        .status-row b {
            display: block;
            color: var(--lp-ink);
            font-size: 11px;
        }

        .task-card {
            left: 5%;
            bottom: -10px;
            width: 280px;
            padding: 18px;
            transform: rotate(2deg);
        }

        .task-card h4 {
            margin: 0 0 12px;
            font-size: 12px;
        }

        .task {
            padding: 10px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .78);
            margin: 7px 0;
            font-size: 10px;
        }

        .task i {
            display: block;
            height: 5px;
            border-radius: 99px;
            background: linear-gradient(90deg, #2e9b73 62%, #dfe8e3 62%);
            margin-top: 8px;
        }

        .integration-card {
            right: 5%;
            bottom: -10px;
            width: 260px;
            padding: 20px;
            transform: rotate(-3deg);
        }

        .bubbles {
            display: flex;
            gap: 8px;
            margin-top: 14px;
        }

        .bubble {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: #fff;
            display: grid;
            place-items: center;
            font-weight: 850;
            color: var(--lp-green);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .06);
        }

        .section {
            padding: 90px 70px;
            scroll-margin-top: 92px;
        }

        .section-title {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 42px;
        }

        .section-title h2 {
            font-size: clamp(32px, 4vw, 50px);
            line-height: 1.08;
            letter-spacing: -.055em;
            margin: 14px 0;
        }

        .section-title p {
            font-size: 13px;
            line-height: 1.8;
            color: var(--lp-muted);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 16px;
            padding: 16px;
            border-radius: 24px;
            background: rgba(246, 250, 248, .75);
            border: 1px solid rgba(255, 255, 255, .9);
            box-shadow: 0 18px 55px rgba(18, 45, 35, .06);
        }

        .feature {
            min-height: 235px;
            padding: 28px;
            border-radius: 18px;
            background: rgba(255, 255, 255, .78);
            border: 1px solid var(--lp-line);
        }

        .feature.wide {
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, .92),
                rgba(232, 246, 238, .80)
            );
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .num {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: grid;
            place-items: center;
            background: var(--lp-mint);
            color: var(--lp-green);
            font-size: 12px;
            font-weight: 850;
        }

        .feature h3 {
            font-size: 19px;
            margin: 20px 0 8px;
        }

        .feature p {
            font-size: 12px;
            line-height: 1.75;
            color: var(--lp-muted);
        }

        .feature .big {
            font-size: 44px;
            font-weight: 850;
            color: var(--lp-green);
            letter-spacing: -.06em;
        }

        .dark-section {
            padding: 78px 70px;
            background: linear-gradient(135deg, #0d3028, #153f34);
            color: #fff;
        }

        .dark-section .eyebrow {
            color: #a7e5c6;
            background: rgba(255, 255, 255, .07);
            border-color: rgba(255, 255, 255, .10);
        }

        .dark-section h2 {
            max-width: 680px;
            font-size: clamp(34px, 4vw, 48px);
            line-height: 1.08;
            letter-spacing: -.05em;
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

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .info-card {
            padding: 34px;
            border-radius: 22px;
            background: rgba(255, 255, 255, .78);
            border: 1px solid var(--lp-line);
            box-shadow: 0 12px 35px rgba(20, 50, 39, .05);
        }

        .info-card h3 {
            font-size: 24px;
            margin: 0 0 12px;
        }

        .info-card p {
            font-size: 13px;
            line-height: 1.8;
            color: var(--lp-muted);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 18px;
        }

        .stat {
            padding: 26px;
            border-radius: 18px;
            background: #edf6f1;
            text-align: center;
        }

        .stat b {
            display: block;
            font-size: 28px;
        }

        .stat span {
            font-size: 11px;
            color: var(--lp-muted);
        }

        .cta {
            margin: 0 70px 60px;
            padding: 52px;
            border-radius: 24px;
            background: linear-gradient(135deg, #0d3028, #1c5947);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
        }

        .cta h2 {
            font-size: clamp(30px, 3vw, 42px);
            line-height: 1.1;
            letter-spacing: -.05em;
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
            padding: 52px 70px 25px;
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

        @media (max-width: 900px) {
            .lnav {
                width: calc(100% - 24px);
                min-height: 68px;
                padding: 0 20px;
                margin: 12px auto 14px;
                top: 8px;
            }

            .lnav-links {
                display: none;
            }

            .lnav-actions .login {
                display: inline-flex;
            }

            .hero {
                margin: 0;
                min-height: 620px;
                border-radius: 0;
                position: relative;
                overflow: hidden;
            }

            .note,
            .status-card {
                display: none;
            }

            .hero-copy {
                padding-top: 60px;
            }

            .task-card {
                left: 18px;
                width: 42%;
                max-width: 230px;
            }

            .integration-card {
                right: 18px;
                width: 42%;
                max-width: 230px;
            }

            .section,
            .dark-section {
                padding: 68px 24px;
            }

            .feature-grid,
            .steps,
            .info-grid {
                grid-template-columns: 1fr;
            }

            .stats-row {
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
            .hero {
                min-height: 570px;
            }

            .hero h1 {
                font-size: 50px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .lbtn {
                width: 100%;
            }

            .task-card,
            .integration-card {
                display: none;
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

        /* Landing page sizing and three-step process cards */

        .section#alur {
            padding-top: 76px;
            padding-bottom: 76px;
        }

        .section#alur .feature-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
            padding: 10px 0;
            border-radius: 0;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .section#alur .feature,
        .section#alur .feature.wide {
            min-height: 180px;
            padding: 26px;
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid rgba(17, 47, 38, .08);
            box-shadow: 0 8px 24px #cbe0d5;
            display: block;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .section#alur .feature:hover {
            transform: translateY(-9px);
            box-shadow: 0 18px 35px rgba(18, 40, 32, .15);
        }

        .section#alur .feature .num {
            width: auto;
            height: auto;
            display: block;
            padding: 0;
            margin: 0 0 16px;
            border: 0;
            border-radius: 0;
            background: transparent;
            color: var(--lp-green);
            font-size: 34px;
            line-height: 1;
            font-weight: 800;
        }

        .section#alur .feature h3 {
            margin: 0 0 10px;
            color: var(--lp-ink);
            font-size: 16px;
            letter-spacing: -.02em;
        }

        .section#alur .feature p {
            max-width: 420px;
            margin: 0;
            color: var(--lp-muted);
            font-size: 12px;
            line-height: 1.75;
        }

        @media (max-width: 900px) {
            .section#alur .feature-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .section#alur .feature {
                min-height: 0;
            }
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

.panduan-heading {
    font-size: 22px;
    margin: 0 0 22px;
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

    font-family: Arial, sans-serif;
    font-size: 27px;
    font-weight: 500;
}

.sop-detail {
    flex: 1;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid var(--lp-line);
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(18, 40, 32, .06);
    transition: transform .3s ease, box-shadow .3s ease;
}

.sop-detail:hover,
.faq-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 30px rgba(18, 40, 32, .13);
}

.sop-detail p {
    margin: 0;
    color: var(--lp-muted);
    font-size: 12px;
    line-height: 1.8;
}

.faq-item {
    padding: 22px 24px;
    background: #fff;
    border: 1px solid var(--lp-line);
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(18, 40, 32, .06);
    transition: transform .3s ease, box-shadow .3s ease;
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

@media (max-width: 900px) {
    .panduan-content {
        grid-template-columns: 1fr;
    }
}

/* Kontainer utama SOP dan FAQ */
.panduan-content > div {
    padding: 30px;
    background: #ffffff;
    border: 1px solid rgba(17, 47, 38, .09);
    border-radius: 22px;
    box-shadow: 0 12px 35px rgba(18, 40, 32, .07);
}

/* Judul di dalam kontainer */
.panduan-heading {
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #edf1ee;
}

/* Kotak isi SOP */
.sop-detail {
    background: #f4f8f5;
    border: 1px solid #e7eee9;
    box-shadow: none;
    transition: transform .3s ease, box-shadow .3s ease;
}

.sop-detail:hover {
    transform: translateY(-5px);
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(18, 40, 32, .10);
}

/* Kotak isi FAQ */
.faq-item {
    background: #f4f8f5;
    border: 1px solid #e7eee9;
    box-shadow: none;
    transition: transform .3s ease, box-shadow .3s ease;
}

.faq-item:hover {
    transform: translateY(-5px);
    background: #ffffff;
    box-shadow: 0 10px 24px rgba(18, 40, 32, .10);
}

/* Simbol SOP */
.sop-symbol {
    flex-shrink: 0;
    background: #dceee3;
    border: 1px solid #cce4d5;
}

@media (max-width: 560px) {
    .panduan-content > div {
        padding: 20px;
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
                <a href="#kontak">Kontak</a>
            </div>

            <div class="lnav-actions">
                <a class="lbtn login" href="{{ route('login') }}">Login</a>
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

                <h2>Alur peminjaman auditorium yang mudah dan terarah.</h2>

                <p>
                    Peminjam mengajukan permohonan peminjaman auditorium,
                    kemudian admin melakukan verifikasi. Setelah pengajuan disetujui,
                    auditorium siap digunakan sesuai jadwal.
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

        <section class="cta reveal" id="kontak">
            <div>
                <span class="eyebrow">Mulai Sekarang</span>

                <h2>Siap mengajukan peminjaman auditorium?</h2>

                <p>
                    {{ $contact?->body ?? 'Masuk ke SIPADU untuk memulai pengajuan dan memantau proses peminjaman.' }}
                </p>
            </div>

            <div class="hero-actions">
                <a class="lbtn primary" href="{{ route('login') }}">
                    Ajukan Sekarang
                </a>

                <a class="lbtn login" href="{{ route('login') }}">
                    Login
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
                    <a href="https://upnvj.ac.id" target="_blank">
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
            document.querySelectorAll('a[href^="#"]').forEach(function (a) {
                a.addEventListener('click', function (e) {
                    var t = document.querySelector(this.getAttribute('href'));

                    if (t) {
                        e.preventDefault();
                        t.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

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
