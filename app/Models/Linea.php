<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    protected $fillable = ['titulo', 'descripcion', 'tipo', 'user_id', 'publica'];

    protected $casts = ['publica' => 'boolean'];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Nodos del grafo (registros pivot con metadatos) */
    public function nodos()
    {
        return $this->hasMany(LineaNarracion::class)->orderBy('orden');
    }

    /** Narraciones que forman parte de esta línea */
    public function narraciones()
    {
        return $this->belongsToMany(Narracion::class, 'linea_narracion')
                    ->withPivot(['etiqueta', 'orden'])
                    ->orderByPivot('orden');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function esLineal(): bool    { return $this->tipo === 'lineal'; }
    public function esRamificada(): bool { return $this->tipo === 'ramificada'; }

    /** Nodos raíz: los que no tienen ninguna conexión entrante dentro de esta línea */
    public function nodosRaiz()
    {
        $idsConEntrada = LineaConexion::whereIn('to_id', $this->nodos->pluck('id'))->pluck('to_id');
        return $this->nodos->whereNotIn('id', $idsConEntrada)->values();
    }
}
