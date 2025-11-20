<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('category', 100);
            $table->string('image', 500)->nullable();
            $table->integer('videos')->default(0);
            $table->enum('mode', ['Online', 'Offline', 'Hybrid'])->default('Online');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->decimal('rating', 2, 1)->default(0);
            $table->text('learning')->nullable();
            $table->string('badge', 50)->nullable();
            $table->string('badge_color', 50)->default('blue');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};