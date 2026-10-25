<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailKerusakan extends Model
{
    use HasFactory;

    protected $table = 'detail_kerusakan';

    protected $fillable = [
        'id_laporan_kerusakan',
        'id_bahan',
        'jumlah',
        'foto_suku_cadang',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    public function laporanKerusakan(): BelongsTo
    {
        return $this->belongsTo(LaporanKerusakan::class, 'id_laporan_kerusakan');
    }

    public function sukuCadang(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}
