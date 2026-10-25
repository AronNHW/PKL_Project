<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi';

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
        'tipe',
        'alamat',
        'telepon',
        'penanggung_jawab',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function stokLokasi(): HasMany
    {
        return $this->hasMany(StokLokasi::class, 'id_lokasi');
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'id_lokasi');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_lokasi');
    }

    public function mesin(): HasMany
    {
        return $this->hasMany(Mesin::class, 'id_lokasi');
    }

    public function isPusat(): bool
    {
        return $this->tipe === 'pusat';
    }
}
