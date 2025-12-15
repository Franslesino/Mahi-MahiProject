<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\CourseSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function store(Request $request, Kursus $course)
    {
        $ownerIds = $this->resolveOwnerIds($course);
        if (!in_array(Auth::id(), $ownerIds, true)) {
            abort(403);
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'order' => [
                'nullable',
                'integer',
                'min:0',
                Rule::unique('course_sections', 'order')->where(fn ($q) => $q->where('course_id', $course->id)),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('course_sections', 'title')->where(fn ($q) => $q->where('course_id', $course->id)),
            ],
        ], [
            'title.unique' => 'Modul dengan judul yang sama sudah ada.',
            'order.unique' => 'Urutan modul sudah digunakan.',
        ]);

        // Jika order tidak diisi, set ke urutan terakhir + 1
        $validated['order'] = $validated['order'] ?? ($course->sections()->max('order') + 1);

        $course->sections()->create($validated);

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Modul berhasil ditambahkan.');
    }

    public function update(Request $request, CourseSection $section)
    {
        $course = $section->course;
        if (!$course) {
            abort(404, 'Course not found for section');
        }

        $ownerIds = $this->resolveOwnerIds($course);
        if (!in_array(Auth::id(), $ownerIds, true)) {
            abort(403);
        }

        $validated = $request->validate([
            'description' => 'nullable|string',
            'order' => [
                'nullable',
                'integer',
                'min:0',
                Rule::unique('course_sections', 'order')
                    ->where(fn ($q) => $q->where('course_id', $course->id))
                    ->ignore($section->id),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('course_sections', 'title')
                    ->where(fn ($q) => $q->where('course_id', $course->id))
                    ->ignore($section->id),
            ],
        ], [
            'title.unique' => 'Modul dengan judul yang sama sudah ada.',
            'order.unique' => 'Urutan modul sudah digunakan.',
        ]);

        // Jika order tidak diisi, pertahankan order lama atau set ke akhir
        $validated['order'] = $validated['order'] ?? $section->order ?? ($course->sections()->max('order') + 1);

        $section->update($validated);

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function destroy(CourseSection $section)
    {
        $course = $section->course;
        $ownerIds = $course ? $this->resolveOwnerIds($course) : [];

        if (!in_array(Auth::id(), $ownerIds, true)) {
            abort(403);
        }

        $section->delete();

        return redirect()
            ->route('instructor.courses.show', $course)
            ->with('success', 'Modul berhasil dihapus.');
    }

    /**
     * Ambil daftar ID pemilik kursus (instructor/creator) tanpa memicu error kolom.
     */
    private function resolveOwnerIds(Kursus $course): array
    {
        if (!$course) {
            return [];
        }

        $attrs = $course->getAttributes();
        return array_values(array_filter([
            $attrs['instructor_id'] ?? null,
            $attrs['pembuat'] ?? null,
            $attrs['created_by'] ?? null,
        ]));
    }
}