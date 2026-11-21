<div class="page-header">
  <div>
    <h1>Dashboard Administrator</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Dashboard</li></ol></nav>
  </div>
  <a href="<?= url('pengaduan') ?>" class="btn btn-navy"><i class="bi bi-clipboard2-list me-1"></i>Kelola Pengaduan</a>
</div>

<div class="row g-3 mb-3">
  <?php
  $statItems = [
    ['navy','collection','Total Pengaduan',$stats['total']],
    ['warning','inbox-fill','Pengaduan Baru',$stats['baru']],
    ['purple','search','Menunggu Verifikasi',$stats['menunggu_verifikasi']],
    ['info','arrow-repeat','Sedang Diproses',$stats['diproses']],
    ['gold','question-circle','Menunggu Info',$stats['menunggu_informasi']],
    ['success','check-circle','Diselesaikan',$stats['diselesaikan']],
    ['danger','exclamation-triangle','Melewati Batas',$stats['overdue']],
    ['navy','percent','Tingkat Selesai',($stats['total']>0?round($stats['diselesaikan']/$stats['total']*100).'%':'0%')],
  ];
  foreach ($statItems as [$color,$icon,$label,$val]): ?>
  <div class="col-6 col-lg-3">
    <div class="stat-card">
      <div class="stat-icon <?= $color ?>"><i class="bi bi-<?= $icon ?>"></i></div>
      <div><div class="stat-label"><?= $label ?></div><div class="stat-value"><?= $val ?></div></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Statistik Pengaduan 6 Bulan Terakhir</div>
      <div class="card-body">
        <?php $maxVal = max(array_column($monthlyStats,'total') ?: [1]); ?>
        <div style="display:flex;align-items:flex-end;gap:6px;height:100px;padding:.5rem 0">
          <?php foreach ($monthlyStats as $m): ?>
          <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px">
            <div style="display:flex;gap:2px;align-items:flex-end;width:100%;height:80px">
              <div style="flex:1;background:var(--navy);opacity:.7;border-radius:4px 4px 0 0;min-height:4px;height:<?= max(4,$m['total']/$maxVal*80) ?>px" title="Total: <?= $m['total'] ?>"></div>
              <div style="flex:1;background:#22c55e;opacity:.8;border-radius:4px 4px 0 0;min-height:4px;height:<?= max(4,$m['total']>0?$m['selesai']/$maxVal*80:0) ?>px" title="Selesai: <?= $m['selesai'] ?>"></div>
            </div>
            <div style="font-size:.6rem;color:var(--muted);text-align:center;line-height:1.2"><?= e($m['label']) ?><br><small><?= $m['total'] ?></small></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="d-flex gap-3 mt-2">
          <span style="font-size:.72rem"><span style="display:inline-block;width:10px;height:10px;background:var(--navy);opacity:.7;border-radius:2px;margin-right:4px"></span>Total</span>
          <span style="font-size:.72rem"><span style="display:inline-block;width:10px;height:10px;background:#22c55e;opacity:.8;border-radius:2px;margin-right:4px"></span>Diselesaikan</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Distribusi Kategori</div>
      <div class="card-body">
        <?php
        $colors = ['#1a3561','#c9a84c','#3b82f6','#22c55e','#f59e0b','#8b5cf6'];
        $catTotal = array_sum(array_column($categoryStats,'total')) ?: 1;
        foreach ($categoryStats as $i => $cat): ?>
        <div class="d-flex align-items-center gap-2 mb-2">
          <div style="width:12px;height:12px;border-radius:3px;background:<?= $colors[$i%count($colors)] ?>;flex-shrink:0"></div>
          <div style="flex:1;font-size:.8rem"><?= e(kategori_label($cat['kategori'])) ?></div>
          <div style="font-size:.8rem;font-weight:600"><?= $cat['total'] ?></div>
          <div style="width:80px"><div style="background:#e5e7eb;border-radius:4px;height:6px"><div style="background:<?= $colors[$i%count($colors)] ?>;border-radius:4px;height:6px;width:<?= round($cat['total']/$catTotal*100) ?>%"></div></div></div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($categoryStats)): ?><p class="text-muted small">Belum ada data.</p><?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-inbox me-2"></i>Pengaduan Masuk Terbaru</span>
    <a href="<?= url('pengaduan') ?>" class="btn btn-sm btn-navy">Kelola Semua</a>
  </div>
  <div class="card-body p-0">
    <?php if (empty($recent)): ?>
    <div class="empty-state"><div class="empty-icon"><i class="bi bi-inbox"></i></div><h6>Belum ada pengaduan</h6></div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>No. Tiket</th><th>Pegawai</th><th>Unit Kerja</th><th>Kategori</th><th>Tgl Diajukan</th><th>Status</th><th>Ditangani</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $p): ?>
          <tr class="<?= $p['is_overdue'] && !in_array($p['status'],['diselesaikan','ditutup','ditolak']) ? 'table-warning' : '' ?>">
            <td><code><?= e($p['nomor_tiket']) ?></code></td>
            <td style="font-size:.82rem"><div class="fw-500"><?= e($p['nama_pegawai']) ?></div><div class="text-muted" style="font-size:.7rem"><?= e($p['nip']) ?></div></td>
            <td style="font-size:.8rem"><?= e(str_limit($p['unit_kerja'],25)) ?></td>
            <td style="font-size:.8rem"><?= e(kategori_label($p['kategori'])) ?></td>
            <td style="font-size:.8rem"><?= date('d M Y',strtotime($p['created_at'])) ?></td>
            <td><span class="badge-status <?= status_badge($p['status']) ?>"><?= status_label($p['status']) ?></span></td>
            <td style="font-size:.8rem"><?= e($p['handler_name'] ?? '-') ?></td>
            <td><a href="<?= url('pengaduan/'.$p['id']) ?>" class="btn btn-sm btn-navy"><i class="bi bi-arrow-right"></i></a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>
