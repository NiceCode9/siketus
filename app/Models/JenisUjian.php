<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisUjian extends Model
{
    protected $table = 'jenis_ujian';

    protected $fillable = [
        'tahun_akademik_id',
        'nama_jenis_ujian',
        'deskripsi',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function penilaianMapel()
    {
        return $this->hasMany(PenilaianMapel::class, 'jenis_ujian_id');
    }
}
