<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nim_nip',
        'phone',
        'profile_photo_path',
        'password',
        'role',
        'faculty',
        'study_program',
        'employee_unit',
        'position',
        'email_notifications',
        'system_notifications',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'email_notifications' => 'boolean',
            'system_notifications' => 'boolean',
        ];
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo_path
            ? asset('storage/' . $this->profile_photo_path)
            : null;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function hasCompleteProfile(): bool
    {
        $role = strtolower((string) ($this->role ?? 'mahasiswa'));

        if (trim((string) $this->name) === '' || trim((string) $this->email) === '') {
            return false;
        }

        if ($role === 'admin') {
            return true;
        }

        // Mahasiswa & Pegawai wajib mengisi nomor telepon dan NIM/NIP
        if (trim((string) $this->phone) === '' || trim((string) $this->nim_nip) === '') {
            return false;
        }

        if ($role === 'pegawai') {
            return trim((string) $this->employee_unit) !== ''
                && trim((string) $this->position) !== '';
        }

        // Mahasiswa
        return trim((string) $this->faculty) !== ''
            && trim((string) $this->study_program) !== '';
    }
}