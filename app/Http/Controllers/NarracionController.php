<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Narracion;
use App\Models\Feedback;
use Illuminate\Support\Str;

class NarracionController extends Controller
{
    public function index()
    {
        // Mostrar TODAS las narraciones publicadas (sin filtrar por permisos)
        // El filtrado por permisos se hace en el método show()
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

        // Verificar permisos de acceso
        $canAccess = false;
        
        switch($narracion->permiso_lectura) {
            case 'publico':
                $canAccess = true;
                break;
            case 'seguidores':
                // Si está autenticado y sigue al autor
                if (auth()->check()) {
                    $isFollowing = auth()->user()->following()
                        ->where('followed_id', $narracion->user_id)
                        ->exists();
                    $canAccess = $isFollowing || $narracion->user_id === auth()->id();
                }
                break;
        }

        if (!$canAccess) {
            if ($narracion->permiso_lectura === 'seguidores') {
                // Redirigir a formulario para seguir al autor
                return redirect()->route('narraciones.follow-required', $slug);
            }
            
            abort(403, 'No tienes permisos para ver esta narración.');
        }
        
        // Incrementar contador de lecturas
        $narracion->increment('count_read');
        
        return view('narraciones.show-literario', compact('narracion'));
    }

    public function followRequired($slug)
    {
        $narracion = Narracion::where('slug', $slug)
            ->publicado()
            ->firstOrFail();
        return view('narraciones.follow-required', compact('narracion'));
    }

    /**
     * Mostrar formulario de feedback para una narración
     */
    public function feedbackForm($slug)
    {
        
         $narracion = Narracion::where('slug', $slug)
            ->publicado()
            ->firstOrFail();
        return view('narraciones.feedback-form', compact('narracion'));
    }

    /**
     * Guardar feedback de una narración
     */
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'narracion_id' => 'required|exists:narraciones,id',
            'tipo_feedback' => 'required|in:excelente,bueno,regular',
            'comentario' => 'nullable|string|max:1000',
            'email' => 'required|email|max:255',
        ]);

        try {
            // Obtener la narración para guardar el ID
            $narracion = Narracion::findOrFail($request->narracion_id);
            
            // Guardar feedback en la base de datos
            Feedback::create([
                'narracion_id' => $narracion->id,
                'tipo_feedback' => $request->tipo_feedback,
                'comentario' => $request->comentario,
                'email' => $request->email,
                'nombre' => auth()->check() ? auth()->user()->name : null,
                'ip_address' => $request->ip(),
                'aprobado' => false, // Requiere moderación
            ]);

            // Log para debugging
            \Log::info('Feedback guardado:', [
                'narracion_id' => $narracion->id,
                'tipo_feedback' => $request->tipo_feedback,
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return redirect()->back()
                ->with('success', '¡Gracias por tu feedback! Tu opinión ha sido enviada y será revisada pronto.');

        } catch (\Exception $e) {
            \Log::error('Error al guardar feedback:', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return redirect()->back()
                ->with('error', 'Hubo un error al enviar tu feedback. Por favor, inténtalo de nuevo.')
                ->withInput();
        }
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
            'orden' => 'nullable|integer|in:0,1000',
            'permiso_lectura' => 'nullable|in:publico,seguidores',
        ]);

        $narracion = Narracion::create([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'slug' => Str::slug($request->titulo) . '-' . time(),
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado' => $request->estado,
            'user_id' => auth()->id(),
            'orden' => $request->orden ?? 0,
            'permiso_lectura' => $request->permiso_lectura ?? 'publico',
            'count_feedback' => 0,
            'count_read' => 0,
        ]);

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración creada exitosamente.');
    }

    public function edit($slug)
    {
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        return view('admin.narraciones.edit-literario', compact('narracion'));
    }

    public function update(Request $request, $slug)
    {
        \Log::info('=== INICIO UPDATE ===');
        \Log::info('Ruta actual: ' . $request->path());
        \Log::info('Método HTTP: ' . $request->method());
        \Log::info('Slug recibido: ' . $slug);
        \Log::info('Datos del request:', $request->all());
        
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        \Log::info('Narración encontrada:', ['id' => $narracion->id, 'slug' => $narracion->slug, 'titulo' => $narracion->titulo]);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required',
            'fecha_publicacion' => 'required|date',
            'estado' => 'required|in:borrador,publicado',
            'orden' => 'nullable|integer|in:0,1000',
            'permiso_lectura' => 'nullable|in:publico,seguidores',
        ]);

        \Log::info('Validación pasada');

        $updateData = [
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado' => $request->estado,
            'orden' => $request->orden ?? $narracion->orden,
            'permiso_lectura' => $request->permiso_lectura ?? $narracion->permiso_lectura,
        ];
        
        \Log::info('Datos para actualizar:', $updateData);
        
        $result = $narracion->update($updateData);
        \Log::info('Resultado de update: ' . $result);
        \Log::info('Narración después de update:', ['id' => $narracion->id, 'slug' => $narracion->fresh()->slug, 'titulo' => $narracion->fresh()->titulo]);

        \Log::info('=== FIN UPDATE ===');

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración actualizada exitosamente.');
    }

    public function destroy($slug)
    {
        \Log::info('=== INICIO DESTROY ===');
        \Log::info('Slug recibido para eliminar: ' . $slug);
        
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        \Log::info('Narración a eliminar:', ['id' => $narracion->id, 'slug' => $narracion->slug, 'titulo' => $narracion->titulo]);
        
        // Primera confirmación: verificar que el slug existe
        if (!$narracion) {
            \Log::info('Narración no encontrada');
            return redirect()->route('admin.narraciones.index')
                ->with('error', 'Narración no encontrada.');
        }
        
        \Log::info('Narración verificada, solicitando confirmación...');
        
        // Segunda confirmación: verificar que el usuario realmente quiere eliminar
        $confirmation = request('confirmar_eliminacion');
        if ($confirmation !== 'ELIMINAR_' . $narracion->slug) {
            \Log::info('Confirmación no recibida o incorrecta: ' . $confirmation);
            return redirect()->route('admin.narraciones.index')
                ->with('error', 'Debe confirmar la eliminación de la narración.');
        }
        
        \Log::info('Confirmación recibida correctamente: ELIMINAR_' . $narracion->slug);
        
        $result = $narracion->delete();
        \Log::info('Resultado de delete: ' . $result);
        
        \Log::info('=== FIN DESTROY ===');

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración eliminada exitosamente.');
    }

    /**
     * Autoguardar contenido de la narración
     */
    public function autosave(Request $request)
    {
        $narracion = Narracion::findOrFail($request->narracion_id);
        $narracion->contenido = $request->contenido;
        $narracion->save();

        return response()->json([
            'success' => true,
            'message' => 'Contenido guardado automáticamente',
            'timestamp' => now()->format('H:i:s')
        ]);
    }

    /**
     * Subir imagen para la narración
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('narraciones/images', $filename, 'public');

            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename,
                'alt' => $file->getClientOriginalName()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen: ' . $e->getMessage()
            ], 500);
        }
    }
}
