<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPemakaian extends Model
{
    use HasFactory;

    protected $table = 'detail_pemakaian';

    protected $fillable = [
        'id_pemakaian',
        'id_bahan',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function pemakaian(): BelongsTo
    {
        return $this->belongsTo(Pemakaian::class, 'id_pemakaian');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}
