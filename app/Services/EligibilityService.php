<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\TahunAkademik;
use App\Models\PenilaianMapel;
use App\Models\PenilaianKedisiplinan;
use App\Models\PenilaianKeagamaan;
use App\Models\RemidiSiswa;
use App\Models\Kedisiplinan;
use App\Models\KegiatanKeagamaan;

class EligibilityService
{
    /**
     * Minimum nilai untuk kedisiplinan dan keagamaan
     * Bisa disesuaikan atau dijadikan config
     */
    const MIN_NILAI_KEAGAMAAN = 70;
    const MIN_PERSENTASE_KEDISIPLINAN = 80; // Persentase minimal kedisiplinan yang harus dipenuhi

    /**
     * Check kelayakan siswa untuk mengikuti ujian semester
     *
     * @param int $siswaId
     * @param TahunAkademik|null $tahunAkademik
     * @return array
     */
    public function checkEligibility($siswaId, ?TahunAkademik $tahunAkademik = null): array
    {
        if (!$tahunAkademik) {
            $tahunAkademik = TahunAkademik::aktif()->first();
        }

        if (!$tahunAkademik) {
            return [
                'eligible' => false,
                'message' => 'Tidak ada tahun akademik aktif',
                'issues' => [],
                'semester' => null,
                'tahun_akademik' => null,
                'summary' => [],
            ];
        }

        // Semester otomatis berdasarkan tanggal
        $semester = $tahunAkademik->semester; // Menggunakan accessor dari model
        $siswa = Siswa::find($siswaId);

        if (!$siswa) {
            return [
                'eligible' => false,
                'message' => 'Siswa tidak ditemukan',
                'issues' => [],
            ];
        }

        $issues = [];

        // 1. Check Penilaian Mapel (Remidi pending)
        $mapelIssues = $this->checkPenilaianMapel($siswaId, $tahunAkademik->id, $semester);
        if (!empty($mapelIssues)) {
            $issues['mapel'] = $mapelIssues;
        }

        // 2. Check Penilaian Kedisiplinan
        $kedisiplinanIssues = $this->checkPenilaianKedisiplinan($siswaId, $tahunAkademik->id, $semester);
        if (!empty($kedisiplinanIssues)) {
            $issues['kedisiplinan'] = $kedisiplinanIssues;
        }

        // 3. Check Penilaian Keagamaan
        $keagamaanIssues = $this->checkPenilaianKeagamaan($siswaId, $tahunAkademik->id, $semester);
        if (!empty($keagamaanIssues)) {
            $issues['keagamaan'] = $keagamaanIssues;
        }

        $eligible = empty($issues);

        return [
            'eligible' => $eligible,
            'semester' => $semester,
            'tahun_akademik' => $tahunAkademik->nama_tahun_akademik,
            'message' => $eligible
                ? 'Anda memenuhi syarat untuk mengikuti Ujian Semester ' . ucfirst($semester)
                : 'Anda belum memenuhi syarat untuk mengikuti Ujian Semester ' . ucfirst($semester),
            'issues' => $issues,
            'summary' => $this->generateSummary($issues),
        ];
    }

    /**
     * Check penilaian mapel - apakah ada remidi yang masih pending
     */
    protected function checkPenilaianMapel($siswaId, $tahunAkademikId, $semester): array
    {
        $issues = [];

        // Cek remidi yang masih pending
        $remidiPending = RemidiSiswa::with(['guruKelas.guruMapel.mapel', 'jenisUjian'])
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->where('status_remidi', 'pending')
            ->get();

        foreach ($remidiPending as $remidi) {
            $mapelName = $remidi->guruKelas->guruMapel->mapel->nama_mapel ?? 'Unknown';
            $jenisUjian = $remidi->jenisUjian->nama_jenis_ujian ?? 'Unknown';

            $issues[] = [
                'type' => 'remidi_pending',
                'mapel' => $mapelName,
                'jenis_ujian' => $jenisUjian,
                'nilai_asli' => $remidi->nilai_asli,
                'kkm' => $remidi->kkm,
                'message' => "Nilai {$mapelName} ({$jenisUjian}) di bawah KKM. Nilai: {$remidi->nilai_asli}, KKM: {$remidi->kkm}",
            ];
        }

        return $issues;
    }

    /**
     * Check penilaian kedisiplinan
     */
    protected function checkPenilaianKedisiplinan($siswaId, $tahunAkademikId, $semester): array
    {
        $issues = [];

        // Get semua jenis kedisiplinan
        $totalKedisiplinan = Kedisiplinan::count();

        if ($totalKedisiplinan === 0) {
            return $issues;
        }

        // Get penilaian kedisiplinan siswa
        $penilaianKedisiplinan = PenilaianKedisiplinan::with('kedisiplinan')
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->get();

        // Hitung yang validasi = true
        $validasiCount = $penilaianKedisiplinan->where('validasi', true)->count();

        // Hitung persentase
        $persentase = $totalKedisiplinan > 0
            ? round(($validasiCount / $totalKedisiplinan) * 100, 2)
            : 0;

        if ($persentase < self::MIN_PERSENTASE_KEDISIPLINAN) {
            $issues[] = [
                'type' => 'kedisiplinan_kurang',
                'persentase' => $persentase,
                'minimal' => self::MIN_PERSENTASE_KEDISIPLINAN,
                'terpenuhi' => $validasiCount,
                'total' => $totalKedisiplinan,
                'message' => "Kedisiplinan belum memenuhi syarat. Terpenuhi: {$validasiCount}/{$totalKedisiplinan} ({$persentase}%), minimal " . self::MIN_PERSENTASE_KEDISIPLINAN . "%",
            ];
        }

        // Cek item kedisiplinan yang belum terpenuhi
        $kedisiplinanBelumTerpenuhi = PenilaianKedisiplinan::with('kedisiplinan')
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->where('validasi', false)
            ->get();

        foreach ($kedisiplinanBelumTerpenuhi as $item) {
            $issues[] = [
                'type' => 'kedisiplinan_item',
                'jenis' => $item->kedisiplinan->jenis ?? 'Unknown',
                'message' => "Kedisiplinan '{$item->kedisiplinan->jenis}' belum terpenuhi",
            ];
        }

        return $issues;
    }

