<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['instructor', 'creator'])
                        ->withCount('materials')
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
        // Debug log
        Log::info('Store method called');
        Log::info('Has file: ' . ($request->hasFile('image') ? 'YES' : 'NO'));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mode' => 'required|in:Online,Offline,Hybrid',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'learning' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                Log::info('Original name: ' . $file->getClientOriginalName());
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Store file
                $path = $file->storeAs('courses', $filename, 'public');
                
                Log::info('File stored at: ' . $path);
                
                $validated['image'] = $path;
            } catch (\Exception $e) {
                Log::error('Upload error: ' . $e->getMessage());
                return back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
            }
        }

        $validated['created_by'] = Auth::id();
        $validated['rating'] = 0;
        $validated['videos'] = 0;

        Course::create($validated);

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function edit(Course $course)
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        Log::info('Update method called for course: ' . $course->id);
        Log::info('Has file: ' . ($request->hasFile('image') ? 'YES' : 'NO'));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'mode' => 'required|in:Online,Offline,Hybrid',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'learning' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // Handle Image Upload
        if ($request->hasFile('image')) {
            try {
                // Delete old image
                if ($course->image && Storage::disk('public')->exists($course->image)) {
                    Storage::disk('public')->delete($course->image);
                    Log::info('Old image deleted: ' . $course->image);
                }
                
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('courses', $filename, 'public');
                
                Log::info('New file stored at: ' . $path);
                
                $validated['image'] = $path;
            } catch (\Exception $e) {
                Log::error('Upload error: ' . $e->getMessage());
                return back()->with('error', 'Gagal upload gambar: ' . $e->getMessage());
            }
        }

        $course->update($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Kursus berhasil diupdate!');
    }

    public function destroy(Course $course)
    {
        // Delete image
        if ($course->image && Storage::disk('public')->exists($course->image)) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Kursus berhasil dihapus!');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'materials.uploader']);
        return view('admin.courses.show', compact('course'));
    }

    
}