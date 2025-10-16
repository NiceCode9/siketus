<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    protected $table = 'mapel';

    protected $fillable = [
        'nama_mapel',
        'deskripsi',
        'kode_pelajaran',
    ];

    public function guruMapel()
    {
        return $this->hasMany(GuruMapel::class, 'mapel_id');
    }
}
