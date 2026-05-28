<?php

namespace App\Http\Controllers;

use App\Models\Narracion;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\MpdfException;

class NarracionController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Helpers de permisos
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve true si la cookie del request corresponde a un suscriptor verificado.
     */
    private function esSubscritorVerificado(Request $request): bool
    {
        return Subscriber::tokenIsValid($request->cookie('subscriber_token'));
    }

    /**
     * Devuelve true si el usuario autenticado puede administrar.
     */
    private function esAdmin(): bool
    {
        return auth()->check() && auth()->user()->canAccessAdmin();
    }

    /**
     * Verifica que el usuario tiene permiso para editar/borrar esta narración.
     * Admin: acceso total. Editor: solo sus propias narraciones.
     */
    private function autorizarEdicion(Narracion $narracion): void
    {
        $user = auth()->user();
        if ($user->isEditor() && $narracion->user_id !== $user->id) {
            abort(403, 'Solo puedes editar tus propias narraciones.');
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Listado público
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $esSubscriptor = $this->esSubscritorVerificado($request);
        $esAdmin       = $this->esAdmin();
        $hoy           = now()->toDateString();
        $q             = trim($request->input('q', ''));

        // Base: publicado + promocion, ordenado por fecha
        // Las fechas futuras se muestran a todos como teaser (el contenido
        // queda bloqueado hasta la fecha; ver show())
        $query = Narracion::visible()->orderByFecha();

        // Filtro de permiso_lectura según rol
        if ($esAdmin) {
            // Admin ve todo: público + seguidores + privado
        } elseif ($esSubscriptor) {
            $query->where('permiso_lectura', '!=', 'privado');
        } else {
            $query->where('permiso_lectura', 'publico');
        }

        // Búsqueda por título
        if ($q !== '') {
            $query->where('titulo', 'like', '%' . $q . '%');
        }

        // Narración destacada: solo cuando no hay búsqueda activa
        $featured = null;
        if ($q === '') {
            $featured = Narracion::publicado()
                ->where('permiso_lectura', 'publico')
                ->where('fecha_publicacion', '<=', $hoy)
                ->orderBy('orden', 'desc')
                ->orderByFecha()
                ->first();
        }

        $narraciones = $query->paginate(6)->withQueryString();

        // Títulos ocultos para visitantes anónimos (teaser de seguidores)
        $ocultas = collect();
        if (!$esAdmin && !$esSubscriptor && $q === '') {
            $ocultas = Narracion::publicado()
                ->where('permiso_lectura', 'seguidores')
                ->where('fecha_publicacion', '<=', $hoy)
                ->orderByFecha()
                ->pluck('titulo');
        }

        return view('narraciones.index-literario',
            compact('narraciones', 'featured', 'esSubscriptor', 'esAdmin', 'ocultas', 'q'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Detalle de narración
    // ─────────────────────────────────────────────────────────────────────────

    public function show(Request $request, $slug)
    {
        // Buscar en publicado + promocion
        $narracion = Narracion::where('slug', $slug)
            ->visible()
            ->firstOrFail();

        $esAdmin = $this->esAdmin();

        $esSubscriptor = $this->esSubscritorVerificado($request);
        $hoy           = now()->toDateString();
        $esFutura      = $narracion->fecha_publicacion->toDateString() > $hoy;

        // Admin ve todo sin restricciones
        if ($esAdmin) {
            return view('narraciones.show-literario', compact('narracion', 'esAdmin', 'esSubscriptor', 'esFutura'));
        }

        // Privadas: nunca accesibles públicamente
        if ($narracion->permiso_lectura === 'privado') {
            abort(404);
        }

        $canAccess = match ($narracion->permiso_lectura) {
            'publico'    => true,
            'seguidores' => $esSubscriptor,
            default      => false,
        };

        if (!$canAccess) {
            return redirect()->route('suscripcion.form', ['slug' => $slug]);
        }

        // Narración con fecha futura: mostrar página de "próximamente"
        // (no contabilizar lectura)
        if ($esFutura) {
            return view('narraciones.show-literario', compact('narracion', 'esAdmin', 'esSubscriptor', 'esFutura'));
        }

        // Promocion: acceso permitido pero contenido limitado en la vista
        $narracion->increment('count_read');

        return view('narraciones.show-literario', compact('narracion', 'esAdmin', 'esSubscriptor', 'esFutura'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Descarga PDF
    // ─────────────────────────────────────────────────────────────────────────

    public function downloadPdf(Request $request, string $slug)
    {
        // Solo narración publicada (no borradores ni solo-promo sin fecha)
        $narracion = Narracion::where('slug', $slug)
            ->where('estado', 'publicado')
            ->firstOrFail();

        $esAdmin       = $this->esAdmin();
        $esSubscriptor = $this->esSubscritorVerificado($request);
        $hoy           = now()->toDateString();

        // Narraciones con fecha futura: no disponibles aún
        if ($narracion->fecha_publicacion->toDateString() > $hoy && !$esAdmin) {
            abort(403, 'Esta narración aún no está disponible.');
        }

        // Acceso: solo suscriptores y admins
        if (!$esAdmin && !$esSubscriptor) {
            return redirect()->route('suscripcion.form', ['slug' => $slug]);
        }

        // Privadas: solo admin
        if ($narracion->permiso_lectura === 'privado' && !$esAdmin) {
            abort(403);
        }

        // Obtener el email del suscriptor o del admin autenticado
        if ($esAdmin) {
            $emailSuscriptor = auth()->user()->email;
        } else {
            $subscriber      = Subscriber::where('token', $request->cookie('subscriber_token'))->first();
            $emailSuscriptor = $subscriber?->email ?? 'suscriptor';
        }

        // Renderizar la vista HTML del PDF
        $html = view('pdf.narracion', compact('narracion', 'emailSuscriptor'))->render();

        // Generar PDF con mPDF
        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 25,
            'margin_right'  => 25,
            'margin_top'    => 20,
            'margin_bottom' => 20,
            'tempDir'       => sys_get_temp_dir(),
        ]);

        // Pie de página en cada hoja
        $mpdf->SetHTMLFooter('
            <table width="100%" style="font-family:\'Courier New\',monospace; font-size:6pt;
                   letter-spacing:0.1em; text-transform:uppercase; color:#b0a898; border-top:0.5pt solid #e0d8d0;">
              <tr>
                <td>Documento para: <strong>' . htmlspecialchars($emailSuscriptor) . '</strong></td>
                <td align="right">' . __('site.name') . ' &nbsp;·&nbsp; Pág. {PAGENO}/{nbpg}</td>
              </tr>
            </table>
        ');

        // Cifrado: contraseña de usuario = email del suscriptor
        //          contraseña de propietario = aleatoria (impide modificar el PDF)
        $mpdf->SetProtection(
            ['print', 'copy'],            // permisos permitidos
            $emailSuscriptor,             // contraseña de apertura (usuario)
            Str::random(32)               // contraseña de propietario (solo para edición)
        );

        $mpdf->WriteHTML($html);

        // Nombre de archivo limpio
        $filename = Str::slug($narracion->titulo) . '.pdf';

        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Administración
    // ─────────────────────────────────────────────────────────────────────────

    public function adminIndex(Request $request)
    {
        $user  = auth()->user();
        $query = Narracion::query();

        // Editor: solo sus propias narraciones
        if ($user->isEditor()) {
            $query->where('user_id', $user->id);
        }

        // Búsqueda por título/contenido
        if ($q = trim($request->input('q', ''))) {
            $query->where(function ($sub) use ($q) {
                $sub->where('titulo', 'like', "%{$q}%")
                    ->orWhere('contenido', 'like', "%{$q}%");
            });
        }

        // Filtro por estado
        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        // Filtro por permiso de lectura
        if ($permiso = $request->input('permiso')) {
            $query->where('permiso_lectura', $permiso);
        }

        // Ordenación
        match ($request->input('orden', 'fecha_desc')) {
            'fecha_asc'   => $query->orderBy('fecha_publicacion', 'asc'),
            'titulo_asc'  => $query->orderBy('titulo', 'asc'),
            'titulo_desc' => $query->orderBy('titulo', 'desc'),
            'lecturas'    => $query->orderBy('count_read', 'desc'),
            default       => $query->orderBy('fecha_publicacion', 'desc'),
        };

        // Stats sobre el scope del usuario (sin filtros de búsqueda)
        $baseStats = Narracion::query();
        if ($user->isEditor()) {
            $baseStats->where('user_id', $user->id);
        }
        $stats = [
            'total'      => (clone $baseStats)->count(),
            'publicadas' => (clone $baseStats)->where('estado', 'publicado')->count(),
            'borradores' => (clone $baseStats)->where('estado', 'borrador')->count(),
            'promocion'  => (clone $baseStats)->where('estado', 'promocion')->count(),
        ];

        $narraciones = $query->paginate(20)->withQueryString();

        return view('admin.narraciones.index-literario', compact('narraciones', 'stats'));
    }

    public function create()
    {
        return view('admin.narraciones.create-literario');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'            => 'required|string|max:255',
            'contenido'         => 'required',
            'fecha_publicacion' => 'required|date',
            'estado'            => 'required|in:borrador,publicado,promocion',
            'orden'             => 'nullable|integer|in:0,1000',
            'permiso_lectura'   => 'nullable|in:publico,seguidores,privado',
        ]);

        // Corrección automática de estado: publicado + fecha futura → promocion
        // (evita saltarse el sistema de autopublicación y notificaciones)
        $estado = $request->estado;
        if ($estado === 'publicado' && now()->toDateString() < $request->fecha_publicacion) {
            $estado = 'promocion';
        }

        Narracion::create([
            'titulo'            => $request->titulo,
            'contenido'         => $request->contenido,
            'slug'              => Str::slug($request->titulo) . '-' . time(),
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado'            => $estado,
            'user_id'           => auth()->id(),
            'orden'             => $request->orden ?? 0,
            'permiso_lectura'   => $request->permiso_lectura ?? 'publico',
            'count_feedback'    => 0,
            'count_read'        => 0,
        ]);

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración creada correctamente.');
    }

    public function edit($slug)
    {
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        $this->autorizarEdicion($narracion);
        return view('admin.narraciones.edit-literario', compact('narracion'));
    }

    public function update(Request $request, $slug)
    {
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        $this->autorizarEdicion($narracion);

        $request->validate([
            'titulo'            => 'required|string|max:255',
            'contenido'         => 'required',
            'fecha_publicacion' => 'required|date',
            'estado'            => 'required|in:borrador,publicado,promocion',
            'orden'             => 'nullable|integer|in:0,1000',
            'permiso_lectura'   => 'nullable|in:publico,seguidores,privado',
        ]);

        // Corrección automática de estado: publicado + fecha futura → promocion
        $estado = $request->estado;
        if ($estado === 'publicado' && now()->toDateString() < $request->fecha_publicacion) {
            $estado = 'promocion';
        }

        $narracion->update([
            'titulo'            => $request->titulo,
            'contenido'         => $request->contenido,
            'fecha_publicacion' => $request->fecha_publicacion,
            'estado'            => $estado,
            'orden'             => $request->orden ?? $narracion->orden,
            'permiso_lectura'   => $request->permiso_lectura ?? $narracion->permiso_lectura,
        ]);

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración actualizada correctamente.');
    }

    public function destroy(Request $request, $slug)
    {
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        $this->autorizarEdicion($narracion);

        $confirmation = $request->input('confirmar_eliminacion');
        if ($confirmation !== 'ELIMINAR_' . $narracion->slug) {
            return redirect()->route('admin.narraciones.index')
                ->with('error', 'Debes confirmar la eliminación escribiendo el código correcto.');
        }

        $narracion->delete();

        return redirect()->route('admin.narraciones.index')
            ->with('success', 'Narración eliminada correctamente.');
    }

    public function autosave(Request $request)
    {
        // Solo puede autosalvar narraciones propias
        $narracion = Narracion::where('id', $request->narracion_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $narracion->contenido = $request->contenido;
        $narracion->save();

        return response()->json([
            'success'   => true,
            'message'   => 'Guardado automáticamente',
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->hashName();
            $path     = $file->storeAs('narraciones/images', $filename, 'public');

            return response()->json([
                'success'  => true,
                'url'      => asset('storage/' . $path),
                'filename' => $filename,
                'alt'      => '',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen.',
            ], 500);
        }
    }
}
