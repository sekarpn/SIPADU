<?php

$organization = [
    'faculties' => [
        'Fakultas Ilmu Sosial dan Ilmu Politik',
        'Fakultas Ekonomi dan Bisnis',
        'Fakultas Ilmu Komputer',
        'Fakultas Kedokteran',
        'Fakultas Ilmu Kesehatan',
        'Fakultas Hukum',
        'Fakultas Teknik',
    ],

    // Program studi dikelompokkan berdasarkan fakultas agar pilihan mahasiswa saling terkait.
    'study_programs' => [
        'Fakultas Ilmu Sosial dan Ilmu Politik' => [
            'S1 Ilmu Komunikasi', 'S1 Hubungan Internasional', 'S1 Ilmu Politik',
            'S1 Sains Informasi', 'S1 Kajian Film, Televisi, dan Media',
            'S2 Ilmu Komunikasi', 'S2 Hubungan Internasional', 'S2 Ilmu Politik',
        ],
        'Fakultas Ekonomi dan Bisnis' => [
            'D3 Perbankan dan Keuangan', 'D3 Akuntansi', 'S1 Manajemen', 'S1 Akuntansi',
            'S1 Ekonomi Pembangunan', 'S1 Ekonomi Syariah', 'S2 Manajemen', 'S2 Akuntansi',
        ],
        'Fakultas Ilmu Komputer' => [
            'D3 Sistem Informasi', 'S1 Informatika', 'S1 Sistem Informasi', 'S1 Sains Data',
        ],
        'Fakultas Kedokteran' => [
            'S1 Kedokteran', 'S1 Farmasi', 'S1 Biologi', 'Profesi Pendidikan Profesi Dokter',
            'Profesi Pendidikan Profesi Apoteker', 'S2 Sains Biomedis', 'Spesialis Radiologi',
        ],
        'Fakultas Ilmu Kesehatan' => [
            'D3 Keperawatan', 'D3 Fisioterapi', 'S1 Keperawatan', 'S1 Kesehatan Masyarakat',
            'S1 Gizi', 'S1 Fisioterapi', 'Profesi Pendidikan Profesi Ners',
            'S2 Kesehatan Masyarakat', 'S2 Keperawatan',
        ],
        'Fakultas Hukum' => ['S1 Hukum', 'S1 Hukum Bisnis', 'S2 Hukum', 'S3 Hukum'],
        'Fakultas Teknik' => [
            'S1 Teknik Mesin', 'S1 Teknik Industri', 'S1 Teknik Perkapalan',
            'S1 Teknik Elektro', 'S1 Teknik Logistik',
        ],
    ],

    // Unit kerja mengikuti nama yang terlihat pada bagan struktur organisasi yang diberikan.
    'employee_units' => [
        'Rektorat',
        'Senat',
        'Dewan Pertimbangan',
        'Satuan Pengawas Internal',
        'Fakultas Ilmu Sosial dan Ilmu Politik',
        'Fakultas Ekonomi dan Bisnis',
        'Fakultas Ilmu Komputer',
        'Fakultas Kedokteran',
        'Fakultas Ilmu Kesehatan',
        'Fakultas Hukum',
        'Fakultas Teknik',
        'Lembaga Penelitian dan Pengabdian kepada Masyarakat',
        'Lembaga Penjaminan Mutu dan Pengembangan Pembelajaran',
        'Biro Akademik, Kemahasiswaan, dan Kerjasama',
        'Bagian Akademik',
        'Biro Perencanaan, Keuangan, dan Umum',
        'Bagian Umum',
        'Subbagian Pengadaan Barang dan Jasa',
        'UPA Perpustakaan',
        'UPA Bahasa',
        'UPA Laboratorium Terpadu',
        'UPA Layanan Uji Kompetensi',
        'UPA Pengembangan Karier dan Kewirausahaan',
        'UPA Teknologi Informasi dan Komunikasi',
        'Kelompok Jabatan Fungsional',
    ],

    // Pilihan jabatan umum yang berkaitan dengan susunan pada bagan organisasi.
    'positions' => [
        'Rektor', 'Wakil Rektor', 'Dekan', 'Wakil Dekan', 'Ketua Senat', 'Anggota Senat',
        'Ketua Dewan Pertimbangan', 'Anggota Dewan Pertimbangan', 'Kepala Satuan Pengawas Internal',
        'Kepala Biro', 'Kepala Bagian', 'Kepala Subbagian', 'Kepala Lembaga', 'Kepala UPA',
        'Ketua Program Studi', 'Sekretaris Program Studi', 'Dosen', 'Tenaga Kependidikan',
        'Staf / Pelaksana', 'Pejabat Fungsional',
    ],
];

// Urutkan seluruh pilihan dropdown secara alfabetis (A-Z).
foreach (['faculties', 'employee_units', 'positions'] as $listKey) {
    sort($organization[$listKey], SORT_NATURAL | SORT_FLAG_CASE);
}

foreach ($organization['study_programs'] as &$programs) {
    sort($programs, SORT_NATURAL | SORT_FLAG_CASE);
}
unset($programs);

return $organization;

