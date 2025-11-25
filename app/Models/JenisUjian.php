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
        'semester',
        'is_syarat_ujian', // TAMBAHAN BARU
    ];

    protected $casts = [
        'is_syarat_ujian' => 'boolean',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    public function penilaianMapel()
    {
        return $this->hasMany(PenilaianMapel::class, 'jenis_ujian_id');
    }

    public function kkm()
    {
        return $this->hasOne(KkmMapel::class, 'jenis_ujian_id');
    }

    public function scopeSyaratUjian($query)
    {
        return $query->where('is_syarat_ujian', true);
    }
}
