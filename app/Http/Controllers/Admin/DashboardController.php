<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk fitur dashboard.
 */
class DashboardController extends Controller
{
    /**
     * Menampilkan daftar dashboard.
     */
    public function index()
    {
        // Basic Stats
        $totalCourses = Kursus::count();
        $activeCourses = Kursus::where('status_diterbitkan', true)->count();
        $totalUsers = User::count();
        $totalStudents = User::where('role', 'student')->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        
        // Transaction Stats (with error handling)
        try {
            $totalTransactions = Transaction::count();
            $pendingTransactions = Transaction::where('status', 'pending')->count();
            $paidTransactions = Transaction::where('status', 'paid')->count();
            $totalRevenue = Transaction::where('status', 'paid')->sum('total_bayar') ?? 0;
        } catch (\Exception $e) {
            $totalTransactions = 0;
            $pendingTransactions = 0;
            $paidTransactions = 0;
            $totalRevenue = 0;
        }
        
        // Today & This Month Revenue (with error handling)
        try {
            $todayRevenue = Transaction::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('total_bayar') ?? 0;
                
            $thisMonthRevenue = Transaction::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total_bayar') ?? 0;
        } catch (\Exception $e) {
            $todayRevenue = 0;
            $thisMonthRevenue = 0;
        }

        // 📊 Revenue by Category (for Donut Chart)
        try {
            $revenueByCategory = Transaction::where('transactions.status', 'paid')
                ->join('kursus', 'transactions.kursus_id', '=', 'kursus.id')
                ->select('kursus.kategori', DB::raw('SUM(transactions.total_bayar) as total'))
                ->groupBy('kursus.kategori')
                ->get();
        } catch (\Exception $e) {
            $revenueByCategory = collect();
        }

        // Calculate percentages for donut chart
        $totalCategoryRevenue = $revenueByCategory->sum('total');
        $categoryData = $revenueByCategory
            ->map(function($item) use ($totalCategoryRevenue) {
                return [
                    'category' => $item->kategori ?? 'Unknown',
                    'total' => $item->total ?? 0,
                    'percentage' => $totalCategoryRevenue > 0 
                        ? round(($item->total / $totalCategoryRevenue) * 100, 1) 
                        : 0
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Tampilkan 6 kategori teratas + "Lainnya", dan pastikan total persentase = 100%
        if ($categoryData->count() > 6) {
            $topCategories = $categoryData->take(6);
            $othersTotal = $categoryData->skip(6)->sum('total');

            if ($othersTotal > 0 && $totalCategoryRevenue > 0) {
                $topCategories->push([
                    'category' => 'Lainnya',
                    'total' => $othersTotal,
                    'percentage' => round(($othersTotal / $totalCategoryRevenue) * 100, 1)
                ]);
            }

            $categoryData = $topCategories->values();
        }

        // Koreksi pembulatan agar genap 100%
        $sumPercentage = $categoryData->sum('percentage');
        if ($sumPercentage > 0 && $categoryData->isNotEmpty()) {
            $adjustment = round(100 - $sumPercentage, 1);
            $lastIndex = $categoryData->count() - 1;
            $categoryData = $categoryData->map(function ($item, $index) use ($lastIndex, $adjustment) {
                if ($index === $lastIndex) {
                    $item['percentage'] = max(0, round(($item['percentage'] ?? 0) + $adjustment, 1));
                }
                return $item;
            });
        }

        // 📈 Monthly Transactions (for Line Chart)
        try {
            $monthlyTransactions = Transaction::where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->select(
                    DB::raw('EXTRACT(MONTH FROM paid_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy(DB::raw('EXTRACT(MONTH FROM paid_at)'))
                ->orderBy('month')
                ->get();
        } catch (\Exception $e) {
            $monthlyTransactions = collect();
        }

        // Fill missing months with 0
        $monthlyData = collect(range(1, 12))->map(function($month) use ($monthlyTransactions) {
            $data = $monthlyTransactions->firstWhere('month', $month);
            return $data ? $data->count : 0;
        });

        // 🏆 Top Courses (most enrolled)
        $topCoursesData = Kursus::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get();

        // Get max count from the collection
        $maxCount = $topCoursesData->max('enrollments_count') ?: 1;

        $topCourses = $topCoursesData->map(function($course) use ($maxCount) {
            return [
                'title' => $course->judul ?? $course->title,
                'count' => $course->enrollments_count,
                'percentage' => round(($course->enrollments_count / $maxCount) * 100)
            ];
        });

        // Recent Enrollments/Transactions
        $recentEnrollments = Enrollment::with(['user', 'kursus'])
            ->latest()
            ->take(5)
            ->get();

        // 👥 Gender Distribution (for Pie Chart)
        $genderData = User::select('gender', DB::raw('COUNT(*) as count'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get()
            ->map(function($item) use ($totalUsers) {
                return [
                    'gender' => ucfirst($item->gender),
                    'count' => $item->count,
                    'percentage' => $totalUsers > 0 
                        ? round(($item->count / $totalUsers) * 100, 1) 
                        : 0
                ];
            });

        // 💼 Profession Distribution (for Bar Chart)
        $profesiData = User::select('profesi', DB::raw('COUNT(*) as count'))
            ->whereNotNull('profesi')
            ->where('profesi', '!=', '')
            ->groupBy('profesi')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get()
            ->map(function($item) {
                return [
                    'profesi' => $item->profesi,
                    'count' => $item->count
                ];
            });

        return view('admin.dashboard', compact(
            'totalCourses',
            'activeCourses',
            'totalUsers',
            'totalStudents',
            'totalInstructors',
            'totalTransactions',
            'pendingTransactions',
            'paidTransactions',
            'totalRevenue',
            'todayRevenue',
            'thisMonthRevenue',
            'categoryData',
            'monthlyData',
            'topCourses',
            'recentEnrollments',
            'genderData',
            'profesiData'
        ));
    }
}
