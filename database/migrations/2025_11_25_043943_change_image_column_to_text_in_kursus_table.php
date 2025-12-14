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
        Schema::table('kursus', function (Blueprint $table) {
            // Change image column from string to text to store base64 encoded images
            $table->text('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            // Revert back to string if needed
            $table->string('image')->nullable()->change();
        });
    }
};
