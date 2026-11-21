<?php
/**
 * Helpers — global utility functions
 */

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void
{
    $token = $_POST['_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('<h2>Token CSRF tidak valid. Silakan <a href="javascript:history.back()">kembali</a>.</h2>');
    }
}

function e(mixed $val): string
{
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function flash(string $key, string $msg = null): ?string
{
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return null;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function set_old(array $data): void
{
    $_SESSION['old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['old']);
}

function status_label(string $status): string
{
    return [
        'diajukan'            => 'Diajukan',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'diverifikasi'        => 'Telah Diverifikasi',
        'diproses'            => 'Sedang Diproses',
        'menunggu_informasi'  => 'Menunggu Informasi Tambahan',
        'diselesaikan'        => 'Diselesaikan',
        'ditutup'             => 'Ditutup',
        'ditolak'             => 'Ditolak',
    ][$status] ?? ucfirst($status);
}

function status_badge(string $status): string
{
    return [
        'diajukan'            => 'badge-diajukan',
        'menunggu_verifikasi' => 'badge-menunggu',
        'diverifikasi'        => 'badge-diverifikasi',
        'diproses'            => 'badge-diproses',
        'menunggu_informasi'  => 'badge-info',
        'diselesaikan'        => 'badge-selesai',
        'ditutup'             => 'badge-tutup',
        'ditolak'             => 'badge-tolak',
    ][$status] ?? 'badge-default';
}

function kategori_label(string $k): string
{
    return [
        'terlambat'      => 'Keterlambatan',
        'pulang_cepat'   => 'Pulang Lebih Awal',
        'tidak_tercatat' => 'Presensi Tidak Tercatat',
        'lupa_absen'     => 'Lupa Absen',
        'gangguan_sistem'=> 'Gangguan Sistem',
        'lainnya'        => 'Lainnya',
    ][$k] ?? ucfirst($k);
}

function jenis_label(string $j): string
{
    return [
        'masuk'    => 'Presensi Masuk',
        'keluar'   => 'Presensi Keluar',
        'keduanya' => 'Presensi Masuk & Keluar',
    ][$j] ?? ucfirst($j);
}

function tipe_response_label(string $t): string
{
    return [
        'response'       => 'Tanggapan',
        'internal_note'  => 'Catatan Internal',
        'status_change'  => 'Perubahan Status',
        'verification'   => 'Hasil Verifikasi',
        'request_info'   => 'Permintaan Informasi',
        'resolution'     => 'Penyelesaian',
    ][$t] ?? ucfirst($t);
}

function role_label(string $r): string
{
    return ['admin' => 'Administrator', 'manajemen' => 'Manajemen', 'pegawai' => 'Pegawai'][$r] ?? $r;
}

function str_limit(string $str, int $len): string
{
    return mb_strlen($str) > $len ? mb_substr($str, 0, $len) . '…' : $str;
}

function format_size(int $bytes): string
{
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
    return round($bytes / 1048576, 2) . ' MB';
}

function next_ticket_number(): string
{
    $year  = date('Y');
    $count = Database::queryOne(
        "SELECT COUNT(*) as cnt FROM pengaduans WHERE YEAR(created_at) = ?", [$year]
    )['cnt'] ?? 0;
    return 'ADU-' . $year . '-' . str_pad((int)$count + 1, 5, '0', STR_PAD_LEFT);
}

/**
 * Render the full page inside the main application layout.
 */
function render_layout(string $pageTitle, string $content, bool $noLayout = false): string
{
    if ($noLayout) return $content;

    $user     = Auth::user();
    $baseUrl  = BASE_URL;
    $isAdmin  = $user && $user['role'] === 'admin';
    $isMgmt   = $user && $user['role'] === 'manajemen';

    // Pending badge counts
    $pendingBadge = '';
    if ($user) {
        if ($user['role'] === 'pegawai') {
            $cnt = Database::queryOne(
                "SELECT COUNT(*) as c FROM pengaduans WHERE user_id=? AND status NOT IN ('diselesaikan','ditutup','ditolak')",
                [$user['id']]
            )['c'] ?? 0;
        } else {
            $cnt = Database::queryOne(
                "SELECT COUNT(*) as c FROM pengaduans WHERE status='diajukan'"
            )['c'] ?? 0;
        }
        if ($cnt > 0) $pendingBadge = '<span class="nav-badge">' . $cnt . '</span>';
    }

    $navDash  = url('dashboard');
    $navPengaduan = url('pengaduan');
    $navProfil = url('profil');

    $reqPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($base && str_starts_with($reqPath, $base)) $reqPath = substr($reqPath, strlen($base));

    $activeDash   = str_starts_with($reqPath, '/dashboard') ? 'active' : '';
    $activePengaduan = str_starts_with($reqPath, '/pengaduan') ? 'active' : '';
    $activeProfil = str_starts_with($reqPath, '/profil') ? 'active' : '';

    $userName  = $user ? e($user['name']) : '';
    $userNip   = $user ? e($user['nip']) : '';
    $userAvatar= $user ? strtoupper(mb_substr($user['name'], 0, 2)) : '??';
    $roleLabel = $user ? role_label($user['role']) : '';
    $roleClass = $user ? e($user['role']) : '';

    $flashSuccess = flash('success');
    $flashError   = flash('error');
    $alertHtml = '';
    if ($flashSuccess) $alertHtml .= '<div class="alert alert-success alert-dismissible fade show mb-3"><i class="bi bi-check-circle me-2"></i>' . e($flashSuccess) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    if ($flashError)   $alertHtml .= '<div class="alert alert-danger alert-dismissible fade show mb-3"><i class="bi bi-exclamation-circle me-2"></i>' . e($flashError) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';

    $logoutUrl = url('logout');
    $csrfToken = csrf_token();

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> — SIKAP DPD RI</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{--navy:#1a3561;--navy-dark:#0f2040;--gold:#c9a84c;--gold-light:#e0c06a;--bg:#f4f6fb;--border:#e2e8f0;--text:#1e2d3d;--muted:#64748b;--sidebar-width:260px}
*{box-sizing:border-box}
body{font-family:'Inter',-apple-system,sans-serif;background:var(--bg);color:var(--text);margin:0}
.sidebar{position:fixed;top:0;left:0;width:var(--sidebar-width);height:100vh;background:linear-gradient(180deg,var(--navy-dark) 0%,var(--navy) 100%);display:flex;flex-direction:column;z-index:1000;transition:transform .3s ease}
.sidebar-brand{padding:1.5rem 1.25rem 1rem;border-bottom:1px solid rgba(255,255,255,.1)}
.brand-logo{width:44px;height:44px;background:var(--gold);border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:var(--navy-dark);margin-bottom:.5rem}
.brand-name{font-size:1rem;font-weight:700;color:#fff;line-height:1.2}
.brand-sub{font-size:.65rem;color:rgba(255,255,255,.55);margin-top:2px;text-transform:uppercase;letter-spacing:.05em}
.sidebar-nav{flex:1;padding:1rem 0;overflow-y:auto}
.nav-section-label{font-size:.6rem;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.35);padding:.5rem 1.25rem;font-weight:600}
.nav-item{margin:2px .75rem}
.nav-link{display:flex;align-items:center;gap:.75rem;padding:.65rem 1rem;border-radius:8px;color:rgba(255,255,255,.7)!important;text-decoration:none;font-size:.875rem;font-weight:500;transition:all .2s}
.nav-link:hover{background:rgba(255,255,255,.1);color:#fff!important}
.nav-link.active{background:var(--gold);color:var(--navy-dark)!important;font-weight:600}
.nav-icon{width:20px;text-align:center;font-size:1rem;flex-shrink:0}
.nav-badge{margin-left:auto;background:rgba(255,255,255,.15);font-size:.65rem;padding:2px 6px;border-radius:10px;font-weight:600;color:rgba(255,255,255,.8)}
.nav-link.active .nav-badge{background:rgba(10,25,50,.2);color:var(--navy-dark)}
.sidebar-role{padding:1rem 1.25rem;border-top:1px solid rgba(255,255,255,.1)}
.role-pill{display:inline-block;font-size:.65rem;text-transform:uppercase;letter-spacing:.08em;padding:3px 10px;border-radius:20px;font-weight:600}
.role-pill.pegawai{background:rgba(201,168,76,.2);color:var(--gold-light)}
.role-pill.admin{background:rgba(99,179,237,.2);color:#90cdf4}
.role-pill.manajemen{background:rgba(154,101,218,.2);color:#c4b5fd}
.sidebar-user-name{font-size:.8rem;color:#fff;font-weight:500;margin-top:.3rem}
.sidebar-user-nip{font-size:.68rem;color:rgba(255,255,255,.45)}
.topbar{position:fixed;top:0;left:var(--sidebar-width);right:0;height:60px;background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 1.5rem;z-index:999;gap:1rem}
.topbar-toggle{display:none;background:none;border:none;font-size:1.25rem;color:var(--navy);cursor:pointer;padding:.25rem}
.topbar-title{font-size:1rem;font-weight:600;color:var(--navy)}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:.75rem}
.avatar{width:32px;height:32px;background:var(--navy);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.7rem;color:#fff}
.main-content{margin-left:var(--sidebar-width);margin-top:60px;padding:1.75rem;min-height:calc(100vh - 60px)}
.card{border:1px solid var(--border);border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.card-header{background:#fff;border-bottom:1px solid var(--border);padding:1rem 1.25rem;border-radius:12px 12px 0 0!important;font-weight:600;color:var(--navy)}
.stat-card{background:#fff;border-radius:12px;padding:1.25rem;border:1px solid var(--border);display:flex;align-items:center;gap:1rem}
.stat-icon{width:50px;height:50px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
.stat-icon.navy{background:rgba(26,53,97,.1);color:var(--navy)}
.stat-icon.gold{background:rgba(201,168,76,.15);color:#a0782a}
.stat-icon.success{background:rgba(34,197,94,.1);color:#15803d}
.stat-icon.danger{background:rgba(239,68,68,.1);color:#dc2626}
.stat-icon.warning{background:rgba(245,158,11,.1);color:#d97706}
.stat-icon.info{background:rgba(59,130,246,.1);color:#2563eb}
.stat-icon.purple{background:rgba(139,92,246,.1);color:#7c3aed}
.stat-label{font-size:.75rem;color:var(--muted);font-weight:500}
.stat-value{font-size:1.75rem;font-weight:700;color:var(--navy);line-height:1.1}
.badge-status{font-size:.7rem;padding:.3rem .65rem;border-radius:20px;font-weight:600;white-space:nowrap;display:inline-block}
.badge-diajukan{background:#dbeafe;color:#1d4ed8}
.badge-menunggu{background:#fef3c7;color:#92400e}
.badge-diverifikasi{background:#ede9fe;color:#5b21b6}
.badge-diproses{background:#d1fae5;color:#065f46}
.badge-info{background:#fce7f3;color:#9d174d}
.badge-selesai{background:#d1fae5;color:#065f46}
.badge-tutup{background:#f3f4f6;color:#374151}
.badge-tolak{background:#fee2e2;color:#991b1b}
.badge-default{background:#f3f4f6;color:#374151}
.btn-navy{background:var(--navy);color:#fff;border:none}
.btn-navy:hover{background:var(--navy-dark);color:#fff}
.btn-gold{background:var(--gold);color:var(--navy-dark);border:none;font-weight:600}
.btn-gold:hover{background:var(--gold-light);color:var(--navy-dark)}
.form-label{font-weight:500;font-size:.875rem;color:var(--text)}
.form-control,.form-select{border:1px solid var(--border);border-radius:8px;font-size:.875rem}
.form-control:focus,.form-select:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(26,53,97,.12)}
.table th{font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);font-weight:600;white-space:nowrap}
.table td{font-size:.875rem;vertical-align:middle}
.alert{border-radius:10px;border:none;font-size:.875rem}
.alert-success{background:#d1fae5;color:#065f46}
.alert-danger{background:#fee2e2;color:#991b1b}
.alert-warning{background:#fef3c7;color:#92400e}
.alert-info{background:#dbeafe;color:#1e40af}
.timeline{list-style:none;padding:0;margin:0}
.timeline-item{display:flex;gap:.875rem;padding-bottom:1.25rem;position:relative}
.timeline-item:not(:last-child)::before{content:'';position:absolute;left:15px;top:32px;width:2px;bottom:0;background:var(--border)}
.timeline-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;flex-shrink:0;background:var(--navy);color:#fff;z-index:1}
.timeline-dot.admin{background:var(--navy)}
.timeline-dot.pegawai{background:var(--gold);color:var(--navy-dark)}
.timeline-dot.manajemen{background:#8b5cf6}
.timeline-dot.system{background:#64748b}
.timeline-content{flex:1}
.timeline-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.3rem}
.timeline-author{font-weight:600;font-size:.8rem;color:var(--navy)}
.timeline-time{font-size:.7rem;color:var(--muted)}
.timeline-tipe{font-size:.65rem;text-transform:uppercase;letter-spacing:.05em;font-weight:600;color:var(--muted);margin-bottom:.25rem}
.timeline-msg{font-size:.825rem;color:var(--text);line-height:1.5;background:#f8f9fb;padding:.6rem .9rem;border-radius:8px;border:1px solid var(--border)}
.timeline-msg.internal{background:#fffbeb;border-color:#fde68a}
.page-header{margin-bottom:1.5rem;padding-bottom:1rem;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.page-header h1{font-size:1.3rem;font-weight:700;color:var(--navy);margin:0}
.empty-state{text-align:center;padding:3rem 1rem;color:var(--muted)}
.empty-icon{font-size:3rem;margin-bottom:.75rem;opacity:.4}
.sso-dev-banner{background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:.6rem 1rem;font-size:.78rem;color:#92400e;margin-bottom:1rem}
@media(max-width:991px){
    .sidebar{transform:translateX(-100%)}
    .sidebar.show{transform:translateX(0);box-shadow:4px 0 20px rgba(0,0,0,.2)}
    .main-content{margin-left:0}
    .topbar{left:0}
    .topbar-toggle{display:block}
    .sidebar-backdrop{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999}
    .sidebar-backdrop.show{display:block}
}
</style>
</head>
<body>
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>
<nav class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo">S</div>
    <div class="brand-name">SIKAP DPD RI</div>
    <div class="brand-sub">Keluhan Administrasi Presensi</div>
  </div>
  <div class="sidebar-nav">
    <div class="nav-section-label">Menu Utama</div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="<?= $navDash ?>" class="nav-link <?= $activeDash ?>">
          <span class="nav-icon"><i class="bi bi-speedometer2"></i></span> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $navPengaduan ?>" class="nav-link <?= $activePengaduan ?>">
          <span class="nav-icon"><i class="bi bi-clipboard2-check"></i></span>
          Pengaduan Presensi <?= $pendingBadge ?>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?= $navProfil ?>" class="nav-link <?= $activeProfil ?>">
          <span class="nav-icon"><i class="bi bi-person-circle"></i></span> Profil
        </a>
      </li>
    </ul>
  </div>
  <div class="sidebar-role">
    <span class="role-pill <?= $roleClass ?>"><?= e($roleLabel) ?></span>
    <div class="sidebar-user-name"><?= $userName ?></div>
    <div class="sidebar-user-nip">NIP: <?= $userNip ?></div>
  </div>
</nav>
<header class="topbar">
  <button class="topbar-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
  <div class="topbar-title"><?= e($pageTitle) ?></div>
  <div class="topbar-right">
    <div class="d-flex align-items-center gap-2">
      <div class="avatar"><?= $userAvatar ?></div>
      <span class="d-none d-sm-inline" style="font-size:.8rem;color:var(--muted);"><?= $userName ?></span>
    </div>
    <form method="POST" action="<?= url('logout') ?>" class="d-inline">
      <input type="hidden" name="_token" value="<?= csrf_token() ?>">
      <button type="submit" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-box-arrow-right"></i><span class="d-none d-md-inline ms-1">Keluar</span>
      </button>
    </form>
  </div>
</header>
<main class="main-content">
  <?= $alertHtml ?>
  <?= $content ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
  document.getElementById('sidebarBackdrop').classList.toggle('show');
}
</script>
</body>
</html>
    <?php
    return ob_get_clean();
}
