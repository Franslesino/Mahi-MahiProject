<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promo_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // e.g., "Weekend Special"
            $table->string('badge')->nullable(); // e.g., "FLASH SALE", "HOT DEAL"
            $table->text('description'); // e.g., "Diskon hingga 60%..."
            $table->string('button_text')->default('SHOP NOW'); // Button text
            $table->string('button_link')->nullable(); // Link when button clicked
            $table->string('gradient_from')->default('orange-500'); // Tailwind color
            $table->string('gradient_to')->default('red-600'); // Tailwind color
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_banners');
    }
};
