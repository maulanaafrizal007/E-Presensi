<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanAttachment extends Model
{
    protected $fillable = [
        'pengaduan_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by_role',
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '-';
        $kb = $this->file_size / 1024;
        if ($kb < 1024) return round($kb, 1) . ' KB';
        return round($kb / 1024, 2) . ' MB';
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->file_type ?? '', 'image/');
    }
}
