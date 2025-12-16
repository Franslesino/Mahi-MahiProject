<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with sample data so public pages
     * (kursus, instruktur) tidak kosong.
     */
    public function run(): void
    {
        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Instructors
        $instructorData = [
            ['name' => 'John Instructor', 'email' => 'instructor1@example.com'],
            ['name' => 'Sarah Johnson', 'email' => 'instructor2@example.com'],
            ['name' => 'Michael Chen', 'email' => 'instructor3@example.com'],
        ];

        $instructors = collect($instructorData)->map(function ($data) {
            return User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'instructor',
                    'email_verified_at' => now(),
                ]
            );
        });

        // Students
        $students = User::factory()
            ->count(8)
            ->state([
                'role' => 'student',
                'email_verified_at' => now(),
            ])
            ->create();

        // Courses (published)
        $courseSeed = [
            [
                'judul' => 'Web Development Mastery',
                'deskripsi' => 'Belajar HTML, CSS, dan JavaScript modern sampai siap produksi.',
                'kategori' => 'Web Development',
                'harga' => 500000,
                'discount_price' => 350000,
                'badge' => 'Best Seller',
                'badge_color' => 'blue',
                'videos' => 24,
            ],
            [
                'judul' => 'Data Analytic dengan Python',
                'deskripsi' => 'Analisis data menggunakan Pandas, Matplotlib, dan dasar machine learning.',
                'kategori' => 'Data Analytic',
                'harga' => 650000,
                'discount_price' => 0,
                'badge' => 'Trending',
                'badge_color' => 'green',
                'videos' => 18,
            ],
            [
                'judul' => 'UI/UX Design Fundamentals',
                'deskripsi' => 'Rancang pengalaman pengguna dengan Figma, wireframe, dan prototyping.',
                'kategori' => 'Graphic Design',
                'harga' => 480000,
                'discount_price' => 0,
                'badge' => 'Baru',
                'badge_color' => 'purple',
                'videos' => 15,
            ],
        ];

        $courses = collect($courseSeed)->map(function ($data) use ($instructors, $admin) {
            $instructor = $instructors->random();

            return Kursus::firstOrCreate(
                ['judul' => $data['judul']],
                [
                    'deskripsi' => $data['deskripsi'],
                    'kategori' => $data['kategori'],
                    'harga' => $data['harga'],
                    'discount_price' => $data['discount_price'],
                    'status_berbayar' => true,
                    'status_diterbitkan' => true,
                    'status' => 'active',
                    'pembuat' => $admin->id,
                    'instructor_id' => $instructor->id,
                    'created_by' => $admin->id,
                    'badge' => $data['badge'],
                    'badge_color' => $data['badge_color'],
                    'videos' => $data['videos'],
                    'learning' => "Belajar terstruktur\nAkses seumur hidup\nSertifikat kelulusan",
                    'min_passing_score' => 70,
                    'max_quiz_attempts' => 3,
                    'require_final_quiz' => false,
                    'mode' => 'Online',
                    'rating' => 4.8,
                ]
            );
        });

        // Materials per course (ringkas)
        $courses->each(function (Kursus $course) {
            if ($course->materi()->count() > 0) {
                return;
            }

            for ($i = 1; $i <= 6; $i++) {
                Materi::create([
                    'kursus_id' => $course->id,
                    'judul' => "Materi {$i} - " . fake()->sentence(3),
                    'description' => fake()->sentence(8),
                    'content' => fake()->paragraph(),
                    'type' => $i % 3 === 0 ? 'pdf' : 'video',
                    'url_konten' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'urutan' => $i,
                    'status_terkunci' => false,
                    'is_preview' => $i === 1,
                    'status' => 'published',
                ]);
            }
        });

        // Enroll a few students to courses
        $courses->each(function (Kursus $course) use ($students) {
            $sampleStudents = $students->random(min(3, $students->count()));
            foreach ($sampleStudents as $student) {
                Enrollment::firstOrCreate(
                    [
                        'kursus_id' => $course->id,
                        'user_id' => $student->id,
                    ],
                    [
                        'status_pendaftaran' => 'active',
                        'tanggal_daftar' => now()->subDays(rand(1, 10)),
                        'tanggal_selesai' => null,
                    ]
                );
            }
        });
    }
}
