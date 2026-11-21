@extends('layouts.app')

@section('title', 'Detail Pengaduan ' . $pengaduan->nomor_tiket)
@section('page-title', 'Pengaduan Presensi')

@section('content')
@php $authUser = auth()->user(); @endphp

<div class="page-header">
    <div>
        <h1>
            <span class="text-muted fw-400 me-1" style="font-size:1rem;">Detail Pengaduan</span><br>
            <code style="font-size:1.1rem;">{{ $pengaduan->nomor_tiket }}</code>
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pengaduan.index') }}">Pengaduan Presensi</a></li>
                <li class="breadcrumb-item active">{{ $pengaduan->nomor_tiket }}</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2 align-items-start flex-wrap">
        <span class="badge-status {{ $pengaduan->status_badge_class }}" style="font-size:.82rem;padding:.4rem .9rem;">
            {{ $pengaduan->status_label }}
        </span>
        @if($pengaduan->is_overdue && !in_array($pengaduan->status, ['diselesaikan','ditutup','ditolak']))
            <span class="badge-status badge-tolak" style="font-size:.82rem;padding:.4rem .9rem;">
                <i class="bi bi-exclamation-triangle me-1"></i>Melewati Batas Waktu
            </span>
        @endif
    </div>
</div>

<div class="row g-3">
    <!-- Left: Main Info -->
    <div class="col-12 col-lg-8">

        <!-- Complaint Details -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-file-text me-2"></i>Detail Pengaduan</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Nama Pegawai</div>
                        <div class="fw-600">{{ $pengaduan->nama_pegawai }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">NIP</div>
                        <div class="fw-600">{{ $pengaduan->nip }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Unit Kerja</div>
                        <div class="fw-600">{{ $pengaduan->unit_kerja }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Kategori Pengaduan</div>
                        <div class="fw-600">{{ $pengaduan->kategori_label }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Tanggal Kejadian</div>
                        <div class="fw-600">{{ $pengaduan->tanggal_kejadian->format('d F Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Jenis Presensi</div>
                        <div class="fw-600">{{ \App\Models\Pengaduan::jenisPresensiLabels()[$pengaduan->jenis_presensi] ?? $pengaduan->jenis_presensi }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small mb-1">Subjek</div>
                        <div class="fw-600">{{ $pengaduan->subjek }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small mb-1">Deskripsi Pengaduan</div>
                        <div style="background:#f8f9fb;border-radius:8px;padding:.85rem;border:1px solid #e2e8f0;font-size:.88rem;line-height:1.6;">
                            {{ $pengaduan->deskripsi }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        @if($pengaduan->attachments->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-paperclip me-2"></i>Bukti Pendukung</div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($pengaduan->attachments as $att)
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2 p-2 border rounded"
                             style="border-radius:8px !important;">
                            <div style="font-size:1.5rem;color:var(--navy);">
                                @if(str_contains($att->file_type ?? '', 'image'))
                                    <i class="bi bi-file-image"></i>
                                @elseif(str_contains($att->file_type ?? '', 'pdf'))
                                    <i class="bi bi-file-pdf text-danger"></i>
                                @else
                                    <i class="bi bi-file-earmark-text"></i>
                                @endif
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.8rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $att->file_name }}
                                </div>
                                <div style="font-size:.7rem;color:#64748b;">
                                    {{ $att->file_size_formatted }}
                                </div>
                            </div>
                            <a href="{{ Storage::url($att->file_path) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Verification Results -->
        @if($pengaduan->verifikasi)
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-clipboard-check me-2"></i>Hasil Verifikasi Manual</div>
            <div class="card-body">
                @php $v = $pengaduan->verifikasi; @endphp
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Tanggal Presensi Diklaim</div>
                        <div class="fw-600">{{ $v->tanggal_presensi_diklaim->format('d F Y') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Diverifikasi oleh</div>
                        <div class="fw-600">{{ $v->verifier->name ?? '-' }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small mb-1">Jam Masuk (Diklaim)</div>
                        <div class="fw-600">{{ $v->jam_masuk_diklaim ?: '-' }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small mb-1">Jam Keluar (Diklaim)</div>
                        <div class="fw-600">{{ $v->jam_keluar_diklaim ?: '-' }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small mb-1">Jam Masuk (Sistem)</div>
                        <div class="fw-600">{{ $v->jam_masuk_sistem ?: '-' }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-muted small mb-1">Jam Keluar (Sistem)</div>
                        <div class="fw-600">{{ $v->jam_keluar_sistem ?: '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Ada Selisih Data?</div>
                        <div class="fw-600 {{ $v->ada_selisih ? 'text-warning' : 'text-success' }}">
                            {{ $v->ada_selisih ? 'Ya, ditemukan selisih' : 'Tidak ada selisih' }}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small mb-1">Hasil Verifikasi</div>
                        <div class="fw-600">{{ \App\Models\Verifikasi::hasilLabels()[$v->hasil_verifikasi] ?? $v->hasil_verifikasi }}</div>
                    </div>
                    @if($v->detail_selisih)
                    <div class="col-12">
                        <div class="text-muted small mb-1">Detail Selisih</div>
                        <div style="background:#fffbeb;border-radius:8px;padding:.85rem;border:1px solid #fde68a;font-size:.88rem;">
                            {{ $v->detail_selisih }}
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <div class="text-muted small mb-1">Catatan Verifikasi</div>
                        <div style="background:#f8f9fb;border-radius:8px;padding:.85rem;border:1px solid #e2e8f0;font-size:.88rem;">
                            {{ $v->catatan_verifikasi }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Response / Timeline -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-chat-left-text me-2"></i>Riwayat Proses &amp; Respons</div>
            <div class="card-body">
                @if($visibleResponses->isEmpty())
                    <p class="text-muted small mb-0">Belum ada respons dari administrator.</p>
                @else
                    <ul class="timeline">
                        @foreach($visibleResponses as $resp)
                        <li class="timeline-item">
                            <div class="timeline-dot {{ $resp->user->role }}">
                                {{ strtoupper(substr($resp->user->name, 0, 1)) }}
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <div>
                                        <div class="timeline-author">{{ $resp->user->name }}</div>
                                        <div class="timeline-tipe">
                                            {{ \App\Models\PengaduanResponse::tipeLabels()[$resp->tipe] ?? $resp->tipe }}
                                            @if($resp->status_before && $resp->status_after && $resp->status_before !== $resp->status_after)
                                                @php $bm2 = ['diajukan'=>'badge-diajukan','menunggu_verifikasi'=>'badge-menunggu','diverifikasi'=>'badge-diverifikasi','diproses'=>'badge-diproses','menunggu_informasi'=>'badge-info','diselesaikan'=>'badge-selesai','ditutup'=>'badge-tutup','ditolak'=>'badge-tolak']; @endphp
                                                &rarr; Status:
                                                <span class="badge-status {{ $bm2[$resp->status_after] ?? 'badge-default' }}" style="font-size:.6rem;">
                                                    {{ \App\Models\Pengaduan::statusLabels()[$resp->status_after] ?? $resp->status_after }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="timeline-time">{{ $resp->created_at->format('d M Y H:i') }}</div>
                                </div>
                                <div class="timeline-msg {{ $resp->tipe === 'internal_note' ? 'internal' : '' }}">
                                    {{ $resp->pesan }}
                                    @if($resp->tipe === 'internal_note' && $authUser->isAdmin())
                                        <span class="badge bg-warning text-dark ms-2" style="font-size:.6rem;">Catatan Internal</span>
                                    @endif
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- ADMIN: Add Response / Update Status -->
        @if($authUser->isAdmin())
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-reply me-2"></i>Tambah Respons / Perbarui Status</div>
            <div class="card-body">
                <form method="POST" action="{{ route('pengaduan.respond', $pengaduan) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Tipe Respons <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipe" required>
                                <option value="">-- Pilih Tipe --</option>
                                @foreach(\App\Models\PengaduanResponse::tipeLabels() as $key => $label)
                                    <option value="{{ $key }}" {{ old('tipe') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Ubah Status Menjadi</label>
                            <select class="form-select" name="new_status">
                                <option value="">-- Tidak Ubah Status --</option>
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ $pengaduan->status === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Pesan / Catatan <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="pesan" rows="4"
                                      placeholder="Tulis respons, catatan internal, atau keterangan penyelesaian..."
                                      required minlength="5">{{ old('pesan') }}</textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-navy">
                                <i class="bi bi-send me-2"></i>Simpan Respons
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- ADMIN: Manual Verification Form -->
        @if(!$pengaduan->verifikasi)
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-search me-2"></i>Verifikasi Manual Presensi</div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Lakukan pengecekan manual di sistem e-Presensi, kemudian isi formulir verifikasi di bawah ini.
                    Data resmi tidak akan diubah secara otomatis.
                </div>
                <form method="POST" action="{{ route('pengaduan.verifikasi.store', $pengaduan) }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Tanggal Presensi yang Diklaim <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_presensi_diklaim"
                                   value="{{ $pengaduan->tanggal_kejadian->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Jam Masuk (Diklaim)</label>
                            <input type="time" class="form-control" name="jam_masuk_diklaim">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Jam Keluar (Diklaim)</label>
                            <input type="time" class="form-control" name="jam_keluar_diklaim">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Jam Masuk (Sistem e-Presensi)</label>
                            <input type="time" class="form-control" name="jam_masuk_sistem"
                                   placeholder="Dari pengecekan sistem">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Jam Keluar (Sistem e-Presensi)</label>
                            <input type="time" class="form-control" name="jam_keluar_sistem">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Ada Selisih Data? <span class="text-danger">*</span></label>
                            <select class="form-select" name="ada_selisih" required>
                                <option value="0">Tidak ada selisih</option>
                                <option value="1">Ya, ditemukan selisih</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Detail Selisih (jika ada)</label>
                            <textarea class="form-control" name="detail_selisih" rows="2"
                                      placeholder="Jelaskan selisih yang ditemukan..."></textarea>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Hasil Verifikasi <span class="text-danger">*</span></label>
                            <select class="form-select" name="hasil_verifikasi" required>
                                <option value="">-- Pilih Hasil --</option>
                                @foreach(\App\Models\Verifikasi::hasilLabels() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Verifikasi <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan_verifikasi" rows="3"
                                      placeholder="Jelaskan hasil pengecekan manual yang dilakukan..." required minlength="10"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-navy">
                                <i class="bi bi-clipboard-check me-2"></i>Simpan Hasil Verifikasi
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            Verifikasi manual telah dilakukan oleh <strong>{{ $pengaduan->verifikasi->verifier->name }}</strong>
            pada {{ $pengaduan->verifikasi->verified_at->format('d M Y H:i') }}.
        </div>
        @endif
        @endif

    </div>

    <!-- Right sidebar: Status & Info -->
    <div class="col-12 col-lg-4">

        <!-- Status Info -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Pengaduan</div>
            <div class="card-body">
                <table class="table table-sm mb-0" style="font-size:.82rem;">
                    <tr>
                        <td class="text-muted pe-2">No. Tiket</td>
                        <td><code>{{ $pengaduan->nomor_tiket }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-2">Status</td>
                        <td><span class="badge-status {{ $pengaduan->status_badge_class }}">{{ $pengaduan->status_label }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted pe-2">Tanggal Diajukan</td>
                        <td>{{ $pengaduan->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @if($pengaduan->handler)
                    <tr>
                        <td class="text-muted pe-2">Ditangani oleh</td>
                        <td>{{ $pengaduan->handler->name }}</td>
                    </tr>
                    @endif
                    @if($pengaduan->handled_at)
                    <tr>
                        <td class="text-muted pe-2">Mulai Ditangani</td>
                        <td>{{ $pengaduan->handled_at->format('d M Y') }}</td>
                    </tr>
                    @endif
                    @if($pengaduan->resolved_at)
                    <tr>
                        <td class="text-muted pe-2">Diselesaikan</td>
                        <td>{{ $pengaduan->resolved_at->format('d M Y') }}</td>
                    </tr>
                    @endif
                    @if($pengaduan->batas_waktu)
                    <tr>
                        <td class="text-muted pe-2">Batas Waktu</td>
                        <td class="{{ $pengaduan->batas_waktu->isPast() && !in_array($pengaduan->status, ['diselesaikan','ditutup','ditolak']) ? 'text-danger fw-600' : '' }}">
                            {{ $pengaduan->batas_waktu->format('d M Y') }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted pe-2">Verifikasi</td>
                        <td>
                            @if($pengaduan->verifikasi)
                                <span class="badge-status badge-diverifikasi">Sudah</span>
                            @else
                                <span class="badge-status badge-menunggu">Belum</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Status History (admin only: see all; pegawai: only public) -->
        @if($authUser->isAdmin() && $pengaduan->responses->where('tipe', 'status_change')->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-arrow-repeat me-2"></i>Riwayat Status</div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($pengaduan->responses->where('tipe', 'status_change') as $sc)
                    <div class="list-group-item" style="font-size:.78rem;padding:.5rem .85rem;">
                        @if($sc->status_before && $sc->status_after)
                            @php
                                $badgeMap = ['diajukan'=>'badge-diajukan','menunggu_verifikasi'=>'badge-menunggu','diverifikasi'=>'badge-diverifikasi','diproses'=>'badge-diproses','menunggu_informasi'=>'badge-info','diselesaikan'=>'badge-selesai','ditutup'=>'badge-tutup','ditolak'=>'badge-tolak'];
                            @endphp
                            <span class="badge-status badge-default" style="font-size:.62rem;">
                                {{ \App\Models\Pengaduan::statusLabels()[$sc->status_before] ?? $sc->status_before }}
                            </span>
                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            <span class="badge-status {{ $badgeMap[$sc->status_after] ?? 'badge-default' }}" style="font-size:.62rem;">
                                {{ \App\Models\Pengaduan::statusLabels()[$sc->status_after] ?? $sc->status_after }}
                            </span>
                        @endif
                        <div class="text-muted mt-1">
                            {{ $sc->user->name }} &middot; {{ $sc->created_at->format('d M Y H:i') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary w-100">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>
</div>
@endsection
