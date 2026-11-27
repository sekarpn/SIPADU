<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Booking extends Model
{
    public const STATUS_PENDING = 'Pending';
    public const STATUS_CANCELLED = 'Dibatalkan';

    public const EXPIRED_PENDING_NOTE = 'Pengajuan otomatis dibatalkan karena sudah kedaluwarsa (melewati tanggal kegiatan). Silakan cari tanggal lain.';

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
        'applicant_role',
        'applicant_identifier',
        'applicant_faculty',
        'applicant_program',
        'applicant_unit',
        'applicant_position',
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

    public static function cancelExpiredPendingBookings(): int
    {
        $today = Carbon::now('Asia/Jakarta')->startOfDay();

        $expiredBookings = self::query()
            ->where('status', self::STATUS_PENDING)
            ->whereDate('booking_date', '<', $today)
            ->get();

        $updatedCount = 0;

        foreach ($expiredBookings as $booking) {
            $oldStatus = $booking->status;

            $booking->update([
                'status' => self::STATUS_CANCELLED,
                'admin_note' => filled($booking->admin_note)
                    ? trim($booking->admin_note) . "\n\n" . self::EXPIRED_PENDING_NOTE
                    : self::EXPIRED_PENDING_NOTE,
            ]);

            if (class_exists(BookingHistory::class)) {
                BookingHistory::create([
                    'booking_id' => $booking->id,
                    'admin_id' => null,
                    'from_status' => $oldStatus,
                    'to_status' => self::STATUS_CANCELLED,
                    'note' => self::EXPIRED_PENDING_NOTE,
                ]);
            }

            $updatedCount++;
        }

        return $updatedCount;
    }
}