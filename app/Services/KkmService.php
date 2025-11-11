<?php

namespace App\Services;

use App\Models\KkmMapel;
use App\Models\GuruKelas;
use App\Models\JenisUjian;
use Illuminate\Support\Facades\DB;

class KkmService
{
    /**
     * Get KKM untuk guru kelas tertentu di tahun akademik
     */
    public function getKkmByGuruKelas($guruKelasId, $tahunAkademikId)
    {
        return KkmMapel::with('jenisUjian')
            ->where('guru_kelas_id', $guruKelasId)
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->get()
            ->keyBy('jenis_ujian_id');
    }

    /**
     * Get specific KKM by guru_kelas, jenis_ujian, tahun_akademik
     */
    public function getKkm($guruKelasId, $jenisUjianId, $tahunAkademikId)
    {
        return KkmMapel::byGuruKelasAndJenisUjian($guruKelasId, $jenisUjianId, $tahunAkademikId)
            ->first();
    }

    /**
     * Get KKM value (hanya nilai-nya)
     */
    public function getKkmValue($guruKelasId, $jenisUjianId, $tahunAkademikId)
    {
        $kkm = $this->getKkm($guruKelasId, $jenisUjianId, $tahunAkademikId);
        return $kkm ? $kkm->kkm : null;
    }

    /**
     * Set/Update KKM
     */
    public function setKkm(array $data)
    {
        return KkmMapel::updateOrCreate(
            [
                'guru_kelas_id' => $data['guru_kelas_id'],
                'jenis_ujian_id' => $data['jenis_ujian_id'],
                'tahun_akademik_id' => $data['tahun_akademik_id'],
            ],
            [
                'kkm' => $data['kkm'],
                'keterangan' => $data['keterangan'] ?? null,
            ]
        );
    }

    /**
     * Bulk set KKM untuk multiple jenis ujian
     */
    public function bulkSetKkm($guruKelasId, $tahunAkademikId, array $kkmData)
    {
        DB::beginTransaction();
        try {
            foreach ($kkmData as $jenisUjianId => $kkm) {
                if ($kkm !== null && $kkm !== '') {
                    $this->setKkm([
                        'guru_kelas_id' => $guruKelasId,
                        'jenis_ujian_id' => $jenisUjianId,
                        'tahun_akademik_id' => $tahunAkademikId,
                        'kkm' => $kkm,
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete KKM
     */
    public function deleteKkm($id)
    {
        $kkm = KkmMapel::findOrFail($id);
        return $kkm->delete();
    }

    /**
     * Get all jenis ujian with KKM status for guru kelas
     */
    public function getJenisUjianWithKkm($guruKelasId, $tahunAkademikId)
    {
        $jenisUjians = JenisUjian::where('tahun_akademik_id', $tahunAkademikId)->get();
        $kkmData = $this->getKkmByGuruKelas($guruKelasId, $tahunAkademikId);

        return $jenisUjians->map(function ($jenisUjian) use ($kkmData) {
            return [
                'jenis_ujian' => $jenisUjian,
                'kkm' => $kkmData->get($jenisUjian->id)?->kkm ?? null,
                'has_kkm' => $kkmData->has($jenisUjian->id),
            ];
        });
    }

    /**
     * Check if nilai meets KKM
     */
    public function checkKetuntasan($nilai, $kkm)
    {
        if ($kkm === null) {
            return null; // KKM belum diset
        }
        return $nilai >= $kkm ? 'tuntas' : 'remidi';
    }

    /**
     * Get statistik KKM untuk guru
     */
    public function getKkmStatistik($guruId, $tahunAkademikId)
    {
        $guruKelasList = GuruKelas::with('kkmMapel')
            ->whereHas('guruMapel', function ($q) use ($guruId) {
                $q->where('guru_id', $guruId);
            })
            ->where('tahun_akademik_id', $tahunAkademikId)
            ->where('aktif', true)
            ->get();

        $totalKelas = $guruKelasList->count();
        $totalKkmSet = $guruKelasList->sum(function ($guruKelas) {
            return $guruKelas->kkmMapel->count();
        });

        return [
            'total_kelas' => $totalKelas,
            'total_kkm_set' => $totalKkmSet,
            'guru_kelas_list' => $guruKelasList,
        ];
    }
}
