<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditoriums', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoriums', 'lokasi_kampus')) {
                $table->string('lokasi_kampus')->nullable()->after('name');
            }
            if (!Schema::hasColumn('auditoriums', 'lokasi_gedung')) {
                $table->text('lokasi_gedung')->nullable()->after('lokasi_kampus');
            }
            if (!Schema::hasColumn('auditoriums', 'jam_mulai')) {
                $table->time('jam_mulai')->nullable()->after('lokasi_gedung');
            }
            if (!Schema::hasColumn('auditoriums', 'jam_selesai')) {
                $table->time('jam_selesai')->nullable()->after('jam_mulai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auditoriums', function (Blueprint $table) {
            $columns = array_filter(
                ['lokasi_kampus', 'lokasi_gedung', 'jam_mulai', 'jam_selesai'],
                fn ($col) => Schema::hasColumn('auditoriums', $col)
            );

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};