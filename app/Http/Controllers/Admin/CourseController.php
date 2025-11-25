<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Kursus::with(['pembuat', 'instructor'])
            ->withCount('materi')
            ->latest()
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'mode'           => 'required|in:Online,Offline,Hybrid',
            'learning'       => 'nullable|string',
            'badge'          => 'nullable|string|max:50',
            'badge_color'    => 'nullable|string|max:50',
            'status'         => 'required|in:active,inactive,draft',
            'instructor_id'  => 'required|exists:users,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'judul'              => $validated['title'],
            'deskripsi'          => $validated['description'],
            'kategori'           => $validated['category'],
            'harga'              => $validated['price'],
            'status'             => $validated['status'],
            'status_berbayar'    => $validated['price'] > 0,
            'status_diterbitkan' => $validated['status'] === 'active',
            'pembuat'            => Auth::id(),

            'mode'           => $validated['mode'],
            'discount_price' => $validated['discount_price'] ?? 0,
            'learning'       => $validated['learning'] ?? null,
            'badge'          => $validated['badge'] ?? null,
            'badge_color'    => $validated['badge_color'] ?? 'blue',
            'instructor_id'  => $validated['instructor_id'],
            'created_by'     => Auth::id(),
            'rating'         => 0,
            'videos'         => 0,
        ];

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('courses', $filename, 'public');
            $data['image'] = $path;
        }

        Kursus::create($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function edit(Kursus $course)
    {
        $instructors = User::where('role', 'instructor')->get();
        // variabel yang dilempar ke view: $course dan $instructors
        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'mode'           => 'required|in:Online,Offline,Hybrid',
            'learning'       => 'nullable|string',
            'badge'          => 'nullable|string|max:50',
            'badge_color'    => 'nullable|string|max:50',
            'status'         => 'required|in:active,inactive,draft',
            'instructor_id'  => 'required|exists:users,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'judul'              => $validated['title'],
            'deskripsi'          => $validated['description'],
            'kategori'           => $validated['category'],
            'harga'              => $validated['price'],
            'status'             => $validated['status'],
            'status_berbayar'    => $validated['price'] > 0,
            'status_diterbitkan' => $validated['status'] === 'active',

            'mode'           => $validated['mode'],
            'discount_price' => $validated['discount_price'] ?? 0,
            'learning'       => $validated['learning'] ?? null,
            'badge'          => $validated['badge'] ?? null,
            'badge_color'    => $validated['badge_color'] ?? 'blue',
            'instructor_id'  => $validated['instructor_id'],
        ];

        if ($request->hasFile('image')) {
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }

            $file     = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('courses', $filename, 'public');
            $data['image'] = $path;
        }

        $course->update($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diupdate!');
    }

    public function destroy(Kursus $course)
    {
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil dihapus!');
    }

    public function show(Kursus $course)
    {
        $course->load([
            'pembuat', 
            'materi' => function($query) {
                $query->orderBy('urutan');
            }, 
            'instructor',
            'enrollments.user'
        ]);

        // Get assignments for this course
        $assignments = \App\Models\Assignment::where('course_id', $course->id)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $stats = [
            'total_enrollments' => $course->enrollments->count(),
            'total_materials' => $course->materi->count(),
            'total_assignments' => $assignments->count(),
            'active_students' => $course->enrollments->where('status', 'active')->count(),
        ];

        return view('admin.courses.show', compact('course', 'assignments', 'stats'));
    }
}
