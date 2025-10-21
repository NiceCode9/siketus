<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'nisn',
        'nama',
        'status',
        'current_class_id',
    ];

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'siswa_id');
    }

    public function akun()
    {
        return $this->hasOne(User::class, 'siswa_id');
    }

    public function currentClass()
    {
        return $this->belongsTo(Kelas::class, 'current_class_id');
    }

    public function penilaianKedisiplinan()
    {
        return $this->hasMany(PenilaianKedisiplinan::class, 'siswa_id');
    }

    public function penilaianKeagamaan()
    {
        return $this->hasMany(PenilaianKeagamaan::class, 'siswa_id');
    }

    public function penilaianMapel()
    {
        return $this->hasMany(PenilaianMapel::class, 'siswa_id');
    }
}
