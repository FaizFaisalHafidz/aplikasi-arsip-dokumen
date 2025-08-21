<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(): View
    {
        // tampilkan form ubah data
        return view('password.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        // validasi form
        $request->validate([
            'password_lama'       => 'required',
            'password_baru'       => 'required',
            'konfirmasi_password' => 'required|same:password_baru'
        ], [
            'password_lama.required'       => 'Password Lama tidak boleh kosong.',
            'password_baru.required'       => 'Password Baru tidak boleh kosong.',
            'konfirmasi_password.required' => 'Konfirmasi Password Baru tidak boleh kosong.',
            'konfirmasi_password.same'     => 'Password Baru dan Konfirmasi Password Baru tidak cocok.'
        ]);

        // cek password lama
        if (!Hash::check($request->password_lama, $request->user()->password)) {
            // jika password lama salah, kembali ke halaman ubah password dan tampilkan pesan password salah
            return back()->withErrors([
                'password_lama' => ['Password Lama yang Anda masukan salah.']
            ]);
        }

        // ubah password
        $request->user()->update([
            'password' => $request->password_baru
        ]);

        // kembali ke halaman ubah password dan tampilkan pesan berhasil ubah password
        return back()->with(['success' => 'Password berhasil diubah.']);
    }
}