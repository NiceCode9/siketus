<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MapelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Mapel::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-info btn-sm view-btn" data-id="' . $row->id . '" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="' . $row->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '" data-nama="' . $row->nama_mapel . '" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>';
                    return $btn;
                })
                ->addColumn('guru_count', function ($row) {
                    return $row->guruMapel->count() . ' guru';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.mapel.mapel');
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
            'kode_pelajaran' => 'required|unique:mapel,kode_pelajaran',
            'nama_mapel' => 'required',
            'deskripsi' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            Mapel::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data mata pelajaran berhasil disimpan!'
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
    public function show(Mapel $mapel)
    {
        $mapel->load('guruMapel.guru');
        return response()->json([
            'mapel' => $mapel,
            'guru_count' => $mapel->guruMapel->count()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mapel $mapel)
    {
        return response()->json($mapel);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mapel $mapel)
    {
        $validator = Validator::make($request->all(), [
            'kode_pelajaran' => 'required|unique:mapel,kode_pelajaran,' . $mapel->id,
            'nama_mapel' => 'required',
            'deskripsi' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $mapel->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data mata pelajaran berhasil diupdate!'
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
    public function destroy(Mapel $mapel)
    {
        try {
            // Check if mapel has guru relations
            if ($mapel->guruMapel()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak dapat menghapus mata pelajaran karena masih memiliki guru pengajar!'
                ], 422);
            }

            $mapel->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data mata pelajaran berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
