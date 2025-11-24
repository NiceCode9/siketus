<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Pertemuan;
use App\Models\Siswa;
use App\Models\TahunAkademik;
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
        $kelasId = $request->get('kelas');
        $kelasList = Kelas::with('jurusan')->get()
            ->sortBy('nama_lengkap')->values();
        $guruId = Auth::user()->guru_id; // Asumsi user login punya relasi ke guru

        $pertemuanList = Pertemuan::with([
            'jadwalPelajaran.guruKelas.guruMapel.guru',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'jadwalPelajaran.guruKelas.kelas'
        ])
            ->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->when($kelasId, function ($q) use ($kelasId) {
                $q->whereHas('jadwalPelajaran.guruKelas', function ($q2) use ($kelasId) {
                    $q2->where('kelas_id', $kelasId);
                });
            })
            ->where('tanggal', $tanggal)
            ->orderBy('jam_mulai_aktual')
            ->get();

        return view('guru.absensi.index', compact('pertemuanList', 'tanggal', 'kelasList', 'kelasId'));
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

            return redirect()->route('guru.absensi.index')
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

    /**
     * Get detail absensi untuk modal (AJAX)
     */
    public function detail(Pertemuan $pertemuan)
    {
        $pertemuan->load(['absensi.siswa']);

        $absensi = $pertemuan->absensi;

        // Hitung ringkasan
        $summary = [
            'hadir' => $absensi->where('status_kehadiran', 'hadir')->count(),
            'izin' => $absensi->where('status_kehadiran', 'izin')->count(),
            'sakit' => $absensi->where('status_kehadiran', 'sakit')->count(),
            'alpha' => $absensi->where('status_kehadiran', 'alpha')->count(),
        ];

        return response()->json([
            'pertemuan' => [
                'tanggal' => $pertemuan->tanggal->format('d/m/Y'),
                'pertemuan_ke' => $pertemuan->pertemuan_ke,
                'materi' => $pertemuan->materi,
            ],
            'absensi' => $absensi->map(function ($item) {
                return [
                    'siswa' => [
                        'nisn' => $item->siswa->nisn,
                        'nama' => $item->siswa->nama,
                    ],
                    'status_kehadiran' => $item->status_kehadiran,
                    'keterangan' => $item->keterangan,
                ];
            }),
            'summary' => $summary
        ]);
    }

    /**
     * NEW: History absensi per jadwal (untuk guru)
     * Menampilkan riwayat absensi untuk satu mata pelajaran tertentu
     */
    public function history(Request $request, $jadwalId)
    {
        $guruId = Auth::user()->guru_id;
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $semester = $request->get('semester');

        // Get tahun akademik aktif jika tidak dipilih
        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        $tahunAkademik = TahunAkademik::find($tahunAkademikId);
        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        // Tentukan rentang tanggal berdasarkan semester
        $tanggalMulai = null;
        $tanggalSelesai = null;

        if ($semester) {
            if ($semester === 'ganjil') {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_ganjil;
            } else {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_genap;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
            }
        } else {
            // Tampilkan semester saat ini
            $currentSemester = $tahunAkademik->getCurrentSemester();
            if ($currentSemester === 'ganjil') {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_ganjil;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_ganjil;
            } else {
                $tanggalMulai = $tahunAkademik->tanggal_mulai_genap;
                $tanggalSelesai = $tahunAkademik->tanggal_selesai_genap;
            }
            $semester = $currentSemester;
        }

        // Query pertemuan dengan absensi
        $pertemuanList = Pertemuan::with([
            'jadwalPelajaran.guruKelas.kelas',
            'jadwalPelajaran.guruKelas.guruMapel.mapel',
            'absensi.siswa'
        ])
            ->where('jadwal_pelajaran_id', $jadwalId)
            ->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        // Get info jadwal
        $jadwal = \App\Models\JadwalPelajaran::with([
            'guruKelas.kelas',
            'guruKelas.guruMapel.mapel',
            'guruKelas.guruMapel.guru'
        ])->findOrFail($jadwalId);

        // Statistik kehadiran
        $statistik = Absensi::select(
            DB::raw('COUNT(CASE WHEN status_kehadiran = "hadir" THEN 1 END) as hadir'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "izin" THEN 1 END) as izin'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "sakit" THEN 1 END) as sakit'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "alpha" THEN 1 END) as alpha'),
            DB::raw('COUNT(*) as total')
        )
            ->whereHas('pertemuan', function ($q) use ($jadwalId, $tanggalMulai, $tanggalSelesai) {
                $q->where('jadwal_pelajaran_id', $jadwalId)
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            })
            ->first();

        return view('guru.absensi.history', compact(
            'pertemuanList',
            'jadwal',
            'statistik',
            'tahunAkademikList',
            'tahunAkademikId',
            'semester',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }
}
