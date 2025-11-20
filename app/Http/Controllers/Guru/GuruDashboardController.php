<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\GuruKelas;
use App\Models\JadwalPelajaran;
use App\Models\Pertemuan;
use App\Models\Absensi;
use App\Models\RemidiSiswa;
use App\Models\PenilaianMapel;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guru = $user->guru;
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', true)->first();

        // Kelas yang diajar
        $kelasYangDiajar = GuruKelas::with(['kelas', 'guruMapel.mapel'])
            ->whereHas('guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->where('aktif', true)
            ->get();

        // Jadwal mengajar hari ini
        $hariIni = Carbon::now()->locale('id')->dayName;
        $jadwalHariIni = JadwalPelajaran::with(['guruKelas.kelas', 'guruKelas.guruMapel.mapel'])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Pertemuan terbaru dengan absensi
        $pertemuanTerbaru = Pertemuan::with(['jadwalPelajaran.guruKelas.kelas', 'absensi'])
            ->whereHas('jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->latest('tanggal')
            ->limit(5)
            ->get();

        // Siswa yang perlu remidi
        $guruKelasIds = $kelasYangDiajar->pluck('id');
        $siswaRemidi = RemidiSiswa::with(['siswa', 'guruKelas.kelas', 'jenisUjian'])
            ->whereIn('guru_kelas_id', $guruKelasIds)
            ->where('status_remidi', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        // Statistik Nilai per Kelas
        $statistikNilai = [];
        foreach ($kelasYangDiajar as $guruKelas) {
            $avgNilai = PenilaianMapel::where('guru_kelas_id', $guruKelas->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
                ->avg('nilai');

            $statistikNilai[] = [
                'kelas' => $guruKelas->kelas->nama_lengkap ?? '-',
                'mapel' => $guruKelas->guruMapel->mapel->nama_mapel ?? '-',
                'rata_rata' => round($avgNilai, 2) ?? 0,
            ];
        }

        return view('dashboard.guru', compact(
            'kelasYangDiajar',
            'jadwalHariIni',
            'pertemuanTerbaru',
            'siswaRemidi',
            'statistikNilai',
            'tahunAkademikAktif'
        ));
    }
}
