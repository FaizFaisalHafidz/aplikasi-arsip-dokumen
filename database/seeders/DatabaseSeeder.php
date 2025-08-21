<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed Users
        DB::table('users')->insert([
            [
                'nama_user' => 'Novia Fitriyanti',
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'Admin',
                'created_at'=> now(),
                'updated_at'=> now()
            ], 
            [
                'nama_user' => 'Fitriyanti',
                'username' => 'user',
                'password' => Hash::make('user'),
                'role' => 'User',
                'created_at'=> now(),
                'updated_at'=> now()
            ]
        ]);

        // Seed Jenis Dokumen
        $this->call([
            JenisSeeder::class,
        ]);
    }
}
