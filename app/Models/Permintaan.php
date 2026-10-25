<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;

    protected $table = 'permintaan';

    protected $fillable = [
        'nomor_permintaan',
        'id_lokasi_pemohon',
        'tanggal_permintaan',
        'status',
        'catatan',
        'alasan_penolakan',
        'id_user_pemohon',
        'id_user_penyetuju',
        'disetujui_pada',
    ];

    protected $casts = [
        'tanggal_permintaan' => 'date',
        'disetujui_pada' => 'datetime',
    ];

    public function lokasiPemohon(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi_pemohon');
    }

    public function userPemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_pemohon');
    }

    public function userPenyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_penyetuju');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPermintaan::class, 'id_permintaan');
    }

    public function pengiriman(): HasMany
    {
        return $this->hasMany(Pengiriman::class, 'id_permintaan');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'disetujui' => 'badge-ok',
            'menunggu' => 'badge-warn',
            'ditolak' => 'badge-danger',
            'diproses' => 'badge-accent',
            'selesai' => 'badge-ok',
            default => 'badge-muted',
        };
    }
}
