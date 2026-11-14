<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Auditorium;
use App\Models\SiteContent;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function borrower(Request $r)
    {
        $q = Booking::where('user_id', $r->user()->id);

        $stats = [
            'total' => (clone $q)->count(),
            'pending' => (clone $q)->where('status', 'Pending')->count(),
            'approved' => (clone $q)->where('status', 'Disetujui')->count(),
            'rejected' => (clone $q)->where('status', 'Ditolak')->count(),
        ];

        $bookings = (clone $q)->with('auditorium')->latest()->take(6)->get();

        $auditoriums = Auditorium::where('is_active', true)->get();

        return view('borrower.dashboard', compact(
            'stats',
            'bookings',
            'auditoriums'
        ));
    }

    public function admin()
    {
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'Pending')->count(),
            'approved' => Booking::where('status', 'Disetujui')->count(),
            'rejected' => Booking::where('status', 'Ditolak')->count(),
            'cancelled' => Booking::where('status', 'Dibatalkan')->count(),
        ];

        $bookings = Booking::with(['user', 'auditorium'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'bookings'));
    }

    public function profile()
    {
        return view('partials.profile');
    }

    public function updateProfile(Request $r)
    {
        $user = $r->user();

        $data = $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
        ]);

        $user->update($data);

        return back()->with('success', 'Informasi profil berhasil diperbarui.');
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

    public function guides()
    {
        $contents = SiteContent::orderBy('sort_order')->get();

        return view('borrower.guides', compact('contents'));
    }
}