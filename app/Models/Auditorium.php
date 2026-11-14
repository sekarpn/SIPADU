<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditorium extends Model
{
    // Menentukan nama tabel yang benar di database
    protected $table = 'auditoriums';

    protected $fillable = [
        'name',
        'location',
        'capacity',
        'description',
        'facilities',
        'is_active',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
