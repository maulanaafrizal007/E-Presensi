@extends('layouts.app')

@section('title', 'Kelola Pengaduan')
@section('page-title', 'Pengaduan Presensi')

@section('content')
<div class="page-header">
    <div>
        <h1>Kelola Pengaduan Presensi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengaduan Presensi</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari nama pegawai, NIP, atau nomor tiket..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="kategori" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $key => $label)
                            <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <input type="date" class="form-control form-control-sm"
                           name="tanggal_dari" value="{{ request('tanggal_dari') }}" placeholder="Dari">
                </div>
                <div class="col-6 col-md-2">
                    <div class="d-flex gap-1">
                        <input type="date" class="form-control form-control-sm"
                               name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" placeholder="Sampai">
                        <button type="submit" class="btn btn-sm btn-navy flex-shrink-0">
                            <i class="bi bi-funnel"></i>
                        </button>
                        @if(request()->hasAny(['search','status','kategori','tanggal_dari','tanggal_sampai']))
                            <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-outline-secondary flex-shrink-0">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-inbox me-2"></i>Daftar Pengaduan Masuk</span>
        <span class="text-muted small">{{ $pengaduans->total() }} pengaduan ditemukan</span>
    </div>
    <div class="card-body p-0">
        @if($pengaduans->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                <h6>Tidak ada pengaduan</h6>
                <p class="small">Tidak ada pengaduan yang sesuai dengan filter yang dipilih.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Pegawai / NIP</th>
                        <th>Unit Kerja</th>
                        <th>Kategori</th>
                        <th>Tgl Kejadian</th>
                        <th>Tgl Diajukan</th>
                        <th>Status</th>
                        <th>Ditangani Oleh</th>
                        <th>Batas Waktu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengaduans as $p)
                    <tr class="{{ $p->is_overdue && !in_array($p->status, ['diselesaikan','ditutup','ditolak']) ? 'table-warning' : '' }}">
                        <td>
                            <code>{{ $p->nomor_tiket }}</code>
                            @if($p->is_overdue && !in_array($p->status, ['diselesaikan','ditutup','ditolak']))
                                <br><span class="badge-status badge-tolak" style="font-size:.6rem;">Melewati Batas</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-500" style="font-size:.82rem;">{{ $p->nama_pegawai }}</div>
                            <div class="text-muted" style="font-size:.7rem;">{{ $p->nip }}</div>
                        </td>
                        <td style="font-size:.8rem;">{{ Str::limit($p->unit_kerja, 30) }}</td>
                        <td style="font-size:.8rem;">{{ $p->kategori_label }}</td>
                        <td style="font-size:.8rem;">{{ $p->tanggal_kejadian->format('d M Y') }}</td>
                        <td style="font-size:.8rem;">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge-status {{ $p->status_badge_class }}">
                                {{ $p->status_label }}
                            </span>
                        </td>
                        <td style="font-size:.8rem;">{{ $p->handler?->name ?? '-' }}</td>
                        <td style="font-size:.8rem;">
                            @if($p->batas_waktu)
                                <span class="{{ $p->batas_waktu->isPast() && !in_array($p->status, ['diselesaikan','ditutup','ditolak']) ? 'text-danger fw-600' : 'text-muted' }}">
                                    {{ $p->batas_waktu->format('d M Y') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('pengaduan.show', $p) }}"
                               class="btn btn-sm btn-navy">
                                <i class="bi bi-arrow-right me-1"></i>Tangani
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
