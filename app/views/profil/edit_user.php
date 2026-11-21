<?php $errors=$errors??[]; ?>
<div class="page-header">
  <div><h1>Edit Pengguna: <?= e($editUser['name']) ?></h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('profil') ?>">Profil</a></li>
      <li class="breadcrumb-item"><a href="<?= url('profil/pengguna') ?>">Manajemen Pengguna</a></li>
      <li class="breadcrumb-item active">Edit</li>
    </ol></nav>
  </div>
</div>
<div class="row g-3"><div class="col-12 col-lg-7">
  <div class="card">
    <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Edit Data Pengguna</div>
    <div class="card-body">
      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger mb-3"><strong>Mohon perbaiki:</strong><ul class="mb-0 mt-1"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>

      <!-- Identity: read-only -->
      <div class="card bg-light mb-4">
        <div class="card-body py-2">
          <div style="font-size:.78rem;color:#64748b;font-weight:600;text-transform:uppercase;margin-bottom:.5rem"><i class="bi bi-lock me-1"></i>Identitas (Hanya Baca)</div>
          <div class="row g-2">
            <div class="col-sm-6"><div class="text-muted small">Nama Lengkap</div><div class="fw-600"><?= e($editUser['name']) ?></div></div>
            <div class="col-sm-6"><div class="text-muted small">NIP</div><div class="fw-600"><?= e($editUser['nip']) ?></div></div>
            <div class="col-12"><div class="text-muted small">Email</div><div class="fw-600"><?= e($editUser['email']) ?></div></div>
          </div>
        </div>
      </div>

      <form method="POST" action="<?= url('profil/pengguna/'.$editUser['id'].'/edit') ?>">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <div class="row g-3">
          <div class="col-sm-6"><label class="form-label">Jabatan</label><input type="text" class="form-control" name="position" value="<?= e($editUser['position']??'') ?>"></div>
          <div class="col-sm-6"><label class="form-label">Unit Kerja</label><input type="text" class="form-control" name="unit" value="<?= e($editUser['unit']??'') ?>"></div>
          <div class="col-sm-6">
            <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
            <select class="form-select" name="role" required>
              <?php foreach (['pegawai'=>'Pegawai','admin'=>'Administrator','manajemen'=>'Manajemen'] as $k=>$lbl): ?>
              <option value="<?= $k ?>" <?= $editUser['role']===$k?'selected':'' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-sm-6">
            <label class="form-label">Status Akun</label>
            <div class="mt-2">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" <?= $editUser['is_active']?'checked':'' ?> <?= $editUser['id']==Auth::id()?'disabled':'' ?>>
                <label class="form-check-label" for="is_active">Akun Aktif</label>
              </div>
            </div>
          </div>
          <div class="col-12"><hr><div class="text-muted small mb-2">Ubah Kata Sandi (kosongkan jika tidak ingin mengubah)</div></div>
          <div class="col-sm-6"><label class="form-label">Kata Sandi Baru</label><input type="password" class="form-control <?= isset($errors['new_password'])?'is-invalid':'' ?>" name="new_password" placeholder="Min. 8 karakter"><?php if(isset($errors['new_password'])):?><div class="invalid-feedback"><?= e($errors['new_password']) ?></div><?php endif;?></div>
          <div class="col-sm-6"><label class="form-label">Konfirmasi Kata Sandi Baru</label><input type="password" class="form-control" name="new_password_confirmation"></div>
          <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-navy px-4"><i class="bi bi-save me-2"></i>Simpan Perubahan</button>
            <a href="<?= url('profil/pengguna') ?>" class="btn btn-outline-secondary">Batal</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div></div>
