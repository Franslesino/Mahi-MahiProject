<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Kursus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display attendance for a class session
     */
    public function index(Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        $attendances = $session->attendances()
            ->with(['user', 'enrollment'])
            ->orderBy('created_at')
            ->get();

        $statusOptions = Attendance::getStatusOptions();

        return view('instructor.attendances.index', compact('course', 'session', 'attendances', 'statusOptions'));
    }

    /**
     * Update attendance status for a student
     */
    public function update(Request $request, Kursus $course, ClassSession $session, Attendance $attendance)
    {
        $this->authorizeInstructor($course);

        $validated = $request->validate([
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit,terlambat',
            'catatan' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'check_in_time' => $validated['status'] === 'hadir' ? now() : null,
            'updated_by' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diperbarui.',
                'attendance' => $attendance->fresh(),
            ]);
        }

        return back()->with('success', 'Status kehadiran berhasil diperbarui.');
    }

    /**
     * Bulk update attendance
     */
    public function bulkUpdate(Request $request, Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.id' => 'required|exists:attendances,id',
            'attendances.*.status' => 'required|in:hadir,tidak_hadir,izin,sakit,terlambat',
        ]);

        foreach ($validated['attendances'] as $data) {
            Attendance::where('id', $data['id'])->update([
                'status' => $data['status'],
                'check_in_time' => $data['status'] === 'hadir' ? now() : null,
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('instructor.courses.sessions.attendances.index', [$course, $session])
            ->with('success', 'Kehadiran berhasil diperbarui.');
    }

    /**
     * Mark all as present
     */
    public function markAllPresent(Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        $session->attendances()->update([
            'status' => 'hadir',
            'check_in_time' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Semua peserta ditandai hadir.');
    }

    /**
     * Regenerate attendance records (for new enrollments)
     */
    public function regenerate(Kursus $course, ClassSession $session)
    {
        $this->authorizeInstructor($course);

        // Get current attendance user IDs
        $existingUserIds = $session->attendances()->pluck('user_id')->toArray();

        // Get enrolled students
        $enrollments = $course->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed'])
            ->whereNotIn('user_id', $existingUserIds)
            ->get();

        // Create attendance for new students
        foreach ($enrollments as $enrollment) {
            Attendance::create([
                'class_session_id' => $session->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'status' => 'tidak_hadir',
            ]);
        }

        return back()->with('success', 'Data kehadiran berhasil diperbarui untuk peserta baru.');
    }

    /**
     * Authorize instructor
     */
    private function authorizeInstructor(Kursus $course)
    {
        $user = Auth::user();

        if ($course->instructor_id !== $user->id && $course->pembuat !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }
    }
}
