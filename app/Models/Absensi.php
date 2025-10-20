<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'pertemuan_id',
        'siswa_id',
        'status_kehadiran',
        'keterangan',
        'waktu_absen',
    ];

    protected $casts = [
        'waktu_absen' => 'datetime',
    ];

    public function pertemuan()
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Scope untuk filter berdasarkan status kehadiran
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_kehadiran', $status);
    }

    /**
     * Scope untuk filter berdasarkan siswa
     */
    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    /**
     * Get badge color berdasarkan status
     */
    public function getBadgeColorAttribute()
    {
        return match ($this->status_kehadiran) {
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Auto set waktu_absen saat create
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($absensi) {
            if (empty($absensi->waktu_absen)) {
                $absensi->waktu_absen = now();
            }
        });
    }
}
