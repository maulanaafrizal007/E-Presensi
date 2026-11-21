<?php
class AuthController
{
    public function redirectHome(): void
    {
        if (Auth::check()) redirect('/dashboard');
        redirect('/login');
    }

    public function showLogin(): void
    {
        if (Auth::check()) redirect('/dashboard');
        $cfg = require BASE_PATH . '/app/config.php';
        $devMode = $cfg['sso_mode'] === 'dev';
        $devAccounts = $devMode ? [
            ['nip'=>'198001012005011001','name'=>'Ahmad Fauzi','role'=>'Pegawai','password'=>'pegawai123'],
            ['nip'=>'197505152000031002','name'=>'Siti Rahayu','role'=>'Pegawai','password'=>'pegawai123'],
            ['nip'=>'198507202010011004','name'=>'Rudi Hermawan','role'=>'Pegawai','password'=>'pegawai123'],
            ['nip'=>'196803251995121001','name'=>'Budi Santoso','role'=>'Administrator','password'=>'admin123'],
            ['nip'=>'197012101994031003','name'=>'Dra. Hj. Endang Supriyati','role'=>'Manajemen','password'=>'manajemen123'],
        ] : [];

        $error = flash('login_error');
        $nipOld = old('nip');

        ob_start();
        require BASE_PATH . '/app/views/auth/login.php';
        $html = ob_get_clean();
        echo $html;
    }

    public function login(): void
    {
        csrf_verify();

        $nip      = trim($_POST['nip'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$nip || !$password) {
            set_old(['nip' => $nip]);
            flash('login_error', 'NIP dan kata sandi wajib diisi.');
            redirect('/login');
        }

        $user = Database::queryOne(
            "SELECT * FROM users WHERE nip = ? AND is_active = 1", [$nip]
        );

        if (!$user || !password_verify($password, $user['password'])) {
            set_old(['nip' => $nip]);
            flash('login_error', 'NIP atau kata sandi tidak valid.');
            redirect('/login');
        }

        Auth::login($user);
        clear_old();
        redirect('/dashboard');
    }

    public function logout(): void
    {
        csrf_verify();
        Auth::logout();
        flash('success', 'Anda telah berhasil keluar dari sistem.');
        redirect('/login');
    }
}
