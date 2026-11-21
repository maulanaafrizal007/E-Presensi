<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $fillable = [
        'pengaduan_id',
        'verified_by',
        'tanggal_presensi_diklaim',
        'jam_masuk_diklaim',
        'jam_keluar_diklaim',
        'jam_masuk_sistem',
        'jam_keluar_sistem',
        'ada_selisih',
        'detail_selisih',
        'catatan_verifikasi',
        'hasil_verifikasi',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_presensi_diklaim' => 'date',
            'ada_selisih' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public static function hasilLabels(): array
    {
        return [
            'valid' => 'Valid - Ditemukan Selisih',
            'tidak_valid' => 'Tidak Valid - Data Sesuai',
            'perlu_investigasi' => 'Perlu Investigasi Lebih Lanjut',
        ];
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
