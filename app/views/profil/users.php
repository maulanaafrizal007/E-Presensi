<div class="page-header">
  <div><h1>Manajemen Pengguna</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('profil') ?>">Profil</a></li>
      <li class="breadcrumb-item active">Manajemen Pengguna</li>
    </ol></nav>
  </div>
  <a href="<?= url('profil/pengguna/buat') ?>" class="btn btn-gold"><i class="bi bi-person-plus me-1"></i>Tambah Pengguna</a>
</div>

<div class="card">
  <div class="card-header"><i class="bi bi-people me-2"></i>Daftar Pengguna Sistem <span class="text-muted small ms-2">(<?= $total ?> pengguna)</span></div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light">
          <tr><th>Nama / NIP</th><th>Email</th><th>Jabatan</th><th>Unit Kerja</th><th>Peran</th><th>Status</th><th>Login Terakhir</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td><div class="fw-500" style="font-size:.85rem"><?= e($u['name']) ?></div><div class="text-muted" style="font-size:.72rem"><?= e($u['nip']) ?></div></td>
            <td style="font-size:.82rem"><?= e($u['email']) ?></td>
            <td style="font-size:.82rem"><?= e($u['position']??'-') ?></td>
            <td style="font-size:.82rem"><?= e(str_limit($u['unit']??'',30)) ?></td>
            <td><span class="role-pill <?= e($u['role']) ?>"><?= role_label($u['role']) ?></span></td>
            <td><?= $u['is_active']?'<span class="badge-status badge-selesai">Aktif</span>':'<span class="badge-status badge-tolak">Nonaktif</span>' ?></td>
            <td style="font-size:.78rem;color:#64748b"><?= $u['last_login_at'] ? date('d M Y',strtotime($u['last_login_at'])) : 'Belum pernah' ?></td>
            <td>
              <div class="d-flex gap-1">
                <a href="<?= url('profil/pengguna/'.$u['id'].'/edit') ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <?php if ($u['id'] != Auth::id()): ?>
                <form method="POST" action="<?= url('profil/pengguna/'.$u['id'].'/toggle') ?>" class="d-inline">
                  <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                  <button type="submit" class="btn btn-sm <?= $u['is_active']?'btn-outline-warning':'btn-outline-success' ?>"
                          onclick="return confirm('Yakin ingin <?= $u['is_active']?'menonaktifkan':'mengaktifkan' ?> akun <?= e($u['name']) ?>?')"
                          title="<?= $u['is_active']?'Nonaktifkan':'Aktifkan' ?>">
                    <i class="bi bi-<?= $u['is_active']?'slash-circle':'check-circle' ?>"></i>
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if ($pages > 1): ?>
    <div class="card-footer d-flex gap-2">
      <?php for ($i=1;$i<=$pages;$i++): ?>
      <a href="<?= url('profil/pengguna') ?>?page=<?= $i ?>" class="btn btn-sm <?= $i===$page?'btn-navy':'btn-outline-secondary' ?>"><?= $i ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
