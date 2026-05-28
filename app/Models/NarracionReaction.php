<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NarracionReaction extends Model
{
    public $timestamps = false; // solo created_at definido en migración

    protected $fillable = ['narracion_id', 'identifier', 'tipo'];

    public function narracion()
    {
        return $this->belongsTo(Narracion::class);
    }
}
