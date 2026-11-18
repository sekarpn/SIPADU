<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim_nip', 40)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Tidak memaksa kembali NOT NULL karena akun yang baru terdaftar
        // mungkin belum melengkapi NIM/NIP.
    }
};
