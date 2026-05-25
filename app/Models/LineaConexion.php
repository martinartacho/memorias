<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaConexion extends Model
{
    protected $table = 'linea_conexiones';

    protected $fillable = ['from_id', 'to_id', 'etiqueta', 'orden'];

    // ── Relaciones ────────────────────────────────────────────────────────────

    /** Nodo origen */
    public function desde()
    {
        return $this->belongsTo(LineaNarracion::class, 'from_id')->with('narracion');
    }

    /** Nodo destino */
    public function hasta()
    {
        return $this->belongsTo(LineaNarracion::class, 'to_id')->with('narracion');
    }
}
