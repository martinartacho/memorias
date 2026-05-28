<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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
        'count_dislikes',
        'count_read',
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
    ];

    /** Solo estado=publicado (admin, featured, etc.) */
    public function scopePublicado($query)
    {
        return $query->where('estado', 'publicado');
    }

    /** Publicado + Promocion: lo visible al público (con distintos niveles) */
    public function scopeVisible($query)
    {
        return $query->whereIn('estado', ['publicado', 'promocion']);
    }

    public function scopeOrderByFecha($query, $direction = 'desc')
    {
        return $query->orderBy('fecha_publicacion', $direction);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->contenido), 200);
    }

    /** Número de palabras del contenido (sin HTML, compatible con español) */
    public function getPalabraCountAttribute(): int
    {
        $texto = strip_tags($this->contenido);
        $partes = preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY);
        return $partes ? count($partes) : 0;
    }

    /** Devuelve true si el estado es promocion */
    public function esPromocion(): bool
    {
        return $this->estado === 'promocion';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Comentarios de suscriptores */
    public function comments()
    {
        return $this->hasMany(NarracionComment::class)->latest();
    }

    /** Nodos de líneas argumentales en que participa esta narración */
    public function nodosLinea()
    {
        return $this->hasMany(LineaNarracion::class);
    }

    /** Líneas argumentales a las que pertenece */
    public function lineas()
    {
        return $this->belongsToMany(Linea::class, 'linea_narracion')
                    ->withPivot(['etiqueta', 'orden']);
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
