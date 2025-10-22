<?php

namespace App\Services;

use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\GuruKelas;
use App\Models\Siswa;
use App\Models\JenisUjian;
use App\Models\Kedisiplinan;
use App\Models\KegiatanKeagamaan;
use App\Models\PenilaianKedisiplinan;
use App\Models\PenilaianKeagamaan;
use App\Models\PenilaianMapel;
use Illuminate\Support\Facades\DB;

class PenilaianService
{
    /**
     * Get list of classes taught by a teacher
     */
    public function getKelasListByGuru($guruId, $tahunAkademikId)
    {
        $guruKelas = GuruKelas::with(['kelas', 'guruMapel.mapel'])
            ->whereHas('guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('aktif', true)
            ->get();

        return $guruKelas->pluck('kelas')->unique('id');
    }

    /**
     * Get list of students in a class
     */
    public function getSiswaByKelas($kelasId, $tahunAkademikId)
    {
        return Siswa::whereHas('riwayatKelas', function ($q) use ($kelasId, $tahunAkademikId) {
            $q->where('kelas_id', $kelasId)
                ->where('tahun_akademik_id', $tahunAkademikId);
        })->orderBy('nama')->get();
    }

    /**
     * Get jenis ujian list
     */
    public function getJenisUjianList($tahunAkademikId)
    {
        return JenisUjian::where('tahun_akademik_id', $tahunAkademikId)->get();
    }

    /**
     * Get kedisiplinan list
     */
    public function getKedisiplinanList()
    {
        return Kedisiplinan::all();
    }

    /**
     * Get kegiatan keagamaan list
     */
    public function getKegiatanKeagamaanList($tahunAkademikId, $semester)
    {
        return KegiatanKeagamaan::where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->get();
    }

    /**
     * Get guru kelas by guru and kelas
     */
    public function getGuruKelas($guruId, $kelasId, $tahunAkademikId, $mapelId)
    {
        return GuruKelas::with(['guruMapel.mapel'])
            ->whereHas('guruMapel', function ($q) use ($guruId, $mapelId) {
                $q->where('guru_id', $guruId)->where('mapel_id', $mapelId);
            })
            ->where('kelas_id', $kelasId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('aktif', true)
            ->first();
    }

    /**
     * Get existing nilai mapel
     */
    public function getExistingNilaiMapel($siswaId, $guruKelasId, $semester)
    {
        return PenilaianMapel::where('siswa_id', $siswaId)
            ->where('guru_kelas_id', $guruKelasId)
            ->where('semester', $semester)
            ->get()
            ->keyBy('jenis_ujian_id');
    }

    /**
     * Get existing nilai kedisiplinan
     */
    public function getExistingNilaiKedisiplinan($siswaId, $tahunAkademikId, $semester)
    {
        return PenilaianKedisiplinan::where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->get()
            ->keyBy('kedisiplinan_id');
    }

    /**
     * Get existing nilai keagamaan
     */
    public function getExistingNilaiKeagamaan($siswaId, $tahunAkademikId, $semester)
    {
        return PenilaianKeagamaan::where('siswa_id', $siswaId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->get()
            ->keyBy('kegiatan_keagamaan_id');
    }

    /**
     * Store nilai mapel
     */
    public function storeNilaiMapel(array $data)
    {
        $guruKelas = $this->getGuruKelas(
            $data['guru_id'],
            $data['kelas_id'],
            $data['tahun_akademik_id'],
            $data['mapel_id']
        );

        if (!$guruKelas) {
            throw new \Exception('Guru kelas tidak ditemukan');
        }

        DB::beginTransaction();
        try {
            foreach ($data['nilai'] as $jenisUjianId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    PenilaianMapel::updateOrCreate(
                        [
                            'siswa_id' => $data['siswa_id'],
                            'guru_kelas_id' => $guruKelas->id,
                            'jenis_ujian_id' => $jenisUjianId,
                            'semester' => $data['semester'],
                        ],
                        [
                            'tahun_akademik_id' => $data['tahun_akademik_id'],
                            'kelas_id' => $data['kelas_id'],
                            'nilai' => $nilai,
                            'catatan' => $data['catatan'][$jenisUjianId] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            throw $e;
        }
    }

    /**
     * Store nilai kedisiplinan
     */
    public function storeNilaiKedisiplinan(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['nilai'] as $kedisiplinanId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    PenilaianKedisiplinan::updateOrCreate(
                        [
                            'siswa_id' => $data['siswa_id'],
                            'tahun_akademik_id' => $data['tahun_akademik_id'],
                            'semester' => $data['semester'],
                            'kedisiplinan_id' => $kedisiplinanId,
                        ],
                        [
                            'guru_id' => $data['guru_id'],
                            'kelas_id' => $data['kelas_id'],
                            'nilai' => $nilai,
                            'catatan' => $data['catatan'][$kedisiplinanId] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store nilai keagamaan
     */
    public function storeNilaiKeagamaan(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['nilai'] as $kegiatanId => $nilai) {
                if ($nilai !== null && $nilai !== '') {
                    PenilaianKeagamaan::updateOrCreate(
                        [
                            'siswa_id' => $data['siswa_id'],
                            'kegiatan_keagamaan_id' => $kegiatanId,
                            'tahun_akademik_id' => $data['tahun_akademik_id'],
                            'semester' => $data['semester'],
                        ],
                        [
                            'guru_id' => $data['guru_id'],
                            'kelas_id' => $data['kelas_id'],
                            'nilai' => $nilai,
                            'catatan' => $data['catatan'][$kegiatanId] ?? null,
                        ]
                    );
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Store penilaian based on kategori
     */
    public function storePenilaian(array $data)
    {
        switch ($data['kategori']) {
            case 'mapel':
                return $this->storeNilaiMapel($data);
            case 'kedisiplinan':
                return $this->storeNilaiKedisiplinan($data);
            case 'keagamaan':
                return $this->storeNilaiKeagamaan($data);
            default:
                throw new \Exception('Kategori tidak valid');
        }
    }

    /**
     * Get data for create/edit form based on kategori
     */
    public function getFormData($siswaId, $tahunAkademikId, $kelasId, $semester, $kategori, $guruId, $mapelId)
    {
        $data = [];

        if ($kategori === 'mapel') {
            $guruKelas = $this->getGuruKelas($guruId, $kelasId, $tahunAkademikId, $mapelId);
            $data['mapelId'] = $mapelId;
            $data['guruKelas'] = $guruKelas;
            $data['jenisUjianList'] = $this->getJenisUjianList($tahunAkademikId);
            $data['existingNilai'] = $this->getExistingNilaiMapel($siswaId, $guruKelas->id, $semester);
        } elseif ($kategori === 'kedisiplinan') {
            $data['kedisiplinanList'] = $this->getKedisiplinanList();
            $data['existingNilai'] = $this->getExistingNilaiKedisiplinan($siswaId, $tahunAkademikId, $semester);
        } elseif ($kategori === 'keagamaan') {
            $data['kegiatanKeagamaanList'] = $this->getKegiatanKeagamaanList($tahunAkademikId, $semester);
            $data['existingNilai'] = $this->getExistingNilaiKeagamaan($siswaId, $tahunAkademikId, $semester);
        }

        return $data;
    }
}
