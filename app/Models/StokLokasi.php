<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokLokasi extends Model
{
    use HasFactory;

    protected $table = 'stok_lokasi';

    protected $fillable = [
        'id_bahan',
        'id_lokasi',
        'jumlah',
        'stok_minimum',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'stok_minimum' => 'decimal:2',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    /**
     * Mengembalikan status stok: 'Kritis', 'Menipis', atau 'Aman'
     */
    public function getStatusStokAttribute(): string
    {
        $jumlah = (float) $this->jumlah;
        $min = (float) $this->stok_minimum;

        if ($jumlah <= 0 || ($min > 0 && $jumlah < ($min * 0.5))) {
            return 'Kritis';
        }

        if ($jumlah <= $min) {
            return 'Menipis';
        }

        return 'Aman';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_stok) {
            'Kritis' => 'badge-danger',
            'Menipis' => 'badge-warn',
            'Aman' => 'badge-ok',
            default => 'badge-muted',
        };
    }
}
