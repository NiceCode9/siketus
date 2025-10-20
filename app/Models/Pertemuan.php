<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertemuan extends Model
{
    protected $table = 'pertemuan';

    protected $fillable = [
        'jadwal_pelajaran_id',
        'tanggal',
        'jam_mulai_aktual',
        'jam_selesai_aktual',
        'materi',
        'pertemuan_ke',
        'status',
        'generated_auto',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai_aktual' => 'datetime:H:i',
        'jam_selesai_aktual' => 'datetime:H:i',
        'generated_auto' => 'boolean',
    ];

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'pertemuan_id');
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan range tanggal
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Check apakah pertemuan sudah diabsen semua
     */
    public function isAbsensiComplete()
    {
        $totalSiswa = $this->jadwalPelajaran->guruKelas->kelas->siswa()->count();
        $totalAbsensi = $this->absensi()->count();

        return $totalSiswa === $totalAbsensi;
    }

    /**
     * Get persentase kehadiran
     */
    public function getPersentaseKehadiranAttribute()
    {
        $total = $this->absensi()->count();
        if ($total === 0) return 0;

        $hadir = $this->absensi()->where('status_kehadiran', 'hadir')->count();

        return round(($hadir / $total) * 100, 2);
    }
}
