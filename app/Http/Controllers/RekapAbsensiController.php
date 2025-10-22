<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Siswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapAbsensiController extends Controller
{
    /**
     * Rekap absensi per kelas
     */
    public function perKelas(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $mapelId = $request->get('mapel_id');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        $kelasList = Kelas::orderBy('tingkat')->get();
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        $rekap = null;

        if ($kelasId && $tahunAkademikId) {
            $tahunAkademik = TahunAkademik::find($tahunAkademikId);

            $query = Absensi::select(
                'siswa.id as siswa_id',
                'siswa.nama as nama_siswa',
                'siswa.nisn',
                DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "hadir" THEN 1 END) as hadir'),
                DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "izin" THEN 1 END) as izin'),
                DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "sakit" THEN 1 END) as sakit'),
                DB::raw('COUNT(CASE WHEN absensi.status_kehadiran = "alpha" THEN 1 END) as alpha'),
                DB::raw('COUNT(*) as total_pertemuan')
            )
                ->join('siswa', 'absensi.siswa_id', '=', 'siswa.id')
                ->join('pertemuan', 'absensi.pertemuan_id', '=', 'pertemuan.id')
                ->join('jadwal_pelajaran', 'pertemuan.jadwal_pelajaran_id', '=', 'jadwal_pelajaran.id')
                ->join('guru_kelas', 'jadwal_pelajaran.guru_kelas_id', '=', 'guru_kelas.id')
                ->where('siswa.current_class_id', $kelasId)
                ->where('guru_kelas.tahun_akademik_id', $tahunAkademikId)
                ->whereBetween('pertemuan.tanggal', [
                    $tahunAkademik->tanggal_mulai,
                    $tahunAkademik->tanggal_selesai
                ]);

            if ($mapelId) {
                $query->join('guru_mapel', 'guru_kelas.guru_mapel_id', '=', 'guru_mapel.id')
                    ->where('guru_mapel.mapel_id', $mapelId);
            }

            $rekap = $query->groupBy('siswa.id', 'siswa.nama', 'siswa.nisn')
                ->orderBy('siswa.nama')
                ->get()
                ->map(function ($item) {
                    $item->persentase_hadir = $item->total_pertemuan > 0
                        ? round(($item->hadir / $item->total_pertemuan) * 100, 2)
                        : 0;
                    return $item;
                });
        }

        return view('rekap.per-kelas', compact(
            'rekap',
            'kelasList',
            'mapelList',
            'tahunAkademikList',
            'kelasId',
            'mapelId',
            'tahunAkademikId'
        ));
    }

    /**
     * Rekap absensi per siswa (detail)
     */
    public function perSiswa(Request $request, Siswa $siswa)
    {
        $tahunAkademikId = $request->get('tahun_akademik_id');
        $mapelId = $request->get('mapel_id');

        if (!$tahunAkademikId) {
            $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
            $tahunAkademikId = $tahunAkademik?->id;
        }

        $tahunAkademik = TahunAkademik::find($tahunAkademikId);
        $mapelList = Mapel::orderBy('nama_mapel')->get();
        $tahunAkademikList = TahunAkademik::orderBy('created_at', 'desc')->get();

        $query = Absensi::with([
            'pertemuan.jadwalPelajaran.guruKelas.guruMapel.mapel',
            'pertemuan.jadwalPelajaran.guruKelas.guruMapel.guru'
        ])
            ->where('siswa_id', $siswa->id)
            ->whereHas('pertemuan.jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->whereHas('pertemuan', function ($q) use ($tahunAkademik) {
                $q->whereBetween('tanggal', [
                    $tahunAkademik->tanggal_mulai,
                    $tahunAkademik->tanggal_selesai
                ]);
            });

        if ($mapelId) {
            $query->whereHas('pertemuan.jadwalPelajaran.guruKelas.guruMapel', function ($q) use ($mapelId) {
                $q->where('mapel_id', $mapelId);
            });
        }

        $absensiList = $query->orderBy('created_at', 'desc')->paginate(20);

        // Hitung ringkasan
        $ringkasan = Absensi::select(
            DB::raw('COUNT(CASE WHEN status_kehadiran = "hadir" THEN 1 END) as hadir'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "izin" THEN 1 END) as izin'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "sakit" THEN 1 END) as sakit'),
            DB::raw('COUNT(CASE WHEN status_kehadiran = "alpha" THEN 1 END) as alpha'),
            DB::raw('COUNT(*) as total')
        )
            ->where('siswa_id', $siswa->id)
            ->whereHas('pertemuan.jadwalPelajaran.guruKelas', function ($q) use ($tahunAkademikId) {
                $q->where('tahun_akademik_id', $tahunAkademikId);
            })
            ->first();

        $ringkasan->persentase_hadir = $ringkasan->total > 0
            ? round(($ringkasan->hadir / $ringkasan->total) * 100, 2)
            : 0;

        return view('rekap.per-siswa', compact(
            'siswa',
            'absensiList',
            'ringkasan',
            'mapelList',
            'tahunAkademikList',
            'mapelId',
            'tahunAkademikId'
        ));
    }
}
