-- =============================================================
-- SIKAP DPD RI - Database Setup Script
-- Sekretariat Jenderal DPD RI
-- =============================================================
-- Run this script after: php artisan migrate --seed
-- Or use this for manual database setup
-- =============================================================

CREATE DATABASE IF NOT EXISTS `sikap_dpd`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `sikap_dpd`;

-- =============================================================
-- USERS TABLE
-- =============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nip` VARCHAR(20) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `position` VARCHAR(255) NULL,
    `unit` VARCHAR(255) NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'pegawai',
    `password` VARCHAR(255) NOT NULL,
    `sso_id` VARCHAR(255) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` TIMESTAMP NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_nip_unique` (`nip`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_sso_id_unique` (`sso_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PENGADUANS TABLE
-- =============================================================
CREATE TABLE IF NOT EXISTS `pengaduans` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nomor_tiket` VARCHAR(20) NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `nama_pegawai` VARCHAR(255) NOT NULL,
    `nip` VARCHAR(20) NOT NULL,
    `unit_kerja` VARCHAR(255) NOT NULL,
    `kategori` VARCHAR(50) NOT NULL,
    `tanggal_kejadian` DATE NOT NULL,
    `jenis_presensi` VARCHAR(20) NOT NULL,
    `subjek` VARCHAR(255) NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `status` VARCHAR(30) NOT NULL DEFAULT 'diajukan',
    `prioritas` VARCHAR(20) NOT NULL DEFAULT 'normal',
    `handled_by` BIGINT UNSIGNED NULL,
    `handled_at` TIMESTAMP NULL,
    `resolved_at` TIMESTAMP NULL,
    `closed_at` TIMESTAMP NULL,
    `batas_waktu` DATE NULL,
    `is_overdue` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `pengaduans_nomor_tiket_unique` (`nomor_tiket`),
    INDEX `pengaduans_status_created_at_index` (`status`, `created_at`),
    INDEX `pengaduans_user_id_status_index` (`user_id`, `status`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`handled_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PENGADUAN RESPONSES TABLE
-- =============================================================
CREATE TABLE IF NOT EXISTS `pengaduan_responses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengaduan_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `tipe` VARCHAR(30) NOT NULL,
    `pesan` TEXT NOT NULL,
    `status_before` VARCHAR(30) NULL,
    `status_after` VARCHAR(30) NULL,
    `visible_to_employee` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduans`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- PENGADUAN ATTACHMENTS TABLE
-- =============================================================
CREATE TABLE IF NOT EXISTS `pengaduan_attachments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengaduan_id` BIGINT UNSIGNED NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_path` VARCHAR(500) NOT NULL,
    `file_type` VARCHAR(50) NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `uploaded_by_role` VARCHAR(20) NOT NULL DEFAULT 'pegawai',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduans`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- VERIFIKASIS TABLE
-- =============================================================
CREATE TABLE IF NOT EXISTS `verifikasis` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `pengaduan_id` BIGINT UNSIGNED NOT NULL,
    `verified_by` BIGINT UNSIGNED NOT NULL,
    `tanggal_presensi_diklaim` DATE NOT NULL,
    `jam_masuk_diklaim` VARCHAR(10) NULL,
    `jam_keluar_diklaim` VARCHAR(10) NULL,
    `jam_masuk_sistem` VARCHAR(10) NULL,
    `jam_keluar_sistem` VARCHAR(10) NULL,
    `ada_selisih` TINYINT(1) NOT NULL DEFAULT 0,
    `detail_selisih` TEXT NULL,
    `catatan_verifikasi` TEXT NOT NULL,
    `hasil_verifikasi` VARCHAR(30) NOT NULL,
    `verified_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `verifikasis_pengaduan_id_unique` (`pengaduan_id`),
    FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduans`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`verified_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================
-- MIGRATIONS TABLE (Laravel tracking)
-- =============================================================
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- SESSIONS TABLE
-- =============================================================
CREATE TABLE IF NOT EXISTS `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================================
-- SEED: Demo Accounts (password hashed with bcrypt)
-- Password for 'pegawai123': $2y$12$...
-- Run: php artisan db:seed after migrate for auto-seeding
-- =============================================================
-- Note: Use `php artisan migrate --seed` instead of running this manually
-- The seeder will create all demo accounts automatically.
