<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kedisiplinan extends Model
{
    protected $table = 'kedisiplinan';

    protected $fillable = [
        'jenis',
    ];

    public function penilaianKedisiplinan()
    {
        return $this->hasMany(PenilaianKedisiplinan::class, 'kedisiplinan_id');
    }
}
