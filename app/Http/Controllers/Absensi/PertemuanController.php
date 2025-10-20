<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\KalenderAkademik;
use App\Models\Pertemuan;
use App\Models\TahunAkademik;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PertemuanController extends Controller
{
    /**
     * Tampilkan halaman management pertemuan
     */
    public function index(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        // Statistik pertemuan
        $stats = null;
        if ($tahunAkademikId) {
            $stats = $this->getStatistikPertemuan($tahunAkademikId);
        }

        return view('master.pertemuan.index', compact('tahunAkademikList', 'tahunAkademikId', 'stats'));
    }

    /**
     * Generate pertemuan via web interface
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $tahunAkademikId = $validated['tahun_akademik_id'];
        $tahunAkademik = TahunAkademik::find($tahunAkademikId);

        try {
            // Ambil semua jadwal pelajaran untuk tahun akademik ini
            $jadwalList = JadwalPelajaran::with(['guruKelas'])
                ->whereHas('guruKelas', function ($q) use ($tahunAkademik) {
                    $q->where('tahun_akademik_id', $tahunAkademik->id)
                        ->where('aktif', true);
                })
                ->get();

            if ($jadwalList->isEmpty()) {
                return back()->with('error', 'Tidak ada jadwal pelajaran untuk tahun akademik ini!');
            }

            // Ambil semua hari libur
            $hariLibur = KalenderAkademik::where('tahun_akademik_id', $tahunAkademik->id)
                ->pluck('tanggal')
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray();

            $totalGenerated = 0;
            $totalSkipped = 0;

            DB::beginTransaction();

            foreach ($jadwalList as $jadwal) {
                $hariMap = [
                    'Minggu' => 0,
                    'Senin' => 1,
                    'Selasa' => 2,
                    'Rabu' => 3,
                    'Kamis' => 4,
                    'Jumat' => 5,
                    'Sabtu' => 6,
                ];

                $targetHari = $hariMap[$jadwal->hari];
                $currentDate = Carbon::parse($tahunAkademik->tanggal_mulai);
                $endDate = Carbon::parse($tahunAkademik->tanggal_selesai);
                $pertemuanKe = 1;

                // Loop dari tanggal mulai sampai selesai
                while ($currentDate->lte($endDate)) {
                    // Cek apakah hari sesuai dengan jadwal
                    if ($currentDate->dayOfWeek === $targetHari) {
                        $dateStr = $currentDate->format('Y-m-d');

                        // Skip jika hari libur
                        if (!in_array($dateStr, $hariLibur)) {
                            // Cek apakah pertemuan sudah ada
                            $exists = Pertemuan::where('jadwal_pelajaran_id', $jadwal->id)
                                ->where('tanggal', $dateStr)
                                ->exists();

                            if (!$exists) {
                                Pertemuan::create([
                                    'jadwal_pelajaran_id' => $jadwal->id,
                                    'tanggal' => $dateStr,
                                    'pertemuan_ke' => $pertemuanKe,
                                    'status' => 'scheduled',
                                    'generated_auto' => true,
                                ]);

                                $totalGenerated++;
                            } else {
                                $totalSkipped++;
                            }

                            $pertemuanKe++;
                        }
                    }

                    $currentDate->addDay();
                }
            }

            DB::commit();

            return back()->with('success', "✓ Berhasil generate {$totalGenerated} pertemuan baru! ({$totalSkipped} sudah ada sebelumnya)");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate pertemuan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus semua pertemuan yang belum diabsen
     */
    public function resetPertemuan(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        try {
            $deleted = Pertemuan::whereHas('jadwalPelajaran.guruKelas', function ($q) use ($validated) {
                $q->where('tahun_akademik_id', $validated['tahun_akademik_id']);
            })
                ->where('status', 'scheduled')
                ->where('generated_auto', true)
                ->delete();

            return back()->with('success', "✓ Berhasil menghapus {$deleted} pertemuan yang belum diabsen!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset pertemuan: ' . $e->getMessage());
        }
    }

    /**
     * Get statistik pertemuan
     */
    private function getStatistikPertemuan($tahunAkademikId)
    {
        $query = Pertemuan::whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
            $q->where('tahun_akademik_id', $tahunAkademikId);
        });

        return [
            'total' => $query->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'ongoing' => (clone $query)->where('status', 'ongoing')->count(),
        ];
    }

    /**
     * Lihat daftar pertemuan
     */
    public function list(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $status = $request->get('status');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        $query = Pertemuan::with([
            'jadwalPelajaran.guruKelas.guruMapel.guru',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'jadwalPelajaran.guruKelas.kelas'
        ])
            ->whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            });

        if ($status) {
            $query->where('status', $status);
        }

        if ($tanggalMulai) {
            $query->where('tanggal', '>=', $tanggalMulai);
        }

        if ($tanggalSelesai) {
            $query->where('tanggal', '<=', $tanggalSelesai);
        }

        $pertemuanList = $query->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        return view('master.pertemuan.list', compact(
            'pertemuanList',
            'tahunAkademikList',
            'tahunAkademikId',
            'status',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }
}
