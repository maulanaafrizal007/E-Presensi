<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIKAP DPD RI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --navy: #1a3561; --navy-dark: #0f2040;
            --gold: #c9a84c; --gold-light: #e0c06a;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--navy-dark) 0%, var(--navy) 60%, #2a5298 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .login-container {
            width: 100%; max-width: 440px;
            padding: 1rem;
        }
        .login-card {
            background: white; border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }
        .brand-area { text-align: center; margin-bottom: 2rem; }
        .brand-emblem {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%);
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 800; color: var(--gold);
            margin-bottom: 1rem;
            border: 3px solid var(--gold);
        }
        .brand-title { font-size: 1.3rem; font-weight: 800; color: var(--navy); margin: 0; }
        .brand-sub { font-size: .75rem; color: #64748b; margin-top: .2rem; }

        .form-label { font-weight: 500; font-size: .875rem; color: #374151; }
        .form-control {
            border: 1.5px solid #d1d5db; border-radius: 8px; padding: .6rem .9rem;
            font-size: .9rem;
        }
        .form-control:focus {
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(26,53,97,.12);
        }
        .btn-login {
            background: var(--navy); color: white;
            border: none; border-radius: 8px;
            padding: .7rem 1rem; font-size: .9rem; font-weight: 600;
            width: 100%; transition: background .2s;
        }
        .btn-login:hover { background: var(--navy-dark); color: white; }

        .dev-banner {
            background: #fef3c7; border: 1px solid #f59e0b;
            border-radius: 10px; padding: .9rem 1rem; margin-bottom: 1.5rem;
            font-size: .78rem; color: #92400e;
        }
        .dev-banner strong { color: #78350f; }

        .dev-accounts { margin-top: .6rem; }
        .dev-account-btn {
            display: flex; align-items: center; gap: .6rem;
            border: 1px solid #d1d5db; border-radius: 7px;
            padding: .45rem .75rem; margin-top: .4rem;
            background: white; cursor: pointer; width: 100%;
            text-align: left; transition: all .15s;
            font-size: .78rem;
        }
        .dev-account-btn:hover { border-color: var(--navy); background: #f8faff; }
        .dev-account-btn .role-dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }
        .role-dot.pegawai { background: #c9a84c; }
        .role-dot.admin { background: #3b82f6; }
        .role-dot.manajemen { background: #8b5cf6; }
        .dev-account-name { font-weight: 600; color: #1e2d3d; }
        .dev-account-role { color: #64748b; font-size: .72rem; }

        .footer-note {
            text-align: center; font-size: .7rem; color: rgba(255,255,255,.5);
            margin-top: 1.25rem;
        }

        .alert { border-radius: 8px; font-size: .85rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-area">
                <div class="brand-emblem">S</div>
                <h1 class="brand-title">SIKAP DPD RI</h1>
                <p class="brand-sub">Sistem Informasi Keluhan Administrasi Presensi<br>Sekretariat Jenderal DPD RI</p>
            </div>

            @if($ssoMode === 'dev')
                <div class="dev-banner">
                    <strong><i class="bi bi-cone-striped me-1"></i>Mode Pengembangan (Development)</strong><br>
                    Autentikasi SSO institusional belum dikonfigurasi. Gunakan akun demo di bawah atau masukkan NIP &amp; kata sandi secara manual.
                    <div class="dev-accounts">
                        @foreach($devAccounts as $acc)
                            <button type="button" class="dev-account-btn"
                                onclick="fillLogin('{{ $acc['nip'] }}', '{{ $acc['password'] }}')">
                                <span class="role-dot {{ $acc['role'] }}"></span>
                                <span>
                                    <span class="dev-account-name">{{ $acc['name'] }}</span><br>
                                    <span class="dev-account-role">{{ ucfirst($acc['role']) }} &middot; {{ $acc['nip'] }}</span>
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success mb-3">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="nip">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" class="form-control" id="nip" name="nip"
                           value="{{ old('nip') }}" placeholder="Masukkan NIP Anda"
                           autocomplete="username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Kata Sandi</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember" style="font-size:.85rem;">
                        Ingat saya
                    </label>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                </button>
            </form>
        </div>
        <div class="footer-note">
            &copy; {{ date('Y') }} Sekretariat Jenderal DPD RI &mdash; SIKAP v1.0
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fillLogin(nip, password) {
            document.getElementById('nip').value = nip;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
