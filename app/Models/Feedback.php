<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'narracion_id',
        'tipo_feedback',
        'comentario',
        'email',
        'nombre',
        'ip_address',
        'aprobado',
    ];

    protected $casts = [
        'aprobado' => 'boolean',
    ];

    /**
     * Relación con la narración
     */
    public function narracion()
    {
        return $this->belongsTo(Narracion::class);
    }

    /**
     * Scope para feedbacks aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('aprobado', true);
    }

    /**
     * Scope para feedbacks pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('aprobado', false);
    }

    /**
     * Obtener el color según el tipo de feedback
     */
    public function getColorAttribute()
    {
        return match($this->tipo_feedback) {
            'excelente' => 'green',
            'bueno' => 'blue',
            'regular' => 'yellow',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono según el tipo de feedback
     */
    public function getIconAttribute()
    {
        return match($this->tipo_feedback) {
            'excelente' => 'favorite',
            'bueno' => 'thumb_up',
            'regular' => 'sentiment_neutral',
            default => 'feedback'
        };
    }

    /**
     * Obtener etiqueta legible del tipo
     */
    public function getTipoLabelAttribute()
    {
        return match($this->tipo_feedback) {
            'excelente' => 'Excelente ❤️',
            'bueno' => 'Bueno 👍',
            'regular' => 'Regular 😐',
            default => $this->tipo_feedback
        };
    }
}
