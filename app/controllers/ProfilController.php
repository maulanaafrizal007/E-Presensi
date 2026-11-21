<?php
class ProfilController
{
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();

        $myStats = null;
        if ($user['role'] === 'pegawai') {
            $uid = $user['id'];
            $myStats = [
                'total'   => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=?",[$uid])['c'],
                'aktif'   => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=? AND status NOT IN('diselesaikan','ditutup','ditolak')",[$uid])['c'],
                'selesai' => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=? AND status IN('diselesaikan','ditutup')",[$uid])['c'],
                'ditolak' => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=? AND status='ditolak'",[$uid])['c'],
            ];
        }

        $userCounts = null;
        if ($user['role'] === 'admin') {
            $userCounts = [
                'total'     => Database::queryOne("SELECT COUNT(*) c FROM users")['c'],
                'pegawai'   => Database::queryOne("SELECT COUNT(*) c FROM users WHERE role='pegawai'")['c'],
                'admin'     => Database::queryOne("SELECT COUNT(*) c FROM users WHERE role='admin'")['c'],
                'manajemen' => Database::queryOne("SELECT COUNT(*) c FROM users WHERE role='manajemen'")['c'],
            ];
        }

        $cfg = require BASE_PATH . '/app/config.php';
        $ssoMode = $cfg['sso_mode'];

