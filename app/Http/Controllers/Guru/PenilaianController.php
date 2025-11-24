<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\KkmService;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    protected $penilaianService;
    protected $kkmService;

    public function __construct(PenilaianService $penilaianService, KkmService $kkmService)
    {
        $this->penilaianService = $penilaianService;
        $this->kkmService = $kkmService;
    }

    public function index(Request $request)
    {
        $tahunAkademiks = TahunAkademik::orderBy('status_aktif', 'desc')
            ->get();

        $selectedTahunAkademik = $request->tahun_akademik_id ?? TahunAkademik::where('status_aktif', true)->first()?->id;
        $selectedKelas = $request->kelas_id;
        $selectedSemester = $request->semester;
        $selectedKategori = $request->kategori;
        $selectedMapel = $request->mapel_id;

        $guru = Auth::user()->guru;
        $kelasList = [];
        $siswaList = [];
        $jenisUjianList = [];
        $kedisiplinanList = [];
        $kegiatanKeagamaanList = [];
        $guruKelas = null;
        $nilaiList = [];

        if ($selectedTahunAkademik) {
            // Get kelas yang diampu guru
            $kelasList = $this->penilaianService->getKelasListByGuru($guru->id, $selectedTahunAkademik);

            // Jika kelas dipilih, ambil siswa
            if ($selectedKelas && $selectedSemester && $selectedKategori) {
                $siswaList = $this->penilaianService->getSiswaByKelas($selectedKelas, $selectedTahunAkademik);

                // Get data berdasarkan kategori
                if ($selectedKategori === 'mapel') {
                    $jenisUjianList = $this->penilaianService->getJenisUjianList($selectedTahunAkademik);
                    $guruKelas = $this->penilaianService->getGuruKelas($guru->id, $selectedKelas, $selectedTahunAkademik, $selectedMapel);

                    $existingNilai = [];
                    foreach ($siswaList as $siswa) {
                        $nilaiData = $this->penilaianService->getFormData(
                            $siswa->id,
                            $selectedTahunAkademik,
                            $selectedKelas,
                            $selectedSemester,
                            $selectedKategori,
                            $guru->id,
                            $selectedMapel
                        );
                        $mappedNilai = [];

                        foreach ($nilaiData['existingNilai'] as $item) {
                            $mappedNilai[$item->jenis_ujian_id] = $item->nilai;
                        }

                        $existingNilai[$siswa->id]['existingNilai'] = $mappedNilai;
                    }
                    $nilaiList = $existingNilai;
                } elseif ($selectedKategori === 'kedisiplinan') {
                    if (!Auth::user()->can('penilaian-kedisiplinan')) {
                        abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
                    }
                    $kedisiplinanList = $this->penilaianService->getKedisiplinanList();

                    // Ambil data nilai kedisiplinan yang sudah ada
                    $existingNilai = [];
                    foreach ($siswaList as $siswa) {
                        $nilaiData = $this->penilaianService->getFormData(
                            $siswa->id,
                            $selectedTahunAkademik,
                            $selectedKelas,
                            $selectedSemester,
                            $selectedKategori,
                        );
                        $mappedNilai = [];

                        foreach ($nilaiData['existingNilai'] as $item) {
                            $mappedNilai[$item->kedisiplinan_id] = $item->validasi;
                        }

                        $existingNilai[$siswa->id]['existingNilai'] = $mappedNilai;
                    }
                    $nilaiList = $existingNilai;
                } elseif ($selectedKategori === 'keagamaan') {
                    $tingkatKelas = Kelas::find($selectedKelas)->tingkat;
                    $kegiatanKeagamaanList = $this->penilaianService->getKegiatanKeagamaanList($selectedTahunAkademik, $selectedSemester, $tingkatKelas);

                    // Ambil data nilai keagamaan yang sudah ada
                    $existingNilai = [];
                    foreach ($siswaList as $siswa) {
                        $nilaiData = $this->penilaianService->getFormData(
                            $siswa->id,
                            $selectedTahunAkademik,
                            $selectedKelas,
                            $selectedSemester,
                            $selectedKategori,
                        );
                        $mappedNilai = [];

                        foreach ($nilaiData['existingNilai'] as $item) {
                            $mappedNilai[$item->kegiatan_keagamaan_id] = $item->nilai;
                        }

                        $existingNilai[$siswa->id]['existingNilai'] = $mappedNilai;
                    }
                    $nilaiList = $existingNilai;
                }
            }
        }

        return view('guru.penilaian.index', compact(
            'tahunAkademiks',
            'kelasList',
            'siswaList',
            'selectedTahunAkademik',
            'selectedKelas',
            'selectedSemester',
            'selectedKategori',
            'jenisUjianList',
            'kedisiplinanList',
            'kegiatanKeagamaanList',
            'guruKelas',
            'selectedMapel',
            'nilaiList'
        ));
    }

    public function create(Request $request)
    {
        $siswa = Siswa::findOrFail($request->siswa_id);
        $tahunAkademik = TahunAkademik::findOrFail($request->tahun_akademik_id);
        $kelas = Kelas::findOrFail($request->kelas_id);
        $semester = $request->semester;
        $kategori = $request->kategori;
        $guru = Auth::user()->guru;

        // Get form data dari service (already includes KKM data)
        $formData = $this->penilaianService->getFormData(
            $siswa->id,
            $tahunAkademik->id,
            $kelas->id,
            $semester,
            $kategori,
            $guru->id,
            $request->mapel_id
        );

        $data = array_merge(
            compact('siswa', 'tahunAkademik', 'kelas', 'semester', 'kategori', 'guru'),
            $formData
        );

        return view('guru.penilaian.create', $data);
    }

    public function store(Request $request)
    {
        $rules = [
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:ganjil,genap',
            'kategori' => 'required|in:mapel,kedisiplinan,keagamaan',
        ];

        // Tambahkan validasi sesuai kategori
        if ($request->kategori === 'mapel') {
            $rules['mapel_id'] = 'required|exists:mapel,id';
            $rules['guru_kelas_id'] = 'required|exists:guru_kelas,id';
            $rules['nilai'] = 'required|array';
            $rules['nilai.*.*'] = 'nullable|numeric|min:0|max:100';
        } elseif ($request->kategori === 'kedisiplinan') {
            $rules['nilai'] = 'required|array';
            $rules['nilai.*.*'] = 'nullable|in:0,1'; // Validasi checkbox
        } elseif ($request->kategori === 'keagamaan') {
            $rules['nilai'] = 'required|array';
            $rules['nilai.*.*'] = 'nullable|numeric|min:0|max:100';
        }

        $validated = $request->validate($rules);

        $guru = Auth::user()->guru;

        try {
            // Prepare data untuk service
            $data = array_merge($validated, [
                'guru_id' => $guru->id,
                'nilai' => $request->nilai,
            ]);

            // Tambahkan guru_kelas_id jika kategori mapel
            if ($validated['kategori'] === 'mapel') {
                $data['guru_kelas_id'] = $validated['guru_kelas_id'];
            }

            // Call service untuk store penilaian
            $this->penilaianService->storePenilaian($data);

            $redirectParams = [
                'tahun_akademik_id' => $validated['tahun_akademik_id'],
                'kelas_id' => $validated['kelas_id'],
                'semester' => $validated['semester'],
                'kategori' => $validated['kategori'],
            ];

            if ($validated['kategori'] === 'mapel') {
                $redirectParams['mapel_id'] = $validated['mapel_id'];
            }

            return redirect()->route('guru.penilaian.index', $redirectParams)
                ->with('success', 'Penilaian berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
