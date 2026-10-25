<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianBulanan extends Model
{
    use HasFactory;

    protected $table = 'pemakaian_bulanan';

    protected $fillable = [
        'id_bahan',
        'id_lokasi',
        'tahun',
        'bulan',
        'total_pemakaian',
    ];

    protected $casts = [
        'total_pemakaian' => 'decimal:2',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
