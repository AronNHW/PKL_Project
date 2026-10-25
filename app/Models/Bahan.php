<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'id_kategori',
        'satuan',
        'spesifikasi',
        'harga_estimasi',
        'is_aktif',
    ];

    protected $casts = [
        'harga_estimasi' => 'decimal:2',
        'is_aktif' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function stokLokasi(): HasMany
    {
        return $this->hasMany(StokLokasi::class, 'id_bahan');
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'id_bahan');
    }

    public function parameterBahan(): HasMany
    {
        return $this->hasMany(ParameterBahan::class, 'id_bahan');
    }

    public function isSukuCadang(): bool
    {
        return $this->kategori && str_contains(strtolower($this->kategori->nama_kategori), 'suku cadang');
    }

    public function getStokTotalAttribute(): float
    {
        return (float) $this->stokLokasi()->sum('jumlah');
    }

    public function getStokDiLokasi(int $idLokasi): float
    {
        $stok = $this->stokLokasi()->where('id_lokasi', $idLokasi)->first();
        return $stok ? (float) $stok->jumlah : 0.0;
    }
}
