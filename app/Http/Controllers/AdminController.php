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
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'facilities' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['facilities'] = array_filter(
            array_map('trim', explode(',', $data['facilities'] ?? ''))
        );

        $data['is_active'] = $r->has('is_active')
            ? $r->boolean('is_active')
            : ($auditorium?->is_active ?? true);

        // Simpan foto auditorium jika admin mengunggah foto baru
        if ($r->hasFile('image')) {

            // Hapus foto lama jika sedang mengedit auditorium
            if ($auditorium && $auditorium->image_path) {
                Storage::disk('public')->delete($auditorium->image_path);
            }

            // Simpan foto baru ke storage/app/public/auditoriums
            $data['image_path'] = $r->file('image')->store('auditoriums', 'public');
        }

        unset($data['image']);

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
        if ($auditorium->image_path) {
            Storage::disk('public')->delete($auditorium->image_path);
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
            'email' => ['required','email','max:255',Rule::unique('users','email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'nim_nip' => ['nullable','string','max:50',Rule::unique('users','nim_nip')->ignore($user->id)],
            'role' => 'required|in:mahasiswa,pegawai,admin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        if (empty($data['password'])) unset($data['password']);
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
