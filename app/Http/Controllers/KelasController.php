<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kelas::with('jurusan');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-info btn-sm view-btn" data-id="' . $row->id . '" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="' . $row->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '" data-nama="' . $row->nama_kelas . '" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>';
                    return $btn;
                })
                ->addColumn('jurusan_nama', function ($row) {
                    return $row->jurusan ? $row->jurusan->nama_jurusan : '-';
                })
                ->addColumn('nama_lengkap', function ($row) {
                    return $row->nama_lengkap;
                })
                ->addColumn('jumlah_siswa', function ($row) {
                    return $row->siswa->count();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $jurusan = Jurusan::all();
        // $tingkat = ['X', 'XI', 'XII'];
        $tingkat = ['10', '11', '12'];

        return view('master.kelas', compact('jurusan', 'tingkat'));
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
            'jurusan_id' => 'required|exists:jurusan,id',
            // 'tingkat' => 'required|in:X,XI,XII',
            'tingkat' => 'required|in:10,11,12',
            'nama_kelas' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Check for duplicate
            $existingKelas = Kelas::where('jurusan_id', $request->jurusan_id)
                ->where('tingkat', $request->tingkat)
                ->where('nama_kelas', $request->nama_kelas)
                ->first();

            if ($existingKelas) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas dengan kombinasi jurusan, tingkat, dan nama kelas tersebut sudah ada!'
                ], 422);
            }

            Kelas::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data kelas berhasil disimpan!'
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
    public function show(Kelas $kela)
    {
        $kela->load('jurusan', 'siswa');
        return response()->json([
            'kelas' => $kela,
            'jurusan' => $kela->jurusan,
            'siswa_count' => $kela->siswa->count()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        return response()->json($kela);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $validator = Validator::make($request->all(), [
            'jurusan_id' => 'required|exists:jurusan,id',
            // 'tingkat' => 'required|in:X,XI,XII',
            'tingkat' => 'required|in:10,11,12',
            'nama_kelas' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            // Check for duplicate excluding current record
            $existingKelas = Kelas::where('jurusan_id', $request->jurusan_id)
                ->where('tingkat', $request->tingkat)
                ->where('nama_kelas', $request->nama_kelas)
                ->where('id', '!=', $kela->id)
                ->first();

            if ($existingKelas) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kelas dengan kombinasi jurusan, tingkat, dan nama kelas tersebut sudah ada!'
                ], 422);
            }

            $kela->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data kelas berhasil diupdate!'
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
    public function destroy(Kelas $kela)
    {
        try {
            // Check if kelas has students
            if ($kela->siswa()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak dapat menghapus kelas karena masih memiliki siswa!'
                ], 422);
            }

            $kela->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data kelas berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kelas by jurusan and tingkat
     */
    public function getByJurusanTingkat(Request $request)
    {
        $kelas = Kelas::where('jurusan_id', $request->jurusan_id)
            ->where('tingkat', $request->tingkat)
            ->get();

        return response()->json($kelas);
    }
}
