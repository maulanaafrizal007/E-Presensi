@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Profil')

@section('content')
<div class="page-header">
    <div>
        <h1>Manajemen Pengguna</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('profil.index') }}">Profil</a></li>
                <li class="breadcrumb-item active">Manajemen Pengguna</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('profil.users.create') }}" class="btn btn-gold">
        <i class="bi bi-person-plus me-1"></i>Tambah Pengguna
    </a>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-people me-2"></i>Daftar Pengguna Sistem
        <span class="text-muted small ms-2">({{ $users->total() }} pengguna)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama / NIP</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Unit Kerja</th>
                        <th>Peran</th>
                        <th>Status</th>
                        <th>Login Terakhir</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <div class="fw-500" style="font-size:.85rem;">{{ $u->name }}</div>
                            <div class="text-muted" style="font-size:.72rem;">{{ $u->nip }}</div>
                        </td>
                        <td style="font-size:.82rem;">{{ $u->email }}</td>
                        <td style="font-size:.82rem;">{{ $u->position ?? '-' }}</td>
                        <td style="font-size:.82rem;">{{ Str::limit($u->unit, 30) }}</td>
                        <td>
                            <span class="role-pill {{ $u->role }}">
                                {{ match($u->role) {
                                    'admin' => 'Admin',
                                    'manajemen' => 'Manajemen',
                                    default => 'Pegawai'
                                } }}
                            </span>
                        </td>
                        <td>
                            @if($u->is_active)
                                <span class="badge-status badge-selesai">Aktif</span>
                            @else
                                <span class="badge-status badge-tolak">Nonaktif</span>
                            @endif
                        </td>
                        <td style="font-size:.78rem;color:#64748b;">
                            {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('profil.users.edit', $u) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('profil.users.toggle-status', $u) }}"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            onclick="return confirm('Yakin ingin {{ $u->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun {{ $u->name }}?')">
                                        <i class="bi bi-{{ $u->is_active ? 'slash-circle' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
