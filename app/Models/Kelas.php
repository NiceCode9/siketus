<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'jurusan_id',
        'tingkat',
        'nama_kelas',
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'current_class_id');
    }

    public function getNamaKelasAttribute()
    {
        return $this->tingkat . '-' . $this->jurusan->nama_jurusan . '-' .  $this->nama_kelas;
    }
}
