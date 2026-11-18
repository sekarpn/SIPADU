<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('faculty')->nullable();
            $table->string('study_program')->nullable();
            $table->string('employee_unit')->nullable();
            $table->string('position')->nullable();
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('applicant_role', 20)->nullable();
            $table->string('applicant_identifier', 40)->nullable();
            $table->string('applicant_faculty')->nullable();
            $table->string('applicant_program')->nullable();
            $table->string('applicant_unit')->nullable();
            $table->string('applicant_position')->nullable();
        });

        DB::table('users')->where('role', 'peminjam')->update(['role' => 'mahasiswa']);
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('mahasiswa')->change();
        });
        DB::table('bookings')->whereNull('applicant_role')->update([
            'applicant_role' => 'mahasiswa',
        ]);
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['applicant_role','applicant_identifier','applicant_faculty','applicant_program','applicant_unit','applicant_position']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['faculty','study_program','employee_unit','position']);
        });
        DB::table('users')->where('role', 'mahasiswa')->update(['role' => 'peminjam']);
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('peminjam')->change();
        });
    }
};
