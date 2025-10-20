<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\KalenderAkademik;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class KalenderAkademikController extends Controller
{
    public function index(Request $request)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        $kalenderList = KalenderAkademik::with('tahunAkademik')
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->orderBy('tanggal')
            ->paginate(20);

        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        return view('master.kalender.index', compact('kalenderList', 'tahunAkademikList', 'tahunAkademikId'));
    }

    public function create()
    {
        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();
        $jenisLiburList = ['nasional', 'sekolah', 'ujian', 'custom'];

        return view('master.kalender.create', compact('tahunAkademikList', 'jenisLiburList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tanggal' => 'required|date',
            'jenis_libur' => 'required|in:nasional,sekolah,ujian,custom',
            'keterangan' => 'required|string|max:255',
        ]);

        KalenderAkademik::create($validated);

        return redirect()->route('admin.kalender.index')
            ->with('success', 'Kalender akademik berhasil ditambahkan!');
    }

    public function edit(KalenderAkademik $kalender)
    {
        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();
        $jenisLiburList = ['nasional', 'sekolah', 'ujian', 'custom'];

        return view('master.kalender.edit', compact('kalender', 'tahunAkademikList', 'jenisLiburList'));
    }

    public function update(Request $request, KalenderAkademik $kalender)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tanggal' => 'required|date',
            'jenis_libur' => 'required|in:nasional,sekolah,ujian,custom',
            'keterangan' => 'required|string|max:255',
        ]);

        $kalender->update($validated);

        return redirect()->route('admin.kalender.index')
            ->with('success', 'Kalender akademik berhasil diupdate!');
    }

    public function destroy(KalenderAkademik $kalender)
    {
        $kalender->delete();

        return redirect()->route('admin.kalender.index')
            ->with('success', 'Kalender akademik berhasil dihapus!');
    }
}
