<?php

namespace App\Http\Controllers;

use App\Models\GuruKelas;
use App\Models\GuruMapel;
use App\Models\Kelas;
use App\Models\TahunAkademik;
use App\Models\Guru;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class GuruKelasController extends Controller
{
    public function index()
    {
        $gurus = Guru::orderBy('nama')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $kelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $tahunAkademiks = TahunAkademik::orderBy('nama_tahun_akademik', 'desc')->get();

        return view('master.guru-kelas', compact('gurus', 'mapels', 'kelas', 'tahunAkademiks'));
    }

    public function getData(Request $request)
    {
        $query = GuruKelas::with([
            'guruMapel.guru',
            'guruMapel.mapel',
            'kelas.jurusan',
            'tahunAkademik'
        ]);

        // Filter by Guru
        if ($request->filled('guru_id')) {
            $query->whereHas('guruMapel', function ($q) use ($request) {
                $q->where('guru_id', $request->guru_id);
            });
        }

        // Filter by Mapel
        if ($request->filled('mapel_id')) {
            $query->whereHas('guruMapel', function ($q) use ($request) {
                $q->where('mapel_id', $request->mapel_id);
            });
        }

        // Filter by Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by Tahun Akademik
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        // Filter by Status
        if ($request->filled('aktif')) {
            $query->where('aktif', $request->aktif);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('guru_nama', function ($row) {
                return $row->guruMapel->guru->nama ?? '-';
            })
            ->addColumn('mapel_nama', function ($row) {
                return $row->guruMapel->mapel->nama_mapel ?? '-';
            })
            ->addColumn('kelas_nama', function ($row) {
                return $row->kelas->nama_lengkap ?? '-';
            })
            ->addColumn('tahun_akademik_nama', function ($row) {
                return $row->tahunAkademik->nama_tahun_akademik ?? '-';
            })
            ->addColumn('status', function ($row) {
                if ($row->aktif) {
                    return '<span class="badge badge-success">Aktif</span>';
                }
                return '<span class="badge badge-secondary">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($row) {
                $editBtn = '<button class="btn btn-sm btn-warning edit-btn" data-id="' . $row->id . '"><i class="fas fa-edit"></i></button>';
                $deleteBtn = '<button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                return $editBtn . ' ' . $deleteBtn;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'aktif' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        // Cek duplikasi: guru_mapel + kelas + tahun akademik harus unik
        $exists = GuruKelas::where('guru_mapel_id', $validated['guru_mapel_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->where('tahun_akademik_id', $validated['tahun_akademik_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru kelas dengan kombinasi Guru Mapel, Kelas, dan Tahun Akademik ini sudah ada!'
            ], 422);
        }

        try {
            DB::beginTransaction();

            GuruKelas::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru kelas berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $guruKelas = GuruKelas::with([
            'guruMapel.guru',
            'guruMapel.mapel',
            'kelas',
            'tahunAkademik'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $guruKelas
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'guru_mapel_id' => 'required|exists:guru_mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'aktif' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        // Cek duplikasi (kecuali data yang sedang di-update)
        $exists = GuruKelas::where('guru_mapel_id', $validated['guru_mapel_id'])
            ->where('kelas_id', $validated['kelas_id'])
            ->where('tahun_akademik_id', $validated['tahun_akademik_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Data guru kelas dengan kombinasi Guru Mapel, Kelas, dan Tahun Akademik ini sudah ada!'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $guruKelas = GuruKelas::findOrFail($id);
            $guruKelas->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru kelas berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $guruKelas = GuruKelas::findOrFail($id);
            $guruKelas->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data guru kelas berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getGuruMapel(Request $request)
    {
        $guruId = $request->get('guru_id');
        $mapelId = $request->get('mapel_id');

        $query = GuruMapel::with(['guru', 'mapel']);

        if ($guruId) {
            $query->where('guru_id', $guruId);
        }

        if ($mapelId) {
            $query->where('mapel_id', $mapelId);
        }

        $guruMapels = $query->get();

        return response()->json([
            'success' => true,
            'data' => $guruMapels
        ]);
    }
}
