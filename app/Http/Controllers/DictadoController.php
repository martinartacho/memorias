<?php

namespace App\Http\Controllers;

use App\Models\Narracion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DictadoController extends Controller
{
    public function index()
    {
        return view('admin.dictar');
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string|min:5',
            'titulo'    => 'nullable|string|max:255',
        ]);

        $titulo = trim($request->titulo) ?: 'Borrador voz ' . now()->format('d/m/Y H:i');

        // Slug único
        $base = Str::slug($titulo);
        $slug = $base . '-' . Str::random(6);

        // Convertir saltos de línea en párrafos HTML
        $contenido = implode('', array_map(
            fn($p) => '<p>' . e(trim($p)) . '</p>',
            array_filter(explode("\n", $request->contenido), fn($p) => trim($p) !== '')
        ));

        $narracion = Narracion::create([
            'titulo'            => $titulo,
            'contenido'         => $contenido,
            'slug'              => $slug,
            'fecha_publicacion' => now(),
            'estado'            => 'borrador',
            'permiso_lectura'   => 'privado',
            'user_id'           => auth()->id(),
            'orden'             => 0,
        ]);

        return redirect()
            ->route('admin.narraciones.edit', $narracion->slug)
            ->with('success', 'Borrador guardado. Puedes editarlo y publicarlo cuando quieras.');
    }
}
