<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Auditorium;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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

    public function adminIndex()
    {
        $bookings = Booking::with(['user', 'auditorium'])
            ->latest()
            ->paginate(15);

        return view('admin.bookings', compact('bookings'));
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
            'admin_note' => 'nullable|string',
        ]);

        abort_unless($booking->status === 'Pending', 422);

        if ($data['status'] === 'Disetujui') {
            $conflict = Booking::where('id', '!=', $booking->id)
                ->where('auditorium_id', $booking->auditorium_id)
                ->whereDate('booking_date', $booking->booking_date)
                ->where('status', 'Disetujui')
                ->where('start_time', '<', $booking->end_time)
                ->where('end_time', '>', $booking->start_time)
                ->exists();

            if ($conflict) {
                return back()->withErrors([
                    'conflict' => 'Jadwal telah disetujui untuk peminjaman lain.'
                ]);
            }
        }

        DB::transaction(function () use ($r, $booking, $data) {
            $old = $booking->status;

            $booking->update([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? null,
            ]);

            BookingHistory::create([
                'booking_id' => $booking->id,
                'admin_id' => $r->user()->id,
                'from_status' => $old,
                'to_status' => $data['status'],
                'note' => $data['admin_note'] ?? null,
            ]);
        });

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');

        public function downloadTemplateSurat(Request $request)
    {
        $user = $request->user();

        $data = [
            'tanggal_surat' => now()->translatedFormat('d F Y'),
            'nama_pemohon'  => $user->name ?? '........................................',
            'nomor_induk'   => $user->nim ?? $user->nip ?? $user->nomor_identitas ?? '........................................',
            'no_telepon'    => $user->phone ?? $user->no_telepon ?? '........................................',
        ];

        $pdf = Pdf::loadView('pdf.template-surat-peminjaman', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('Template_Surat_Permohonan_Auditorium_UPNVJ.pdf');
    }
}
