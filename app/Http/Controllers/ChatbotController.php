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
        $rawMessage = trim((string) $request->input('message', ''));
        $message    = strtolower($rawMessage);
        $action     = (string) $request->input('action', '');

        $auditoriums = Auditorium::where('is_active', true)->get();

        // Deteksi filter lokasi dari kalimat user (dinamis dari database + kata kunci umum)
        $matchedLocation = $this->detectLocationFilter($message, $auditoriums);

        // Deteksi jumlah peserta dari input
        preg_match('/(\d+)\s*(orang|peserta|tamu|mahasiswa|kursi|kapasitas)/i', $message, $matches);
        $participants = (int) ($request->input('participants') ?: ($matches[1] ?? 0));

        // 1. REKOMENDASI RUANGAN BERDASARKAN JUMLAH PESERTA (+ OPSIONAL LOKASI)
        if ($action === 'recommend_room' || $participants > 0) {
            if ($participants <= 0) {
                return response()->json([
                    'reply' => 'Berapa perkiraan jumlah peserta acara Anda? Ketik angkanya saja (contoh: **150** atau **150 orang di Limo**) atau pilih rentang di bawah ini:',
                    'quick_replies' => [
                        ['label' => '≤ 100 Peserta', 'action' => 'recommend_room', 'participants' => 100],
                        ['label' => '101 - 250 Peserta', 'action' => 'recommend_room', 'participants' => 250],
                        ['label' => '> 250 Peserta', 'action' => 'recommend_room', 'participants' => 400],
                    ],
                ]);
            }

            $filtered = $auditoriums->filter(function ($room) use ($participants, $matchedLocation) {
                $fitCap = is_null($room->capacity) || (int) $room->capacity >= $participants;
                $fitLoc = !$matchedLocation || str_contains(strtolower((string) $room->location), $matchedLocation);
                return $fitCap && $fitLoc;
            })->sortBy('capacity')->values();

            if ($filtered->isEmpty()) {
                $locLabel = $matchedLocation ? " di area **" . ucwords($matchedLocation) . "**" : "";
                $maxRoom = $auditoriums->sortByDesc('capacity')->first();

                return response()->json([
                    'reply' => $maxRoom
                        ? "Belum ada auditorium aktif{$locLabel} yang memuat **{$participants} peserta**. Kapasitas terbesar saat ini adalah **{$maxRoom->name}** ({$maxRoom->capacity} orang — {$maxRoom->location})."
                        : "Belum ada data auditorium aktif yang memenuhi kapasitas {$participants} peserta.",
                    'quick_replies' => [
                        ['label' => 'Lihat Semua Auditorium', 'message' => 'daftar auditorium'],
                        ['label' => 'Auditorium di Limo', 'message' => 'auditorium di limo'],
                        ['label' => 'Auditorium di Pondok Labu', 'message' => 'auditorium di pondok labu'],
                    ],
                ]);
            }

            $locSuffix = $matchedLocation ? " di **" . ucwords($matchedLocation) . "**" : "";
            $lines = ["Berikut rekomendasi auditorium{$locSuffix} yang muat untuk **{$participants} peserta**:\n"];
            $quickReplies = [];

            foreach ($filtered as $room) {
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
            $audId = (int) $request->input('auditorium_id');
            $date  = (string) $request->input('date');
            $room  = Auditorium::find($audId);

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

        // 4. PERTANYAAN BERDASARKAN LOKASI / KAMPUS (MISAL: "LIMO", "PONDOK LABU")
        if ($matchedLocation) {
            $locRooms = $auditoriums->filter(
                fn ($aud) => str_contains(strtolower((string) $aud->location), $matchedLocation)
            )->values();

            $locTitle = ucwords($matchedLocation);

            if ($locRooms->isEmpty()) {
                return response()->json([
                    'reply' => "Saat ini belum ada auditorium aktif yang terdaftar di lokasi **{$locTitle}**.",
                    'quick_replies' => [
                        ['label' => 'Lihat Semua Auditorium', 'message' => 'daftar auditorium'],
                        ['label' => 'Menu Utama', 'message' => 'menu'],
                    ],
                ]);
            }

            $lines = ["Berikut daftar auditorium yang berlokasi di **{$locTitle}** ({$locRooms->count()} ruangan):\n"];
            $quickReplies = [];

            foreach ($locRooms as $aud) {
                $cap = $aud->capacity ? "{$aud->capacity} orang" : 'Kapasitas fleksibel';
                $loc = $aud->location ?: $locTitle;
                $lines[] = "• **{$aud->name}**\n  Lokasi: {$loc} | Kapasitas: {$cap}";

                $quickReplies[] = [
                    'label' => "Cek Jadwal {$aud->name}",
                    'action' => 'ask_date',
                    'auditorium_id' => $aud->id,
                    'auditorium_name' => $aud->name,
                ];
            }

            $quickReplies[] = ['label' => 'Semua Auditorium', 'message' => 'daftar auditorium'];

            return response()->json([
                'reply' => implode("\n", $lines),
                'quick_replies' => $quickReplies,
            ]);
        }

        // 5. INFO AUDITORIUM SPESIFIK (MENDUKUNG NAMA LENGKAP MAUPUN SINGKATAN SEPERTI BTI, MERCE, WAHIDIN, TANAH AIRKU)
        $matchedAud = $this->findAuditoriumByKeyword($message, $auditoriums);
        if ($matchedAud) {
            $facilities = is_array($matchedAud->facilities)
                ? implode(', ', $matchedAud->facilities)
                : ($matchedAud->facilities ?: 'Informasi menyusul');
            $cap = $matchedAud->capacity ? "{$matchedAud->capacity} orang" : 'Belum ditentukan';

            return response()->json([
                'reply' => "**{$matchedAud->name}**\n"
                         . "• **Lokasi:** " . ($matchedAud->location ?: '-') . "\n"
                         . "• **Kapasitas:** {$cap}\n"
                         . "• **Fasilitas:** {$facilities}\n"
                         . "• **Deskripsi:** " . ($matchedAud->description ?: '-'),
                'quick_replies' => [
                    [
                        'label' => "Cek Jadwal {$matchedAud->name}",
                        'action' => 'ask_date',
                        'auditorium_id' => $matchedAud->id,
                        'auditorium_name' => $matchedAud->name,
                    ],
                    ['label' => 'Lihat Auditorium Lain', 'message' => 'daftar auditorium'],
                    ['label' => 'Menu Utama', 'message' => 'menu'],
                ],
            ]);
        }

        // 6. PERTANYAAN KAPASITAS TERBESAR / TERKECIL
        if (str_contains($message, 'terbesar') || str_contains($message, 'paling besar') || str_contains($message, 'paling luas')) {
            $biggest = $auditoriums->sortByDesc('capacity')->first();
            if ($biggest) {
                return response()->json([
                    'reply' => "Auditorium dengan kapasitas terbesar saat ini adalah **{$biggest->name}** dengan kapasitas **{$biggest->capacity} orang** (Lokasi: {$biggest->location}).",
                    'quick_replies' => [
                        [
                            'label' => "Cek Jadwal {$biggest->name}",
                            'action' => 'ask_date',
                            'auditorium_id' => $biggest->id,
                            'auditorium_name' => $biggest->name,
                        ],
                        ['label' => 'Daftar Semua Auditorium', 'message' => 'daftar auditorium'],
                    ],
                ]);
            }
        }

        if (str_contains($message, 'terkecil') || str_contains($message, 'paling kecil')) {
            $smallest = $auditoriums->whereNotNull('capacity')->sortBy('capacity')->first();
            if ($smallest) {
                return response()->json([
                    'reply' => "Auditorium dengan kapasitas paling ringkas saat ini adalah **{$smallest->name}** dengan kapasitas **{$smallest->capacity} orang** (Lokasi: {$smallest->location}).",
                    'quick_replies' => [
                        [
                            'label' => "Cek Jadwal {$smallest->name}",
                            'action' => 'ask_date',
                            'auditorium_id' => $smallest->id,
                            'auditorium_name' => $smallest->name,
                        ],
                        ['label' => 'Daftar Semua Auditorium', 'message' => 'daftar auditorium'],
                    ],
                ]);
            }
        }

        // 7. PERTANYAAN UMUM TENTANG LOKASI / DAFTAR AUDITORIUM
        if (
            str_contains($message, 'lokasi') ||
            str_contains($message, 'dimana') ||
            str_contains($message, 'di mana') ||
            str_contains($message, 'alamat') ||
            str_contains($message, 'kampus') ||
            str_contains($message, 'auditorium') ||
            str_contains($message, 'ruang') ||
            str_contains($message, 'kapasitas') ||
            str_contains($message, 'fasilitas')
        ) {
            if ($auditoriums->isEmpty()) {
                return response()->json([
                    'reply' => 'Saat ini belum ada data auditorium aktif di sistem.',
                    'quick_replies' => $this->defaultQuickReplies(),
                ]);
            }

            $list = ["Daftar Auditorium Aktif di SIPADU beserta lokasinya:\n"];
            $replies = [
                ['label' => 'Kampus Limo', 'message' => 'auditorium di limo'],
                ['label' => 'Kampus Pondok Labu', 'message' => 'auditorium di pondok labu'],
            ];

            foreach ($auditoriums as $aud) {
                $cap = $aud->capacity ? "{$aud->capacity} orang" : '-';
                $loc = $aud->location ?: 'Kampus';
                $list[] = "• **{$aud->name}**\n  Lokasi: {$loc} | Kapasitas: {$cap}";
                $replies[] = ['label' => $aud->name, 'message' => strtolower($aud->name)];
            }

            return response()->json([
                'reply' => implode("\n", $list),
                'quick_replies' => $replies,
            ]);
        }

        // 8. FAQ DATABASE DARI TABEL SITE_CONTENTS
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

        // 9. SAPAAN / MENU UTAMA / DEFAULT FALLBACK
        if (in_array($message, ['halo', 'hai', 'hi', 'menu', 'mulai', 'start', 'p'])) {
            return response()->json([
                'reply' => "Halo! Saya **Asisten SIPADU**. Anda bisa menanyakan daftar ruangan, auditorium per kampus (Limo / Pondok Labu), kapasitas peserta, hingga cek jadwal.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        return response()->json([
            'reply' => "Maaf, saya belum menemukan jawaban spesifik untuk pertanyaan tersebut. Coba ketik contoh seperti **\"auditorium di limo\"**, **\"kapasitas 150 orang\"**, atau pilih menu di bawah ini:",
            'quick_replies' => $this->defaultQuickReplies(),
        ]);
    }

    /**
     * Deteksi kata kunci lokasi dari pesan pengguna berdasarkan data lokasi di DB & kata kunci kampus.
     */
    private function detectLocationFilter(string $message, $auditoriums): ?string
    {
        $knownAreas = ['pondok labu', 'limo', 'depok', 'jakarta selatan', 'cinere'];

        foreach ($auditoriums as $aud) {
            $loc = strtolower((string) $aud->location);
            if ($loc === '') {
                continue;
            }
            // Ambil bagian setelah kata "kampus" atau tanda "-" jika ada (misal: "Kampus Limo" -> "limo")
            if (preg_match('/(?:kampus|-|–)\s*([a-z0-9\s]+)$/i', $loc, $m)) {
                $area = trim(strtolower($m[1]));
                if ($area !== '' && !in_array($area, $knownAreas, true)) {
                    $knownAreas[] = $area;
                }
            }
        }

        // Urutkan dari frasa terpanjang agar "pondok labu" dicek lebih dulu daripada kata pendek
        usort($knownAreas, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($knownAreas as $area) {
            if (str_contains($message, $area)) {
                return $area;
            }
        }

        return null;
    }

    /**
     * Pencocokan cerdas nama auditorium (nama utuh, dalam kurung seperti "bti", atau kata kunci unik).
     */
    private function findAuditoriumByKeyword(string $message, $auditoriums): ?Auditorium
    {
        $stopWords = ['auditorium', 'gedung', 'gd', 'dr', 'prof', 'ruang', 'aula', 'dan', 'di', 'yang'];

        foreach ($auditoriums as $aud) {
            $fullName = strtolower((string) $aud->name);

            // 1. Cocok nama utuh
            if (str_contains($message, $fullName)) {
                return $aud;
            }

            // 2. Cocok singkatan di dalam tanda kurung, misal "(BTI)" -> "bti"
            if (preg_match('/\(([^)]+)\)/', $fullName, $m)) {
                $abbr = trim($m[1]);
                if ($abbr !== '' && preg_match('/\b' . preg_quote($abbr, '/') . '\b/i', $message)) {
                    return $aud;
                }
            }

            // 3. Cocok kata unik penting dari nama ruangan (misal: "bhineka", "wahidin", "merce", "tanah airku")
            $cleanName = preg_replace('/[^a-z0-9\s]/', ' ', $fullName);
            $tokens = array_filter(explode(' ', $cleanName), fn ($w) => strlen($w) >= 4 && !in_array($w, $stopWords, true));

            foreach ($tokens as $token) {
                if (preg_match('/\b' . preg_quote($token, '/') . '\b/i', $message)) {
                    return $aud;
                }
            }
        }

        return null;
    }

    private function defaultQuickReplies(): array
    {
        return [
            ['label' => 'Cari Ruangan & Cek Jadwal', 'action' => 'recommend_room'],
            ['label' => 'Auditorium di Limo', 'message' => 'auditorium di limo'],
            ['label' => 'Auditorium di Pondok Labu', 'message' => 'auditorium di pondok labu'],
            ['label' => 'Semua Auditorium', 'message' => 'daftar auditorium'],
            ['label' => 'Alur Pengajuan', 'message' => 'alur pengajuan'],
            ['label' => 'SOP & Syarat', 'message' => 'sop'],
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