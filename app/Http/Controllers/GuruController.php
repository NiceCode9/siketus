<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Guru::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-warning btn-sm edit-btn" data-id="' . $row->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '" data-nama="' . $row->nama . '" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.guru');
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
            'nip' => 'required|unique:guru,nip',
            'nama' => 'required',
            'bidang_keahlian' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $guru = Guru::create($request->all());
            $akun = $guru->akun()->create([
                'name' => $request->nama,
                'username' => $request->nip,
                'email' => $request->email ?? null,
                'password' => bcrypt('password'),
                'role' => 'guru',
            ]);

            $akun->assignRole('guru');

            return response()->json([
                'status' => true,
                'message' => 'Data guru berhasil disimpan!'
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
    public function show(Guru $guru)
    {
        return response()->json($guru);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guru $guru)
    {
        return response()->json($guru);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|unique:guru,nip,' . $guru->id,
            'nama' => 'required',
            'bidang_keahlian' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $guru->update($request->all());
            $guru->akun()->update([
                'name' => $request->nama,
                'username' => $request->nip,
                'email' => $request->email ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data guru berhasil diupdate!'
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
    public function destroy(Guru $guru)
    {
        try {
            // Cek apakah guru memiliki relasi dengan user
            // if ($guru->akun) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Tidak dapat menghapus guru karena memiliki akun user terkait!'
            //     ], 422);
            // }

            $guru->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data guru berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
