<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalenderAkademik extends Model
{
    protected $table = 'kalender_akademik';

    protected $fillable = [
        'tahun_akademik_id',
        'tanggal',
        'jenis_libur',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    /**
     * Scope untuk filter berdasarkan tahun akademik
     */
    public function scopeByTahunAkademik($query, $tahunAkademikId)
    {
        return $query->where('tahun_akademik_id', $tahunAkademikId);
    }

    /**
     * Scope untuk filter berdasarkan range tanggal
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Check apakah tanggal tertentu adalah hari libur
     */
    public static function isHariLibur($tanggal, $tahunAkademikId)
    {
        return self::where('tahun_akademik_id', $tahunAkademikId)
            ->where('tanggal', $tanggal)
            ->exists();
    }
}
