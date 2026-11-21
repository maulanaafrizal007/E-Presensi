<?php
$authUser = Auth::user();
$badgeMap = ['diajukan'=>'badge-diajukan','menunggu_verifikasi'=>'badge-menunggu','diverifikasi'=>'badge-diverifikasi','diproses'=>'badge-diproses','menunggu_informasi'=>'badge-info','diselesaikan'=>'badge-selesai','ditutup'=>'badge-tutup','ditolak'=>'badge-tolak'];
$isAdmin  = $authUser['role'] === 'admin';
?>
<div class="page-header">
  <div>
    <h1><span class="text-muted fw-normal" style="font-size:1rem">Detail Pengaduan</span><br><code style="font-size:1.1rem"><?= e($pengaduan['nomor_tiket']) ?></code></h1>
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="<?= url('dashboard') ?>">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="<?= url('pengaduan') ?>">Pengaduan Presensi</a></li>
      <li class="breadcrumb-item active"><?= e($pengaduan['nomor_tiket']) ?></li>
    </ol></nav>
  </div>
  <div class="d-flex gap-2 align-items-start flex-wrap">
    <span class="badge-status <?= status_badge($pengaduan['status']) ?>" style="font-size:.82rem;padding:.4rem .9rem"><?= status_label($pengaduan['status']) ?></span>
    <?php if ($pengaduan['is_overdue'] && !in_array($pengaduan['status'],['diselesaikan','ditutup','ditolak'])): ?>
    <span class="badge-status badge-tolak" style="font-size:.82rem;padding:.4rem .9rem"><i class="bi bi-exclamation-triangle me-1"></i>Melewati Batas Waktu</span>
    <?php endif; ?>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-8">

    <!-- Detail -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-file-text me-2"></i>Detail Pengaduan</div>
      <div class="card-body">
        <div class="row g-3">
          <?php foreach ([['Nama Pegawai',$pengaduan['nama_pegawai']],['NIP',$pengaduan['nip']],['Unit Kerja',$pengaduan['unit_kerja']],['Kategori',kategori_label($pengaduan['kategori'])],['Tanggal Kejadian',date('d F Y',strtotime($pengaduan['tanggal_kejadian']))],['Jenis Presensi',jenis_label($pengaduan['jenis_presensi'])]] as [$label,$val]): ?>
          <div class="col-sm-6"><div class="text-muted small mb-1"><?= $label ?></div><div class="fw-600"><?= e($val) ?></div></div>
          <?php endforeach; ?>
          <div class="col-12"><div class="text-muted small mb-1">Subjek</div><div class="fw-600"><?= e($pengaduan['subjek']) ?></div></div>
          <div class="col-12">
            <div class="text-muted small mb-1">Deskripsi</div>
            <div style="background:#f8f9fb;border-radius:8px;padding:.85rem;border:1px solid #e2e8f0;font-size:.88rem;line-height:1.6"><?= e($pengaduan['deskripsi']) ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Attachments -->
    <?php if (!empty($attachments)): ?>
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-paperclip me-2"></i>Bukti Pendukung</div>
      <div class="card-body">
        <div class="row g-2">
          <?php foreach ($attachments as $att): ?>
          <div class="col-12 col-sm-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded">
              <div style="font-size:1.5rem;color:var(--navy)">
                <?php if (str_contains($att['file_type']??'','image')): ?><i class="bi bi-file-image"></i>
                <?php elseif(str_contains($att['file_type']??'','pdf')): ?><i class="bi bi-file-pdf text-danger"></i>
                <?php else: ?><i class="bi bi-file-earmark-text"></i><?php endif; ?>
              </div>
              <div style="flex:1;min-width:0">
                <div style="font-size:.8rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($att['file_name']) ?></div>
                <div style="font-size:.7rem;color:#64748b"><?= $att['file_size'] ? format_size((int)$att['file_size']) : '-' ?></div>
              </div>
              <a href="<?= url('uploads/'.$pengaduan['id'].'/'.basename($att['file_path'])) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-download"></i></a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Verification results -->
    <?php if ($verifikasi): ?>
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-clipboard-check me-2"></i>Hasil Verifikasi Manual</div>
      <div class="card-body">
        <div class="row g-3">
          <?php foreach ([['Tanggal Diklaim',date('d F Y',strtotime($verifikasi['tanggal_presensi_diklaim']))],['Diverifikasi oleh',$verifikasi['verifier_name']],['Jam Masuk (Diklaim)',$verifikasi['jam_masuk_diklaim']?:'-'],['Jam Keluar (Diklaim)',$verifikasi['jam_keluar_diklaim']?:'-'],['Jam Masuk (Sistem)',$verifikasi['jam_masuk_sistem']?:'-'],['Jam Keluar (Sistem)',$verifikasi['jam_keluar_sistem']?:'-']] as [$lbl,$val]): ?>
          <div class="col-sm-6"><div class="text-muted small mb-1"><?= $lbl ?></div><div class="fw-600"><?= e($val) ?></div></div>
          <?php endforeach; ?>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Ada Selisih?</div>
            <div class="fw-600 <?= $verifikasi['ada_selisih'] ? 'text-warning':'text-success' ?>"><?= $verifikasi['ada_selisih']?'Ya, ditemukan selisih':'Tidak ada selisih' ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Hasil Verifikasi</div>
            <div class="fw-600"><?= e(['valid'=>'Valid - Ditemukan Selisih','tidak_valid'=>'Tidak Valid','perlu_investigasi'=>'Perlu Investigasi'][$verifikasi['hasil_verifikasi']]??$verifikasi['hasil_verifikasi']) ?></div>
          </div>
          <?php if ($verifikasi['detail_selisih']): ?>
          <div class="col-12"><div class="text-muted small mb-1">Detail Selisih</div><div style="background:#fffbeb;border-radius:8px;padding:.85rem;border:1px solid #fde68a;font-size:.88rem"><?= e($verifikasi['detail_selisih']) ?></div></div>
          <?php endif; ?>
          <div class="col-12"><div class="text-muted small mb-1">Catatan Verifikasi</div><div style="background:#f8f9fb;border-radius:8px;padding:.85rem;border:1px solid #e2e8f0;font-size:.88rem"><?= e($verifikasi['catatan_verifikasi']) ?></div></div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Timeline responses -->
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-chat-left-text me-2"></i>Riwayat Proses &amp; Respons</div>
      <div class="card-body">
        <?php if (empty($responses)): ?>
        <p class="text-muted small mb-0">Belum ada respons dari administrator.</p>
        <?php else: ?>
        <ul class="timeline">
          <?php foreach ($responses as $resp): ?>
          <li class="timeline-item">
            <div class="timeline-dot <?= e($resp['user_role']) ?>"><?= strtoupper(mb_substr($resp['user_name']??'?',0,1)) ?></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <div>
                  <div class="timeline-author"><?= e($resp['user_name']??'Sistem') ?></div>
                  <div class="timeline-tipe">
                    <?= tipe_response_label($resp['tipe']) ?>
                    <?php if ($resp['status_before'] && $resp['status_after'] && $resp['status_before']!==$resp['status_after']): ?>
                    &rarr; <span class="badge-status <?= $badgeMap[$resp['status_after']]??'badge-default' ?>" style="font-size:.6rem"><?= status_label($resp['status_after']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="timeline-time"><?= date('d M Y H:i',strtotime($resp['created_at'])) ?></div>
              </div>
              <div class="timeline-msg <?= $resp['tipe']==='internal_note'?'internal':'' ?>">
                <?= e($resp['pesan']) ?>
                <?php if ($resp['tipe']==='internal_note' && $isAdmin): ?>
                <span class="badge bg-warning text-dark ms-2" style="font-size:.6rem">Catatan Internal</span>
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>

    <!-- Admin: respond -->
    <?php if ($isAdmin): ?>
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-reply me-2"></i>Tambah Respons / Perbarui Status</div>
      <div class="card-body">
        <form method="POST" action="<?= url('pengaduan/'.$pengaduan['id'].'/respons') ?>">
          <input type="hidden" name="_token" value="<?= csrf_token() ?>">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Tipe Respons <span class="text-danger">*</span></label>
              <select class="form-select" name="tipe" required>
                <option value="">-- Pilih Tipe --</option>
                <?php foreach (['response'=>'Tanggapan','internal_note'=>'Catatan Internal','status_change'=>'Perubahan Status','verification'=>'Hasil Verifikasi','request_info'=>'Permintaan Informasi','resolution'=>'Penyelesaian'] as $k=>$lbl): ?>
                <option value="<?= $k ?>"><?= $lbl ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-sm-6">
              <label class="form-label">Ubah Status Menjadi</label>
              <select class="form-select" name="new_status">
                <option value="">-- Tidak Ubah --</option>
                <?php foreach (['diajukan','menunggu_verifikasi','diverifikasi','diproses','menunggu_informasi','diselesaikan','ditutup','ditolak'] as $s): ?>
                <option value="<?= $s ?>" <?= $pengaduan['status']===$s?'selected':'' ?>><?= status_label($s) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Pesan / Catatan <span class="text-danger">*</span></label>
              <textarea class="form-control" name="pesan" rows="4" required minlength="5" placeholder="Tulis respons, catatan internal, atau keterangan penyelesaian..."></textarea>
            </div>
            <div class="col-12"><button type="submit" class="btn btn-navy"><i class="bi bi-send me-2"></i>Simpan Respons</button></div>
          </div>
        </form>
      </div>
    </div>

    <!-- Admin: verification form -->
    <?php if (!$verifikasi): ?>
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-search me-2"></i>Verifikasi Manual Presensi</div>
      <div class="card-body">
        <div class="alert alert-info mb-3"><i class="bi bi-info-circle me-2"></i>Lakukan pengecekan manual di sistem e-Presensi, kemudian isi formulir di bawah ini. Data resmi tidak akan diubah otomatis.</div>
        <form method="POST" action="<?= url('pengaduan/'.$pengaduan['id'].'/verifikasi') ?>">
          <input type="hidden" name="_token" value="<?= csrf_token() ?>">
          <div class="row g-3">
            <div class="col-sm-6">
              <label class="form-label">Tanggal Presensi yang Diklaim <span class="text-danger">*</span></label>
              <input type="date" class="form-control" name="tanggal_presensi_diklaim" value="<?= e($pengaduan['tanggal_kejadian']) ?>" required>
            </div>
            <div class="col-sm-3"><label class="form-label">Jam Masuk (Diklaim)</label><input type="time" class="form-control" name="jam_masuk_diklaim"></div>
            <div class="col-sm-3"><label class="form-label">Jam Keluar (Diklaim)</label><input type="time" class="form-control" name="jam_keluar_diklaim"></div>
            <div class="col-sm-3"><label class="form-label">Jam Masuk (Sistem)</label><input type="time" class="form-control" name="jam_masuk_sistem"></div>
            <div class="col-sm-3"><label class="form-label">Jam Keluar (Sistem)</label><input type="time" class="form-control" name="jam_keluar_sistem"></div>
            <div class="col-sm-6">
              <label class="form-label">Ada Selisih Data? <span class="text-danger">*</span></label>
              <select class="form-select" name="ada_selisih" required><option value="0">Tidak ada selisih</option><option value="1">Ya, ditemukan selisih</option></select>
            </div>
            <div class="col-12"><label class="form-label">Detail Selisih</label><textarea class="form-control" name="detail_selisih" rows="2"></textarea></div>
            <div class="col-sm-6">
              <label class="form-label">Hasil Verifikasi <span class="text-danger">*</span></label>
              <select class="form-select" name="hasil_verifikasi" required>
                <option value="">-- Pilih Hasil --</option>
                <option value="valid">Valid - Ditemukan Selisih</option>
                <option value="tidak_valid">Tidak Valid - Data Sesuai</option>
                <option value="perlu_investigasi">Perlu Investigasi Lebih Lanjut</option>
              </select>
            </div>
            <div class="col-12"><label class="form-label">Catatan Verifikasi <span class="text-danger">*</span></label><textarea class="form-control" name="catatan_verifikasi" rows="3" required minlength="10"></textarea></div>
            <div class="col-12"><button type="submit" class="btn btn-navy"><i class="bi bi-clipboard-check me-2"></i>Simpan Hasil Verifikasi</button></div>
          </div>
        </form>
      </div>
    </div>
    <?php else: ?>
    <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Verifikasi manual telah dilakukan oleh <strong><?= e($verifikasi['verifier_name']) ?></strong> pada <?= date('d M Y H:i',strtotime($verifikasi['verified_at'])) ?>.</div>
    <?php endif; ?>
    <?php endif; ?>

  </div>

  <!-- Right sidebar -->
  <div class="col-12 col-lg-4">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Pengaduan</div>
      <div class="card-body">
        <table class="table table-sm mb-0" style="font-size:.82rem">
          <tr><td class="text-muted pe-2">No. Tiket</td><td><code><?= e($pengaduan['nomor_tiket']) ?></code></td></tr>
          <tr><td class="text-muted pe-2">Status</td><td><span class="badge-status <?= status_badge($pengaduan['status']) ?>"><?= status_label($pengaduan['status']) ?></span></td></tr>
          <tr><td class="text-muted pe-2">Diajukan</td><td><?= date('d M Y H:i',strtotime($pengaduan['created_at'])) ?></td></tr>
          <?php if ($handler): ?><tr><td class="text-muted pe-2">Ditangani</td><td><?= e($handler['name']) ?></td></tr><?php endif; ?>
          <?php if ($pengaduan['handled_at']): ?><tr><td class="text-muted pe-2">Mulai</td><td><?= date('d M Y',strtotime($pengaduan['handled_at'])) ?></td></tr><?php endif; ?>
          <?php if ($pengaduan['resolved_at']): ?><tr><td class="text-muted pe-2">Selesai</td><td><?= date('d M Y',strtotime($pengaduan['resolved_at'])) ?></td></tr><?php endif; ?>
          <?php if ($pengaduan['batas_waktu']): ?>
          <tr><td class="text-muted pe-2">Batas Waktu</td>
          <td class="<?= strtotime($pengaduan['batas_waktu'])<time() && !in_array($pengaduan['status'],['diselesaikan','ditutup','ditolak'])?'text-danger fw-600':'' ?>"><?= date('d M Y',strtotime($pengaduan['batas_waktu'])) ?></td></tr>
          <?php endif; ?>
          <tr><td class="text-muted pe-2">Verifikasi</td><td><?= $verifikasi?'<span class="badge-status badge-diverifikasi">Sudah</span>':'<span class="badge-status badge-menunggu">Belum</span>' ?></td></tr>
        </table>
      </div>
    </div>
    <a href="<?= url('pengaduan') ?>" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar</a>
  </div>
</div>
