<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $jurusan = Jurusan::all();

            return response()->json([
                'success' => true,
                'data' => $jurusan
            ]);
        } else {
            return view('master.jurusan');
        }
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
        $request->validate([
            'nama_jurusan' => 'required|string|max:255|unique:jurusan',
            'kode_jurusan' => 'required|string|max:255|unique:jurusan'
        ]);

        $jurusan = Jurusan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil ditambahkan',
            'data' => $jurusan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurusan $jurusan)
    {
        return response()->json([
            'success' => true,
            'data' => $jurusan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jurusan $jurusan)
    {
        return response()->json([
            'success' => true,
            'data' => $jurusan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan,' . $jurusan->id,
            'kode_jurusan' => 'required|string|max:255|unique:jurusan,kode_jurusan,' . $jurusan->id
        ]);

        $jurusan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil diupdate',
            'data' => $jurusan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurusan $jurusan)
    {
        try {
            $jurusan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jurusan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jurusan'
            ], 500);
        }
    }
}
