<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\TahunAkademik;
use App\Models\Pertemuan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalGuruController extends Controller
{
    public function index()
    {
        $guru = Auth::user()->guru;
        $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();

        if (!$tahunAkademik) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif!');
        }

        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');
        $hariMapping = [
            'Minggu' => 'Minggu',
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
            'Sabtu' => 'Sabtu'
        ];
        $hariIni = $hariMapping[$hariIni] ?? 'Senin';

        // Ambil semua jadwal guru
        $jadwalQuery = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'guruKelas.tahunAkademik'
        ])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->whereHas('guruKelas', function ($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik->id);
            });

        // Jadwal hari ini
        $jadwalHariIni = (clone $jadwalQuery)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Cek jadwal yang sedang berlangsung
        $sekarang = Carbon::now();
        $jadwalSedangBerlangsung = $jadwalHariIni->first(function ($jadwal) use ($sekarang) {
            $jamMulai = Carbon::parse($jadwal->jam_mulai);
            $jamSelesai = Carbon::parse($jadwal->jam_selesai);
            return $sekarang->between($jamMulai, $jamSelesai);
        });

        // Tandai jadwal yang akan datang
        foreach ($jadwalHariIni as $jadwal) {
            $jamMulai = Carbon::parse($jadwal->jam_mulai);
            $jadwal->isUpcoming = $sekarang->lessThan($jamMulai);
        }

        // Jadwal per hari untuk minggu ini
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = (clone $jadwalQuery)
                ->where('hari', $hari)
                ->orderBy('jam_mulai')
                ->get();
        }

        // Statistik
        $stats = [
            'total_jam_minggu' => $this->hitungTotalJamMinggu($jadwalQuery),
            'total_kelas' => $jadwalQuery->distinct('guru_kelas_id')->count(),
            'jadwal_hari_ini' => $jadwalHariIni->count(),
            'pertemuan_bulan_ini' => Pertemuan::whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
                ->whereMonth('tanggal', Carbon::now()->month)
                ->whereYear('tanggal', Carbon::now()->year)
                ->count()
        ];

        return view('guru.jadwal.index', compact(
            'jadwalHariIni',
            'jadwalSedangBerlangsung',
            'jadwalPerHari',
            'tahunAkademik',
            'hariIni',
            'stats'
        ));
    }

    private function hitungTotalJamMinggu($jadwalQuery)
    {
        $jadwal = (clone $jadwalQuery)->get();
        $totalMenit = 0;

        foreach ($jadwal as $j) {
            $jamMulai = Carbon::parse($j->jam_mulai);
            $jamSelesai = Carbon::parse($j->jam_selesai);
            $totalMenit += $jamMulai->diffInMinutes($jamSelesai);
        }

        return round($totalMenit / 60, 1);
    }

    public function show($id)
    {
        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'guruKelas.tahunAkademik',
            'pertemuan'
        ])->findOrFail($id);

        // Pastikan jadwal milik guru yang login
        $guru = Auth::user()->guru;
        if ($jadwal->guruKelas->guruMapel->guru_id != $guru->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $jadwal->id,
            'mapel' => $jadwal->guruKelas->guruMapel->mapel->nama_mapel,
            'kelas' => $jadwal->guruKelas->kelas->nama_kelas,
            'hari' => $jadwal->hari,
            'waktu' => Carbon::parse($jadwal->jam_mulai)->format('H:i') . ' - ' .
                Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            'ruangan' => $jadwal->ruangan ?? '-',
            'tahun_akademik' => $jadwal->guruKelas->tahunAkademik->nama_tahun_akademik,
            'total_pertemuan' => $jadwal->pertemuan->count(),
            'is_active' => $jadwal->guruKelas->aktif
        ]);
    }

    public function setReminder(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);
        $guru = Auth::user()->guru;

        // Validasi kepemilikan
        if ($jadwal->guruKelas->guruMapel->guru_id != $guru->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Logic untuk set reminder (bisa simpan ke database atau kirim notifikasi)
        // Implementasi sesuai kebutuhan sistem notifikasi Anda

        return response()->json([
            'status' => true,
            'message' => 'Reminder berhasil diaktifkan!'
        ]);
    }

    public function exportPdf()
    {
        $guru = Auth::user()->guru;
        $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();

        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'pertemuan',
        ])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->whereHas('guruKelas', function ($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik->id);
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = $jadwal->where('hari', $hari);
        }

        $type = 'daily';
        // dd($jadwalPerHari);

        $pdf = Pdf::loadView('guru.jadwal.pdf', compact(
            'jadwalPerHari',
            'guru',
            'tahunAkademik',
            'hariList',
            'type',
        ));

        return $pdf->stream('jadwal-mengajar-' . $guru->nama . '.pdf');
    }
}
