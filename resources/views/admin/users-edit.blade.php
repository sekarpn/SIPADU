@extends('layouts.panel')
@section('panel')
<style>
.user-page{color:#163626}.user-header{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:22px}.user-header h1{margin:0 0 5px;font-size:23px}.user-header p{margin:0;color:#718078;font-size:13px}.user-card{background:#fff;border:1px solid #e5ebe7;border-radius:14px;padding:22px;box-shadow:0 3px 12px rgba(0,0,0,.045);max-width:900px}.user-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.user-field label{display:block;font-size:12px;font-weight:700;margin-bottom:7px;color:#315541}.user-field input,.user-field select{width:100%;box-sizing:border-box;padding:11px 12px;border:1px solid #d8e1db;border-radius:9px;background:#fff;font:inherit;font-size:13px;color:#163626}.user-field small{display:block;color:#87938c;margin-top:5px;font-size:11px}.user-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:22px}.user-btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:9px;padding:10px 15px;background:#21804d;color:#fff;text-decoration:none;font-size:13px;font-weight:700;cursor:pointer}.user-btn.secondary{background:#edf3ef;color:#315541}.user-alert{padding:11px 14px;border-radius:9px;background:#fff0f0;color:#a52d2d;margin-bottom:16px;font-size:13px}@media(max-width:650px){.user-grid{grid-template-columns:1fr}.user-card{padding:16px}}
</style>
<div class="user-page">
 <div class="user-header"><div><h1>Edit Data Pengguna</h1><p>Perbarui informasi akun dan hak akses pengguna.</p></div><a class="user-btn secondary" href="{{ route('admin.users') }}">Kembali</a></div>
 @if($errors->any())<div class="user-alert">Periksa kembali data yang dimasukkan. {{ $errors->first() }}</div>@endif
 <section class="user-card"><form method="POST" action="{{ route('admin.users.update',$user) }}">@csrf @method('PUT')
  <div class="user-grid">
   <div class="user-field"><label>Nama Lengkap</label><input name="name" value="{{ old('name',$user->name) }}" required></div>
   <div class="user-field"><label>Email</label><input type="email" name="email" value="{{ old('email',$user->email) }}" required></div>
   <div class="user-field"><label>Nomor Telepon</label><input name="phone" value="{{ old('phone',$user->phone) }}"></div>
   <div class="user-field"><label>NIM/NIP</label><input name="nim_nip" value="{{ old('nim_nip',$user->nim_nip) }}"></div>
   <div class="user-field"><label>Role</label><select name="role" required><option value="mahasiswa" @selected(old('role',$user->role)==='mahasiswa')>Mahasiswa</option><option value="pegawai" @selected(old('role',$user->role)==='pegawai')>Pegawai</option><option value="admin" @selected(old('role',$user->role)==='admin')>Admin</option></select></div>
   <div class="user-field"><label>Password Baru</label><input type="password" name="password" minlength="8" autocomplete="new-password"><small>Kosongkan jika tidak ingin mengubah password.</small></div>
   <div class="user-field"><label>Konfirmasi Password Baru</label><input type="password" name="password_confirmation" minlength="8" autocomplete="new-password"></div>
  </div>
  <div class="user-actions"><a class="user-btn secondary" href="{{ route('admin.users.show',$user) }}">Batal</a><button class="user-btn" type="submit">Simpan Perubahan</button></div>
 </form></section>
</div>
@endsection
