<?php
class PengaduanController
{
    // ── Index (role-based) ───────────────────────────────────────
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();

        $user['role'] === 'pegawai'
            ? $this->pegawaiIndex($user)
            : $this->adminIndex($user);
    }

    private function pegawaiIndex(array $user): void
    {
        $status   = $_GET['status']   ?? '';
        $kategori = $_GET['kategori'] ?? '';

        $where  = "WHERE p.user_id = ?";
        $params = [$user['id']];
        if ($status)   { $where .= " AND p.status = ?";   $params[] = $status; }
        if ($kategori) { $where .= " AND p.kategori = ?"; $params[] = $kategori; }

        $page  = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $total = (int)Database::queryOne(
            "SELECT COUNT(*) c FROM pengaduans p $where", $params
        )['c'];

        $rows = Database::query(
            "SELECT p.*,
              (SELECT r.pesan FROM pengaduan_responses r
               WHERE r.pengaduan_id=p.id AND r.visible_to_employee=1
               ORDER BY r.created_at DESC LIMIT 1) as last_response,
              (SELECT r.created_at FROM pengaduan_responses r
               WHERE r.pengaduan_id=p.id AND r.visible_to_employee=1
               ORDER BY r.created_at DESC LIMIT 1) as last_response_at
             FROM pengaduans p $where ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );

        $pages   = ceil($total / $limit);
        $filters = compact('status', 'kategori');

        ob_start(); require BASE_PATH . '/app/views/pengaduan/index_pegawai.php';
        echo render_layout('Pengaduan Presensi', ob_get_clean());
    }

    private function adminIndex(array $user): void
    {
        $search   = $_GET['search']        ?? '';
        $status   = $_GET['status']        ?? '';
        $kategori = $_GET['kategori']      ?? '';
        $dari     = $_GET['tanggal_dari']  ?? '';
        $sampai   = $_GET['tanggal_sampai']?? '';

        $where  = "WHERE 1=1";
        $params = [];
        if ($search)   { $where .= " AND (p.nama_pegawai LIKE ? OR p.nip LIKE ? OR p.nomor_tiket LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
        if ($status)   { $where .= " AND p.status = ?";           $params[] = $status; }
        if ($kategori) { $where .= " AND p.kategori = ?";         $params[] = $kategori; }
        if ($dari)     { $where .= " AND DATE(p.created_at) >= ?";$params[] = $dari; }
        if ($sampai)   { $where .= " AND DATE(p.created_at) <= ?";$params[] = $sampai; }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 15;
        $offset = ($page - 1) * $limit;

        $total = (int)Database::queryOne("SELECT COUNT(*) c FROM pengaduans p $where", $params)['c'];
        $rows  = Database::query(
            "SELECT p.*, u.name as handler_name FROM pengaduans p
             LEFT JOIN users u ON u.id = p.handled_by
             $where ORDER BY p.created_at DESC LIMIT $limit OFFSET $offset",
            $params
        );
        $pages   = ceil($total / $limit);
        $filters = compact('search','status','kategori','dari','sampai');

        ob_start(); require BASE_PATH . '/app/views/pengaduan/index_admin.php';
        echo render_layout('Pengaduan Presensi', ob_get_clean());
    }

    // ── Create (pegawai only) ────────────────────────────────────
    public function create(): void
    {
        Auth::requireRole(['pegawai']);
        $errors = flash('form_errors') ? json_decode(flash('form_errors'), true) : [];
        ob_start(); require BASE_PATH . '/app/views/pengaduan/create.php';
        echo render_layout('Ajukan Pengaduan', ob_get_clean());
    }

    public function store(): void
    {
        Auth::requireRole(['pegawai']);
        csrf_verify();

        $user = Auth::user();
        $data = [
            'kategori'         => trim($_POST['kategori'] ?? ''),
            'tanggal_kejadian' => trim($_POST['tanggal_kejadian'] ?? ''),
            'jenis_presensi'   => trim($_POST['jenis_presensi'] ?? ''),
            'subjek'           => trim($_POST['subjek'] ?? ''),
            'deskripsi'        => trim($_POST['deskripsi'] ?? ''),
        ];

        $errors = [];
        if (!$data['kategori'])         $errors['kategori']         = 'Kategori wajib dipilih.';
        if (!$data['tanggal_kejadian']) $errors['tanggal_kejadian'] = 'Tanggal kejadian wajib diisi.';
        elseif ($data['tanggal_kejadian'] > date('Y-m-d')) $errors['tanggal_kejadian'] = 'Tanggal tidak boleh di masa depan.';
        if (!$data['jenis_presensi'])   $errors['jenis_presensi']   = 'Jenis presensi wajib dipilih.';
        if (!$data['subjek'])           $errors['subjek']           = 'Subjek wajib diisi.';
        if (mb_strlen($data['deskripsi']) < 20) $errors['deskripsi'] = 'Deskripsi minimal 20 karakter.';

        if ($errors) {
            set_old($data);
            flash('form_errors', json_encode($errors));
            redirect('/pengaduan/buat');
        }

        $nomor = next_ticket_number();
        $batas = date('Y-m-d', strtotime('+7 days'));

        $id = Database::execute(
            "INSERT INTO pengaduans (nomor_tiket,user_id,nama_pegawai,nip,unit_kerja,kategori,tanggal_kejadian,jenis_presensi,subjek,deskripsi,status,batas_waktu)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?)",
            [$nomor,$user['id'],$user['name'],$user['nip'],$user['unit']??'',
             $data['kategori'],$data['tanggal_kejadian'],$data['jenis_presensi'],
             $data['subjek'],$data['deskripsi'],'diajukan',$batas]
        );

        // Handle file uploads
        if (!empty($_FILES['attachments']['name'][0])) {
            $this->handleUploads($id);
        }

        clear_old();
        flash('success', "Pengaduan berhasil diajukan dengan nomor tiket $nomor.");
        redirect('/pengaduan/' . $id);
    }

    // ── Show ─────────────────────────────────────────────────────
    public function show(string $rawId): void
    {
        Auth::require();
        $user = Auth::user();
        $id   = (int)$rawId;

        $pengaduan = Database::queryOne("SELECT * FROM pengaduans WHERE id = ?", [$id]);
        if (!$pengaduan) { http_response_code(404); echo render_layout('404', '<div class="empty-state"><h4>Pengaduan tidak ditemukan.</h4></div>'); return; }

        if ($user['role'] === 'pegawai' && $pengaduan['user_id'] != $user['id']) {
            http_response_code(403); echo render_layout('Akses Ditolak', '<div class="empty-state"><h4>Akses ditolak.</h4></div>'); return;
        }

        $responses = Database::query(
            "SELECT r.*, u.name as user_name, u.role as user_role
             FROM pengaduan_responses r
             LEFT JOIN users u ON u.id = r.user_id
             WHERE r.pengaduan_id = ?" . ($user['role'] === 'pegawai' ? " AND r.visible_to_employee = 1" : "") .
            " ORDER BY r.created_at ASC",
            [$id]
        );

        $allResponses = $user['role'] !== 'pegawai'
            ? Database::query("SELECT r.*, u.name as user_name, u.role as user_role FROM pengaduan_responses r LEFT JOIN users u ON u.id=r.user_id WHERE r.pengaduan_id=? ORDER BY r.created_at ASC", [$id])
            : $responses;

        $attachments = Database::query("SELECT * FROM pengaduan_attachments WHERE pengaduan_id = ?", [$id]);
        $verifikasi  = Database::queryOne("SELECT v.*, u.name as verifier_name FROM verifikasis v LEFT JOIN users u ON u.id=v.verified_by WHERE v.pengaduan_id=?", [$id]);
        $handler     = $pengaduan['handled_by'] ? Database::queryOne("SELECT name FROM users WHERE id=?", [$pengaduan['handled_by']]) : null;

        ob_start(); require BASE_PATH . '/app/views/pengaduan/show.php';
        echo render_layout('Detail Pengaduan ' . e($pengaduan['nomor_tiket']), ob_get_clean());
    }

    // ── Respond (admin only) ─────────────────────────────────────
    public function respond(string $rawId): void
    {
        Auth::requireRole(['admin']);
        csrf_verify();
        $user = Auth::user();
        $id   = (int)$rawId;

        $pengaduan = Database::queryOne("SELECT * FROM pengaduans WHERE id=?", [$id]);
        if (!$pengaduan) redirect('/pengaduan');

        $tipe       = $_POST['tipe']       ?? '';
        $pesan      = trim($_POST['pesan'] ?? '');
        $new_status = $_POST['new_status'] ?? '';

        if (!$tipe || !$pesan) { flash('error','Tipe dan pesan wajib diisi.'); redirect('/pengaduan/'.$id); }

        $statusBefore = $pengaduan['status'];
        $statusAfter  = ($new_status && $new_status !== $statusBefore) ? $new_status : $statusBefore;

        Database::execute(
            "INSERT INTO pengaduan_responses (pengaduan_id,user_id,tipe,pesan,status_before,status_after,visible_to_employee) VALUES (?,?,?,?,?,?,?)",
            [$id, $user['id'], $tipe, $pesan, $statusBefore, $statusAfter, $tipe !== 'internal_note' ? 1 : 0]
        );

        if ($new_status && $new_status !== $statusBefore) {
            $set  = "status=?, updated_at=NOW()";
            $vals = [$new_status];
            if (!$pengaduan['handled_by']) { $set .= ", handled_by=?, handled_at=NOW()"; $vals[] = $user['id']; }
            if (in_array($new_status,['diselesaikan','ditutup'])) { $set .= ", resolved_at=NOW()"; }
            $vals[] = $id;
            Database::execute("UPDATE pengaduans SET $set WHERE id=?", $vals);
        }

        flash('success', 'Respons berhasil disimpan.');
        redirect('/pengaduan/' . $id);
    }

    // ── Verifikasi (admin only) ───────────────────────────────────
    public function storeVerifikasi(string $rawId): void
    {
        Auth::requireRole(['admin']);
        csrf_verify();
        $user = Auth::user();
        $id   = (int)$rawId;

        $pengaduan = Database::queryOne("SELECT * FROM pengaduans WHERE id=?", [$id]);
        if (!$pengaduan) redirect('/pengaduan');

        $catatan = trim($_POST['catatan_verifikasi'] ?? '');
        $hasil   = $_POST['hasil_verifikasi'] ?? '';

        if (!$catatan || !$hasil) { flash('error','Catatan dan hasil verifikasi wajib diisi.'); redirect('/pengaduan/'.$id); }

        $existing = Database::queryOne("SELECT id FROM verifikasis WHERE pengaduan_id=?", [$id]);
        $fields = [
            'pengaduan_id'            => $id,
            'verified_by'             => $user['id'],
            'tanggal_presensi_diklaim'=> $_POST['tanggal_presensi_diklaim'] ?? $pengaduan['tanggal_kejadian'],
            'jam_masuk_diklaim'       => $_POST['jam_masuk_diklaim'] ?? null,
            'jam_keluar_diklaim'      => $_POST['jam_keluar_diklaim'] ?? null,
            'jam_masuk_sistem'        => $_POST['jam_masuk_sistem'] ?? null,
            'jam_keluar_sistem'       => $_POST['jam_keluar_sistem'] ?? null,
            'ada_selisih'             => (int)($_POST['ada_selisih'] ?? 0),
            'detail_selisih'          => trim($_POST['detail_selisih'] ?? ''),
            'catatan_verifikasi'      => $catatan,
            'hasil_verifikasi'        => $hasil,
            'verified_at'             => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $sets = implode(',', array_map(fn($k)=>"$k=?", array_keys($fields)));
            Database::execute("UPDATE verifikasis SET $sets WHERE pengaduan_id=?", [...array_values($fields), $id]);
        } else {
            $cols = implode(',', array_keys($fields));
            $phs  = implode(',', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO verifikasis ($cols) VALUES ($phs)", array_values($fields));
        }

        // Advance status to 'diverifikasi' if still at diajukan/menunggu
        if (in_array($pengaduan['status'], ['diajukan','menunggu_verifikasi'])) {
            Database::execute(
                "UPDATE pengaduans SET status='diverifikasi', handled_by=?, handled_at=NOW(), updated_at=NOW() WHERE id=?",
                [$user['id'], $id]
            );
        }

        Database::execute(
            "INSERT INTO pengaduan_responses (pengaduan_id,user_id,tipe,pesan,status_before,status_after,visible_to_employee) VALUES (?,?,?,?,?,?,?)",
            [$id, $user['id'], 'verification',
             'Verifikasi manual selesai dilakukan. Hasil: ' . ['valid'=>'Valid - Ditemukan Selisih','tidak_valid'=>'Tidak Valid','perlu_investigasi'=>'Perlu Investigasi'][$hasil] ?? $hasil,
             $pengaduan['status'], 'diverifikasi', 1]
        );

        flash('success', 'Hasil verifikasi berhasil disimpan.');
        redirect('/pengaduan/' . $id);
    }

    // ── Upload helper ─────────────────────────────────────────────
    private function handleUploads(int $pengaduanId): void
    {
        $cfg     = require BASE_PATH . '/app/config.php';
        $dir     = $cfg['upload_path'] . $pengaduanId . '/';
        $allowed = $cfg['upload_allowed'];
        $maxSize = $cfg['upload_max_mb'] * 1024 * 1024;

        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $files = $_FILES['attachments'];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) continue;
            $origName = $files['name'][$i];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) continue;
            if ($files['size'][$i] > $maxSize) continue;

            $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origName);
            $dest = $dir . $safeName;

            if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                Database::execute(
                    "INSERT INTO pengaduan_attachments (pengaduan_id,file_name,file_path,file_type,file_size) VALUES (?,?,?,?,?)",
                    [$pengaduanId, $origName, $dest, $files['type'][$i], $files['size'][$i]]
                );
            }
        }
    }
}
