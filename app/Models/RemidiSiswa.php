<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemidiSiswa extends Model
{
    protected $table = 'remidi_siswa';

    protected $fillable = [
        'siswa_id',
        'penilaian_mapel_id',
        'guru_kelas_id',
        'jenis_ujian_id',
        'tahun_akademik_id',
        'kelas_id',
        'semester',
        'nilai_asli',
        'kkm',
        'nilai_remidi',
        'nilai_akhir',
        'status_remidi',
        'tanggal_remidi',
        'keterangan',
        'is_notified',
    ];

    protected $casts = [
        'nilai_asli' => 'decimal:2',
        'kkm' => 'decimal:2',
        'nilai_remidi' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
        'tanggal_remidi' => 'datetime',
        'is_notified' => 'boolean',
    ];

    /**
     * Relationship: Remidi belongs to Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relationship: Remidi belongs to PenilaianMapel
     */
    public function penilaianMapel()
    {
        return $this->belongsTo(PenilaianMapel::class, 'penilaian_mapel_id');
    }

    /**
     * Relationship: Remidi belongs to GuruKelas
     */
    public function guruKelas()
    {
        return $this->belongsTo(GuruKelas::class, 'guru_kelas_id');
    }

    /**
     * Relationship: Remidi belongs to JenisUjian
     */
    public function jenisUjian()
    {
        return $this->belongsTo(JenisUjian::class, 'jenis_ujian_id');
    }

    /**
     * Relationship: Remidi belongs to TahunAkademik
     */
    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_akademik_id');
    }

    /**
     * Relationship: Remidi belongs to Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Scope: Get remidi pending
     */
    public function scopePending($query)
    {
        return $query->where('status_remidi', 'pending');
    }

    /**
     * Scope: Get remidi selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status_remidi', 'selesai');
    }

    /**
     * Scope: Get remidi by siswa
     */
    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    /**
     * Scope: Get remidi with unread notification
     */
    public function scopeUnreadNotification($query)
    {
        return $query->where('is_notified', true)
            ->where('status_remidi', 'pending');
    }

    /**
     * Scope: Get remidi by guru kelas
     */
    public function scopeByGuruKelas($query, $guruKelasId)
    {
        return $query->where('guru_kelas_id', $guruKelasId);
    }

    /**
     * Calculate nilai akhir (MAX of nilai_asli and nilai_remidi)
     */
    public function calculateNilaiAkhir()
    {
        if ($this->nilai_remidi !== null) {
            return max($this->nilai_asli, $this->nilai_remidi);
        }
        return $this->nilai_asli;
    }

    /**
     * Check if remidi is completed (nilai_akhir >= kkm)
     */
    public function isCompleted()
    {
        return $this->nilai_akhir >= $this->kkm;
    }
}
