<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class KegiatanKeagamaan extends Model
{
    protected $table = 'kegiatan_keagamaan';

    protected $fillable = [
        'nama_kegiatan',
        'tahun_akademik_id',
        'tingkat_kelas',
        'semester',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    public function penilaianKeagamaan()
    {
        return $this->hasMany(PenilaianKeagamaan::class, 'kegiatan_keagamaan_id');
    }

    // protected function semester(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn(string $value) => ucfirst($value),
    //         set: fn(string $value) => strtolower($value),

    //     );
    // }
}
