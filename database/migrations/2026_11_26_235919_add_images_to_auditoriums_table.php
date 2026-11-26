<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditoriums', function (Blueprint $table) {
            if (!Schema::hasColumn('auditoriums', 'images')) {
                $table->json('images')->nullable()->after('image_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auditoriums', function (Blueprint $table) {
            if (Schema::hasColumn('auditoriums', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};