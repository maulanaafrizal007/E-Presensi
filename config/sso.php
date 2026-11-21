<?php

return [
    'sso_mode' => env('SSO_MODE', 'dev'), // 'dev' or 'production'
    'sso_url' => env('SSO_URL', ''),
    'sso_client_id' => env('SSO_CLIENT_ID', ''),
    'sso_client_secret' => env('SSO_CLIENT_SECRET', ''),
    'sso_redirect_uri' => env('SSO_REDIRECT_URI', ''),

    // Development mode demo accounts
    'dev_accounts' => [
        [
            'nip' => '198001012005011001',
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@dpd.go.id',
            'position' => 'Analis Kepegawaian',
            'unit' => 'Biro Kepegawaian',
            'role' => 'pegawai',
            'password' => 'pegawai123',
        ],
        [
            'nip' => '197505152000031002',
            'name' => 'Siti Rahayu',
            'email' => 'siti.rahayu@dpd.go.id',
            'position' => 'Staf Administrasi',
            'unit' => 'Biro Umum',
            'role' => 'pegawai',
            'password' => 'pegawai123',
        ],
        [
            'nip' => '196803251995121001',
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@dpd.go.id',
            'position' => 'Administrator Sistem Presensi',
            'unit' => 'Biro Kepegawaian',
            'role' => 'admin',
            'password' => 'admin123',
        ],
        [
            'nip' => '197012101994031003',
            'name' => 'Dra. Hj. Endang Supriyati',
            'email' => 'endang.supriyati@dpd.go.id',
            'position' => 'Kepala Biro Kepegawaian',
            'unit' => 'Biro Kepegawaian',
            'role' => 'manajemen',
            'password' => 'manajemen123',
        ],
    ],
];
