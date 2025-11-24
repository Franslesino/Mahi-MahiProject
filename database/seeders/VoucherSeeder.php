<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'PNJ-TIK-35',
                'name' => 'Diskon Mahasiswa PNJ TIK',
                'description' => 'Voucher khusus untuk mahasiswa Politeknik Negeri Jakarta jurusan Teknik Informatika dan Komputer. Dapatkan diskon 35% untuk semua kursus!',
                'type' => 'percentage',
                'value' => 35,
                'max_usage' => null, // Unlimited
                'min_purchase' => 0,
                'max_discount' => null, // No max discount
                'start_date' => now(),
                'end_date' => now()->addYear(), // Valid 1 tahun
                'is_active' => true,
            ],
            [
                'code' => 'WELCOME50K',
                'name' => 'Selamat Datang - Potongan Rp 50.000',
                'description' => 'Voucher selamat datang untuk pengguna baru! Dapatkan potongan langsung Rp 50.000',
                'type' => 'fixed',
                'value' => 50000,
                'max_usage' => 1000,
                'min_purchase' => 100000,
                'max_discount' => null,
                'start_date' => now(),
                'end_date' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'FLASH20',
                'name' => 'Flash Sale 20%',
                'description' => 'Flash sale spesial! Diskon 20% untuk semua kursus, maksimal potongan Rp 100.000',
                'type' => 'percentage',
                'value' => 20,
                'max_usage' => 500,
                'min_purchase' => 0,
                'max_discount' => 100000,
                'start_date' => now(),
                'end_date' => now()->addWeeks(2),
                'is_active' => true,
            ],
            [
                'code' => 'STUDENT15',
                'name' => 'Diskon Pelajar 15%',
                'description' => 'Voucher khusus pelajar dan mahasiswa. Diskon 15% untuk semua kursus',
                'type' => 'percentage',
                'value' => 15,
                'max_usage' => null,
                'min_purchase' => 50000,
                'max_discount' => 150000,
                'start_date' => now(),
                'end_date' => null, // Permanent
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::create($voucher);
        }

        $this->command->info('Vouchers seeded successfully!');
        $this->command->info('');
        $this->command->info('Available Vouchers:');
        $this->command->info('1. PNJ-TIK-35 - Diskon 35% untuk mahasiswa PNJ TIK');
        $this->command->info('2. WELCOME50K - Potongan Rp 50.000');
        $this->command->info('3. FLASH20 - Diskon 20% (max Rp 100.000)');
        $this->command->info('4. STUDENT15 - Diskon 15% untuk pelajar');
    }
}