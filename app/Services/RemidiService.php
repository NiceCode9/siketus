<?php

namespace App\Services;

use App\Models\RemidiSiswa;
use App\Models\PenilaianMapel;
use Illuminate\Support\Facades\DB;

class RemidiService
{
    /**
     * Create remidi record
     */
    public function createRemidi(array $data)
    {
        return RemidiSiswa::create([
            'siswa_id' => $data['siswa_id'],
            'penilaian_mapel_id' => $data['penilaian_mapel_id'],
            'guru_kelas_id' => $data['guru_kelas_id'],
            'jenis_ujian_id' => $data['jenis_ujian_id'],
            'tahun_akademik_id' => $data['tahun_akademik_id'],
            'kelas_id' => $data['kelas_id'],
            'semester' => $data['semester'],
            'nilai_asli' => $data['nilai_asli'],
            'kkm' => $data['kkm'],
            'nilai_akhir' => $data['nilai_asli'], // Awalnya sama dengan nilai_asli
            'status_remidi' => 'pending',
            'is_notified' => true,
        ]);
    }

    /**
     * Update or create remidi
     */
    public function updateOrCreateRemidi($penilaianMapelId, array $data)
    {
        return RemidiSiswa::updateOrCreate(
            ['penilaian_mapel_id' => $penilaianMapelId],
            [
                'siswa_id' => $data['siswa_id'],
                'guru_kelas_id' => $data['guru_kelas_id'],
                'jenis_ujian_id' => $data['jenis_ujian_id'],
                'tahun_akademik_id' => $data['tahun_akademik_id'],
                'kelas_id' => $data['kelas_id'],
                'semester' => $data['semester'],
                'nilai_asli' => $data['nilai_asli'],
                'kkm' => $data['kkm'],
                'nilai_akhir' => $data['nilai_asli'],
                'status_remidi' => 'pending',
                'is_notified' => true,
            ]
        );
    }

    /**
     * Input nilai remidi by guru
     */
    public function inputNilaiRemidi($remidiId, $nilaiRemidi, $keterangan = null)
    {
        DB::beginTransaction();
        try {
            $remidi = RemidiSiswa::findOrFail($remidiId);

            // Update nilai remidi
            $remidi->nilai_remidi = $nilaiRemidi;
            $remidi->tanggal_remidi = now();
            $remidi->keterangan = $keterangan;

            // Calculate nilai akhir (MAX of nilai_asli and nilai_remidi)
            $remidi->nilai_akhir = max($remidi->nilai_asli, $nilaiRemidi);

            // Check if remidi completed
            if ($remidi->nilai_akhir >= $remidi->kkm) {
                $remidi->status_remidi = 'selesai';
                $remidi->is_notified = false; // Hide notification

                // Update penilaian_mapel
                $penilaian = PenilaianMapel::find($remidi->penilaian_mapel_id);
                if ($penilaian) {
                    $penilaian->nilai = $remidi->nilai_akhir;
                    $penilaian->status_ketuntasan = 'tuntas';
                    $penilaian->save();
                }
            } else {
                // Nilai remidi masih < KKM, tetap pending
                $remidi->status_remidi = 'pending';
                $remidi->is_notified = true; // Keep notification
            }

            $remidi->save();

            DB::commit();
            return [
                'success' => true,
                'remidi' => $remidi,
                'is_completed' => $remidi->status_remidi === 'selesai',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel remidi (when nilai asli updated and becomes tuntas)
     */
    public function cancelRemidi($penilaianMapelId)
    {
        $remidi = RemidiSiswa::where('penilaian_mapel_id', $penilaianMapelId)->first();

        if ($remidi) {
            $remidi->status_remidi = 'batal';
            $remidi->is_notified = false;
            $remidi->save();
        }

        return $remidi;
    }

    /**
     * Get remidi list by guru kelas
     */
    public function getRemidiByGuruKelas($guruKelasId, $statusRemidi = null)
    {
        $query = RemidiSiswa::with([
            'siswa',
            'jenisUjian',
            'guruKelas.guruMapel.mapel',
            'kelas'
        ])->where('guru_kelas_id', $guruKelasId);

        if ($statusRemidi) {
            $query->where('status_remidi', $statusRemidi);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get remidi list by siswa
     */
    public function getRemidiBySiswa($siswaId, $statusRemidi = null)
    {
        $query = RemidiSiswa::with([
            'jenisUjian',
            'guruKelas.guruMapel.mapel',
            'kelas'
        ])->where('siswa_id', $siswaId);

        if ($statusRemidi) {
            $query->where('status_remidi', $statusRemidi);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get unread remidi notifications for siswa
     */
    public function getUnreadRemidiNotifications($siswaId)
    {
        return RemidiSiswa::with([
            'jenisUjian',
            'guruKelas.guruMapel.mapel',
            'guruKelas.guruMapel.guru'
        ])
            ->where('siswa_id', $siswaId)
            ->unreadNotification()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mark remidi as read
     */
    public function markAsRead($remidiId)
    {
        $remidi = RemidiSiswa::findOrFail($remidiId);
        $remidi->is_notified = false;
        $remidi->save();

        return $remidi;
    }

    /**
     * Get remidi statistik for siswa
     */
    public function getRemidiStatistikSiswa($siswaId)
    {
        $total = RemidiSiswa::where('siswa_id', $siswaId)->count();
        $pending = RemidiSiswa::where('siswa_id', $siswaId)->pending()->count();
        $selesai = RemidiSiswa::where('siswa_id', $siswaId)->selesai()->count();
        $batal = RemidiSiswa::where('siswa_id', $siswaId)->where('status_remidi', 'batal')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'selesai' => $selesai,
            'batal' => $batal,
        ];
    }

    /**
     * Get remidi statistik for guru kelas
     */
    public function getRemidiStatistikGuruKelas($guruKelasId)
    {
        $total = RemidiSiswa::where('guru_kelas_id', $guruKelasId)->count();
        $pending = RemidiSiswa::where('guru_kelas_id', $guruKelasId)->pending()->count();
        $selesai = RemidiSiswa::where('guru_kelas_id', $guruKelasId)->selesai()->count();

        // Get siswa unique yang remidi
        $siswaRemidi = RemidiSiswa::where('guru_kelas_id', $guruKelasId)
            ->where('status_remidi', 'pending')
            ->distinct('siswa_id')
            ->count('siswa_id');

        return [
            'total' => $total,
            'pending' => $pending,
            'selesai' => $selesai,
            'siswa_remidi' => $siswaRemidi,
        ];
    }

    /**
     * Get remidi by mapel (for statistik)
     */
    public function getRemidiByMapel($guruId, $tahunAkademikId)
    {
        return RemidiSiswa::with(['guruKelas.guruMapel.mapel'])
            ->whereHas('guruKelas.guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('status_remidi', 'pending')
            ->get()
            ->groupBy('guruKelas.guruMapel.mapel.nama_mapel')
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc();
    }
}
