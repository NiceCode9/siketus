<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruKelas extends Model
{
    protected $table = 'guru_kelas';

    protected $fillable = [
        'guru_mapel_id',
        'kelas_id',
        'tahun_akademik_id',
        'aktif',
        'keterangan',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function guruMapel()
    {
        return $this->belongsTo(GuruMapel::class, 'guru_mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_kelas_id');
    }

    /**
     * Scope untuk filter aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope untuk filter berdasarkan tahun akademik
     */
    public function scopeByTahunAkademik($query, $tahunAkademikId)
    {
        return $query->where('tahun_akademik_id', $tahunAkademikId);
    }

    public function penilaianMapel()
    {
        return $this->hasMany(PenilaianMapel::class, 'guru_kelas_id');
    }
}
