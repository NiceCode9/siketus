<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\GuruKelas;
use App\Models\JenisUjian;
use App\Models\TahunAkademik;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianSiswaController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    /**
     * Index
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tahunAkdemikId = TahunAkademik::aktif()->first()->id;
        $jenisUjians = JenisUjian::all();
        $guruKelas = GuruKelas::with('guruMapel.guru', 'guruMapel.mapel')->where('kelas_id', $user->siswa->current_class_id)
            ->where('tahun_akademik_id', $tahunAkdemikId)
            ->where('aktif', true)
            ->get();

        return view('siswa.penilaian.index', compact('guruKelas', 'jenisUjians'));
    }
}
