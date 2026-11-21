@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil')

@section('content')
@php $authUser = auth()->user(); @endphp

<div class="page-header">
    <div>
        <h1>Profil Pengguna</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Profil</li>
            </ol>
        </nav>
    </div>
</div>

@if(config('sso.sso_mode') === 'dev')
<div class="sso-dev-banner mb-3">
    <i class="bi bi-cone-striped me-1"></i>
    <strong>Mode Pengembangan:</strong> Informasi profil ini bersumber dari akun demo lokal.
    Pada lingkungan produksi, data akan diambil secara otomatis dari SSO institusional DPD RI.
</div>
@endif

<div class="row g-3">
    <!-- Profile Card -->
    <div class="col-12 col-md-4">
        <div class="card text-center">
            <div class="card-body py-4">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--navy);color:white;
                            font-size:2rem;font-weight:700;display:inline-flex;align-items:center;
                            justify-content:center;margin-bottom:1rem;">
                    {{ strtoupper(substr($authUser->name, 0, 2)) }}
                </div>
                <h5 class="mb-1" style="color:var(--navy);">{{ $authUser->name }}</h5>
                <p class="text-muted mb-2" style="font-size:.85rem;">{{ $authUser->position ?? 'Belum diatur' }}</p>
                <span class="role-pill {{ $authUser->role }}" style="font-size:.7rem;">
                    {{ match($authUser->role) {
                        'admin' => 'Administrator',
                        'manajemen' => 'Manajemen',
                        default => 'Pegawai'
                    } }}
                </span>
                <hr>
                <!-- Logout button -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar dari Sistem
                    </button>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card mt-3">
            <div class="card-header"><i class="bi bi-shield-check me-2"></i>Informasi Akun</div>
            <div class="card-body">
                <table class="table table-sm mb-0" style="font-size:.82rem;">
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($authUser->is_active)
                                <span class="badge-status badge-selesai">Aktif</span>
                            @else
                                <span class="badge-status badge-tolak">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Login Terakhir</td>
                        <td>{{ $authUser->last_login_at ? $authUser->last_login_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Akun Dibuat</td>
                        <td>{{ $authUser->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Autentikasi</td>
                        <td>
                            @if(config('sso.sso_mode') === 'dev')
                                <span class="text-warning small">Dev Mode</span>
                            @else
                                <span class="text-success small">SSO Institusional</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Data Pegawai -->
    <div class="col-12 col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-person-badge me-2"></i>Data Kepegawaian
                <span class="text-muted small ms-2">
                    <i class="bi bi-lock me-1"></i>Data dikelola oleh SSO institusional
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Nama Lengkap</label>
                        <div class="form-control bg-light" style="cursor:not-allowed;">
                            {{ $authUser->name }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">NIP</label>
                        <div class="form-control bg-light" style="cursor:not-allowed;">
                            {{ $authUser->nip }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Jabatan</label>
                        <div class="form-control bg-light" style="cursor:not-allowed;">
                            {{ $authUser->position ?? '-' }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small">Unit Kerja</label>
                        <div class="form-control bg-light" style="cursor:not-allowed;">
                            {{ $authUser->unit ?? '-' }}
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small">Email Dinas</label>
                        <div class="form-control bg-light" style="cursor:not-allowed;">
                            {{ $authUser->email }}
                        </div>
                    </div>
                </div>
                <div class="mt-3 p-2 rounded" style="background:#f0f4ff;border:1px solid #c7d7f8;font-size:.78rem;color:#3b5bdb;">
                    <i class="bi bi-info-circle me-1"></i>
                    Data kepegawaian diambil secara otomatis dari sistem SSO Sekretariat Jenderal DPD RI dan tidak dapat diubah secara manual melalui aplikasi ini.
                    Untuk perubahan data, hubungi Biro Kepegawaian.
                </div>
            </div>
        </div>

        <!-- Statistik Pengaduan (pegawai) -->
        @if($authUser->isPegawai())
        <div class="card">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Ringkasan Pengaduan Saya</div>
            <div class="card-body">
                @php
                    $myStats = [
                        'total' => \App\Models\Pengaduan::where('user_id', $authUser->id)->count(),
                        'aktif' => \App\Models\Pengaduan::where('user_id', $authUser->id)->whereNotIn('status', ['diselesaikan','ditutup','ditolak'])->count(),
                        'selesai' => \App\Models\Pengaduan::where('user_id', $authUser->id)->whereIn('status', ['diselesaikan','ditutup'])->count(),
                        'ditolak' => \App\Models\Pengaduan::where('user_id', $authUser->id)->where('status', 'ditolak')->count(),
                    ];
                @endphp
                <div class="row g-3">
                    <div class="col-6 col-sm-3">
                        <div class="text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:var(--navy);">{{ $myStats['total'] }}</div>
                            <div class="text-muted small">Total</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:#d97706;">{{ $myStats['aktif'] }}</div>
                            <div class="text-muted small">Aktif</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:#15803d;">{{ $myStats['selesai'] }}</div>
                            <div class="text-muted small">Selesai</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center">
                            <div style="font-size:1.5rem;font-weight:700;color:#dc2626;">{{ $myStats['ditolak'] }}</div>
                            <div class="text-muted small">Ditolak</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-clipboard2-list me-1"></i>Lihat Semua Pengaduan
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Admin: User Management quick link -->
        @if($authUser->isAdmin())
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Manajemen Pengguna</div>
            <div class="card-body">
                <p class="text-muted small mb-3">Kelola akun pengguna sistem SIKAP DPD RI.</p>
                @php
                    $userCounts = [
                        'total' => \App\Models\User::count(),
                        'pegawai' => \App\Models\User::where('role', 'pegawai')->count(),
                        'admin' => \App\Models\User::where('role', 'admin')->count(),
                        'manajemen' => \App\Models\User::where('role', 'manajemen')->count(),
                    ];
                @endphp
                <div class="row g-2 mb-3">
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-700" style="font-size:1.3rem;color:var(--navy);">{{ $userCounts['total'] }}</div>
                            <div class="text-muted small">Total</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-700" style="font-size:1.3rem;color:#a0782a;">{{ $userCounts['pegawai'] }}</div>
                            <div class="text-muted small">Pegawai</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-700" style="font-size:1.3rem;color:#2563eb;">{{ $userCounts['admin'] }}</div>
                            <div class="text-muted small">Admin</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-center p-2 border rounded">
                            <div class="fw-700" style="font-size:1.3rem;color:#7c3aed;">{{ $userCounts['manajemen'] }}</div>
                            <div class="text-muted small">Manajemen</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('profil.users') }}" class="btn btn-sm btn-navy">
                        <i class="bi bi-people me-1"></i>Kelola Pengguna
                    </a>
                    <a href="{{ route('profil.users.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-person-plus me-1"></i>Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
