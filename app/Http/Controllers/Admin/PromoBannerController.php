<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use Illuminate\Http\Request;

/**
 * Controller untuk fitur promo banner.
 */
class PromoBannerController extends Controller
{
    /**
     * Menampilkan daftar promo banner.
     */
    public function index()
    {
        $banners = PromoBanner::orderBy('order')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.promo-banners.index', compact('banners'));
    }

    /**
     * Menampilkan form tambah promo banner.
     */
    public function create()
    {
        return view('admin.promo-banners.create');
    }

    /**
     * Menyimpan promo banner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'badge' => 'nullable|string|max:50',
            'description' => 'required|string|max:500',
            'button_text' => 'required|string|max:50',
            'button_link' => 'nullable|url|max:255',
            'gradient_from' => 'required|string|max:50',
            'gradient_to' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PromoBanner::create($validated);

        return redirect()->route('admin.promo-banners.index')
            ->with('success', 'Banner promo berhasil ditambahkan!');
    }

    /**
     * Menampilkan detail promo banner.
     */
    public function show(PromoBanner $promoBanner)
    {
        return view('admin.promo-banners.show', compact('promoBanner'));
    }

    /**
     * Menampilkan form ubah promo banner.
     */
    public function edit(PromoBanner $promoBanner)
    {
        return view('admin.promo-banners.edit', compact('promoBanner'));
    }

    /**
     * Memperbarui promo banner.
     */
    public function update(Request $request, PromoBanner $promoBanner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'badge' => 'nullable|string|max:50',
            'description' => 'required|string|max:500',
            'button_text' => 'required|string|max:50',
            'button_link' => 'nullable|url|max:255',
            'gradient_from' => 'required|string|max:50',
            'gradient_to' => 'required|string|max:50',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $promoBanner->update($validated);

        return redirect()->route('admin.promo-banners.index')
            ->with('success', 'Banner promo berhasil diperbarui!');
    }

    /**
     * Menghapus promo banner.
     */
    public function destroy(PromoBanner $promoBanner)
    {
        $promoBanner->delete();

        return redirect()->route('admin.promo-banners.index')
            ->with('success', 'Banner promo berhasil dihapus!');
    }

    /**
     * Mengubah status status.
     */
    public function toggleStatus(PromoBanner $promoBanner)
    {
        $promoBanner->update([
            'is_active' => !$promoBanner->is_active
        ]);

        $status = $promoBanner->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Banner promo berhasil {$status}!");
    }
}
