@extends('layouts.panel')

@section('panel')

@php
    use Illuminate\Support\Facades\Route;

    $user = auth()->user();
    $role = strtolower((string) data_get($user, 'role', 'mahasiswa'));
    $isAdmin = $role === 'admin';
    $isEmployee = $role === 'pegawai';

    if ($isAdmin && Route::has('admin.profile.update')) {
        $profileUpdateRoute = route('admin.profile.update');
    } elseif ($isEmployee && Route::has('pegawai.profile.update')) {
        $profileUpdateRoute = route('pegawai.profile.update');
    } elseif (Route::has('borrower.profile.update')) {
        $profileUpdateRoute = route('borrower.profile.update');
    } elseif (Route::has('profile.update')) {
        $profileUpdateRoute = route('profile.update');
    } else {
        $profileUpdateRoute = url()->current();
    }

    if ($isAdmin && Route::has('admin.profile.password')) {
        $profilePasswordRoute = route('admin.profile.password');
    } elseif ($isEmployee && Route::has('pegawai.profile.password')) {
        $profilePasswordRoute = route('pegawai.profile.password');
    } elseif (Route::has('borrower.profile.password')) {
        $profilePasswordRoute = route('borrower.profile.password');
    } elseif (Route::has('profile.password')) {
        $profilePasswordRoute = route('profile.password');
    } else {
        $profilePasswordRoute = url()->current();
    }

    $userName = (string) data_get($user, 'name', 'Pengguna');
    $userEmail = (string) data_get($user, 'email', '');
    $initial = mb_strtoupper(mb_substr(trim($userName) !== '' ? trim($userName) : 'U', 0, 1));
@endphp

