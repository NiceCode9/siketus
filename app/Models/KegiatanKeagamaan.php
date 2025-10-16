<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanKeagamaan extends Model
{
    protected $table = 'kegiatan_keagamaan';

    protected $fillable = [
        'nama_kegiatan',
    ];
}
