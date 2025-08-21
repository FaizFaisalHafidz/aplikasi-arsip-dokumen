<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Jenis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // ambil data jenis
        $jenis = Jenis::get(['id', 'nama']);

        // tampilkan form filter data arsip dengan data jenis
        return view('laporan.index', compact('jenis'));
    }

    /**
     * filter
     */
    public function filter(Request $request): View
    {
        // validasi form
        $request->validate([
            'jenis'  => 'required',
            'tgl_awal'  => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal'
        ], [
            'jenis.required'        => 'Jenis dokumen tidak boleh kosong.',
            'tgl_awal.required'        => 'Tanggal awal tidak boleh kosong.',
            'tgl_awal.date'            => 'Tanggal awal harus berupa tanggal yang valid.',
            'tgl_akhir.required'       => 'Tanggal akhir tidak boleh kosong.',
            'tgl_akhir.date'           => 'Tanggal akhir harus berupa tanggal yang valid.',
            'tgl_akhir.after_or_equal' => 'Tanggal akhir harus berupa tanggal setelah atau sama dengan tanggal awal.'
        ]);

        // data filter
        $jenisId = $request->jenis;
        $tglAwal    = $request->tgl_awal;
        $tglAkhir   = $request->tgl_akhir;

        if ($jenisId == 'Semua') {
            // menampilkan data berdasarkan filter tanggal dokumen
            $arsip = Arsip::select('id', 'nama_surat', 'nomor_surat', 'tanggal_surat', 'jenis_id')->with('jenis:id,nama')
                ->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir])
                ->orderBy('tanggal_surat', 'asc')
                ->get();
        } else {
            // menampilkan data berdasarkan filter jenis dan tanggal dokumen
            $arsip = Arsip::select('id', 'nama_surat', 'nomor_surat', 'tanggal_surat', 'jenis_id')->with('jenis:id,nama')
                ->where('jenis_id', $jenisId)
                ->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir])
                ->orderBy('tanggal_surat', 'asc')
                ->get();
        }

        // ambil data jenis untuk form select
        $jenis = Jenis::get(['id', 'nama']);

        // ambil data nama jenis untuk judul laporan
        $fieldJenis = Jenis::select('nama')
            ->where('id', $jenisId)
            ->first();

        // tampilkan data ke view
        return view('laporan.index', compact('arsip', 'jenis', 'fieldJenis'));
    }

    /**
     * print (PDF)
     */
    public function print(Request $request)
    {
        // data filter
        $jenisId = $request->jenis;
        $tglAwal    = $request->tgl_awal;
        $tglAkhir   = $request->tgl_akhir;

        if ($jenisId == 'Semua') {
            // menampilkan data berdasarkan filter tanggal dokumen
            $arsip = Arsip::select('id', 'nama_surat', 'nomor_surat', 'tanggal_surat', 'jenis_id')->with('jenis:id,nama')
                ->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir])
                ->orderBy('tanggal_surat', 'asc')
                ->get();
        } else {
            // menampilkan data berdasarkan filter jenis dan tanggal dokumen
            $arsip = Arsip::select('id', 'nama_surat', 'nomor_surat', 'tanggal_surat', 'jenis_id')->with('jenis:id,nama')
                ->where('jenis_id', $jenisId)
                ->whereBetween('tanggal_surat', [$tglAwal, $tglAkhir])
                ->orderBy('tanggal_surat', 'asc')
                ->get();
        }

        // ambil data nama jenis untuk judul laporan
        $fieldJenis = Jenis::select('nama')
            ->where('id', $jenisId)
            ->first();

        // load view PDF
        $pdf = Pdf::loadview('laporan.print', compact('arsip', 'fieldJenis'))->setPaper('a4', 'landscape');
        // tampilkan ke browser
        return $pdf->stream('Laporan-Data-Arsip-dokumen.pdf');
    }
}