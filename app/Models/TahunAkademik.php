<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';

    protected $fillable = [
        'nama_tahun_akademik',
        'tanggal_mulai',
        'tanggal_selesai',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function kegiatanKeagamaan()
    {
        return $this->hasMany(KegiatanKeagamaan::class, 'tahun_akademik_id');
    }

    public function jenisUjian()
    {
        return $this->hasMany(JenisUjian::class, 'tahun_akademik_id');
    }

    public function guruKelas()
    {
        return $this->hasMany(GuruKelas::class, 'tahun_akademik_id');
    }

    public function kalenderAkademik()
    {
        return $this->hasMany(KalenderAkademik::class, 'tahun_akademik_id');
    }

    public function scopeAktif(Builder $query): void
    {
        $query->where('status_aktif', true);
    }
}
