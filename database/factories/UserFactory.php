<?php

// ========================================
// database/factories/UserFactory.php
// ========================================
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'user', // default role
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indicate that the user is an instructor.
     */
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'instructor',
        ]);
    }

    /**
     * Indicate that the user is a regular user/student.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'user',
        ]);
    }
}

// ========================================
// database/factories/CourseFactory.php
// ========================================
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['Programming', 'Design', 'Business', 'Marketing', 'Photography', 'Music'];
        $modes = ['Online', 'Offline', 'Hybrid'];
        $statuses = ['active', 'inactive', 'draft'];
        $badges = ['Populer', 'Terbaru', 'Best Seller', null];
        $badgeColors = ['blue', 'green', 'red', 'yellow', 'purple'];

        $price = fake()->numberBetween(100000, 5000000);
        $hasDiscount = fake()->boolean(30); // 30% chance memiliki diskon

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'category' => fake()->randomElement($categories),
            'image' => null, // akan di-set di seeder jika perlu
            'videos' => fake()->numberBetween(5, 50),
            'mode' => fake()->randomElement($modes),
            'price' => $price,
            'discount_price' => $hasDiscount ? $price * 0.8 : null,
            'rating' => fake()->randomFloat(1, 3.5, 5.0),
            'learning' => implode("\n", fake()->sentences(5)),
            'badge' => fake()->randomElement($badges),
            'badge_color' => fake()->randomElement($badgeColors),
            'status' => fake()->randomElement($statuses),
            'instructor_id' => User::where('role', 'instructor')->inRandomOrder()->first()?->id,
            'created_by' => User::where('role', 'admin')->inRandomOrder()->first()?->id,
        ];
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the course is draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}

// ========================================
// database/seeders/DatabaseSeeder.php
// ========================================
namespace Database\Seeders;

use App\Models\User;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::create([
            'name' => 'Bagus Arriza',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        echo "✅ Admin created: admin@gmail.com\n";

        // Create Instructors
        $instructors = [];
        $instructorNames = [
            ['name' => 'John Instructor', 'email' => 'instructor@gmail.com'],
            ['name' => 'Sarah Johnson', 'email' => 'sarah@gmail.com'],
            ['name' => 'Michael Chen', 'email' => 'michael@gmail.com'],
        ];

        foreach ($instructorNames as $inst) {
            $instructors[] = User::create([
                'name' => $inst['name'],
                'email' => $inst['email'],
                'password' => bcrypt('password'),
                'role' => 'instructor',
                'email_verified_at' => now(),
            ]);
        }

        echo "✅ " . count($instructors) . " Instructors created\n";

        // Create Regular Users
        $users = User::factory()
            ->count(20)
            ->student()
            ->create();

        echo "✅ 20 Users created\n";

        // Create Courses
        $courses = [];
        $courseData = [
            [
                'judul' => 'Web Development Mastery',
                'deskripsi' => 'Belajar web development dari dasar hingga mahir. Mulai dari HTML, CSS, JavaScript, hingga framework modern seperti React dan Laravel.',
                'kategori' => 'Programming',
                'harga' => 500000,
                'status_berbayar' => true,
                'status_diterbitkan' => true,
                'status' => 'active',
            ],
            [
                'judul' => 'UI/UX Design Complete',
                'deskripsi' => 'Pelajari desain UI/UX dari nol. Menggunakan Figma, Adobe XD, dan prinsip-prinsip desain yang baik.',
                'kategori' => 'Design',
                'harga' => 450000,
                'status_berbayar' => true,
                'status_diterbitkan' => true,
                'status' => 'active',
            ],
            [
                'judul' => 'Digital Marketing Strategy',
                'deskripsi' => 'Strategi digital marketing lengkap untuk bisnis Anda. SEO, SEM, Social Media Marketing, dan Content Marketing.',
                'kategori' => 'Marketing',
                'harga' => 600000,
                'status_berbayar' => true,
                'status_diterbitkan' => true,
                'status' => 'active',
            ],
        ];

        foreach ($courseData as $data) {
            $course = Kursus::create([
                ...$data,
                'pembuat' => $instructors[array_rand($instructors)]->id,
            ]);

            $courses[] = $course;

            // Create materials for each course
            $materialCount = rand(5, 15);

            for ($i = 1; $i <= $materialCount; $i++) {
                Materi::create([
                    'kursus_id' => $course->id,
                    'judul' => "Materi " . $i . ": " . fake()->sentence(3),
                    'isi' => fake()->paragraph(),
                    'urutan' => $i,
                    'status_terkunci' => false,
                ]);
            }

            echo "✅ Course created: {$course->judul} ({$materialCount} materials)\n";
        }

        // Create more random courses (optional - hanya jika ada factory)
        // Uncomment jika sudah buat KursusFactory
        // $randomCourses = Kursus::factory()
        //     ->count(7)
        //     ->create();

        // foreach ($randomCourses as $course) {
        //     $materialCount = rand(5, 20);
        //     for ($i = 1; $i <= $materialCount; $i++) {
        //         Materi::create([
        //             'kursus_id' => $course->id,
        //             'judul' => "Module " . $i . ": " . fake()->sentence(3),
        //             'isi' => fake()->paragraph(),
        //             'urutan' => $i,
        //             'status_terkunci' => false,
        //         ]);
        //     }
        // }

        echo "✅ Courses creation completed\n";

        // Create Enrollments
        $allCourses = Kursus::all();
        foreach ($users->random(15) as $user) {
            $coursesToEnroll = $allCourses->random(rand(1, 3));
            
            foreach ($coursesToEnroll as $course) {
                $status = ['pending', 'active', 'completed'][array_rand(['pending', 'active', 'completed'])];

                Enrollment::create([
                    'user_id' => $user->id,
                    'kursus_id' => $course->id,
                    'status_pendaftaran' => $status,
                    'tanggal_daftar' => $status !== 'pending' ? now()->subDays(rand(1, 30)) : null,
                    'tanggal_selesai' => $status === 'completed' ? now()->subDays(rand(1, 15)) : null,
                ]);
            }
        }

        echo "✅ Enrollments created\n";

        echo "\n";
        echo "========================================\n";
        echo "🎉 Seeding completed successfully!\n";
        echo "========================================\n";
        echo "Login credentials:\n";
        echo "Admin: admin@gmail.com / password\n";
        echo "Instructor: instructor@gmail.com / password\n";
        echo "User: user@gmail.com / password (or any generated user)\n";
        echo "========================================\n";
    }
}

// ========================================
// database/seeders/UserSeeder.php (Optional - Separate Seeder)
// ========================================
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Bagus Arriza',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Instructors
        User::factory()
            ->count(3)
            ->instructor()
            ->create();

        // Regular Users
        User::factory()
            ->count(20)
            ->student()
            ->create();
    }
}

// ========================================
// database/seeders/CourseSeeder.php (Optional - Separate Seeder)
// ========================================
namespace Database\Seeders;

use App\Models\Kursus;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $instructors = User::where('role', 'instructor')->get();

        // Create 10 courses (optional)
        // Uncomment jika ingin membuat lebih banyak courses
        // Kursus::factory()
        //     ->count(10)
        //     ->create()
        //     ->each(function ($course) {
        //         Materi::factory()
        //             ->count(rand(5, 15))
        //             ->create([
        //                 'kursus_id' => $course->id,
        //             ]);
        //     });
    }
}

// ========================================
// Jalankan Seeder:
// ========================================
/*
# Seed semua
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CourseSeeder

# Fresh migration + seed
php artisan migrate:fresh --seed

# Rollback, migrate, dan seed
php artisan migrate:refresh --seed
*/