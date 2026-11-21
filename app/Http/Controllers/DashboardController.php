<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'manajemen' => $this->manajemenDashboard(),
            default => $this->pegawaiDashboard(),
        };
    }

    private function pegawaiDashboard()
    {
        $user = Auth::user();

        $stats = [
            'total' => Pengaduan::where('user_id', $user->id)->count(),
            'diajukan' => Pengaduan::where('user_id', $user->id)->where('status', 'diajukan')->count(),
            'diproses' => Pengaduan::where('user_id', $user->id)
                ->whereIn('status', ['menunggu_verifikasi', 'diverifikasi', 'diproses', 'menunggu_informasi'])
                ->count(),
            'diselesaikan' => Pengaduan::where('user_id', $user->id)
                ->whereIn('status', ['diselesaikan', 'ditutup'])
                ->count(),
        ];

        $recentPengaduans = Pengaduan::where('user_id', $user->id)
            ->with('latestResponse')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.pegawai', compact('stats', 'recentPengaduans'));
    }

    private function adminDashboard()
    {
        $stats = [
            'total' => Pengaduan::count(),
            'baru' => Pengaduan::where('status', 'diajukan')->count(),
            'menunggu_verifikasi' => Pengaduan::where('status', 'menunggu_verifikasi')->count(),
            'diproses' => Pengaduan::whereIn('status', ['diverifikasi', 'diproses'])->count(),
            'menunggu_informasi' => Pengaduan::where('status', 'menunggu_informasi')->count(),
            'diselesaikan' => Pengaduan::whereIn('status', ['diselesaikan', 'ditutup'])->count(),
            'overdue' => Pengaduan::overdue()->count(),
        ];

        // Monthly statistics for current year (last 6 months)
        $monthlyStats = $this->getMonthlyStats();

        // Category distribution
        $categoryStats = Pengaduan::select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->get()
            ->mapWithKeys(fn($item) => [
                Pengaduan::kategoriLabels()[$item->kategori] ?? $item->kategori => $item->total
            ]);

        $recentPengaduans = Pengaduan::with(['user', 'latestResponse'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.admin', compact('stats', 'monthlyStats', 'categoryStats', 'recentPengaduans'));
    }

    private function manajemenDashboard()
    {
        $total = Pengaduan::count();
        $selesai = Pengaduan::whereIn('status', ['diselesaikan', 'ditutup'])->count();
        $resolutionRate = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;

        $stats = [
            'total' => $total,
            'resolution_rate' => $resolutionRate,
            'pending' => Pengaduan::whereNotIn('status', ['diselesaikan', 'ditutup', 'ditolak'])->count(),
            'overdue' => Pengaduan::overdue()->count(),
        ];

        $monthlyStats = $this->getMonthlyStats();

        // Distribution by unit kerja
        $unitStats = Pengaduan::select('unit_kerja', DB::raw('count(*) as total'))
            ->groupBy('unit_kerja')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('dashboard.manajemen', compact('stats', 'monthlyStats', 'unitStats'));
    }

    private function getMonthlyStats(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'label' => $date->translatedFormat('M Y'),
                'total' => Pengaduan::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'selesai' => Pengaduan::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->whereIn('status', ['diselesaikan', 'ditutup'])
                    ->count(),
            ];
        }
        return $months;
    }
}
