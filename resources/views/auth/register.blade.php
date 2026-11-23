@extends('layouts.app')

@section('content')

<style>
    html,
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        overflow: hidden !important;
    }

    body {
        min-height: 100vh;
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
        font-family: "Plus Jakarta Sans", sans-serif;
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

    /* PANEL FORM */
    .auth-form-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
        min-height: 0;
        overflow: hidden;
        padding: 30px clamp(28px, 5vw, 72px);
        box-sizing: border-box;
        background: #fff;
    }

    /* FORM REGISTRASI */
    .auth-form {
        width: min(440px, 100%);
        max-height: none;
        margin: 0 auto;
        flex-shrink: 0;
        color: #172b20;
    }

    .auth-form .back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 12px;
        color: #64736a;
        font-size: 12px;
        text-decoration: none;
    }

    .auth-form .back:hover {
        color: #2f7d58;
    }

    .auth-form h2 {
        margin: 0 0 7px;
        color: #172b20;
        font-size: clamp(24px, 2.3vw, 31px);
        line-height: 1.2;
        letter-spacing: -.8px;
    }

    .auth-intro {
        margin: 0 0 20px;
        color: #68736c;
        font-size: 12px;
        line-height: 1.5;
    }

    .auth-form form {
        display: flex;
        flex-direction: column;
    }

    .auth-form form > label {
        margin: 0 0 7px;
        color: #20372a;
        font-size: 11px;
        font-weight: 700;
    }

    .auth-form input:not([type=checkbox]) {
        box-sizing: border-box;
        width: 100%;
        height: 42px;
        margin: 0 0 15px;
        padding: 0 14px;
        border: 1px solid #dce5e0;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #172b20;
        font: 400 12px "Plus Jakarta Sans", sans-serif;
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

    /* Samakan tampilan input password di berbagai browser */
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
        margin: 0 0 15px;
    }

    .auth-form .password-field input {
        margin: 0;
        padding-right: 47px;
    }

    .password-toggle {
        position: absolute;
        top: 5px;
        right: 5px;
        display: grid;
        place-items: center;
        width: 34px;
        height: 32px;
        padding: 0;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #68736c;
        cursor: pointer;
    }

    .password-toggle:hover {
        background: #eaf4ef;
        color: #2f7d58;
    }

    .password-toggle svg {
        width: 16px;
        height: 16px;
    }

    .btn.wide {
        width: 100%;
        min-height: 42px;
        margin-top: 2px;
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
        margin: 14px 0 0;
        color: #68736c;
        font-size: 12px;
        text-align: center;
    }

    .auth-switch a {
        color: #2f7d58 !important;
        font-weight: 700;
        text-decoration: none;
    }

    .auth-error {
        margin: -8px 0 9px;
        color: #b32e2e;
        font-size: 11px;
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

    /* LAPTOP DAN DESKTOP DENGAN TINGGI LAYAR TERBATAS */
    @media (max-height: 850px) and (min-width: 761px) {

    .auth-page {
        position: fixed;
        inset: 0;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100vh;
        height: 100dvh;
        padding: clamp(12px, 2.2vw, 30px);
        overflow: hidden;
    }

    .auth-shell {
        height: 100%;
        min-height: 0;
        max-height: 900px;
    }

    .auth-form-panel {
        padding-top: 16px;
        padding-bottom: 16px;
    }

    .auth-form .back {
        margin-bottom: 8px;
    }

    .auth-form h2 {
        margin-bottom: 5px;
        font-size: 26px;
    }

    .auth-intro {
        margin-bottom: 12px;
    }

    .auth-form form > label {
        margin-bottom: 5px;
    }

    .auth-form input:not([type=checkbox]) {
        height: 37px;
        margin-bottom: 9px;
    }

    .password-field {
        margin-bottom: 9px;
    }

    .password-toggle {
        top: 2px;
    }

    .btn.wide {
        min-height: 39px;
        margin-top: 0;
    }

    .auth-switch {
        margin-top: 10px;
    }
}

    /* TABLET DAN MOBILE */
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
        }

        .auth-form-panel {
            align-items: center;
            padding: 30px 22px;
            overflow: visible;
        }

        .auth-form {
            width: min(440px, 100%);
            transform: none;
        }
    }

    /* MOBILE */
    @media (max-width: 560px) {
        .auth-page {
            min-height: 100dvh;
            padding: 10px 10px 30px;
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
            padding: 14px 20px;
        }

        .auth-art h1 {
            font-size: 20px;
            line-height: 1.2;
            margin-bottom: 5px;
        }

        .auth-art .eyebrow {
            margin-bottom: 5px;
        }

        .auth-art-copy > p:last-child {
            font-size: 10px;
            line-height: 1.35;
        }

        .auth-form-panel {
            align-items: center;
            justify-content: center;
            min-height: 0;
            padding: 30px 20px 64px;
            overflow: visible;
        }

        .auth-form {
            width: 100%;
            max-width: 440px;
            margin: 0 auto;
        }

        .auth-form .back {
            margin-bottom: 10px;
        }

        .auth-form h2 {
            font-size: 25px;
        }

        .auth-intro {
            margin-bottom: 20px;
        }

        .auth-form form > label {
            margin-bottom: 7px;
        }

        .auth-form input:not([type=checkbox]) {
            height: 42px;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .password-field {
            margin-bottom: 14px;
        }

        .password-toggle {
            top: 5px;
            height: 32px;
        }

        .btn.wide {
            min-height: 42px;
            margin-top: 4px;
        }

        .auth-switch {
            margin-top: 16px;
            padding-bottom: 8px;
        }
    }

    /* MOBILE DENGAN TINGGI LAYAR PENDEK */
    @media (max-width: 560px) and (max-height: 700px) {
        .auth-page {
            padding-bottom: 24px;
        }

        .auth-form-panel {
            padding-top: 24px;
            padding-bottom: 56px;
        }

        .auth-intro {
            margin-bottom: 14px;
        }

        .auth-form input:not([type=checkbox]) {
            height: 38px;
            margin-bottom: 10px;
        }

        .password-field {
            margin-bottom: 10px;
        }

        .auth-switch {
            margin-top: 12px;
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
                <p class="eyebrow">UPN VETERAN JAKARTA</p>

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
                    Isi data dasar menggunakan email institusi yang sesuai dengan status Anda.
                </p>

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

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

                    <label for="email">Email Institusi</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email institusi"
                        autocomplete="email"
                        required
                    >

                    @error('email')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror

                    <label for="phone">Nomor Telepon</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone') }}"
                        placeholder="Masukkan nomor telepon aktif"
                        autocomplete="tel"
                        required
                    >

                    @error('phone')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror

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