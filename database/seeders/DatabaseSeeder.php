<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pengaduan;
use App\Models\PengaduanResponse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users from SSO dev config
        $devAccounts = config('sso.dev_accounts', []);

        foreach ($devAccounts as $account) {
            User::updateOrCreate(
                ['nip' => $account['nip']],
                [
                    'name' => $account['name'],
                    'email' => $account['email'],
                    'position' => $account['position'],
                    'unit' => $account['unit'],
                    'role' => $account['role'],
                    'password' => Hash::make($account['password']),
                    'is_active' => true,
                ]
            );
        }

        // Create additional sample employees
        $units = [
            'Biro Kepegawaian',
            'Biro Umum',
            'Biro Keuangan',
            'Biro Hukum',
            'Biro Hubungan Masyarakat',
            'Pusat Kajian DPD RI',
            'Bagian Teknologi Informasi',
        ];

        $positions = [
            'Staf Administrasi',
            'Analis Kepegawaian',
            'Pranata Komputer',
            'Arsiparis',
            'Perencana',
            'Analis Keuangan',
        ];

        for ($i = 1; $i <= 15; $i++) {
            $nip = '19' . rand(70, 99) . '0' . rand(1, 9) . rand(10, 28) . rand(1995, 2010) . '0' . rand(1, 2) . str_pad($i, 3, '0', STR_PAD_LEFT);
            $nip = substr($nip, 0, 18);

            User::updateOrCreate(
                ['nip' => $nip],
                [
                    'name' => 'Pegawai Demo ' . $i,
                    'email' => 'pegawai.demo' . $i . '@dpd.go.id',
                    'position' => $positions[array_rand($positions)],
                    'unit' => $units[array_rand($units)],
                    'role' => 'pegawai',
                    'password' => Hash::make('pegawai123'),
                    'is_active' => true,
                ]
            );
        }

        // Create sample complaints
        $this->createSamplePengaduans();

        $this->command->info('✅ Database seeded with SIKAP DPD RI sample data.');
    }

    private function createSamplePengaduans(): void
    {
        $employees = User::where('role', 'pegawai')->get();
        $admin = User::where('role', 'admin')->first();

        if ($employees->isEmpty()) return;

        $kategoris = ['terlambat', 'pulang_cepat', 'tidak_tercatat', 'lupa_absen', 'gangguan_sistem', 'lainnya'];
        $jenisPresensi = ['masuk', 'keluar', 'keduanya'];
        $statuses = [
            Pengaduan::STATUS_DIAJUKAN,
            Pengaduan::STATUS_MENUNGGU_VERIFIKASI,
            Pengaduan::STATUS_DIVERIFIKASI,
            Pengaduan::STATUS_DIPROSES,
            Pengaduan::STATUS_DISELESAIKAN,
            Pengaduan::STATUS_DITOLAK,
        ];

        $subjects = [
            'Presensi tidak terekam saat check-in',
            'Keterlambatan akibat gangguan transportasi',
            'Fingerprint tidak terbaca',
            'Server e-presensi down saat jam masuk',
            'Mesin presensi rusak di lantai 3',
            'Data presensi tidak sinkron',
        ];

        $descriptions = [
            'Pada tanggal tersebut saya telah hadir di kantor namun mesin fingerprint tidak merekam kehadiran saya. Saya memerlukan perbaikan data presensi.',
            'Saya mengalami keterlambatan karena kondisi lalu lintas yang sangat macet akibat banjir. Mohon diproses verifikasi.',
            'Fingerprint saya tidak terbaca oleh mesin presensi sehingga data tidak tercatat. Ini bukan pertama kali terjadi.',
            'Server e-presensi mengalami gangguan sehingga data presensi tidak tersimpan meskipun saya sudah melakukan absen.',
        ];

        for ($i = 0; $i < 20; $i++) {
            $employee = $employees->random();
            $status = $statuses[array_rand($statuses)];
            $kategori = $kategoris[array_rand($kategoris)];
            $kejadian = now()->subDays(rand(1, 60));

            $pengaduan = Pengaduan::create([
                'user_id' => $employee->id,
                'nama_pegawai' => $employee->name,
                'nip' => $employee->nip,
                'unit_kerja' => $employee->unit,
                'kategori' => $kategori,
                'tanggal_kejadian' => $kejadian->toDateString(),
                'jenis_presensi' => $jenisPresensi[array_rand($jenisPresensi)],
                'subjek' => $subjects[array_rand($subjects)],
                'deskripsi' => $descriptions[array_rand($descriptions)],
                'status' => $status,
                'handled_by' => in_array($status, ['diajukan']) ? null : ($admin ? $admin->id : null),
                'handled_at' => in_array($status, ['diajukan']) ? null : $kejadian->addDays(1),
                'resolved_at' => in_array($status, ['diselesaikan', 'ditutup']) ? $kejadian->addDays(5) : null,
                'is_overdue' => rand(0, 5) === 0,
            ]);

            // Add sample responses for non-new complaints
            if ($admin && !in_array($status, ['diajukan'])) {
                PengaduanResponse::create([
                    'pengaduan_id' => $pengaduan->id,
                    'user_id' => $admin->id,
                    'tipe' => 'response',
                    'pesan' => 'Pengaduan Anda sedang kami proses. Tim verifikasi akan segera melakukan pengecekan.',
                    'status_before' => 'diajukan',
                    'status_after' => $status,
                    'visible_to_employee' => true,
                ]);
            }
        }
    }
}
