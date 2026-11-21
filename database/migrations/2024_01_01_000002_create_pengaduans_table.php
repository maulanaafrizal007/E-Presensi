<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 20)->unique(); // e.g. ADU-2024-00001
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Employee info snapshot at time of submission
            $table->string('nama_pegawai');
            $table->string('nip', 20);
            $table->string('unit_kerja');

            // Complaint details
            $table->string('kategori'); // terlambat|pulang_cepat|tidak_tercatat|lainnya
            $table->date('tanggal_kejadian');
            $table->string('jenis_presensi'); // masuk|keluar|keduanya
            $table->string('subjek', 255);
            $table->text('deskripsi');

            // Status flow
            $table->string('status')->default('diajukan');
            // diajukan | menunggu_verifikasi | diverifikasi | diproses |
            // menunggu_informasi | diselesaikan | ditutup | ditolak

            $table->string('prioritas')->default('normal'); // rendah|normal|tinggi|urgent

            // Admin handling
            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('handled_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            // Deadline tracking
            $table->date('batas_waktu')->nullable();
            $table->boolean('is_overdue')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
