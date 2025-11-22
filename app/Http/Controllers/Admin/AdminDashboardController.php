<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\RemidiSiswa;
use App\Models\PenilaianMapel;
use App\Models\TahunAkademik;
use App\Models\Pertemuan;
use App\Services\EligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    protected $eligibilityService;

    public function __construct(EligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    public function index()
    {
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', true)->first();
        $semester = $tahunAkademikAktif?->semester ?? 'ganjil';

        // Statistik Umum
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();

        // Statistik Absensi Hari Ini
        $today = Carbon::today();
        $pertemuanHariIni = Pertemuan::whereDate('tanggal', $today)->pluck('id');
        $absensiHariIni = Absensi::whereIn('pertemuan_id', $pertemuanHariIni)
            ->select('status_kehadiran', DB::raw('count(*) as total'))
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran');

        // Statistik Remidi
        $totalRemidi = RemidiSiswa::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)->count();
        $remidiPending = RemidiSiswa::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('status_remidi', 'pending')->count();
        $remidiSelesai = RemidiSiswa::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('status_remidi', 'selesai')->count();

        // Siswa yang perlu Remidi (10 terbaru)
        $siswaRemidi = RemidiSiswa::with(['siswa', 'guruKelas.guruMapel.mapel', 'jenisUjian'])
            ->where('status_remidi', 'pending')
            ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->latest()
            ->limit(10)
            ->get();

        // Grafik Nilai per Bulan (6 bulan terakhir)
        $nilaiPerBulan = PenilaianMapel::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(DB::raw('MONTH(created_at) as bulan'), DB::raw('AVG(nilai) as rata_rata'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // ============================================
        // ELIGIBILITY - SISWA TIDAK LAYAK UJIAN
        // ============================================
        $eligibilityData = $this->getEligibilityStatistics($tahunAkademikAktif);

        return view('dashboard.admin', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'absensiHariIni',
            'siswaRemidi',
            'nilaiPerBulan',
            'tahunAkademikAktif',
            'semester',
            'totalRemidi',
            'remidiPending',
            'remidiSelesai',
            'eligibilityData'
        ));
    }

    /**
     * Get eligibility statistics for all active students
     */
    protected function getEligibilityStatistics(?TahunAkademik $tahunAkademik): array
    {
        if (!$tahunAkademik) {
            return [
                'total_siswa' => 0,
                'layak' => 0,
                'tidak_layak' => 0,
                'persentase_layak' => 0,
                'siswa_tidak_layak' => collect(),
                'per_kelas' => collect(),
                'per_kategori' => [
                    'mapel' => 0,
                    'kedisiplinan' => 0,
                    'keagamaan' => 0,
                ],
            ];
        }

        $siswaAktif = Siswa::where('status', 'aktif')
            ->with(['currentClass', 'riwayatKelas' => function ($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik->id);
            }])
            ->get();

        $layak = 0;
        $tidakLayak = 0;
        $siswaTidakLayak = [];
        $perKelas = [];
        $perKategori = ['mapel' => 0, 'kedisiplinan' => 0, 'keagamaan' => 0];

        foreach ($siswaAktif as $siswa) {
            $eligibility = $this->eligibilityService->getEligibilityForDashboard($siswa->id);

            if ($eligibility['eligible']) {
                $layak++;
            } else {
                $tidakLayak++;

                // Collect siswa tidak layak
                $siswaTidakLayak[] = [
                    'siswa' => $siswa,
                    'kelas' => $siswa->currentClass,
                    'issues' => $eligibility['issues'],
                    'summary' => $eligibility['summary'],
                    'has_mapel_issues' => $eligibility['has_mapel_issues'],
                    'has_kedisiplinan_issues' => $eligibility['has_kedisiplinan_issues'],
                    'has_keagamaan_issues' => $eligibility['has_keagamaan_issues'],
                ];

                // Count per kategori
                if ($eligibility['has_mapel_issues']) $perKategori['mapel']++;
                if ($eligibility['has_kedisiplinan_issues']) $perKategori['kedisiplinan']++;
                if ($eligibility['has_keagamaan_issues']) $perKategori['keagamaan']++;

                // Count per kelas
                $kelasId = $siswa->current_class_id;
                $kelasNama = $siswa->currentClass->nama_lengkap ?? 'Tanpa Kelas';
                if (!isset($perKelas[$kelasId])) {
                    $perKelas[$kelasId] = [
                        'nama' => $kelasNama,
                        'total' => 0,
                        'tidak_layak' => 0,
                    ];
                }
                $perKelas[$kelasId]['tidak_layak']++;
            }

            // Count total per kelas
            $kelasId = $siswa->current_class_id;
            $kelasNama = $siswa->currentClass->nama_lengkap ?? 'Tanpa Kelas';
            if (!isset($perKelas[$kelasId])) {
                $perKelas[$kelasId] = [
                    'nama' => $kelasNama,
                    'total' => 0,
                    'tidak_layak' => 0,
                ];
            }
            $perKelas[$kelasId]['total']++;
        }

        // Sort siswa tidak layak by kelas
        usort($siswaTidakLayak, function ($a, $b) {
            return strcmp($a['kelas']->nama_lengkap ?? '', $b['kelas']->nama_lengkap ?? '');
        });

        // Calculate persentase per kelas
        foreach ($perKelas as &$kelas) {
            $kelas['persentase_tidak_layak'] = $kelas['total'] > 0
                ? round(($kelas['tidak_layak'] / $kelas['total']) * 100, 1)
                : 0;
        }

        $totalSiswa = count($siswaAktif);

        return [
            'total_siswa' => $totalSiswa,
            'layak' => $layak,
            'tidak_layak' => $tidakLayak,
            'persentase_layak' => $totalSiswa > 0 ? round(($layak / $totalSiswa) * 100, 1) : 0,
            'persentase_tidak_layak' => $totalSiswa > 0 ? round(($tidakLayak / $totalSiswa) * 100, 1) : 0,
            'siswa_tidak_layak' => collect($siswaTidakLayak)->take(20), // Limit 20 untuk dashboard
            'per_kelas' => collect($perKelas)->sortByDesc('tidak_layak'),
            'per_kategori' => $perKategori,
        ];
    }
}
