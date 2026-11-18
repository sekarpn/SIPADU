@extends('layouts.panel')
@section('panel')
<style>
.user-edit-page {
    width: 100%;
    box-sizing: border-box;
    padding: 10px 10px 35px;
    color: #163626;
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
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -.03em;
    color: #163626;
}

.user-edit-header p {
    margin: 6px 0 0;
    color: #4c6456;
    font-size: 13px;
    line-height: 1.5;
}

/* GLASSMORPHISM CARD MATCHING DASHBOARD ADMIN */
.user-edit-card {
    background: linear-gradient(
        145deg,
        rgba(255, 255, 255, .68) 0%,
        rgba(255, 255, 255, .40) 100%
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
    border-bottom: 1px solid rgba(22, 54, 38, .09);
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
    background: #237c4d;
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
    color: #163626;
}

.user-edit-identity-info span {
    display: block;
    margin-top: 3px;
    font-size: 12px;
    color: #64786c;
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
    color: #163626;
    outline: none;
    transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
}

.user-edit-field input:focus,
.user-edit-field select:focus {
    background: #fff;
    border-color: #237c4d;
    box-shadow: 0 0 0 3px rgba(35, 124, 77, .14);
}

.user-edit-field small {
    display: block;
    color: #64786c;
    margin-top: 6px;
    font-size: 11px;
    font-weight: 500;
}

.user-edit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid rgba(22, 54, 38, .09);
}

.user-edit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid transparent;
    border-radius: 11px;
    padding: 10px 18px;
    background: #237c4d;
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
    color: #163626;
}

.user-edit-alert {
    padding: 12px 15px;
    border-radius: 12px;
    background: rgba(254, 226, 226, .88);
    border: 1px solid rgba(248, 113, 113, .35);
    color: #991b1b;
    margin-bottom: 16px;
    font-size: 13px;
    font-weight: 600;
    max-width: 960px;
    box-sizing: border-box;
}

@media (max-width: 650px) {
    .user-edit-page { padding: 8px 4px 28px; }
    .user-edit-header h1 { font-size: 22px; }
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

    @if($errors->any())
        <div class="user-edit-alert">
            Periksa kembali data yang dimasukkan. {{ $errors->first() }}
        </div>
    @endif

    <section class="user-edit-card">
        <div class="user-edit-identity">
            <div class="user-edit-identity-left">
                <div class="user-edit-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="user-edit-identity-info">
                    <h2>{{ $user->name }}</h2>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
            <span class="role-pill {{ $user->role }}">{{ ucfirst($user->role) }}</span>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="user-edit-grid">
                <div class="user-edit-field">
                    <label for="editUserName">Nama Lengkap</label>
                    <input id="editUserName" name="name" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="user-edit-field">
                    <label for="editUserEmail">Email</label>
                    <input id="editUserEmail" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="user-edit-field">
                    <label for="editUserPhone">Nomor Telepon</label>
                    <input id="editUserPhone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890">
                </div>

                <div class="user-edit-field">
                    <label for="editUserNim">NIM / NIP</label>
                    <input id="editUserNim" name="nim_nip" value="{{ old('nim_nip', $user->nim_nip) }}" placeholder="Masukkan NIM atau NIP">
                </div>

                <div class="user-edit-field">
                    <label for="editUserRole">Role Pengguna</label>
                    <select id="editUserRole" name="role" required>
                        <option value="mahasiswa" @selected(old('role', $user->role) === 'mahasiswa')>Mahasiswa</option>
                        <option value="pegawai" @selected(old('role', $user->role) === 'pegawai')>Pegawai</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                </div>

                <div class="user-edit-field">
                    <label for="editUserPassword">Password Baru</label>
                    <input id="editUserPassword" type="password" name="password" minlength="8" autocomplete="new-password" placeholder="••••••••">
                    <small>Kosongkan jika tidak ingin mengubah password.</small>
                </div>

                <div class="user-edit-field">
                    <label for="editUserPasswordConfirm">Konfirmasi Password Baru</label>
                    <input id="editUserPasswordConfirm" type="password" name="password_confirmation" minlength="8" autocomplete="new-password" placeholder="••••••••">
                </div>
            </div>

            <div class="user-edit-actions">
                <a class="user-edit-btn secondary" href="{{ route('admin.users.show', $user) }}">Batal</a>
                <button class="user-edit-btn" type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</div>
@endsection