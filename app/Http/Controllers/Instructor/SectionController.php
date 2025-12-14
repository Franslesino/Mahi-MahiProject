<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    public function store(Request $request, Kursus $course)
    {
        $ownerIds = array_filter([$course->instructor_id, $course->pembuat]);
        if (!in_array(Auth::id(), $ownerIds, true)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['order'])) {
            $validated['order'] = $course->sections()->count() + 1;
        }

        $course->sections()->create($validated);

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Modul berhasil ditambahkan.');
    }

    public function update(Request $request, Kursus $course, CourseSection $section)
    {
        $ownerIds = array_filter([$course->instructor_id, $course->pembuat]);
        if (!in_array(Auth::id(), $ownerIds, true)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $section->update($validated);

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(CourseSection $section)
    {
        $course = $section->course;
        $ownerIds = array_filter([$course?->instructor_id, $course?->pembuat]);

        if (!in_array(Auth::id(), $ownerIds, true)) {
            abort(403);
        }

        $section->delete();

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Modul berhasil dihapus.');
    }
}
