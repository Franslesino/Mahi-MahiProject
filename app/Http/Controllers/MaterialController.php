<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller untuk fitur materi.
 */
class MaterialController extends Controller
{
    /**
     * Menangani proses admin dashboard.
     */
    public function adminDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Menangani proses pengguna dashboard.
     */
    public function userDashboard()
    {
        return view('user.dashboard');
    }
}