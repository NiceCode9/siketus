<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Pertemuan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Tampilkan daftar pertemuan hari ini untuk guru
     */
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $guruId = Auth::user()->guru_id; // Asumsi user login punya relasi ke guru

        $pertemuanList = Pertemuan::with([
            'jadwalPelajaran.guruKelas.guruMapel.guru',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'jadwalPelajaran.guruKelas.kelas'
        ])
            ->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->where('tanggal', $tanggal)
            ->orderBy('jam_mulai_aktual')
            ->get();

        return view('guru.absensi.index', compact('pertemuanList', 'tanggal'));
    }

    /**
     * Form absensi untuk pertemuan tertentu
     */
    public function create(Pertemuan $pertemuan)
    {
        $pertemuan->load([
            'jadwalPelajaran.guruKelas.kelas',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'absensi.siswa'
        ]);

        // Ambil semua siswa di kelas
        $kelasId = $pertemuan->jadwalPelajaran->guruKelas->kelas_id;
        $siswaList = Siswa::where('current_class_id', $kelasId)
            ->orderBy('nama')
            ->get();

        // Ambil data absensi yang sudah ada
        $absensiData = $pertemuan->absensi->keyBy('siswa_id');

        return view('guru.absensi.create', compact('pertemuan', 'siswaList', 'absensiData'));
    }

    /**
     * Simpan atau update absensi
     */
    public function store(Request $request, Pertemuan $pertemuan)
    {
        $validated = $request->validate([
            'absensi' => 'required|array',
            'absensi.*.siswa_id' => 'required|exists:siswa,id',
            'absensi.*.status_kehadiran' => 'required|in:hadir,izin,sakit,alpha',
            'absensi.*.keterangan' => 'nullable|string',
            'materi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update pertemuan
            $pertemuan->update([
                'materi' => $request->materi,
                'jam_mulai_aktual' => $request->jam_mulai_aktual ?? now(),
                'status' => 'completed',
            ]);

            // Simpan absensi
            foreach ($validated['absensi'] as $data) {
                Absensi::updateOrCreate(
                    [
                        'pertemuan_id' => $pertemuan->id,
                        'siswa_id' => $data['siswa_id'],
                    ],
                    [
                        'status_kehadiran' => $data['status_kehadiran'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'waktu_absen' => now(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.absensi.index')
                ->with('success', 'Absensi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Edit absensi yang sudah ada
     */
    public function edit(Pertemuan $pertemuan)
    {
        return $this->create($pertemuan);
    }

    /**
     * Update absensi
     */
    public function update(Request $request, Pertemuan $pertemuan)
    {
        return $this->store($request, $pertemuan);
    }
}
