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

    public function getNamaLengkapAttribute()
    {
        return $this->tingkat . '-' . $this->jurusan->kode_jurusan . '-' .  $this->nama_kelas;
    }

    public function guruKelas()
    {
        return $this->hasMany(GuruKelas::class, 'kelas_id');
    }

    public function penilaianKedisiplinan()
    {
        return $this->hasMany(PenilaianKedisiplinan::class, 'kelas_id');
    }

    public function penilaianKeagamaan()
    {
        return $this->hasMany(PenilaianKeagamaan::class, 'kelas_id');
    }

    public function penilaianMapel()
    {
        return $this->hasMany(PenilaianMapel::class, 'kelas_id');
    }

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'kelas_id');
    }
}
