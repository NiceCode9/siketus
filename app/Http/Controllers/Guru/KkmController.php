<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use App\Models\GuruKelas;
use App\Services\KkmService;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KkmController extends Controller
{
    protected $kkmService;
    protected $penilaianService;

    public function __construct(KkmService $kkmService, PenilaianService $penilaianService)
    {
        $this->kkmService = $kkmService;
        $this->penilaianService = $penilaianService;
    }

    /**
     * Index - List KKM yang sudah diset
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $tahunAkademiks = TahunAkademik::orderBy('status_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        $selectedTahunAkademik = $request->tahun_akademik_id ?? TahunAkademik::where('status_aktif', true)->first()?->id;

        // Get kelas yang diampu guru
        $kelasList = $this->penilaianService->getKelasListByGuru($guru->id, $selectedTahunAkademik);

        // Get statistik KKM
        $statistik = $this->kkmService->getKkmStatistik($guru->id, $selectedTahunAkademik);

        return view('guru.kkm.index', compact(
            'tahunAkademiks',
            'selectedTahunAkademik',
            'kelasList',
            'statistik'
        ));
    }

    /**
     * Create/Edit - Form set KKM untuk guru kelas tertentu
     */
    public function create(Request $request)
    {
        $guruKelas = GuruKelas::with(['kelas', 'guruMapel.mapel', 'tahunAkademik'])
            ->findOrFail($request->guru_kelas_id);

        // Get jenis ujian with existing KKM
        $jenisUjianWithKkm = $this->kkmService->getJenisUjianWithKkm(
            $guruKelas->id,
            $guruKelas->tahun_akademik_id
        );

        return view('guru.kkm.create', compact('guruKelas', 'jenisUjianWithKkm'));
    }

    /**
     * Store - Simpan KKM
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_kelas_id' => 'required|exists:guru_kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'kkm' => 'required|array',
            'kkm.*' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            // Bulk set KKM
            $this->kkmService->bulkSetKkm(
                $validated['guru_kelas_id'],
                $validated['tahun_akademik_id'],
                $validated['kkm']
            );

            return redirect()->route('guru.kkm.index', [
                'tahun_akademik_id' => $validated['tahun_akademik_id']
            ])->with('success', 'KKM berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Show - Detail KKM untuk satu guru kelas
     */
    public function show($guruKelasId)
    {
        $guruKelas = GuruKelas::with(['kelas', 'guruMapel.mapel', 'tahunAkademik', 'kkmMapel.jenisUjian'])
            ->findOrFail($guruKelasId);

        return view('guru.kkm.show', compact('guruKelas'));
    }

    /**
     * Delete - Hapus satu KKM
     */
    public function destroy($id)
    {
        try {
            $this->kkmService->deleteKkm($id);

            return response()->json([
                'success' => true,
                'message' => 'KKM berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get KKM value for specific jenis ujian
     */
    public function getKkmValue(Request $request)
    {
        $kkm = $this->kkmService->getKkmValue(
            $request->guru_kelas_id,
            $request->jenis_ujian_id,
            $request->tahun_akademik_id
        );

        return response()->json([
            'success' => true,
            'kkm' => $kkm
        ]);
    }
}
