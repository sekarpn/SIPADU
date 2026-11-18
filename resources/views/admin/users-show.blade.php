@extends('layouts.panel')
@section('panel')
<style>
.user-page{color:#163626}.user-header{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:22px}.user-header h1{margin:0 0 5px;font-size:23px}.user-header p{margin:0;color:#718078;font-size:13px}.user-card{background:#fff;border:1px solid #e5ebe7;border-radius:14px;padding:22px;box-shadow:0 3px 12px rgba(0,0,0,.045);max-width:980px}.user-profile-head{display:flex;align-items:center;gap:15px;padding-bottom:20px;border-bottom:1px solid #edf1ee;margin-bottom:18px}.user-avatar{width:54px;height:54px;border-radius:14px;background:#eaf4ed;color:#21804d;display:grid;place-items:center;font-size:22px;font-weight:800}.user-profile-head h2{font-size:18px;margin:0 0 5px}.user-profile-head p{font-size:13px;color:#718078;margin:0}.user-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0 24px}.user-item{padding:13px 0;border-bottom:1px solid #f0f3f1}.user-item label{display:block;color:#87938c;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px}.user-item div{font-size:13px;font-weight:600;overflow-wrap:anywhere}.role-pill{display:inline-block;border-radius:99px;padding:5px 10px;font-size:11px;font-weight:700;background:#edf5ef;color:#287247}.role-pill.admin{background:#eeeafe;color:#6546a5}.role-pill.pegawai{background:#eaf2ff;color:#315fa8}.user-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:22px}.user-btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:9px;padding:10px 15px;background:#21804d;color:#fff;text-decoration:none;font-size:13px;font-weight:700;cursor:pointer}.user-btn.secondary{background:#edf3ef;color:#315541}.user-btn.danger{background:#dc4545}@media(max-width:650px){.user-grid{grid-template-columns:1fr}.user-card{padding:16px}}
</style>
<div class="user-page">
 <div class="user-header"><div><h1>Detail Data Pengguna</h1><p>Informasi lengkap akun pengguna SIPADU.</p></div><a class="user-btn secondary" href="{{ route('admin.users') }}">Kembali ke Data Pengguna</a></div>
 <section class="user-card">
  <div class="user-profile-head"><div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div><div><h2>{{ $user->name }}</h2><p>{{ $user->email }}</p></div></div>
  <div class="user-grid">
   <div class="user-item"><label>Nama Lengkap</label><div>{{ $user->name }}</div></div>
   <div class="user-item"><label>Email</label><div>{{ $user->email }}</div></div>
   <div class="user-item"><label>Nomor Telepon</label><div>{{ $user->phone ?: 'Belum diisi' }}</div></div>
   <div class="user-item"><label>NIM/NIP</label><div>{{ $user->nim_nip ?: 'Belum diisi' }}</div></div>
   <div class="user-item"><label>Role</label><div><span class="role-pill {{ $user->role }}">{{ ucfirst($user->role) }}</span></div></div>
   <div class="user-item"><label>Fakultas</label><div>{{ $user->faculty ?: 'Belum diisi' }}</div></div>
   <div class="user-item"><label>Program Studi</label><div>{{ $user->study_program ?: 'Belum diisi' }}</div></div>
   <div class="user-item"><label>Unit / Biro</label><div>{{ $user->employee_unit ?: 'Belum diisi' }}</div></div>
   <div class="user-item"><label>Jabatan</label><div>{{ $user->position ?: 'Belum diisi' }}</div></div>
   <div class="user-item"><label>Tanggal Bergabung</label><div>{{ optional($user->created_at)->format('d F Y, H:i') ?: '—' }}</div></div>
   <div class="user-item"><label>Jumlah Riwayat Peminjaman</label><div>{{ $user->bookings_count }} pengajuan</div></div>
   <div class="user-item"><label>Terakhir Diperbarui</label><div>{{ optional($user->updated_at)->format('d F Y, H:i') ?: '—' }}</div></div>
  </div>
  <div class="user-actions"><a class="user-btn" href="{{ route('admin.users.edit',$user) }}">Edit Data Pengguna</a></div>
 </section>
</div>
@endsection
