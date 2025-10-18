<?php

namespace App\Http\Controllers;

use App\Models\JenisUjian;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class JenisUjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisUjian::with('tahunAkademik')->select('*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-info btn-sm view-btn" data-id="' . $row->id . '" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="' . $row->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '" data-nama="' . $row->nama_jenis_ujian . '" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>';
                    return $btn;
                })
                ->addColumn('tahun_akademik', function ($row) {
                    return $row->tahunAkademik ? $row->tahunAkademik->nama_tahun_akademik : '-';
                })
                ->addColumn('status_tahun', function ($row) {
                    if ($row->tahunAkademik) {
                        $badge = $row->tahunAkademik->status_aktif ? 'success' : 'secondary';
                        $text = $row->tahunAkademik->status_aktif ? 'Aktif' : 'Non-Aktif';
                        return '<span class="badge badge-' . $badge . '">' . ucfirst($text) . '</span>';
                    }
                    return '-';
                })
                ->rawColumns(['action', 'status_tahun'])
                ->make(true);
        }

        $tahunAkademik = TahunAkademik::orderBy('nama_tahun_akademik', 'desc')->get();
        return view('master.jenis-ujian', compact('tahunAkademik'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama_jenis_ujian' => 'required',
            'deskripsi' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Check for duplicate jenis ujian in the same tahun akademik
            $existingJenisUjian = JenisUjian::where('tahun_akademik_id', $request->tahun_akademik_id)
                ->where('nama_jenis_ujian', $request->nama_jenis_ujian)
                ->first();

            if ($existingJenisUjian) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jenis ujian dengan nama tersebut sudah ada pada tahun akademik yang dipilih!'
                ], 422);
            }

            JenisUjian::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data jenis ujian berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(JenisUjian $jenisUjian)
    {
        $jenisUjian->load('tahunAkademik');
        return response()->json([
            'jenis_ujian' => $jenisUjian,
            'tahun_akademik' => $jenisUjian->tahunAkademik
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisUjian $jenisUjian)
    {
        $jenisUjian->load('tahunAkademik');
        return response()->json($jenisUjian);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisUjian $jenisUjian)
    {
        $validator = Validator::make($request->all(), [
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama_jenis_ujian' => 'required',
            'deskripsi' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Check for duplicate jenis ujian in the same tahun akademik excluding current record
            $existingJenisUjian = JenisUjian::where('tahun_akademik_id', $request->tahun_akademik_id)
                ->where('nama_jenis_ujian', $request->nama_jenis_ujian)
                ->where('id', '!=', $jenisUjian->id)
                ->first();

            if ($existingJenisUjian) {
                return response()->json([
                    'status' => false,
                    'message' => 'Jenis ujian dengan nama tersebut sudah ada pada tahun akademik yang dipilih!'
                ], 422);
            }

            $jenisUjian->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data jenis ujian berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisUjian $jenisUjian)
    {
        try {
            // Check if jenis ujian has relations (you can add relation checks here if needed)
            // For example: if ($jenisUjian->ujian()->count() > 0) { ... }

            $jenisUjian->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data jenis ujian berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get jenis ujian by tahun akademik
     */
    public function getByTahunAkademik(Request $request)
    {
        $jenisUjian = JenisUjian::where('tahun_akademik_id', $request->tahun_akademik_id)
            ->orderBy('nama_jenis_ujian')
            ->get();

        return response()->json($jenisUjian);
    }
}
