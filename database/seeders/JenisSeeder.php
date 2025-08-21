<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jenis;

class JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data jenis dokumen sesuai dengan kategori klasifikasi
        $jenisData = [
            [
                'nama' => 'Administrasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Akademik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Evaluasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kepegawaian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Keuangan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Kurikulum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Sarana dan Prasarana',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Siswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data menggunakan insert() untuk efisiensi
        Jenis::insert($jenisData);

        // Atau bisa juga menggunakan create() jika ingin trigger events
        // foreach ($jenisData as $jenis) {
        //     Jenis::create($jenis);
        // }

        // Output informasi
        $this->command->info('Berhasil menambahkan ' . count($jenisData) . ' jenis dokumen.');
    }
}
