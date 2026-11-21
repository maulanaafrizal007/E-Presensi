@extends('layouts.app')

@section('title', 'Pengaduan Presensi Saya')
@section('page-title', 'Pengaduan Presensi')

@section('content')
<div class="page-header">
    <div>
        <h1>Pengaduan Presensi Saya</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengaduan Presensi</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pengaduan.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-circle me-1"></i>Buat Pengaduan Baru
    </a>
</div>

<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-sm-5 col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-5 col-md-4">
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $key => $label)
                        <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-navy">Filter</button>
                @if(request('status') || request('kategori'))
                    <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-list-ul me-2"></i>Daftar Pengaduan
        <span class="text-muted ms-2 small">({{ $pengaduans->total() }} pengaduan)</span>
    </div>
    <div class="card-body p-0">
        @if($pengaduans->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-clipboard2-x"></i></div>
                <h6>Tidak ada pengaduan</h6>
                <p class="small">Anda belum mengajukan pengaduan sesuai filter yang dipilih.</p>
                <a href="{{ route('pengaduan.create') }}" class="btn btn-sm btn-navy">
                    <i class="bi bi-plus me-1"></i>Ajukan Pengaduan
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
                        <th>Tgl Kejadian</th>
                        <th>Tgl Diajukan</th>
                        <th>Status</th>
                        <th>Respons Terakhir</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaduans as $p)
                    <tr>
                        <td><code class="text-primary">{{ $p->nomor_tiket }}</code></td>
                        <td>
                            <div class="fw-500" style="font-size:.85rem;">{{ Str::limit($p->subjek, 40) }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ \App\Models\Pengaduan::jenisPresensiLabels()[$p->jenis_presensi] ?? $p->jenis_presensi }}</div>
                        </td>
                        <td style="font-size:.82rem;">{{ $p->kategori_label }}</td>
                        <td style="font-size:.82rem;">{{ $p->tanggal_kejadian->format('d M Y') }}</td>
                        <td style="font-size:.82rem;">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge-status {{ $p->status_badge_class }}">
                                {{ $p->status_label }}
                            </span>
                        </td>
                        <td style="font-size:.78rem;max-width:200px;">
                            @if($p->latestResponse && $p->latestResponse->visible_to_employee)
                                <div class="text-truncate">{{ Str::limit($p->latestResponse->pesan, 50) }}</div>
                                <div class="text-muted">{{ $p->latestResponse->created_at->diffForHumans() }}</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pengaduan.show', $p) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Lihat Detail">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pengaduans->hasPages())
        <div class="card-footer">
            {{ $pengaduans->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection
