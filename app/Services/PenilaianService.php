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
    protected $kkmService;
    protected $remidiService;

    public function __construct(KkmService $kkmService, RemidiService $remidiService)
    {
        $this->kkmService = $kkmService;
        $this->remidiService = $remidiService;
    }

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
     * Get list of classes taught by a teacher
     */
    public function getKelasList($tahunAkademikId)
    {
        $guruKelas = GuruKelas::with(['kelas', 'guruMapel.mapel'])
            // ->whereHas('guruMapel', function ($q) use ($guruId) {
            //     $q->where('guru_id', $guruId);
            // })
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('aktif', true)
            ->get();

        return $guruKelas->pluck('kelas')->unique('id');
    }

    public function getMapelListByGuru($guruId, $tahunAkademikId)
    {
        return GuruKelas::with('guruMapel.mapel')
            ->whereHas('guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('aktif', true)
            ->get()
            ->pluck('guruMapel.mapel')
            ->flatten()
            ->unique('id');
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
    public function getKegiatanKeagamaanList($tahunAkademikId, $semester, $tingkat = null)
    {
        return KegiatanKeagamaan::where('tahun_akademik_id', $tahunAkademikId)
            ->where('semester', $semester)
            ->when($tingkat, function ($q) use ($tingkat) {
                $q->where('tingkat_kelas', $tingkat);
            })
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
    public function getExistingNilaiMapel($siswaId, $guruKelasId, $semester = 'genap')
    {
        return PenilaianMapel::with('remidiSiswa')->where('siswa_id', $siswaId)
            ->where('guru_kelas_id', $guruKelasId)
            // ->where('semester', $semester)
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
     * Store nilai mapel WITH KKM check and auto remidi
     * UPDATE method yang sudah ada
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
            // Loop untuk setiap siswa
            foreach ($data['nilai'] as $siswaId => $jenisUjianNilai) {
                // Loop untuk setiap jenis ujian per siswa
                foreach ($jenisUjianNilai as $jenisUjianId => $nilai) {
                    if ($nilai !== null && $nilai !== '') {
                        // Get KKM untuk jenis ujian ini
                        $kkm = $this->kkmService->getKkmValue(
                            $guruKelas->id,
                            $jenisUjianId,
                            $data['tahun_akademik_id']
                        );

                        // Tentukan status ketuntasan
                        $statusKetuntasan = null;
                        if ($kkm !== null) {
                            $statusKetuntasan = $this->kkmService->checkKetuntasan($nilai, $kkm);
                        }

                        // Update or create penilaian
                        $penilaian = PenilaianMapel::updateOrCreate(
                            [
                                'siswa_id' => $siswaId,
                                'guru_kelas_id' => $data['guru_kelas_id'],
                                'jenis_ujian_id' => $jenisUjianId,
                                'semester' => $data['semester'],
                            ],
                            [
                                'tahun_akademik_id' => $data['tahun_akademik_id'],
                                'kelas_id' => $data['kelas_id'],
                                'nilai' => $nilai,
                                'status_ketuntasan' => $statusKetuntasan,
                                'kkm_saat_itu' => $kkm,
                            ]
                        );

                        // Handle remidi logic
                        if ($statusKetuntasan === 'remidi') {
                            // Create/Update remidi record
                            $this->remidiService->updateOrCreateRemidi($penilaian->id, [
                                'siswa_id' => $siswaId,
                                'guru_kelas_id' => $guruKelas->id,
                                'jenis_ujian_id' => $jenisUjianId,
                                'tahun_akademik_id' => $data['tahun_akademik_id'],
                                'kelas_id' => $data['kelas_id'],
                                'semester' => $data['semester'],
                                'nilai_asli' => $nilai,
                                'kkm' => $kkm,
                            ]);
                        } elseif ($statusKetuntasan === 'tuntas') {
                            // Cancel remidi if exists (nilai updated and now tuntas)
                            $this->remidiService->cancelRemidi($penilaian->id);
                        }
                    }
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
     * Store nilai kedisiplinan
     */
    public function storeNilaiKedisiplinan(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['nilai'] as $siswaId => $kedisiplinanNilai) {
                foreach ($kedisiplinanNilai as $kedisiplinanId => $validasi) {
                    // Simpan data, termasuk jika nilai 0 (tidak dicentang)
                    PenilaianKedisiplinan::updateOrCreate(
                        [
                            'siswa_id' => $siswaId,
                            'tahun_akademik_id' => $data['tahun_akademik_id'],
                            'semester' => $data['semester'],
                            'kedisiplinan_id' => $kedisiplinanId,
                        ],
                        [
                            'guru_id' => $data['guru_id'],
                            'kelas_id' => $data['kelas_id'],
                            'validasi' => (bool) $validasi, // Convert ke boolean
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
            foreach ($data['nilai'] as $siswaId => $kegiatanNilai) {
                foreach ($kegiatanNilai as $kegiatanId => $nilai) {
                    // Hanya simpan jika ada nilai yang diinput
                    if ($nilai !== null && $nilai !== '') {
                        PenilaianKeagamaan::updateOrCreate(
                            [
                                'siswa_id' => $siswaId,
                                'kegiatan_keagamaan_id' => $kegiatanId,
                                'tahun_akademik_id' => $data['tahun_akademik_id'],
                                'semester' => $data['semester'],
                            ],
                            [
                                'guru_id' => $data['guru_id'],
                                'kelas_id' => $data['kelas_id'],
                                'nilai' => $nilai,
                            ]
                        );
                    }
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
    public function getFormData($siswaId, $tahunAkademikId, $kelasId, $semester, $kategori, $guruId = null, $mapelId = null)
    {
        $data = [];

        if ($kategori === 'mapel') {
            $guruKelas = $this->getGuruKelas($guruId, $kelasId, $tahunAkademikId, $mapelId);
            $data['mapelId'] = $mapelId;
            $data['guruKelas'] = $guruKelas;
            $data['jenisUjianList'] = $this->getJenisUjianList($tahunAkademikId);
            $data['existingNilai'] = $this->getExistingNilaiMapel($siswaId, $guruKelas->id, $semester);

            // ADD: KKM data
            $data['kkmData'] = $this->kkmService->getKkmByGuruKelas($guruKelas->id, $tahunAkademikId);
        } elseif ($kategori === 'kedisiplinan') {
            $data['kedisiplinanList'] = $this->getKedisiplinanList();
            $data['existingNilai'] = $this->getExistingNilaiKedisiplinan($siswaId, $tahunAkademikId, $semester);
        } elseif ($kategori === 'keagamaan') {
            $data['kegiatanKeagamaanList'] = $this->getKegiatanKeagamaanList($tahunAkademikId, $semester);
            $data['existingNilai'] = $this->getExistingNilaiKeagamaan($siswaId, $tahunAkademikId, $semester);
        }

        return $data;
    }

    /**
     * Get nilai siswa yang melebihi nilai guru
     * Untuk ditampilkan sebagai warning di halaman siswa
     */
    public function getNilaiSiswaBerlebih($siswaId, $guruKelasIds)
    {
        $nilaiBerlebih = [];

        foreach ($guruKelasIds as $guruKelasId) {
            $penilaianList = PenilaianMapel::where('guru_kelas_id', $guruKelasId)
                ->where('siswa_id', $siswaId)
                ->whereNotNull('nilai_by_siswa')
                ->where('nilai', '>', 0) // Hanya jika guru sudah input
                ->get();

            foreach ($penilaianList as $penilaian) {
                // Cek jika nilai siswa > nilai guru (dan guru sudah input)
                if ($penilaian->nilai > 0 && $penilaian->nilai_by_siswa > $penilaian->nilai) {
                    $nilaiBerlebih[$guruKelasId][$penilaian->jenis_ujian_id] = [
                        'nilai_siswa' => $penilaian->nilai_by_siswa,
                        'nilai_guru' => $penilaian->nilai,
                        'selisih' => $penilaian->nilai_by_siswa - $penilaian->nilai,
                    ];
                }
            }
        }

        return $nilaiBerlebih;
    }

    /**
     * Get nilai data for siswa WITH ketuntasan info
     * UPDATE method yang sudah ada untuk include ketuntasan
     */
    public function getNilaiDataForSiswaWithKetuntasan($siswaId, $guruKelasIds)
    {
        $nilaiData = [];

        foreach ($guruKelasIds as $guruKelasId) {
            $nilaiMapel = PenilaianMapel::with('remidiSiswa')
                ->where('guru_kelas_id', $guruKelasId)
                ->where('siswa_id', $siswaId)
                ->get();

            foreach ($nilaiMapel as $nilai) {
                $isOverflow = (
                    $nilai->nilai_by_siswa !== null &&
                    $nilai->nilai_by_siswa !== '' &&
                    $nilai->nilai > 0 &&
                    $nilai->nilai_by_siswa > $nilai->nilai
                );

                $nilaiData[$guruKelasId][$nilai->jenis_ujian_id] = [
                    'nilai_siswa' => $nilai->nilai_by_siswa ?? '',
                    'nilai_guru' => $nilai->nilai ?? 0,
                    'is_overflow' => $isOverflow,
                    'status_ketuntasan' => $nilai->status_ketuntasan,
                    'kkm' => $nilai->kkm_saat_itu,
                    'has_remidi' => $nilai->remidiSiswa !== null,
                    'remidi_status' => $nilai->remidiSiswa?->status_remidi,
                ];
            }
        }

        return $nilaiData;
    }

    public function getNilaiGuruForValidation($guruKelasId, $jenisUjianId, $siswaId)
    {
        $penilaian = PenilaianMapel::where('guru_kelas_id', $guruKelasId)
            ->where('jenis_ujian_id', $jenisUjianId)
            ->where('siswa_id', $siswaId)
            ->first();

        return $penilaian ? $penilaian->nilai : null;
    }

    /**
     * Validasi nilai siswa terhadap nilai guru
     */
    public function validateNilaiSiswaAgainstGuru($siswaId, $nilaiArray)
    {
        $errors = [];
        $valid = true;

        foreach ($nilaiArray as $guruKelasId => $jenisUjianNilai) {
            foreach ($jenisUjianNilai as $jenisUjianId => $nilaiSiswa) {
                if ($nilaiSiswa === null || $nilaiSiswa === '') {
                    continue;
                }

                // Cek nilai yang diinput oleh guru
                $nilaiGuru = $this->getNilaiGuruForValidation($guruKelasId, $jenisUjianId, $siswaId);

                if ($nilaiGuru !== null && $nilaiSiswa > $nilaiGuru) {
                    $errors[$guruKelasId][$jenisUjianId] = [
                        'message' => "Nilai tidak boleh melebihi {$nilaiGuru}",
                        'max_nilai' => $nilaiGuru,
                        'input_nilai' => $nilaiSiswa
                    ];
                    $valid = false;
                }
            }
        }

        return [
            'valid' => $valid,
            'errors' => $errors
        ];
    }

    /**
     * Store nilai mapel by siswa
     */
    public function storeNilaiMapelBySiswa(array $data)
    {
        DB::beginTransaction();
        try {
            foreach ($data['nilai'] as $guruKelasId => $jenisUjianNilai) {
                foreach ($jenisUjianNilai as $jenisUjianId => $nilai) {
                    // Skip jika nilai kosong, null, atau bukan angka
                    if ($nilai === null || $nilai === '' || !is_numeric($nilai)) {
                        continue;
                    }

                    // Pastikan nilai adalah angka yang valid
                    $nilaiFloat = floatval($nilai);

                    // Ambil semester dari data yang sudah difilter
                    $semester = $data['semester'][$guruKelasId][$jenisUjianId] ?? null;

                    // Skip jika semester tidak ada
                    if (!$semester) {
                        continue;
                    }

                    // Cek apakah record sudah ada
                    $existingRecord = PenilaianMapel::where([
                        'siswa_id' => $data['siswa_id'],
                        'guru_kelas_id' => $guruKelasId,
                        'jenis_ujian_id' => $jenisUjianId,
                        'semester' => $semester,
                    ])->first();

                    if ($existingRecord) {
                        // Update hanya nilai_by_siswa jika record sudah ada
                        $existingRecord->update([
                            'nilai_by_siswa' => $nilaiFloat,
                        ]);
                    } else {
                        // Insert record baru dengan nilai default 0 untuk field 'nilai'
                        PenilaianMapel::create([
                            'siswa_id' => $data['siswa_id'],
                            'guru_kelas_id' => $guruKelasId,
                            'jenis_ujian_id' => $jenisUjianId,
                            'semester' => $semester,
                            'tahun_akademik_id' => $data['tahun_akademik_id'],
                            'kelas_id' => $data['kelas_id'],
                            'nilai_by_siswa' => $nilaiFloat,
                        ]);
                    }
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
