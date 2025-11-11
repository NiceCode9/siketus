<?php

namespace App\Services;

use App\Models\RemidiSiswa;
use App\Models\Siswa;

class NotifikasiService
{
    protected $remidiService;

    public function __construct(RemidiService $remidiService)
    {
        $this->remidiService = $remidiService;
    }

    /**
     * Get all unread remidi notifications for siswa
     */
    public function getRemidiNotifications($siswaId)
    {
        return $this->remidiService->getUnreadRemidiNotifications($siswaId);
    }

    /**
     * Count unread remidi notifications
     */
    public function countUnreadRemidi($siswaId)
    {
        return RemidiSiswa::where('siswa_id', $siswaId)
            ->unreadNotification()
            ->count();
    }

    /**
     * Mark single remidi as read
     */
    public function markRemidiAsRead($remidiId)
    {
        return $this->remidiService->markAsRead($remidiId);
    }

    /**
     * Mark all remidi as read for siswa
     */
    public function markAllRemidiAsRead($siswaId)
    {
        return RemidiSiswa::where('siswa_id', $siswaId)
            ->where('is_notified', true)
            ->update(['is_notified' => false]);
    }

    /**
     * Get notification summary for siswa
     */
    public function getNotificationSummary($siswaId)
    {
        $remidiPending = $this->remidiService->getUnreadRemidiNotifications($siswaId);

        return [
            'remidi_count' => $remidiPending->count(),
            'remidi_list' => $remidiPending,
            'has_notifications' => $remidiPending->count() > 0,
        ];
    }

    /**
     * Get notification badge data
     */
    public function getNotificationBadge($siswaId)
    {
        $count = $this->countUnreadRemidi($siswaId);

        return [
            'count' => $count,
            'has_badge' => $count > 0,
            'badge_text' => $count > 99 ? '99+' : $count,
        ];
    }

    /**
     * Create notification message for remidi
     */
    public function createRemidiNotificationMessage($remidi)
    {
        $mapel = $remidi->guruKelas->guruMapel->mapel->nama_mapel;
        $jenisUjian = $remidi->jenisUjian->nama_jenis_ujian;
        $nilai = $remidi->nilai_asli;
        $kkm = $remidi->kkm;

        return "Anda perlu remidi untuk {$mapel} - {$jenisUjian}. Nilai: {$nilai}, KKM: {$kkm}";
    }

    /**
     * Get grouped notifications by mata pelajaran
     */
    public function getGroupedNotifications($siswaId)
    {
        $notifications = $this->getRemidiNotifications($siswaId);

        return $notifications->groupBy(function ($remidi) {
            return $remidi->guruKelas->guruMapel->mapel->nama_mapel;
        });
    }
}
