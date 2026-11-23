@extends('layouts.panel')

@section('panel')
@php
    $selectedRole = old('role', $user->role);
    $initials = mb_strtoupper(mb_substr(trim($user->name ?? 'US'), 0, 2));
@endphp

<style>
.user-edit-page {
    --ue-ink: #163626;
    --ue-muted: #4c6456;
    --ue-subtle: #64786c;
    --ue-line: rgba(22, 54, 38, .09);
    --ue-green: #1f7a49;
    --ue-green-mid: #237c4d;
    width: 100%;
    box-sizing: border-box;
    padding: 0 0 32px;
    color: var(--ue-ink);
    font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}

.user-edit-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 22px;
}

.user-edit-header h1 {
    margin: 0;
    font-size: 25px;
    font-weight: 800;
    letter-spacing: -.03em;
    color: var(--ue-ink);
}

.user-edit-header p {
    margin: 6px 0 0;
    color: var(--ue-muted);
    font-size: 13px;
    line-height: 1.55;
}

/* GLASSMORPHISM CARD MATCHING DASHBOARD ADMIN */
.user-edit-card {
    background: linear-gradient(
        145deg,
        rgba(255, 255, 255, .68) 0%,
        rgba(255, 255, 255, .42) 100%
    );
    backdrop-filter: blur(18px) saturate(150%);
    -webkit-backdrop-filter: blur(18px) saturate(150%);
    border: 1px solid rgba(255, 255, 255, .55);
    box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
    border-radius: 16px;
    padding: 24px 26px;
    max-width: 960px;
}

/* IDENTITY BANNER INSIDE CARD */
.user-edit-identity {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    padding-bottom: 20px;
    margin-bottom: 22px;
    border-bottom: 1px solid var(--ue-line);
}

.user-edit-identity-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.user-edit-avatar {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: var(--ue-green-mid);
    color: #fff;
    display: grid;
    place-items: center;
    font-size: 16px;
    font-weight: 800;
    box-shadow: 0 4px 12px rgba(35, 124, 77, .22);
    flex-shrink: 0;
}

.user-edit-identity-info h2 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: var(--ue-ink);
}

.user-edit-identity-info span {
    display: block;
    margin-top: 3px;
    font-size: 12px;
    color: var(--ue-subtle);
    font-weight: 500;
}

.role-pill {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 800;
    background: #bbf7d0;
    color: #065f46;
    border: 1px solid #34d399;
}

.role-pill.admin {
    background: #ede9fe;
    color: #5b21b6;
    border: 1px solid #c4b5fd;
}

.role-pill.pegawai {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
}

/* FORM GRID & GLASS INPUTS */
.user-edit-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px 20px;
}

.user-edit-field label {
    display: block;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 7px;
    color: #294735;
}

.user-edit-password-wrap {
    position: relative;
    width: 100%;
}

.user-edit-password-wrap input {
    padding-right: 44px;
}

.user-edit-password-toggle {
    position: absolute;
    top: 50%;
    right: 9px;
    transform: translateY(-50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    padding: 0;
    border: 0;
    border-radius: 7px;
    background: transparent;
    color: #718078;
    cursor: pointer;
}

.user-edit-password-toggle:hover {
    background: rgba(35, 124, 77, .08);
    color: var(--ue-green-mid);
}

.user-edit-password-toggle svg {
    width: 17px;
    height: 17px;
}

.user-edit-field input,
.user-edit-field select {
    width: 100%;
    box-sizing: border-box;
    padding: 11px 13px;
    border: 1px solid rgba(22, 54, 38, .13);
    border-radius: 11px;
    background: rgba(255, 255, 255, .82);
    font: inherit;
    font-size: 13px;
    font-weight: 500;
    color: var(--ue-ink);
    outline: none;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.user-edit-field input:focus,
.user-edit-field select:focus {
    background: #fff;
    border-color: var(--ue-green-mid);
    box-shadow: 0 0 0 3px rgba(35, 124, 77, .14);
}

.user-edit-field small {
    display: block;
    color: var(--ue-subtle);
    margin-top: 6px;
    font-size: 11px;
    font-weight: 500;
}

.user-edit-field-error {
    color: #b91c1c;
    font-size: 11.5px;
    font-weight: 600;
    margin-top: 5px;
}

.user-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid var(--ue-line);
}

.user-edit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid transparent;
    border-radius: 11px;
    padding: 10px 18px;
    background: var(--ue-green-mid);
    color: #fff;
    text-decoration: none;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(35, 124, 77, .22);
    transition: background .2s ease, transform .18s ease;
}

