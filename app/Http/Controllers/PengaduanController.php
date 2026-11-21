<?php

namespace App\Http\Controllers;

use App\Models\Pengaduan;
use App\Models\PengaduanResponse;
use App\Models\PengaduanAttachment;
use App\Models\Verifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PengaduanController extends Controller
{
    // ============================================================
    // SHARED: INDEX (role-based routing)
    // ============================================================
    public function index(Request $request)
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin', 'manajemen' => $this->adminIndex($request),
            default => $this->pegawaiIndex($request),
        };
    }

    // ============================================================
    // PEGAWAI: list own complaints
    // ============================================================
    private function pegawaiIndex(Request $request)
    {
        $query = Pengaduan::where('user_id', Auth::id())
            ->with('latestResponse')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $pengaduans = $query->paginate(10)->withQueryString();
        $statuses = Pengaduan::statusLabels();
        $kategoris = Pengaduan::kategoriLabels();

        return view('pengaduan.index_pegawai', compact('pengaduans', 'statuses', 'kategoris'));
    }

    // ============================================================
    // ADMIN/MANAJEMEN: list all complaints
    // ============================================================
    private function adminIndex(Request $request)
    {
        $query = Pengaduan::with(['user', 'handler', 'latestResponse'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pegawai', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nomor_tiket', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        $pengaduans = $query->paginate(15)->withQueryString();
        $statuses = Pengaduan::statusLabels();
        $kategoris = Pengaduan::kategoriLabels();

        return view('pengaduan.index_admin', compact('pengaduans', 'statuses', 'kategoris'));
    }

    // ============================================================
    // PEGAWAI: show complaint form
    // ============================================================
    public function create()
    {
        $this->authorize_role(['pegawai']);
        $kategoris = Pengaduan::kategoriLabels();
        $jenisPresensi = Pengaduan::jenisPresensiLabels();
        return view('pengaduan.create', compact('kategoris', 'jenisPresensi'));
    }

    // ============================================================
    // PEGAWAI: store new complaint
    // ============================================================
    public function store(Request $request)
    {
        $this->authorize_role(['pegawai']);

        $validated = $request->validate([
            'kategori' => 'required|in:terlambat,pulang_cepat,tidak_tercatat,lupa_absen,gangguan_sistem,lainnya',
            'tanggal_kejadian' => 'required|date|before_or_equal:today',
            'jenis_presensi' => 'required|in:masuk,keluar,keduanya',
            'subjek' => 'required|string|max:255',
            'deskripsi' => 'required|string|min:20|max:2000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ], [
            'kategori.required' => 'Kategori pengaduan wajib dipilih.',
            'tanggal_kejadian.required' => 'Tanggal kejadian wajib diisi.',
            'tanggal_kejadian.before_or_equal' => 'Tanggal kejadian tidak boleh di masa depan.',
            'jenis_presensi.required' => 'Jenis presensi wajib dipilih.',
            'subjek.required' => 'Subjek pengaduan wajib diisi.',
            'deskripsi.required' => 'Deskripsi pengaduan wajib diisi.',
            'deskripsi.min' => 'Deskripsi minimal 20 karakter.',
            'attachments.*.mimes' => 'File hanya boleh berformat JPG, PNG, PDF, DOC, atau DOCX.',
            'attachments.*.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $user = Auth::user();

        $pengaduan = DB::transaction(function () use ($validated, $request, $user) {
            $pengaduan = Pengaduan::create([
                'user_id' => $user->id,
                'nama_pegawai' => $user->name,
                'nip' => $user->nip,
                'unit_kerja' => $user->unit,
                'kategori' => $validated['kategori'],
                'tanggal_kejadian' => $validated['tanggal_kejadian'],
                'jenis_presensi' => $validated['jenis_presensi'],
                'subjek' => $validated['subjek'],
                'deskripsi' => $validated['deskripsi'],
                'status' => Pengaduan::STATUS_DIAJUKAN,
            ]);

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('pengaduan-attachments/' . $pengaduan->id, 'public');
                    PengaduanAttachment::create([
                        'pengaduan_id' => $pengaduan->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by_role' => 'pegawai',
                    ]);
                }
            }

            return $pengaduan;
        });

        return redirect()->route('pengaduan.show', $pengaduan)
            ->with('success', "Pengaduan berhasil diajukan dengan nomor tiket {$pengaduan->nomor_tiket}.");
    }

    // ============================================================
    // SHARED: show complaint detail
    // ============================================================
    public function show(Pengaduan $pengaduan)
    {
        $user = Auth::user();

        // Pegawai can only see their own complaints
        if ($user->isPegawai() && $pengaduan->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke pengaduan ini.');
        }

        $pengaduan->load(['user', 'handler', 'responses.user', 'attachments', 'verifikasi.verifier']);

        $visibleResponses = $user->isPegawai()
            ? $pengaduan->responses->where('visible_to_employee', true)
            : $pengaduan->responses;

        $statuses = Pengaduan::statusLabels();

        return view('pengaduan.show', compact('pengaduan', 'visibleResponses', 'statuses'));
    }

    // ============================================================
    // ADMIN: update status / respond
    // ============================================================
    public function respond(Request $request, Pengaduan $pengaduan)
    {
        $this->authorize_role(['admin']);

        $request->validate([
            'tipe' => 'required|in:response,internal_note,status_change,verification,request_info,resolution',
            'pesan' => 'required|string|min:5|max:2000',
            'new_status' => 'nullable|in:diajukan,menunggu_verifikasi,diverifikasi,diproses,menunggu_informasi,diselesaikan,ditutup,ditolak',
        ], [
            'tipe.required' => 'Tipe respons wajib dipilih.',
            'pesan.required' => 'Pesan respons wajib diisi.',
        ]);

        DB::transaction(function () use ($request, $pengaduan) {
            $user = Auth::user();
            $statusBefore = $pengaduan->status;

            PengaduanResponse::create([
                'pengaduan_id' => $pengaduan->id,
                'user_id' => $user->id,
                'tipe' => $request->tipe,
                'pesan' => $request->pesan,
                'status_before' => $statusBefore,
                'status_after' => $request->new_status ?? $statusBefore,
                'visible_to_employee' => $request->tipe !== 'internal_note',
            ]);

            if ($request->filled('new_status') && $request->new_status !== $statusBefore) {
                $updates = ['status' => $request->new_status];

                if (!$pengaduan->handled_by) {
                    $updates['handled_by'] = $user->id;
                    $updates['handled_at'] = now();
                }

                if (in_array($request->new_status, ['diselesaikan', 'ditutup'])) {
                    $updates['resolved_at'] = now();
                }

                if ($request->new_status === 'ditutup') {
                    $updates['closed_at'] = now();
                }

                $pengaduan->update($updates);
            }
        });

        return back()->with('success', 'Respons berhasil disimpan.');
    }

    // ============================================================
    // ADMIN: manual verification
    // ============================================================
    public function storeVerifikasi(Request $request, Pengaduan $pengaduan)
    {
        $this->authorize_role(['admin']);

        $request->validate([
            'tanggal_presensi_diklaim' => 'required|date',
            'jam_masuk_diklaim' => 'nullable|string',
            'jam_keluar_diklaim' => 'nullable|string',
            'jam_masuk_sistem' => 'nullable|string',
            'jam_keluar_sistem' => 'nullable|string',
            'ada_selisih' => 'required|boolean',
            'detail_selisih' => 'nullable|string|max:1000',
            'catatan_verifikasi' => 'required|string|min:10|max:2000',
            'hasil_verifikasi' => 'required|in:valid,tidak_valid,perlu_investigasi',
        ], [
            'catatan_verifikasi.required' => 'Catatan verifikasi wajib diisi.',
            'hasil_verifikasi.required' => 'Hasil verifikasi wajib dipilih.',
        ]);

        DB::transaction(function () use ($request, $pengaduan) {
            Verifikasi::updateOrCreate(
                ['pengaduan_id' => $pengaduan->id],
                [
                    'verified_by' => Auth::id(),
                    'tanggal_presensi_diklaim' => $request->tanggal_presensi_diklaim,
                    'jam_masuk_diklaim' => $request->jam_masuk_diklaim,
                    'jam_keluar_diklaim' => $request->jam_keluar_diklaim,
                    'jam_masuk_sistem' => $request->jam_masuk_sistem,
                    'jam_keluar_sistem' => $request->jam_keluar_sistem,
                    'ada_selisih' => $request->ada_selisih,
                    'detail_selisih' => $request->detail_selisih,
                    'catatan_verifikasi' => $request->catatan_verifikasi,
                    'hasil_verifikasi' => $request->hasil_verifikasi,
                    'verified_at' => now(),
                ]
            );

            // Auto-advance status to verified
            if ($pengaduan->status === Pengaduan::STATUS_DIAJUKAN ||
                $pengaduan->status === Pengaduan::STATUS_MENUNGGU_VERIFIKASI) {
                $pengaduan->update([
                    'status' => Pengaduan::STATUS_DIVERIFIKASI,
                    'handled_by' => Auth::id(),
                    'handled_at' => now(),
                ]);
            }

            PengaduanResponse::create([
                'pengaduan_id' => $pengaduan->id,
                'user_id' => Auth::id(),
                'tipe' => 'verification',
                'pesan' => "Verifikasi manual selesai dilakukan. Hasil: " . Verifikasi::hasilLabels()[$request->hasil_verifikasi],
                'status_before' => $pengaduan->status,
                'status_after' => Pengaduan::STATUS_DIVERIFIKASI,
                'visible_to_employee' => true,
            ]);
        });

        return back()->with('success', 'Hasil verifikasi berhasil disimpan.');
    }

    // ============================================================
    // Helper: authorize by role
    // ============================================================
    private function authorize_role(array $roles): void
    {
        $user = Auth::user();
        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }
    }
}
