
@extends('layouts.panel')

@section('panel')
    <style>
        .user-show-page {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 10px 35px;
            color: #163626;
            font-family: 'Plus Jakarta Sans', 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }

        .user-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .user-header h1 {
            margin: 0 0 5px;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -.03em;
            color: #163626;
        }

        .user-header p {
            margin: 0;
            color: #4c6456;
            font-size: 13px;
        }

        .user-card {
            background: linear-gradient(145deg, rgba(255, 255, 255, .68), rgba(255, 255, 255, .40));
            backdrop-filter: blur(18px) saturate(150%);
            -webkit-backdrop-filter: blur(18px) saturate(150%);
            border: 1px solid rgba(255, 255, 255, .55);
            border-radius: 16px;
            padding: 24px 26px;
            box-shadow: 0 8px 28px rgba(22, 54, 38, .06);
            max-width: 980px;
        }

        .user-profile-head {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(22, 54, 38, .09);
            margin-bottom: 18px;
        }

        .user-avatar {
            width: 54px;
            height: 54px;
            border-radius: 15px;
            background: #237c4d;
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 20px;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(35, 124, 77, .22);
        }

        .user-profile-head h2 {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 4px;
            color: #163626;
        }

        .user-profile-head p {
            font-size: 13px;
            color: #64786c;
            margin: 0;
        }

        .user-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0 24px;
        }

        .user-item {
            padding: 13px 0;
            border-bottom: 1px solid rgba(22, 54, 38, .07);
        }

        .user-item label {
            display: block;
            color: #64786c;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 5px;
        }

        .user-item div {
            font-size: 13px;
            font-weight: 600;
            color: #163626;
            overflow-wrap: anywhere;
        }

        .role-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 11px;
            font-size: 11px;
            font-weight: 800;
            background: #bbf7d0;
            color: #065f46;
            border: 1px solid #34d399;
        }

        .role-pill.mahasiswa {
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

        .user-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid rgba(22, 54, 38, .09);
        }

        .user-actions form {
            margin: 0;
        }

        .user-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid transparent;
            border-radius: 11px;
            padding: 10px 16px;
            background: #237c4d;
            color: #fff;
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s ease, transform .18s ease;
        }

        .user-btn:hover {
            transform: translateY(-1px);
        }

        .user-btn.edit {
            background: #eab308 !important;
            color: #29220a !important;
            border-color: #eab308 !important;
        }

        .user-btn.edit:hover {
            background: #d99b06 !important;
            color: #29220a !important;
        }

        .user-btn.secondary {
            background: rgba(255, 255, 255, .78) !important;
            border-color: rgba(22, 54, 38, .14) !important;
            color: #294735 !important;
        }

        .user-btn.secondary:hover {
            background: #fff !important;
            color: #163626 !important;
        }

        .user-btn.danger {
            background: #dc2626 !important;
            background-color: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
        }

        .user-btn.danger:hover {
            background: #b91c1c !important;
            background-color: #b91c1c !important;
            color: #ffffff !important;
            border-color: #b91c1c !important;
        }

        .user-btn.danger:disabled {
            background: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
            opacity: .45 !important;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 650px) {
            .user-show-page {
                padding: 8px 4px 28px;
            }

            .user-grid {
                grid-template-columns: 1fr;
            }

            .user-card {
                padding: 18px;
            }
        }
    </style>

    <div class="user-show-page">
        <div class="user-header">
            <div>
                <h1>Detail Data Pengguna</h1>
                <p>Informasi lengkap akun pengguna SIPADU.</p>
            </div>

            <a class="user-btn secondary" href="{{ route('admin.users') }}">
                ← Kembali ke Data Pengguna
            </a>
        </div>

        <section class="user-card">
            <div class="user-profile-head">
                <div class="user-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>

                <div>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
            </div>

            <div class="user-grid">
                <div class="user-item">
                    <label>Nama Lengkap</label>
                    <div>{{ $user->name }}</div>
                </div>

                <div class="user-item">
                    <label>Email</label>
                    <div>{{ $user->email }}</div>
                </div>

                <div class="user-item">
                    <label>Nomor Telepon</label>
                    <div>{{ $user->phone ?: 'Belum diisi' }}</div>
                </div>

                <div class="user-item">
                    <label>
                        {{ in_array($user->role, ['pegawai', 'admin'], true) ? 'NIP' : 'NIM' }}
                    </label>
                    <div>{{ $user->nim_nip ?: 'Belum diisi' }}</div>
                </div>

                <div class="user-item">
                    <label>Role</label>
                    <div>
                        <span class="role-pill {{ $user->role }}">
                            {{ $user->role === 'pegawai' ? 'Dosen & Tendik' : ucfirst($user->role) }}
                        </span>
                    </div>
                </div>

                <div class="user-item">
                    <label>Fakultas</label>
                    <div>{{ $user->faculty ?: 'Belum diisi' }}</div>
                </div>

                <div class="user-item">
                    <label>Program Studi</label>
                    <div>{{ $user->study_program ?: 'Belum diisi' }}</div>
                </div>

                <div class="user-item">
                    <label>Unit / Biro</label>
                    <div>{{ $user->employee_unit ?: 'Belum diisi' }}</div>
                </div>

                <div class="user-item">
                    <label>Jabatan</label>
                    <div>{{ $user->position ?: 'Belum diisi' }}</div>
                </div>

                <div class="user-item">
                    <label>Tanggal Bergabung</label>
                    <div>
                        {{ optional($user->created_at)->format('d F Y, H:i') ?: '—' }}
                    </div>
                </div>

                <div class="user-item">
                    <label>Jumlah Riwayat Peminjaman</label>
                    <div>{{ $user->bookings_count }} pengajuan</div>
                </div>

                <div class="user-item">
                    <label>Terakhir Diperbarui</label>
                    <div>
                        {{ optional($user->updated_at)->format('d F Y, H:i') ?: '—' }}
                    </div>
                </div>
            </div>

            <div class="user-actions">
                <a class="user-btn edit" href="{{ route('admin.users.edit', $user) }}">
                    Edit Data Pengguna
                </a>

                <form
                    method="POST"
                    action="{{ route('admin.users.delete', $user) }}"
                    onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        class="user-btn danger"
                        type="submit"
                        {{ (string) $user->getAuthIdentifier() === (string) auth()->id() ? 'disabled' : '' }}
                    >
                        Hapus Pengguna
                    </button>
                </form>
            </div>
        </section>
    </div>
@endsection
