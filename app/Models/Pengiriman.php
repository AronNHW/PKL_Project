<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengiriman';

    protected $fillable = [
        'nomor_surat_jalan',
        'id_permintaan',
        'id_lokasi_asal',
        'id_lokasi_tujuan',
        'tanggal_kirim',
        'tanggal_terima',
        'status',
        'ekspedisi_sopir',
        'catatan_kirim',
        'catatan_terima',
        'id_user_pengirim',
        'id_user_penerima',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'tanggal_terima' => 'date',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class, 'id_permintaan');
    }

    public function lokasiAsal(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi_asal');
    }

    public function lokasiTujuan(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi_tujuan');
    }

    public function userPengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_pengirim');
    }

    public function userPenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_penerima');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPengiriman::class, 'id_pengiriman');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'dikirim' => 'badge-warn',
            'diterima' => 'badge-ok',
            'selisih' => 'badge-danger',
            default => 'badge-muted',
        };
    }
}
