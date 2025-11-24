<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode voucher (e.g., PNJ-TIK-35)
            $table->string('name'); // Nama voucher
            $table->text('description')->nullable(); // Deskripsi
            $table->enum('type', ['percentage', 'fixed'])->default('percentage'); // Tipe diskon
            $table->decimal('value', 10, 2); // Nilai diskon (35 untuk 35% atau 50000 untuk Rp 50.000)
            $table->integer('max_usage')->nullable(); // Maksimal penggunaan (null = unlimited)
            $table->integer('used_count')->default(0); // Jumlah sudah digunakan
            $table->decimal('min_purchase', 15, 2)->default(0); // Minimal pembelian
            $table->decimal('max_discount', 15, 2)->nullable(); // Maksimal diskon (untuk percentage)
            $table->timestamp('start_date')->nullable(); // Tanggal mulai
            $table->timestamp('end_date')->nullable(); // Tanggal berakhir
            $table->boolean('is_active')->default(true); // Status aktif
            $table->json('allowed_courses')->nullable(); // Kursus yang diperbolehkan (null = semua)
            $table->json('allowed_users')->nullable(); // User yang diperbolehkan (null = semua)
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index(['start_date', 'end_date']);
        });

        // Tabel untuk tracking penggunaan voucher
        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('discount_amount', 15, 2); // Jumlah diskon yang didapat
            $table->timestamp('used_at');
            
            // Indexes
            $table->index('used_at');
            $table->unique(['voucher_id', 'user_id', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
    }
};