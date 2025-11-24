<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\KalenderAkademik;
use App\Models\Pertemuan;
use App\Models\TahunAkademik;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PertemuanController extends Controller
{
    /**
     * Tampilkan halaman management pertemuan
     */
    public function index(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $semester = $request->get('semester');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        } else {
            $tahunAkademik = TahunAkademik::find($tahunAkademikId);
        }

        // Auto-detect semester jika tidak dipilih
        if (!$semester && $tahunAkademik) {
            $semester = $tahunAkademik->semester;
        }

        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        // Statistik pertemuan
        $stats = null;
        if ($tahunAkademikId && $semester) {
            $stats = $this->getStatistikPertemuan($tahunAkademikId, $semester);
        }

        return view('master.pertemuan.index', compact(
            'tahunAkademikList',
            'tahunAkademikId',
            'semester',
            'stats',
            'tahunAkademik'
        ));
    }

    /**
     * Generate pertemuan via web interface
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
        ]);

        $tahunAkademik = TahunAkademik::find($validated['tahun_akademik_id']);

        try {
            DB::beginTransaction();

            $totalGeneratedGanjil = 0;
            $totalSkippedGanjil = 0;
            $totalGeneratedGenap = 0;
            $totalSkippedGenap = 0;

            // Generate untuk SEMESTER GANJIL
            if ($tahunAkademik->tanggal_mulai_ganjil && $tahunAkademik->tanggal_selesai_ganjil) {
                $result = $this->generateSemester($tahunAkademik, 'ganjil');
                $totalGeneratedGanjil = $result['generated'];
                $totalSkippedGanjil = $result['skipped'];
            }

            // Generate untuk SEMESTER GENAP
            if ($tahunAkademik->tanggal_mulai_genap && $tahunAkademik->tanggal_selesai_genap) {
                $result = $this->generateSemester($tahunAkademik, 'genap');
                $totalGeneratedGenap = $result['generated'];
                $totalSkippedGenap = $result['skipped'];
            }

            DB::commit();

            $totalGenerated = $totalGeneratedGanjil + $totalGeneratedGenap;
            $totalSkipped = $totalSkippedGanjil + $totalSkippedGenap;

            $message = "✓ Berhasil generate pertemuan untuk {$tahunAkademik->nama_tahun_akademik}!<br>";
            $message .= "• Semester Ganjil: {$totalGeneratedGanjil} pertemuan baru<br>";
            $message .= "• Semester Genap: {$totalGeneratedGenap} pertemuan baru<br>";
            $message .= "• Total: {$totalGenerated} pertemuan ({$totalSkipped} sudah ada sebelumnya)";

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal generate pertemuan: ' . $e->getMessage());
        }
    }

    /**
     * Generate pertemuan untuk satu semester
     */
    private function generateSemester(TahunAkademik $tahunAkademik, string $semester): array
    {
        $tanggalMulai = $tahunAkademik->getTanggalMulai($semester);
        $tanggalSelesai = $tahunAkademik->getTanggalSelesai($semester);

        if (!$tanggalMulai || !$tanggalSelesai) {
            return ['generated' => 0, 'skipped' => 0];
        }

        // Ambil semua jadwal pelajaran untuk semester ini
        $jadwalList = JadwalPelajaran::with(['guruKelas'])
            ->whereHas('guruKelas', function ($q) use ($tahunAkademik, $semester) {
                $q->where('tahun_akademik_id', $tahunAkademik->id)
                    ->where('aktif', true);
            })
            ->get();

        if ($jadwalList->isEmpty()) {
            return ['generated' => 0, 'skipped' => 0];
        }

        // Ambil semua hari libur untuk semester ini
        $hariLibur = KalenderAkademik::where('tahun_akademik_id', $tahunAkademik->id)
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->pluck('tanggal')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        $totalGenerated = 0;
        $totalSkipped = 0;

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
            $currentDate = Carbon::parse($tanggalMulai);
            $endDate = Carbon::parse($tanggalSelesai);
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
                            ->where('semester', $semester)
                            ->exists();

                        if (!$exists) {
                            Pertemuan::create([
                                'jadwal_pelajaran_id' => $jadwal->id,
                                'tanggal' => $dateStr,
                                'pertemuan_ke' => $pertemuanKe,
                                'status' => 'scheduled',
                                'semester' => $semester,
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

        return [
            'generated' => $totalGenerated,
            'skipped' => $totalSkipped,
        ];
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
            $deletedGanjil = Pertemuan::whereHas('jadwalPelajaran.guruKelas', function ($q) use ($validated) {
                $q->where('tahun_akademik_id', $validated['tahun_akademik_id'])
                    ->where('semester', 'ganjil');
            })
                ->where('semester', 'ganjil')
                ->where('status', 'scheduled')
                ->where('generated_auto', true)
                ->delete();

            $deletedGenap = Pertemuan::whereHas('jadwalPelajaran.guruKelas', function ($q) use ($validated) {
                $q->where('tahun_akademik_id', $validated['tahun_akademik_id'])
                    ->where('semester', 'genap');
            })
                ->where('semester', 'genap')
                ->where('status', 'scheduled')
                ->where('generated_auto', true)
                ->delete();

            $total = $deletedGanjil + $deletedGenap;

            $message = "✓ Berhasil menghapus {$total} pertemuan yang belum diabsen!<br>";
            $message .= "• Semester Ganjil: {$deletedGanjil} pertemuan<br>";
            $message .= "• Semester Genap: {$deletedGenap} pertemuan";

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset pertemuan: ' . $e->getMessage());
        }
    }

    /**
     * Get statistik pertemuan
     */
    private function getStatistikPertemuan($tahunAkademikId, $semester)
    {
        $query = Pertemuan::whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId, $semester) {
            $q->where('tahun_akademik_id', $tahunAkademikId)
                ->where('semester', $semester);
        })->where('semester', $semester);

        // Hitung juga total keseluruhan (kedua semester)
        $queryTotal = Pertemuan::whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
            $q->where('tahun_akademik_id', $tahunAkademikId);
        });

        return [
            'total' => $query->count(),
            'scheduled' => (clone $query)->where('status', 'scheduled')->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'ongoing' => (clone $query)->where('status', 'ongoing')->count(),
            // Total keseluruhan
            'total_all' => $queryTotal->count(),
            'total_ganjil' => (clone $queryTotal)->where('semester', 'ganjil')->count(),
            'total_genap' => (clone $queryTotal)->where('semester', 'genap')->count(),
        ];
    }

    /**
     * Lihat daftar pertemuan
     */
    public function list(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $semester = $request->get('semester');
        $status = $request->get('status');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        } else {
            $tahunAkademik = TahunAkademik::find($tahunAkademikId);
        }

        // Auto-detect semester
        if (!$semester && $tahunAkademik) {
            $semester = $tahunAkademik->semester;
        }

        $query = Pertemuan::with([
            'jadwalPelajaran.guruKelas.guruMapel.guru',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'jadwalPelajaran.guruKelas.kelas'
        ])
            ->whereHas('jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId, $semester) {
                $q->where('tahun_akademik_id', $tahunAkademikId)
                    ->where('semester', $semester);
            })
            ->where('semester', $semester);

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
            'semester',
            'status',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }
}
