<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_perbaikan';

    protected $fillable = [
        'id_laporan_kerusakan',
        'id_mesin',
        'tanggal_selesai',
        'tindakan',
        'teknisi',
        'catatan',
        'id_user',
    ];

    protected $casts = [
        'tanggal_selesai' => 'date',
    ];

    public function laporanKerusakan(): BelongsTo
    {
        return $this->belongsTo(LaporanKerusakan::class, 'id_laporan_kerusakan');
    }

    public function mesin(): BelongsTo
    {
        return $this->belongsTo(Mesin::class, 'id_mesin');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
