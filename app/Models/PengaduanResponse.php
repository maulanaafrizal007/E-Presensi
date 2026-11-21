<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanResponse extends Model
{
    protected $fillable = [
        'pengaduan_id',
        'user_id',
        'tipe',
        'pesan',
        'status_before',
        'status_after',
        'visible_to_employee',
    ];

    protected function casts(): array
    {
        return [
            'visible_to_employee' => 'boolean',
        ];
    }

    public static function tipeLabels(): array
    {
        return [
            'response' => 'Tanggapan',
            'internal_note' => 'Catatan Internal',
            'status_change' => 'Perubahan Status',
            'verification' => 'Hasil Verifikasi',
            'request_info' => 'Permintaan Informasi',
            'resolution' => 'Penyelesaian',
        ];
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
