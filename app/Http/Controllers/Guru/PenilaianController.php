<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    public function index(Request $request)
    {
        $tahunAkademiks = TahunAkademik::orderBy('status_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
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
                } elseif ($selectedKategori === 'kedisiplinan') {
                    $kedisiplinanList = $this->penilaianService->getKedisiplinanList();
                } elseif ($selectedKategori === 'keagamaan') {
                    $kegiatanKeagamaanList = $this->penilaianService->getKegiatanKeagamaanList($selectedTahunAkademik, $selectedSemester);
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
            'selectedMapel'
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

        // Get form data dari service
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
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|in:ganjil,genap',
            'kategori' => 'required|in:mapel,kedisiplinan,keagamaan',
            'mapel_id' => 'required_if:kategori,mapel|exists:mapel,id',
        ]);

        $guru = Auth::user()->guru;

        try {
            // Prepare data untuk service
            $data = array_merge($validated, [
                'guru_id' => $guru->id,
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
                'mapel_id' => $request->mapel_id,
            ]);

            // Call service untuk store penilaian
            $this->penilaianService->storePenilaian($data);

            return redirect()->route('guru.penilaian.index', [
                'tahun_akademik_id' => $validated['tahun_akademik_id'],
                'kelas_id' => $validated['kelas_id'],
                'semester' => $validated['semester'],
                'kategori' => $validated['kategori'],
                'mapel_id' => $validated['mapel_id'],
            ])->with('success', 'Penilaian berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
