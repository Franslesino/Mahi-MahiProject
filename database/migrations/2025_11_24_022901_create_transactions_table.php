<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // Kode unik transaksi
            
            // User & Course
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kursus_id')->constrained('kursus')->cascadeOnDelete();
            
            // Pricing Details
            $table->decimal('harga_asli', 15, 2); // Harga original
            $table->decimal('harga_diskon', 15, 2)->nullable(); // Harga setelah diskon
            $table->decimal('total_bayar', 15, 2); // Total yang harus dibayar
            $table->integer('diskon_persen')->default(0); // Persentase diskon
            
            // Payment Details
            $table->enum('payment_method', ['bank_transfer', 'e_wallet', 'credit_card', 'virtual_account'])->nullable();
            $table->string('payment_channel')->nullable(); // BCA, Mandiri, GoPay, dll
            $table->text('payment_details')->nullable(); // JSON details (nomor VA, dll)
            
            // Status & Timing
            $table->enum('status', ['pending', 'paid', 'expired', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('payment_deadline')->nullable(); // Batas waktu pembayaran
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            
            // Additional Info
            $table->text('notes')->nullable(); // Catatan pembeli
            $table->string('invoice_url')->nullable(); // URL invoice PDF
            
            $table->timestamps();
            
            // Indexes
            $table->index('transaction_code');
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};