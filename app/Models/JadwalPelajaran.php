<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'guru_kelas_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    public function guruKelas()
    {
        return $this->belongsTo(GuruKelas::class, 'guru_kelas_id');
    }

    public function pertemuan()
    {
        return $this->hasMany(Pertemuan::class, 'jadwal_pelajaran_id');
    }

    /**
     * Scope untuk filter berdasarkan hari
     */
    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope untuk filter berdasarkan tahun akademik melalui guru_kelas
     */
    public function scopeByTahunAkademik($query, $tahunAkademikId)
    {
        return $query->whereHas('guruKelas', function ($q) use ($tahunAkademikId) {
            $q->where('tahun_akademik_id', $tahunAkademikId);
        });
    }

    /**
     * Get info lengkap jadwal (dengan relasi)
     */
    public function getInfoLengkapAttribute()
    {
        $guruKelas = $this->guruKelas;
        $guruMapel = $guruKelas->guruMapel;

        return [
            'guru' => $guruMapel->guru->nama ?? '-',
            'mapel' => $guruMapel->mapel->nama_mapel ?? '-',
            'kelas' => $guruKelas->kelas->nama_kelas ?? '-',
            'hari' => $this->hari,
            'waktu' => $this->jam_mulai->format('H:i') . ' - ' . $this->jam_selesai->format('H:i'),
            'ruangan' => $this->ruangan ?? '-',
        ];
    }
}
