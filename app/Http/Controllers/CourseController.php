<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ WAJIB ADA

class CourseController extends Controller
{
    public function index()
    {
        $courses = Kursus::with(['pembuat'])
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
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,draft',
            'status_berbayar' => 'nullable|boolean',
            'status_diterbitkan' => 'nullable|boolean',
            'pembuat' => 'required|exists:users,id',
        ]);

        // ✅ Auth sudah dikenali
        $validated['pembuat'] = Auth::id();

        Kursus::create($validated);

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function edit(Kursus $kursus)
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.edit', compact('kursus', 'instructors'));
    }

    public function update(Request $request, Kursus $kursus)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive,draft',
            'status_berbayar' => 'nullable|boolean',
            'status_diterbitkan' => 'nullable|boolean',
            'pembuat' => 'required|exists:users,id',
        ]);

        $kursus->update($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Kursus berhasil diupdate!');
    }

    public function destroy(Kursus $kursus)
    {
        $kursus->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Kursus berhasil dihapus!');
    }

    public function show(Kursus $kursus)
    {
        $kursus->load(['pembuat', 'materi']);
        return view('admin.courses.show', compact('kursus'));
    }
}