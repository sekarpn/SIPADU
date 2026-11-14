<?php

namespace App\Http\Controllers;

use App\Models\Auditorium;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $data['is_active'] = $r->boolean('is_active');

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
        $users = User::latest()->paginate(20);

        return view('admin.users', compact('users'));
    }
}
