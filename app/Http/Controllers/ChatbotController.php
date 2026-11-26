<?php

namespace App\Http\Controllers;

use App\Models\Auditorium;
use App\Models\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class ChatbotController extends Controller
{
    public function reply(Request $request)
    {
        $rawMessage = trim((string) $request->input('message', ''));
        $message    = strtolower($rawMessage);
        $action     = (string) $request->input('action', '');

        $auditoriums = Auditorium::where('is_active', true)->get();

        // 1. AKSI INTERAKTIF UI (DATE PICKER & CEK JADWAL LANGSUNG)
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

        if ($action === 'check_availability') {
            return $this->handleCheckAvailability(
                (int) $request->input('auditorium_id'),
                (string) $request->input('date')
            );
        }

        if ($action === 'recommend_room' && !$request->filled('participants')) {
            return response()->json([
                'reply' => 'Berapa perkiraan jumlah peserta acara Anda? Ketik langsung (contoh: **"200 orang di Limo"**) atau pilih rentang di bawah ini:',
                'quick_replies' => [
                    ['label' => '≤ 100 Peserta', 'action' => 'recommend_room', 'participants' => 100],
                    ['label' => '101 - 250 Peserta', 'action' => 'recommend_room', 'participants' => 250],
                    ['label' => '> 250 Peserta', 'action' => 'recommend_room', 'participants' => 400],
                ],
            ]);
        }

        // 2. MODE AI RAG (MENCARI INFO OTOMATIS DARI FAKTA DATABASE SIPADU)
        $apiKey = config('services.gemini.key');
        if (!empty($apiKey) && $rawMessage !== '' && !in_array($message, ['halo', 'hai', 'hi', 'menu', 'mulai', 'start', 'p'])) {
            $aiResponse = $this->askAiWithDatabaseFacts($rawMessage, $auditoriums, $request->input('participants'));
            if ($aiResponse !== null) {
                return response()->json($aiResponse);
            }
        }

        // 3. FALLBACK LOKAL (JIKA SAPAAN AWAL / API KEY BELUM DIISI / OFFLINE)
        return $this->localRuleFallback($message, $action, $auditoriums, $request->input('participants'));
    }

    /**
     * Mengumpulkan fakta database real-time lalu meminta AI menjawab strictly berdasarkan fakta tersebut.
     */
    private function askAiWithDatabaseFacts(string $userQuestion, $auditoriums, $explicitParticipants = null): ?array
    {
        $factsContext = $this->buildDatabaseFacts($auditoriums);
        $today = now()->format('Y-m-d (l)');

        $systemPrompt = <<<PROMPT
Anda adalah **Asisten SIPADU** (Sistem Peminjaman Auditorium).
Hari ini tanggal: {$today}.

TUGAS ANDA:
Jawab pertanyaan pengguna secara ramah, ringkas, dan akurat HANYA berdasarkan **FAKTA DATABASE SIPADU** di bawah ini.

ATURAN KETAT:
1. DILARANG mengarang fakta, kapasitas, fasilitas, lokasi, syarat, atau jadwal di luar data yang diberikan.
2. Jika informasi tidak tercantum di fakta database (misalnya fasilitas masih tertulis "Informasi menyusul"), sampaikan apa adanya sesuai database.
3. Gunakan format Markdown tebal (**teks**) untuk nama auditorium, lokasi, atau angka penting agar mudah dibaca di bubble chat.
4. Jawab singkat dan langsung ke inti pertanyaan (maksimal 5-7 baris).
5. Jika pengguna menanyakan auditorium tertentu, sebutkan juga bahwa mereka bisa langsung mengecek jadwalnya lewat tombol di bawah.

=== FAKTA DATABASE SIPADU ===
{$factsContext}
=== AKHIR FAKTA ===
PROMPT;

        if ($explicitParticipants) {
            $userQuestion .= " (Jumlah peserta yang dipilih: {$explicitParticipants} orang)";
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.5-flash');
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . config('services.gemini.key');

            $response = Http::timeout(12)->post($url, [
                'systemInstruction' => [
                    'parts' => [['text' => $systemPrompt]],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $userQuestion]],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 500,
                ],
            ]);

            if (!$response->successful()) {
                return null;
            }

            $replyText = data_get($response->json(), 'candidates.0.content.parts.0.text');
            if (empty($replyText)) {
                return null;
            }

            return [
                'reply' => trim($replyText),
                'quick_replies' => $this->buildSmartQuickReplies($userQuestion, $replyText, $auditoriums),
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Menyusun seluruh fakta aktual dari database SIPADU (Auditorium, Konten SOP/Alur/FAQ, & Jadwal Terbooking).
     */
    private function buildDatabaseFacts($auditoriums): string
    {
        $sections = [];

        // A. Fakta Auditorium Aktif
        $audLines = ["[DAFTAR AUDITORIUM AKTIF]"];
        if ($auditoriums->isEmpty()) {
            $audLines[] = "- Belum ada auditorium aktif.";
        } else {
            foreach ($auditoriums as $aud) {
                $facilities = is_array($aud->facilities)
                    ? implode(', ', $aud->facilities)
                    : ($aud->facilities ?: 'Informasi menyusul');
                $cap = $aud->capacity ? "{$aud->capacity} orang" : 'Belum ditentukan';
                $loc = $aud->location ?: 'Tidak disebutkan';
                $desc = $aud->description ?: '-';

                $audLines[] = "- ID: {$aud->id} | Nama: {$aud->name} | Lokasi: {$loc} | Kapasitas: {$cap} | Fasilitas: {$facilities} | Deskripsi: {$desc}";
            }
        }
        $sections[] = implode("\n", $audLines);

        // B. Fakta Konten Informasi (Alur, SOP, FAQ, Kontak)
        $contents = SiteContent::all();
        if ($contents->isNotEmpty()) {
            $contentLines = ["[INFORMASI PANDUAN, SOP, ALUR & KONTAK]"];
            foreach ($contents as $item) {
                $bodyClean = trim(preg_replace('/\s+/', ' ', strip_tags((string) $item->body)));
                $contentLines[] = "- [{$item->key}] {$item->title}: {$bodyClean}";
            }
            $sections[] = implode("\n", $contentLines);
        }

        // C. Fakta Jadwal Terpakai (45 hari ke depan)
        if (Schema::hasTable('bookings')) {
            $dateCol = Schema::hasColumn('bookings', 'booking_date') ? 'booking_date' : 'start_date';
            $sessionCol = Schema::hasColumn('bookings', 'session') ? 'session' : null;

            $upcoming = DB::table('bookings')
                ->whereDate($dateCol, '>=', now()->toDateString())
                ->whereDate($dateCol, '<=', now()->addDays(45)->toDateString())
                ->whereIn('status', ['pending', 'approved', 'menunggu', 'disetujui'])
                ->orderBy($dateCol)
                ->limit(40)
                ->get();

            $bookLines = ["[JADWAL YANG SUDAH TERISI / DIBOOKING]"];
            if ($upcoming->isEmpty()) {
                $bookLines[] = "- Belum ada jadwal yang terisi dalam 45 hari ke depan (semua sesi masih kosong).";
            } else {
                $audMap = $auditoriums->pluck('name', 'id');
                foreach ($upcoming as $b) {
                    $rName = $audMap[$b->auditorium_id] ?? "Auditorium #{$b->auditorium_id}";
                    $sesi = $sessionCol && !empty($b->{$sessionCol}) ? strtoupper($b->{$sessionCol}) : 'FULL DAY';
                    $bookLines[] = "- Tanggal {$b->{$dateCol}} | {$rName} | Sesi: {$sesi} (Status: {$b->status})";
                }
            }
            $sections[] = implode("\n", $bookLines);
        }

        return implode("\n\n", $sections);
    }

    /**
     * Menambahkan tombol cepat (Cek Jadwal) secara otomatis jika jawaban AI menyebut nama auditorium.
     */
    private function buildSmartQuickReplies(string $userQuestion, string $aiReply, $auditoriums): array
    {
        $combined = strtolower($userQuestion . ' ' . $aiReply);
        $replies = [];

        foreach ($auditoriums as $aud) {
            if (str_contains($combined, strtolower($aud->name))) {
                $replies[] = [
                    'label' => "Cek Jadwal {$aud->name}",
                    'action' => 'ask_date',
                    'auditorium_id' => $aud->id,
                    'auditorium_name' => $aud->name,
                ];
            }
        }

        if (empty($replies)) {
            return $this->defaultQuickReplies();
        }

        $replies[] = ['label' => 'Menu Utama', 'message' => 'menu'];
        return array_slice($replies, 0, 4);
    }

    private function handleCheckAvailability(int $audId, string $date)
    {
        $room = Auditorium::find($audId);

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

    /**
     * Fallback aturan lokal jika Gemini API offline / belum dikonfigurasi / sapaan awal.
     */
    private function localRuleFallback(string $message, string $action, $auditoriums, $explicitParticipants = null)
    {
        $matchedLocation = $this->detectLocationFilter($message, $auditoriums);

        preg_match('/(\d+)\s*(orang|peserta|tamu|mahasiswa|kursi|kapasitas)/i', $message, $matches);
        $participants = (int) ($explicitParticipants ?: ($matches[1] ?? 0));

        // 1. Rekomendasi berdasarkan jumlah peserta (+ opsional lokasi)
        if ($action === 'recommend_room' || $participants > 0) {
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
                    'quick_replies' => $this->defaultQuickReplies(),
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

        // 2. Filter lokasi spesifik
        if ($matchedLocation) {
            $locRooms = $auditoriums->filter(
                fn ($aud) => str_contains(strtolower((string) $aud->location), $matchedLocation)
            )->values();

            $locTitle = ucwords($matchedLocation);

            if ($locRooms->isEmpty()) {
                return response()->json([
                    'reply' => "Saat ini belum ada auditorium aktif yang terdaftar di lokasi **{$locTitle}**.",
                    'quick_replies' => $this->defaultQuickReplies(),
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

            $quickReplies[] = ['label' => 'Menu Utama', 'message' => 'menu'];

            return response()->json([
                'reply' => implode("\n", $lines),
                'quick_replies' => $quickReplies,
            ]);
        }

        // 3. Pencocokan nama auditorium spesifik
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
                    ['label' => 'Menu Utama', 'message' => 'menu'],
                ],
            ]);
        }

        // 4. Sapaan / Menu Utama
        if (in_array($message, ['halo', 'hai', 'hi', 'menu', 'mulai', 'start', 'p'])) {
            return response()->json([
                'reply' => "Halo! Saya **Asisten SIPADU**. Silakan tanyakan informasi auditorium, lokasi kampus (Limo / Pondok Labu), kapasitas peserta, syarat peminjaman, atau cek jadwal.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        // 5. Konten SOP / Alur / Kontak dari DB
        $contents = SiteContent::all()->keyBy('key');
        if (str_contains($message, 'alur') || str_contains($message, 'cara pinjam')) {
            $item = $contents->get('alur');
            return response()->json([
                'reply' => $item ? "**{$item->title}**\n\n{$item->body}" : "Alur pengajuan: 1) Pilih auditorium & tanggal, 2) Lengkapi formulir & unggah proposal PDF, 3) Tunggu verifikasi admin, 4) Unduh surat persetujuan.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        if (str_contains($message, 'sop') || str_contains($message, 'syarat')) {
            $item = $contents->get('sop');
            return response()->json([
                'reply' => $item ? "**{$item->title}**\n\n{$item->body}" : "Pastikan pengajuan dilakukan sesuai jadwal operasional kampus dan melampirkan surat permohonan resmi.",
                'quick_replies' => $this->defaultQuickReplies(),
            ]);
        }

        // 6. Default daftar semua auditorium
        $list = ["Daftar Auditorium Aktif di SIPADU:\n"];
        foreach ($auditoriums as $aud) {
            $cap = $aud->capacity ? "{$aud->capacity} orang" : '-';
            $loc = $aud->location ?: 'Kampus';
            $list[] = "• **{$aud->name}** ({$loc} — {$cap})";
        }

        return response()->json([
            'reply' => implode("\n", $list),
            'quick_replies' => $this->defaultQuickReplies(),
        ]);
    }

    private function detectLocationFilter(string $message, $auditoriums): ?string
    {
        $knownAreas = ['pondok labu', 'limo', 'depok', 'jakarta selatan', 'cinere'];

        foreach ($auditoriums as $aud) {
            $loc = strtolower((string) $aud->location);
            if ($loc !== '' && preg_match('/(?:kampus|-|–)\s*([a-z0-9\s]+)$/i', $loc, $m)) {
                $area = trim(strtolower($m[1]));
                if ($area !== '' && !in_array($area, $knownAreas, true)) {
                    $knownAreas[] = $area;
                }
            }
        }

        usort($knownAreas, fn ($a, $b) => strlen($b) <=> strlen($a));

        foreach ($knownAreas as $area) {
            if (str_contains($message, $area)) {
                return $area;
            }
        }

        return null;
    }

    private function findAuditoriumByKeyword(string $message, $auditoriums): ?Auditorium
    {
        $stopWords = ['auditorium', 'gedung', 'gd', 'dr', 'prof', 'ruang', 'aula', 'dan', 'di', 'yang'];

        foreach ($auditoriums as $aud) {
            $fullName = strtolower((string) $aud->name);

            if (str_contains($message, $fullName)) {
                return $aud;
            }

            if (preg_match('/\(([^)]+)\)/', $fullName, $m)) {
                $abbr = trim($m[1]);
                if ($abbr !== '' && preg_match('/\b' . preg_quote($abbr, '/') . '\b/i', $message)) {
                    return $aud;
                }
            }

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
            ['label' => 'Auditorium di Limo', 'message' => 'Auditorium apa saja yang ada di Limo?'],
            ['label' => 'Auditorium di Pondok Labu', 'message' => 'Auditorium apa saja yang ada di Pondok Labu?'],
            ['label' => 'Alur Pengajuan', 'message' => 'Bagaimana alur pengajuan peminjaman auditorium?'],
            ['label' => 'SOP & Syarat', 'message' => 'Apa saja SOP dan syarat peminjaman?'],
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
        $role = Auth::check()?(Auth::user()->role??null):null;

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