<?php

namespace App\Helpers;

class NilaiHelper
{
    /**
     * Konversi nilai angka ke huruf
     */
    public static function nilaiToHuruf($nilai)
    {
        if ($nilai >= 90) {
            return 'A';
        } elseif ($nilai >= 80) {
            return 'B';
        } elseif ($nilai >= 70) {
            return 'C';
        } elseif ($nilai >= 60) {
            return 'D';
        } else {
            return 'E';
        }
    }

    /**
     * Konversi nilai angka ke predikat
     */
    public static function nilaiToPredikat($nilai)
    {
        if ($nilai >= 90) {
            return 'Sangat Baik';
        } elseif ($nilai >= 80) {
            return 'Baik';
        } elseif ($nilai >= 70) {
            return 'Cukup';
        } elseif ($nilai >= 60) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }

    /**
     * Hitung rata-rata nilai
     */
    public static function hitungRataRata($nilaiArray)
    {
        if (empty($nilaiArray)) {
            return 0;
        }

        $total = array_sum($nilaiArray);
        $jumlah = count($nilaiArray);

        return round($total / $jumlah, 2);
    }

    /**
     * Format nilai dengan 2 desimal
     */
    public static function formatNilai($nilai)
    {
        return number_format($nilai, 2, ',', '.');
    }

    /**
     * Cek apakah siswa lulus (minimal 60)
     */
    public static function isLulus($nilai, $batasLulus = 60)
    {
        return $nilai >= $batasLulus;
    }

    /**
     * Get warna badge berdasarkan nilai
     */
    public static function getBadgeColor($nilai)
    {
        if ($nilai >= 90) {
            return 'success'; // Hijau
        } elseif ($nilai >= 80) {
            return 'primary'; // Biru
        } elseif ($nilai >= 70) {
            return 'info'; // Cyan
        } elseif ($nilai >= 60) {
            return 'warning'; // Kuning
        } else {
            return 'danger'; // Merah
        }
    }
}

// Cara penggunaan di Blade:
// {{ App\Helpers\NilaiHelper::nilaiToHuruf(85) }} // Output: B
// {{ App\Helpers\NilaiHelper::nilaiToPredikat(85) }} // Output: Baik
// <span class="badge badge-{{ App\Helpers\NilaiHelper::getBadgeColor($nilai) }}">
//     {{ App\Helpers\NilaiHelper::formatNilai($nilai) }}
// </span>