    /**
     * Check penilaian keagamaan
     */
    protected function checkPenilaianKeagamaan($siswaId, $tahunAkademikId, $semester): array
    {
        $issues = [];

        // Get penilaian keagamaan siswa yang di bawah minimal
        $penilaianKeagamaan = PenilaianKeagamaan::with('kegiatanKeagamaan')
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->where('nilai', '<', self::MIN_NILAI_KEAGAMAAN)
            ->get();

        foreach ($penilaianKeagamaan as $penilaian) {
            $kegiatanName = $penilaian->kegiatanKeagamaan->nama_kegiatan ?? 'Unknown';

            $issues[] = [
                'type' => 'keagamaan_kurang',
                'kegiatan' => $kegiatanName,
                'nilai' => $penilaian->nilai,
                'minimal' => self::MIN_NILAI_KEAGAMAAN,
                'message' => "Nilai keagamaan '{$kegiatanName}' di bawah standar. Nilai: {$penilaian->nilai}, Minimal: " . self::MIN_NILAI_KEAGAMAAN,
            ];
        }

        // Cek kegiatan keagamaan yang belum dinilai
        $kegiatanIds = PenilaianKeagamaan::where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->pluck('kegiatan_keagamaan_id');

        $siswa = Siswa::find($siswaId);
        $tingkat = $siswa->currentClass->tingkat ?? null;

        $kegiatanBelumDinilai = KegiatanKeagamaan::where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->when($tingkat, function ($q) use ($tingkat) {
                $q->where('tingkat_kelas', $tingkat);
            })
            ->whereNotIn('id', $kegiatanIds)
            ->get();

        foreach ($kegiatanBelumDinilai as $kegiatan) {
            $issues[] = [
                'type' => 'keagamaan_belum_dinilai',
                'kegiatan' => $kegiatan->nama_kegiatan,
                'message' => "Kegiatan keagamaan '{$kegiatan->nama_kegiatan}' belum memiliki nilai",
            ];
        }

        return $issues;
    }

    /**
     * Generate summary of issues
     */
    protected function generateSummary(array $issues): array
    {
        $summary = [];

        if (isset($issues['mapel']) && count($issues['mapel']) > 0) {
            $summary[] = count($issues['mapel']) . ' mata pelajaran memiliki nilai di bawah KKM (perlu remidi)';
        }

        if (isset($issues['kedisiplinan']) && count($issues['kedisiplinan']) > 0) {
            $kedisiplinanKurang = collect($issues['kedisiplinan'])->where('type', 'kedisiplinan_kurang')->first();
            if ($kedisiplinanKurang) {
                $summary[] = "Kedisiplinan belum memenuhi syarat ({$kedisiplinanKurang['persentase']}% dari minimal {$kedisiplinanKurang['minimal']}%)";
            }
        }

        if (isset($issues['keagamaan']) && count($issues['keagamaan']) > 0) {
            $keagamaanCount = collect($issues['keagamaan'])->where('type', 'keagamaan_kurang')->count();
            $belumDinilaiCount = collect($issues['keagamaan'])->where('type', 'keagamaan_belum_dinilai')->count();

            if ($keagamaanCount > 0) {
                $summary[] = "{$keagamaanCount} kegiatan keagamaan memiliki nilai di bawah standar";
            }
            if ($belumDinilaiCount > 0) {
                $summary[] = "{$belumDinilaiCount} kegiatan keagamaan belum memiliki nilai";
            }
        }

        return $summary;
    }

    /**
     * Get eligibility status dengan detail lengkap untuk dashboard
     */
    public function getEligibilityForDashboard($siswaId): array
    {
        $result = $this->checkEligibility($siswaId);

        // Tambahkan informasi detail untuk tampilan
        $result['has_mapel_issues'] = isset($result['issues']['mapel']) && count($result['issues']['mapel']) > 0;
        $result['has_kedisiplinan_issues'] = isset($result['issues']['kedisiplinan']) && count($result['issues']['kedisiplinan']) > 0;
        $result['has_keagamaan_issues'] = isset($result['issues']['keagamaan']) && count($result['issues']['keagamaan']) > 0;

        $result['mapel_issues_count'] = $result['has_mapel_issues'] ? count($result['issues']['mapel']) : 0;
        $result['kedisiplinan_issues_count'] = $result['has_kedisiplinan_issues'] ? count($result['issues']['kedisiplinan']) : 0;
        $result['keagamaan_issues_count'] = $result['has_keagamaan_issues'] ? count($result['issues']['keagamaan']) : 0;

        return $result;
    }
}
