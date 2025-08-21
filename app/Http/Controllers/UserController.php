<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // jumlah data yang ditampilkan per paginasi halaman
        $pagination = 10;

        if ($request->search) {
            // menampilkan pencarian data
            $user = User::select('id', 'nama_user', 'username', 'role')
                ->whereAny(['nama_user', 'username'], 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $user = User::select('id', 'nama_user', 'username', 'role')
                ->latest()
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('user.index', compact('user'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // tampilkan form tambah data
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama_user' => 'required',
            'username'  => 'required|unique:users',
            'password'  => 'required',
            'role'      => 'required|in:Admin,User'
        ], [
            'nama_user.required' => 'Nama user tidak boleh kosong.',
            'username.required'  => 'Username tidak boleh kosong.',
            'username.unique'    => 'Username sudah ada.',
            'password.required'  => 'Password tidak boleh kosong.',
            'role.required'      => 'Role tidak boleh kosong.',
            'role.in'            => 'Role yang dipilih tidak valid.'
        ]);

        // simpan data
        User::create([
            'nama_user' => $request->nama_user,
            'username'  => $request->username,
            'password'  => $request->password,
            'role'      => $request->role
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil simpan data
        return redirect()->route('user.index')->with(['success' => 'Data user berhasil disimpan.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // dapatkan data berdasarakan "id"
        $user = User::findOrFail($id);

        // tampilkan form ubah data
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama_user' => 'required',
            'username'  => 'required|unique:users,username,' . $id,
            'role'      => 'required|in:Admin,User'
        ], [
            'nama_user.required' => 'Nama user tidak boleh kosong.',
            'username.required'  => 'Username tidak boleh kosong.',
            'username.unique'    => 'Username sudah ada.',
            'role.required'      => 'Role tidak boleh kosong.',
            'role.in'            => 'Role yang dipilih tidak valid.'
        ]);

        // dapatkan data berdasarakan "id"
        $user = User::findOrFail($id);

        // jika "password" diubah
        if ($request->password) {
            // ubah data
            $user->update([
                'nama_user' => $request->nama_user,
                'username'  => $request->username,
                'password'  => $request->password,
                'role'      => $request->role
            ]);
        }
        // jika "password" tidak diubah
        else {
            // ubah data
            $user->update([
                'nama_user' => $request->nama_user,
                'username'  => $request->username,
                'role'      => $request->role
            ]);
        }

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('user.index')->with(['success' => 'Data user berhasil diubah.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // dapatkan data berdasarakan "id"
        $user = User::findOrFail($id);

        // hapus data
        $user->delete();

        // redirect ke halaman index dan tampilkan pesan berhasil hapus data
        return redirect()->route('user.index')->with(['success' => 'Data user berhasil dihapus.']);
    }
}