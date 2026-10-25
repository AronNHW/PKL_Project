<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaranPembelian extends Model
{
    use HasFactory;

    protected $table = 'saran_pembelian';

    protected $fillable = [
        'id_bahan',
        'jumlah_saran',
        'estimasi_biaya',
        'status',
    ];

    protected $casts = [
        'jumlah_saran' => 'decimal:2',
        'estimasi_biaya' => 'decimal:2',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}
