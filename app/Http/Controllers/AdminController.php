<?php

namespace App\Http\Controllers;

use App\Models\Auditorium;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function contents()
    {
        $contents = SiteContent::orderBy('sort_order')->get();

        return view('admin.contents', compact('contents'));
    }

    public function saveContent(Request $r)
    {
        $data = $r->validate([
            'key' => 'required|in:alur,sop,faq,contact',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        SiteContent::updateOrCreate(
            ['key' => $data['key']],
            $data
        );

        return back()->with('success', 'Konten informasi berhasil disimpan.');
    }

    public function deleteContent(SiteContent $content)
    {
        $content->delete();

        return back()->with('success', 'Konten dihapus.');
    }

    public function auditoriums()
    {
        $auditoriums = Auditorium::latest()->paginate(15);

        return view('admin.auditoriums', compact('auditoriums'));
    }

    public function saveAuditorium(Request $r, ?Auditorium $auditorium = null)
    {
        // Normalisasi format waktu ke H:i (antisipasi nilai TIME H:i:s dari database saat edit)
        if ($r->filled('jam_mulai')) {
            $r->merge(['jam_mulai' => substr((string) $r->jam_mulai, 0, 5)]);
        }
        if ($r->filled('jam_selesai')) {
            $r->merge(['jam_selesai' => substr((string) $r->jam_selesai, 0, 5)]);
        }

        $data = $r->validate([
            'name' => 'required|string|max:255',
            'lokasi_kampus' => 'required|in:UPNVJ Kampus Pondok Labu,UPNVJ Kampus Limo',
            'lokasi_gedung' => 'nullable|string|max:500',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
        ], [
            'lokasi_kampus.required' => 'Lokasi kampus wajib dipilih.',
            'lokasi_kampus.in' => 'Lokasi kampus harus UPNVJ Kampus Pondok Labu atau UPNVJ Kampus Limo.',
            'jam_mulai.required' => 'Jam mulai operasional wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',
            'jam_selesai.required' => 'Jam selesai operasional wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',
            'jam_selesai.after' => 'Jam selesai harus lebih akhir dari jam mulai.',
            'images.*.image' => 'Semua file yang diunggah harus berupa gambar.',
            'images.*.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'images.*.max' => 'Ukuran setiap foto maksimal 2 MB.',
        ]);

        // Fallback agar halaman lama yang masih membaca kolom `location` tetap terisi
        if (empty($data['location'])) {
            $data['location'] = trim(
                $data['lokasi_kampus'] . (!empty($data['lokasi_gedung']) ? ' - ' . $data['lokasi_gedung'] : '')
            );
        }

        $data['facilities'] = array_values(array_filter(
            array_map('trim', explode(',', $data['facilities'] ?? ''))
        ));

        $data['is_active'] = $r->has('is_active')
            ? $r->boolean('is_active')
            : ($auditorium?->is_active ?? true);

        // Ambil daftar foto lama jika sedang mengedit auditorium
        $currentImages = [];
        if ($auditorium) {
            $rawImages = $auditorium->images;
            if (is_string($rawImages)) {
                $decoded = json_decode($rawImages, true);
                $rawImages = is_array($decoded) ? $decoded : [];
            }

            $currentImages = array_values(array_filter(
                is_array($rawImages) ? $rawImages : [],
                fn ($p) => is_string($p) && trim($p) !== ''
            ));

            if (empty($currentImages) && is_string($auditorium->image_path) && trim($auditorium->image_path) !== '') {
                $currentImages[] = trim($auditorium->image_path);
            }
        }

        // Hapus foto lama yang ditandai hapus oleh admin saat edit
        $removeImages = $r->input('remove_images', []);
        if (!empty($removeImages) && is_array($removeImages)) {
            foreach ($removeImages as $removedPath) {
                if (in_array($removedPath, $currentImages, true)) {
                    Storage::disk('public')->delete($removedPath);
                }
            }
            $currentImages = array_values(array_diff($currentImages, $removeImages));
        }

        // Simpan foto-foto baru ke storage/app/public/auditoriums
        if ($r->hasFile('images')) {
            foreach ($r->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $currentImages[] = $file->store('auditoriums', 'public');
                }
            }
        }

        $data['images'] = array_values($currentImages);
        $data['image_path'] = $currentImages[0] ?? null;

        unset($data['remove_images']);

        Auditorium::updateOrCreate(
            ['id' => $auditorium?->id],
            $data
        );

        return back()->with('success', 'Data auditorium berhasil disimpan.');
    }

    public function toggleAuditoriumStatus(Auditorium $auditorium)
    {
        $auditorium->update([
            'is_active' => !$auditorium->is_active,
        ]);

        return back()->with('success', 'Status auditorium berhasil diperbarui.');
    }

    public function deleteAuditorium(Auditorium $auditorium)
    {
        $rawImages = $auditorium->images;
        if (is_string($rawImages)) {
            $decoded = json_decode($rawImages, true);
            $rawImages = is_array($decoded) ? $decoded : [];
        }

        $paths = array_values(array_filter(
            is_array($rawImages) ? $rawImages : [],
            fn ($p) => is_string($p) && trim($p) !== ''
        ));

        if (is_string($auditorium->image_path) && trim($auditorium->image_path) !== '') {
            $paths[] = trim($auditorium->image_path);
        }

        foreach (array_unique($paths) as $path) {
            Storage::disk('public')->delete($path);
        }

        $auditorium->delete();

        return back()->with('success', 'Auditorium dihapus.');
    }

    public function users()
    {
        // Seluruh data dimuat satu kali agar pencarian, filter role, dan slider
        // pagination bekerja pada dataset yang sama tanpa pagination ganda.
        $users = User::latest()->get();
        $roleCounts = [
            'all' => $users->count(),
            'mahasiswa' => $users->where('role', 'mahasiswa')->count(),
            'pegawai' => $users->where('role', 'pegawai')->count(),
            'admin' => $users->where('role', 'admin')->count(),
        ];

        return view('admin.users', compact('users', 'roleCounts'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'nim_nip' => 'nullable|string|max:50|unique:users,nim_nip',
            'role' => 'required|in:mahasiswa,pegawai,admin',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create($data);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function showUser(User $user)
    {
        $user->loadCount('bookings');

        return view('admin.users-show', compact('user'));
    }

    public function editUser(User $user)
    {
        return view('admin.users-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'nim_nip' => ['nullable', 'string|max:50', Rule::unique('users', 'nim_nip')->ignore($user->id)],
            'role' => 'required|in:mahasiswa,pegawai,admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function deleteUser(User $user)
    {
        if ((string) $user->getAuthIdentifier() === (string) \Illuminate\Support\Facades\Auth::id()) {
            return back()->with('error', 'Akun admin yang sedang digunakan tidak dapat dihapus.');
        }

        if ($user->bookings()->exists()) {
            return back()->with('error', 'Pengguna tidak dapat dihapus karena masih memiliki riwayat peminjaman.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus.');
    }
}