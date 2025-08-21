<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JenisController extends Controller
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
            $jenis = Jenis::select('id', 'nama')
                ->where('nama', 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $jenis = Jenis::select('id', 'nama')
                ->orderBy('nama', 'asc')
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('jenis.index', compact('jenis'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // tampilkan form tambah data
        return view('jenis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama' => 'required|unique:jenis'
        ], [
            'nama.required' => 'Jenis dokumen tidak boleh kosong.',
            'nama.unique'   => 'Jenis dokumen sudah ada.'
        ]);

        // simpan data
        Jenis::create([
            'nama' => $request->nama
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil simpan data
        return redirect()->route('jenis.index')->with('success', 'Data jenis dokumen berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // dapatkan data berdasarakan "id"
        $jenis = Jenis::findOrFail($id);

        // tampilkan form ubah data
        return view('jenis.edit', compact('jenis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama' => 'required|unique:jenis,nama,' . $id
        ], [
            'nama.required' => 'Jenis dokumen tidak boleh kosong.',
            'nama.unique'   => 'Jenis dokumen sudah ada.'
        ]);

        // dapatkan data berdasarakan "id"
        $jenis = Jenis::findOrFail($id);

        // ubah data
        $jenis->update([
            'nama' => $request->nama
        ]);

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('jenis.index')->with('success', 'Data jenis dokumen berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        // dapatkan data berdasarakan "id"
        $jenis = Jenis::findOrFail($id);

        // hapus data
        $jenis->delete();

        // redirect ke halaman index dan tampilkan pesan berhasil hapus data
        return redirect()->route('jenis.index')->with('success', 'Data jenis dokumen berhasil dihapus.');
    }
}