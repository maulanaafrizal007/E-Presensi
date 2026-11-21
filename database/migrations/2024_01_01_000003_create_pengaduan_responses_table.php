<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengaduan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who responded

            $table->string('tipe'); // response|internal_note|status_change|verification|request_info|resolution
            $table->text('pesan');
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->boolean('visible_to_employee')->default(true); // internal notes hidden from employees

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan_responses');
    }
};
