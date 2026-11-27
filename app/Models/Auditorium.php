<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    use HasFactory;

    protected $table = 'auditoriums';

    protected $fillable = [
        'name',
        'lokasi_kampus',
        'lokasi_gedung',
        'jam_mulai',
        'jam_selesai',
        'location',
        'capacity',
        'facilities',
        'description',
        'image_path',
        'images',
        'is_active',
        'deactivation_reason',
    ];

    protected $casts = [
        'facilities' => 'array',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    /* Mengambil seluruh path foto auditorium (kompatibel dengan data lama `image_path`) */
    public function getAllImagesAttribute(): array
    {
        $images = $this->images;
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }

        $images = array_values(array_filter(
            is_array($images) ? $images : [],
            fn ($path) => is_string($path) && trim($path) !== ''
        ));

        if (empty($images) && is_string($this->image_path) && trim($this->image_path) !== '') {
            $images[] = trim($this->image_path);
        }

        return $images;
    }

    public function getJamOperasionalAttribute(): string
    {
        if (!$this->jam_mulai || !$this->jam_selesai) {
            return '-';
        }

        $mulai = Carbon::parse($this->jam_mulai)->format('H:i');
        $selesai = Carbon::parse($this->jam_selesai)->format('H:i');

        return "{$mulai} - {$selesai}";
    }

    public function getDeactivationNoteAttribute(): string
    {
        if ($this->is_active) {
            return '';
        }

        return trim((string) ($this->deactivation_reason ?? $this->keterangan_nonaktif ?? ''));
    }
}