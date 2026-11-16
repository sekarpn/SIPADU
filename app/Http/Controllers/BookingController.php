<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Auditorium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;


class BookingController extends Controller
{
    public function index(Request $r)
    {
        $bookings = Booking::with('auditorium')
            ->where('user_id', $r->user()->id)
            ->latest()
            ->paginate(10);

        return view('borrower.bookings', compact('bookings'));
    }

    public function create(Request $r)
    {
        $auditoriums = Auditorium::where('is_active', true)->get();
        $selected = $r->query('auditorium');

        return view('borrower.booking-form', compact('auditoriums', 'selected'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'auditorium_id' => 'required|exists:auditoriums,id',
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'organization' => 'required|string|max:255',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:30',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'surat' => 'required|file|mimes:pdf|max:5120',
        ]);

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
        $data['user_id'] = $r->user()->id;
        $data['status'] = 'Pending';

        Booking::create($data);

        return redirect()->route('borrower.bookings')
            ->with('success', 'Pengajuan berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function cancel(Request $r, Booking $booking)
    {
        abort_unless($booking->user_id === $r->user()->id, 403);

        abort_unless(
            $booking->status === 'Pending',
            422,
            'Hanya pengajuan Pending yang dapat dibatalkan.'
        );

        $booking->update(['status' => 'Dibatalkan']);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    public function adminIndex(Request $request)
    {
        $query = Booking::with(['user', 'auditorium', 'histories'])->latest();

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

        $bookings = $query->paginate(12)->withQueryString();

        $stats = [
            'pending' => Booking::where('status', 'Pending')->count(),
            'approved' => Booking::where('status', 'Disetujui')->count(),
            'rejected' => Booking::where('status', 'Ditolak')->count(),
            'total' => Booking::count(),
        ];

        $auditoriums = Auditorium::orderBy('name')->get();

        return view('admin.bookings', compact('bookings', 'stats', 'auditoriums'));
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

        $filePath = Storage::disk('local')->path($booking->surat_path);

        return Response::download(
            $filePath,
            'Surat-Peminjaman-' . $booking->id . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function decide(Request $r, Booking $booking)
    {
        $data = $r->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'admin_note' => 'nullable|string|max:2000',
            'conflict_rejection_reason' => 'nullable|string|max:2000',
        ]);

        abort_unless($booking->status === 'Pending', 422, 'Pengajuan ini sudah diproses.');

        DB::transaction(function () use ($r, $booking, $data) {
            $target = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($target->status !== 'Pending') {
                abort(422, 'Pengajuan ini sudah diproses oleh admin lain.');
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

                    $conflict->update([
                        'status' => 'Ditolak',
                        'admin_note' => $autoNote,
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

            $target->update([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            BookingHistory::create([
                'booking_id' => $target->id,
                'admin_id' => $r->user()->id,
                'from_status' => $oldStatus,
                'to_status' => $data['status'],
                'note' => $data['admin_note'] ?? null,
            ]);
        });

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
