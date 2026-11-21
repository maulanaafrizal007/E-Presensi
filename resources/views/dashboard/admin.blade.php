@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page-title', 'Dashboard Administrator')

@push('styles')
<style>
    .chart-bar-wrap { display: flex; align-items: flex-end; gap: 6px; height: 100px; padding: .5rem 0; }
    .chart-bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; }
    .chart-bar { width: 100%; border-radius: 4px 4px 0 0; min-height: 4px; transition: height .4s; }
    .chart-bar.total { background: var(--navy); opacity: .7; }
    .chart-bar.selesai { background: #22c55e; opacity: .8; }
    .chart-label { font-size: .6rem; color: var(--muted); text-align: center; }
    .pie-segment { cursor: default; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Dashboard Administrator</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('pengaduan.index') }}" class="btn btn-navy">
        <i class="bi bi-clipboard2-list me-1"></i>Kelola Pengaduan
    </a>
</div>

<!-- Stats Row 1 -->
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="bi bi-collection"></i></div>
            <div>
                <div class="stat-label">Total Pengaduan</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon warning"><i class="bi bi-inbox-fill"></i></div>
            <div>
                <div class="stat-label">Pengaduan Baru</div>
                <div class="stat-value">{{ $stats['baru'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="bi bi-search"></i></div>
            <div>
                <div class="stat-label">Menunggu Verifikasi</div>
                <div class="stat-value">{{ $stats['menunggu_verifikasi'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon info"><i class="bi bi-arrow-repeat"></i></div>
            <div>
                <div class="stat-label">Sedang Diproses</div>
                <div class="stat-value">{{ $stats['diproses'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row 2 -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon gold"><i class="bi bi-question-circle"></i></div>
            <div>
                <div class="stat-label">Menunggu Info Tambahan</div>
                <div class="stat-value">{{ $stats['menunggu_informasi'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="stat-label">Diselesaikan</div>
                <div class="stat-value">{{ $stats['diselesaikan'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon danger"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="stat-label">Melewati Batas Waktu</div>
                <div class="stat-value">{{ $stats['overdue'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-icon navy"><i class="bi bi-percent"></i></div>
            <div>
                <div class="stat-label">Tingkat Penyelesaian</div>
                <div class="stat-value">
                    @php
                        $rate = $stats['total'] > 0 ? round(($stats['diselesaikan'] / $stats['total']) * 100) : 0;
                    @endphp
                    {{ $rate }}%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Recent -->
<div class="row g-3 mb-4">
    <!-- Monthly Bar Chart -->
    <div class="col-12 col-lg-7">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Statistik Pengaduan 6 Bulan Terakhir
            </div>
            <div class="card-body">
                @php
                    $maxVal = collect($monthlyStats)->max('total') ?: 1;
                @endphp
                <div class="chart-bar-wrap">
                    @foreach($monthlyStats as $m)
                    <div class="chart-bar-col">
                        <div style="display:flex;gap:2px;align-items:flex-end;width:100%;height:80px;">
                            <div class="chart-bar total"
                                 style="height:{{ ($m['total']/$maxVal)*100 }}%;flex:1;"
                                 title="Total: {{ $m['total'] }}"></div>
                            <div class="chart-bar selesai"
                                 style="height:{{ $m['total']>0 ? ($m['selesai']/$maxVal)*100 : 0 }}%;flex:1;"
                                 title="Selesai: {{ $m['selesai'] }}"></div>
                        </div>
                        <div class="chart-label">{{ $m['label'] }}<br><small>{{ $m['total'] }}</small></div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex gap-3 mt-2">
                    <span style="font-size:.72rem;"><span style="display:inline-block;width:10px;height:10px;background:var(--navy);border-radius:2px;opacity:.7;margin-right:4px;"></span>Total</span>
                    <span style="font-size:.72rem;"><span style="display:inline-block;width:10px;height:10px;background:#22c55e;border-radius:2px;opacity:.8;margin-right:4px;"></span>Diselesaikan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="col-12 col-lg-5">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Distribusi Kategori Pengaduan
            </div>
            <div class="card-body">
                @if($categoryStats->isEmpty())
                    <div class="empty-state py-3">
                        <div class="empty-icon"><i class="bi bi-pie-chart"></i></div>
                        <p class="small">Belum ada data kategori.</p>
                    </div>
                @else
                    @php
                        $catTotal = $categoryStats->sum();
                        $colors = ['#1a3561','#c9a84c','#3b82f6','#22c55e','#f59e0b','#8b5cf6'];
                        $i = 0;
                    @endphp
                    @foreach($categoryStats as $label => $count)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div style="width:12px;height:12px;border-radius:3px;background:{{ $colors[$i % count($colors)] }};flex-shrink:0;"></div>
                        <div style="flex:1;font-size:.8rem;">{{ $label }}</div>
                        <div style="font-size:.8rem;font-weight:600;">{{ $count }}</div>
                        <div style="width:80px;">
                            <div style="background:#e5e7eb;border-radius:4px;height:6px;">
                                <div style="background:{{ $colors[$i % count($colors)] }};border-radius:4px;height:6px;width:{{ $catTotal > 0 ? ($count/$catTotal)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    @php $i++; @endphp
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Incoming Complaints -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-inbox me-2"></i>Pengaduan Masuk Terbaru</span>
        <a href="{{ route('pengaduan.index') }}" class="btn btn-sm btn-navy">Kelola Semua</a>
    </div>
    <div class="card-body p-0">
        @if($recentPengaduans->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                <h6>Tidak ada pengaduan</h6>
            </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Tiket</th>
                        <th>Pegawai</th>
                        <th>Unit Kerja</th>
                        <th>Kategori</th>
                        <th>Tgl Diajukan</th>
                        <th>Status</th>
                        <th>Ditangani</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPengaduans as $p)
                    <tr class="{{ $p->is_overdue ? 'table-warning' : '' }}">
                        <td><code>{{ $p->nomor_tiket }}</code></td>
                        <td>
                            <div style="font-size:.8rem;font-weight:500;">{{ $p->nama_pegawai }}</div>
                            <div style="font-size:.7rem;color:#64748b;">{{ $p->nip }}</div>
                        </td>
                        <td style="font-size:.8rem;">{{ Str::limit($p->unit_kerja, 25) }}</td>
                        <td style="font-size:.8rem;">{{ $p->kategori_label }}</td>
                        <td style="font-size:.8rem;">{{ $p->created_at->format('d M Y') }}</td>
                        <td>
                            <span class="badge-status {{ $p->status_badge_class }}">
                                {{ $p->status_label }}
                            </span>
                            @if($p->is_overdue)
                                <span class="badge-status badge-tolak ms-1">Terlambat</span>
                            @endif
                        </td>
                        <td style="font-size:.8rem;">
                            {{ $p->handler?->name ?? '-' }}
                        </td>
                        <td>
                            <a href="{{ route('pengaduan.show', $p) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-arrow-right"></i>
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