<style>
    .profile-page {
        --pf-ink: #163626;
        --pf-muted: #4c6456;
        --pf-subtle: #64786c;
        --pf-line: rgba(22, 54, 38, .09);
        --pf-green: #1f7a49;
        --pf-green-mid: #237c4d;
        --pf-glass-bg: linear-gradient(
            145deg,
            rgba(255, 255, 255, .68) 0%,
            rgba(255, 255, 255, .42) 100%
        );
        --pf-glass-border: 1px solid rgba(255, 255, 255, .55);
        --pf-glass-shadow: 0 8px 28px rgba(22, 54, 38, .06);
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0 0 35px;
        box-sizing: border-box;
        color: var(--pf-ink);
        font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    }

    .profile-heading {
        margin-bottom: 22px;
    }

    .profile-heading h1 {
        font-size: 25px;
        font-weight: 800;
        letter-spacing: -.03em;
        color: var(--pf-ink);
        margin: 0;
    }

    .profile-heading p {
        color: var(--pf-muted);
        margin: 6px 0 0;
        font-size: 13px;
        line-height: 1.55;
    }

    /* BANNER PROFIL */
    .profile-banner {
        width: 100%;
        box-sizing: border-box;
        background: linear-gradient(125deg, #164e34 0%, #237c4d 100%);
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 18px;
        padding: 26px 28px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 22px;
        box-shadow: 0 10px 26px rgba(20, 92, 59, .14);
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .18);
        border: 2px solid rgba(255, 255, 255, .52);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        flex-shrink: 0;
    }

    .profile-banner h2 {
        margin: 0 0 4px;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .profile-banner p {
        margin: 0;
        color: rgba(255, 255, 255, .86);
        font-size: 13.5px;
    }

    .profile-role {
        display: inline-block;
        margin-top: 10px;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .18);
        border: 1px solid rgba(255, 255, 255, .25);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .05em;
    }

    /* GRID KARTU PROFIL */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: stretch;
        width: 100%;
    }

    /* KARTU GLASSMORPHISM */
    .profile-card {
        width: 100%;
        min-width: 0;
        height: 100%;
        box-sizing: border-box;
        background: var(--pf-glass-bg);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: var(--pf-glass-border);
        border-radius: 16px;
        padding: 24px 26px;
        box-shadow: var(--pf-glass-shadow);
        display: flex;
        flex-direction: column;
    }

    .profile-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--pf-line);
        flex-shrink: 0;
    }

    .profile-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(33, 128, 77, .14);
        border: 1px solid rgba(255, 255, 255, .45);
        color: var(--pf-green);
        display: grid;
        place-items: center;
        flex-shrink: 0;
    }

    .profile-icon svg {
        width: 19px;
        height: 19px;
    }

    .profile-card-header h3 {
        font-size: 16px;
        color: var(--pf-ink);
        font-weight: 800;
        letter-spacing: -.01em;
        margin: 0 0 3px;
    }

    .profile-card-header p {
        font-size: 12px;
        color: var(--pf-subtle);
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
        margin-bottom: 16px;
        min-width: 0;
    }

    .profile-field label {
        display: block;
        color: #294735;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .profile-input-wrap {
        position: relative;
    }

    .profile-field input {
        display: block;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 11px 13px;
        border: 1px solid rgba(22, 54, 38, .13);
        border-radius: 11px;
        background: rgba(255, 255, 255, .82);
        color: var(--pf-ink);
        font-family: inherit;
        font-size: 13px;
        font-weight: 500;
        outline: none;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .profile-input-wrap input {
        padding-right: 40px;
    }

    .profile-field input:focus {
        background: #fff;
        border-color: var(--pf-green-mid);
        box-shadow: 0 0 0 3px rgba(35, 124, 77, .14);
    }

    .profile-field input:disabled {
        background: rgba(22, 54, 38, .05);
        color: var(--pf-subtle);
        cursor: not-allowed;
    }

    .profile-eye-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 28px;
        height: 28px;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: var(--pf-subtle);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color .18s ease, background .18s ease;
    }

    .profile-eye-btn:hover {
        background: rgba(22, 54, 38, .06);
        color: var(--pf-ink);
    }

    .profile-eye-btn svg {
        width: 16px;
        height: 16px;
    }

    .profile-hint {
        font-size: 11.5px;
        color: var(--pf-subtle);
        line-height: 1.5;
        margin-top: 4px;
    }

    /* TOMBOL SEJAJAR DI BAGIAN BAWAH */
    .profile-button {
        width: 100%;
        box-sizing: border-box;
        border: none;
        border-radius: 11px;
        background: var(--pf-green-mid);
        color: #fff;
        padding: 12px 18px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(35, 124, 77, .22);
        transition: background .2s ease, transform .18s ease;
        margin-top: auto;
    }

    .profile-button:hover {
        background: #1b663f;
        transform: translateY(-1px);
    }

    /* NOTIFIKASI */
    .profile-alert {
        padding: 13px 16px;
        border-radius: 14px;
        margin-bottom: 18px;
        font-size: 12.5px;
        font-weight: 600;
    }

    .profile-alert.success {
        color: #166534;
        background: rgba(220, 252, 231, .88);
        border: 1px solid rgba(74, 222, 128, .35);
    }

    .profile-alert.error {
        color: #991b1b;
        background: rgba(254, 226, 226, .88);
        border: 1px solid rgba(248, 113, 113, .35);
    }

    .profile-error {
        color: #b91c1c;
        font-size: 11.5px;
        font-weight: 600;
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
            margin-top: 8px;
        }
    }

    /* HP */
    @media (max-width: 500px) {
        .profile-heading h1 {
            font-size: 21px;
        }

        .profile-banner {
            padding: 20px;
        }

        .profile-avatar {
            width: 56px;
            height: 56px;
            font-size: 22px;
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
            {{ $initial }}
        </div>

        <div>
            <h2>{{ $userName }}</h2>
            <p>{{ $userEmail }}</p>

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
            <ul style="margin:6px 0 0 18px;padding:0;font-weight:500;">
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
                <div class="profile-icon">
                    <i data-lucide="user"></i>
                </div>
                <div>
                    <h3>Informasi {{ $isAdmin ? 'Administrator' : ($isEmployee ? 'Pegawai' : 'Mahasiswa') }}</h3>
                    <p>Perbarui informasi profil sesuai identitas akun.</p>
                </div>
            </div>

            <form method="POST" action="{{ $profileUpdateRoute }}">
                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label for="profileName">Nama Lengkap</label>
                    <input id="profileName"
                           type="text"
                           name="name"
                           value="{{ old('name', $userName) }}"
                           placeholder="Masukkan nama lengkap"
                           required>
                    @error('name')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="profileEmail">Email</label>
                    <input id="profileEmail"
                           type="email"
                           name="email"
                           value="{{ old('email', $userEmail) }}"
                           placeholder="Masukkan email"
                           required>
                    @error('email')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                @if($isAdmin)
                    <div class="profile-field">
                        <label>Peran Akun</label>
                        <input type="text" value="Administrator" disabled>
                    </div>
                @else
                    <div class="profile-field">
                        <label for="profileNimNip">{{ $isEmployee ? 'NIP' : 'NIM' }}</label>
                        <input id="profileNimNip"
                               type="text"
                               name="nim_nip"
                               value="{{ old('nim_nip', data_get($user, 'nim_nip')) }}"
                               placeholder="Masukkan {{ $isEmployee ? 'NIP' : 'NIM' }}"
                               required>
                        @error('nim_nip')
                            <div class="profile-error">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($isEmployee)
                        <div class="profile-field">
                            <label for="profileEmployeeUnit">Unit / Biro Kerja</label>
                            <input id="profileEmployeeUnit"
                                   name="employee_unit"
                                   value="{{ old('employee_unit', data_get($user, 'employee_unit')) }}"
                                   placeholder="Masukkan unit atau biro"
                                   required>
                            @error('employee_unit')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="profile-field">
                            <label for="profilePosition">Jabatan</label>
                            <input id="profilePosition"
                                   name="position"
                                   value="{{ old('position', data_get($user, 'position')) }}"
                                   placeholder="Masukkan jabatan"
                                   required>
                            @error('position')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="profile-field">
                            <label for="profileFaculty">Fakultas</label>
                            <input id="profileFaculty"
                                   name="faculty"
                                   value="{{ old('faculty', data_get($user, 'faculty')) }}"
                                   placeholder="Masukkan fakultas"
                                   required>
                            @error('faculty')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="profile-field">
                            <label for="profileStudyProgram">Program Studi</label>
                            <input id="profileStudyProgram"
                                   name="study_program"
                                   value="{{ old('study_program', data_get($user, 'study_program')) }}"
                                   placeholder="Masukkan program studi"
                                   required>
                            @error('study_program')
                                <div class="profile-error">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                @endif

                <div class="profile-field">
                    <label for="profilePhone">Nomor Telepon</label>
                    <input id="profilePhone"
                           type="tel"
                           name="phone"
                           value="{{ old('phone', data_get($user, 'phone')) }}"
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
                <div class="profile-icon">
                    <i data-lucide="lock"></i>
                </div>
                <div>
                    <h3>Keamanan Akun</h3>
                    <p>Ubah password untuk menjaga keamanan akun.</p>
                </div>
            </div>

            <form method="POST" action="{{ $profilePasswordRoute }}">
                @csrf
                @method('PUT')

                <div class="profile-field">
                    <label for="currentPasswordInput">Password Lama</label>
                    <div class="profile-input-wrap">
                        <input id="currentPasswordInput"
                               type="password"
                               name="current_password"
                               placeholder="Masukkan password lama"
                               required
                               autocomplete="current-password">
                        <button type="button" class="profile-eye-btn" data-toggle-password="currentPasswordInput" aria-label="Tampilkan password">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="newPasswordInput">Password Baru</label>
                    <div class="profile-input-wrap">
                        <input id="newPasswordInput"
                               type="password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               minlength="8"
                               required
                               autocomplete="new-password">
                        <button type="button" class="profile-eye-btn" data-toggle-password="newPasswordInput" aria-label="Tampilkan password">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="confirmPasswordInput">Konfirmasi Password Baru</label>
                    <div class="profile-input-wrap">
                        <input id="confirmPasswordInput"
                               type="password"
                               name="password_confirmation"
                               placeholder="Ulangi password baru"
                               minlength="8"
                               required
                               autocomplete="new-password">
                        <button type="button" class="profile-eye-btn" data-toggle-password="confirmPasswordInput" aria-label="Tampilkan password">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="profile-error">{{ $message }}</div>
                    @enderror
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = document.getElementById(btn.dataset.togglePassword);
            if (!target) return;
            const isHidden = target.type === 'password';
            target.type = isHidden ? 'text' : 'password';
            btn.innerHTML = isHidden ? '<i data-lucide="eye-off"></i>' : '<i data-lucide="eye"></i>';
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    });

    if (window.lucide) {
        window.lucide.createIcons();
    }
});
</script>

@endsection