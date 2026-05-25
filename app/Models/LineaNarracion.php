<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaNarracion extends Model
{
    protected $table = 'linea_narracion';

    protected $fillable = ['linea_id', 'narracion_id', 'etiqueta', 'orden'];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function linea()
    {
        return $this->belongsTo(Linea::class);
    }

    public function narracion()
    {
        return $this->belongsTo(Narracion::class);
    }

    /** Conexiones que salen de este nodo (hacia qué nodos puede ir el lector) */
    public function conexionesSalientes()
    {
        return $this->hasMany(LineaConexion::class, 'from_id')->orderBy('orden');
    }

    /** Conexiones que llegan a este nodo (desde qué nodos se puede venir) */
    public function conexionesEntrantes()
    {
        return $this->hasMany(LineaConexion::class, 'to_id');
    }

    /** Nodos siguientes (destinos accesibles desde este nodo) */
    public function siguientes()
    {
        return $this->conexionesSalientes->map(fn($c) => $c->hasta)->filter();
    }

    /** Nodos anteriores (orígenes que apuntan a este nodo) */
    public function anteriores()
    {
        return $this->conexionesEntrantes->map(fn($c) => $c->desde)->filter();
    }

    public function esRaiz(): bool
    {
        return $this->conexionesEntrantes()->doesntExist();
    }

    public function esHoja(): bool
    {
        return $this->conexionesSalientes()->doesntExist();
    }
}
