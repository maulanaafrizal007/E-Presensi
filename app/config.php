<?php
/**
 * SIKAP DPD RI - Konfigurasi Aplikasi
 *
 * Sesuaikan db_name, db_user, db_pass dengan pengaturan MySQL Anda.
 */
return [
    // ---- Database ----
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'sikap_dpd',
    'db_user' => 'root',
    'db_pass' => '',          // Kosong untuk XAMPP default

    // ---- Aplikasi ----
    'app_name' => 'SIKAP DPD RI',
    'app_debug' => true,

    // ---- SSO ----
    // 'dev'        = gunakan akun demo lokal (tidak perlu konfigurasi tambahan)
    // 'production' = hubungkan ke SSO institusional DPD RI
    'sso_mode' => 'dev',

    // ---- Upload ----
    'upload_path'    => __DIR__ . '/../../uploads/',
    'upload_max_mb'  => 5,
    'upload_allowed' => ['jpg','jpeg','png','pdf','doc','docx'],
];
