
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

    .auth-slideshow img:nth-child(1) {
        animation-delay: 0s;
    }

    .auth-slideshow img:nth-child(2) {
        animation-delay: 5s;
    }

    .auth-slideshow img:nth-child(3) {
        animation-delay: 10s;
    }

    .auth-slideshow img:nth-child(4) {
        animation-delay: 15s;
    }

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
        0%, 5% {
            opacity: 0;
        }

        10%, 27% {
            opacity: 1;
        }

        32%, 100% {
            opacity: 0;
        }
    }

    .auth-form-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 0;
        min-height: 0;
        overflow: hidden;
        padding: clamp(20px, 3.5vw, 52px);
        background: #fff;
    }

    .auth-form {
        width: min(440px, 100%);
        max-height: 100%;
        overflow: hidden;
        margin: auto;
        color: #172b20;
    }

    .auth-form .back {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 clamp(12px, 2vh, 22px);
        color: #64736a;
        font-size: 12px;
        text-decoration: none;
    }

    .auth-form .back:hover {
        color: #16834a;
    }

    .auth-form h2 {
        margin: 0 0 7px;
        color: #172b20;
        font-size: clamp(24px, 2.3vw, 31px);
        line-height: 1.2;
        letter-spacing: -.8px;
    }

    .auth-intro {
        margin: 0 0 clamp(13px, 2vh, 20px);
        color: #68736c;
        font-size: 12px;
        line-height: 1.5;
    }

    .auth-form form {
        display: flex;
        flex-direction: column;
    }

    .auth-form form > label {
        margin: 0 0 5px;
        color: #20372a;
        font-size: 11px;
        font-weight: 700;
    }

    .auth-form input:not([type=checkbox]) {
        box-sizing: border-box;
        width: 100%;
        height: 39px;
        margin: 0 0 10px;
        padding: 0 12px;
        border: 1px solid #dce5e0;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #172b20;
        font: 400 12px "Plus Jakarta Sans", sans-serif;
    }

    .auth-form input:focus {
        border-color: #16834a;
        box-shadow: 0 0 0 3px rgba(22, 131, 74, .11);
    }

    .password-field {
        position: relative;
        margin: 0 0 10px;
    }

    .auth-form .password-field input {
        margin: 0;
        padding-right: 47px;
    }

    .password-toggle {
        position: absolute;
        top: 2px;
        right: 5px;
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
    }

    .password-toggle:hover {
        background: #eaf5ee;
        color: #16834a;
    }

    .password-toggle svg {
        width: 16px;
        height: 16px;
    }

    .btn.wide {
        width: 100%;
        min-height: 42px;
        margin-top: 3px;
        border: 1px solid #11643a;
        border-radius: 8px;
        background: #16834a !important;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .btn.wide:hover {
        background: #11643a !important;
    }

    .auth-switch {
        margin: 15px 0 0;
        color: #68736c;
        font-size: 12px;
        text-align: center;
    }

    .auth-switch a {
        color: #16834a !important;
        font-weight: 700;
        text-decoration: none;
    }

    .auth-error {
        margin: -6px 0 8px;
        color: #b32e2e;
        font-size: 10px;
    }

    @media (max-width: 760px) {
        .auth-page {
            padding: 10px;
        }

        .auth-shell {
            grid-template-columns: minmax(0, .82fr) minmax(0, 1.18fr);
            border-radius: 12px;
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
            padding: 14px;
        }
    }

    @media (max-width: 560px) {
        .auth-shell {
            grid-template-columns: 1fr;
            grid-template-rows: minmax(115px, 23%) minmax(0, 1fr);
        }

        .auth-art {
            padding: 14px 20px;
        }

        .auth-art h1 {
            font-size: 20px;
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
            padding: 10px 20px;
        }

        .auth-form .back {
            margin-bottom: 7px;
        }

        .auth-intro {
            margin-bottom: 9px;
        }

        .auth-form input:not([type=checkbox]) {
            height: 34px;
            margin-bottom: 7px;
        }

        .password-field {
            margin-bottom: 7px;
        }

        .password-toggle {
            top: 0;
            height: 32px;
        }

        .auth-switch {
            margin-top: 9px;
        }

        .btn.wide {
            min-height: 36px;
        }
    }

    @media (max-height: 700px) and (min-width: 561px) {
        .auth-form-panel {
            padding: 12px 28px;
        }

        .auth-form .back {
            margin-bottom: 7px;
        }

        .auth-intro {
            margin-bottom: 9px;
        }

        .auth-form input:not([type=checkbox]) {
            height: 34px;
            margin-bottom: 7px;
        }

        .password-field {
            margin-bottom: 7px;
        }

        .password-toggle {
            top: 0;
            height: 32px;
        }

        .auth-switch {
            margin-top: 9px;
        }

        .btn.wide {
            min-height: 36px;
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
                    Lengkapi informasi berikut untuk membuat akun peminjam.
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

                    <label for="nim_nip">NIM/NIP</label>
                    <input
                        id="nim_nip"
                        name="nim_nip"
                        type="text"
                        value="{{ old('nim_nip') }}"
                        placeholder="Masukkan NIM/NIP"
                        required
                    >

                    @error('nim_nip')
                        <p class="auth-error">{{ $message }}</p>
                    @enderror

                    <label for="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email"
                        autocomplete="email"
                        required
                    >

                    @error('email')
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
        if (window.lucide) window.lucide.createIcons();

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

                if (window.lucide) window.lucide.createIcons();
            });
        });
    });
</script>

@endsection
