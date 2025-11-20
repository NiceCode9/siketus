<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Absensi;
use App\Models\RemidiSiswa;
use App\Models\PenilaianMapel;
use App\Models\TahunAkademik;
use App\Models\Pertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', true)->first();

        // Statistik Umum
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();

        // Statistik Absensi Hari Ini
        $today = Carbon::today();
        $pertemuanHariIni = Pertemuan::whereDate('tanggal', $today)
            ->pluck('id');

        $absensiHariIni = Absensi::whereIn('pertemuan_id', $pertemuanHariIni)
            ->select('status_kehadiran', DB::raw('count(*) as total'))
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran');

        // Siswa yang perlu Remidi
        $siswaRemidi = RemidiSiswa::with(['siswa', 'guruKelas.guruMapel.mapel', 'jenisUjian'])
            ->where('status_remidi', 'pending')
            ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->latest()
            ->limit(10)
            ->get();

        // Grafik Nilai per Bulan (6 bulan terakhir)
        $nilaiPerBulan = PenilaianMapel::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('AVG(nilai) as rata_rata')
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // Statistik Remidi
        $totalRemidi = RemidiSiswa::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)->count();
        $remidiPending = RemidiSiswa::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('status_remidi', 'pending')
            ->count();
        $remidiSelesai = RemidiSiswa::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('status_remidi', 'selesai')
            ->count();

        return view('dashboard.admin', compact(
            'totalSiswa',
            'totalGuru',
            'totalKelas',
            'absensiHariIni',
            'siswaRemidi',
            'nilaiPerBulan',
            'tahunAkademikAktif',
            'totalRemidi',
            'remidiPending',
            'remidiSelesai'
        ));
    }
}
