<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaranDistribusi extends Model
{
    use HasFactory;

    protected $table = 'saran_distribusi';

    protected $fillable = [
        'id_bahan',
        'id_lokasi_tujuan',
        'jumlah_saran',
        'status',
    ];

    protected $casts = [
        'jumlah_saran' => 'decimal:2',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function lokasiTujuan(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi_tujuan');
    }
}
