<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PromoBanner;

class PromoBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PromoBanner::create([
            'title' => 'PNJ SPECIAL',
            'badge' => '35% OFF',
            'description' => 'Dapatkan Voucher Kode Bagi<br>Mahasiswa Politeknik Negeri Jakarta',
            'button_text' => 'MASUKKAN NIM ANDA',
            'button_link' => null,
            'gradient_from' => 'teal-600',
            'gradient_to' => 'teal-700',
            'order' => 1,
            'is_active' => true,
        ]);

        PromoBanner::create([
            'title' => 'Bundle Package',
            'badge' => 'HOT DEAL',
            'description' => 'Beli 3 Kursus Dapat Diskon 50%<br>Penawaran Terbatas!',
            'button_text' => 'LIHAT BUNDLE',
            'button_link' => null,
            'gradient_from' => 'purple-600',
            'gradient_to' => 'pink-600',
            'order' => 2,
            'is_active' => true,
        ]);

        PromoBanner::create([
            'title' => 'Weekend Special',
            'badge' => 'FLASH SALE',
            'description' => 'Diskon hingga 60% untuk<br>Kursus Pilihan Akhir Pekan',
            'button_text' => 'SHOP NOW',
            'button_link' => null,
            'gradient_from' => 'orange-500',
            'gradient_to' => 'red-600',
            'order' => 3,
            'is_active' => true,
        ]);
    }
}
