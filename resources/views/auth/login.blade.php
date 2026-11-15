
@extends('layouts.app')

@section('content')

<style>
    .auth-art {
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .auth-slideshow {
        position: absolute;
        inset: 0;
        z-index: -2;
    }

    .auth-slideshow img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        animation: authSlide 16s infinite;
    }

    .auth-slideshow img:nth-child(1) {
        animation-delay: 0s;
    }

    .auth-slideshow img:nth-child(2) {
        animation-delay: 4s;
    }

    .auth-slideshow img:nth-child(3) {
        animation-delay: 8s;
    }

    .auth-slideshow img:nth-child(4) {
        animation-delay: 12s;
    }

    .auth-art::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(
            135deg,
            rgba(10, 35, 65, 0.82),
            rgba(10, 35, 65, 0.45)
        );
        z-index: -1;
    }

    .auth-art > *:not(.auth-slideshow) {
        position: relative;
        z-index: 1;
    }

    @keyframes authSlide {
        0% {
            opacity: 0;
        }
        8% {
            opacity: 1;
        }
        25% {
            opacity: 1;
        }
        33% {
            opacity: 0;
        }
        100% {
            opacity: 0;
        }
    }

    /* LOGO SIPADU */
    .auth-art .brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .auth-art .brand-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 58px;
        min-width: 58px;
        padding: 0;
        overflow: visible;
        border-radius: 0;
        background: transparent;
    }

    /* LOGO DENGAN EFEK BAYANGAN */
    .auth-art .brand-icon img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: transparent;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.45));
    }
</style>

<div class="auth-shell">

    <div class="auth-art">

        <div class="auth-slideshow">
            <img src="{{ asset('images/slideshow/upnvjnew.jpg') }}" alt="Foto auditorium 1">
            <img src="{{ asset('images/slideshow/auditbti.jpg') }}" alt="Foto auditorium 2">
            <img src="{{ asset('images/slideshow/fk.jpg') }}" alt="Foto auditorium 3">
            <img src="{{ asset('images/slideshow/auditft.jpg') }}" alt="Foto auditorium 4">
        </div>

        <div class="brand inverse">
            <span class="brand-icon">
                <img src="{{ asset('images/logosipadu.png') }}" alt="Logo SIPADU">
            </span>
            <b>SIPADU</b>
        </div>

        <div>
            <p class="eyebrow">UPN VETERAN JAKARTA</p>

            <h1>Layanan peminjaman auditorium dalam satu portal.</h1>

            <p>
                Kelola pengajuan dan informasi jadwal auditorium
                dengan lebih mudah.
            </p>
        </div>

    </div>

    <div class="auth-form">

        <a href="/" class="back"><i data-lucide="arrow-left"></i><span>Kembali ke landing page</span></a>

        <h2>Login</h2>
        <p>Masuk menggunakan email dan password akun Anda.</p>

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <label>Email</label>
            <input
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
            >

            <label>Password</label>
            <input
                name="password"
                type="password"
                required
            >

            <label class="remember">
                <input type="checkbox" name="remember">
                Ingat saya
            </label>

            <button class="btn wide">Masuk</button>
        </form>

        <p class="auth-switch">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar</a>
        </p>

    </div>

</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>

@endsection
