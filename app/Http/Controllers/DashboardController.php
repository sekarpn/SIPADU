<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Auditorium;
use App\Models\SiteContent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function borrower(Request $r)
    {
        $q = Booking::where('user_id', $r->user()->id);

        $stats = [
            'total'    => (clone $q)->count(),
            'pending'  => (clone $q)->where('status', 'Pending')->count(),
            'approved' => (clone $q)->where('status', 'Disetujui')->count(),
            'rejected' => (clone $q)->where('status', 'Ditolak')->count(),
        ];

        $bookings = (clone $q)->with('auditorium')->latest()->take(6)->get();

        // 1. DATA GRAFIK PEMINJAMAN PER TAHUN & BULAN (DISETUJUI & DITOLAK)
        $now = Carbon::now('Asia/Jakarta');
        $defaultChartYear = (int) $now->year;
        $bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $rawStats = Booking::select(
                DB::raw('YEAR(booking_date) as tahun'),
                DB::raw('MONTH(booking_date) as bulan'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('booking_date')
            ->whereIn('status', ['Disetujui', 'Ditolak'])
            ->groupBy('tahun', 'bulan', 'status')
            ->get();

        $yearsFromDb = $rawStats->pluck('tahun')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->unique()
            ->values()
            ->all();

        $availableYears = collect(array_merge($yearsFromDb, [$defaultChartYear]))
            ->unique()
            ->sort()
            ->values()
            ->all();

        $chartByYear = [];
        foreach ($availableYears as $yr) {
            $chartByYear[(string) $yr] = [
                'approved' => array_fill(0, 12, 0),
                'rejected' => array_fill(0, 12, 0),
            ];
        }

        foreach ($rawStats as $row) {
            $yrKey = (string) ((int) $row->tahun);
            $monthIdx = ((int) $row->bulan) - 1;
            if (isset($chartByYear[$yrKey]) && $monthIdx >= 0 && $monthIdx < 12) {
                if ($row->status === 'Disetujui') {
                    $chartByYear[$yrKey]['approved'][$monthIdx] = (int) $row->total;
                } elseif ($row->status === 'Ditolak') {
                    $chartByYear[$yrKey]['rejected'][$monthIdx] = (int) $row->total;
                }
            }
        }

        // 2. DATA PEMINJAMAN MENDATANG (YANG SUDAH DISETUJUI & BELUM SELESAI)
        $todayStr = $now->toDateString();
        $currentTimeStr = $now->format('H:i:s');

        $upcomingBookings = Booking::with(['auditorium', 'user'])
            ->where('status', 'Disetujui')
            ->where(function ($query) use ($todayStr, $currentTimeStr) {
                $query->whereDate('booking_date', '>', $todayStr)
                    ->orWhere(function ($sub) use ($todayStr, $currentTimeStr) {
                        $sub->whereDate('booking_date', '=', $todayStr)
                            ->where('end_time', '>=', $currentTimeStr);
                    });
            })
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get()
            ->map(function ($booking) use ($now) {
                $tglMulai = Carbon::parse($booking->booking_date, 'Asia/Jakarta');
                $startDateTime = Carbon::parse($tglMulai->toDateString() . ' ' . $booking->start_time, 'Asia/Jakarta');
                $endDateTime   = Carbon::parse($tglMulai->toDateString() . ' ' . $booking->end_time, 'Asia/Jakarta');

                if ($now->between($startDateTime, $endDateTime)) {
                    $booking->countdown_label = 'Berlangsung';
                    $booking->countdown_type  = 'ongoing';
                } elseif ($tglMulai->isToday()) {
                    $booking->countdown_label = 'Hari ini';
                    $booking->countdown_type  = 'upcoming';
                } else {
                    $selisihHari = (int) $now->copy()->startOfDay()->diffInDays($tglMulai->copy()->startOfDay());
                    $booking->countdown_label = 'Dalam ' . $selisihHari . ' hari';
                    $booking->countdown_type  = 'upcoming';
                }

                $booking->display_date       = $tglMulai;
                $booking->display_start_time = Carbon::parse($booking->start_time)->format('H.i');
                $booking->display_end_time   = Carbon::parse($booking->end_time)->format('H.i');
                $booking->display_organizer  = $booking->organization
                    ?: ($booking->user->employee_unit ?? $booking->user->faculty ?? $booking->user->name ?? '-');

                return $booking;
            });

        $auditoriums = Auditorium::orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $view = $r->user()->role === 'pegawai' ? 'pegawai.dashboard' : 'borrower.dashboard';

        return view($view, compact(
            'stats',
            'bookings',
            'auditoriums',
            'bulanLabels',
            'chartByYear',
            'availableYears',
            'defaultChartYear',
            'upcomingBookings'
        ));
    }

    public function admin()
    {
        $stats = [
            'total'     => Booking::count(),
            'pending'   => Booking::where('status', 'Pending')->count(),
            'approved'  => Booking::where('status', 'Disetujui')->count(),
            'rejected'  => Booking::where('status', 'Ditolak')->count(),
            'cancelled' => Booking::where('status', 'Dibatalkan')->count(),
        ];

        $bookings = Booking::with(['user', 'auditorium'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'bookings'));
    }

    public function profile(Request $r)
    {
        $view = $r->user()->role === 'pegawai' ? 'pegawai.profile' : 'partials.profile';
        return view($view);
    }

    public function updateProfile(Request $r)
    {
        $user = $r->user();

        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'nim_nip' => $user->role === 'admin' ? 'nullable|string|max:40|unique:users,nim_nip,' . $user->id : 'required|string|max:40|unique:users,nim_nip,' . $user->id,
            'phone' => $user->role === 'admin' ? 'nullable|string|max:20' : 'required|string|max:20',
            'faculty' => $user->role === 'mahasiswa' ? 'required|string|max:150' : 'nullable|string|max:150',
            'study_program' => $user->role === 'mahasiswa' ? 'required|string|max:150' : 'nullable|string|max:150',
            'employee_unit' => $user->role === 'pegawai' ? 'required|string|max:150' : 'nullable|string|max:150',
            'position' => $user->role === 'pegawai' ? 'required|string|max:150' : 'nullable|string|max:150',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'nim_nip.required' => 'NIM atau NIP wajib diisi.',
            'nim_nip.unique' => 'NIM atau NIP sudah digunakan akun lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'profile_photo.image' => 'File foto profil harus berupa gambar.',
            'profile_photo.mimes' => 'Format foto profil harus JPG, JPEG, PNG, atau WEBP.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        if ($r->boolean('remove_profile_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $data['profile_photo_path'] = null;
        }

        if ($r->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $r->file('profile_photo')->store('profile-photos', 'public');
        }

        unset($data['profile_photo'], $data['remove_profile_photo']);

        $user->update($data);

        return back()->with('success', 'Informasi dan foto profil berhasil diperbarui.');
    }

    public function updateNotificationPreferences(Request $r)
    {
        $data = $r->validate([
            'email_notifications' => 'required|boolean',
            'system_notifications' => 'required|boolean',
        ]);

        $r->user()->update([
            'email_notifications' => (bool) $data['email_notifications'],
            'system_notifications' => (bool) $data['system_notifications'],
        ]);

        return back()->with('preferences_success', 'Pengaturan notifikasi berhasil diperbarui.');
    }

    public function updatePassword(Request $r)
    {
        $data = $r->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $r->user()->update([
            'password' => $data['password'],
        ]);

        return back()->with('password_success', 'Password berhasil diperbarui.');
    }

    public function guides(Request $r)
    {
        $contents = SiteContent::orderBy('sort_order')->get();
        $view = $r->user()->role === 'pegawai' ? 'pegawai.guides' : 'borrower.guides';
        return view($view, compact('contents'));
    }
}