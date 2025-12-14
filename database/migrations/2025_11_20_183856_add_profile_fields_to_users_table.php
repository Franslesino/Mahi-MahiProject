<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nama depan & belakang
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            // Tanggal lahir
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('phone');
            }

            // Gender
            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender', 20)->nullable()->after('dob');
            }

            // Path foto profil
            if (!Schema::hasColumn('users', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('password');
            }

            // (Opsional) NIM, kalau mau dipakai
            if (!Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->after('avatar_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nim')) {
                $table->dropColumn('nim');
            }

            if (Schema::hasColumn('users', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }

            if (Schema::hasColumn('users', 'gender')) {
                $table->dropColumn('gender');
            }

            if (Schema::hasColumn('users', 'dob')) {
                $table->dropColumn('dob');
            }

            if (Schema::hasColumn('users', 'last_name')) {
                $table->dropColumn('last_name');
            }

            if (Schema::hasColumn('users', 'first_name')) {
                $table->dropColumn('first_name');
            }
        });
    }
};
