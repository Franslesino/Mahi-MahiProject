<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATA KURSUS ===\n";
$courses = \App\Models\Kursus::select('id', 'judul', 'instructor_id', 'pembuat')->get();
foreach($courses as $course) {
    echo "ID: {$course->id} | Judul: {$course->judul} | instructor_id: {$course->instructor_id} | pembuat: {$course->pembuat}\n";
}

echo "\n=== DATA INSTRUCTOR ===\n";
$instructors = \App\Models\User::where('role', 'instructor')->select('id', 'name', 'email')->get();
foreach($instructors as $instructor) {
    echo "ID: {$instructor->id} | Name: {$instructor->name} | Email: {$instructor->email}\n";
}

echo "\n=== AUTH USER (instruktur2@gmail.com) ===\n";
$user = \App\Models\User::where('email', 'instruktur2@gmail.com')->first();
if($user) {
    echo "ID: {$user->id} | Name: {$user->name} | Role: {$user->role}\n";
    echo "\nKursus untuk instructor ini:\n";
    $myCourses = \App\Models\Kursus::where(function($query) use ($user) {
        $query->where('instructor_id', $user->id)
              ->orWhere('pembuat', $user->id);
    })->get();
    echo "Total: " . $myCourses->count() . " kursus\n";
    foreach($myCourses as $course) {
        echo "- {$course->judul}\n";
    }
}
