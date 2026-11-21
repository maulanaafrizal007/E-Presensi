<?php $errors=$errors??[]; ?>
<div class="page-header">
  <div><h1>Tambah Pengguna Baru</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('profil') ?>">Profil</a></li>
      <li class="breadcrumb-item"><a href="<?= url('profil/pengguna') ?>">Manajemen Pengguna</a></li>
      <li class="breadcrumb-item active">Tambah</li>
    </ol></nav>
  </div>
</div>
<div class="row g-3"><div class="col-12 col-lg-7">
  <div class="card">
    <div class="card-header"><i class="bi bi-person-plus me-2"></i>Formulir Pengguna Baru</div>
    <div class="card-body">
      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger mb-3"><strong>Mohon perbaiki:</strong><ul class="mb-0 mt-1"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul></div>
      <?php endif; ?>
      <form method="POST" action="<?= url('profil/pengguna/buat') ?>">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <div class="row g-3">
          <div class="col-sm-6"><label class="form-label">NIP <span class="text-danger">*</span></label><input type="text" class="form-control <?= isset($errors['nip'])?'is-invalid':'' ?>" name="nip" value="<?= old('nip') ?>" maxlength="20" required><?php if(isset($errors['nip'])):?><div class="invalid-feedback"><?= e($errors['nip']) ?></div><?php endif;?></div>
          <div class="col-sm-6"><label class="form-label">Nama Lengkap <span class="text-danger">*</span></label><input type="text" class="form-control <?= isset($errors['name'])?'is-invalid':'' ?>" name="name" value="<?= old('name') ?>" required><?php if(isset($errors['name'])):?><div class="invalid-feedback"><?= e($errors['name']) ?></div><?php endif;?></div>
          <div class="col-12"><label class="form-label">Email Dinas <span class="text-danger">*</span></label><input type="email" class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>" name="email" value="<?= old('email') ?>" placeholder="nama@dpd.go.id" required><?php if(isset($errors['email'])):?><div class="invalid-feedback"><?= e($errors['email']) ?></div><?php endif;?></div>
          <div class="col-sm-6"><label class="form-label">Jabatan</label><input type="text" class="form-control" name="position" value="<?= old('position') ?>"></div>
          <div class="col-sm-6"><label class="form-label">Unit Kerja</label><input type="text" class="form-control" name="unit" value="<?= old('unit') ?>"></div>
          <div class="col-sm-6">
            <label class="form-label">Peran (Role) <span class="text-danger">*</span></label>
            <select class="form-select <?= isset($errors['role'])?'is-invalid':'' ?>" name="role" required>
              <option value="">-- Pilih Peran --</option>
              <?php foreach (['pegawai'=>'Pegawai','admin'=>'Administrator','manajemen'=>'Manajemen'] as $k=>$lbl): ?>
              <option value="<?= $k ?>" <?= old('role')===$k?'selected':'' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
            <?php if(isset($errors['role'])):?><div class="invalid-feedback"><?= e($errors['role']) ?></div><?php endif;?>
          </div>
          <div class="col-sm-6"><label class="form-label">Kata Sandi <span class="text-danger">*</span></label><input type="password" class="form-control <?= isset($errors['password'])?'is-invalid':'' ?>" name="password" placeholder="Min. 8 karakter" required minlength="8"><?php if(isset($errors['password'])):?><div class="invalid-feedback"><?= e($errors['password']) ?></div><?php endif;?></div>
          <div class="col-sm-6"><label class="form-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label><input type="password" class="form-control" name="password_confirmation" required></div>
          <div class="col-12 d-flex gap-2 mt-2">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-person-check me-2"></i>Buat Akun</button>
            <a href="<?= url('profil/pengguna') ?>" class="btn btn-outline-secondary">Batal</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div></div>
