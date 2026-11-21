<?php $user = Auth::user(); $errors = $errors??[]; ?>
<div class="page-header">
  <div>
    <h1>Ajukan Pengaduan Presensi</h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= url('pengaduan') ?>">Pengaduan Presensi</a></li>
      <li class="breadcrumb-item active">Ajukan Pengaduan</li>
    </ol></nav>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-8">
    <div class="card">
      <div class="card-header"><i class="bi bi-clipboard2-plus me-2"></i>Formulir Pengaduan Presensi</div>
      <div class="card-body">
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3">
          <strong><i class="bi bi-exclamation-circle me-1"></i>Mohon perbaiki kesalahan berikut:</strong>
          <ul class="mb-0 mt-1"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('pengaduan/buat') ?>" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="<?= csrf_token() ?>">

          <!-- SSO info (read-only) -->
          <div class="card bg-light mb-4">
            <div class="card-body py-2">
              <div class="mb-1" style="font-size:.78rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.05em"><i class="bi bi-person-badge me-1"></i>Informasi Pegawai (dari SSO)</div>
              <div class="row g-2">
                <div class="col-sm-6"><div style="font-size:.78rem;color:#64748b">Nama Lengkap</div><div style="font-size:.88rem;font-weight:600"><?= e($user['name']) ?></div></div>
                <div class="col-sm-6"><div style="font-size:.78rem;color:#64748b">NIP</div><div style="font-size:.88rem;font-weight:600"><?= e($user['nip']) ?></div></div>
                <div class="col-12"><div style="font-size:.78rem;color:#64748b">Unit Kerja</div><div style="font-size:.88rem;font-weight:600"><?= e($user['unit']??'-') ?></div></div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Kategori Pengaduan <span class="text-danger">*</span></label>
            <select class="form-select <?= isset($errors['kategori'])?'is-invalid':'' ?>" name="kategori" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach (['terlambat'=>'Keterlambatan','pulang_cepat'=>'Pulang Lebih Awal','tidak_tercatat'=>'Presensi Tidak Tercatat','lupa_absen'=>'Lupa Absen','gangguan_sistem'=>'Gangguan Sistem','lainnya'=>'Lainnya'] as $k=>$lbl): ?>
              <option value="<?= $k ?>" <?= old('kategori')===$k?'selected':'' ?>><?= $lbl ?></option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['kategori'])): ?><div class="invalid-feedback"><?= e($errors['kategori']) ?></div><?php endif; ?>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-sm-6">
              <label class="form-label">Tanggal Kejadian <span class="text-danger">*</span></label>
              <input type="date" class="form-control <?= isset($errors['tanggal_kejadian'])?'is-invalid':'' ?>" name="tanggal_kejadian" value="<?= old('tanggal_kejadian') ?>" max="<?= date('Y-m-d') ?>" required>
              <?php if (isset($errors['tanggal_kejadian'])): ?><div class="invalid-feedback"><?= e($errors['tanggal_kejadian']) ?></div><?php endif; ?>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Jenis Presensi <span class="text-danger">*</span></label>
              <select class="form-select <?= isset($errors['jenis_presensi'])?'is-invalid':'' ?>" name="jenis_presensi" required>
                <option value="">-- Pilih Jenis --</option>
                <?php foreach (['masuk'=>'Presensi Masuk','keluar'=>'Presensi Keluar','keduanya'=>'Presensi Masuk & Keluar'] as $k=>$lbl): ?>
                <option value="<?= $k ?>" <?= old('jenis_presensi')===$k?'selected':'' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
              </select>
              <?php if (isset($errors['jenis_presensi'])): ?><div class="invalid-feedback"><?= e($errors['jenis_presensi']) ?></div><?php endif; ?>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Subjek Pengaduan <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= isset($errors['subjek'])?'is-invalid':'' ?>" name="subjek" value="<?= old('subjek') ?>" placeholder="Contoh: Presensi masuk tidak terekam pada tanggal ..." maxlength="255" required>
            <?php if (isset($errors['subjek'])): ?><div class="invalid-feedback"><?= e($errors['subjek']) ?></div><?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">Deskripsi Pengaduan <span class="text-danger">*</span></label>
            <textarea class="form-control <?= isset($errors['deskripsi'])?'is-invalid':'' ?>" name="deskripsi" id="deskripsi" rows="5" minlength="20" maxlength="2000" required placeholder="Jelaskan secara detail kronologi kejadian..."><?= old('deskripsi') ?></textarea>
            <div class="form-text">Minimal 20 karakter. <span id="charCount">0</span>/2000</div>
            <?php if (isset($errors['deskripsi'])): ?><div class="invalid-feedback"><?= e($errors['deskripsi']) ?></div><?php endif; ?>
          </div>

          <div class="mb-4">
            <label class="form-label">Bukti Pendukung <span class="text-muted small">(opsional)</span></label>
            <input type="file" class="form-control" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
            <div class="form-text">Format: JPG, PNG, PDF, DOC, DOCX. Maks. 5 MB per file.</div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-send me-2"></i>Kirim Pengaduan</button>
            <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Panduan Pengisian</div>
      <div class="card-body" style="font-size:.82rem">
        <p class="mb-2"><strong>Kategori:</strong></p>
        <ul class="ps-3 mb-3" style="line-height:1.9">
          <li>Keterlambatan</li><li>Pulang Lebih Awal</li><li>Presensi Tidak Tercatat</li><li>Lupa Absen</li><li>Gangguan Sistem</li><li>Lainnya</li>
        </ul>
        <p class="mb-2"><strong>Bukti yang bisa dilampirkan:</strong></p>
        <ul class="ps-3 mb-0" style="line-height:1.9"><li>Screenshot e-Presensi</li><li>Foto mesin absensi</li><li>Surat keterangan atasan</li><li>Bukti aktivitas/perjalanan</li></ul>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><i class="bi bi-clock-history me-2"></i>Alur Proses</div>
      <div class="card-body" style="font-size:.8rem">
        <?php foreach ([['1','var(--navy)','Pengajuan','Isi &amp; kirim formulir'],['2','#3b82f6','Verifikasi','Admin cek e-Presensi'],['3','#8b5cf6','Tindak Lanjut','Admin memproses'],['4','#22c55e','Penyelesaian','Pengaduan selesai']] as [$no,$bg,$title,$desc]): ?>
        <div class="d-flex align-items-start gap-2 mb-2">
          <div style="width:22px;height:22px;border-radius:50%;background:<?= $bg ?>;color:#fff;display:flex;align-items:center;justify-content:center;font-size:.65rem;font-weight:700;flex-shrink:0"><?= $no ?></div>
          <div><strong><?= $title ?></strong><br><?= $desc ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<script>
const ta=document.getElementById('deskripsi'),cc=document.getElementById('charCount');
function uc(){cc.textContent=ta.value.length}ta.addEventListener('input',uc);uc();
</script>
