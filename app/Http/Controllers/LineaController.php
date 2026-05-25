<?php

namespace App\Http\Controllers;

use App\Models\Linea;
use App\Models\LineaNarracion;
use App\Models\LineaConexion;
use App\Models\Narracion;
use Illuminate\Http\Request;

class LineaController extends Controller
{
    // ── Helpers ───────────────────────────────────────────────────────────────

    private function autorizarLinea(Linea $linea): void
    {
        $user = auth()->user();
        if ($user->isEditor() && $linea->user_id !== $user->id) {
            abort(403, 'Solo puedes gestionar tus propias líneas argumentales.');
        }
    }

    private function narracionesDisponibles(): \Illuminate\Database\Eloquent\Collection
    {
        $user = auth()->user();
        $query = Narracion::orderByFecha();
        if ($user->isEditor()) {
            $query->where('user_id', $user->id);
        }
        return $query->get();
    }

    // ── CRUD Líneas ───────────────────────────────────────────────────────────

    public function index()
    {
        $user  = auth()->user();
        $query = Linea::with(['user', 'nodos'])->latest();

        if ($user->isEditor()) {
            $query->where('user_id', $user->id);
        }

        $lineas = $query->get();
        return view('admin.lineas.index', compact('lineas'));
    }

    public function create()
    {
        return view('admin.lineas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|in:lineal,ramificada',
            'publica'     => 'nullable|boolean',
        ]);

        $linea = Linea::create([
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo'        => $request->tipo,
            'user_id'     => auth()->id(),
            'publica'     => $request->boolean('publica'),
        ]);

        return redirect()->route('admin.lineas.edit', $linea)
            ->with('success', 'Línea argumental creada. Ahora añade las narraciones.');
    }

    public function edit(Linea $linea)
    {
        $this->autorizarLinea($linea);

        $linea->load(['nodos.narracion', 'nodos.conexionesSalientes.hasta.narracion']);
        $disponibles = $this->narracionesDisponibles();

        // Narraciones ya en la línea
        $enLinea = $linea->nodos->pluck('narracion_id')->toArray();

        return view('admin.lineas.edit', compact('linea', 'disponibles', 'enLinea'));
    }

    public function update(Request $request, Linea $linea)
    {
        $this->autorizarLinea($linea);

        $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|in:lineal,ramificada',
            'publica'     => 'nullable|boolean',
        ]);

        $linea->update([
            'titulo'      => $request->titulo,
            'descripcion' => $request->descripcion,
            'tipo'        => $request->tipo,
            'publica'     => $request->boolean('publica'),
        ]);

        return back()->with('success', 'Línea actualizada.');
    }

    public function destroy(Linea $linea)
    {
        $this->autorizarLinea($linea);
        $linea->delete();

        return redirect()->route('admin.lineas.index')
            ->with('success', 'Línea argumental eliminada.');
    }

    // ── Gestión de nodos ──────────────────────────────────────────────────────

    public function addNodo(Request $request, Linea $linea)
    {
        $this->autorizarLinea($linea);

        $request->validate([
            'narracion_id' => 'required|exists:narracions,id',
            'etiqueta'     => 'nullable|string|max:20',
        ]);

        // Evitar duplicados
        LineaNarracion::firstOrCreate(
            ['linea_id' => $linea->id, 'narracion_id' => $request->narracion_id],
            ['etiqueta' => $request->etiqueta, 'orden' => $linea->nodos()->count()]
        );

        return back()->with('success', 'Narración añadida a la línea.');
    }

    public function removeNodo(Linea $linea, LineaNarracion $nodo)
    {
        $this->autorizarLinea($linea);

        // Eliminar también todas las conexiones que involucran este nodo
        LineaConexion::where('from_id', $nodo->id)
                     ->orWhere('to_id', $nodo->id)
                     ->delete();
        $nodo->delete();

        return back()->with('success', 'Narración retirada de la línea.');
    }

    // ── Gestión de conexiones ─────────────────────────────────────────────────

    public function addConexion(Request $request, Linea $linea)
    {
        $this->autorizarLinea($linea);

        $request->validate([
            'from_id'  => 'required|exists:linea_narracion,id',
            'to_id'    => 'required|exists:linea_narracion,id|different:from_id',
            'etiqueta' => 'nullable|string|max:60',
        ]);

        // Evitar conexión duplicada
        LineaConexion::firstOrCreate(
            ['from_id' => $request->from_id, 'to_id' => $request->to_id],
            ['etiqueta' => $request->etiqueta, 'orden' => 0]
        );

        return back()->with('success', 'Conexión añadida.');
    }

    public function removeConexion(Linea $linea, LineaConexion $conexion)
    {
        $this->autorizarLinea($linea);
        $conexion->delete();

        return back()->with('success', 'Conexión eliminada.');
    }

    // ── Vista pública ─────────────────────────────────────────────────────────

    public function publicIndex()
    {
        $lineas = Linea::where('publica', true)
            ->with(['nodos' => fn($q) => $q->orderBy('orden')])
            ->latest()
            ->get();

        return view('lineas.index', compact('lineas'));
    }
}
