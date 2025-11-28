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
    public function index(Request $request)
    {
        $query = Kursus::with(['pembuat', 'instructor'])
            ->withCount('materi');

        // SEARCH (judul / deskripsi)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // pakai ilike karena Postgres (Supabase)
                $q->where('judul', 'ilike', "%{$search}%")
                  ->orWhere('deskripsi', 'ilike', "%{$search}%");
            });
        }

        // 🏷 FILTER STATUS (active / draft / inactive)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query
            ->latest()
            ->paginate(10)
            ->withQueryString(); // biar query di url nggak hilang pas ganti halaman

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

    public function edit(Kursus $kursus)
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.edit', [
            'course'      => $kursus,
            'instructors' => $instructors,
        ]);
    }

    public function update(Request $request, Kursus $kursus)
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
            if ($kursus->image && Storage::disk('public')->exists($kursus->image)) {
                Storage::disk('public')->delete($kursus->image);
            }

            $file     = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('courses', $filename, 'public');
            $data['image'] = $path;
        }

        $kursus->update($data);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diupdate!');
    }

    public function destroy(Kursus $kursus)
    {
        if ($kursus->image && Storage::disk('public')->exists($kursus->image)) {
            Storage::disk('public')->delete($kursus->image);
        }

        $kursus->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil dihapus!');
    }

    public function show(Kursus $kursus)
    {
        $kursus->load(['pembuat', 'materi']);

        return view('admin.courses.show', compact('kursus'));
    }
}