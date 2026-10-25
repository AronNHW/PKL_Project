<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanKerusakan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kerusakan';

    protected $fillable = [
        'nomor_laporan',
        'id_mesin',
        'id_lokasi',
        'tanggal_lapor',
        'gejala',
        'kronologi',
        'status',
        'foto_kerusakan',
        'id_user_pelapor',
    ];

    protected $casts = [
        'tanggal_lapor' => 'date',
    ];

    public function mesin(): BelongsTo
    {
        return $this->belongsTo(Mesin::class, 'id_mesin');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function userPelapor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_pelapor');
    }

    public function detailKerusakan(): HasMany
    {
        return $this->hasMany(DetailKerusakan::class, 'id_laporan_kerusakan');
    }

    public function riwayatPerbaikan(): HasMany
    {
        return $this->hasMany(RiwayatPerbaikan::class, 'id_laporan_kerusakan');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'dilaporkan' => 'badge-danger',
            'diperiksa' => 'badge-warn',
            'diperbaiki' => 'badge-accent',
            'selesai' => 'badge-ok',
            default => 'badge-muted',
        };
    }
}
