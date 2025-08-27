<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            // Mengubah panjang kolom nama_surat dari varchar(255) ke varchar(100)
            $table->string('nama_surat', 100)->change();
            // Mengubah panjang kolom nomor_surat dari varchar(255) ke varchar(100)
            $table->string('nomor_surat', 100)->change();
            // Mengubah panjang kolom dokumen_elektronik dari varchar(255) ke varchar(100)
            $table->string('dokumen_elektronik', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            // Kembalikan ke varchar(255)
            $table->string('nama_surat', 255)->change();
            $table->string('nomor_surat', 255)->change();
            $table->string('dokumen_elektronik', 255)->change();
        });
    }
};
