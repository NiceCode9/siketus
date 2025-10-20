<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelajaran;
use App\Models\TahunAkademik;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class JadwalSiswaController extends Controller
{
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa || !$siswa->current_class_id) {
            return redirect()->back()->with('error', 'Data siswa atau kelas tidak ditemukan!');
        }

        $kelas = $siswa->currentClass;
        $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();

        if (!$tahunAkademik) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif!');
        }

        $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');
        $hariMapping = [
            'Minggu' => 'Minggu',
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
            'Sabtu' => 'Sabtu'
        ];
        $hariIni = $hariMapping[$hariIni] ?? 'Senin';

        // Ambil semua jadwal kelas siswa
        $jadwalQuery = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'guruKelas.tahunAkademik'
        ])
            ->whereHas('guruKelas', function ($q) use ($kelas, $tahunAkademik) {
                $q->where('kelas_id', $kelas->id)
                    ->where('tahun_akademik_id', $tahunAkademik->id);
            });

        // Jadwal hari ini
        $jadwalHariIni = (clone $jadwalQuery)
            ->where('hari', $hariIni)
            ->orderBy('jam_mulai')
            ->get();

        // Jadwal per hari untuk minggu ini
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = (clone $jadwalQuery)
                ->where('hari', $hari)
                ->orderBy('jam_mulai')
                ->get();
        }

        return view('siswa.jadwal.index', compact(
            'jadwalHariIni',
            'jadwalPerHari',
            'kelas',
            'tahunAkademik',
            'hariIni'
        ));
    }

    public function show($id)
    {
        $siswa = Auth::user()->siswa;

        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas',
            'guruKelas.tahunAkademik',
            'pertemuan'
        ])->findOrFail($id);

        // Pastikan jadwal adalah milik kelas siswa
        if ($jadwal->guruKelas->kelas_id != $siswa->current_class_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id' => $jadwal->id,
            'mapel' => $jadwal->guruKelas->guruMapel->mapel->nama_mapel,
            'guru' => $jadwal->guruKelas->guruMapel->guru->nama,
            'kelas' => $jadwal->guruKelas->kelas->nama_lengkap,
            'hari' => $jadwal->hari,
            'waktu' => Carbon::parse($jadwal->jam_mulai)->format('H:i') . ' - ' .
                Carbon::parse($jadwal->jam_selesai)->format('H:i'),
            'ruangan' => $jadwal->ruangan ?? null,
            'tahun_akademik' => $jadwal->guruKelas->tahunAkademik->nama_tahun_akademik . ' - ' .
                $jadwal->guruKelas->tahunAkademik->semester,
            'total_pertemuan' => $jadwal->pertemuan->count(),
            'is_active' => $jadwal->guruKelas->aktif
        ]);
    }

    public function setReminder(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_pelajaran,id',
            'reminder_time' => 'required|integer|in:5,10,15,30'
        ]);

        $siswa = Auth::user()->siswa;
        $jadwal = JadwalPelajaran::findOrFail($request->jadwal_id);

        // Validasi kepemilikan
        if ($jadwal->guruKelas->kelas_id != $siswa->kelas_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Logic untuk set reminder (bisa simpan ke database atau kirim notifikasi)
        // Contoh: simpan ke tabel reminders
        // Reminder::create([
        //     'siswa_id' => $siswa->id,
        //     'jadwal_pelajaran_id' => $jadwal->id,
        //     'reminder_minutes' => $request->reminder_time,
        //     'is_active' => true
        // ]);

        return response()->json([
            'status' => true,
            'message' => 'Reminder berhasil diaktifkan!'
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $siswa = Auth::user()->siswa;
        $kelas = $siswa->currentClass;
        $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
        $type = $request->get('type', 'weekly'); // daily, weekly, semester

        $jadwalQuery = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas'
        ])
            ->whereHas('guruKelas', function ($q) use ($kelas, $tahunAkademik) {
                $q->where('kelas_id', $kelas->id)
                    ->where('tahun_akademik_id', $tahunAkademik->id);
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai');

        if ($type == 'daily') {
            $hariIni = Carbon::now()->locale('id')->isoFormat('dddd');
            $hariMapping = [
                'Minggu' => 'Minggu',
                'Senin' => 'Senin',
                'Selasa' => 'Selasa',
                'Rabu' => 'Rabu',
                'Kamis' => 'Kamis',
                'Jumat' => 'Jumat',
                'Sabtu' => 'Sabtu'
            ];
            $hariIni = $hariMapping[$hariIni] ?? 'Senin';

            $jadwal = $jadwalQuery->where('hari', $hariIni)->get();
            $title = 'Jadwal Hari Ini - ' . $hariIni;
        } else {
            $jadwal = $jadwalQuery->get();
            $title = $type == 'weekly' ? 'Jadwal Minggu Ini' : 'Jadwal Semester';
        }

        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalPerHari = [];

        foreach ($hariList as $hari) {
            $jadwalPerHari[$hari] = $jadwal->where('hari', $hari);
        }

        $pdf = Pdf::loadView('siswa.jadwal.pdf', compact(
            'jadwalPerHari',
            'siswa',
            'kelas',
            'tahunAkademik',
            'hariList',
            'title',
            'type'
        ))->setPaper('a4', 'portrait');

        $filename = 'jadwal-' . strtolower($kelas->nama_kelas) . '-' . $type . '.pdf';
        return $pdf->stream($filename);
    }

    public function exportCalendar(Request $request)
    {
        $siswa = Auth::user()->siswa;
        $kelas = $siswa->currentClass;
        $tahunAkademik = TahunAkademik::where('status_aktif', true)->first();
        $type = $request->get('type', 'google'); // google, outlook, ical

        $jadwal = JadwalPelajaran::with([
            'guruKelas.guruMapel.guru',
            'guruKelas.guruMapel.mapel',
            'guruKelas.kelas'
        ])
            ->whereHas('guruKelas', function ($q) use ($kelas, $tahunAkademik) {
                $q->where('kelas_id', $kelas->id)
                    ->where('tahun_akademik_id', $tahunAkademik->id);
            })
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        if ($type == 'google') {
            return $this->exportToGoogleCalendar($jadwal);
        } elseif ($type == 'ical') {
            return $this->exportToICal($jadwal, $kelas);
        } else {
            return $this->exportToOutlook($jadwal);
        }
    }

    private function exportToGoogleCalendar($jadwal)
    {
        // Redirect ke Google Calendar dengan pre-filled data
        $baseUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE';

        // Untuk kesederhanaan, redirect ke halaman info
        // Implementasi lengkap memerlukan loop untuk setiap jadwal
        return redirect()->back()->with('info', 'Silakan tambahkan jadwal secara manual ke Google Calendar.');
    }

    private function exportToICal($jadwal, $kelas)
    {
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Sekolah//Jadwal Pelajaran//ID\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "X-WR-CALNAME:Jadwal " . $kelas->nama_lengkap . "\r\n";
        $ical .= "X-WR-TIMEZONE:Asia/Jakarta\r\n";

        $hariToNumber = [
            'Senin' => 'MO',
            'Selasa' => 'TU',
            'Rabu' => 'WE',
            'Kamis' => 'TH',
            'Jumat' => 'FR',
            'Sabtu' => 'SA'
        ];

        foreach ($jadwal as $j) {
            $mapel = $j->guruKelas->guruMapel->mapel->nama_mapel;
            $guru = $j->guruKelas->guruMapel->guru->nama;
            $ruangan = $j->ruangan ?? 'TBA';

            $jamMulai = Carbon::parse($j->jam_mulai)->format('His');
            $jamSelesai = Carbon::parse($j->jam_selesai)->format('His');

            // Cari tanggal mulai minggu depan untuk hari tersebut
            // Konversi nama hari ke bahasa Inggris untuk Carbon
            $hariInggris = [
                'Senin' => 'Monday',
                'Selasa' => 'Tuesday',
                'Rabu' => 'Wednesday',
                'Kamis' => 'Thursday',
                'Jumat' => 'Friday',
                'Sabtu' => 'Saturday',
                'Minggu' => 'Sunday'
            ];
            $nextDay = Carbon::now()->next($hariInggris[$j->hari] ?? 'Monday');
            $dtstart = $nextDay->format('Ymd') . 'T' . $jamMulai;
            $dtend = $nextDay->format('Ymd') . 'T' . $jamSelesai;

            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:" . md5($j->id . time()) . "@sekolah.com\r\n";
            $ical .= "DTSTAMP:" . date('Ymd\THis\Z') . "\r\n";
            $ical .= "DTSTART;TZID=Asia/Jakarta:" . $dtstart . "\r\n";
            $ical .= "DTEND;TZID=Asia/Jakarta:" . $dtend . "\r\n";
            $ical .= "SUMMARY:" . $mapel . "\r\n";
            $ical .= "DESCRIPTION:Guru: " . $guru . "\r\n";
            $ical .= "LOCATION:" . $ruangan . "\r\n";

            // Recurring setiap minggu
            if (isset($hariToNumber[$j->hari])) {
                $ical .= "RRULE:FREQ=WEEKLY;BYDAY=" . $hariToNumber[$j->hari] . "\r\n";
            }

            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="jadwal-' . strtolower($kelas->nama_kelas) . '.ics"');
    }

    private function exportToOutlook($jadwal)
    {
        // Similar to iCal, Outlook also uses .ics format
        return $this->exportToICal($jadwal, Auth::user()->siswa->currentClass);
    }
}
