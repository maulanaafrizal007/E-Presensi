<?php $cfg = require BASE_PATH . '/app/config.php'; ?>
<div class="page-header">
  <div><h1>Profil Pengguna</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Profil</li></ol></nav>
  </div>
</div>

<?php if ($ssoMode === 'dev'): ?>
<div class="sso-dev-banner mb-3">
  <i class="bi bi-cone-striped me-1"></i><strong>Mode Pengembangan:</strong>
  Profil ini bersumber dari akun demo lokal. Pada produksi, data diambil otomatis dari SSO institusional DPD RI.
</div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-md-4">
    <div class="card text-center">
      <div class="card-body py-4">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--navy);color:#fff;font-size:2rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem"><?= strtoupper(mb_substr($user['name'],0,2)) ?></div>
        <h5 class="mb-1" style="color:var(--navy)"><?= e($user['name']) ?></h5>
        <p class="text-muted mb-2" style="font-size:.85rem"><?= e($user['position']??'Belum diatur') ?></p>
        <span class="role-pill <?= e($user['role']) ?>"><?= role_label($user['role']) ?></span>
        <hr>
        <form method="POST" action="<?= url('logout') ?>">
          <input type="hidden" name="_token" value="<?= csrf_token() ?>">
          <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-2"></i>Keluar dari Sistem</button>
        </form>
      </div>
    </div>
    <div class="card mt-3">
      <div class="card-header"><i class="bi bi-shield-check me-2"></i>Informasi Akun</div>
      <div class="card-body">
        <table class="table table-sm mb-0" style="font-size:.82rem">
          <tr><td class="text-muted">Status</td><td><?= $user['is_active']?'<span class="badge-status badge-selesai">Aktif</span>':'<span class="badge-status badge-tolak">Nonaktif</span>' ?></td></tr>
          <tr><td class="text-muted">Login Terakhir</td><td><?= $user['last_login_at'] ? date('d M Y H:i',strtotime($user['last_login_at'])) : '-' ?></td></tr>
          <tr><td class="text-muted">Akun Dibuat</td><td><?= date('d M Y',strtotime($user['created_at'])) ?></td></tr>
          <tr><td class="text-muted">Autentikasi</td><td><span class="<?= $ssoMode==='dev'?'text-warning':'text-success' ?> small"><?= $ssoMode==='dev'?'Dev Mode':'SSO Institusional' ?></span></td></tr>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-8">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-person-badge me-2"></i>Data Kepegawaian <span class="text-muted small ms-2"><i class="bi bi-lock me-1"></i>Dikelola oleh SSO</span></div>
      <div class="card-body">
        <div class="row g-3">
          <?php foreach ([['Nama Lengkap','name'],['NIP','nip'],['Jabatan','position'],['Unit Kerja','unit'],['Email Dinas','email']] as [$lbl,$field]): ?>
          <div class="col-<?= in_array($field,['email','unit'])?'12':'sm-6' ?>">
            <label class="form-label text-muted small"><?= $lbl ?></label>
            <div class="form-control bg-light" style="cursor:not-allowed"><?= e($user[$field]??'-') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="mt-3 p-2 rounded" style="background:#f0f4ff;border:1px solid #c7d7f8;font-size:.78rem;color:#3b5bdb">
          <i class="bi bi-info-circle me-1"></i>Data diambil dari SSO Sekretariat Jenderal DPD RI dan tidak dapat diubah secara manual. Hubungi Biro Kepegawaian untuk perubahan data.
        </div>
      </div>
    </div>

    <?php if ($myStats): ?>
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-graph-up me-2"></i>Ringkasan Pengaduan Saya</div>
      <div class="card-body">
        <div class="row g-3">
          <?php foreach ([['Total',$myStats['total'],'var(--navy)'],['Aktif',$myStats['aktif'],'#d97706'],['Selesai',$myStats['selesai'],'#15803d'],['Ditolak',$myStats['ditolak'],'#dc2626']] as [$lbl,$val,$clr]): ?>
          <div class="col-6 col-sm-3 text-center">
            <div style="font-size:1.5rem;font-weight:700;color:<?= $clr ?>"><?= $val ?></div>
            <div class="text-muted small"><?= $lbl ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="mt-3 text-center"><a href="<?= url('pengaduan') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-clipboard2-list me-1"></i>Lihat Semua Pengaduan</a></div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($userCounts): ?>
    <div class="card">
      <div class="card-header"><i class="bi bi-people me-2"></i>Manajemen Pengguna</div>
      <div class="card-body">
        <div class="row g-2 mb-3">
          <?php foreach ([['Total',$userCounts['total'],'var(--navy)'],['Pegawai',$userCounts['pegawai'],'#a0782a'],['Admin',$userCounts['admin'],'#2563eb'],['Manajemen',$userCounts['manajemen'],'#7c3aed']] as [$lbl,$val,$clr]): ?>
          <div class="col-6 col-sm-3 text-center p-2 border rounded mx-1">
            <div style="font-size:1.3rem;font-weight:700;color:<?= $clr ?>"><?= $val ?></div>
            <div class="text-muted small"><?= $lbl ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="d-flex gap-2">
          <a href="<?= url('profil/pengguna') ?>" class="btn btn-sm btn-navy"><i class="bi bi-people me-1"></i>Kelola Pengguna</a>
          <a href="<?= url('profil/pengguna/buat') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
