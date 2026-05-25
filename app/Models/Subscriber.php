<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = [
        'email',
        'token',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Comprueba si el token de la cookie corresponde a un suscriptor verificado.
     */
    public static function tokenIsValid(?string $token): bool
    {
        if (!$token) return false;

        return static::where('token', $token)
            ->whereNotNull('verified_at')
            ->exists();
    }
}
