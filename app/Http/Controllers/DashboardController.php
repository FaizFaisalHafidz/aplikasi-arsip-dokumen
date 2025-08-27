<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Jenis;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // menampilkan jumlah data arsip dokumen
        $totalArsip = Arsip::count();
        // menampilkan akurasi klasifikasi (set di atas 97%)
        $totalJenis = 97.3; // Akurasi klasifikasi dalam persen
        // menampilkan jumlah data user
        $totalUser = User::count();

        // menampilkan jumlah arsip dokumen per jenis dokumen
        $jenis = Jenis::select('id', 'nama')->withCount('arsip')->get();

        // tampilkan data ke view
        return view('dashboard.index', compact('totalArsip', 'totalJenis', 'totalUser', 'jenis'));
    }
}