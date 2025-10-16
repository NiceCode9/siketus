<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kedisiplinan extends Model
{
    protected $table = 'kedisiplinan';

    protected $fillable = [
        'jenis',
    ];
}
