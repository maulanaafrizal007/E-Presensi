@extends('layouts.app')

@section('title', 'Ajukan Pengaduan Presensi')
@section('page-title', 'Pengaduan Presensi')

@section('content')
<div class="page-header">
    <div>
        <h1>Ajukan Pengaduan Presensi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pengaduan.index') }}">Pengaduan Presensi</a></li>
                <li class="breadcrumb-item active">Ajukan Pengaduan</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clipboard2-plus me-2"></i>Formulir Pengaduan Presensi
            </div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <strong><i class="bi bi-exclamation-circle me-1"></i>Mohon perbaiki kesalahan berikut:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Auto-populated from SSO/auth -->
                    <div class="card bg-light mb-4">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-person-badge text-muted"></i>
                                <span style="font-size:.78rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                                    Informasi Pegawai (dari SSO)
                                </span>
                            </div>
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div style="font-size:.78rem;color:#64748b;">Nama Lengkap</div>
                                    <div style="font-size:.88rem;font-weight:600;">{{ auth()->user()->name }}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div style="font-size:.78rem;color:#64748b;">NIP</div>
                                    <div style="font-size:.88rem;font-weight:600;">{{ auth()->user()->nip }}</div>
                                </div>
                                <div class="col-12">
                                    <div style="font-size:.78rem;color:#64748b;">Unit Kerja</div>
                                    <div style="font-size:.88rem;font-weight:600;">{{ auth()->user()->unit ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-3">
                        <label class="form-label" for="kategori">
                            Kategori Pengaduan <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('kategori') is-invalid @enderror"
                                id="kategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $key => $label)
                                <option value="{{ $key }}" {{ old('kategori') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tanggal & Jenis -->
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="tanggal_kejadian">
                                Tanggal Kejadian <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('tanggal_kejadian') is-invalid @enderror"
                                   id="tanggal_kejadian" name="tanggal_kejadian"
                                   value="{{ old('tanggal_kejadian') }}"
                                   max="{{ date('Y-m-d') }}" required>
                            @error('tanggal_kejadian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label" for="jenis_presensi">
                                Jenis Presensi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('jenis_presensi') is-invalid @enderror"
                                    id="jenis_presensi" name="jenis_presensi" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisPresensi as $key => $label)
                                    <option value="{{ $key }}" {{ old('jenis_presensi') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_presensi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Subjek -->
                    <div class="mb-3">
                        <label class="form-label" for="subjek">
                            Subjek Pengaduan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('subjek') is-invalid @enderror"
                               id="subjek" name="subjek"
                               value="{{ old('subjek') }}"
                               placeholder="Contoh: Presensi masuk tidak terekam pada tanggal ..."
                               maxlength="255" required>
                        @error('subjek')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <label class="form-label" for="deskripsi">
                            Deskripsi Pengaduan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi" name="deskripsi"
                                  rows="5"
                                  placeholder="Jelaskan secara detail kronologi kejadian, termasuk waktu, lokasi, dan kondisi yang menyebabkan permasalahan presensi..."
                                  minlength="20" maxlength="2000" required>{{ old('deskripsi') }}</textarea>
                        <div class="form-text text-muted">Minimal 20 karakter. <span id="charCount">0</span>/2000</div>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Attachments -->
                    <div class="mb-4">
                        <label class="form-label" for="attachments">
                            Bukti Pendukung
                            <span class="text-muted small">(opsional)</span>
                        </label>
                        <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                               id="attachments" name="attachments[]"
                               multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="form-text text-muted">
                            Format yang diterima: JPG, PNG, PDF, DOC, DOCX. Maks. 5 MB per file.
                            Contoh: foto layar mesin presensi, screenshot sistem, surat keterangan.
                        </div>
                        @error('attachments.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-gold px-4">
                            <i class="bi bi-send me-2"></i>Kirim Pengaduan
                        </button>
                        <a href="{{ route('pengaduan.index') }}" class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Help sidebar -->
    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Panduan Pengisian</div>
            <div class="card-body" style="font-size:.82rem;">
                <p class="mb-2"><strong>Kategori Pengaduan:</strong></p>
                <ul class="ps-3 mb-3" style="line-height:1.9;">
                    @foreach($kategoris as $key => $label)
                        <li>{{ $label }}</li>
                    @endforeach
                </ul>
                <p class="mb-2"><strong>Bukti yang Dapat Dilampirkan:</strong></p>
                <ul class="ps-3 mb-0" style="line-height:1.9;">
                    <li>Screenshot sistem e-Presensi</li>
                    <li>Foto mesin absensi</li>
                    <li>Surat keterangan atasan</li>
                    <li>Bukti perjalanan/aktivitas</li>
                </ul>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Alur Proses</div>
            <div class="card-body" style="font-size:.8rem;">
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div style="width:22px;height:22px;border-radius:50%;background:var(--navy);color:white;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0;">1</div>
                    <div><strong>Pengajuan</strong><br>Anda mengisi &amp; mengirim formulir</div>
                </div>
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div style="width:22px;height:22px;border-radius:50%;background:#3b82f6;color:white;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0;">2</div>
                    <div><strong>Verifikasi</strong><br>Admin memeriksa data di e-Presensi</div>
                </div>
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div style="width:22px;height:22px;border-radius:50%;background:#8b5cf6;color:white;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0;">3</div>
                    <div><strong>Tindak Lanjut</strong><br>Admin memproses pengaduan</div>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <div style="width:22px;height:22px;border-radius:50%;background:#22c55e;color:white;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0;">4</div>
                    <div><strong>Penyelesaian</strong><br>Pengaduan diselesaikan</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const textarea = document.getElementById('deskripsi');
    const counter = document.getElementById('charCount');
    function updateCount() {
        counter.textContent = textarea.value.length;
    }
    textarea.addEventListener('input', updateCount);
    updateCount();
</script>
@endpush
