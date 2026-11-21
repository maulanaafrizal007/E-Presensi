<?php
class DashboardController
{
    public function index(): void
    {
        Auth::require();
        $user = Auth::user();

        switch ($user['role']) {
            case 'admin':     $this->adminDashboard($user);    break;
            case 'manajemen': $this->manajemenDashboard($user); break;
            default:          $this->pegawaiDashboard($user);  break;
        }
    }

    private function pegawaiDashboard(array $user): void
    {
        $uid = $user['id'];
        $stats = [
            'total'       => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=?",[$uid])['c'],
            'diajukan'    => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=? AND status='diajukan'",[$uid])['c'],
            'diproses'    => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=? AND status IN('menunggu_verifikasi','diverifikasi','diproses','menunggu_informasi')",[$uid])['c'],
            'diselesaikan'=> Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE user_id=? AND status IN('diselesaikan','ditutup')",[$uid])['c'],
        ];

        $recent = Database::query(
            "SELECT p.*, (SELECT r.pesan FROM pengaduan_responses r WHERE r.pengaduan_id=p.id AND r.visible_to_employee=1 ORDER BY r.created_at DESC LIMIT 1) as last_response
             FROM pengaduans p WHERE p.user_id=? ORDER BY p.created_at DESC LIMIT 5",
            [$uid]
        );

        ob_start();
        require BASE_PATH . '/app/views/dashboard/pegawai.php';
        $content = ob_get_clean();
        echo render_layout('Dashboard', $content);
    }

    private function adminDashboard(array $user): void
    {
        $stats = [
            'total'               => Database::queryOne("SELECT COUNT(*) c FROM pengaduans")['c'],
            'baru'                => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status='diajukan'")['c'],
            'menunggu_verifikasi' => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status='menunggu_verifikasi'")['c'],
            'diproses'            => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status IN('diverifikasi','diproses')")['c'],
            'menunggu_informasi'  => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status='menunggu_informasi'")['c'],
            'diselesaikan'        => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status IN('diselesaikan','ditutup')")['c'],
            'overdue'             => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE is_overdue=1 AND status NOT IN('diselesaikan','ditutup','ditolak')")['c'],
        ];

        $monthlyStats = $this->getMonthlyStats();

        $categoryStats = Database::query(
            "SELECT kategori, COUNT(*) as total FROM pengaduans GROUP BY kategori ORDER BY total DESC"
        );

        $recent = Database::query(
            "SELECT p.*, u.name as handler_name FROM pengaduans p
             LEFT JOIN users u ON u.id = p.handled_by
             ORDER BY p.created_at DESC LIMIT 10"
        );

        ob_start();
        require BASE_PATH . '/app/views/dashboard/admin.php';
        $content = ob_get_clean();
        echo render_layout('Dashboard Administrator', $content);
    }

    private function manajemenDashboard(array $user): void
    {
        $total    = (int)Database::queryOne("SELECT COUNT(*) c FROM pengaduans")['c'];
        $selesai  = (int)Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status IN('diselesaikan','ditutup')")['c'];
        $rate     = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        $stats = [
            'total'           => $total,
            'resolution_rate' => $rate,
            'pending'         => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE status NOT IN('diselesaikan','ditutup','ditolak')")['c'],
            'overdue'         => Database::queryOne("SELECT COUNT(*) c FROM pengaduans WHERE is_overdue=1 AND status NOT IN('diselesaikan','ditutup','ditolak')")['c'],
        ];

        $monthlyStats = $this->getMonthlyStats();

        $unitStats = Database::query(
            "SELECT unit_kerja, COUNT(*) as total FROM pengaduans GROUP BY unit_kerja ORDER BY total DESC LIMIT 10"
        );

        ob_start();
        require BASE_PATH . '/app/views/dashboard/manajemen.php';
        $content = ob_get_clean();
        echo render_layout('Dashboard Manajemen', $content);
    }

    private function getMonthlyStats(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $ts    = mktime(0, 0, 0, date('n') - $i, 1, (int)date('Y'));
            $y     = date('Y', $ts);
            $m     = date('m', $ts);
            $label = $this->bulanIndo((int)$m) . ' ' . $y;
            $total = (int)Database::queryOne(
                "SELECT COUNT(*) c FROM pengaduans WHERE YEAR(created_at)=? AND MONTH(created_at)=?",
                [$y, $m]
            )['c'];
            $selesai = (int)Database::queryOne(
                "SELECT COUNT(*) c FROM pengaduans WHERE YEAR(created_at)=? AND MONTH(created_at)=? AND status IN('diselesaikan','ditutup')",
                [$y, $m]
            )['c'];
            $months[] = compact('label', 'total', 'selesai');
        }
        return $months;
    }

    private function bulanIndo(int $m): string
    {
        return ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$m];
    }
}
