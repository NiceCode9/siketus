<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';

    protected $fillable = [
        'nama_tahun_akademik',
        'tanggal_mulai_ganjil',
        'tanggal_selesai_ganjil',
        'tanggal_mulai_genap',
        'tanggal_selesai_genap',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'tanggal_mulai_ganjil' => 'date',
        'tanggal_selesai_ganjil' => 'date',
        'tanggal_mulai_genap' => 'date',
        'tanggal_selesai_genap' => 'date',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================

    public function kegiatanKeagamaan()
    {
        return $this->hasMany(KegiatanKeagamaan::class, 'tahun_akademik_id');
    }

    public function jenisUjian()
    {
        return $this->hasMany(JenisUjian::class, 'tahun_akademik_id');
    }

    public function guruKelas()
    {
        return $this->hasMany(GuruKelas::class, 'tahun_akademik_id');
    }

    public function kalenderAkademik()
    {
        return $this->hasMany(KalenderAkademik::class, 'tahun_akademik_id');
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeAktif(Builder $query): void
    {
        $query->where('status_aktif', true);
    }

    // ============================================
    // SEMESTER AUTO-DETECTION
    // ============================================

    /**
     * Get current semester based on today's date
     * Returns: 'ganjil', 'genap', or null (jika di luar periode)
     */
    public function getCurrentSemester(): ?string
    {
        $today = Carbon::today();

        // Check semester ganjil
        if ($this->tanggal_mulai_ganjil && $this->tanggal_selesai_ganjil) {
            if ($today->between($this->tanggal_mulai_ganjil, $this->tanggal_selesai_ganjil)) {
                return 'ganjil';
            }
        }

        // Check semester genap
        if ($this->tanggal_mulai_genap && $this->tanggal_selesai_genap) {
            if ($today->between($this->tanggal_mulai_genap, $this->tanggal_selesai_genap)) {
                return 'genap';
            }
        }

        // Jika tidak dalam rentang semester manapun, cek mana yang lebih dekat
        // atau return semester terakhir
        if ($this->tanggal_selesai_genap && $today->gt($this->tanggal_selesai_genap)) {
            return 'genap'; // Tahun akademik sudah selesai, return semester terakhir
        }

        if ($this->tanggal_mulai_ganjil && $today->lt($this->tanggal_mulai_ganjil)) {
            return 'ganjil'; // Tahun akademik belum mulai, return semester pertama
        }

        // Default: coba tentukan berdasarkan bulan jika tanggal tidak lengkap
        $month = $today->month;
        return ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';
    }

    /**
     * Attribute: semester (auto-detected)
     */
    public function getSemesterAttribute(): string
    {
        return $this->getCurrentSemester() ?? 'ganjil';
    }

    /**
     * Get semester label (Ganjil/Genap)
     */
    public function getSemesterLabelAttribute(): string
    {
        return ucfirst($this->semester);
    }

    /**
     * Get full name with current semester
     */
    public function getNamaLengkapAttribute(): string
    {
        return $this->nama_tahun_akademik . ' - Semester ' . $this->semester_label;
    }

    /**
     * Check if current semester is ganjil
     */
    public function isGanjil(): bool
    {
        return $this->semester === 'ganjil';
    }

    /**
     * Check if current semester is genap
     */
    public function isGenap(): bool
    {
        return $this->semester === 'genap';
    }

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Get tanggal mulai untuk semester tertentu
     */
    public function getTanggalMulai(?string $semester = null): ?Carbon
    {
        $semester = $semester ?? $this->semester;
        return $semester === 'ganjil' ? $this->tanggal_mulai_ganjil : $this->tanggal_mulai_genap;
    }

    /**
     * Get tanggal selesai untuk semester tertentu
     */
    public function getTanggalSelesai(?string $semester = null): ?Carbon
    {
        $semester = $semester ?? $this->semester;
        return $semester === 'ganjil' ? $this->tanggal_selesai_ganjil : $this->tanggal_selesai_genap;
    }

    /**
     * Get info semester lengkap
     */
    public function getSemesterInfo(): array
    {
        return [
            'semester' => $this->semester,
            'label' => $this->semester_label,
            'tanggal_mulai' => $this->getTanggalMulai(),
            'tanggal_selesai' => $this->getTanggalSelesai(),
            'is_ganjil' => $this->isGanjil(),
            'is_genap' => $this->isGenap(),
        ];
    }

    /**
     * Check apakah tanggal tertentu masuk dalam semester ganjil
     */
    public function isInSemesterGanjil(Carbon $date): bool
    {
        if (!$this->tanggal_mulai_ganjil || !$this->tanggal_selesai_ganjil) {
            return false;
        }
        return $date->between($this->tanggal_mulai_ganjil, $this->tanggal_selesai_ganjil);
    }

    /**
     * Check apakah tanggal tertentu masuk dalam semester genap
     */
    public function isInSemesterGenap(Carbon $date): bool
    {
        if (!$this->tanggal_mulai_genap || !$this->tanggal_selesai_genap) {
            return false;
        }
        return $date->between($this->tanggal_mulai_genap, $this->tanggal_selesai_genap);
    }

    /**
     * Get semester untuk tanggal tertentu
     */
    public function getSemesterForDate(Carbon $date): ?string
    {
        if ($this->isInSemesterGanjil($date)) {
            return 'ganjil';
        }
        if ($this->isInSemesterGenap($date)) {
            return 'genap';
        }
        return null;
    }
}
