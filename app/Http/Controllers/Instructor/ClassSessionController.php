<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Kursus;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSessionController extends Controller
{
    /**
     * Display list of class sessions for a course
     */
    public function index(Kursus $course)
    {
        $this->authorizeInstructor($course);

        $sessions = $course->classSessions()
            ->withCount([
                'attendances as hadir_count' => function ($query) {
                    $query->where('status', 'hadir');
                }
            ])
            ->get();

        return view('instructor.class-sessions.index', compact('course', 'sessions'));
    }

    /**
     * Show form to create a new class session
     */
    public function create(Kursus $course)
    {
        $this->authorizeInstructor($course);

        return view('instructor.class-sessions.create', compact('course'));
    }

    /**
     * Store a new class session
     */
    public function store(Request $request, Kursus $course)
    {
        $this->authorizeInstructor($course);

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi' => 'nullable|string|max:255',
            'tipe' => 'required|in:online,offline',
            'meeting_link' => 'nullable|url|max:500',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $validated['kursus_id'] = $course->id;
        $validated['status'] = 'scheduled';

        $session = ClassSession::create($validated);

        // Auto-create attendance records for enrolled students
        $enrollments = $course->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed'])
            ->get();

        foreach ($enrollments as $enrollment) {
            Attendance::create([
                'class_session_id' => $session->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'status' => 'tidak_hadir',
            ]);
        }

        return redirect()
            ->route('instructor.courses.sessions.index', $course)
            ->with('success', 'Sesi kelas berhasil ditambahkan.');
    }

    /**
     * Show form to edit a class session
     */
    public function edit(Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        return view('instructor.class-sessions.edit', compact('course', 'session'));
    }

    /**
     * Update a class session
     */
    public function update(Request $request, Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        $validated = $request->validate([
            'judul' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'lokasi' => 'nullable|string|max:255',
            'tipe' => 'required|in:online,offline',
            'meeting_link' => 'nullable|url|max:500',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $session->update($validated);

        return redirect()
            ->route('instructor.courses.sessions.index', $course)
            ->with('success', 'Sesi kelas berhasil diperbarui.');
    }

    /**
     * Delete a class session
     */
    public function destroy(Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        $session->delete();

        return redirect()
            ->route('instructor.courses.sessions.index', $course)
            ->with('success', 'Sesi kelas berhasil dihapus.');
    }

    /**
     * Authorize that the current user is the instructor of the course
     */
    private function authorizeInstructor(Kursus $course)
    {
        $user = Auth::user();

        if ($course->instructor_id !== $user->id && $course->pembuat !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }
    }
}
