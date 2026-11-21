<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained()->onDelete('cascade')->unique();
            $table->foreignId('verified_by')->constrained('users')->onDelete('cascade');

            // Manual verification fields
            $table->date('tanggal_presensi_diklaim');
            $table->string('jam_masuk_diklaim')->nullable();
            $table->string('jam_keluar_diklaim')->nullable();
            $table->string('jam_masuk_sistem')->nullable(); // found in e-presensi manual check
            $table->string('jam_keluar_sistem')->nullable();
            $table->boolean('ada_selisih')->default(false);
            $table->text('detail_selisih')->nullable();
            $table->text('catatan_verifikasi');
            $table->string('hasil_verifikasi'); // valid|tidak_valid|perlu_investigasi
            $table->timestamp('verified_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasis');
    }
};
