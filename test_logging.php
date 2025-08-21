<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Log;

// Test logging
Log::info('=== TEST LOGGING ===');
Log::info('Test logging berfungsi dengan baik');

// Simulate script Python call
$filePath = storage_path('app/public/dokumen/test_dokumen_kepegawaian.pdf');
$pythonScript = base_path('python_scripts/classify_document.py');
$command = "python3 $pythonScript '$filePath'";
$output = null;
$result = null;

Log::info('Command: ' . $command);
exec($command, $output, $result);

Log::info('Python result code: ' . $result);
Log::info('Python output: ' . implode('', $output));

if ($result === 0 && !empty($output)) {
    $json = json_decode(implode('', $output), true);
    Log::info('JSON decoded: ' . json_encode($json));
    
    if (isset($json['predicted_category_id'])) {
        Log::info('Predicted category: ' . $json['predicted_category_id']);
        
        $jenis = \App\Models\Jenis::where('nama', $json['predicted_category_id'])->first();
        if ($jenis) {
            Log::info('Found jenis in database: ' . $jenis->nama . ' (ID: ' . $jenis->id . ')');
        } else {
            Log::warning('Jenis not found in database for: ' . $json['predicted_category_id']);
        }
    }
}

echo "Test logging selesai. Cek file storage/logs/laravel.log\n";
