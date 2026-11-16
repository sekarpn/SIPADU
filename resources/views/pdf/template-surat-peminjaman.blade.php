<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Template Surat Permohonan Peminjaman Auditorium UPNVJ</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
            margin: 1.5cm 2cm;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .kop-surat h3, .kop-surat h4, .kop-surat p {
            margin: 0;
        }
        .kop-surat h3 {
            font-size: 13pt;
            text-transform: uppercase;
        }
        .kop-surat h4 {
            font-size: 12pt;
            text-transform: uppercase;
        }
        .kop-surat p {
            font-size: 10pt;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .detail-table {
            width: 100%;
            margin: 12px 0 16px 20px;
            border-collapse: collapse;
        }
        .detail-table td {
            vertical-align: top;
            padding: 3px 0;
        }
        .ttd-table {
            width: 100%;
            margin-top: 30px;
            text-align: center;
            border-collapse: collapse;
        }
        .ttd-space {
            height: 65px;
        }
    </style>
</head>
<body>

    <div class="kop-surat">
        <h4>KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h4>
        <h3>UNIVERSITAS PEMBANGUNAN NASIONAL “VETERAN” JAKARTA</h3>
        <h4>[NAMA ORGANISASI MAHASISWA / UNIT / BIRO PENGUSUL]</h4>
        <p>Jl. RS. Fatmawati No. 1, Pondok Labu, Jakarta Selatan 12450 • Telp. (021) 7656971</p>
    </div>

    <table class="meta-table">
        <tr>
            <td width="15%">Nomor</td>
            <td width="2%">:</td>
            <td width="48%">.../UN61/.../KM/{{ date('Y') }}</td>
            <td width="35%" style="text-align: right;">Jakarta, {{ $tanggal_surat }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>1 (Satu) Berkas Proposal Kegiatan</td>
            <td></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td><strong>Permohonan Peminjaman Ruang Auditorium</strong></td>
            <td></td>
        </tr>
    </table>

    <p>
        Yth. <strong>Kepala Biro Umum dan Keuangan (BUK)</strong><br>
        Universitas Pembangunan Nasional “Veteran” Jakarta<br>
        di Tempat
    </p>

    <p style="text-align: justify;">
        Dengan hormat,<br>
        Sehubungan dengan akan dilaksanakannya kegiatan <strong>[Nama Kegiatan]</strong> yang diselenggarakan oleh <strong>[Nama Organisasi / Unit]</strong>, bersama ini kami mengajukan permohonan peminjaman fasilitas auditorium di lingkungan UPN “Veteran” Jakarta dengan rincian sebagai berikut:
    </p>

    <table class="detail-table">
        <tr>
            <td width="30%">Ruang Auditorium</td>
            <td width="3%">:</td>
            <td>[Auditorium Utama / Auditorium Bhinneka Tunggal Ika]</td>
        </tr>
        <tr>
            <td>Hari / Tanggal</td>
            <td>:</td>
            <td>[Hari, Tanggal Bulan Tahun]</td>
        </tr>
        <tr>
            <td>Sesi & Waktu</td>
            <td>:</td>
            <td>[Sesi Pagi (08.00–12.00) / Sesi Siang (13.00–17.00) / Full Day (08.00–17.00)] WIB</td>
        </tr>
        <tr>
            <td>Perkiraan Peserta</td>
            <td>:</td>
            <td>[...] Orang</td>
        </tr>
        <tr>
            <td>Penanggung Jawab</td>
            <td>:</td>
            <td>{{ $nama_pemohon }} ({{ $nomor_induk }})</td>
        </tr>
        <tr>
            <td>No. Handphone / WA</td>
            <td>:</td>
            <td>{{ $no_telepon }}</td>
        </tr>
    </table>

    <p style="text-align: justify;">
        Sebagai bahan pertimbangan, bersama surat ini turut kami lampirkan Proposal Kegiatan yang telah disahkan. Kami bersedia mematuhi seluruh tata tertib serta menjaga kebersihan dan ketertiban fasilitas auditorium selama kegiatan berlangsung.
    </p>

    <p>Demikian permohonan ini kami sampaikan. Atas perhatian dan izin yang diberikan, kami ucapkan terima kasih.</p>

    <table class="ttd-table">
        <tr>
            <td width="50%">
                Mengetahui,<br>
                <strong>Wakil Dekan Bidang Umum dan Keuangan /<br>Koordinator Bidang Kemahasiswaan*</strong>
                <div class="ttd-space"></div>
                <u><strong>( .................................................... )</strong></u><br>
                NIP. ........................................
            </td>
            <td width="50%">
                Hormat kami,<br>
                <strong>Ketua Pelaksana / Penanggung Jawab</strong>
                <div class="ttd-space"></div>
                <u><strong>{{ $nama_pemohon }}</strong></u><br>
                NIM/NIP. {{ $nomor_induk }}
            </td>
        </tr>
    </table>

    <p style="font-size: 9pt; margin-top: 25px; color: #333333;">
        <em>*Catatan: Surat wajib ditandatangani oleh Dekanat (WaDek II) untuk Ormawa Fakultas, atau Koordinator Bidang Kemahasiswaan khusus untuk UKM tingkat Universitas.</em>
    </p>

</body>
</html>