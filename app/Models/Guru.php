<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama',
        'biografi',
        'bidang_keahlian',
    ];

    public function akun()
    {
        return $this->hasOne(User::class, 'guru_id');
    }
}
