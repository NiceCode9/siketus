<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class TahunAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tahunAkademik = TahunAkademik::orderBy('created_at', 'desc')->get();
        return view('master.tahun-akademik', compact('tahunAkademik'));
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
            'nama_tahun_akademik' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_aktif' => 'boolean',
        ]);

        try {
            // Jika status aktif true, nonaktifkan semua tahun akademik lainnya
            if ($request->status_aktif) {
                TahunAkademik::where('status_aktif', true)->update(['status_aktif' => false]);
            }

            TahunAkademik::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tahun Akademik berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TahunAkademik $tahunAkademik)
    {
        return response()->json($tahunAkademik);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TahunAkademik $tahunAkademik)
    {
        // return view('master.tahun-akademik-edit', compact('tahunAkademik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $request->validate([
            'nama_tahun_akademik' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_aktif' => 'boolean',
        ]);

        try {
            // Jika status aktif true, nonaktifkan semua tahun akademik lainnya
            if ($request->status_aktif) {
                TahunAkademik::where('status_aktif', true)
                    ->where('id', '!=', $tahunAkademik->id)
                    ->update(['status_aktif' => false]);
            }

            $tahunAkademik->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tahun Akademik berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TahunAkademik $tahunAkademik)
    {
        try {
            $tahunAkademik->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tahun Akademik berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
