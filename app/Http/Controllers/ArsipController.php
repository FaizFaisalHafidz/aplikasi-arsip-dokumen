<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\Jenis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ArsipController extends Controller
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
            $arsip = Arsip::select('id', 'nama_surat', 'nomor_surat', 'tanggal_surat', 'jenis_id')->with('jenis:id,nama')
                ->whereAny(['nama_surat', 'nomor_surat'], 'LIKE', '%' . $request->search . '%')
                ->paginate($pagination)
                ->withQueryString();
        } else {
            // menampilkan semua data
            $arsip = Arsip::select('id', 'nama_surat', 'nomor_surat', 'tanggal_surat', 'jenis_id')->with('jenis:id,nama')
                ->latest()
                ->paginate($pagination);
        }

        // tampilkan data ke view
        return view('arsip.index', compact('arsip'))->with('i', ($request->input('page', 1) - 1) * $pagination);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // tampilkan form tambah data arsip
        return view('arsip.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Log::info('=== MULAI PROSES STORE ARSIP ===');
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->url());
        Log::info('Request data: ', $request->all());
        Log::info('Request files: ', $request->allFiles());
        
        try {
            // validasi form
            Log::info('Mulai validasi form...');
            $request->validate([
                'nama_dokumen'         => 'required',
                'nomor_dokumen'        => 'required|unique:arsip,nomor_surat',
                'tanggal_dokumen'      => 'required|date',
                'dokumen_elektronik' => 'required|file|mimes:pdf|max:5120'
            ], [
                'nama_dokumen.required'         => 'Nama dokumen tidak boleh kosong.',
                'nomor_dokumen.required'        => 'Nomor dokumen tidak boleh kosong.',
                'nomor_dokumen.unique'          => 'Nomor dokumen sudah ada.',
                'tanggal_dokumen.required'      => 'Tanggal dokumen tidak boleh kosong.',
                'tanggal_dokumen.date'          => 'Tanggal dokumen harus berupa tanggal yang valid.',
                'dokumen_elektronik.required' => 'Dokumen elektronik tidak boleh kosong.',
                'dokumen_elektronik.file'     => 'Dokumen elektronik harus berupa file.',
                'dokumen_elektronik.mimes'    => 'Dokumen elektronik harus berupa file dengan jenis: pdf.',
                'dokumen_elektronik.max'      => 'Dokumen elektronik tidak boleh lebih besar dari 5 MB.'
            ]);
            Log::info('Validasi form berhasil');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validasi gagal: ', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error unexpected dalam validasi: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with(['error' => 'Terjadi kesalahan dalam validasi: ' . $e->getMessage()])->withInput();
        }

                // upload file
        Log::info('Mulai upload file...');
        $file = $request->file('dokumen_elektronik');
        $originalFileName = $file->getClientOriginalName();
        
        // Buat nama file unik untuk menghindari duplikasi
        $fileName = time() . '_' . $originalFileName;
        
        $file->storeAs('public/dokumen', $fileName);
        Log::info('File berhasil diupload: ' . $fileName);

        Log::info('=== MULAI KLASIFIKASI DOKUMEN ===');
        Log::info('File uploaded: ' . $fileName);
        Log::info('Original filename: ' . $originalFileName);

        // Jalankan script Python untuk klasifikasi dokumen
        $pythonScript = base_path('python_scripts/classify_document_new.py');
        $uploadedFilePath = storage_path('app/public/dokumen/' . $fileName);
        $command = "python3 $pythonScript '$uploadedFilePath' '$originalFileName'";
        $output = null;
        $result = null;
        
        Log::info('Command: ' . $command);
        exec($command, $output, $result);
        
        Log::info('Python result code: ' . $result);
        Log::info('Python output: ' . implode('', $output));

        // Default values dari input user
        $nama_dokumen = $request->nama_dokumen;
        $nomor_dokumen = $request->nomor_dokumen;
        $tanggal_dokumen = $request->tanggal_dokumen;
        
        // Default jenis_id jika gagal klasifikasi (gunakan jenis pertama sebagai default)
        $defaultJenis = \App\Models\Jenis::first();
        $jenis_id = $defaultJenis ? $defaultJenis->id : 1; // fallback ke ID 1
        Log::info('Default jenis_id (jika klasifikasi gagal): ' . $jenis_id);
        
        if ($result === 0 && !empty($output)) {
            $json = json_decode(implode('', $output), true);
            Log::info('JSON decoded: ' . json_encode($json));
            
            // Gunakan informasi yang diekstrak dari PDF jika tersedia
            if (isset($json['extraction_success']) && $json['extraction_success']) {
                Log::info('Berhasil ekstraksi dari PDF');
                
                // Nama dokumen selalu dari nama file asli
                if (!empty($json['nama_dokumen'])) {
                    $nama_dokumen = $json['nama_dokumen'];
                    Log::info('Menggunakan nama dokumen dari nama file: ' . $nama_dokumen);
                }
                
                // Nomor dokumen dari PDF jika ada, jika tidak gunakan input
                if (!empty($json['nomor_dokumen'])) {
                    $nomor_dokumen = $json['nomor_dokumen'];
                    Log::info('Menggunakan nomor dokumen dari PDF: ' . $nomor_dokumen);
                } else {
                    Log::info('Nomor dokumen tidak ditemukan di PDF, menggunakan input: ' . $nomor_dokumen);
                }
                
                // Tanggal dokumen dari PDF jika ada, jika tidak gunakan input
                if (!empty($json['tanggal_dokumen'])) {
                    $tanggal_dokumen = $json['tanggal_dokumen'];
                    Log::info('Menggunakan tanggal dokumen dari PDF: ' . $tanggal_dokumen);
                } else {
                    Log::info('Tanggal dokumen tidak ditemukan di PDF, menggunakan input: ' . $tanggal_dokumen);
                }
            } else {
                Log::info('Menggunakan data dari input form user');
            }
            
            // Klasifikasi jenis dokumen
            if (isset($json['predicted_category_id'])) {
                Log::info('Predicted category: ' . $json['predicted_category_id']);
                
                // Cari ID jenis berdasarkan nama kategori
                $jenis = \App\Models\Jenis::where('nama', $json['predicted_category_id'])->first();
                if ($jenis) {
                    $jenis_id = $jenis->id;
                    Log::info('Found jenis in database: ' . $jenis->nama . ' (ID: ' . $jenis->id . ')');
                } else {
                    Log::warning('Jenis not found in database for: ' . $json['predicted_category_id']);
                }
            } else {
                Log::warning('predicted_category_id not found in JSON response');
            }
        } else {
            Log::warning('Python script failed or empty output. Result: ' . $result . ', Output: ' . implode('', $output));
        }
        
        Log::info('Final jenis_id yang akan disimpan: ' . $jenis_id);
        Log::info('Final data yang akan disimpan:', [
            'nama_dokumen' => $nama_dokumen,
            'nomor_dokumen' => $nomor_dokumen, 
            'tanggal_dokumen' => $tanggal_dokumen
        ]);
        Log::info('=== SELESAI KLASIFIKASI DOKUMEN ===');

        // simpan data
        Log::info('=== MULAI SIMPAN DATA ARSIP ===');
        Log::info('Data yang akan disimpan:', [
            'nama_surat' => $nama_dokumen,
            'nomor_surat' => $nomor_dokumen,
            'tanggal_surat' => $tanggal_dokumen,
            'jenis_id' => $jenis_id,
            'dokumen_elektronik' => $fileName
        ]);
        
        try {
            $arsip = Arsip::create([
                'nama_surat'         => $nama_dokumen,
                'nomor_surat'        => $nomor_dokumen,
                'tanggal_surat'      => $tanggal_dokumen,
                'jenis_id'           => $jenis_id,
                'dokumen_elektronik' => $fileName
            ]);
            
            Log::info('Data arsip berhasil disimpan dengan ID: ' . $arsip->id);
            Log::info('=== SELESAI SIMPAN DATA ARSIP ===');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data arsip: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }

        // redirect ke halaman index dan tampilkan pesan berhasil simpan data
        Log::info('Redirect ke halaman index...');
        return redirect()->route('arsip.index')->with(['success' => 'Data arsip dokumen berhasil disimpan.']);
    }    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // dapatkan data berdasarkan "id" dengan relasi jenis
        $arsip = Arsip::with('jenis')->findOrFail($id);

        // tampilkan form detail data
        return view('arsip.show', compact('arsip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // dapatkan data berdasarakan "id"
        $arsip = Arsip::findOrFail($id);

        // tampilkan form ubah data
        return view('arsip.edit', compact('arsip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validasi form
        $request->validate([
            'nama_dokumen'         => 'required',
            'nomor_dokumen'        => 'required|unique:arsip,nomor_surat,' . $id,
            'tanggal_dokumen'      => 'required|date',
            'dokumen_elektronik'   => 'nullable|file|mimes:pdf|max:5120'
        ], [
            'nama_dokumen.required'      => 'Nama dokumen tidak boleh kosong.',
            'nomor_dokumen.required'     => 'Nomor dokumen tidak boleh kosong.',
            'nomor_dokumen.unique'       => 'Nomor dokumen sudah ada.',
            'tanggal_dokumen.required'   => 'Tanggal dokumen tidak boleh kosong.',
            'tanggal_dokumen.date'       => 'Tanggal dokumen harus berupa tanggal yang valid.',
            'dokumen_elektronik.file'    => 'Dokumen elektronik harus berupa file.',
            'dokumen_elektronik.mimes'   => 'Dokumen elektronik harus berupa file dengan jenis: pdf.',
            'dokumen_elektronik.max'     => 'Dokumen elektronik tidak boleh lebih besar dari 5 MB.'
        ]);

        // dapatkan data berdasarakan "id"
        $arsip = Arsip::findOrFail($id);

        // jika "dokumen_elektronik" diubah
        if ($request->hasFile('dokumen_elektronik')) {

            // upload file baru
            $file = $request->file('dokumen_elektronik');
            $originalFileName = $file->getClientOriginalName();
            $fileName = time() . '_' . $originalFileName;
            $file->storeAs('public/dokumen', $fileName);

            // Jalankan script Python untuk klasifikasi dokumen
            $pythonScript = base_path('python_scripts/classify_document_new.py');
            $uploadedFilePath = storage_path('app/public/dokumen/' . $fileName);
            $command = "python3 $pythonScript '$uploadedFilePath' '$originalFileName'";
            $output = null;
            $result = null;
            exec($command, $output, $result);

            // Default jenis_id jika gagal klasifikasi (gunakan jenis yang sudah ada)
            $jenis_id = $arsip->jenis_id; // fallback ke jenis yang sudah ada
            if ($result === 0 && !empty($output)) {
                $json = json_decode(implode('', $output), true);
                if (isset($json['predicted_category_id'])) {
                    // Cari ID jenis berdasarkan nama kategori
                    $jenis = \App\Models\Jenis::where('nama', $json['predicted_category_id'])->first();
                    if ($jenis) {
                        $jenis_id = $jenis->id;
                    }
                }
            }

            // hapus file lama
            Storage::delete('public/dokumen/' . $arsip->dokumen_elektronik);

            // ubah data dengan file baru
            $arsip->update([
                'nama_surat'         => $request->nama_dokumen,
                'nomor_surat'        => $request->nomor_dokumen,
                'tanggal_surat'      => $request->tanggal_dokumen,
                'jenis_id'           => $jenis_id,
                'dokumen_elektronik' => $fileName
            ]);
        }
        // jika "dokumen_elektronik" tidak diubah
        else {
            // ubah data tanpa mengubah file dan jenis (tetap gunakan jenis yang sudah ada)
            $arsip->update([
                'nama_surat'    => $request->nama_dokumen,
                'nomor_surat'   => $request->nomor_dokumen,
                'tanggal_surat' => $request->tanggal_dokumen
            ]);
        }

        // redirect ke halaman index dan tampilkan pesan berhasil ubah data
        return redirect()->route('arsip.index')->with(['success' => 'Data arsip dokumen berhasil diubah.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        // dapatkan data berdasarakan "id"
        $arsip = Arsip::findOrFail($id);

        // hapus file
        Storage::delete('public/dokumen/' . $arsip->dokumen_elektronik);

        // hapus data
        $arsip->delete();

        // redirect ke halaman index dan tampilkan pesan berhasil hapus data
        return redirect()->route('arsip.index')->with(['success' => 'Data arsip dokumen berhasil dihapus.']);
    }
}