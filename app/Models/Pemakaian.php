<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemakaian extends Model
{
    use HasFactory;

    protected $table = 'pemakaian';

    protected $fillable = [
        'nomor_pemakaian',
        'id_lokasi',
        'tanggal_pemakaian',
        'nomor_spk',
        'keperluan',
        'catatan',
        'id_user',
    ];

    protected $casts = [
        'tanggal_pemakaian' => 'date',
    ];

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPemakaian::class, 'id_pemakaian');
    }
}
