@extends('layouts.app')

@section('title', 'Tambah Pengguna')
@section('page-title', 'Profil')

@section('content')
<div class="page-header">
    <div>
        <h1>Tambah Pengguna Baru</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('profil.index') }}">Profil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('profil.users') }}">Manajemen Pengguna</a></li>
                <li class="breadcrumb-item active">Tambah</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-plus me-2"></i>Formulir Pengguna Baru</div>
            <div class="card-body">
                @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <strong>Mohon perbaiki kesalahan berikut:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('profil.users.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">NIP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nip') is-invalid @enderror"
                                   name="nip" value="{{ old('nip') }}" maxlength="20"
                                   placeholder="18 digit NIP" required>
                            @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email Dinas <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}"
                                   placeholder="nama@dpd.go.id" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Jabatan</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror"
                                   name="position" value="{{ old('position') }}">
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Unit Kerja</label>
                            <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                   name="unit" value="{{ old('unit') }}">
                            @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" name="role" required>
                                <option value="">-- Pilih Peran --</option>
                                <option value="pegawai" {{ old('role') === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="manajemen" {{ old('role') === 'manajemen' ? 'selected' : '' }}>Manajemen</option>
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" placeholder="Min. 8 karakter" required minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password_confirmation"
                                   placeholder="Ulangi kata sandi" required>
                        </div>
                        <div class="col-12 d-flex gap-2 mt-2">
                            <button type="submit" class="btn btn-gold px-4">
                                <i class="bi bi-person-check me-2"></i>Buat Akun
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
