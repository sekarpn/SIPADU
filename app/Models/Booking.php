<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'auditorium_id',
        'event_name',
        'event_description',
        'organization',
        'pic_name',
        'pic_phone',
        'booking_date',
        'start_time',
        'end_time',
        'surat_path',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditorium()
    {
        return $this->belongsTo(Auditorium::class);
    }

    public function histories()
    {
        return $this->hasMany(BookingHistory::class);
    }
}
