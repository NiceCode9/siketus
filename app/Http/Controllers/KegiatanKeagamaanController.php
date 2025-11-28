<?php

namespace App\Http\Controllers;

use App\Models\KegiatanKeagamaan;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class KegiatanKeagamaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kegiatan = KegiatanKeagamaan::with('tahunAkademik')->get();
            return response()->json(['data' => $kegiatan]);
        }
        return view('master.kegiatan-keagamaan');
    }

    /**
     * Get list tahun akademik untuk dropdown
     */
    public function getTahunAkademik(): JsonResponse
    {
        $tahunAkademik = TahunAkademik::orderBy('status_aktif', 'desc')
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tahunAkademik
        ]);
    }

    /**
     * Store a newly created resource in storage (Bulk Insert).
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kegiatan' => 'required|array|min:1',
            'kegiatan.*.nama_kegiatan' => 'required|string|max:255',
            'kegiatan.*.tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'kegiatan.*.tingkat_kelas' => 'required|in:10,11,12',
            'kegiatan.*.semester' => 'required|in:ganjil,genap',
        ], [
            'kegiatan.required' => 'Data kegiatan harus diisi',
            'kegiatan.*.nama_kegiatan.required' => 'Nama kegiatan harus diisi',
            'kegiatan.*.tahun_akademik_id.required' => 'Tahun akademik harus dipilih',
            'kegiatan.*.tahun_akademik_id.exists' => 'Tahun akademik tidak valid',
            'kegiatan.*.tingkat_kelas.required' => 'Tingkat kelas harus dipilih',
            'kegiatan.*.tingkat_kelas.in' => 'Tingkat kelas harus 10, 11, atau 12',
            'kegiatan.*.semester.required' => 'Semester harus dipilih',
            'kegiatan.*.semester.in' => 'Semester harus Ganjil atau Genap',
        ]);

        try {
            DB::beginTransaction();

            $dataKegiatan = [];
            foreach ($request->kegiatan as $item) {
                $dataKegiatan[] = [
                    'nama_kegiatan' => $item['nama_kegiatan'],
                    'tahun_akademik_id' => $item['tahun_akademik_id'],
                    'tingkat_kelas' => $item['tingkat_kelas'],
                    'semester' => $item['semester'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            KegiatanKeagamaan::insert($dataKegiatan);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($dataKegiatan) . ' data kegiatan keagamaan berhasil ditambahkan!',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KegiatanKeagamaan $kegiatanKeagamaan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kegiatanKeagamaan->load('tahunAkademik')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KegiatanKeagamaan $kegiatanKeagamaan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kegiatanKeagamaan->load('tahunAkademik')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KegiatanKeagamaan $kegiatanKeagamaan): JsonResponse
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tingkat_kelas' => 'required|in:10,11,12',
            'semester' => 'required|in:ganjil,genap',
        ], [
            'nama_kegiatan.required' => 'Nama kegiatan harus diisi',
            'tahun_akademik_id.required' => 'Tahun akademik harus dipilih',
            'tahun_akademik_id.exists' => 'Tahun akademik tidak valid',
            'tingkat_kelas.required' => 'Tingkat kelas harus dipilih',
            'tingkat_kelas.in' => 'Tingkat kelas harus 10, 11, atau 12',
            'semester.required' => 'Semester harus dipilih',
            'semester.in' => 'Semester harus Ganjil atau Genap',
        ]);

        try {
            $kegiatanKeagamaan->update($request->only([
                'nama_kegiatan',
                'tahun_akademik_id',
                'tingkat_kelas',
                'semester'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Data kegiatan keagamaan berhasil diupdate!',
                'data' => $kegiatanKeagamaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KegiatanKeagamaan $kegiatanKeagamaan): JsonResponse
    {
        try {
            $kegiatanKeagamaan->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data kegiatan keagamaan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