.user-edit-btn:hover {
    background: #1b663f;
    transform: translateY(-1px);
}

.user-edit-btn.secondary {
    background: rgba(255, 255, 255, .78);
    border-color: rgba(22, 54, 38, .14);
    color: #294735;
    box-shadow: none;
}

.user-edit-btn.secondary:hover {
    background: #fff;
    color: var(--ue-ink);
}

.user-edit-alert {
    padding: 13px 16px;
    border-radius: 14px;
    margin-bottom: 16px;
    font-size: 12.5px;
    font-weight: 600;
    max-width: 960px;
    box-sizing: border-box;
}

.user-edit-alert.error {
    background: rgba(254, 226, 226, .88);
    border: 1px solid rgba(248, 113, 113, .35);
    color: #991b1b;
}

.user-edit-alert.success {
    background: rgba(220, 252, 231, .88);
    border: 1px solid rgba(74, 222, 128, .35);
    color: #166534;
}

@media (max-width: 650px) {
    .user-edit-header h1 { font-size: 21px; }
    .user-edit-grid { grid-template-columns: 1fr; }
    .user-edit-card { padding: 18px; }
}
</style>

<div class="user-edit-page">
    <div class="user-edit-header">
        <div>
            <h1>Edit Data Pengguna</h1>
            <p>Perbarui informasi akun, identitas akademik/kepegawaian, dan hak akses pengguna.</p>
        </div>
        <a class="user-edit-btn secondary" href="{{ route('admin.users') }}">← Kembali</a>
    </div>

    @if(session('success'))
        <div class="user-edit-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="user-edit-alert error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="user-edit-alert error">
            Periksa kembali data yang dimasukkan. {{ $errors->first() }}
        </div>
    @endif

    <section class="user-edit-card">
        <div class="user-edit-identity">
            <div class="user-edit-identity-left">
                <div class="user-edit-avatar">
                    {{ $initials }}
                </div>
                <div class="user-edit-identity-info">
                    <h2>{{ $user->name }}</h2>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
            <span class="role-pill {{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="user-edit-grid">
                <div class="user-edit-field">
                    <label for="editUserName">Nama Lengkap</label>
                    <input id="editUserName" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field">
                    <label for="editUserEmail">Email</label>
                    <input id="editUserEmail" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field">
                    <label for="editUserRole">Role Pengguna</label>
                    <select id="editUserRole" name="role" required>
                        <option value="mahasiswa" @selected($selectedRole === 'mahasiswa')>Mahasiswa</option>
                        <option value="pegawai" @selected($selectedRole === 'pegawai')>Pegawai</option>
                        <option value="admin" @selected($selectedRole === 'admin')>Admin</option>
                    </select>
                    @error('role')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field">
                    <label for="editUserNim" id="editUserNimLabel">
                        {{ $selectedRole === 'mahasiswa' ? 'NIM' : ($selectedRole === 'pegawai' ? 'NIP' : 'NIM / NIP (Opsional)') }}
                    </label>
                    <input id="editUserNim" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}" placeholder="Masukkan NIM atau NIP">
                    @error('nim_nip')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                {{-- FIELD KHUSUS MAHASISWA --}}
                <div class="user-edit-field" data-edit-role-field="mahasiswa" @if($selectedRole !== 'mahasiswa') style="display:none;" @endif>
                    <label for="editUserFaculty">Fakultas</label>
                    <input id="editUserFaculty" name="faculty" value="{{ old('faculty', $user->faculty) }}" placeholder="Masukkan fakultas" @if($selectedRole !== 'mahasiswa') disabled @endif>
                    @error('faculty')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field" data-edit-role-field="mahasiswa" @if($selectedRole !== 'mahasiswa') style="display:none;" @endif>
                    <label for="editUserStudyProgram">Program Studi</label>
                    <input id="editUserStudyProgram" name="study_program" value="{{ old('study_program', $user->study_program) }}" placeholder="Masukkan program studi" @if($selectedRole !== 'mahasiswa') disabled @endif>
                    @error('study_program')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                {{-- FIELD KHUSUS PEGAWAI --}}
                <div class="user-edit-field" data-edit-role-field="pegawai" @if($selectedRole !== 'pegawai') style="display:none;" @endif>
                    <label for="editUserUnit">Unit / Biro Kerja</label>
                    <input id="editUserUnit" name="employee_unit" value="{{ old('employee_unit', $user->employee_unit) }}" placeholder="Masukkan unit atau biro kerja" @if($selectedRole !== 'pegawai') disabled @endif>
                    @error('employee_unit')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field" data-edit-role-field="pegawai" @if($selectedRole !== 'pegawai') style="display:none;" @endif>
                    <label for="editUserPosition">Jabatan</label>
                    <input id="editUserPosition" name="position" value="{{ old('position', $user->position) }}" placeholder="Masukkan jabatan" @if($selectedRole !== 'pegawai') disabled @endif>
                    @error('position')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field">
                    <label for="editUserPhone">Nomor Telepon</label>
                    <input id="editUserPhone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                    @error('phone')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field">
                    <label for="editUserPassword">Password Baru</label>
                    <div class="user-edit-password-wrap">
                        <input
                            id="editUserPassword"
                            type="password"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            class="user-edit-password-toggle"
                            data-toggle-password="editUserPassword"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <small>Kosongkan jika tidak ingin mengubah password.</small>
                    @error('password')<div class="user-edit-field-error">{{ $message }}</div>@enderror
                </div>

                <div class="user-edit-field">
                    <label for="editUserPasswordConfirm">Konfirmasi Password Baru</label>
                    <div class="user-edit-password-wrap">
                        <input
                            id="editUserPasswordConfirm"
                            type="password"
                            name="password_confirmation"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            class="user-edit-password-toggle"
                            data-toggle-password="editUserPasswordConfirm"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    <small>Ulangi password baru jika mengganti password.</small>
                </div>
            </div>

            <div class="user-edit-actions">
                <a class="user-edit-btn secondary" href="{{ route('admin.users') }}">Batal</a>
                <button class="user-edit-btn" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('editUserRole');
    const nimLabel = document.getElementById('editUserNimLabel');
    const nimInput = document.getElementById('editUserNim');

    function syncEditRoleFields() {
        if (!roleSelect) return;
        const selectedRole = roleSelect.value;

        document.querySelectorAll('[data-edit-role-field]').forEach(function (field) {
            const isMatch = field.dataset.editRoleField === selectedRole;
            field.style.display = isMatch ? '' : 'none';
            field.querySelectorAll('input, select').forEach(function (input) {
                input.disabled = !isMatch;
            });
        });

        if (nimLabel && nimInput) {
            if (selectedRole === 'mahasiswa') {
                nimLabel.textContent = 'NIM';
                nimInput.placeholder = 'Masukkan NIM';
            } else if (selectedRole === 'pegawai') {
                nimLabel.textContent = 'NIP';
                nimInput.placeholder = 'Masukkan NIP';
            } else {
                nimLabel.textContent = 'NIM / NIP (Opsional)';
                nimInput.placeholder = 'Opsional untuk Admin';
            }
        }
    }

    roleSelect?.addEventListener('change', syncEditRoleFields);
    syncEditRoleFields();

    // Tampilkan/sembunyikan password dengan ikon mata.
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.togglePassword);

            if (!input) return;

            const isPasswordHidden = input.type === 'password';
            input.type = isPasswordHidden ? 'text' : 'password';

            button.setAttribute(
                'aria-label',
                isPasswordHidden ? 'Sembunyikan password' : 'Tampilkan password'
            );

            button.setAttribute(
                'aria-pressed',
                isPasswordHidden ? 'true' : 'false'
            );

            button.innerHTML = isPasswordHidden
                ? '<i data-lucide="eye-off"></i>'
                : '<i data-lucide="eye"></i>';

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