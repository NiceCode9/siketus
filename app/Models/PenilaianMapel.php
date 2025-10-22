<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianMapel extends Model
{
    protected $table = 'penilaian_mapel';

    protected $fillable = [
        'siswa_id',
        'guru_kelas_id',
        'jenis_ujian_id',
        'tahun_akademik_id',
        'kelas_id',
        'semester',
        'nilai',
        'nilai_by_siswa',
        'catatan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guruKelas()
    {
        return $this->belongsTo(GuruKelas::class, 'guru_kelas_id');
    }

    public function jenisUjian()
    {
        return $this->belongsTo(JenisUjian::class, 'jenis_ujian_id');
    }

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
