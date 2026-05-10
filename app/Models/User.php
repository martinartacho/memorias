<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'follower_approval',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'follower_approval' => 'boolean',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is editor
     */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if user is lector (reader)
     */
    public function isLector(): bool
    {
        return $this->role === 'lector';
    }

    /**
     * Check if user can access admin panel
     */
    public function canAccessAdmin(): bool
    {
        return in_array($this->role, ['admin', 'editor']);
    }

    // Relación: narraciones que ha creado
    public function narraciones()
    {
        return $this->hasMany(Narracion::class);
    }

    // Relación: usuarios que este usuario sigue
    public function following()
    {
        return $this->hasMany(Follow::class, 'follower_id');
    }

    // Relación: usuarios que siguen a este autor
    public function followers()
    {
        return $this->hasMany(Follow::class, 'followed_id');
    }

    // Scope para obtener autores que sigue este usuario
    public function scopeFollowingAuthors($query)
    {
        return $query->whereHas('following');
    }

    // Scope para obtener autores con seguidores
    public function scopeWithFollowers($query)
    {
        return $query->whereHas('followers');
    }
}
