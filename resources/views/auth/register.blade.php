@extends('layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    html,
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        overflow: hidden !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        font-family: "Plus Jakarta Sans", sans-serif;
    }

    body {
        min-height: 100vh;
    }

    .auth-page,
    .auth-page * {
        font-family: "Plus Jakarta Sans", sans-serif;
    }

    .auth-page {
        position: fixed;
        inset: 0;
        z-index: 20;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        padding: clamp(12px, 2.2vw, 30px);
        overflow: hidden;
        background: #e9efec;
    }

    .auth-shell {
        width: min(1320px, 100%);
        height: 100%;
        max-height: 900px;
        min-height: 0;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        overflow: hidden;
        background: #fff;
        border: 1px solid #dce5e0;
        border-radius: 18px;
        box-shadow: 0 18px 55px rgba(24, 55, 37, .09);
    }

    /* PANEL KIRI (VISUAL & COPY) */
    .auth-art {
        position: relative;
        isolation: isolate;
        min-width: 0;
        min-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: clamp(28px, 4.5vw, 68px);
        color: #fff;
        background: #24563d;
    }

    .auth-slideshow {
        position: absolute;
        inset: 0;
        z-index: -2;
        background: #24563d;
    }

    .auth-slideshow img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        animation: authSlide 20s infinite;
    }

    .auth-slideshow img:nth-child(1) { animation-delay: 0s; }
    .auth-slideshow img:nth-child(2) { animation-delay: 5s; }
    .auth-slideshow img:nth-child(3) { animation-delay: 10s; }
    .auth-slideshow img:nth-child(4) { animation-delay: 15s; }

    .auth-art::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -1;
        background: linear-gradient(
            180deg,
            rgba(12, 48, 29, .20) 0%,
            rgba(12, 48, 29, .35) 40%,
            rgba(12, 48, 29, .88) 100%
        );
    }

    .auth-art-copy {
        display: flex;
        flex-direction: column;
        gap: 0;
        max-width: 520px;
    }

    .auth-art .eyebrow {
        margin: 0 0 14px;
        color: rgba(255, 255, 255, .82);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.8px;
    }

    .auth-art h1 {
        max-width: 520px;
        margin: 0 0 16px;
        color: #fff;
        font-size: clamp(27px, 3.1vw, 43px);
        font-weight: 800;
        line-height: 1.14;
        letter-spacing: -1.2px;
    }

    .auth-art-copy > p:last-child {
        max-width: 430px;
        margin: 0;
        color: rgba(255, 255, 255, .9);
        font-size: 14px;
        line-height: 1.75;
    }

    @keyframes authSlide {
        0%, 5% { opacity: 0; }
        10%, 27% { opacity: 1; }
        32%, 100% { opacity: 0; }
    }

    /* PANEL KANAN (FORM) */
    .auth-form-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: clamp(24px, 3.8vh, 44px) clamp(24px, 4vw, 62px);
        box-sizing: border-box;
        background: #fff;
        scrollbar-width: thin;
        scrollbar-color: rgba(47, 125, 88, .35) transparent;
    }

    .auth-form-panel::-webkit-scrollbar {
        width: 6px;
    }

    .auth-form-panel::-webkit-scrollbar-thumb {
        background: rgba(47, 125, 88, .35);
        border-radius: 999px;
    }

    /* WRAPPER FORM REGISTRASI (SERAGAM DENGAN LOGIN) */
    .auth-form {
        width: min(440px, 100%);
        margin: auto;
        color: #172b20;
    }

    /* TAUTAN KEMBALI + UKURAN PANAH SAMA PERSIS DENGAN LOGIN */
    .auth-form .back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin: 0 0 22px;
        color: #2f7d58;
        font-size: 12.5px;
        font-weight: 600;
        line-height: 1.2;
        text-decoration: none;
        transition: color .18s ease;
    }

    .auth-form .back svg {
        width: 15px;
        height: 15px;
        stroke-width: 2.2;
        flex-shrink: 0;
        transition: transform .18s ease;
    }

    .auth-form .back:hover {
        color: #225e41;
    }

    .auth-form .back:hover svg {
        transform: translateX(-2px);
    }

    .auth-form h2 {
        margin: 0 0 8px;
        color: #172b20;
        font-size: clamp(25px, 2.4vw, 31px);
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.025em;
    }

    .auth-intro {
        margin: 0 0 22px;
        color: #68736c;
        font-size: 13px;
        line-height: 1.6;
    }

    /* SUSUNAN FIELD */
    .auth-form form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .auth-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .auth-field label {
        margin: 0;
        color: #20372a;
        font-size: 12px;
        font-weight: 700;
    }

    .auth-form input:not([type=checkbox]) {
        box-sizing: border-box;
        width: 100%;
        height: 44px;
        margin: 0;
        padding: 0 13px;
        border: 1px solid #dce5e0;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #172b20;
        font-size: 13px;
        font-weight: 400;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    /* Sembunyikan ikon password bawaan browser */
    .auth-form input[type="password"]::-ms-reveal,
    .auth-form input[type="password"]::-ms-clear,
    .auth-form input[type="password"]::-webkit-password-reveal-button,
    .auth-form input[type="password"]::-webkit-credentials-auto-fill-button {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    .auth-form input[type="password"] {
        -webkit-appearance: none;
        appearance: none;
    }

    .auth-form input:focus {
        border-color: #2f7d58;
        box-shadow: 0 0 0 3px rgba(47, 125, 88, .14);
    }

    .password-field {
        position: relative;
        margin: 0;
    }

    .auth-form .password-field input {
        margin: 0;
        padding-right: 48px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 5px;
        transform: translateY(-50%);
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        padding: 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #68736c;
        cursor: pointer;
        transition: background-color .18s ease, color .18s ease;
    }

    .password-toggle:hover {
        background: #eaf4ef;
        color: #2f7d58;
    }

    .password-toggle svg {
        width: 17px;
        height: 17px;
    }

    .btn.wide {
        width: 100%;
        min-height: 44px;
        margin-top: 6px;
        border: 1px solid #276b4c;
        border-radius: 8px;
        background: #2f7d58 !important;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color .18s ease;
    }

    .btn.wide:hover {
        background: #276b4c !important;
    }

    .auth-switch {
        margin: 20px 0 0;
        color: #68736c;
        font-size: 12px;
        text-align: center;
    }

    .auth-switch a {
        margin-left: 3px;
        color: #2f7d58 !important;
        font-weight: 700;
        text-decoration: none;
    }

    .auth-switch a:hover {
        text-decoration: underline;
    }

    .auth-error {
        margin: 0;
        color: #b32e2e;
        font-size: 11px;
        line-height: 1.45;
    }

    .auth-page ~ .flash,
    body > .flash {
        position: fixed;
        z-index: 50;
        top: 12px;
        left: 50%;
        transform: translateX(-50%);
        max-width: min(520px, 90vw);
    }

    /* LAPTOP & DESKTOP DENGAN TINGGI LAYAR TERBATAS (<= 850px) */
    @media (max-height: 850px) and (min-width: 761px) {
        .auth-page {
            padding: clamp(10px, 2vw, 24px);
        }

        .auth-form-panel {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .auth-form .back {
            margin-bottom: 14px;
        }

        .auth-form h2 {
            margin-bottom: 6px;
            font-size: 26px;
        }

        .auth-intro {
            margin-bottom: 16px;
        }

        .auth-form form {
            gap: 11px;
        }

        .auth-field {
            gap: 5px;
        }

        .auth-form input:not([type=checkbox]) {
            height: 40px;
        }

        .btn.wide {
            min-height: 41px;
            margin-top: 4px;
        }

        .auth-switch {
            margin-top: 14px;
        }
    }

    /* TABLET (<= 760px) */
    @media (max-width: 760px) {
        html,
        body {
            height: auto;
            min-height: 100%;
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        .auth-page {
            position: relative;
            inset: auto;
            align-items: flex-start;
            justify-content: center;
            width: 100%;
            height: auto;
            min-height: 100dvh;
            padding: 10px;
            overflow: visible;
        }

        .auth-shell {
            width: 100%;
            height: auto;
            min-height: calc(100dvh - 20px);
            max-height: none;
            grid-template-columns: minmax(0, .82fr) minmax(0, 1.18fr);
            border-radius: 12px;
            overflow: hidden;
        }

        .auth-art {
            padding: 22px;
        }

        .auth-art h1 {
            font-size: clamp(21px, 4vw, 30px);
        }

        .auth-art-copy > p:last-child {
            font-size: 12px;
            line-height: 1.6;
        }

        .auth-form-panel {
            align-items: center;
            padding: 24px 20px;
            overflow: visible;
        }

        .auth-form {
            width: min(440px, 100%);
        }
    }

    /* MOBILE (<= 560px) */
    @media (max-width: 560px) {
        .auth-page {
            min-height: 100dvh;
            padding: 10px 10px 28px;
        }

        .auth-shell {
            display: grid;
            grid-template-columns: 1fr;
            grid-template-rows: auto auto;
            min-height: 0;
            height: auto;
            border-radius: 14px;
            overflow: hidden;
        }

        .auth-art {
            min-height: 145px;
            padding: 16px 20px;
        }

        .auth-art .eyebrow {
            margin-bottom: 6px;
        }

        .auth-art h1 {
            font-size: 21px;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .auth-art-copy > p:last-child {
            font-size: 11px;
            line-height: 1.45;
        }

        .auth-form-panel {
            align-items: flex-start;
            justify-content: center;
            min-height: 0;
            padding: 26px 20px 44px;
            overflow: visible;
        }

        .auth-form {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }

        .auth-form .back {
            margin-bottom: 12px;
        }

        .auth-intro {
            margin-bottom: 18px;
        }

        .auth-form form {
            gap: 13px;
        }

        .auth-form input:not([type=checkbox]) {
            height: 42px;
            font-size: 13px;
        }

        .btn.wide {
            min-height: 44px;
            margin-top: 6px;
        }

        .auth-switch {
            margin-top: 18px;
            padding-bottom: 4px;
        }
    }
</style>

<div class="auth-page">
    <main class="auth-shell">
        <section class="auth-art" aria-label="Informasi SIPADU">
            <div class="auth-slideshow" aria-hidden="true">
                <img src="{{ asset('images/slideshow/upnvjnew.jpg') }}" alt="">
                <img src="{{ asset('images/slideshow/auditbti.jpg') }}" alt="">
                <img src="{{ asset('images/slideshow/fk.jpg') }}" alt="">
                <img src="{{ asset('images/slideshow/auditft.jpg') }}" alt="">
            </div>

            <div class="auth-art-copy">
                <p class="eyebrow">UPN "VETERAN" JAKARTA</p>

                <h1>Mulai ajukan peminjaman auditorium.</h1>

                <p>
                    Buat akun SIPADU untuk mengakses layanan peminjaman auditorium dengan lebih mudah.
                </p>
            </div>
        </section>

        <section class="auth-form-panel">
            <div class="auth-form">
                <a href="{{ route('landing') }}" class="back">
                    <i data-lucide="arrow-left"></i>
                    <span>Kembali ke landing page</span>
                </a>

                <h2>Daftar Akun</h2>

                <p class="auth-intro">
                    Isi data dasar menggunakan email UPNVJ yang sesuai dengan status Anda.
                </p>

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <div class="auth-field">
                        <label for="name">Nama Lengkap</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap"
                            autocomplete="name"
                            required
                        >
                        @error('name')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="email">Email UPNVJ</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="Masukkan email UPNVJ"
                            autocomplete="email"
                            required
                        >
                        @error('email')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                            <button
                                class="password-toggle"
                                type="button"
                                data-toggle-password="password"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <i data-lucide="eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="auth-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation">Ulangi Password</label>
                        <div class="password-field">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                placeholder="Ulangi password"
                                autocomplete="new-password"
                                minlength="8"
                                required
                            >

                            <button
                                class="password-toggle"
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                            >
                                <i data-lucide="eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn wide">Daftar</button>
                </form>

                <p class="auth-switch">
                    Sudah punya akun?
                    <a href="{{ route('login') }}">Login</a>
                </p>
            </div>
        </section>
    </main>
</div>

<script src="https://unpkg.com/lucide@latest"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            window.lucide.createIcons();
        }

        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = document.getElementById(button.dataset.togglePassword);
                const showing = input.type === 'password';

                input.type = showing ? 'text' : 'password';

                button.setAttribute(
                    'aria-label',
                    showing ? 'Sembunyikan password' : 'Tampilkan password'
                );

                button.setAttribute(
                    'aria-pressed',
                    showing ? 'true' : 'false'
                );

                button.innerHTML = showing
                    ? '<i data-lucide="eye-off"></i>'
                    : '<i data-lucide="eye"></i>';

                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        });
    });
</script>

@endsection