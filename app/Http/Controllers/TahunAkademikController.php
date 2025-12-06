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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun_akademik' => 'required|string|max:255',
            'tanggal_mulai_ganjil' => 'required|date',
            'tanggal_selesai_ganjil' => 'required|date|after:tanggal_mulai_ganjil',
            'tanggal_mulai_genap' => 'required|date|after:tanggal_selesai_ganjil',
            'tanggal_selesai_genap' => 'required|date|after:tanggal_mulai_genap',
            'status_aktif' => 'boolean',
        ], [
            'tanggal_selesai_ganjil.after' => 'Tanggal selesai semester ganjil harus setelah tanggal mulai.',
            'tanggal_mulai_genap.after' => 'Tanggal mulai semester genap harus setelah semester ganjil selesai.',
            'tanggal_selesai_genap.after' => 'Tanggal selesai semester genap harus setelah tanggal mulai.',
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
        // Tambahkan info semester saat ini
        $data = $tahunAkademik->toArray();
        $data['current_semester'] = $tahunAkademik->semester;
        $data['semester_label'] = $tahunAkademik->semester_label;

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $request->validate([
            'nama_tahun_akademik' => 'required|string|max:255',
            'tanggal_mulai_ganjil' => 'required|date',
            'tanggal_selesai_ganjil' => 'required|date|after:tanggal_mulai_ganjil',
            'tanggal_mulai_genap' => 'required|date|after:tanggal_selesai_ganjil',
            'tanggal_selesai_genap' => 'required|date|after:tanggal_mulai_genap',
            'status_aktif' => 'boolean',
        ], [
            'tanggal_selesai_ganjil.after' => 'Tanggal selesai semester ganjil harus setelah tanggal mulai.',
            'tanggal_mulai_genap.after' => 'Tanggal mulai semester genap harus setelah semester ganjil selesai.',
            'tanggal_selesai_genap.after' => 'Tanggal selesai semester genap harus setelah tanggal mulai.',
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
                // 'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'message' => 'Terjadi kesalahan: tahun akademik ini mungkin sedang digunakan oleh data lain.',
            ], 500);
        }
    }
}
