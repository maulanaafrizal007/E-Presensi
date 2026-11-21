<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'position',
        'unit',
        'role',
        'password',
        'sso_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Role helpers
    public function isPegawai(): bool
    {
        return $this->role === 'pegawai';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManajemen(): bool
    {
        return $this->role === 'manajemen';
    }

    public function isAdminOrManajemen(): bool
    {
        return in_array($this->role, ['admin', 'manajemen']);
    }

    // Relationships
    public function pengaduans()
    {
        return $this->hasMany(Pengaduan::class);
    }

    public function handledPengaduans()
    {
        return $this->hasMany(Pengaduan::class, 'handled_by');
    }

    public function responses()
    {
        return $this->hasMany(PengaduanResponse::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
