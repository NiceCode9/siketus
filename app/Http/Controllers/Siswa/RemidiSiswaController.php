<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Services\RemidiService;
use App\Services\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemidiSiswaController extends Controller
{
    protected $remidiService;
    protected $notifikasiService;

    public function __construct(RemidiService $remidiService, NotifikasiService $notifikasiService)
    {
        $this->remidiService = $remidiService;
        $this->notifikasiService = $notifikasiService;
    }

    /**
     * Index - Daftar remidi siswa
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $siswaId = $user->siswa->id;

        $statusFilter = $request->status ?? 'all';

        // Get remidi list
        $remidiList = $this->remidiService->getRemidiBySiswa(
            $siswaId,
            $statusFilter === 'all' ? null : $statusFilter
        );

        // Get statistik
        $statistik = $this->remidiService->getRemidiStatistikSiswa($siswaId);

        // Group by status
        $remidiPending = $remidiList->where('status_remidi', 'pending');
        $remidiSelesai = $remidiList->where('status_remidi', 'selesai');
        $remidiBatal = $remidiList->where('status_remidi', 'batal');

        return view('siswa.remidi.index', compact(
            'remidiList',
            'statistik',
            'statusFilter',
            'remidiPending',
            'remidiSelesai',
            'remidiBatal'
        ));
    }

    /**
     * Show - Detail remidi
     */
    public function show($id)
    {
        $user = Auth::user();
        $remidi = $this->remidiService->getRemidiBySiswa($user->siswa->id)
            ->where('id', $id)
            ->first();

        if (!$remidi) {
            abort(404, 'Data remidi tidak ditemukan');
        }

        return view('siswa.remidi.show', compact('remidi'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $user = Auth::user();

            // Verify ownership
            $remidi = $this->remidiService->getRemidiBySiswa($user->siswa->id)
                ->where('id', $id)
                ->first();

            if (!$remidi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $this->notifikasiService->markRemidiAsRead($id);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil ditandai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $user = Auth::user();
            $this->notifikasiService->markAllRemidiAsRead($user->siswa->id);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil ditandai dibaca'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification count (for AJAX)
     */
    public function getNotificationCount()
    {
        $user = Auth::user();
        $badge = $this->notifikasiService->getNotificationBadge($user->siswa->id);

        return response()->json([
            'success' => true,
            'data' => $badge
        ]);
    }
}
