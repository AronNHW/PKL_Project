<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiStok extends Model
{
    use HasFactory;

    protected $table = 'mutasi_stok';

    public $timestamps = false;

    protected $fillable = [
        'id_bahan',
        'id_lokasi',
        'jenis',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'referensi_tipe',
        'referensi_id',
        'keterangan',
        'id_user',
        'created_at',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'stok_sebelum' => 'decimal:2',
        'stok_sesudah' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'masuk' => 'Masuk',
            'keluar' => 'Keluar',
            'kirim' => 'Kirim Cabang',
            'terima' => 'Terima Distribusi',
            'penyesuaian' => 'Penyesuaian (Opname)',
            default => ucfirst($this->jenis),
        };
    }

    public function getJenisBadgeClassAttribute(): string
    {
        return match ($this->jenis) {
            'masuk', 'terima' => 'badge-ok',
            'keluar', 'kirim' => 'badge-warn',
            'penyesuaian' => 'badge-accent',
            default => 'badge-muted',
        };
    }
}
