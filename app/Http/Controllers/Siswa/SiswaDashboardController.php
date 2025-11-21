<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\PenilaianMapel;
use App\Models\RemidiSiswa;
use App\Models\Absensi;
use App\Models\JenisUjian;
use App\Models\TahunAkademik;
use App\Models\KalenderAkademik;
use App\Models\Kedisiplinan;
use App\Models\KegiatanKeagamaan;
use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SiswaDashboardController extends Controller
{
    public function index()
    {
        $cek = $this->cekKetuntasan();
        $user = Auth::user();
        $siswa = $user->siswa;
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', true)->first();

        // Profil & Kelas
        $kelasSekarang = $siswa->currentClass;

        // Jadwal Pelajaran Minggu Ini
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $jadwalMingguIni = JadwalPelajaran::with(['guruKelas.guruMapel.guru', 'guruKelas.guruMapel.mapel'])
            ->whereHas('guruKelas', function ($q) use ($kelasSekarang, $tahunAkademikAktif) {
                $q->where('kelas_id', $kelasSekarang->id ?? null)
                    ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null);
            })
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('jam_mulai')
            ->get();

        // Nilai Terbaru (Per Mata Pelajaran)
        $nilaiTerbaru = PenilaianMapel::with(['guruKelas.guruMapel.mapel', 'jenisUjian'])
            ->where('siswa_id', $siswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->latest()
            ->limit(10)
            ->get();

        // Status Remidi
        $remidiPending = RemidiSiswa::with(['guruKelas.guruMapel.mapel', 'jenisUjian'])
            ->where('siswa_id', $siswa->id)
            ->where('status_remidi', 'pending')
            ->where('is_notified', true)
            ->get();

        // Rekap Absensi
        $rekapAbsensi = Absensi::where('siswa_id', $siswa->id)
            ->whereHas('pertemuan', function ($q) use ($tahunAkademikAktif) {
                $q->whereHas('jadwalPelajaran.guruKelas', function ($q2) use ($tahunAkademikAktif) {
                    $q2->where('tahun_akademik_id', $tahunAkademikAktif->id ?? null);
                });
            })
            ->select('status_kehadiran', DB::raw('count(*) as total'))
            ->groupBy('status_kehadiran')
            ->pluck('total', 'status_kehadiran');

        // Kalender Akademik (Event Penting Bulan Ini)
        $kalenderBulanIni = KalenderAkademik::where('tahun_akademik_id', $tahunAkademikAktif->id ?? null)
            ->whereMonth('tanggal', Carbon::now()->month)
            ->whereYear('tanggal', Carbon::now()->year)
            ->orderBy('tanggal')
            ->get();

        return view('dashboard.siswa', compact(
            'siswa',
            'kelasSekarang',
            'jadwalMingguIni',
            'nilaiTerbaru',
            'remidiPending',
            'rekapAbsensi',
            'kalenderBulanIni',
            'tahunAkademikAktif',
            'cek'
        ));
    }

    private function cekKetuntasan()
    {
        $tahunakademik = TahunAkademik::where('status_aktif', true)->first()->id;
        $jenisUjian = JenisUjian::where('tahun_akademik_id', $tahunakademik)->count();
        $mapel_master = Mapel::count();
        $keagamaan_master = KegiatanKeagamaan::where('tahun_akademik_id', $tahunakademik)->count();
        $kedisiplinan_master = Kedisiplinan::count();

        $siswa = Auth::user()->siswa;
        $mapel = $siswa->penilaianMapel()
            ->where('tahun_akademik_id', $tahunakademik)
            ->count();
        $keagamaan = $siswa->penilaianKeagamaan()
            ->where('tahun_akademik_id', $tahunakademik)
            ->count();
        $kedisiplinan = $siswa->penilaianKedisiplinan()
            ->where('tahun_akademik_id', $tahunakademik)
            ->count();

        $data = [
            'mapel' => true,
            'kedisiplinan' => true,
            'keagamaan' => true,
        ];

        if ($mapel < ($jenisUjian + $mapel_master)) {
            $data['mapel'] = false;
        }
        if ($kedisiplinan < $kedisiplinan_master) {    
            $data['kedisiplinan'] = false;
        }
        if ($keagamaan < $keagamaan_master) {
            $data['keagamaan'] = false;
        }

        if (in_array(false, $data)) {
            return false;
        }

        return true;
    }
}
