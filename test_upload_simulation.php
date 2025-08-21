<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\ArsipController;
use Illuminate\Support\Facades\Log;

// Clear log untuk test baru
file_put_contents(storage_path('logs/laravel.log'), '');
Log::info('=== MULAI TEST UPLOAD DOKUMEN ===');

// Simulate uploaded file
$originalFile = base_path('DAFTAR HADIR RAPAT GURU SMK.pdf');
$tempFile = sys_get_temp_dir() . '/test_upload.pdf';
copy($originalFile, $tempFile);

$uploadedFile = new UploadedFile(
    $tempFile,
    'DAFTAR HADIR RAPAT GURU SMK.pdf',
    'application/pdf',
    null,
    true
);

// Create mock request
$request = Request::create('/arsip', 'POST', [
    'nama_dokumen' => 'Test Dokumen Kepegawaian',
    'nomor_dokumen' => 'TEST/KPG/001/2025',
    'tanggal_dokumen' => '2025-08-21',
    'jenis' => '13' // ID jenis Kepegawaian sebagai fallback
]);

$request->files->set('dokumen_elektronik', $uploadedFile);

try {
    // Panggil controller store method (hanya bagian processing, skip validation)
    Log::info('Request data: ', $request->all());
    Log::info('File info: ', [
        'name' => $uploadedFile->getClientOriginalName(),
        'size' => $uploadedFile->getSize(),
        'mime' => $uploadedFile->getMimeType()
    ]);
    
    echo "Test request dibuat. Silakan cek storage/logs/laravel.log untuk melihat log.\n";
    echo "Untuk test penuh, silakan upload melalui browser di http://127.0.0.1:8008\n";
    
} catch (\Exception $e) {
    Log::error('Error: ' . $e->getMessage());
    echo "Error: " . $e->getMessage() . "\n";
}
