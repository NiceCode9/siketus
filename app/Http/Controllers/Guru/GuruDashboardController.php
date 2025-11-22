<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruKelas;
use App\Models\JadwalPelajaran;
use App\Models\Pertemuan;
use App\Models\Absensi;
use App\Models\RemidiSiswa;
use App\Models\PenilaianMapel;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use App\Services\EligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    protected $eligibilityService;

    public function __construct(EligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    public function index()
    {
        $user = Auth::user();
        $guru = $user->guru;
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', true)->first();

        // Kelas yang diajar
        $kelasYangDiajar = GuruKelas::with(['kelas', 'guruMapel.mapel'])
            ->whereHas('guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('aktif', true)
            ->get();

        // Jadwal mengajar hari ini
        $hariIni = Carbon::now()->locale('id')->dayName;
        $jadwalHariIni = JadwalPelajaran::with(['guruKelas.kelas', 'guruKelas.guruMapel.mapel'])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Pertemuan terbaru dengan absensi
        $pertemuanTerbaru = Pertemuan::with(['jadwalPelajaran.guruKelas.kelas', 'absensi'])
            ->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->latest('tanggal')
            ->limit(5)
            ->get();

        // Siswa yang perlu remidi
        $guruKelasIds = $kelasYangDiajar->pluck('id');
        $siswaRemidi = RemidiSiswa::with(['siswa', 'guruKelas.kelas', 'jenisUjian'])
            ->whereIn('guru_kelas_id', $guruKelasIds)
            ->where('status_remidi', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Statistik Nilai per Kelas
        $statistikNilai = [];
        foreach ($kelasYangDiajar as $guruKelas) {
            $avgNilai = PenilaianMapel::where('guru_kelas_id', $guruKelas->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
                ->avg('nilai');

            $statistikNilai[] = [
                'kelas' => $guruKelas->kelas->nama_lengkap ?? '-',
                'mapel' => $guruKelas->guruMapel->mapel->nama_mapel ?? '-',
                'rata_rata' => round($avgNilai, 2) ?? 0,
            ];
        }

        // ============================================
        // ELIGIBILITY - SISWA TIDAK LAYAK UJIAN DI KELAS GURU
        // ============================================
        $eligibilityData = $this->getEligibilityForGuruKelas($guru->id, $kelasYangDiajar, $tahunAkademikAktif);

        return view('guru.dashboard', compact(
            'kelasYangDiajar',
            'jadwalHariIni',
            'pertemuanTerbaru',
            'siswaRemidi',
            'statistikNilai',
            'tahunAkademikAktif',
            'eligibilityData'
        ));
    }

    /**
     * Get eligibility data for students in classes taught by this teacher
     */
    protected function getEligibilityForGuruKelas($guruId, $kelasYangDiajar, ?TahunAkademik $tahunAkademik): array
    {
        if (!$tahunAkademik || $kelasYangDiajar->isEmpty()) {
            return [
                'total_siswa' => 0,
                'layak' => 0,
                'tidak_layak' => 0,
                'masalah_mapel_guru' => 0,
                'siswa_tidak_layak' => collect(),
            ];
        }

        // Get all kelas IDs taught by this teacher
        $kelasIds = $kelasYangDiajar->pluck('kelas_id')->unique();
        $guruKelasIds = $kelasYangDiajar->pluck('id');

        // Get all students in those classes
        $siswaList = Siswa::where('status', 'aktif')
            ->whereIn('current_class_id', $kelasIds)
            ->with('currentClass')
            ->get();

        $layak = 0;
        $tidakLayak = 0;
        $masalahMapelGuru = 0;
        $siswaTidakLayak = [];

        foreach ($siswaList as $siswa) {
            $eligibility = $this->eligibilityService->getEligibilityForDashboard($siswa->id);

            if ($eligibility['eligible']) {
                $layak++;
            } else {
                $tidakLayak++;

                // Check if this student has issues in this teacher's subjects
                $hasMapelIssuesGuru = false;
                if ($eligibility['has_mapel_issues'] && isset($eligibility['issues']['mapel'])) {
                    // Check if any remidi is in guru's kelas
                    $remidiInGuruKelas = RemidiSiswa::where('siswa_id', $siswa->id)
                        ->whereIn('guru_kelas_id', $guruKelasIds)
                        ->where('status_remidi', 'pending')
                        ->exists();

                    $hasMapelIssuesGuru = $remidiInGuruKelas;
                    if ($hasMapelIssuesGuru) {
                        $masalahMapelGuru++;
                    }
                }

                $siswaTidakLayak[] = [
                    'siswa' => $siswa,
                    'kelas' => $siswa->currentClass,
                    'issues' => $eligibility['issues'],
                    'summary' => $eligibility['summary'],
                    'has_mapel_issues' => $eligibility['has_mapel_issues'],
                    'has_kedisiplinan_issues' => $eligibility['has_kedisiplinan_issues'],
                    'has_keagamaan_issues' => $eligibility['has_keagamaan_issues'],
                    'has_mapel_issues_guru' => $hasMapelIssuesGuru, // Highlight if in this teacher's subject
                ];
            }
        }

        // Sort: students with issues in this teacher's subject first
        usort($siswaTidakLayak, function ($a, $b) {
            if ($a['has_mapel_issues_guru'] && !$b['has_mapel_issues_guru']) return -1;
            if (!$a['has_mapel_issues_guru'] && $b['has_mapel_issues_guru']) return 1;
            return strcmp($a['kelas']->nama_lengkap ?? '', $b['kelas']->nama_lengkap ?? '');
        });

        return [
            'total_siswa' => count($siswaList),
            'layak' => $layak,
            'tidak_layak' => $tidakLayak,
            'masalah_mapel_guru' => $masalahMapelGuru,
            'siswa_tidak_layak' => collect($siswaTidakLayak),
        ];
    }
}
