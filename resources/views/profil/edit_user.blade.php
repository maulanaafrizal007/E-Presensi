@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('page-title', 'Profil')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Pengguna: {{ $user->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('profil.index') }}">Profil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('profil.users') }}">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Edit Data Pengguna</div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <strong>Mohon perbaiki kesalahan berikut:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <!-- Read-only identity info -->
                <div class="card bg-light mb-4">
                    <div class="card-body py-2">
                        <div style="font-size:.78rem;color:#64748b;font-weight:600;text-transform:uppercase;margin-bottom:.5rem;">
                            <i class="bi bi-lock me-1"></i>Identitas (Hanya Baca)
                        </div>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <div class="text-muted small">Nama Lengkap</div>
                                <div class="fw-600">{{ $user->name }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">NIP</div>
                                <div class="fw-600">{{ $user->nip }}</div>
                            </div>
                            <div class="col-12">
                                <div class="text-muted small">Email</div>
                                <div class="fw-600">{{ $user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('profil.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" class="form-control"
                                   name="position" value="{{ old('position', $user->position) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" class="form-control"
                                   name="unit" value="{{ old('unit', $user->unit) }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" required>
                                <option value="pegawai" {{ old('role', $user->role) === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="manajemen" {{ old('role', $user->role) === 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Status Akun</label>
                            <div class="mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           role="switch" id="is_active" name="is_active"
                                           value="1"
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                           {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Akun Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr>
                            <div class="text-muted small mb-2">Ubah Kata Sandi (kosongkan jika tidak ingin mengubah)</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Kata Sandi Baru</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   name="new_password" placeholder="Min. 8 karakter">
                            @error('new_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" class="form-control"
                                   name="new_password_confirmation">
                        </div>
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-navy px-4">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('profil.users') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
