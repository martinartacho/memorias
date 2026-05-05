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
}
