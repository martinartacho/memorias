<?php

namespace App\Support;

use App\Models\Narracion;
use Illuminate\Support\Collection;

/**
 * Series por capítulos: cada capítulo apunta al anterior (after_id).
 * Aquí se agrupan para el listado y se calculan las cadenas para los formularios.
 */
class SerieOrden
{
    /**
     * Recibe las narraciones ya ordenadas como se quiera mostrar y devuelve la misma lista
     * con cada serie seguida, en orden de capítulos, donde cae su primer capítulo.
     * Si el capítulo anterior no está en la lista (filtro, permisos), el capítulo sale solo.
     */
    public static function agrupar(Collection $narraciones): Collection
    {
        $enLista = $narraciones->keyBy('id');
        $siguiente = $narraciones->filter(fn (Narracion $n) => $n->after_id && $enLista->has($n->after_id))
            ->keyBy('after_id');

        $resultado = collect();
        $puestas = [];

        foreach ($narraciones as $n) {
            if ($n->after_id && $enLista->has($n->after_id)) {
                continue;
            }

            for ($actual = $n; $actual && ! isset($puestas[$actual->id]); $actual = $siguiente->get($actual->id)) {
                $puestas[$actual->id] = true;
                $resultado->push($actual);
            }
        }

        return $resultado;
    }

    /** Ids de esta narración y de todos los capítulos que cuelgan de ella (no pueden ser su «anterior»). */
    public static function descendientes(Narracion $narracion): array
    {
        $ids = [$narracion->id];
        $pendientes = [$narracion->id];

        while ($pendientes) {
            $hijos = Narracion::whereIn('after_id', $pendientes)->pluck('id')->all();
            $hijos = array_values(array_diff($hijos, $ids));
            $ids = array_merge($ids, $hijos);
            $pendientes = $hijos;
        }

        return $ids;
    }

    /** Narraciones que se pueden elegir como «va después de»: sin capítulo siguiente aún, y sin crear un ciclo. */
    public static function candidatas(?Narracion $actual = null): Collection
    {
        $excluir = $actual ? self::descendientes($actual) : [];

        return Narracion::query()
            ->where(function ($q) use ($actual) {
                $q->whereDoesntHave('siguiente');
                if ($actual?->after_id) {
                    $q->orWhere('id', $actual->after_id);
                }
            })
            ->when($excluir, fn ($q) => $q->whereNotIn('id', $excluir))
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'after_id']);
    }

    /** Capítulo número N de la serie (1 si es el primero o va suelto). */
    public static function numeroDeCapitulo(Narracion $narracion): int
    {
        $n = 1;
        $visto = [$narracion->id];
        $actual = $narracion;

        while ($actual->after_id && ! in_array($actual->after_id, $visto, true)) {
            $actual = Narracion::find($actual->after_id);
            if (! $actual) {
                break;
            }
            $visto[] = $actual->id;
            $n++;
        }

        return $n;
    }
}
