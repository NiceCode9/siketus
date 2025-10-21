<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\PenilaianKedisiplinan;
use App\Models\PenilaianKeagamaan;
use App\Models\PenilaianMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiwayatPenilaianController extends Controller
{
    /**
     * Riwayat Penilaian untuk Role GURU
     */
    public function guruIndex(Request $request)
    {
        $guru = Auth::user()->guru;

        $tahunAkademiks = TahunAkademik::orderBy('status_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $selectedTahunAkademik = $request->tahun_akademik_id ?? TahunAkademik::where('status_aktif', true)->first()?->id;
        $selectedKelas = $request->kelas_id;
        $selectedSemester = $request->semester;
        $selectedKategori = $request->kategori;

        // Get kelas yang diampu guru
        $kelasList = [];
        $riwayatData = [];

        if ($selectedTahunAkademik) {
            $guruKelasQuery = DB::table('guru_kelas')
                ->join('guru_mapel', 'guru_kelas.guru_mapel_id', '=', 'guru_mapel.id')
                ->join('kelas', 'guru_kelas.kelas_id', '=', 'kelas.id')
                ->where('guru_mapel.guru_id', $guru->id)
                ->where('guru_kelas.tahun_akademik_id', $selectedTahunAkademik)
                ->where('guru_kelas.aktif', true)
                ->select('kelas.*')
                ->distinct()
                ->get();

            $kelasList = Kelas::whereIn('id', $guruKelasQuery->pluck('id'))->get();

            // Jika semua filter dipilih, ambil data riwayat
            if ($selectedKelas && $selectedSemester && $selectedKategori) {
                if ($selectedKategori === 'mapel') {
                    $riwayatData = $this->getGuruRiwayatMapel($guru->id, $selectedTahunAkademik, $selectedKelas, $selectedSemester);
                } elseif ($selectedKategori === 'kedisiplinan') {
                    $riwayatData = $this->getGuruRiwayatKedisiplinan($guru->id, $selectedTahunAkademik, $selectedKelas, $selectedSemester);
                } elseif ($selectedKategori === 'keagamaan') {
                    $riwayatData = $this->getGuruRiwayatKeagamaan($guru->id, $selectedTahunAkademik, $selectedKelas, $selectedSemester);
                }
            }
        }

        return view('guru.riwayat-penilaian.index', compact(
            'tahunAkademiks',
            'kelasList',
            'selectedTahunAkademik',
            'selectedKelas',
            'selectedSemester',
            'selectedKategori',
            'riwayatData'
        ));
    }

    /**
     * Detail Riwayat Penilaian Siswa untuk GURU
     */
    public function guruDetail($siswaId, Request $request)
    {
        $siswa = Siswa::with(['currentClass'])->findOrFail($siswaId);
        $guru = Auth::user()->guru;

        $tahunAkademikId = $request->tahun_akademik_id;
        $semester = $request->semester;
        $kategori = $request->kategori;

        $detail = [];

        if ($kategori === 'mapel') {
            $detail = PenilaianMapel::with(['jenisUjian', 'guruKelas.guruMapel.mapel'])
                ->whereHas('guruKelas.guruMapel', function ($q) use ($guru) {
                    $q->where('guru_id', $guru->id);
                })
                ->where('siswa_id', $siswaId)
                ->where('tahun_akademik_id', $tahunAkademikId)
                ->where('semester', $semester)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($kategori === 'kedisiplinan') {
            $detail = PenilaianKedisiplinan::with(['kedisiplinan'])
                ->where('guru_id', $guru->id)
                ->where('siswa_id', $siswaId)
                ->where('tahun_akademik_id', $tahunAkademikId)
                ->where('semester', $semester)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($kategori === 'keagamaan') {
            $detail = PenilaianKeagamaan::with(['kegiatanKeagamaan'])
                ->where('guru_id', $guru->id)
                ->where('siswa_id', $siswaId)
                ->where('tahun_akademik_id', $tahunAkademikId)
                ->where('semester', $semester)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('guru.riwayat-penilaian.detail', compact(
            'siswa',
            'detail',
            'kategori',
            'semester',
            'tahunAkademikId'
        ));
    }

    /**
     * Riwayat Penilaian untuk Role SISWA
     */
    public function siswaIndex(Request $request)
    {
        $siswa = Auth::user()->siswa;

        $tahunAkademiks = TahunAkademik::orderBy('status_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $selectedTahunAkademik = $request->tahun_akademik_id ?? TahunAkademik::where('status_aktif', true)->first()?->id;
        $selectedSemester = $request->semester;

        $nilaiMapel = [];
        $nilaiKedisiplinan = [];
        $nilaiKeagamaan = [];
        $statistik = [];

        if ($selectedTahunAkademik && $selectedSemester) {
            // Nilai Mata Pelajaran
            $nilaiMapel = PenilaianMapel::with(['jenisUjian', 'guruKelas.guruMapel.mapel', 'guruKelas.guruMapel.guru'])
                ->where('siswa_id', $siswa->id)
                ->where('tahun_akademik_id', $selectedTahunAkademik)
                ->where('semester', $selectedSemester)
                ->orderBy('created_at', 'desc')
                ->get();

            // Nilai Kedisiplinan
            $nilaiKedisiplinan = PenilaianKedisiplinan::with(['kedisiplinan', 'guru'])
                ->where('siswa_id', $siswa->id)
                ->where('tahun_akademik_id', $selectedTahunAkademik)
                ->where('semester', $selectedSemester)
                ->orderBy('created_at', 'desc')
                ->get();

            // Nilai Keagamaan
            $nilaiKeagamaan = PenilaianKeagamaan::with(['kegiatanKeagamaan', 'guru'])
                ->where('siswa_id', $siswa->id)
                ->where('tahun_akademik_id', $selectedTahunAkademik)
                ->where('semester', $selectedSemester)
                ->orderBy('created_at', 'desc')
                ->get();

            // Hitung Statistik
            $statistik = $this->hitungStatistik($nilaiMapel, $nilaiKedisiplinan, $nilaiKeagamaan);
        }

        return view('siswa.riwayat-penilaian.index', compact(
            'siswa',
            'tahunAkademiks',
            'selectedTahunAkademik',
            'selectedSemester',
            'nilaiMapel',
            'nilaiKedisiplinan',
            'nilaiKeagamaan',
            'statistik'
        ));
    }

    /**
     * Detail per Mata Pelajaran untuk SISWA
     */
    public function siswaDetailMapel($mapelId, Request $request)
    {
        $siswa = Auth::user()->siswa;
        $tahunAkademikId = $request->tahun_akademik_id;
        $semester = $request->semester;

        $nilaiMapel = PenilaianMapel::with(['jenisUjian', 'guruKelas.guruMapel.mapel', 'guruKelas.guruMapel.guru'])
            ->where('siswa_id', $siswa->id)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->whereHas('guruKelas.guruMapel', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $mapel = $nilaiMapel->first()?->guruKelas->guruMapel->mapel;
        $rataRata = $nilaiMapel->avg('nilai');

        return view('siswa.riwayat-penilaian.detail-mapel', compact(
            'siswa',
            'nilaiMapel',
            'mapel',
            'rataRata',
            'semester',
            'tahunAkademikId'
        ));
    }

    // ========== PRIVATE METHODS ==========

    private function getGuruRiwayatMapel($guruId, $tahunAkademikId, $kelasId, $semester)
    {
        $data = PenilaianMapel::with(['siswa', 'jenisUjian', 'guruKelas.guruMapel.mapel'])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by siswa
        return $data->groupBy('siswa_id')->map(function ($items) {
            return [
                'siswa' => $items->first()->siswa,
                'nilai' => $items,
                'rata_rata' => $items->avg('nilai'),
                'jumlah_nilai' => $items->count(),
            ];
        });
    }

    private function getGuruRiwayatKedisiplinan($guruId, $tahunAkademikId, $kelasId, $semester)
    {
        $data = PenilaianKedisiplinan::with(['siswa', 'kedisiplinan'])
            ->where('guru_id', $guruId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->orderBy('created_at', 'desc')
            ->get();

        return $data->groupBy('siswa_id')->map(function ($items) {
            return [
                'siswa' => $items->first()->siswa,
                'nilai' => $items,
                'rata_rata' => $items->avg('nilai'),
                'jumlah_nilai' => $items->count(),
            ];
        });
    }

    private function getGuruRiwayatKeagamaan($guruId, $tahunAkademikId, $kelasId, $semester)
    {
        $data = PenilaianKeagamaan::with(['siswa', 'kegiatanKeagamaan'])
            ->where('guru_id', $guruId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->orderBy('created_at', 'desc')
            ->get();

        return $data->groupBy('siswa_id')->map(function ($items) {
            return [
                'siswa' => $items->first()->siswa,
                'nilai' => $items,
                'rata_rata' => $items->avg('nilai'),
                'jumlah_nilai' => $items->count(),
            ];
        });
    }

    private function hitungStatistik($nilaiMapel, $nilaiKedisiplinan, $nilaiKeagamaan)
    {
        return [
            'total_nilai_mapel' => $nilaiMapel->count(),
            'rata_rata_mapel' => $nilaiMapel->avg('nilai') ?? 0,
            'total_nilai_kedisiplinan' => $nilaiKedisiplinan->count(),
            'rata_rata_kedisiplinan' => $nilaiKedisiplinan->avg('nilai') ?? 0,
            'total_nilai_keagamaan' => $nilaiKeagamaan->count(),
            'rata_rata_keagamaan' => $nilaiKeagamaan->avg('nilai') ?? 0,
            'rata_rata_keseluruhan' => collect([
                $nilaiMapel->avg('nilai'),
                $nilaiKedisiplinan->avg('nilai'),
                $nilaiKeagamaan->avg('nilai')
            ])->filter()->avg() ?? 0,
        ];
    }
}
