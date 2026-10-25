<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesin extends Model
{
    use HasFactory;

    protected $table = 'mesin';

    protected $fillable = [
        'kode_mesin',
        'nama_mesin',
        'tipe_mesin',
        'id_lokasi',
        'status',
        'keterangan',
    ];

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'id_mesin');
    }

    public function riwayatPerbaikan(): HasMany
    {
        return $this->hasMany(RiwayatPerbaikan::class, 'id_mesin');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'badge-ok',
            'perbaikan' => 'badge-warn',
            'rusak' => 'badge-danger',
            default => 'badge-muted',
        };
    }
}
