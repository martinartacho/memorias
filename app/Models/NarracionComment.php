<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NarracionComment extends Model
{
    protected $fillable = [
        'narracion_id',
        'subscriber_id',
        'contenido',
    ];

    public function narracion()
    {
        return $this->belongsTo(Narracion::class);
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Alias público del suscriptor: muestra la parte antes del @
     * Ejemplo: "pepemartin" de "pepemartin@gmail.com"
     */
    public function getAliasAttribute(): string
    {
        return explode('@', $this->subscriber->email ?? 'lector')[0];
    }
}
