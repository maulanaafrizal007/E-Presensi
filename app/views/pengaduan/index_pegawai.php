<?php $user = Auth::user(); ?>
<div class="page-header">
  <div>
    <h1>Pengaduan Presensi Saya</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item active">Pengaduan Presensi</li>
    </ol></nav>
  </div>
  <a href="<?= url('pengaduan/buat') ?>" class="btn btn-gold"><i class="bi bi-plus-circle me-1"></i>Buat Pengaduan Baru</a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-5 col-md-4">
        <select name="status" class="form-select form-select-sm">
          <option value="">Semua Status</option>
          <?php foreach (['diajukan','menunggu_verifikasi','diverifikasi','diproses','menunggu_informasi','diselesaikan','ditutup','ditolak'] as $s): ?>
          <option value="<?= $s ?>" <?= ($filters['status']===$s)?'selected':'' ?>><?= status_label($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-5 col-md-4">
        <select name="kategori" class="form-select form-select-sm">
          <option value="">Semua Kategori</option>
          <?php foreach (['terlambat','pulang_cepat','tidak_tercatat','lupa_absen','gangguan_sistem','lainnya'] as $k): ?>
          <option value="<?= $k ?>" <?= ($filters['kategori']===$k)?'selected':'' ?>><?= kategori_label($k) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-2 col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-sm btn-navy">Filter</button>
        <?php if ($filters['status']||$filters['kategori']): ?>
        <a href="<?= url('pengaduan') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header"><i class="bi bi-list-ul me-2"></i>Daftar Pengaduan <span class="text-muted ms-1 small">(<?= $total ?> pengaduan)</span></div>
  <div class="card-body p-0">
    <?php if (empty($rows)): ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-clipboard2-x"></i></div>
      <h6>Tidak ada pengaduan</h6>
      <a href="<?= url('pengaduan/buat') ?>" class="btn btn-sm btn-navy mt-2"><i class="bi bi-plus me-1"></i>Ajukan Pengaduan</a>
    </div>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>No. Tiket</th><th>Subjek</th><th>Kategori</th><th>Tgl Kejadian</th><th>Tgl Diajukan</th><th>Status</th><th>Respons Terakhir</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $p): ?>
          <tr>
            <td><code class="text-primary"><?= e($p['nomor_tiket']) ?></code></td>
            <td>
              <div class="fw-500" style="font-size:.85rem"><?= e(str_limit($p['subjek'],40)) ?></div>
              <div class="text-muted" style="font-size:.72rem"><?= jenis_label($p['jenis_presensi']) ?></div>
            </td>
            <td style="font-size:.82rem"><?= e(kategori_label($p['kategori'])) ?></td>
            <td style="font-size:.82rem"><?= date('d M Y',strtotime($p['tanggal_kejadian'])) ?></td>
            <td style="font-size:.82rem"><?= date('d M Y',strtotime($p['created_at'])) ?></td>
            <td><span class="badge-status <?= status_badge($p['status']) ?>"><?= status_label($p['status']) ?></span></td>
            <td style="font-size:.78rem;max-width:200px">
              <?php if ($p['last_response']): ?>
              <div class="text-truncate"><?= e(str_limit($p['last_response'],50)) ?></div>
              <div class="text-muted" style="font-size:.7rem"><?= $p['last_response_at'] ? date('d M Y',strtotime($p['last_response_at'])) : '' ?></div>
              <?php else: ?><span class="text-muted">-</span><?php endif; ?>
            </td>
            <td><a href="<?= url('pengaduan/'.$p['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Detail</a></td>
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
