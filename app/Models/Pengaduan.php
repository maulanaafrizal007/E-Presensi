<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_tiket',
        'user_id',
        'nama_pegawai',
        'nip',
        'unit_kerja',
        'kategori',
        'tanggal_kejadian',
        'jenis_presensi',
        'subjek',
        'deskripsi',
        'status',
        'prioritas',
        'handled_by',
        'handled_at',
        'resolved_at',
        'closed_at',
        'batas_waktu',
        'is_overdue',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'batas_waktu' => 'date',
            'handled_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'is_overdue' => 'boolean',
        ];
    }

    // Status constants
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    const STATUS_DIVERIFIKASI = 'diverifikasi';
    const STATUS_DIPROSES = 'diproses';
    const STATUS_MENUNGGU_INFORMASI = 'menunggu_informasi';
    const STATUS_DISELESAIKAN = 'diselesaikan';
    const STATUS_DITUTUP = 'ditutup';
    const STATUS_DITOLAK = 'ditolak';

    // Status labels in Bahasa Indonesia
    public static function statusLabels(): array
    {
        return [
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::STATUS_DIVERIFIKASI => 'Telah Diverifikasi',
            self::STATUS_DIPROSES => 'Sedang Diproses',
            self::STATUS_MENUNGGU_INFORMASI => 'Menunggu Informasi Tambahan',
            self::STATUS_DISELESAIKAN => 'Diselesaikan',
            self::STATUS_DITUTUP => 'Ditutup',
            self::STATUS_DITOLAK => 'Ditolak',
        ];
    }

    public static function kategoriLabels(): array
    {
        return [
            'terlambat' => 'Keterlambatan',
            'pulang_cepat' => 'Pulang Lebih Awal',
            'tidak_tercatat' => 'Presensi Tidak Tercatat',
            'lupa_absen' => 'Lupa Absen',
            'gangguan_sistem' => 'Gangguan Sistem',
            'lainnya' => 'Lainnya',
        ];
    }

    public static function jenisPresensiLabels(): array
    {
        return [
            'masuk' => 'Presensi Masuk',
            'keluar' => 'Presensi Keluar',
            'keduanya' => 'Presensi Masuk & Keluar',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::kategoriLabels()[$this->kategori] ?? ucfirst($this->kategori);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'diajukan' => 'badge-diajukan',
            'menunggu_verifikasi' => 'badge-menunggu',
            'diverifikasi' => 'badge-diverifikasi',
            'diproses' => 'badge-diproses',
            'menunggu_informasi' => 'badge-info',
            'diselesaikan' => 'badge-selesai',
            'ditutup' => 'badge-tutup',
            'ditolak' => 'badge-tolak',
            default => 'badge-default',
        };
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function responses()
    {
        return $this->hasMany(PengaduanResponse::class)->orderBy('created_at', 'asc');
    }

    public function publicResponses()
    {
        return $this->hasMany(PengaduanResponse::class)
            ->where('visible_to_employee', true)
            ->orderBy('created_at', 'asc');
    }

    public function attachments()
    {
        return $this->hasMany(PengaduanAttachment::class);
    }

    public function verifikasi()
    {
        return $this->hasOne(Verifikasi::class);
    }

    public function latestResponse()
    {
        return $this->hasOne(PengaduanResponse::class)->latestOfMany();
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('is_overdue', true)
            ->whereNotIn('status', ['diselesaikan', 'ditutup', 'ditolak']);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['diselesaikan', 'ditutup', 'ditolak']);
    }

    // Boot: generate ticket number & set deadline
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pengaduan) {
            if (empty($pengaduan->nomor_tiket)) {
                $year = now()->format('Y');
                $count = static::whereYear('created_at', $year)->count() + 1;
                $pengaduan->nomor_tiket = 'ADU-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
            if (empty($pengaduan->batas_waktu)) {
                $pengaduan->batas_waktu = now()->addDays(7)->toDateString();
            }
        });
    }
}
