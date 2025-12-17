<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseSchedule;
use App\Models\Kursus;
use Illuminate\Http\Request;

class CourseScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Kursus $course)
    {
        $schedules = $course->schedules()->orderBy('date')->orderBy('start_time')->get();
        return view('admin.courses.schedules.index', compact('course', 'schedules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required', // after:start_time validasi kadang tricky dengan format waktu, kita handle simple dulu
            'location' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url',
            'description' => 'nullable|string',
        ]);

        // Tambahan validasi logic waktu jika perlu, tapi format H:i biasanya aman
        
        $course->schedules()->create($validated);

        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kursus $course, CourseSchedule $schedule)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'location' => 'nullable|string|max:255',
            'meeting_url' => 'nullable|url',
            'description' => 'nullable|string',
            'status' => 'required|in:scheduled,completed,cancelled',
        ]);

        $schedule->update($validated);

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kursus $course, CourseSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->back()->with('success', 'Jadwal berhasil dihapus');
    }
}
