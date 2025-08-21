<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriController extends Controller
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
            $kategori = Kategori::select('id', 'nama')
                ->where('nama', 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $kategori = Kategori::select('id', 'nama')
                ->latest()
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('kategori.index', compact('kategori'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // tampilkan form tambah data
        return view('kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama' => 'required|unique:kategori'
        ], [
            'nama.required' => 'Kategori dokumen tidak boleh kosong.',
            'nama.unique'   => 'Kategori dokumen sudah ada.'
        ]);

        // simpan data
        Kategori::create([
            'nama' => $request->nama
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil simpan data
        return redirect()->route('kategori.index')->with(['success' => 'Data kategori dokumen berhasil disimpan.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // dapatkan data berdasarakan "id"
        $kategori = Kategori::findOrFail($id);

        // tampilkan form ubah data
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama' => 'required|unique:kategori,nama,' . $id
        ], [
            'nama.required' => 'Kategori dokumen tidak boleh kosong.',
            'nama.unique'   => 'Kategori dokumen sudah ada.'
        ]);

        // dapatkan data berdasarakan "id"
        $kategori = Kategori::findOrFail($id);

        // ubah data
        $kategori->update([
            'nama' => $request->nama
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('kategori.index')->with(['success' => 'Data kategori dokumen berhasil diubah.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        // dapatkan data berdasarakan "id"
        $kategori = Kategori::findOrFail($id);

        // hapus data
        $kategori->delete();

        // redirect ke halaman index dan tampilkan pesan berhasil hapus data
        return redirect()->route('kategori.index')->with(['success' => 'Data kategori dokumen berhasil dihapus.']);
    }
}