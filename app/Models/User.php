<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'id_lokasi',
        'telepon',
        'is_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_aktif' => 'boolean',
        ];
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdminPusat(): bool
    {
        return $this->role === 'admin_pusat';
    }

    public function isAdminCabang(): bool
    {
        return $this->role === 'admin_cabang';
    }

    public function isPusatOrOwner(): bool
    {
        return in_array($this->role, ['owner', 'admin_pusat']);
    }

    public function canAccessLocation(?int $lokasiId): bool
    {
        if ($this->isPusatOrOwner()) {
            return true;
        }

        return (int) $this->id_lokasi === (int) $lokasiId;
    }
}
