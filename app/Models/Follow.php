<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Follow extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'follower_id',
        'followed_id',
        'followed_at',
    ];

    protected $casts = [
        'followed_at' => 'datetime',
    ];

    // Relación con el usuario que sigue
    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    // Relación con el usuario seguido (autor)
    public function followed()
    {
        return $this->belongsTo(User::class, 'followed_id');
    }

    // Scope para obtener seguidores de un autor
    public function scopeFollowersOf($query, $authorId)
    {
        return $query->where('followed_id', $authorId);
    }

    // Scope para obtener autores que sigue un usuario
    public function scopeFollowingBy($query, $userId)
    {
        return $query->where('follower_id', $userId);
    }
}
