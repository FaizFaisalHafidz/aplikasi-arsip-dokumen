<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test klasifikasi
$filePath = storage_path('app/public/dokumen/test_dokumen_kepegawaian.pdf');
$pythonScript = base_path('python_scripts/classify_document.py');
$command = "python3 $pythonScript '$filePath'";
$output = null;
$result = null;
exec($command, $output, $result);

echo "Command: $command\n";
echo "Result code: $result\n";
echo "Output: " . implode('', $output) . "\n";

if ($result === 0 && !empty($output)) {
    $json = json_decode(implode('', $output), true);
    if (isset($json['predicted_category_id'])) {
        $jenis = \App\Models\Jenis::where('nama', $json['predicted_category_id'])->first();
        if ($jenis) {
            echo "Kategori ditemukan: " . $jenis->nama . " (ID: " . $jenis->id . ")\n";
        } else {
            echo "Kategori tidak ditemukan di database: " . $json['predicted_category_id'] . "\n";
        }
    }
} else {
    echo "Gagal menjalankan klasifikasi\n";
}
