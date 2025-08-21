<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arsip extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'arsip';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_surat',
        'nomor_surat',
        'tanggal_surat',
        'jenis_id',
        'dokumen_elektronik',
    ];

    /**
     * Relasi Jenis
     */
    public function jenis(): BelongsTo
    {
        return $this->belongsTo(Jenis::class);
    }
}