        ob_start(); require BASE_PATH . '/app/views/profil/index.php';
        echo render_layout('Profil', ob_get_clean());
    }

    public function users(): void
    {
        Auth::requireRole(['admin']);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 20;
        $offset = ($page - 1) * $limit;
        $total  = (int)Database::queryOne("SELECT COUNT(*) c FROM users")['c'];
        $users  = Database::query("SELECT * FROM users ORDER BY role, name LIMIT $limit OFFSET $offset");
        $pages  = ceil($total / $limit);

        ob_start(); require BASE_PATH . '/app/views/profil/users.php';
        echo render_layout('Manajemen Pengguna', ob_get_clean());
    }

    public function createUser(): void
    {
        Auth::requireRole(['admin']);
        $errors = flash('form_errors') ? json_decode(flash('form_errors'), true) : [];
        ob_start(); require BASE_PATH . '/app/views/profil/create_user.php';
        echo render_layout('Tambah Pengguna', ob_get_clean());
    }

    public function storeUser(): void
    {
        Auth::requireRole(['admin']);
        csrf_verify();

        $data = [
            'nip'      => trim($_POST['nip']      ?? ''),
            'name'     => trim($_POST['name']     ?? ''),
            'email'    => trim($_POST['email']    ?? ''),
            'position' => trim($_POST['position'] ?? ''),
            'unit'     => trim($_POST['unit']     ?? ''),
            'role'     => $_POST['role'] ?? '',
            'password' => $_POST['password'] ?? '',
            'password_confirmation' => $_POST['password_confirmation'] ?? '',
        ];

        $errors = [];
        if (!$data['nip'])    $errors['nip']    = 'NIP wajib diisi.';
        elseif (Database::queryOne("SELECT id FROM users WHERE nip=?",[$data['nip']])) $errors['nip'] = 'NIP sudah terdaftar.';
        if (!$data['name'])   $errors['name']   = 'Nama wajib diisi.';
        if (!$data['email'])  $errors['email']  = 'Email wajib diisi.';
        elseif (Database::queryOne("SELECT id FROM users WHERE email=?",[$data['email']])) $errors['email'] = 'Email sudah terdaftar.';
        if (!in_array($data['role'], ['pegawai','admin','manajemen'])) $errors['role'] = 'Peran tidak valid.';
        if (strlen($data['password']) < 8) $errors['password'] = 'Kata sandi minimal 8 karakter.';
        elseif ($data['password'] !== $data['password_confirmation']) $errors['password'] = 'Konfirmasi kata sandi tidak cocok.';

        if ($errors) {
            set_old($data);
            flash('form_errors', json_encode($errors));
            redirect('/profil/pengguna/buat');
        }

        Database::execute(
            "INSERT INTO users (nip,name,email,position,unit,role,password) VALUES (?,?,?,?,?,?,?)",
            [$data['nip'],$data['name'],$data['email'],$data['position'],$data['unit'],$data['role'],password_hash($data['password'],PASSWORD_DEFAULT)]
        );

        clear_old();
        flash('success', "Akun pengguna {$data['name']} berhasil dibuat.");
        redirect('/profil/pengguna');
    }

    public function editUser(string $rawId): void
    {
        Auth::requireRole(['admin']);
        $uid = (int)$rawId;
        $editUser = Database::queryOne("SELECT * FROM users WHERE id=?", [$uid]);
        if (!$editUser) redirect('/profil/pengguna');

        $errors = flash('form_errors') ? json_decode(flash('form_errors'), true) : [];
        ob_start(); require BASE_PATH . '/app/views/profil/edit_user.php';
        echo render_layout('Edit Pengguna', ob_get_clean());
    }

    public function updateUser(string $rawId): void
    {
        Auth::requireRole(['admin']);
        csrf_verify();
        $uid = (int)$rawId;
        $editUser = Database::queryOne("SELECT * FROM users WHERE id=?", [$uid]);
        if (!$editUser) redirect('/profil/pengguna');

        $data = [
            'position'  => trim($_POST['position'] ?? ''),
            'unit'      => trim($_POST['unit']     ?? ''),
            'role'      => $_POST['role'] ?? '',
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'new_password' => $_POST['new_password'] ?? '',
            'new_password_confirmation' => $_POST['new_password_confirmation'] ?? '',
        ];

        $errors = [];
        if (!in_array($data['role'], ['pegawai','admin','manajemen'])) $errors['role'] = 'Peran tidak valid.';
        if ($data['new_password'] && strlen($data['new_password']) < 8) $errors['new_password'] = 'Kata sandi baru minimal 8 karakter.';
        if ($data['new_password'] && $data['new_password'] !== $data['new_password_confirmation']) $errors['new_password'] = 'Konfirmasi kata sandi baru tidak cocok.';

        if ($errors) {
            flash('form_errors', json_encode($errors));
            redirect('/profil/pengguna/'.$uid.'/edit');
        }

        $sql  = "UPDATE users SET position=?, unit=?, role=?, is_active=?";
        $vals = [$data['position'], $data['unit'], $data['role'], $data['is_active']];

        if ($data['new_password']) {
            $sql  .= ", password=?";
            $vals[] = password_hash($data['new_password'], PASSWORD_DEFAULT);
        }
        $vals[] = $uid;
        Database::execute($sql . " WHERE id=?", $vals);

        // Update session if editing own account
        if ($uid === Auth::id()) {
            $refreshed = Database::queryOne("SELECT * FROM users WHERE id=?", [$uid]);
            $_SESSION['user'] = $refreshed;
        }

        flash('success', "Akun {$editUser['name']} berhasil diperbarui.");
        redirect('/profil/pengguna');
    }

    public function toggleUser(string $rawId): void
    {
        Auth::requireRole(['admin']);
        csrf_verify();
        $uid = (int)$rawId;

        if ($uid === Auth::id()) {
            flash('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            redirect('/profil/pengguna');
        }

        $u = Database::queryOne("SELECT * FROM users WHERE id=?", [$uid]);
        if (!$u) redirect('/profil/pengguna');

        $newStatus = $u['is_active'] ? 0 : 1;
        Database::execute("UPDATE users SET is_active=? WHERE id=?", [$newStatus, $uid]);
        flash('success', "Akun {$u['name']} berhasil " . ($newStatus ? 'diaktifkan' : 'dinonaktifkan') . '.');
        redirect('/profil/pengguna');
    }
}
