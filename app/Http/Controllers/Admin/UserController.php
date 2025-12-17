<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.users.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,instructor,student,user',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        // Normalisasi: jika ada input "user" dari form lama, simpan sebagai "student"
        if ($validated['role'] === 'user') {
            $validated['role'] = 'student';
        }

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    /**
     * Display the specified user with role-specific information
     */
    public function show(User $user)
    {
        // Load relasi berdasarkan role
        if ($user->role === 'instructor') {
            // Untuk instructor: ambil courses yang dia ampu beserta jumlah peserta
            $user->load([
                'instructorCourses' => function ($query) {
                    $query->withCount('enrollments')
                        ->orderBy('created_at', 'desc');
                }
            ]);

            // Hitung statistik - total revenue dari kursus yang diajar
            $totalRevenue = \DB::table('transactions')
                ->whereIn('kursus_id', $user->instructorCourses->pluck('id'))
                ->where('status', 'paid')
                ->sum('total_bayar');

            $stats = [
                'total_courses' => $user->instructorCourses->count(),
                'total_students' => $user->instructorCourses->sum('enrollments_count'),
                'active_courses' => $user->instructorCourses->where('status_diterbitkan', true)->count(),
                'total_revenue' => $totalRevenue
            ];

        } elseif ($user->role === 'student' || $user->role === 'user') {
            // Untuk student/user: ambil courses yang diikuti dan transaksi
            $user->load([
                'enrollments.kursus.instructor',
                'transactions' => function ($query) {
                    $query->latest()->limit(10);
                }
            ]);

            // Status yang dihitung sebagai partisipasi / aktif
            $participatingStatuses = ['active', 'completed', 'paid', 'approved', 'enrolled'];
            $inProgressStatuses = ['active', 'paid', 'approved', 'enrolled'];
            $completedStatuses = ['completed'];

            $enrollments = $user->enrollments;
            $enrollmentsCountable = $enrollments->whereIn('status_pendaftaran', $participatingStatuses);

            // Hitung statistik
            $stats = [
                'total_enrolled' => $enrollmentsCountable->count(),
                'completed_courses' => $enrollmentsCountable->whereIn('status_pendaftaran', $completedStatuses)->count(),
                'in_progress' => $enrollmentsCountable->whereIn('status_pendaftaran', $inProgressStatuses)->count(),
                'total_spent' => $user->transactions()->where('status', 'paid')->sum('total_bayar')
            ];

        } else {
            // Untuk admin atau role lainnya
            $stats = null;
        }

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,instructor,student,user',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($validated['role']) && $validated['role'] === 'user') {
            $validated['role'] = 'student';
        }


        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }

    /**
     * Bulk delete multiple users
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->input('user_ids');

        // Prevent deleting current admin user
        $currentUserId = auth()->id();
        $userIds = array_filter($userIds, fn($id) => $id != $currentUserId);

        if (empty($userIds)) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Tidak ada pengguna yang dihapus.');
        }

        $deletedCount = User::whereIn('id', $userIds)->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "$deletedCount pengguna berhasil dihapus!");
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::query();

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();

        $filename = 'users_export_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'ID',
                'Nama',
                'Email',
                'Role',
                'Telepon',
                'NIM',
                'Profesi',
                'Email Verified',
                'Tanggal Daftar',
            ]);

            // CSV Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->phone ?? '-',
                    $user->nim ?? '-',
                    $user->profesi ?? '-',
                    $user->email_verified_at ? 'Ya' : 'Tidak',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
