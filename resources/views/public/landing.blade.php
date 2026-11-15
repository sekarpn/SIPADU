
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
            width: min(1500px, calc(100% - 48px));
            margin: 18px auto;
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, .80);
            border: 1px solid rgba(255, 255, 255, .92);
            box-shadow: 0 28px 70px rgba(18, 40, 32, .12);
            backdrop-filter: blur(18px);
        }

        .lnav {
            height: 76px;
            padding: 0 44px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255, 255, 255, .88);
            border-bottom: 1px solid var(--lp-line);
            backdrop-filter: blur(18px);

            position: sticky;
            top: 0;
            z-index: 1000;
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
                padding: 0 20px;
                position: sticky;
                top: 0;
                z-index: 1000;
            }

            .lnav {
                padding: 0 20px;
                position: sticky;
                top: 0;
            }

            .lnav-links {
                display: none;
            }

            .lnav-actions .login {
                display: none;
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
    </style>

    <main class="landing-shell">
        <nav class="lnav">
            <a class="brand" href="{{ route('landing') }}">
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
                <a class="lbtn primary" href="{{ route('login') }}">Mulai</a>
            </div>
        </nav>

        <section class="hero">
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
                        Mulai Pengajuan
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

                <h2>Pengajuan yang jelas dari awal sampai keputusan.</h2>

                <p>
                    {{ $alur?->body ?? 'SIPADU menyediakan alur pengajuan yang terstruktur agar pengguna dapat mengetahui tahapan peminjaman auditorium dengan lebih mudah.' }}
                </p>
            </div>

            <div class="feature-grid">
                <article class="feature wide">
                    <div>
                        <div class="num">01</div>
                        <h3>Pilih Auditorium</h3>
                        <p>Mulai dari auditorium dan kebutuhan kegiatan yang sesuai.</p>
                    </div>

                    <div class="big">SIPADU</div>
                </article>

                <article class="feature">
                    <div class="num">02</div>
                    <h3>Buat Pengajuan</h3>
                    <p>
                        Lengkapi data acara, jadwal, serta kebutuhan sesuai formulir yang tersedia.
                    </p>
                </article>

                <article class="feature">
                    <div class="num">03</div>
                    <h3>Pantau Status</h3>
                    <p>
                        Perkembangan pengajuan dapat dipantau sampai keputusan diberikan.
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

                <h2>
                    {{ $sop?->title ?? 'Informasi yang membantu proses peminjaman berjalan lebih jelas.' }}
                </h2>

                <p>
                    {{ $sop?->body ?? ($faq?->body ?? 'Gunakan SOP dan informasi yang tersedia sebagai panduan sebelum melakukan pengajuan peminjaman auditorium.') }}
                </p>
            </div>

            <div class="info-grid">
                <article class="info-card">
                    <h3>SOP</h3>
                    <p>
                        {{ $sop?->title ?? 'Panduan prosedur peminjaman auditorium.' }}
                    </p>
                </article>

                <article class="info-card">
                    <h3>FAQ</h3>
                    <p>
                        {{ $faq?->title ?? 'Jawaban untuk pertanyaan umum pengguna.' }}
                    </p>
                </article>
            </div>

            <div class="stats-row">
                <div class="stat">
                    <b>Alur</b>
                    <span>Pengajuan terstruktur</span>
                </div>

                <div class="stat">
                    <b>Status</b>
                    <span>Proses mudah dipantau</span>
                </div>

                <div class="stat">
                    <b>Informasi</b>
                    <span>Panduan tersedia</span>
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
                    Mulai Ajukan
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
