<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — SIKAP DPD RI</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{--navy:#1a3561;--navy-dark:#0f2040;--gold:#c9a84c;--gold-light:#e0c06a}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,var(--navy-dark) 0%,var(--navy) 60%,#2a5298 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem}
.login-card{background:#fff;border-radius:16px;padding:2.5rem 2rem;box-shadow:0 20px 60px rgba(0,0,0,.35);width:100%;max-width:440px}
.brand-emblem{width:72px;height:72px;background:linear-gradient(135deg,var(--navy) 0%,var(--navy-dark) 100%);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:var(--gold);border:3px solid var(--gold)}
.form-control{border:1.5px solid #d1d5db;border-radius:8px;padding:.6rem .9rem}
.form-control:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(26,53,97,.12)}
.btn-login{background:var(--navy);color:#fff;border:none;border-radius:8px;padding:.7rem;font-size:.9rem;font-weight:600;width:100%}
.btn-login:hover{background:var(--navy-dark);color:#fff}
.dev-banner{background:#fef3c7;border:1px solid #f59e0b;border-radius:10px;padding:.9rem 1rem;font-size:.78rem;color:#92400e}
.dev-account-btn{display:flex;align-items:center;gap:.6rem;border:1px solid #d1d5db;border-radius:7px;padding:.45rem .75rem;margin-top:.4rem;background:#fff;cursor:pointer;width:100%;text-align:left;font-size:.78rem}
.dev-account-btn:hover{border-color:var(--navy);background:#f8faff}
.role-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}
.dot-pegawai{background:#c9a84c}.dot-admin{background:#3b82f6}.dot-manajemen{background:#8b5cf6}
</style>
</head>
<body>
<div class="login-card">
  <div class="text-center mb-4">
    <div class="brand-emblem mb-2">S</div>
    <h1 class="h4 fw-800" style="color:var(--navy)">SIKAP DPD RI</h1>
    <p class="text-muted small mb-0">Sistem Informasi Keluhan Administrasi Presensi<br>Sekretariat Jenderal DPD RI</p>
  </div>

  <?php if ($devMode): ?>
  <div class="dev-banner mb-3">
    <strong><i class="bi bi-cone-striped me-1"></i>Mode Pengembangan</strong><br>
    SSO institusional belum dikonfigurasi. Klik akun demo di bawah atau masukkan NIP &amp; kata sandi manual.
    <div class="mt-2">
      <?php foreach ($devAccounts as $acc): ?>
      <button type="button" class="dev-account-btn"
              onclick="document.getElementById('nip').value='<?= e($acc['nip']) ?>';document.getElementById('password').value='<?= e($acc['password']) ?>'">
        <span class="role-dot dot-<?= strtolower($acc['role']) === 'administrator' ? 'admin' : strtolower($acc['role']) ?>"></span>
        <span>
          <span class="fw-600" style="color:#1e2d3d"><?= e($acc['name']) ?></span><br>
          <span class="text-muted"><?= e($acc['role']) ?> · <?= e($acc['nip']) ?></span>
        </span>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($error): ?>
  <div class="alert alert-danger mb-3 py-2">
    <i class="bi bi-exclamation-circle me-1"></i><?= e($error) ?>
  </div>
  <?php endif; ?>

  <?php if ($s = flash('success')): ?>
  <div class="alert alert-success mb-3 py-2"><i class="bi bi-check-circle me-1"></i><?= e($s) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= url('login') ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <div class="mb-3">
      <label class="form-label fw-500" for="nip">NIP (Nomor Induk Pegawai)</label>
      <input type="text" class="form-control" id="nip" name="nip"
             value="<?= $nipOld ?>" placeholder="Masukkan NIP Anda" autocomplete="username" required>
    </div>
    <div class="mb-3">
      <label class="form-label fw-500" for="password">Kata Sandi</label>
      <input type="password" class="form-control" id="password" name="password"
             placeholder="Masukkan kata sandi" autocomplete="current-password" required>
    </div>
    <div class="mb-4 form-check">
      <input type="checkbox" class="form-check-input" id="remember">
      <label class="form-check-label small" for="remember">Ingat saya</label>
    </div>
    <button type="submit" class="btn btn-login">
      <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
    </button>
  </form>

  <p class="text-center text-muted mt-3 mb-0" style="font-size:.7rem;">
    &copy; <?= date('Y') ?> Sekretariat Jenderal DPD RI &mdash; SIKAP v1.0
  </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
