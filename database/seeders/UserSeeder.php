<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Akun awal. Role hanya bisa ditetapkan lewat seeder ini atau
     * lewat halaman Kelola Pengguna (khusus admin), bukan lewat form register.
     */
    public function run(): void
    {
        $akun = [
            ['Admin Utama',  'admin@test.com',   'admin'],
            ['Petugas Satu', 'petugas@test.com', 'petugas'],
            ['Siswa Satu',   'siswa@test.com',   'siswa'],
            ['Siswa Dua',    'siswa2@test.com',  'siswa'],
        ];

        // Soal 5c: password admin diambil dari .env (SEED_ADMIN_PASSWORD), bukan 'password'.
        // Jika belum diisi, dibuat acak sehingga password bawaan tidak pernah berlaku.
        $passwordAdmin = env('SEED_ADMIN_PASSWORD') ?: Str::random(24);

        foreach ($akun as [$nama, $email, $role]) {
            $password = $role === 'admin' ? $passwordAdmin : 'PasswordBaru!250441100038';

            $user = User::updateOrCreate(
                ['email' => $email],
                ['name' => $nama, 'password' => Hash::make($password)],
            );
            $user->syncRoles([$role]);
        }
    }
}