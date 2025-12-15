<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\Kursus;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::withCount('usages')
            ->latest()
            ->paginate(10);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $courses = Kursus::where('status_diterbitkan', true)
            ->orderBy('judul')
            ->get();

        return view('admin.vouchers.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_usage' => 'nullable|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'allowed_courses' => 'nullable|array',
            'allowed_courses.*' => 'exists:kursus,id',
        ], [
            'end_date.after' => 'Tanggal Mulai lebih besar dari Tanggal Berakhir.',
        ]);

        // Validasi khusus untuk percentage
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()
                ->withErrors(['value' => 'Nilai diskon untuk tipe Persentase maksimal adalah 100%'])
                ->withInput();
        }

        // Default nilai yang nullable agar tidak null violation
        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['max_usage'] = $validated['max_usage'] ?? null;
        $validated['max_discount'] = $validated['max_discount'] ?? null;

        $validated['is_active'] = $request->has('is_active');
        $validated['used_count'] = 0;
        $validated['code'] = strtoupper($validated['code']);

        Voucher::create($validated);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan!');
    }

    public function edit(Voucher $voucher)
    {
        $courses = Kursus::where('status_diterbitkan', true)
            ->orderBy('judul')
            ->get();

        return view('admin.vouchers.edit', compact('voucher', 'courses'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_usage' => 'nullable|integer|min:1',
            'min_purchase' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'allowed_courses' => 'nullable|array',
            'allowed_courses.*' => 'exists:kursus,id',
        ], [
            'end_date.after' => 'Tanggal Mulai lebih besar dari Tanggal Berakhir.',
        ]);

        // Validasi khusus untuk percentage
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()
                ->withErrors(['value' => 'Nilai diskon untuk tipe Persentase maksimal adalah 100%'])
                ->withInput();
        }

        $validated['min_purchase'] = $validated['min_purchase'] ?? 0;
        $validated['max_usage'] = $validated['max_usage'] ?? null;
        $validated['max_discount'] = $validated['max_discount'] ?? null;

        $validated['is_active'] = $request->has('is_active');
        $validated['code'] = strtoupper($validated['code']);

        $voucher->update($validated);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diupdate!');
    }

    public function destroy(Voucher $voucher)
    {
        // Cek apakah voucher sudah digunakan
        if ($voucher->used_count > 0) {
            return back()->with('error', 'Voucher tidak dapat dihapus karena sudah digunakan!');
        }

        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus!');
    }

    public function show(Voucher $voucher)
    {
        $voucher->load(['usages.user', 'usages.transaction']);

        return view('admin.vouchers.show', compact('voucher'));
    }

    public function toggleStatus(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);

        $status = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Voucher berhasil {$status}!");
    }
}
