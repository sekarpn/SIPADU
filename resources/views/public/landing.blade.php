
@extends('layouts.app')

@section('content')

<style>
    /* Membuat ikon dan tulisan fitur sejajar */
    .feature-grid article b {
        display: flex;
        align-items: center;
        gap: 10px;
        line-height: 1.4;
    }

    .feature-grid article b i,
    .feature-grid article b svg {
        width: 22px;
        height: 22px;
        flex-shrink: 0;
        display: block;
    }

    /* LOGO SIPADU TRANSPARAN */
    .public-nav .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .public-nav .brand-icon {
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

    .public-nav .brand-icon img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: transparent;

        /* BAYANGAN DI BELAKANG LOGO */
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.45));
    }

    .public-nav .brand b {
        display: block;
    }

    .public-nav .brand small {
        display: block;
    }

    /* Ikon tombol login */
    .hero-copy .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .hero-copy .btn svg {
        width: 18px;
        height: 18px;
    }
</style>

<main class="landing">

    <nav class="public-nav">
        <a class="brand" href="/">
            <span class="brand-icon">
                <img src="{{ asset('images/logosipadu.png') }}"
                     alt="Logo SIPADU">
            </span>

            <span>
                <b>SIPADU</b>
                <small>Sistem Peminjaman Auditorium UPNVJ</small>
            </span>
        </a>
    </nav>

    <section class="hero">

        <div class="hero-copy">
            <p class="eyebrow">UPN VETERAN JAKARTA</p>

            <h1>Peminjaman auditorium, lebih tertata dan mudah diakses.</h1>

            <p>
                Sistem informasi untuk membantu proses pengajuan dan pengelolaan
                peminjaman auditorium di lingkungan Universitas Pembangunan
                Nasional Veteran Jakarta.
            </p>

            <a class="btn" href="{{ route('login') }}">
                Login ke SIPADU
                <i data-lucide="arrow-up-right"></i>
            </a>

            <small>Gunakan akun yang telah terdaftar untuk mengakses sistem.</small>
        </div>

        <div class="hero-photo">
            <div class="slides">
                <img src="{{ asset('images/slideshow/upnvjnew.jpg') }}" alt="Foto auditorium 2">
                <img src="{{ asset('images/slideshow/auditbti.jpg') }}" alt="Foto auditorium 3">
                <img src="{{ asset('images/slideshow/fk.jpg') }}" alt="Foto auditorium 4">
            </div>

            <div class="photo-caption">
                Sistem Peminjaman Auditorium<br>
                <b>UPN Veteran Jakarta</b>
            </div>
        </div>

    </section>

    <section class="feature-grid">

        <article>
            <b>
                <i data-lucide="clipboard-list"></i>
                <span>Pengajuan Peminjaman</span>
            </b>
            <p>
                Ajukan penggunaan auditorium dengan mengisi informasi kegiatan
                dan kebutuhan ruangan.
            </p>
        </article>

        <article>
            <b>
                <i data-lucide="clipboard-check"></i>
                <span>Proses Verifikasi</span>
            </b>
            <p>
                Pantau pengajuan sesuai proses pemeriksaan dan keputusan admin.
            </p>
        </article>

        <article>
            <b>
                <i data-lucide="building-2"></i>
                <span>Informasi Auditorium</span>
            </b>
            <p>
                Lihat informasi auditorium yang dikelola dalam sistem peminjaman UPNVJ.
            </p>
        </article>

    </section>

    <section class="public-info">

        <div>
            <p class="eyebrow">INFORMASI LAYANAN</p>
            <h2>Ketentuan dan alur peminjaman</h2>
        </div>

        <div class="info-columns">

            @forelse($contents as $c)

                <article class="info-card">
                    <h3>{{ $c->title }}</h3>

                    <div class="rich-text">
                        {!! nl2br(e(str_replace('\\n', "\n", $c->body))) !!}
                    </div>

                    @if($c->key === 'sop')
                        <p>
                            <a href="http://bit.ly/AlurPeminjamanRuanganPerFakultas"
                               target="_blank"
                               rel="noopener">
                                bit.ly/AlurPeminjamanRuanganPerFakultas ↗
                            </a>
                            <br>
                            <small>Link hanya dapat diakses menggunakan email UPNVJ.</small>
                        </p>
                    @endif
                </article>

            @empty

                <article class="info-card">
                    <h3>Informasi peminjaman</h3>
                    <p>Konten informasi akan ditampilkan di sini.</p>
                </article>

            @endforelse

        </div>

    </section>

    <footer>
        © {{ date('Y') }} SIPADU · UPN Veteran Jakarta
    </footer>

</main>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>

@endsection
