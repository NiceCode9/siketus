<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KkmMapel extends Model
{
    protected $table = 'kkm_mapel';

    protected $fillable = [
        'guru_kelas_id',
        'jenis_ujian_id',
        'tahun_akademik_id',
        'kkm',
        'keterangan',
    ];

    protected $casts = [
        'kkm' => 'decimal:2',
    ];

    /**
     * Relationship: KKM belongs to GuruKelas
     */
    public function guruKelas()
    {
        return $this->belongsTo(GuruKelas::class, 'guru_kelas_id');
    }

    /**
     * Relationship: KKM belongs to JenisUjian
     */
    public function jenisUjian()
    {
        return $this->belongsTo(JenisUjian::class, 'jenis_ujian_id');
    }

    /**
     * Relationship: KKM belongs to TahunAkademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    /**
     * Scope: Get KKM by guru kelas and jenis ujian
     */
    public function scopeByGuruKelasAndJenisUjian($query, $guruKelasId, $jenisUjianId, $tahunAkademikId)
    {
        return $query->where('guru_kelas_id', $guruKelasId)
            ->where('jenis_ujian_id', $jenisUjianId)
            ->where('tahun_akademik_id', $tahunAkademikId);
    }

    /**
     * Scope: Get all KKM by guru kelas
     */
    public function scopeByGuruKelas($query, $guruKelasId, $tahunAkademikId)
    {
        return $query->where('guru_kelas_id', $guruKelasId)
            ->where('tahun_akademik_id', $tahunAkademikId);
    }
}
