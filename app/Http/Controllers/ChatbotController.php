<?php

namespace App\Http\Controllers;

use App\Models\Auditorium;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChatbotController extends Controller
{
    public function reply(Request $request)
    {
        $message = strtolower(trim((string) $request->input('message', '')));
        $action  = (string) $request->input('action', '');

        // 1. REKOMENDASI RUANGAN BERDASARKAN JUMLAH PESERTA
        if ($action === 'recommend_room' || preg_match('/(\d+)\s*(orang|peserta|tamu|mahasiswa)/i', $message, $matches)) {
            $participants = (int) ($request->input('participants') ?: ($matches[1] ?? 0));

            if ($participants <= 0) {
                return response()->json([
                    'reply' => 'Berapa perkiraan jumlah peserta acara Anda? Ketik angkanya saja (contoh: **150**) atau pilih rentang di bawah ini:',
                    'quick_replies' => [
                        ['label' => '≤ 100 Peserta', 'action' => 'recommend_room', 'participants' => 100],
                        ['label' => '101 - 250 Peserta', 'action' => 'recommend_room', 'participants' => 250],
                        ['label' => '> 250 Peserta', 'action' => 'recommend_room', 'participants' => 400],
                    ],
                ]);
            }

            $rooms = Auditorium::where('is_active', true)
                ->where(function ($q) use ($participants) {
                    $q->whereNull('capacity')->orWhere('capacity', '>=', $participants);
                })
                ->orderBy('capacity')
                ->get();

            if ($rooms->isEmpty()) {
                $maxRoom = Auditorium::where('is_active', true)->orderByDesc('capacity')->first();
                return response()->json([
                    'reply' => $maxRoom
                        ? "Untuk **{$participants} peserta**, kapasitas terbesar yang tersedia saat ini adalah **{$maxRoom->name}** (kapasitas {$maxRoom->capacity} orang). Silakan hubungi pengelola untuk koordinasi khusus."
                        : "Belum ada data auditorium aktif yang memenuhi kapasitas {$participants} peserta.",
                    'quick_replies' => [
                        ['label' => 'Lihat Semua Auditorium', 'message' => 'daftar auditorium'],
                        ['label' => 'Hubungi Pengelola', 'message' => 'contact person'],
                    ],
                ]);
            }

            $lines = ["Berikut rekomendasi auditorium yang muat untuk **{$participants} peserta**:\n"];
            $quickReplies = [];

            foreach ($rooms as $room) {
                $cap = $room->capacity ? "{$room->capacity} orang" : 'Kapasitas fleksibel';
                $loc = $room->location ?: 'Kampus';
                $lines[] = "• **{$room->name}** ({$cap} — {$loc})";

                $quickReplies[] = [
                    'label' => "Cek Jadwal {$room->name}",
                    'action' => 'ask_date',
                    'auditorium_id' => $room->id,
                    'auditorium_name' => $room->name,
                ];
            }

            return response()->json([
                'reply' => implode("\n", $lines),
                'quick_replies' => $quickReplies,
            ]);
        }

        // 2. PILIH TANGGAL UNTUK AUDITORIUM TERPILIH
        if ($action === 'ask_date') {
            $audId   = $request->input('auditorium_id');
            $audName = $request->input('auditorium_name', 'Auditorium');

            return response()->json([
                'reply' => "Silakan pilih tanggal rencana penggunaan untuk **{$audName}**:",
                'show_date_picker' => true,
                'auditorium_id' => $audId,
                'auditorium_name' => $audName,
            ]);
        }

        // 3. CEK KETERSEDIAAN SESI PADA TANGGAL TERTENTU
        if ($action === 'check_availability') {
            $audId   = (int) $request->input('auditorium_id');
            $date    = (string) $request->input('date');
            $room    = Auditorium::find($audId);

            if (!$room || !$date) {
                return response()->json([
                    'reply' => 'Data auditorium atau tanggal belum lengkap. Silakan pilih ulang.',
                    'quick_replies' => [
                        ['label' => 'Rekomendasi Ruangan', 'action' => 'recommend_room'],
                    ],
                ]);
            }

            $bookedSessions = $this->getBookedSessions($audId, $date);
            $isFullDayBlocked = in_array('full_day', $bookedSessions)
                || (in_array('pagi', $bookedSessions) && in_array('siang', $bookedSessions));

            $pagiStatus  = ($isFullDayBlocked || in_array('pagi', $bookedSessions)) ? '❌ Terisi' : '✅ Tersedia';
            $siangStatus = ($isFullDayBlocked || in_array('siang', $bookedSessions)) ? '❌ Terisi' : '✅ Tersedia';
            $fullStatus  = (!empty($bookedSessions)) ? '❌ Tidak Tersedia' : '✅ Tersedia';

            $formattedDate = date('d M Y', strtotime($date));
            $bookingUrl = $this->resolveBookingUrl($room->id, $date);

            $reply = "Status ketersediaan **{$room->name}** pada **{$formattedDate}**:\n"
                   . "• Sesi Pagi (08:00 - 12:00): **{$pagiStatus}**\n"
                   . "• Sesi Siang (13:00 - 17:00): **{$siangStatus}**\n"
                   . "• Full Day (08:00 - 17:00): **{$fullStatus}**";

            $quickReplies = [];
            if (!$isFullDayBlocked) {
                $quickReplies[] = [
                    'label' => 'Ajukan Peminjaman Sekarang →',
                    'redirect_url' => $bookingUrl,
                ];
            }
            $quickReplies[] = [
                'label' => 'Cek Tanggal Lain',
                'action' => 'ask_date',
                'auditorium_id' => $room->id,
                'auditorium_name' => $room->name,
            ];
            $quickReplies[] = ['label' => 'Menu Utama', 'message' => 'menu'];

            return response()->json([
                'reply' => $reply,
                'quick_replies' => $quickReplies,
            ]);
        }

        // 4. FAQ DATABASE DARI TABEL SITE_CONTENTS
        $contents = SiteContent::all()->keyBy('key');

        if (str_contains($message, 'alur') || str_contains($message, 'cara pinjam') || str_contains($message, 'prosedur')) {
            $item = $contents->get('alur');
            return response()->json([
                'reply' => $item
                    ? "**{$item->title}**\n\n{$item->body}"
                    : "Alur pengajuan: 1) Pilih auditorium & tanggal, 2) Lengkapi formulir & unggah proposal PDF, 3) Tunggu verifikasi admin, 4) Unduh surat persetujuan.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        if (str_contains($message, 'sop') || str_contains($message, 'syarat') || str_contains($message, 'aturan') || str_contains($message, 'ketentuan')) {
            $item = $contents->get('sop');
            return response()->json([
                'reply' => $item
                    ? "**{$item->title}**\n\n{$item->body}"
                    : "Pastikan pengajuan dilakukan sesuai jadwal operasional kampus dan melampirkan surat permohonan resmi.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        if (str_contains($message, 'faq') || str_contains($message, 'tanya') || str_contains($message, 'pertanyaan')) {
            $item = $contents->get('faq');
            return response()->json([
                'reply' => $item
                    ? "**{$item->title}**\n\n{$item->body}"
                    : "Silakan pilih topik pertanyaan yang ingin Anda ketahui di bawah ini.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        if (str_contains($message, 'kontak') || str_contains($message, 'contact') || str_contains($message, 'admin') || str_contains($message, 'hubungi') || str_contains($message, 'wa')) {
            $item = $contents->get('contact');
            return response()->json([
                'reply' => $item
                    ? "**{$item->title}**\n\n{$item->body}"
                    : "Untuk bantuan lebih lanjut, silakan hubungi bagian Sarana & Prasarana / Pengelola Auditorium kampus pada jam kerja.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        // 5. INFO AUDITORIUM SPESIFIK DARI DATABASE
        $auditoriums = Auditorium::where('is_active', true)->get();
        foreach ($auditoriums as $aud) {
            if (str_contains($message, strtolower($aud->name))) {
                $facilities = is_array($aud->facilities) ? implode(', ', $aud->facilities) : ($aud->facilities ?: 'Standar ruangan');
                $cap = $aud->capacity ? "{$aud->capacity} orang" : 'Belum ditentukan';
                return response()->json([
                    'reply' => "**{$aud->name}**\n"
                             . "• **Lokasi:** " . ($aud->location ?: '-') . "\n"
                             . "• **Kapasitas:** {$cap}\n"
                             . "• **Fasilitas:** {$facilities}\n"
                             . "• **Deskripsi:** " . ($aud->description ?: '-'),
                    'quick_replies' => [
                        ['label' => "Cek Jadwal {$aud->name}", 'action' => 'ask_date', 'auditorium_id' => $aud->id, 'auditorium_name' => $aud->name],
                        ['label' => 'Menu Utama', 'message' => 'menu'],
                    ],
                ]);
            }
        }

        if (str_contains($message, 'auditorium') || str_contains($message, 'ruang') || str_contains($message, 'kapasitas') || str_contains($message, 'fasilitas')) {
            if ($auditoriums->isEmpty()) {
                return response()->json([
                    'reply' => 'Saat ini belum ada data auditorium aktif di sistem.',
                    'quick_replies' => $this->defaultQuickReplies(),
                ]);
            }

            $list = ["Daftar Auditorium Aktif di SIPADU:\n"];
            $replies = [];
            foreach ($auditoriums as $aud) {
                $cap = $aud->capacity ? "{$aud->capacity} orang" : '-';
                $list[] = "• **{$aud->name}** (Kapasitas: {$cap})";
                $replies[] = ['label' => $aud->name, 'message' => strtolower($aud->name)];
            }

            return response()->json([
                'reply' => implode("\n", $list),
                'quick_replies' => $replies,
            ]);
        }

        // 6. SAPAAN / MENU UTAMA / DEFAULT FALLBACK
        if (in_array($message, ['halo', 'hai', 'hi', 'menu', 'mulai', 'start', 'p'])) {
            return response()->json([
                'reply' => "Halo! Saya **Asisten SIPADU**. Ada yang bisa saya bantu terkait peminjaman auditorium?",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        return response()->json([
            'reply' => "Maaf, saya belum menemukan jawaban spesifik untuk pertanyaan tersebut. Silakan pilih menu bantuan cepat di bawah ini:",
            'quick_replies' => $this->defaultQuickReplies(),
        ]);
    }

    private function defaultQuickReplies(): array
    {
        return [
            ['label' => 'Cari Ruangan & Cek Jadwal', 'action' => 'recommend_room'],
            ['label' => 'Daftar Auditorium', 'message' => 'daftar auditorium'],
            ['label' => 'Alur Pengajuan', 'message' => 'alur pengajuan'],
            ['label' => 'SOP & Syarat', 'message' => 'sop'],
            ['label' => 'Contact Person', 'message' => 'contact person'],
        ];
    }

    private function getBookedSessions(int $auditoriumId, string $date): array
    {
        if (!Schema::hasTable('bookings')) {
            return [];
        }

        $dateCol = Schema::hasColumn('bookings', 'booking_date') ? 'booking_date' : 'start_date';
        $sessionCol = Schema::hasColumn('bookings', 'session') ? 'session' : null;

        $query = DB::table('bookings')
            ->where('auditorium_id', $auditoriumId)
            ->whereDate($dateCol, $date)
            ->whereIn('status', ['pending', 'approved', 'menunggu', 'disetujui']);

        if ($sessionCol) {
            return $query->pluck($sessionCol)->map(fn ($s) => strtolower((string) $s))->toArray();
        }

        return $query->exists() ? ['full_day'] : [];
    }

    private function resolveBookingUrl(int $auditoriumId, string $date): string
    {
        $params = ['auditorium_id' => $auditoriumId, 'date' => $date];
        $role = auth()->user()->role ?? null;

        if ($role === 'pegawai') {
            return route('pegawai.booking.create', $params);
        }

        if ($role === 'mahasiswa') {
            return route('borrower.booking.create', $params);
        }

        if ($role === 'admin') {
            return route('admin.bookings');
        }

        return route('login');
    }
}