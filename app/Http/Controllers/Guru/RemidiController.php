<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use App\Models\GuruKelas;
use App\Models\RemidiSiswa;
use App\Services\RemidiService;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemidiController extends Controller
{
    protected $remidiService;
    protected $penilaianService;

    public function __construct(RemidiService $remidiService, PenilaianService $penilaianService)
    {
        $this->remidiService = $remidiService;
        $this->penilaianService = $penilaianService;
    }

    /**
     * Index - Daftar siswa yang perlu remidi
     */
    public function index(Request $request)
    {
        $guru = Auth::user()->guru;
        $tahunAkademiks = TahunAkademik::orderBy('status_aktif', 'desc')
            ->get();

        $selectedTahunAkademik = $request->tahun_akademik_id ?? TahunAkademik::where('status_aktif', true)->first()?->id;
        $mapelList = $this->penilaianService->getMapelListByGuru($guru->id, $selectedTahunAkademik);
        $selectedKelas = $request->kelas_id;
        $selectedMapel = $request->mapel_id;
        $selectedStatus = $request->status ?? 'pending';

        // Get kelas yang diampu guru
        $kelasList = $this->penilaianService->getKelasListByGuru($guru->id, $selectedTahunAkademik);

        $remidiList = collect();
        $statistik = null;
        $guruKelas = null;

        if ($selectedKelas && $selectedMapel) {
            // Get guru kelas
            $guruKelas = $this->penilaianService->getGuruKelas(
                $guru->id,
                $selectedKelas,
                $selectedTahunAkademik,
                $selectedMapel
            );

            if ($guruKelas) {
                // Get remidi list
                $remidiList = $this->remidiService->getRemidiByGuruKelas(
                    $guruKelas->id,
                    $selectedStatus
                );

                // Get statistik
                $statistik = $this->remidiService->getRemidiStatistikGuruKelas($guruKelas->id);
            }
        }

        return view('guru.remidi.index', compact(
            'tahunAkademiks',
            'selectedTahunAkademik',
            'selectedKelas',
            'selectedMapel',
            'selectedStatus',
            'kelasList',
            'remidiList',
            'statistik',
            'guruKelas',
            'mapelList'
        ));
    }

    /**
     * Show - Detail remidi siswa
     */
    public function show($id)
    {
        $remidi = RemidiSiswa::with([
            'siswa',
            'guruKelas.guruMapel.mapel',
            'guruKelas.guruMapel.guru',
            'jenisUjian',
            'kelas',
            'penilaianMapel'
        ])->findOrFail($id);

        // Authorization check
        $guru = Auth::user()->guru;
        if ($remidi->guruKelas->guruMapel->guru_id !== $guru->id) {
            abort(403, 'Anda tidak memiliki akses ke data ini');
        }

        return view('guru.remidi.show', compact('remidi'));
    }

    /**
     * Input nilai remidi
     */
    public function inputNilai(Request $request, $id)
    {
        $validated = $request->validate([
            'nilai_remidi' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        try {
            $result = $this->remidiService->inputNilaiRemidi(
                $id,
                $validated['nilai_remidi'],
                $validated['keterangan'] ?? null
            );

            if ($result['is_completed']) {
                $message = 'Nilai remidi berhasil disimpan. Siswa sudah tuntas!';
            } else {
                $message = 'Nilai remidi berhasil disimpan. Namun nilai masih di bawah KKM, siswa perlu remidi ulang.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_completed' => $result['is_completed'],
                'remidi' => $result['remidi']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk input nilai remidi
     */
    public function bulkInputNilai(Request $request)
    {
        $validated = $request->validate([
            'remidi_ids' => 'required|array',
            'remidi_ids.*' => 'exists:remidi_siswa,id',
            'nilai_remidi' => 'required|array',
            'nilai_remidi.*' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $results = [];
            $successCount = 0;
            $completedCount = 0;

            foreach ($validated['remidi_ids'] as $index => $remidiId) {
                $nilai = $validated['nilai_remidi'][$index];

                $result = $this->remidiService->inputNilaiRemidi($remidiId, $nilai);

                if ($result['success']) {
                    $successCount++;
                    if ($result['is_completed']) {
                        $completedCount++;
                    }
                }

                $results[] = $result;
            }

            return response()->json([
                'success' => true,
                'message' => "{$successCount} nilai remidi berhasil disimpan. {$completedCount} siswa tuntas.",
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistik remidi by mapel
     */
    // public function statistik(Request $request)
    // {
    //     $guru = Auth::user()->guru;
    //     $tahunAkademikId = $request->tahun_akademik_id ?? TahunAkademik::where('status_aktif', true)->first()?->id;

    //     $remidiByMapel = $this->remidiService->getRemidiByMapel($guru->id, $tahunAkademikId);

    //     return view('guru.remidi.statistik', compact('remidiByMapel', 'tahunAkademikId'));
    // }
}
