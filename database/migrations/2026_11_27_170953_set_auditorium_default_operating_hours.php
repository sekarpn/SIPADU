<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('auditoriums') && Schema::hasColumn('auditoriums', 'jam_mulai') && Schema::hasColumn('auditoriums', 'jam_selesai')) {
            DB::table('auditoriums')->update([
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '17:00:00',
            ]);
        }
    }

    public function down(): void
    {
        // Operasional yang telah diseragamkan tidak dipulihkan ke nilai sebelumnya.
    }
};
