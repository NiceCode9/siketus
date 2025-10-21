<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianKeagamaan extends Model
{
    protected $table = 'penilaian_keagamaan';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'tahun_akademik_id',
        'kelas_id',
        'kegiatan_keagamaan_id',
        'semester',
        'nilai',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function kegiatanKeagamaan()
    {
        return $this->belongsTo(KegiatanKeagamaan::class, 'kegiatan_keagamaan_id');
    }
}
