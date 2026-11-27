<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Auditorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function index(Request $r)
    {
        $bookings = Booking::with(['auditorium', 'histories'])
            ->where('user_id', $r->user()->id)
            ->latest()
            ->paginate(10);

        $view = $r->user()->role === 'pegawai' ? 'pegawai.bookings' : 'borrower.bookings';
        return view($view, compact('bookings'));
    }

    private function profileIsComplete($user): bool
    {
        return $user->hasCompleteProfile();
    }

    private function redirectToIncompleteProfile($user)
    {
        $route = $user->role === 'pegawai' ? 'pegawai.profile' : 'borrower.profile';

        return redirect()
            ->route($route)
            ->with('profile_incomplete', 'Lengkapi data profil terlebih dahulu sebelum mengajukan peminjaman.');
    }

    public function create(Request $r)
    {
        $user = $r->user();
        $profileComplete = $this->profileIsComplete($user);
        $auditoriums = Auditorium::where('is_active', true)->orderBy('name')->get();
        $selected = $r->query('auditorium') ?? $r->query('auditorium_id');

        if ($selected) {
            $requestedAuditorium = Auditorium::find($selected);
            if ($requestedAuditorium && !$requestedAuditorium->is_active) {
                $reason = trim((string) ($requestedAuditorium->deactivation_reason ?? ''));
                $reasonText = $reason !== '' ? " Keterangan: {$reason}" : '';
                $dashboardRoute = $user->role === 'pegawai' ? 'pegawai.dashboard' : 'borrower.dashboard';

                return redirect()
                    ->route($dashboardRoute)
                    ->with('error', "Auditorium {$requestedAuditorium->name} sedang dinonaktifkan dan tidak dapat diajukan.{$reasonText}");
            }
        }

        $view = $user->role === 'pegawai' ? 'pegawai.booking-form' : 'borrower.booking-form';
        return view($view, compact('auditoriums', 'selected', 'profileComplete'));
    }

    public function store(Request $r)
    {
        if (!$this->profileIsComplete($r->user())) {
            return $this->redirectToIncompleteProfile($r->user());
        }

        $minimumBookingDate = date('Y-m-d', strtotime('+7 days'));

        $data = $r->validate([
            'auditorium_id' => 'required|exists:auditoriums,id',
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'organization' => 'required|string|max:255',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:30',
            'booking_date' => 'required|date|after_or_equal:' . $minimumBookingDate,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'surat' => 'required|file|mimes:pdf|max:5120',
        ]);

        // Pastikan auditorium masih aktif saat pengajuan dikirim
        $auditorium = Auditorium::findOrFail($data['auditorium_id']);
        if (!$auditorium->is_active) {
            $reason = trim((string) ($auditorium->deactivation_reason ?? ''));
            $reasonText = $reason !== '' ? " ({$reason})" : '';

            return back()->withInput()->withErrors([
                'auditorium_id' => "Auditorium {$auditorium->name} sedang dinonaktifkan dan tidak dapat dipinjam{$reasonText}.",
            ]);
        }

        // Cek jadwal bentrok
        $conflict = Booking::where('auditorium_id', $data['auditorium_id'])
            ->whereDate('booking_date', $data['booking_date'])
            ->where('status', 'Disetujui')
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors([
                'start_time' => 'Jadwal bertabrakan dengan peminjaman yang sudah disetujui. Silakan pilih sesi lain.'
            ]);
        }

        // Simpan surat PDF
        $suratPath = $r->file('surat')->store('surat-peminjaman', 'local');

        // Siapkan data pengajuan
        unset($data['surat']);

        $data['surat_path'] = $suratPath;
        $user = $r->user();
        $data['user_id'] = $user->id;
        $data['applicant_role'] = $user->role;
        $data['applicant_identifier'] = $user->nim_nip;
        $data['applicant_faculty'] = $user->faculty;
        $data['applicant_program'] = $user->study_program;
        $data['applicant_unit'] = $user->employee_unit;
        $data['applicant_position'] = $user->position;
        $data['status'] = 'Pending';

        Booking::create($data);

        return redirect()->route($user->role === 'pegawai' ? 'pegawai.bookings' : 'borrower.bookings')
            ->with('success', 'Pengajuan berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function cancel(Request $r, Booking $booking)
    {
        abort_unless($booking->user_id === $r->user()->id, 403);

        if ($booking->status !== 'Pending') {
            return back()->with('warning', 'Pengajuan ini sudah berstatus ' . $booking->status . ' dan tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'Dibatalkan']);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    public function adminIndex(Request $request)
    {
        $query = Booking::with(['user', 'auditorium', 'histories'])->latest();

        if ($request->filled('booking_id')) {
            $query->where('id', $request->booking_id);
        }

        if ($request->filled('q')) {
            $term = trim($request->q);

            $query->where(function ($q) use ($term) {
                $q->where('event_name', 'like', "%{$term}%")
                    ->orWhere('organization', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('auditorium')) {
            $query->where('auditorium_id', $request->auditorium);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $perPage = in_array((int) $request->input('per_page'), [10, 25, 50, 100], true)
            ? (int) $request->input('per_page')
            : 10;

        $bookings = $query->paginate($perPage)->withQueryString();

        $pageItems = $bookings->getCollection();
        $extraDrawerBookings = collect();

        foreach ($pageItems as $item) {
            if ($item->status === 'Pending') {
                $conflicts = Booking::with(['user', 'auditorium', 'histories'])
                    ->where('id', '!=', $item->id)
                    ->where('auditorium_id', $item->auditorium_id)
                    ->whereDate('booking_date', $item->booking_date)
                    ->whereIn('status', ['Pending', 'Disetujui'])
                    ->where('start_time', '<', $item->end_time)
                    ->where('end_time', '>', $item->start_time)
                    ->orderByRaw("CASE WHEN status = 'Disetujui' THEN 0 ELSE 1 END")
                    ->orderBy('created_at')
                    ->get();

                $item->setRelation('conflicts', $conflicts);

                foreach ($conflicts as $conflict) {
                    if (
                        !$pageItems->contains('id', $conflict->id) &&
                        !$extraDrawerBookings->contains('id', $conflict->id)
                    ) {
                        $subConflicts = Booking::with(['user', 'auditorium', 'histories'])
                            ->where('id', '!=', $conflict->id)
                            ->where('auditorium_id', $conflict->auditorium_id)
                            ->whereDate('booking_date', $conflict->booking_date)
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->where('start_time', '<', $conflict->end_time)
                            ->where('end_time', '>', $conflict->start_time)
                            ->orderByRaw("CASE WHEN status = 'Disetujui' THEN 0 ELSE 1 END")
                            ->orderBy('created_at')
                            ->get();

                        $conflict->setRelation('conflicts', $subConflicts);
                        $extraDrawerBookings->push($conflict);
                    }
                }
            } else {
                $item->setRelation('conflicts', collect());
            }
        }

        $stats = [
            'pending' => Booking::where('status', 'Pending')->count(),
            'approved' => Booking::where('status', 'Disetujui')->count(),
            'rejected' => Booking::where('status', 'Ditolak')->count(),
            'revision' => Booking::where('status', 'Perlu Revisi')->count(),
            'cancelled' => Booking::where('status', 'Dibatalkan')->count(),
            'total' => Booking::count(),
        ];

        $auditoriums = Auditorium::orderBy('name')->get();

        return view('admin.bookings', compact('bookings', 'stats', 'auditoriums', 'extraDrawerBookings'));
    }

    // Export data peminjaman ke CSV/Excel oleh admin
    public function adminExport(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $query = Booking::with(['user', 'auditorium'])->latest();

        if ($request->filled('booking_id')) {
            $query->where('id', $request->booking_id);
        }

        if ($request->filled('q')) {
            $term = trim($request->q);

            $query->where(function ($q) use ($term) {
                $q->where('event_name', 'like', "%{$term}%")
                    ->orWhere('organization', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('auditorium')) {
            $query->where('auditorium_id', $request->auditorium);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->get();
        $filename = 'Data-Peminjaman-Auditorium-' . now()->format('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM agar kolom otomatis rapi saat dibuka langsung di Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'Nama Pemohon',
                'NIM / NIP',
                'Role',
                'Fakultas / Unit',
                'Prodi / Jabatan',
                'Organisasi / Instansi',
                'Auditorium',
                'Nama Kegiatan',
                'Deskripsi Kegiatan',
                'Penanggung Jawab (PIC)',
                'No. HP PIC',
                'Tanggal Peminjaman',
                'Waktu Mulai',
                'Waktu Selesai',
                'Status',
                'Catatan Admin',
                'Tanggal Pengajuan',
            ], ';');

            foreach ($bookings as $index => $item) {
                $roleLabel = match ($item->applicant_role ?? $item->user->role ?? '') {
                    'mahasiswa' => 'Mahasiswa',
                    'pegawai' => 'Dosen & Tendik',
                    'admin' => 'Administrator',
                    default => '-',
                };

                $fakultasUnit = $item->applicant_faculty ?: ($item->applicant_unit ?: '-');
                $prodiJabatan = $item->applicant_program ?: ($item->applicant_position ?: '-');

                $bookingDate = $item->booking_date
                    ? \Carbon\Carbon::parse($item->booking_date)->format('d/m/Y')
                    : '-';

                $createdAt = $item->created_at
                    ? $item->created_at->format('d/m/Y H:i')
                    : '-';

                fputcsv($file, [
                    $index + 1,
                    $item->user->name ?? '-',
                    $item->applicant_identifier ?? $item->user->nim_nip ?? '-',
                    $roleLabel,
                    $fakultasUnit,
                    $prodiJabatan,
                    $item->organization ?? '-',
                    $item->auditorium->name ?? '-',
                    $item->event_name ?? '-',
                    $item->event_description ?? '-',
                    $item->pic_name ?? '-',
                    $item->pic_phone ?? '-',
                    $bookingDate,
                    substr((string) $item->start_time, 0, 5),
                    substr((string) $item->end_time, 0, 5),
                    $item->status ?? '-',
                    $item->admin_note ?? '-',
                    $createdAt,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Download surat peminjaman oleh admin
    public function downloadLetter(Booking $booking)
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'admin',
            403
        );

        abort_unless(
            $booking->surat_path &&
            Storage::disk('local')->exists($booking->surat_path),
            404,
            'File surat peminjaman tidak ditemukan.'
        );

        // Kirim file sebagai response PDF inline agar dapat ditampilkan di iframe
        // pada halaman Kelola Peminjaman, bukan langsung diunduh.
        $disk = Storage::disk('local');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        return $disk->response(
            $booking->surat_path,
            'Surat-Peminjaman-' . $booking->id . '.pdf',
            [
                'Content-Type' => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ],
            'inline'
        );
    }

    public function decide(Request $r, Booking $booking)
    {
        $data = $r->validate([
            'status' => 'required|in:Disetujui,Ditolak,Perlu Revisi',
            'admin_note' => 'nullable|string|max:2000',
            'conflict_rejection_reason' => 'nullable|string|max:2000',
        ]);

        if ($data['status'] === 'Perlu Revisi' && blank($data['admin_note'] ?? null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'admin_note' => 'Catatan revisi wajib diisi agar peminjam mengetahui bagian yang harus diperbaiki.'
            ]);
        }

        if ($booking->status !== 'Pending') {
            return back()->with('warning', 'Pengajuan ini sudah diproses sebelumnya dengan status: ' . $booking->status . '.');
        }

        $alreadyProcessedStatus = null;

        DB::transaction(function () use ($r, $booking, $data, &$alreadyProcessedStatus) {
            $target = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->status !== 'Pending') {
                $alreadyProcessedStatus = $target->status;
                return;
            }

            $overlapQuery = Booking::where('id', '!=', $target->id)
                ->where('auditorium_id', $target->auditorium_id)
                ->whereDate('booking_date', $target->booking_date)
                ->where('start_time', '<', $target->end_time)
                ->where('end_time', '>', $target->start_time);

            if ($data['status'] === 'Disetujui') {
                $approvedConflict = (clone $overlapQuery)
                    ->where('status', 'Disetujui')
                    ->lockForUpdate()
                    ->exists();

                if ($approvedConflict) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'conflict' => 'Jadwal tidak dapat disetujui karena slot auditorium sudah dikunci oleh peminjaman lain.'
                    ]);
                }

                $pendingConflicts = (clone $overlapQuery)
                    ->where('status', 'Pending')
                    ->lockForUpdate()
                    ->get();

                if (
                    $pendingConflicts->isNotEmpty() &&
                    blank($data['conflict_rejection_reason'] ?? null)
                ) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'conflict_rejection_reason' => 'Alasan penolakan otomatis wajib diisi karena terdapat pengajuan lain yang bentrok.'
                    ]);
                }

                foreach ($pendingConflicts as $conflict) {
                    $oldStatus = $conflict->status;
                    $autoNote = '[Ditolak otomatis karena bentrok] ' . $data['conflict_rejection_reason'];

                    // Pertahankan catatan lama (misal catatan perbaikan) jika sebelumnya pernah ada
                    $combinedNote = filled($conflict->admin_note)
                        ? trim($conflict->admin_note) . "\n\n" . $autoNote
                        : $autoNote;

                    $conflict->update([
                        'status' => 'Ditolak',
                        'admin_note' => $combinedNote,
                    ]);

                    BookingHistory::create([
                        'booking_id' => $conflict->id,
                        'admin_id' => $r->user()->id,
                        'from_status' => $oldStatus,
                        'to_status' => 'Ditolak',
                        'note' => $autoNote,
                    ]);
                }
            }

            $oldStatus = $target->status;
            $newNote = $data['admin_note'] ?? null;

            // Jika menolak/memutuskan setelah sebelumnya ada catatan (mis. catatan bentrok), gabungkan agar keduanya terbaca
            if (filled($newNote) && filled($target->admin_note) && trim($target->admin_note) !== trim($newNote)) {
                $finalNote = trim($target->admin_note) . "\n\n" . trim($newNote);
            } else {
                $finalNote = filled($newNote) ? trim($newNote) : $target->admin_note;
            }

            $target->update([
                'status' => $data['status'],
                'admin_note' => $finalNote,
            ]);

            BookingHistory::create([
                'booking_id' => $target->id,
                'admin_id' => $r->user()->id,
                'from_status' => $oldStatus,
                'to_status' => $data['status'],
                'note' => $newNote,
            ]);
        });

        if ($alreadyProcessedStatus !== null) {
            return back()->with('warning', 'Pengajuan ini sudah diproses sebelumnya dengan status: ' . $alreadyProcessedStatus . '.');
        }

        return back()->with('success', 'Keputusan pengajuan berhasil disimpan.');
    }

    // Download template surat peminjaman dalam format PDF
    public function downloadTemplateSurat(Request $request)
    {
        $user = $request->user();

        $data = [
            'tanggal_surat' => now()->translatedFormat('d F Y'),
            'nama_pemohon' => $user->name ?? '........................................',
            'nomor_induk' => $user->nim ?? $user->nip ?? $user->nomor_identitas ?? '........................................',
            'no_telepon' => $user->phone ?? $user->no_telepon ?? '........................................',
        ];

        $pdf = Pdf::loadView('pdf.template-surat-peminjaman', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Template_Surat_Permohonan_Auditorium_UPNVJ.pdf');
    }
}