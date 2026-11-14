<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('users', function(Blueprint $t){ $t->string('nim_np')->nullable()->unique(); $t->string('role')->default('peminjam')->index(); }); } public function down(): void { Schema::table('users', function(Blueprint $t){$t->dropColumn(['nim_np','role']);}); }};
