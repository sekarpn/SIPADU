<?php

namespace Database\Seeders;

use App\Models\Auditorium;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* akun admin */
        User::updateOrCreate(
            [
                'email' => 'admin@upnvj.ac.id',
            ],
            [
                'name' => 'Admin SIPADU',
                'nim_nip' => 'ADMIN-001',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        /* data auditorium */
        Auditorium::firstOrCreate(
            [
                'name' => 'Auditorium Utama',
            ],
            [
                'location' => 'UPN Veteran Jakarta',
                'capacity' => null,
                'description' =>
                    'Informasi kapasitas dan fasilitas menunggu konfirmasi pengelola.',
                'facilities' => [
                    'Informasi menyusul',
                ],
                'is_active' => true,
            ]
        );

        /* konten sipadu */
        $contents = [
            [
                'key' => 'alur',
                'title' => 'Alur Pengajuan Peminjaman',
                'body' => "1. Himpunan, BEM Fakultas atau KSM mengajukan proposal dan surat peminjaman kepada pihak fakultas.\n\n"
                    . "2. Fakultas membuat permohonan peminjaman tempat dan sarana prasarana yang ditandatangani Dekanat (WaDek II).\n\n"
                    . "3. Universitas memeriksa apakah ruangan tersedia atau telah digunakan pihak lain yang lebih dahulu mengajukan.\n\n"
                    . "4. Jika ruangan tersedia dan perizinan lengkap, peminjaman dapat disetujui.",
                'sort_order' => 0,
            ],

            [
                'key' => 'sop',
                'title' => 'SOP Peminjaman Ruang Auditorium/Aula UPN Veteran Jakarta',
                'body' => "UKM dapat mengajukan proposal dan surat peminjaman ke KoorBid Kemahasiswaan.\n\n"
                    . "Jika ada acara yang jadwalnya bersamaan dengan pihak rektorat, pihak rektorat didahulukan.\n\n"
                    . "Jika jadwal BEM dan Himpunan bersamaan, akan dilihat tingkat urgensi dan kepentingannya.\n\n"
                    . "Tingkat urgensi dan pentingnya acara ditentukan Kepala Biro Umum & Keuangan atas nama Rektor.\n\n"
                    . "Jika terdapat acara eksternal dan internal UPN yang jadwalnya bersamaan, akan dilihat tujuan masing-masing acara dan apakah narasumber berasal dari luar.\n\n"
                    . "Cara/alur peminjaman ruangan di setiap fakultas: http://bit.ly/AlurPeminjamanRuanganPerFakultas "
                    . "(hanya dapat diakses menggunakan email UPNVJ).",
                'sort_order' => 1,
            ],

            [
                'key' => 'faq',
                'title' => 'FAQ',
                'body' => "Apakah pengajuan langsung disetujui?\n"
                    . "Tidak. Pengajuan akan diperiksa oleh pengelola.\n\n"
                    . "Bagaimana mengetahui jadwal tersedia?\n"
                    . "Periksa kalender dan tunggu verifikasi Admin.\n\n"
                    . "Siapa yang dapat mengajukan?\n"
                    . "Organisasi/unit di lingkungan UPNVJ sesuai ketentuan.",
                'sort_order' => 2,
            ],

            [
                'key' => 'contact',
                'title' => 'Contact Person',
                'body' =>
                    'Informasi contact person resmi akan dilengkapi oleh Admin/pengelola auditorium.',
                'sort_order' => 3,
            ],
        ];

        foreach ($contents as $content) {
            SiteContent::updateOrCreate(
                [
                    'key' => $content['key'],
                ],
                [
                    'title' => $content['title'],
                    'body' => $content['body'],
                    'sort_order' => $content['sort_order'],
                ]
            );
        }
    }
}