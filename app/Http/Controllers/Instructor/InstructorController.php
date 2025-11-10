<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InstructorController extends Controller
{
    public function dashboard()
    {
        $courses = Course::where('instructor_id', Auth::id())
            ->withCount('materials')
            ->latest()
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact('courses'));
    }

    public function index()
    {
        $courses = Course::where('instructor_id', Auth::id())->paginate(10);

        return view('instructor.courses.index', compact('courses'));
    }

    public function courses()
    {
        $courses = Course::where('instructor_id', Auth::id())->paginate(10);
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('instructor.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|nullable',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
        }

        Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'instructor_id' => Auth::id(),
            'image' => $path,
        ]);

        return redirect()->route('instructor.courses')->with('success', 'Course berhasil dibuat.');
    }
}