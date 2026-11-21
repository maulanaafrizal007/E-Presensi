<div class="page-header">
  <div>
    <h1>Dashboard Manajemen</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
  </div>
  <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary"><i class="bi bi-clipboard2-data me-1"></i>Lihat Laporan</a>
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
      <div class="stat-icon success"><i class="bi bi-graph-up-arrow"></i></div>
      <div><div class="stat-label">Tingkat Penyelesaian</div><div class="stat-value"><?= $stats['resolution_rate'] ?>%</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon warning"><i class="bi bi-hourglass-split"></i></div>
      <div><div class="stat-label">Pengaduan Aktif</div><div class="stat-value"><?= $stats['pending'] ?></div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon danger"><i class="bi bi-exclamation-triangle"></i></div>
      <div><div class="stat-label">Melewati Batas Waktu</div><div class="stat-value"><?= $stats['overdue'] ?></div></div>
    </div>
  </div>
</div>

<?php if ($stats['total'] > 0): ?>
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-speedometer me-2"></i>Tingkat Penyelesaian Keseluruhan</div>
  <div class="card-body">
    <div class="d-flex align-items-center gap-3 mb-2">
      <div style="flex:1"><div style="background:#e5e7eb;border-radius:8px;height:16px;overflow:hidden"><div style="background:linear-gradient(90deg,var(--navy) 0%,#22c55e 100%);height:100%;width:<?= $stats['resolution_rate'] ?>%;border-radius:8px"></div></div></div>
      <div style="font-size:1.25rem;font-weight:700;color:var(--navy);min-width:60px;text-align:right"><?= $stats['resolution_rate'] ?>%</div>
    </div>
    <div class="d-flex gap-4" style="font-size:.78rem;color:#64748b">
      <span>Total: <strong><?= $stats['total'] ?></strong></span>
      <span>Diselesaikan: <strong><?= round($stats['total']*$stats['resolution_rate']/100) ?></strong></span>
      <span>Aktif: <strong><?= $stats['pending'] ?></strong></span>
      <span>Terlambat: <strong class="text-danger"><?= $stats['overdue'] ?></strong></span>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-graph-up me-2"></i>Tren Pengaduan Bulanan (6 Bulan Terakhir)</div>
      <div class="card-body">
        <?php $maxVal = max(array_column($monthlyStats,'total') ?: [1]); ?>
        <div style="display:flex;align-items:flex-end;gap:8px;height:120px;padding:.5rem 0">
          <?php foreach ($monthlyStats as $m): ?>
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
            <div style="font-size:.65rem;color:var(--muted)"><?= $m['total'] ?></div>
            <div style="width:100%;background:var(--navy);opacity:.75;border-radius:4px 4px 0 0;min-height:4px;height:<?= max(4,$m['total']/$maxVal*100) ?>px"></div>
            <div style="font-size:.6rem;color:var(--muted);text-align:center;line-height:1.2"><?= e($m['label']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-building me-2"></i>Distribusi per Unit Kerja</div>
      <div class="card-body">
        <?php if (empty($unitStats)): ?><p class="text-muted small">Belum ada data.</p>
        <?php else:
          $unitMax = max(array_column($unitStats,'total') ?: [1]);
          foreach ($unitStats as $u): ?>
        <div class="mb-2">
          <div class="d-flex justify-content-between" style="font-size:.78rem;margin-bottom:3px">
            <span><?= e(str_limit($u['unit_kerja'],35)) ?></span>
            <span class="fw-600"><?= $u['total'] ?></span>
          </div>
          <div style="background:#e5e7eb;border-radius:4px;height:6px"><div style="background:var(--navy);opacity:.7;border-radius:4px;height:6px;width:<?= round($u['total']/$unitMax*100) ?>%"></div></div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>
