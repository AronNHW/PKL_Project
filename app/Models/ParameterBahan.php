<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParameterBahan extends Model
{
    use HasFactory;

    protected $table = 'parameter_bahan';

    protected $fillable = [
        'id_bahan',
        'id_lokasi',
        'lead_time_hari',
        'safety_factor',
        'alpha',
        'rop',
        'safety_stock',
    ];

    protected $casts = [
        'safety_factor' => 'decimal:2',
        'alpha' => 'decimal:2',
        'rop' => 'decimal:2',
        'safety_stock' => 'decimal:2',
    ];

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
