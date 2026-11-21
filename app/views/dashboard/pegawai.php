<?php $u = Auth::user(); ?>
<div class="page-header">
  <div>
    <h1>Selamat Datang, <?= e($u['name']) ?></h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
  </div>
  <a href="<?= url('pengaduan/buat') ?>" class="btn btn-gold"><i class="bi bi-plus-circle me-1"></i>Buat Pengaduan</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon navy"><i class="bi bi-collection"></i></div>
      <div><div class="stat-label">Total Pengaduan</div><div class="stat-value"><?= $stats['total'] ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon warning"><i class="bi bi-hourglass-split"></i></div>
      <div><div class="stat-label">Menunggu</div><div class="stat-value"><?= $stats['diajukan'] ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon info"><i class="bi bi-arrow-repeat"></i></div>
      <div><div class="stat-label">Sedang Diproses</div><div class="stat-value"><?= $stats['diproses'] ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon success"><i class="bi bi-check-circle"></i></div>
      <div><div class="stat-label">Diselesaikan</div><div class="stat-value"><?= $stats['diselesaikan'] ?></div></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-clock-history me-2"></i>Riwayat Pengaduan Terbaru</span>
    <a href="<?= url('pengaduan') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
  </div>
  <div class="card-body p-0">
    <?php if (empty($recent)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-inbox"></i></div>
      <h6>Belum ada pengaduan</h6>
      <p class="small">Anda belum pernah mengajukan pengaduan presensi.</p>
      <a href="<?= url('pengaduan/buat') ?>" class="btn btn-sm btn-navy"><i class="bi bi-plus me-1"></i>Buat Pengaduan Pertama</a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>No. Tiket</th><th>Subjek</th><th>Kategori</th><th>Tgl Diajukan</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $p): ?>
          <tr>
            <td><code class="text-primary"><?= e($p['nomor_tiket']) ?></code></td>
            <td class="fw-500" style="font-size:.85rem"><?= e(str_limit($p['subjek'],45)) ?></td>
            <td style="font-size:.82rem"><?= e(kategori_label($p['kategori'])) ?></td>
            <td style="font-size:.82rem"><?= date('d M Y', strtotime($p['created_at'])) ?></td>
            <td><span class="badge-status <?= status_badge($p['status']) ?>"><?= status_label($p['status']) ?></span></td>
            <td><a href="<?= url('pengaduan/'.$p['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
