<?php
/**
 * Database — PDO singleton with auto-setup
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo) return self::$pdo;

        $cfg = require BASE_PATH . '/app/config.php';

        try {
            self::$pdo = new PDO(
                "mysql:host={$cfg['db_host']};port={$cfg['db_port']};dbname={$cfg['db_name']};charset=utf8mb4",
                $cfg['db_user'],
                $cfg['db_pass'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die(self::dbErrorPage($e->getMessage()));
        }

        return self::$pdo;
    }

    /** Run a query and return all rows */
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Run a query and return single row */
    public static function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Execute INSERT/UPDATE/DELETE, return last insert id or affected rows */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        $id = self::connect()->lastInsertId();
        return $id ? (int)$id : $stmt->rowCount();
    }

    /** Auto-create all tables if they don't exist */
    public static function setupSchema(): void
    {
        $pdo = self::connect();

        $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nip` VARCHAR(20) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `position` VARCHAR(255) NULL,
            `unit` VARCHAR(255) NULL,
            `role` VARCHAR(20) NOT NULL DEFAULT 'pegawai',
            `password` VARCHAR(255) NOT NULL,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `last_login_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `nip` (`nip`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `pengaduans` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `nomor_tiket` VARCHAR(20) NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `nama_pegawai` VARCHAR(255) NOT NULL,
            `nip` VARCHAR(20) NOT NULL,
            `unit_kerja` VARCHAR(255) NOT NULL,
            `kategori` VARCHAR(50) NOT NULL,
            `tanggal_kejadian` DATE NOT NULL,
            `jenis_presensi` VARCHAR(20) NOT NULL,
            `subjek` VARCHAR(255) NOT NULL,
            `deskripsi` TEXT NOT NULL,
            `status` VARCHAR(30) NOT NULL DEFAULT 'diajukan',
            `handled_by` INT UNSIGNED NULL,
            `handled_at` DATETIME NULL,
            `resolved_at` DATETIME NULL,
            `batas_waktu` DATE NULL,
            `is_overdue` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `nomor_tiket` (`nomor_tiket`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `pengaduan_responses` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `pengaduan_id` INT UNSIGNED NOT NULL,
            `user_id` INT UNSIGNED NOT NULL,
            `tipe` VARCHAR(30) NOT NULL,
            `pesan` TEXT NOT NULL,
            `status_before` VARCHAR(30) NULL,
            `status_after` VARCHAR(30) NULL,
            `visible_to_employee` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `pengaduan_attachments` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `pengaduan_id` INT UNSIGNED NOT NULL,
            `file_name` VARCHAR(255) NOT NULL,
            `file_path` VARCHAR(500) NOT NULL,
            `file_type` VARCHAR(50) NULL,
            `file_size` INT UNSIGNED NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `verifikasis` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `pengaduan_id` INT UNSIGNED NOT NULL,
            `verified_by` INT UNSIGNED NOT NULL,
            `tanggal_presensi_diklaim` DATE NOT NULL,
            `jam_masuk_diklaim` VARCHAR(10) NULL,
            `jam_keluar_diklaim` VARCHAR(10) NULL,
            `jam_masuk_sistem` VARCHAR(10) NULL,
            `jam_keluar_sistem` VARCHAR(10) NULL,
            `ada_selisih` TINYINT(1) NOT NULL DEFAULT 0,
            `detail_selisih` TEXT NULL,
            `catatan_verifikasi` TEXT NOT NULL,
            `hasil_verifikasi` VARCHAR(30) NOT NULL,
            `verified_at` DATETIME NOT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `pengaduan_id` (`pengaduan_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Seed demo accounts if users table is empty
        $count = self::queryOne("SELECT COUNT(*) as cnt FROM users")['cnt'];
        if ($count == 0) {
            self::seedDemoAccounts();
        }
    }

    private static function seedDemoAccounts(): void
    {
        $accounts = [
            ['198001012005011001','Ahmad Fauzi','ahmad.fauzi@dpd.go.id','Analis Kepegawaian','Biro Kepegawaian','pegawai','pegawai123'],
            ['197505152000031002','Siti Rahayu','siti.rahayu@dpd.go.id','Staf Administrasi','Biro Umum','pegawai','pegawai123'],
            ['196803251995121001','Budi Santoso','budi.santoso@dpd.go.id','Administrator Sistem Presensi','Biro Kepegawaian','admin','admin123'],
            ['197012101994031003','Dra. Hj. Endang Supriyati','endang.supriyati@dpd.go.id','Kepala Biro Kepegawaian','Biro Kepegawaian','manajemen','manajemen123'],
            ['198507202010011004','Rudi Hermawan','rudi.hermawan@dpd.go.id','Pranata Komputer','Bagian TI','pegawai','pegawai123'],
            ['199001152015031005','Dewi Anggraini','dewi.anggraini@dpd.go.id','Arsiparis','Biro Umum','pegawai','pegawai123'],
        ];
        foreach ($accounts as $a) {
            self::execute(
                "INSERT IGNORE INTO users (nip,name,email,position,unit,role,password) VALUES (?,?,?,?,?,?,?)",
                [$a[0],$a[1],$a[2],$a[3],$a[4],$a[5],password_hash($a[6],PASSWORD_DEFAULT)]
            );
        }
    }

    private static function dbErrorPage(string $msg): string
    {
        $cfg = BASE_PATH . '/app/config.php';
        return '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
        <title>Koneksi Database Gagal</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        </head><body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="card shadow" style="max-width:520px;width:100%;">
        <div class="card-header bg-danger text-white fw-bold">❌ Koneksi Database Gagal</div>
        <div class="card-body">
        <p><strong>Error:</strong> <code>' . htmlspecialchars($msg) . '</code></p>
        <hr>
        <p class="mb-1"><strong>Langkah perbaikan:</strong></p>
        <ol>
            <li>Pastikan MySQL / MariaDB sudah berjalan (cek XAMPP Control Panel)</li>
            <li>Buka file <code>app/config.php</code> dan sesuaikan:<br>
                <code>db_host, db_name, db_user, db_pass</code></li>
            <li>Buat database: <code>CREATE DATABASE sikap_dpd</code></li>
            <li>Refresh halaman ini — tabel akan dibuat otomatis</li>
        </ol>
        </div></div></body></html>';
    }
}

// Auto-setup schema on every request (cheap — only runs DDL if table missing)
Database::setupSchema();
