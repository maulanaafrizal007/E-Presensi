@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Selamat Datang, {{ auth()->user()->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pengaduan.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-circle me-1"></i> Buat Pengaduan
    </a>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="bi bi-collection"></i></div>
            <div>
                <div class="stat-label">Total Pengaduan</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="stat-label">Menunggu</div>
                <div class="stat-value">{{ $stats['diajukan'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon info"><i class="bi bi-arrow-repeat"></i></div>
            <div>
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-value">{{ $stats['diproses'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-label">Diselesaikan</div>
                <div class="stat-value">{{ $stats['diselesaikan'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Complaints -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clock-history me-2"></i>Riwayat Pengaduan Terbaru</span>
        <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-primary">
            Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        @if($recentPengaduans->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                <h6>Belum ada pengaduan</h6>
                <p class="small">Anda belum pernah mengajukan pengaduan presensi.</p>
                <a href="{{ route('pengaduan.create') }}" class="btn btn-sm btn-navy">
                    <i class="bi bi-plus me-1"></i>Buat Pengaduan Pertama
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Tiket</th>
                            <th>Subjek</th>
                            <th>Kategori</th>
                            <th>Tgl Diajukan</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPengaduans as $p)
                        <tr>
                            <td><code class="text-navy">{{ $p->nomor_tiket }}</code></td>
                            <td class="fw-500">{{ Str::limit($p->subjek, 45) }}</td>
                            <td>{{ $p->kategori_label }}</td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="badge-status {{ $p->status_badge_class }}">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('pengaduan.show', $p) }}"
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
