<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPermintaan extends Model
{
    use HasFactory;

    protected $table = 'detail_permintaan';

    protected $fillable = [
        'id_permintaan',
        'id_bahan',
        'jumlah_minta',
        'jumlah_disetujui',
    ];

    protected $casts = [
        'jumlah_minta' => 'decimal:2',
        'jumlah_disetujui' => 'decimal:2',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}
