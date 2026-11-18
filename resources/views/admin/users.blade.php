@extends('layouts.panel')
@section('panel')
<style>
.user-page{width:100%;box-sizing:border-box;padding:10px 10px 35px;color:#163626;font-family:'Plus Jakarta Sans','Instrument Sans',ui-sans-serif,system-ui,sans-serif}
.user-head{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-bottom:20px}
.user-head h1{font-size:26px;font-weight:800;letter-spacing:-.03em;color:#163626;margin:0 0 5px}
.user-head p{font-size:13px;color:#4c6456;margin:0}

.user-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 16px;border:1px solid transparent;border-radius:11px;background:#edf1f4;color:#34424e;text-decoration:none;font-size:12.5px;font-weight:700;box-shadow:none !important;cursor:pointer;white-space:nowrap;transition:background .2s ease,color .2s ease,border-color .2s ease,transform .18s ease}
.user-btn:hover{transform:translateY(-1px);box-shadow:none !important}
.user-btn.primary{background:#237c4d;color:#fff;border-color:#237c4d}
.user-btn.primary:hover{background:#1b663f;color:#fff}
.user-btn.detail{background:#237c4d;color:#fff;border-color:#237c4d}
.user-btn.detail:hover{background:#1b663f;color:#fff}
.user-btn.edit{background:#eab308;color:#29220a;border-color:#eab308}
.user-btn.edit:hover{background:#d99b06;color:#29220a}
.user-btn.danger{background:#dc2626;color:#fff;border-color:#dc2626}
.user-btn.danger:hover{background:#b91c1c;color:#fff}
.user-btn.secondary{background:rgba(255,255,255,.78);color:#294735;border-color:rgba(22,54,38,.14)}
.user-btn.secondary:hover{background:#fff;color:#163626}
.user-btn:disabled{opacity:.45;cursor:not-allowed;transform:none}

.user-alert{padding:12px 15px;border-radius:12px;margin-bottom:14px;font-size:13px;font-weight:600}
.user-alert.success{background:rgba(220,252,231,.88);color:#166534;border:1px solid rgba(74,222,128,.35)}
.user-alert.error{background:rgba(254,226,226,.88);color:#991b1b;border:1px solid rgba(248,113,113,.35)}

.user-table-card{background:linear-gradient(145deg,rgba(255,255,255,.66),rgba(255,255,255,.38));backdrop-filter:blur(18px) saturate(150%);-webkit-backdrop-filter:blur(18px) saturate(150%);border:1px solid rgba(255,255,255,.52);border-radius:16px;padding:16px 20px 18px;box-shadow:0 8px 28px rgba(22,54,38,.06);width:100%;box-sizing:border-box}
.user-table-scroll{width:100%;overflow-x:auto;border:1px solid rgba(22,54,38,.08);border-radius:12px;background:rgba(255,255,255,.45)}
.user-table{width:100%;min-width:900px;border-collapse:collapse;font-size:12.5px}
.user-table th{background:rgba(35,124,77,.14);color:#163626;font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;text-align:left;padding:13px 14px;white-space:nowrap}
.user-table td{color:#243b2e;font-size:12.5px;font-weight:500;padding:13px 14px;border-bottom:1px solid rgba(22,54,38,.06);vertical-align:middle}
.user-table tbody tr:hover td{background:rgba(255,255,255,.65)}
.user-table tr:last-child td{border-bottom:0}
.user-actions{display:flex;align-items:center;gap:7px}
.user-actions form{margin:0}
.user-actions .user-btn{padding:7px 12px;font-size:11.5px;border-radius:9px}

.role-pill{display:inline-flex;align-items:center;border-radius:999px;padding:5px 11px;font-size:11px;font-weight:800;background:#bbf7d0;color:#065f46;border:1px solid #34d399;letter-spacing:.01em}
.role-pill.mahasiswa{background:#bbf7d0;color:#065f46;border:1px solid #34d399}
.role-pill.admin{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
.role-pill.pegawai{background:#dbeafe;color:#1e40af;border:1px solid #93c5fd}

.user-empty{text-align:center!important;color:#4c6456!important;padding:28px 10px!important}

.user-page .bp-drawer-backdrop{position:fixed;inset:0;z-index:10000;display:flex;justify-content:flex-end;background:rgba(15,32,23,.40);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease}
.user-page .bp-drawer-backdrop.show{opacity:1;visibility:visible}
.user-page .bp-drawer{display:flex;flex-direction:column;width:min(510px,100%);height:100%;background:#f8fbf9;box-shadow:-18px 0 55px rgba(12,32,21,.16);transform:translateX(100%);transition:transform .28s ease}
.user-page .bp-drawer-backdrop.show .bp-drawer{transform:translateX(0)}
.user-page .bp-drawer-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 24px 16px;background:#fff;border-bottom:1px solid rgba(22,54,38,.08);flex-shrink:0}
.user-page .bp-drawer-header h2{margin:0;color:#163626;font-size:18px;font-weight:800;letter-spacing:-.02em}
.user-page .bp-drawer-header p{margin:4px 0 0;color:#718078;font-size:12px}
.user-page .bp-drawer-close{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;padding:0;flex-shrink:0;border:1px solid rgba(22,54,38,.12);border-radius:50%;background:#fff;color:#52665a;cursor:pointer}
.user-page .bp-drawer-close svg{width:17px;height:17px;display:block}
.user-page .bp-drawer-body{flex:1;overflow-y:auto;padding:22px 24px 26px}
.user-form-intro{font-size:12px;color:#64776b;margin:0 0 18px;line-height:1.6}
.user-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.user-field label{display:block;font-size:12px;font-weight:700;color:#294735;margin-bottom:7px}
.user-field input,.user-field select{width:100%;box-sizing:border-box;border:1px solid #d8e1db;border-radius:10px;padding:11px 12px;font-family:inherit;font-size:13px;color:#243b2e;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s}
.user-field input:focus,.user-field select:focus{border-color:#237c4d;box-shadow:0 0 0 3px rgba(35,124,77,.12)}
.user-field.full{grid-column:1/-1}
.user-form-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:23px;padding-top:18px;border-top:1px solid #e5ece7}
body.user-drawer-open{overflow:hidden}

@media(max-width:650px){
    .user-page{padding:8px 4px 28px}
    .user-head h1{font-size:23px}
    .user-table-card{padding:12px}
    .user-page .bp-drawer{width:100%}
    .user-page .bp-drawer-header{padding:16px 18px}
    .user-page .bp-drawer-body{padding:18px}
    .user-form-grid{grid-template-columns:1fr}
    .user-field.full{grid-column:auto}
}
</style>

<div class="user-page">
    <div class="user-head">
        <div>
            <h1>Data Pengguna</h1>
            <p>Kelola akun Mahasiswa, Pegawai, dan Administrator.</p>
        </div>
        <button class="user-btn primary" type="button" id="openAddUserDrawer">＋ Tambah Pengguna</button>
    </div>

    @if(session('success'))<div class="user-alert success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="user-alert error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="user-alert error">Periksa kembali data yang dimasukkan. {{ $errors->first() }}</div>@endif

    <section class="user-table-card">
        <div class="user-table-scroll">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>NIM/NIP</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($users->count() > 0)
                        @foreach($users as $u)
                            <tr>
                                <td><strong>{{ $u->name }}</strong></td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->phone ?: '—' }}</td>
                                <td>{{ $u->nim_nip ?: '—' }}</td>
                                <td><span class="role-pill {{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                                <td>{{ optional($u->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="user-actions">
                                        <a class="user-btn detail" href="{{ route('admin.users.show', $u) }}">Detail</a>
                                        <a class="user-btn edit" href="{{ route('admin.users.edit', $u) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.delete', $u) }}" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="user-btn danger" type="submit" {{ (string)$u->getAuthIdentifier() === (string)auth()->id() ? 'disabled' : '' }}>Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="user-empty">Belum ada data pengguna.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px;">{{ $users->links() }}</div>
    </section>

    <div class="bp-drawer-backdrop" id="addUserDrawerBackdrop" aria-hidden="true" data-has-errors="{{ $errors->any() ? '1' : '0' }}">
        <aside class="bp-drawer" role="dialog" aria-modal="true" aria-labelledby="addUserDrawerTitle">
            <header class="bp-drawer-header">
                <div>
                    <h2 id="addUserDrawerTitle">Tambah Pengguna</h2>
                    <p>Masukkan informasi akun pengguna baru.</p>
                </div>
                <button type="button" class="bp-drawer-close" id="closeAddUserDrawer" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </header>
            <div class="bp-drawer-body">
                <p class="user-form-intro">Lengkapi data berikut untuk membuat akun baru di SIPADU. Pastikan role yang dipilih sudah sesuai.</p>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="user-form-grid">
                        <div class="user-field full"><label for="newUserName">Nama Lengkap</label><input id="newUserName" name="name" value="{{ old('name') }}" required></div>
                        <div class="user-field full"><label for="newUserEmail">Email</label><input id="newUserEmail" type="email" name="email" value="{{ old('email') }}" required></div>
                        <div class="user-field"><label for="newUserPhone">Nomor Telepon</label><input id="newUserPhone" name="phone" value="{{ old('phone') }}"></div>
                        <div class="user-field"><label for="newUserNim">NIM/NIP</label><input id="newUserNim" name="nim_nip" value="{{ old('nim_nip') }}"></div>
                        <div class="user-field full">
                            <label for="newUserRole">Role</label>
                            <select id="newUserRole" name="role" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="pegawai">Pegawai</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="user-field"><label for="newUserPassword">Password</label><input id="newUserPassword" type="password" name="password" required minlength="8" autocomplete="new-password"></div>
                        <div class="user-field"><label for="newUserPasswordConfirm">Konfirmasi Password</label><input id="newUserPasswordConfirm" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></div>
                    </div>
                    <div class="user-form-actions">
                        <button class="user-btn secondary" type="button" id="cancelAddUserDrawer">Batal</button>
                        <button class="user-btn primary" type="submit">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </aside>
    </div>
</div>@extends('layouts.panel')
@section('panel')
<style>
.user-page{width:100%;box-sizing:border-box;padding:10px 10px 35px;color:#163626;font-family:'Plus Jakarta Sans','Instrument Sans',ui-sans-serif,system-ui,sans-serif}
.user-head{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-bottom:20px}
.user-head h1{font-size:26px;font-weight:800;letter-spacing:-.03em;color:#163626;margin:0 0 5px}
.user-head p{font-size:13px;color:#4c6456;margin:0}

.user-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 16px;border:1px solid transparent;border-radius:11px;background:#edf1f4;color:#34424e;text-decoration:none;font-size:12.5px;font-weight:700;box-shadow:none !important;cursor:pointer;white-space:nowrap;transition:background .2s ease,color .2s ease,border-color .2s ease,transform .18s ease}
.user-btn:hover{transform:translateY(-1px);box-shadow:none !important}
.user-btn.primary{background:#237c4d !important;color:#fff !important;border-color:#237c4d !important}
.user-btn.primary:hover{background:#1b663f !important;color:#fff !important}
.user-btn.detail{background:#237c4d !important;color:#fff !important;border-color:#237c4d !important}
.user-btn.detail:hover{background:#1b663f !important;color:#fff !important}
.user-btn.edit{background:#eab308 !important;color:#29220a !important;border-color:#eab308 !important}
.user-btn.edit:hover{background:#d99b06 !important;color:#29220a !important}
.user-btn.danger{background:#dc2626 !important;background-color:#dc2626 !important;color:#ffffff !important;border-color:#dc2626 !important}
.user-btn.danger:hover{background:#b91c1c !important;background-color:#b91c1c !important;color:#ffffff !important;border-color:#b91c1c !important}
.user-btn.danger:disabled{background:#dc2626 !important;color:#ffffff !important;border-color:#dc2626 !important;opacity:.45 !important;cursor:not-allowed;transform:none}
.user-btn.secondary{background:rgba(255,255,255,.78) !important;color:#294735 !important;border-color:rgba(22,54,38,.14) !important}
.user-btn.secondary:hover{background:#fff !important;color:#163626 !important}
.user-btn:disabled{opacity:.45;cursor:not-allowed;transform:none}

.user-alert{padding:12px 15px;border-radius:12px;margin-bottom:14px;font-size:13px;font-weight:600}
.user-alert.success{background:rgba(220,252,231,.88);color:#166534;border:1px solid rgba(74,222,128,.35)}
.user-alert.error{background:rgba(254,226,226,.88);color:#991b1b;border:1px solid rgba(248,113,113,.35)}

.user-table-card{background:linear-gradient(145deg,rgba(255,255,255,.66),rgba(255,255,255,.38));backdrop-filter:blur(18px) saturate(150%);-webkit-backdrop-filter:blur(18px) saturate(150%);border:1px solid rgba(255,255,255,.52);border-radius:16px;padding:16px 20px 18px;box-shadow:0 8px 28px rgba(22,54,38,.06);width:100%;box-sizing:border-box}
.user-table-scroll{width:100%;overflow-x:auto;border:1px solid rgba(22,54,38,.08);border-radius:12px;background:rgba(255,255,255,.45)}
.user-table{width:100%;min-width:900px;border-collapse:collapse;font-size:12.5px}
.user-table th{background:rgba(35,124,77,.14);color:#163626;font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;text-align:left;padding:13px 14px;white-space:nowrap}
.user-table td{color:#243b2e;font-size:12.5px;font-weight:500;padding:13px 14px;border-bottom:1px solid rgba(22,54,38,.06);vertical-align:middle}
.user-table tbody tr:hover td{background:rgba(255,255,255,.65)}
.user-table tr:last-child td{border-bottom:0}
.user-actions{display:flex;align-items:center;gap:7px}
.user-actions form{margin:0}
.user-actions .user-btn{padding:7px 12px;font-size:11.5px;border-radius:9px}

.role-pill{display:inline-flex;align-items:center;border-radius:999px;padding:5px 11px;font-size:11px;font-weight:800;background:#bbf7d0;color:#065f46;border:1px solid #34d399;letter-spacing:.01em}
.role-pill.mahasiswa{background:#bbf7d0;color:#065f46;border:1px solid #34d399}
.role-pill.admin{background:#ede9fe;color:#5b21b6;border:1px solid #c4b5fd}
.role-pill.pegawai{background:#dbeafe;color:#1e40af;border:1px solid #93c5fd}

.user-empty{text-align:center!important;color:#4c6456!important;padding:28px 10px!important}

.user-page .bp-drawer-backdrop{position:fixed;inset:0;z-index:10000;display:flex;justify-content:flex-end;background:rgba(15,32,23,.40);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease}
.user-page .bp-drawer-backdrop.show{opacity:1;visibility:visible}
.user-page .bp-drawer{display:flex;flex-direction:column;width:min(510px,100%);height:100%;background:#f8fbf9;box-shadow:-18px 0 55px rgba(12,32,21,.16);transform:translateX(100%);transition:transform .28s ease}
.user-page .bp-drawer-backdrop.show .bp-drawer{transform:translateX(0)}
.user-page .bp-drawer-header{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 24px 16px;background:#fff;border-bottom:1px solid rgba(22,54,38,.08);flex-shrink:0}
.user-page .bp-drawer-header h2{margin:0;color:#163626;font-size:18px;font-weight:800;letter-spacing:-.02em}
.user-page .bp-drawer-header p{margin:4px 0 0;color:#718078;font-size:12px}
.user-page .bp-drawer-close{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;padding:0;flex-shrink:0;border:1px solid rgba(22,54,38,.12);border-radius:50%;background:#fff;color:#52665a;cursor:pointer}
.user-page .bp-drawer-close svg{width:17px;height:17px;display:block}
.user-page .bp-drawer-body{flex:1;overflow-y:auto;padding:22px 24px 26px}
.user-form-intro{font-size:12px;color:#64776b;margin:0 0 18px;line-height:1.6}
.user-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}
.user-field label{display:block;font-size:12px;font-weight:700;color:#294735;margin-bottom:7px}
.user-field input,.user-field select{width:100%;box-sizing:border-box;border:1px solid #d8e1db;border-radius:10px;padding:11px 12px;font-family:inherit;font-size:13px;color:#243b2e;background:#fff;outline:none;transition:border-color .2s,box-shadow .2s}
.user-field input:focus,.user-field select:focus{border-color:#237c4d;box-shadow:0 0 0 3px rgba(35,124,77,.12)}
.user-field.full{grid-column:1/-1}
.user-form-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:23px;padding-top:18px;border-top:1px solid #e5ece7}
body.user-drawer-open{overflow:hidden}

@media(max-width:650px){
    .user-page{padding:8px 4px 28px}
    .user-head h1{font-size:23px}
    .user-table-card{padding:12px}
    .user-page .bp-drawer{width:100%}
    .user-page .bp-drawer-header{padding:16px 18px}
    .user-page .bp-drawer-body{padding:18px}
    .user-form-grid{grid-template-columns:1fr}
    .user-field.full{grid-column:auto}
}
</style>

<div class="user-page">
    <div class="user-head">
        <div>
            <h1>Data Pengguna</h1>
            <p>Kelola akun Mahasiswa, Pegawai, dan Administrator.</p>
        </div>
        <button class="user-btn primary" type="button" id="openAddUserDrawer">＋ Tambah Pengguna</button>
    </div>

    @if(session('success'))<div class="user-alert success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="user-alert error">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="user-alert error">Periksa kembali data yang dimasukkan. {{ $errors->first() }}</div>@endif

    <section class="user-table-card">
        <div class="user-table-scroll">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>NIM/NIP</th>
                        <th>Role</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($users->count() > 0)
                        @foreach($users as $u)
                            <tr>
                                <td><strong>{{ $u->name }}</strong></td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->phone ?: '—' }}</td>
                                <td>{{ $u->nim_nip ?: '—' }}</td>
                                <td><span class="role-pill {{ $u->role }}">{{ ucfirst($u->role) }}</span></td>
                                <td>{{ optional($u->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="user-actions">
                                        <a class="user-btn detail" href="{{ route('admin.users.show', $u) }}">Detail</a>
                                        <a class="user-btn edit" href="{{ route('admin.users.edit', $u) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.users.delete', $u) }}" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="user-btn danger" type="submit" {{ (string)$u->getAuthIdentifier() === (string)auth()->id() ? 'disabled' : '' }}>Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="user-empty">Belum ada data pengguna.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px;">{{ $users->links() }}</div>
    </section>

    <div class="bp-drawer-backdrop" id="addUserDrawerBackdrop" aria-hidden="true" data-has-errors="{{ $errors->any() ? '1' : '0' }}">
        <aside class="bp-drawer" role="dialog" aria-modal="true" aria-labelledby="addUserDrawerTitle">
            <header class="bp-drawer-header">
                <div>
                    <h2 id="addUserDrawerTitle">Tambah Pengguna</h2>
                    <p>Masukkan informasi akun pengguna baru.</p>
                </div>
                <button type="button" class="bp-drawer-close" id="closeAddUserDrawer" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </header>
            <div class="bp-drawer-body">
                <p class="user-form-intro">Lengkapi data berikut untuk membuat akun baru di SIPADU. Pastikan role yang dipilih sudah sesuai.</p>
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="user-form-grid">
                        <div class="user-field full"><label for="newUserName">Nama Lengkap</label><input id="newUserName" name="name" value="{{ old('name') }}" required></div>
                        <div class="user-field full"><label for="newUserEmail">Email</label><input id="newUserEmail" type="email" name="email" value="{{ old('email') }}" required></div>
                        <div class="user-field"><label for="newUserPhone">Nomor Telepon</label><input id="newUserPhone" name="phone" value="{{ old('phone') }}"></div>
                        <div class="user-field"><label for="newUserNim">NIM/NIP</label><input id="newUserNim" name="nim_nip" value="{{ old('nim_nip') }}"></div>
                        <div class="user-field full">
                            <label for="newUserRole">Role</label>
                            <select id="newUserRole" name="role" required>
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="pegawai">Pegawai</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="user-field"><label for="newUserPassword">Password</label><input id="newUserPassword" type="password" name="password" required minlength="8" autocomplete="new-password"></div>
                        <div class="user-field"><label for="newUserPasswordConfirm">Konfirmasi Password</label><input id="newUserPasswordConfirm" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></div>
                    </div>
                    <div class="user-form-actions">
                        <button class="user-btn secondary" type="button" id="cancelAddUserDrawer">Batal</button>
                        <button class="user-btn primary" type="submit">Simpan Pengguna</button>
                    </div>
                </form>
            </div>
        </aside>
    </div>
</div>

<script>
(function(){
    const backdrop=document.getElementById('addUserDrawerBackdrop');
    const openButton=document.getElementById('openAddUserDrawer');
    const closeButton=document.getElementById('closeAddUserDrawer');
    const cancelButton=document.getElementById('cancelAddUserDrawer');
    function openDrawer(){if(!backdrop)return;backdrop.classList.add('show');backdrop.setAttribute('aria-hidden','false');document.body.classList.add('user-drawer-open');setTimeout(()=>document.getElementById('newUserName')?.focus(),180)}
    function closeDrawer(){if(!backdrop)return;backdrop.classList.remove('show');backdrop.setAttribute('aria-hidden','true');document.body.classList.remove('user-drawer-open')}
    openButton?.addEventListener('click',openDrawer);closeButton?.addEventListener('click',closeDrawer);cancelButton?.addEventListener('click',closeDrawer);
    backdrop?.addEventListener('click',e=>{if(e.target===backdrop)closeDrawer()});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&backdrop?.classList.contains('show'))closeDrawer()});
    if(backdrop?.dataset.hasErrors==='1') openDrawer();
})();
</script>
@endsection

<script>
(function(){
    const backdrop=document.getElementById('addUserDrawerBackdrop');
    const openButton=document.getElementById('openAddUserDrawer');
    const closeButton=document.getElementById('closeAddUserDrawer');
    const cancelButton=document.getElementById('cancelAddUserDrawer');
    function openDrawer(){if(!backdrop)return;backdrop.classList.add('show');backdrop.setAttribute('aria-hidden','false');document.body.classList.add('user-drawer-open');setTimeout(()=>document.getElementById('newUserName')?.focus(),180)}
    function closeDrawer(){if(!backdrop)return;backdrop.classList.remove('show');backdrop.setAttribute('aria-hidden','true');document.body.classList.remove('user-drawer-open')}
    openButton?.addEventListener('click',openDrawer);closeButton?.addEventListener('click',closeDrawer);cancelButton?.addEventListener('click',closeDrawer);
    backdrop?.addEventListener('click',e=>{if(e.target===backdrop)closeDrawer()});
    document.addEventListener('keydown',e=>{if(e.key==='Escape'&&backdrop?.classList.contains('show'))closeDrawer()});
    if(backdrop?.dataset.hasErrors==='1') openDrawer();
})();
</script>
@endsection