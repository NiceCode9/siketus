<?php

namespace App\Http\Controllers;

use App\Models\KegiatanKeagamaan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KegiatanKeagamaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kegiatan = KegiatanKeagamaan::all();
            return response()->json(['data' => $kegiatan]);
        }
        return view('master.kegiatan-keagamaan');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kegiatan-keagamaan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255'
        ]);

        try {
            $kegiatan = KegiatanKeagamaan::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Data kegiatan keagamaan berhasil ditambahkan!',
                'data' => $kegiatan
            ], 201);
        } catch (\Exception $e) {
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
            'data' => $kegiatanKeagamaan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KegiatanKeagamaan $kegiatanKeagamaan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kegiatanKeagamaan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KegiatanKeagamaan $kegiatanKeagamaan): JsonResponse
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255'
        ]);

        try {
            $kegiatanKeagamaan->update($request->all());
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
