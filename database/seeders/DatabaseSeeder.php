<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin;
use App\Models\asal;
use App\Models\bahasa;
use App\Models\kategori;
use App\Models\penerbit;
use App\Models\pengarang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Admin::create([
            'username' => 'Admin',
            'name' => 'Ridho Sulistyo Saputro',
            'password' => Hash::make('123'),
            'email' => 'ridhosulistyo1314@gmail.com',
            'phone' => '0895-4010-51613',
            'gender' => 'L',
            'religion' => 'Islam',
            'date' => '2006-02-13',
            'photo' => 'profile.png',
            'status' => 'superadmin'
        ]);

        $datakategori = [
            ['name' => 'Buku Teks'],
            ['name' => 'Referensi'],
            ['name' => 'Fiksi'],
            ['name' => 'Lain - Lain']
        ];

        $dataasal = [
            ['name' => 'Pembelian'],
            ['name' => 'Hadiah'],
            ['name' => 'Dana BOS'],
            ['name' => 'Dinas'],
            ['name' => 'Lain - Lain']
        ];

        $databahasa = [
            ['name' => 'Bahasa Indonesia'],
            ['name' => 'Bahasa Inggris'],
            ['name' => 'Bahasa Daerah'],
            ['name' => 'Bahasa Lainnya']
        ];

        kategori::insert($datakategori);

        asal::insert($dataasal);

        bahasa::insert($databahasa);
    }
}
