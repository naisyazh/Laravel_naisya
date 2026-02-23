<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User Admin Utama (Untuk tes login & OTP)
        User::create([
            'name' => 'Abhi Svariyu',
            'email' => 'abhizvariyu@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'otp' => null,
        ]);

        // 2. Tambahkan Email Naisya Zahra
        User::create([
            'name' => 'Naisya Zahra',
            'email' => 'naisyaazaraa@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'otp' => null,
        ]);

        // 3. Buat User Admin Tambahan
        User::create([
            'name' => 'Admin Perpus',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'otp' => null,
        ]);

        // 4. Buat User Biasa
        User::create([
            'name' => 'User Biasa',
            'email' => 'user@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'otp' => null,
        ]);

        // 5. Buat Kategori
        $novel = Kategori::create(['nama_kategori' => 'Novel']);
        $biografi = Kategori::create(['nama_kategori' => 'Biografi']);
        $komik = Kategori::create(['nama_kategori' => 'Komik']);

        // 6. Buat Data Buku
        Buku::create([
            'idkategori' => $novel->idkategori,
            'kode' => 'NV-01',
            'judul' => 'Home Sweet Loan',
            'pengarang' => 'Almira Bastari',
        ]);

        Buku::create([
            'idkategori' => $biografi->idkategori,
            'kode' => 'BO-01',
            'judul' => 'Mohammad Hatta, Untuk Negeriku',
            'pengarang' => 'Taufik Abdullah',
        ]);

        Buku::create([
            'idkategori' => $novel->idkategori,
            'kode' => 'NV-02',
            'judul' => 'Keajaiban Toko Kelontong Namiya',
            'pengarang' => 'Keigo Higashino',
        ]);
    }
}