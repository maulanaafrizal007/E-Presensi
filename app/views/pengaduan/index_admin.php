<div class="page-header">
  <div>
    <h1>Kelola Pengaduan Presensi</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Pengaduan Presensi</li>
    </ol></nav>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET">
      <div class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
          <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" name="search" placeholder="Cari nama, NIP, nomor tiket..." value="<?= e($filters['search']) ?>">
          </div>
        </div>
        <div class="col-6 col-md-2">
          <select name="status" class="form-select form-select-sm">
            <option value="">Semua Status</option>
            <?php foreach (['diajukan','menunggu_verifikasi','diverifikasi','diproses','menunggu_informasi','diselesaikan','ditutup','ditolak'] as $s): ?>
            <option value="<?= $s ?>" <?= ($filters['status']===$s)?'selected':'' ?>><?= status_label($s) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2">
          <select name="kategori" class="form-select form-select-sm">
            <option value="">Semua Kategori</option>
            <?php foreach (['terlambat','pulang_cepat','tidak_tercatat','lupa_absen','gangguan_sistem','lainnya'] as $k): ?>
            <option value="<?= $k ?>" <?= ($filters['kategori']===$k)?'selected':'' ?>><?= kategori_label($k) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-6 col-md-2"><input type="date" class="form-control form-control-sm" name="tanggal_dari" value="<?= e($filters['dari']) ?>"></div>
        <div class="col-6 col-md-2 d-flex gap-1">
          <input type="date" class="form-control form-control-sm" name="tanggal_sampai" value="<?= e($filters['sampai']) ?>">
          <button type="submit" class="btn btn-sm btn-navy flex-shrink-0"><i class="bi bi-funnel"></i></button>
          <?php if (array_filter($filters)): ?>
          <a href="<?= url('pengaduan') ?>" class="btn btn-sm btn-outline-secondary flex-shrink-0"><i class="bi bi-x"></i></a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span><i class="bi bi-inbox me-2"></i>Daftar Pengaduan Masuk</span>
    <span class="text-muted small"><?= $total ?> pengaduan ditemukan</span>
  </div>
  <div class="card-body p-0">
    <?php if (empty($rows)): ?>
    <div class="empty-state"><div class="empty-icon"><i class="bi bi-inbox"></i></div><h6>Tidak ada pengaduan</h6></div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>No. Tiket</th><th>Pegawai / NIP</th><th>Unit Kerja</th><th>Kategori</th><th>Tgl Diajukan</th><th>Status</th><th>Ditangani</th><th>Batas Waktu</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $p): $ov = $p['is_overdue'] && !in_array($p['status'],['diselesaikan','ditutup','ditolak']); ?>
          <tr class="<?= $ov?'table-warning':'' ?>">
            <td>
              <code><?= e($p['nomor_tiket']) ?></code>
              <?php if ($ov): ?><br><span class="badge-status badge-tolak" style="font-size:.6rem">Melewati Batas</span><?php endif; ?>
            </td>
            <td><div class="fw-500" style="font-size:.82rem"><?= e($p['nama_pegawai']) ?></div><div class="text-muted" style="font-size:.7rem"><?= e($p['nip']) ?></div></td>
            <td style="font-size:.8rem"><?= e(str_limit($p['unit_kerja'],30)) ?></td>
            <td style="font-size:.8rem"><?= e(kategori_label($p['kategori'])) ?></td>
            <td style="font-size:.8rem"><?= date('d M Y',strtotime($p['created_at'])) ?></td>
            <td><span class="badge-status <?= status_badge($p['status']) ?>"><?= status_label($p['status']) ?></span></td>
            <td style="font-size:.8rem"><?= e($p['handler_name']??'-') ?></td>
            <td style="font-size:.8rem">
              <?php if ($p['batas_waktu']): ?>
              <span class="<?= (strtotime($p['batas_waktu'])<time() && !in_array($p['status'],['diselesaikan','ditutup','ditolak']))?'text-danger fw-600':'text-muted' ?>">
                <?= date('d M Y',strtotime($p['batas_waktu'])) ?>
              </span>
              <?php else: ?>-<?php endif; ?>
            </td>
            <td><a href="<?= url('pengaduan/'.$p['id']) ?>" class="btn btn-sm btn-navy"><i class="bi bi-arrow-right me-1"></i>Tangani</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($pages > 1): ?>
    <div class="card-footer d-flex gap-2">
      <?php for ($i=1;$i<=$pages;$i++): $q=http_build_query(array_merge($filters,['page'=>$i])); ?>
      <a href="<?= url('pengaduan') ?>?<?= $q ?>" class="btn btn-sm <?= $i===$page?'btn-navy':'btn-outline-secondary' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
