
@extends('layouts.panel')

@section('panel')

@php
    $user = auth()->user();
    $isAdmin = $user->role === 'admin';
    $isEmployee = $user->role === 'pegawai';
    $profileUpdateRoute = $isAdmin ? route('admin.profile.update') : ($isEmployee ? route('pegawai.profile.update') : route('borrower.profile.update'));
    $profilePasswordRoute = $isAdmin ? route('admin.profile.password') : ($isEmployee ? route('pegawai.profile.password') : route('borrower.profile.password'));
@endphp

<style>
    /* HALAMAN PROFIL */
    .profile-page {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0 0 35px;
        box-sizing: border-box;
    }

    .profile-heading {
        margin-bottom: 25px;
    }

    .profile-heading h1 {
        font-size: 27px;
        font-weight: 750;
        color: #183b2b;
        margin: 0 0 7px;
    }

    .profile-heading p {
        color: #718078;
        margin: 0;
        font-size: 14px;
    }

    /* BANNER PROFIL */
    .profile-banner {
        width: 100%;
        box-sizing: border-box;
        background: linear-gradient(120deg, #145c3b, #238653);
        border-radius: 18px;
        padding: 28px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
        box-shadow: 0 8px 22px rgba(20, 92, 59, .12);
    }

    .profile-avatar {
        width: 75px;
        height: 75px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        border: 2px solid rgba(255,255,255,.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .profile-banner h2 {
        margin: 0 0 6px;
        font-size: 21px;
        font-weight: 700;
    }

    .profile-banner p {
        margin: 0;
        color: rgba(255,255,255,.85);
        font-size: 14px;
    }

    .profile-role {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 12px;
        border-radius: 20px;
        background: rgba(255,255,255,.17);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .5px;
    }

    /* GRID KARTU PROFIL */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
        align-items: stretch;
        width: 100%;
    }

    /* KEDUA KARTU DIBUAT SEIMBANG */
    .profile-card {
        width: 100%;
        min-width: 0;
        height: 100%;
        box-sizing: border-box;
        background: white;
        border: 1px solid #e8eee9;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,.035);
        display: flex;
        flex-direction: column;
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 23px;
        padding-bottom: 18px;
        border-bottom: 1px solid #edf1ee;
        flex-shrink: 0;
    }

    .profile-icon {
        width: 43px;
        height: 43px;
        border-radius: 12px;
        background: #e9f5ed;
        color: #21804d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    .profile-card-header h3 {
        font-size: 17px;
        color: #244332;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .profile-card-header p {
        font-size: 12px;
        color: #849188;
        margin: 0;
    }

    /* FORM */
    .profile-card form {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .profile-field {
        margin-bottom: 17px;
        min-width: 0;
    }

    .profile-field label {
        display: block;
        color: #405449;
        font-size: 13px;
        font-weight: 650;
        margin-bottom: 8px;
    }

    .profile-field input {
        display: block;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 12px 14px;
        border: 1px solid #dce5de;
        border-radius: 9px;
        background: #fff;
        color: #304638;
        font-size: 13px;
        outline: none;
        transition: .2s;
    }

    .profile-field input:focus {
        border-color: #278653;
        box-shadow: 0 0 0 3px rgba(39,134,83,.1);
    }

    .profile-field input:disabled {
        background: #f2f5f3;
        color: #89958d;
        cursor: not-allowed;
    }

    .profile-hint {
        font-size: 11px;
        color: #8b978f;
        margin-top: 6px;
    }

    /* TOMBOL SEJAJAR DI BAGIAN BAWAH */
    .profile-button {
        width: 100%;
        box-sizing: border-box;
        border: none;
        border-radius: 9px;
        background: #21804d;
        color: white;
        padding: 13px 18px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
        margin-top: auto;
    }

    .profile-button:hover {
        background: #17683d;
    }

    /* NOTIFIKASI */
    .profile-alert {
        padding: 13px 16px;
        border-radius: 9px;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .profile-alert.success {
        color: #17683d;
        background: #e9f7ee;
        border: 1px solid #c9ead5;
    }

    .profile-alert.error {
        color: #a52e2e;
        background: #fff0f0;
        border: 1px solid #f4cccc;
    }

    .profile-error {
        color: #c0392b;
        font-size: 11px;
        margin-top: 5px;
    }

    /* TABLET */
    @media (max-width: 900px) {
        .profile-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .profile-card {
            height: auto;
        }

        .profile-button {
            margin-top: 5px;
        }
    }

    /* HP */
    @media (max-width: 500px) {
        .profile-banner {
            padding: 20px;
        }

        .profile-avatar {
            width: 58px;
            height: 58px;
            font-size: 23px;
        }

        .profile-banner h2 {
            font-size: 17px;
        }

        .profile-card {
            padding: 19px;
        }
    }
</style>

<div class="profile-page">

    <div class="profile-heading">
        <h1>Profil Akun</h1>
        <p>Kelola informasi pribadi dan keamanan akun SIPADU.</p>
    </div>

    @if(session('profile_incomplete'))
        <div class="profile-alert error">
            {{ session('profile_incomplete') }} Isi data identitas yang masih kosong, lalu simpan perubahan.
        </div>
    @endif

    {{-- BANNER PROFIL --}}
    <div class="profile-banner">
        <div class="profile-avatar">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>

        <div>
            <h2>{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>

            <span class="profile-role">
                {{ $isAdmin ? 'ADMINISTRATOR' : ($isEmployee ? 'PEGAWAI' : 'MAHASISWA') }}
            </span>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="profile-alert success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('password_success'))
        <div class="profile-alert success">
            {{ session('password_success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="profile-alert error">
            <strong>Periksa kembali data yang kamu masukkan.</strong>
            <ul style="margin:8px 0 0 18px;padding:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-grid">

        {{-- INFORMASI PRIBADI --}}
        <div class="profile-card">

            <div class="profile-card-header">
                <div class="profile-icon">👤</div>
                <div>
                    <h3>Informasi {{ $isAdmin ? 'Administrator' : ($isEmployee ? 'Pegawai' : 'Mahasiswa') }}</h3>
                    <p>Perbarui informasi profil sesuai identitas akun.</p>
                </div>
            </div>

            <form method="POST"
                  action="{{ $profileUpdateRoute }}">

                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label>Nama Lengkap</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name', $user->name) }}"
                           placeholder="Masukkan nama lengkap"
                           required>

                    @error('name')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-field">
                    <label>Email</label>
                    <input type="email"
                           name="email"
                           value="{{ old('email', $user->email) }}"
                           placeholder="Masukkan email"
                           required>

                    @error('email')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                @if($isAdmin)
                    <div class="profile-field">
                        <label>ID Admin</label>
                        <input type="text" value="Admin" disabled>
                    </div>
                @else
                    <div class="profile-field">
                        <label>{{ $isEmployee ? 'NIP' : 'NIM' }}</label>
                        <input type="text"
                               name="nim_nip"
                               value="{{ old('nim_nip', $user->nim_nip ?? $user->nim_np) }}"
                               placeholder="Masukkan {{ $isEmployee ? 'NIP' : 'NIM' }}"
                               required>
                        @error('nim_nip')
                            <div class="profile-error">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($isEmployee)
                        <div class="profile-field">
                            <label>Unit / Biro Kerja</label>
                            <input name="employee_unit" value="{{ old('employee_unit', $user->employee_unit) }}" placeholder="Masukkan unit atau biro" required>
                            @error('employee_unit') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="profile-field">
                            <label>Jabatan</label>
                            <input name="position" value="{{ old('position', $user->position) }}" placeholder="Masukkan jabatan" required>
                            @error('position') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                    @else
                        <div class="profile-field">
                            <label>Fakultas</label>
                            <input name="faculty" value="{{ old('faculty', $user->faculty) }}" placeholder="Masukkan fakultas" required>
                            @error('faculty') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="profile-field">
                            <label>Program Studi</label>
                            <input name="study_program" value="{{ old('study_program', $user->study_program) }}" placeholder="Masukkan program studi" required>
                            @error('study_program') <div class="profile-error">{{ $message }}</div> @enderror
                        </div>
                    @endif
                @endif

                <div class="profile-field">
                    <label>Nomor Telepon</label>
                    <input type="tel"
                           name="phone"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="Contoh: 081234567890">

                    @error('phone')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="profile-button">
                    Simpan Perubahan Profil
                </button>

            </form>
        </div>

        {{-- KEAMANAN AKUN --}}
        <div class="profile-card">

            <div class="profile-card-header">
                <div class="profile-icon">🔒</div>
                <div>
                    <h3>Keamanan Akun</h3>
                    <p>Ubah password untuk menjaga keamanan akun.</p>
                </div>
            </div>

            <form method="POST"
                  action="{{ $profilePasswordRoute }}">

                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label>Password Lama</label>
                    <input type="password"
                           name="current_password"
                           placeholder="Masukkan password lama"
                           required
                           autocomplete="current-password">

                    @error('current_password')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-field">
                    <label>Password Baru</label>
                    <input type="password"
                           name="password"
                           placeholder="Minimal 8 karakter"
                           required
                           autocomplete="new-password">

                    @error('password')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-field">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password"
                           name="password_confirmation"
                           placeholder="Ulangi password baru"
                           required
                           autocomplete="new-password">
                </div>

                <div class="profile-hint" style="margin-bottom:18px;">
                    Pastikan password baru mudah kamu ingat dan tidak dibagikan kepada orang lain.
                </div>

                <button type="submit" class="profile-button">
                    Ubah Password
                </button>

            </form>
        </div>

    </div>
</div>

@endsection
