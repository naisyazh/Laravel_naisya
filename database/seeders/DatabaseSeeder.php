<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kategori;
use App\Models\Buku;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Admin & User Biasa (Poin 4.c)
        User::create([
            'name' => 'Admin Perpus',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'User Biasa',
            'email' => 'user@mail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // 2. Buat Kategori (Poin 4.d.i)
        $novel = Kategori::create(['nama_kategori' => 'Novel']);
        $biografi = Kategori::create(['nama_kategori' => 'Biografi']);
        $komik = Kategori::create(['nama_kategori' => 'Komik']);

        // 3. Buat Data Buku (Poin 4.d.ii)
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