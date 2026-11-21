<?php
/**
 * Auth — session-based authentication helpers
 */
class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        // Cache in session to avoid repeated DB calls
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = $user;

        Database::execute(
            "UPDATE users SET last_login_at = NOW() WHERE id = ?",
            [$user['id']]
        );
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function require(): void
    {
        if (!self::check()) {
            redirect('/login');
        }
        // Refresh user from DB
        $user = Database::queryOne("SELECT * FROM users WHERE id = ? AND is_active = 1", [self::id()]);
        if (!$user) {
            self::logout();
            redirect('/login');
        }
        $_SESSION['user'] = $user;
    }

    public static function requireRole(array $roles): void
    {
        self::require();
        $user = self::user();
        if (!in_array($user['role'], $roles)) {
            http_response_code(403);
            die(render_layout('Akses Ditolak', '<div class="empty-state text-center" style="margin:4rem auto;">
                <div style="font-size:3.5rem;font-weight:800;color:#dc2626;">403</div>
                <h4>Akses Ditolak</h4>
                <p class="text-muted">Anda tidak memiliki izin untuk halaman ini.</p>
                <a href="' . url('/dashboard') . '" class="btn btn-navy">Kembali ke Dashboard</a>
            </div>'));
        }
    }
}
