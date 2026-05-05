<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Narracion;
use Illuminate\Support\Str;

class NarracionController extends Controller
{
    public function index()
    {
        $narraciones = Narracion::publicado()
            ->orderByFecha()
            ->paginate(6);
        
        return view('narraciones.index-literario', compact('narraciones'));
    }

    public function show($slug)
    {
        $narracion = Narracion::where('slug', $slug)
            ->publicado()
            ->firstOrFail();
        
        return view('narraciones.show-literario', compact('narracion'));
    }

    public function adminIndex()
    {
        $narraciones = Narracion::orderByFecha()->paginate(10);
        return view('admin.narraciones.index-literario', compact('narraciones'));
    }

    public function create()
    {
        return view('admin.narraciones.create-literario');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required',
            'fecha_publicacion' => 'required|date',
            'estado' => 'required|in:borrador,publicado',
        ]);

        $narracion = Narracion::create([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'slug' => Str::slug($request->titulo) . '-' . time(),
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración creada exitosamente.');
    }

    public function edit($id)
    {
        $narracion = Narracion::findOrFail($id);
        return view('admin.narraciones.edit-literario', compact('narracion'));
    }

    public function update(Request $request, $id)
    {
        $narracion = Narracion::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required',
            'fecha_publicacion' => 'required|date',
            'estado' => 'required|in:borrador,publicado',
        ]);

        $narracion->update([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'slug' => Str::slug($request->titulo) . '-' . time(),
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $narracion = Narracion::findOrFail($id);
        $narracion->delete();

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración eliminada exitosamente.');
    }
}
