<?php

namespace App\Http\Controllers;

use App\Models\Kedisiplinan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KedisiplinanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $kedisiplinan = Kedisiplinan::all();
            return response()->json($kedisiplinan);
        }
        return view('master.kedisiplinan');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'jenis' => 'required|string|max:255'
        ]);

        try {
            $kedisiplinan = Kedisiplinan::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Data kedisiplinan berhasil ditambahkan!',
                'data' => $kedisiplinan
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
    public function show(Kedisiplinan $kedisiplinan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kedisiplinan
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kedisiplinan $kedisiplinan): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $kedisiplinan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kedisiplinan $kedisiplinan): JsonResponse
    {
        $request->validate([
            'jenis' => 'required|string|max:255'
        ]);

        try {
            $kedisiplinan->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Data kedisiplinan berhasil diperbarui!',
                'data' => $kedisiplinan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kedisiplinan $kedisiplinan): JsonResponse
    {
        try {
            $kedisiplinan->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data kedisiplinan berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
