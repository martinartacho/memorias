<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Narracion extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'contenido',
        'slug',
        'fecha_publicacion',
        'estado',
        'user_id',
        'orden',
        'permiso_lectura',
        'count_feedback',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
    ];

    public function scopePublicado($query)
    {
        return $query->where('estado', 'publicado');
    }

    public function scopeOrderByFecha($query, $direction = 'desc')
    {
        return $query->orderBy('fecha_publicacion', $direction);
    }

    public function getExcerptAttribute()
    {
        return str_limit(strip_tags($this->contenido), 200);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeConPermisoLectura($query, $user = null)
    {
        if (!$user) {
            return $query->where('permiso_lectura', 'publico');
        }
        
        return $query->where(function($q) use ($user) {
            $q->where('permiso_lectura', 'publico')
              ->orWhere('permiso_lectura', 'seguidores')
              ->orWhere('user_id', $user->id);
        });
    }

    public function scopeByOrden($query, $direction = 'asc')
    {
        return $query->orderBy('orden', $direction);
    }
}
