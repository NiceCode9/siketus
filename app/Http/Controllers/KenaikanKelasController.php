<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KenaikanKelasController extends Controller
{
    /**
     * Menampilkan halaman kenaikan kelas
     */
    public function index(Request $request)
    {
        // Ambil tahun akademik aktif
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', 'aktif')->first();

        // Ambil semua tahun akademik untuk dropdown
        $tahunAkademikList = TahunAkademik::orderBy('nama_tahun_akademik', 'desc')->get();

        // Ambil kelas yang dipilih dari request
        $kelasId = $request->get('kelas_id');

        // Ambil semua kelas untuk dropdown
        $kelasList = Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // Inisialisasi variabel siswa
        $siswaList = collect();
        $kelasSelected = null;

        if ($kelasId) {
            $kelasSelected = Kelas::with('jurusan')->find($kelasId);

            if ($kelasSelected && $tahunAkademikAktif) {
                // Ambil siswa yang ada di kelas tersebut pada tahun akademik aktif
                $siswaList = Siswa::whereHas('riwayatKelas', function ($query) use ($kelasId, $tahunAkademikAktif) {
                    $query->where('kelas_id', $kelasId)
                        ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                        ->where('status', 'aktif');
                })
                    ->with(['currentClass.jurusan', 'riwayatKelas' => function ($query) use ($tahunAkademikAktif) {
                        $query->where('tahun_akademik_id', $tahunAkademikAktif->id);
                    }])
                    ->orderBy('nama')
                    ->get();
            }
        }

        return view('master.kenaikan_kelas.index', compact(
            'tahunAkademikAktif',
            'tahunAkademikList',
            'kelasList',
            'siswaList',
            'kelasSelected'
        ));
    }

    /**
     * Menampilkan form untuk memilih kelas tujuan kenaikan
     */
    public function create(Request $request)
    {
        $kelasAsalId = $request->get('kelas_asal_id');
        $tahunAkademikAktif = TahunAkademik::where('status_aktif', 'aktif')->first();

        if (!$tahunAkademikAktif) {
            return redirect()->back()->with('error', 'Tidak ada tahun akademik aktif.');
        }

        $kelasAsal = Kelas::with('jurusan')->find($kelasAsalId);

        if (!$kelasAsal) {
            return redirect()->back()->with('error', 'Kelas tidak ditemukan.');
        }

        // Ambil siswa dari kelas asal
        $siswaList = Siswa::whereHas('riwayatKelas', function ($query) use ($kelasAsalId, $tahunAkademikAktif) {
            $query->where('kelas_id', $kelasAsalId)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->where('status', 'aktif');
        })
            ->with('currentClass.jurusan')
            ->orderBy('nama')
            ->get();

        // Ambil kelas tujuan (tingkat lebih tinggi atau kelas lain untuk tinggal kelas)
        $kelasTujuanList = Kelas::with('jurusan')
            ->where(function ($query) use ($kelasAsal) {
                // Kelas dengan tingkat lebih tinggi (untuk naik kelas)
                $query->where('tingkat', '>', $kelasAsal->tingkat)
                    ->where('jurusan_id', $kelasAsal->jurusan_id);
            })
            ->orWhere(function ($query) use ($kelasAsal) {
                // Kelas dengan tingkat sama (untuk tinggal kelas)
                $query->where('tingkat', $kelasAsal->tingkat)
                    ->where('jurusan_id', $kelasAsal->jurusan_id)
                    ->where('id', '!=', $kelasAsal->id);
            })
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        return view('master.kenaikan_kelas.create', compact(
            'kelasAsal',
            'siswaList',
            'kelasTujuanList',
            'tahunAkademikAktif'
        ));
    }

    /**
     * Proses kenaikan kelas
     */
    public function store(Request $request)
    {
        $request->validate([
            'kelas_asal_id' => 'required|exists:kelas,id',
            'siswa' => 'required|array',
            'siswa.*.siswa_id' => 'required|exists:siswa,id',
            'siswa.*.kelas_tujuan_id' => 'required|exists:kelas,id',
            'siswa.*.status' => 'required|in:naik,tinggal,lulus,pindah,dropout',
            'siswa.*.keterangan' => 'nullable|string',
            'tahun_akademik_baru_id' => 'required|exists:tahun_akademik,id',
        ]);

        DB::beginTransaction();

        try {
            $tahunAkademikAktif = TahunAkademik::aktif()->first();
            $tahunAkademikBaru = TahunAkademik::find($request->tahun_akademik_baru_id);

            foreach ($request->siswa as $siswaData) {
                $siswa = Siswa::find($siswaData['siswa_id']);

                if (!$siswa) {
                    continue;
                }

                // Update status riwayat kelas lama
                RiwayatKelas::where('siswa_id', $siswa->id)
                    ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                    ->where('status', 'aktif')
                    ->update(['status' => 'selesai']);

                // Tentukan status baru berdasarkan pilihan
                $statusBaru = 'aktif';
                if (in_array($siswaData['status'], ['lulus', 'pindah', 'dropout'])) {
                    $statusBaru = $siswaData['status'];
                }

                // Buat riwayat kelas baru untuk tahun akademik baru
                RiwayatKelas::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $siswaData['kelas_tujuan_id'],
                    'tahun_akademik_id' => $tahunAkademikBaru->id,
                    'status' => $statusBaru,
                    'keterangan' => $siswaData['keterangan'] ?? null,
                ]);

                // Update current_class_id di tabel siswa
                $siswa->update([
                    'current_class_id' => $siswaData['kelas_tujuan_id'],
                    'status' => $statusBaru == 'aktif' ? 'aktif' : $statusBaru,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.kenaikan-kelas.index')
                ->with('success', 'Kenaikan kelas berhasil diproses untuk ' . count($request->siswa) . ' siswa.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error kenaikan kelas: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses kenaikan kelas: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Kenaikan kelas massal untuk seluruh kelas
     */
    public function naikkanMassal(Request $request)
    {
        $request->validate([
            'tahun_akademik_baru_id' => 'required|exists:tahun_akademik,id',
        ]);

        DB::beginTransaction();

        try {
            $tahunAkademikAktif = TahunAkademik::aktif()->first();
            $tahunAkademikBaru = TahunAkademik::find($request->tahun_akademik_baru_id);

            // Ambil semua siswa aktif
            $siswaAktif = Siswa::where('status', 'aktif')
                ->whereHas('riwayatKelas', function ($query) use ($tahunAkademikAktif) {
                    $query->where('tahun_akademik_id', $tahunAkademikAktif->id)
                        ->where('status', 'aktif');
                })
                ->with(['currentClass'])
                ->get();

            $berhasil = 0;

            foreach ($siswaAktif as $siswa) {
                if (!$siswa->currentClass) {
                    continue;
                }

                // Cari kelas tujuan (tingkat +1, jurusan sama)
                $kelasTujuan = Kelas::where('jurusan_id', $siswa->currentClass->jurusan_id)
                    ->where('tingkat', $siswa->currentClass->tingkat + 1)
                    ->first();

                // Jika tidak ada kelas tujuan, siswa dianggap lulus
                if (!$kelasTujuan) {
                    // Update status riwayat lama
                    RiwayatKelas::where('siswa_id', $siswa->id)
                        ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                        ->where('status', 'aktif')
                        ->update(['status' => 'selesai']);

                    // Buat riwayat baru dengan status lulus
                    RiwayatKelas::create([
                        'siswa_id' => $siswa->id,
                        'kelas_id' => $siswa->currentClass->id,
                        'tahun_akademik_id' => $tahunAkademikBaru->id,
                        'status' => 'lulus',
                        'keterangan' => 'Lulus - Kenaikan Massal',
                    ]);

                    $siswa->update(['status' => 'lulus']);
                    $berhasil++;
                    continue;
                }

                // Update status riwayat lama
                RiwayatKelas::where('siswa_id', $siswa->id)
                    ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                    ->where('status', 'aktif')
                    ->update(['status' => 'selesai']);

                // Buat riwayat baru
                RiwayatKelas::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelasTujuan->id,
                    'tahun_akademik_id' => $tahunAkademikBaru->id,
                    'status' => 'aktif',
                    'keterangan' => 'Naik Kelas - Kenaikan Massal',
                ]);

                // Update current_class_id
                $siswa->update(['current_class_id' => $kelasTujuan->id]);

                $berhasil++;
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Kenaikan kelas massal berhasil diproses untuk {$berhasil} siswa.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error kenaikan kelas massal: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memproses kenaikan kelas massal: ' . $e->getMessage());
        }
    }
}
