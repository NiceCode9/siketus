<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\RiwayatKelas;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Siswa::with('currentClass');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<button type="button" class="btn btn-info btn-sm view-btn" data-id="' . $row->id . '" title="View">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="' . $row->id . '" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '" data-nama="' . $row->nama . '" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>';
                    return $btn;
                })
                ->addColumn('kelas', function ($row) {
                    return $row->currentClass ? $row->currentClass->nama_lengkap : '-';
                })
                ->addColumn('status_badge', function ($row) {
                    $badge = $row->status == 'aktif' ? 'success' : 'secondary';
                    return '<span class="badge badge-' . $badge . '">' . ucfirst($row->status) . '</span>';
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        $kelas = Kelas::all();
        return view('master.siswa.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|unique:siswa,nisn',
            'nama' => 'required',
            'status' => 'required|in:aktif,nonaktif',
            'current_class_id' => 'nullable|exists:kelas,id',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create siswa
            $siswa = Siswa::create([
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'status' => $request->status,
                'current_class_id' => $request->current_class_id,
            ]);

            // Create user account
            $user = User::create([
                'name' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'siswa_id' => $siswa->id,
            ]);

            $tahunakademik = TahunAkademik::aktif()->first();
            Log::info('Tahun Akademik Aktif: ' . $tahunakademik->id);
            // Create riwayat kelas
            RiwayatKelas::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $request->current_class_id,
                'tahun_akademik_id' => $tahunakademik->id,
                'keterangan' => 'data pertama kali masuk',
            ]);

            // Assign role to user
            $siswaRole = Role::where('name', 'siswa')->first();
            if ($siswaRole) {
                $user->assignRole($siswaRole);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data siswa dan akun berhasil dibuat!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        $siswa->load('akun', 'currentClass.jurusan');
        return response()->json([
            'siswa' => $siswa,
            'akun' => $siswa->akun
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        $siswa->load('akun');
        return response()->json([
            'siswa' => $siswa,
            'akun' => $siswa->akun
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|unique:siswa,nisn,' . $siswa->id,
            'nama' => 'required',
            'status' => 'required|in:aktif,nonaktif',
            'current_class_id' => 'nullable|exists:kelas,id',
            'email' => 'required|email|unique:users,email,' . ($siswa->akun ? $siswa->akun->id : 'NULL'),
            'username' => 'required|unique:users,username,' . ($siswa->akun ? $siswa->akun->id : 'NULL'),
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update siswa
            $siswa->update([
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'status' => $request->status,
                'current_class_id' => $request->current_class_id,
            ]);

            // Update or create user account
            $userData = [
                'name' => $request->nama,
                'username' => $request->username,
                'email' => $request->email,
            ];

            if ($request->password) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($siswa->akun) {
                $siswa->akun->update($userData);
            } else {
                $user = User::create(array_merge($userData, [
                    'password' => Hash::make($request->password ?? 'password'),
                    'siswa_id' => $siswa->id,
                ]));

                // Assign role to user
                $siswaRole = Role::where('name', 'siswa')->first();
                if ($siswaRole) {
                    $user->assignRole($siswaRole);
                }
            }

            // Update riwayat kelas
            $tahunakademik = TahunAkademik::aktif()->first();
            RiwayatKelas::updateOrCreate(
                ['siswa_id' => $siswa->id, 'tahun_akademik_id' => $tahunakademik->id],
                ['kelas_id' => $request->current_class_id]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data siswa dan akun berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        try {
            DB::beginTransaction();

            // Delete user account if exists
            if ($siswa->akun) {
                $siswa->akun->delete();
            }

            // Delete siswa
            $siswa->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data siswa dan akun berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kelas data for dropdown
     */
    public function getKelas()
    {
        $kelas = Kelas::all();
        return response()->json($kelas);
    }
}
