<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {Schema::create('booking_histories',function(Blueprint $t){$t->id();$t->foreignId('booking_id')->constrained()->cascadeOnDelete();$t->foreignId('admin_id')->constrained('users')->cascadeOnDelete();$t->string('from_status')->nullable();$t->string('to_status');$t->text('note')->nullable();$t->timestamps();});} public function down(): void {Schema::dropIfExists('booking_histories');}};
