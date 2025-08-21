<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Login form.
     */
    public function login(): View
    {
        // tampilkan form login
        return view('auth.login');
    }

    /**
     * Login authentication.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // validasi form
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.'
        ]);
        
        // jika login berhasil
        if (Auth::attempt($credentials)) {
            // regenerate session user
            $request->session()->regenerate();

            // redirect ke halaman yang dituju (intended) atau halaman default (dashboard)
            return redirect()->intended(route('dashboard.index'));
        }

        // jika login gagal, kembali ke halaman login dan tampilkan pesan gagal login
        return back()->with(['error' => 'Username atau Password salah. Cek kembali Username dan Password Anda.']);
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // redirect ke halaman login dan tampilkan pesan berhasil logout
        return redirect()->route('login')->with(['success' => 'Anda telah berhasil logout.']);
    }
}