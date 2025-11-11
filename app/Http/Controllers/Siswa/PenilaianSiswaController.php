<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\GuruKelas;
use App\Models\JenisUjian;
use App\Models\TahunAkademik;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianSiswaController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    /**
     * Index
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahunAkdemikId = TahunAkademik::aktif()->first()->id;
        $jenisUjians = JenisUjian::all();
        $guruKelas = GuruKelas::with('guruMapel.guru', 'guruMapel.mapel')
            ->where('kelas_id', $user->siswa->current_class_id)
            ->where('tahun_akademik_id', $tahunAkdemikId)
            ->where('aktif', true)
            ->get();

        // Ambil data nilai yang sudah diinput (guru dan siswa) WITH ketuntasan info
        $nilaiData = $this->penilaianService->getNilaiDataForSiswaWithKetuntasan(
            $user->siswa->id,
            $guruKelas->pluck('id')->toArray()
        );

        // Ambil data nilai yang berlebih untuk warning
        $nilaiBerlebih = $this->penilaianService->getNilaiSiswaBerlebih(
            $user->siswa->id,
            $guruKelas->pluck('id')->toArray()
        );

        return view('siswa.penilaian.index', compact(
            'guruKelas',
            'jenisUjians',
            'nilaiData',
            'nilaiBerlebih'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $tahunAkademik = TahunAkademik::aktif()->first();

        // Validasi input
        $request->validate([
            'guru_kelas_id' => 'required|array',
            'guru_kelas_id.*' => 'exists:guru_kelas,id',
            'nilai' => 'required|array',
            'nilai.*.*' => 'nullable|numeric|min:0|max:100',
            'semester' => 'required|array',
            'semester.*.*' => 'required|in:ganjil,genap',
        ]);

        try {
            // Filter hanya nilai yang terisi beserta semesternya
            $nilaiFiltered = [];
            $semesterFiltered = [];

            foreach ($request->nilai as $guruKelasId => $jenisUjianNilai) {
                foreach ($jenisUjianNilai as $jenisUjianId => $nilai) {
                    if ($nilai !== null && $nilai !== '' && is_numeric($nilai)) {
                        $nilaiFiltered[$guruKelasId][$jenisUjianId] = $nilai;

                        // Ambil semester dari input hidden
                        if (isset($request->semester[$guruKelasId][$jenisUjianId])) {
                            $semesterFiltered[$guruKelasId][$jenisUjianId] = $request->semester[$guruKelasId][$jenisUjianId];
                        }
                    }
                }
            }

            // Cek apakah ada nilai yang diinput
            if (empty($nilaiFiltered)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Harap isi minimal satu nilai sebelum menyimpan!'
                ], 422);
            }

            // Validasi nilai terhadap nilai guru
            $validationResult = $this->penilaianService->validateNilaiSiswaAgainstGuru(
                $user->siswa->id,
                $nilaiFiltered
            );

            if (!$validationResult['valid']) {
                return response()->json([
                    'success' => false,
                    'errors' => $validationResult['errors'],
                    'message' => 'Terdapat nilai yang melebihi nilai yang diinput oleh guru!'
                ], 422);
            }

            // Simpan nilai yang terisi saja dengan semester masing-masing
            $this->penilaianService->storeNilaiMapelBySiswa([
                'siswa_id' => $user->siswa->id,
                'tahun_akademik_id' => $tahunAkademik->id,
                'kelas_id' => $user->siswa->current_class_id,
                'nilai' => $nilaiFiltered,
                'semester' => $semesterFiltered,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nilai berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get nilai guru untuk validasi real-time
     */
    public function getNilaiGuru(Request $request)
    {
        try {
            $nilaiGuru = $this->penilaianService->getNilaiGuruForValidation(
                $request->guru_kelas_id,
                $request->jenis_ujian_id,
                Auth::user()->siswa->id
            );

            return response()->json([
                'success' => true,
                'nilai' => $nilaiGuru
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
