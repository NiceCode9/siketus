<?php

namespace App\Services;

use App\Models\GuruKelas;
use App\Models\JenisUjian;
use App\Models\Kedisiplinan;
use App\Models\KegiatanKeagamaan;
use App\Models\Mapel;
use App\Models\PenilaianKeagamaan;
use App\Models\PenilaianKedisiplinan;
use App\Models\PenilaianMapel;
use App\Models\RiwayatKelas;
use App\Models\Siswa;
use App\Models\TahunAkademik;

class ReportUjianService
{
    public function getDataUjianMapel($data)
    {
        $tahunAkademikId = $data['tahun_akademik_id'] ?? null;
        $semester = $data['semester'] ?? null;
        $siswaId = $data['siswa_id'] ?? null;

        // Get data master
        $tahunAkademik = TahunAkademik::findOrFail($tahunAkademikId);
        $siswa = Siswa::findOrFail($siswaId);

        // Get kelas siswa pada tahun akademik tertentu dari riwayat_kelas
        $riwayatKelas = RiwayatKelas::with('kelas')
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->first();

        // Jika tidak ada di riwayat, gunakan current_class_id sebagai fallback
        $kelasId = $riwayatKelas ? $riwayatKelas->kelas_id : $siswa->current_class_id;
        $kelas = $riwayatKelas ? $riwayatKelas->kelas : $siswa->currentClass;

        // Get jenis ujian sesuai filter
        $jenisUjian = JenisUjian::where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->orderBy('id')
            ->get();

        // Get all mapel
        $mapels = Mapel::orderBy('nama_mapel')->get();

        // Get guru untuk setiap mapel berdasarkan kelas siswa pada tahun akademik tersebut

        $reportData = [];

        foreach ($mapels as $mapel) {
            // Cari guru yang mengajar mapel ini di kelas siswa
            $guruKelas = GuruKelas::with(['guruMapel.guru', 'guruMapel.mapel'])
                ->whereHas('guruMapel', function ($q) use ($mapel) {
                    $q->where('mapel_id', $mapel->id);
                })
                ->where('kelas_id', $kelasId)
                ->where('tahun_akademik_id', $tahunAkademikId)
                ->where('aktif', true)
                ->first();

            $namaGuru = $guruKelas && $guruKelas->guruMapel && $guruKelas->guruMapel->guru
                ? $guruKelas->guruMapel->guru->nama
                : '-';

            // Get nilai untuk setiap jenis ujian
            $nilai = [];
            if ($guruKelas) {
                foreach ($jenisUjian as $ju) {
                $penilaian = PenilaianMapel::where('siswa_id', $siswaId)
                    ->where('jenis_ujian_id', $ju->id)
                    ->where('tahun_akademik_id', $tahunAkademikId)
                    ->where('semester', $semester)
                    ->where('kelas_id', $kelasId)
                    ->when($guruKelas, function ($q) use ($guruKelas) {
                        return $q->where('guru_kelas_id', $guruKelas->id);
                    })
                    ->first();

                $nilai[$ju->id] = $penilaian ? $penilaian->nilai : '-';
            }}

            $reportData[] = [
                'mapel' => $mapel,
                'guru' => $namaGuru,
                'nilai' => $nilai,
            ];
        }

        return [
            'tahunAkademik' => $tahunAkademik,
            'semester' => $semester,
            'siswa' => $siswa,
            'kelas' => $kelas,
            'jenisUjian' => $jenisUjian,
            'reportData' => $reportData,
            'pageTitle' => 'LAPORAN PENILAIAN FORMATIF, SUMATIF, DAN UJIAN AKHIR',
        ];
    }

    public function getDataUjianKedisiplinan($data)
    {
        $tahunAkademikId = $data['tahun_akademik_id'] ?? null;
        $semester = $data['semester'] ?? null;
        $siswaId = $data['siswa_id'] ?? null;

        // Get data master
        $tahunAkademik = TahunAkademik::findOrFail($tahunAkademikId);
        $siswa = Siswa::findOrFail($siswaId);

        // Get kelas siswa pada tahun akademik tertentu dari riwayat_kelas
        $riwayatKelas = RiwayatKelas::with('kelas')
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->first();

        // Jika tidak ada di riwayat, gunakan current_class_id sebagai fallback
        $kelasId = $riwayatKelas ? $riwayatKelas->kelas_id : $siswa->current_class_id;
        $kelas = $riwayatKelas ? $riwayatKelas->kelas : $siswa->currentClass;

        // Get Kedisiplinan
        $kedisiplinan = Kedisiplinan::orderBy('jenis')->get();

        // Get penilaian kedisiplinan
        $penilaianKedisiplinan = PenilaianKedisiplinan::where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->get();

        $nilai = [];
        $guru = [];

        foreach ($kedisiplinan as $k) {
            foreach ($penilaianKedisiplinan as $pk) {
                if ($pk->kedisiplinan_id && $pk->kedisiplinan_id == $k->id) {
                    $nilai[$k->id] = $pk->validasi;
                    $guru[$k->id] = $pk->guru ? $pk->guru->nama : '-';
                }
            }
        }

        return [
            'tahunAkademik' => $tahunAkademik,
            'semester' => $semester,
            'siswa' => $siswa,
            'kedisiplinan' => $kedisiplinan,
            'nilai' => $nilai,
            'guru' => $guru,
            'kelas' => $kelas,
            'pageTitle' => 'LAPORAN PENILAIAN KEDISIPLINAN DAN KERAPIAN',
        ];
    }

    public function getDataUjianKeagamaan($data)
    {
        $tahunAkademikId = $data['tahun_akademik_id'] ?? null;
        $semester = $data['semester'] ?? null;
        $siswaId = $data['siswa_id'] ?? null;

        // Get data master
        $tahunAkademik = TahunAkademik::findOrFail($tahunAkademikId);
        $siswa = Siswa::findOrFail($siswaId);

        // Get kelas siswa pada tahun akademik tertentu dari riwayat_kelas
        $riwayatKelas = RiwayatKelas::with('kelas')
            ->where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->first();

        // Jika tidak ada di riwayat, gunakan current_class_id sebagai fallback
        $kelasId = $riwayatKelas ? $riwayatKelas->kelas_id : $siswa->current_class_id;
        $kelas = $riwayatKelas ? $riwayatKelas->kelas : $siswa->currentClass;

        $keagamaan = KegiatanKeagamaan::orderBy('nama_kegiatan')
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('tingkat_kelas', $kelas->tingkat)
            ->where('semester', $semester)
            ->get();

        $guru = [];
        $nilai = [];

        foreach ($keagamaan as $k) {
            $penilaianKeagamaan = PenilaianKeagamaan::with('guru')
                ->where('siswa_id', $siswaId)
                ->where('kegiatan_keagamaan_id', $k->id)
                ->first();

            $nilai[$k->id] = $penilaianKeagamaan ? $penilaianKeagamaan->nilai : '-';
            $guru[$k->id] = $penilaianKeagamaan ? $penilaianKeagamaan->guru->nama : '-';
        }

        return [
            'tahunAkademik' => $tahunAkademik,
            'semester' => $semester,
            'siswa' => $siswa,
            'keagamaan' => $keagamaan,
            'nilai' => $nilai,
            'guru' => $guru,
            'kelas' => $kelas,
            'keagamaan' => $keagamaan,
            'pageTitle' => 'LAPORAN PENILAIAN KEGIATAN KEAGAMANAN',
        ];
    }
}
