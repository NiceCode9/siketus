<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model Penilaian Kedisiplinan
class PenilaianKedisiplinan extends Model
{
    protected $table = 'penilaian_kedisiplinan';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'tahun_akademik_id',
        'kelas_id',
        'semester',
        'kedisiplinan_id',
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

    public function kedisiplinan()
    {
        return $this->belongsTo(Kedisiplinan::class, 'kedisiplinan_id');
    }
}
