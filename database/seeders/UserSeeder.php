<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $admin = User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Guru Users
        $gurus = Guru::all();
        foreach ($gurus as $guru) {
            $username = strtolower(str_replace(' ', '', explode(',', $guru->nama)[0]));
            $user = User::create([
                'name' => $guru->nama,
                'username' => $username,
                'email' => $username . '@sekolah.com',
                'password' => Hash::make('password'),
                'guru_id' => $guru->id,
            ]);
            $user->assignRole('guru');
        }

        // Siswa Users
        $siswas = Siswa::all();
        foreach ($siswas as $siswa) {
            $username = strtolower(str_replace(' ', '', $siswa->nama));
            $user = User::create([
                'name' => $siswa->nama,
                'username' => $username,
                'email' => $siswa->nisn . '@sekolah.com',
                'password' => Hash::make('password'),
                'siswa_id' => $siswa->id,
            ]);
            $user->assignRole('siswa');
        }
    }
}
