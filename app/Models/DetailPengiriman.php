<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPengiriman extends Model
{
    use HasFactory;

    protected $table = 'detail_pengiriman';

    protected $fillable = [
        'id_pengiriman',
        'id_bahan',
        'jumlah_kirim',
        'jumlah_terima',
        'selisih',
        'catatan_selisih',
    ];

    protected $casts = [
        'jumlah_kirim' => 'decimal:2',
        'jumlah_terima' => 'decimal:2',
        'selisih' => 'decimal:2',
    ];

    public function pengiriman(): BelongsTo
    {
        return $this->belongsTo(Pengiriman::class, 'id_pengiriman');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}
