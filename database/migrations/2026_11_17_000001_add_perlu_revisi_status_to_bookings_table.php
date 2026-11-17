<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE bookings MODIFY status ENUM('Pending', 'Disetujui', 'Ditolak', 'Perlu Revisi', 'Dibatalkan') NOT NULL DEFAULT 'Pending'");
    }

    public function down(): void
    {
        DB::table('bookings')
            ->where('status', 'Perlu Revisi')
            ->update(['status' => 'Pending']);

        DB::statement("ALTER TABLE bookings MODIFY status ENUM('Pending', 'Disetujui', 'Ditolak', 'Dibatalkan') NOT NULL DEFAULT 'Pending'");
    }
};
