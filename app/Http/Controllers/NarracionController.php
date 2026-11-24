<?php

namespace App\Http\Controllers;

use App\Models\Narracion;
use App\Models\NarracionComment;
use App\Models\NarracionReaction;
use App\Models\Subscriber;
use App\Support\SerieOrden;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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

    /** Identificador de quien vota: mismo criterio para votar y para comprobar que ya votó. */
    private function identificadorVotante(Request $request): string
    {
        if ($this->esAdmin() || auth()->check()) {
            return 'u:' . auth()->id();
        }

        if ($this->esSubscritorVerificado($request)) {
            return 's:' . $request->cookie('subscriber_token');
        }

        return 'ip:' . md5($request->ip() . $request->userAgent());
    }

    private function haValorado(Request $request, Narracion $narracion): bool
    {
        return NarracionReaction::where('narracion_id', $narracion->id)
            ->where('identifier', $this->identificadorVotante($request))
            ->exists();
    }

    /** Capítulo anterior/siguiente, número de capítulo y voto propio, para la vista de una narración. */
    private function datosSerie(Request $request, Narracion $narracion, bool $esAdmin): array
    {
        $visibles = fn () => Narracion::visible()
            ->when(!$esAdmin, fn ($q) => $q->where('permiso_lectura', '!=', 'privado'));

        $anterior  = $narracion->after_id ? $visibles()->find($narracion->after_id) : null;
        $siguiente = $visibles()->where('after_id', $narracion->id)->first();

        return [
            'anterior'  => $anterior,
            'siguiente' => $siguiente,
            'capitulo'  => ($anterior || $siguiente) ? SerieOrden::numeroDeCapitulo($narracion) : null,
            'miVoto'    => NarracionReaction::where('narracion_id', $narracion->id)
                ->where('identifier', $this->identificadorVotante($request))
                ->value('tipo'),
        ];
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

        // Base: publicado + promocion
        // Las fechas futuras se muestran a todos como teaser (el contenido
        // queda bloqueado hasta la fecha; ver show())
        $query = Narracion::visible();

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

        // Ordenación
        $orden = $request->input('orden', 'fecha_desc');
        match ($orden) {
            'fecha_asc'   => $query->orderBy('fecha_publicacion', 'asc'),
            'titulo_asc'  => $query->orderBy('titulo', 'asc'),
            'titulo_desc' => $query->orderBy('titulo', 'desc'),
            default       => $query->orderBy('fecha_publicacion', 'desc'),
        };

        // Narración destacada: solo cuando no hay búsqueda activa
        $featured = null;
        if ($q === '') {
            $featured = Narracion::publicado()
                ->where('permiso_lectura', 'publico')
                ->where('fecha_publicacion', '<=', $hoy)
                ->whereNull('after_id')
                ->orderBy('orden', 'desc')
                ->orderByFecha()
                ->first();
        }

        if (Narracion::whereNotNull('after_id')->exists()) {
            $todas = SerieOrden::agrupar($query->get());
            $pagina = max(1, (int) $request->input('page', 1));
            $narraciones = new LengthAwarePaginator(
                $todas->forPage($pagina, 50)->values(),
                $todas->count(),
                50,
                $pagina,
                ['path' => $request->url(), 'query' => $request->query()],
            );
        } else {
            $narraciones = $query->paginate(50)->withQueryString();
        }

        // Títulos ocultos para visitantes anónimos (teaser de seguidores)
        $ocultas = collect();
        if (!$esAdmin && !$esSubscriptor && $q === '') {
            $ocultas = Narracion::publicado()
                ->where('permiso_lectura', 'seguidores')
                ->where('fecha_publicacion', '<=', $hoy)
                ->orderByFecha()
                ->pluck('titulo');
        }

        // Capítulos 1 de series con más de un capítulo: llevan el enlace «Ver toda la serie»
        $primerosDeSerie = Narracion::whereNotNull('after_id')->pluck('after_id')->all();

        return view('narraciones.index-literario',
            compact('narraciones', 'featured', 'esSubscriptor', 'esAdmin', 'ocultas', 'q', 'orden', 'primerosDeSerie'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Página de una serie por capítulos
    // ─────────────────────────────────────────────────────────────────────────

    public function serie(Request $request, string $slug)
    {
        $esAdmin = $this->esAdmin();
        $hoy = now()->toDateString();
        $visibles = fn () => Narracion::publicado()
            ->where('fecha_publicacion', '<=', $hoy)
            ->when(!$esAdmin, fn ($q) => $q->where('permiso_lectura', '!=', 'privado'));

        $narracion = $visibles()->where('slug', $slug)->firstOrFail();

        // Subir hasta el primer capítulo y bajar por los siguientes
        $raiz = $narracion;
        $vistos = [$raiz->id];
        while ($raiz->after_id && ($padre = Narracion::find($raiz->after_id)) && !in_array($padre->id, $vistos, true)) {
            $raiz = $padre;
            $vistos[] = $padre->id;
        }

        $capitulos = collect();
        $actual = $raiz;
        $puestos = [];
        while ($actual && !isset($puestos[$actual->id])) {
            $puestos[$actual->id] = true;
            $capitulos->push($actual);
            $actual = Narracion::where('after_id', $actual->id)->first();
        }

        $visiblesIds = $visibles()->whereIn('id', $capitulos->pluck('id'))->pluck('id')->all();
        $capitulos = $capitulos->filter(fn (Narracion $n) => in_array($n->id, $visiblesIds, true))->values();

        if ($capitulos->count() < 2) {
            return redirect()->route('narraciones.show', $slug);
        }

        $primero = $capitulos->first();
        $esSubscriptor = $this->esSubscritorVerificado($request);

        $leidas = array_filter(array_map('intval', explode(',', (string) $request->cookie('narraciones_leidas'))));
        $leidas = array_merge($leidas, DB::table('narracion_lecturas')
            ->where('identifier', $this->identificadorVotante($request))
            ->whereIn('narracion_id', $capitulos->pluck('id'))
            ->pluck('narracion_id')->all());

        $resumen = trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags(str_replace(['</p>', '</h3>', '</h2>', '<br>', '<br/>'], ' ', (string) $primero->contenido)))));

        return view('narraciones.serie-literario', [
            'tituloSerie'   => trim((string) preg_replace('/\.?\s*Cap[ií]tulo\s*\d+.*$/iu', '', $primero->titulo)) ?: $primero->titulo,
            'resumen'       => Str::limit($resumen, 380),
            'capitulos'     => $capitulos,
            'primero'       => $primero,
            'leidas'        => array_values(array_unique($leidas)),
            'lecturas'      => $capitulos->sum('count_read'),
            'esSubscriptor' => $esSubscriptor,
            'esAdmin'       => $esAdmin,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Detalle de narración
    // ─────────────────────────────────────────────────────────────────────────

    public function show(Request $request, $slug)
    {
        $esAdmin = $this->esAdmin();

        // Visitantes: publicado + promocion. El admin también puede previsualizar borradores.
        $narracion = Narracion::where('slug', $slug)
            ->when(!$esAdmin, fn ($query) => $query->visible())
            ->first();

        if (!$narracion) {
            $sinNumero = preg_replace('/-\d{9,}$/', '', $slug);

            if ($sinNumero !== $slug && Narracion::where('slug', $sinNumero)->when(!$esAdmin, fn ($query) => $query->visible())->exists()) {
                return redirect()->route('narraciones.show', $sinNumero, 301);
            }

            abort(404);
        }

        if (!in_array($narracion->estado, ['publicado', 'promocion'], true)) {
            $this->autorizarEdicion($narracion);
        }

        $esSubscriptor = $this->esSubscritorVerificado($request);
        $hoy           = now()->toDateString();
        $esFutura      = $narracion->fecha_publicacion->toDateString() > $hoy;

        // Suscriptor activo (para comentarios)
        $subscriber = $esSubscriptor
            ? Subscriber::where('token', $request->cookie('subscriber_token'))->first()
            : null;

        // Comentarios públicos (solo narraciones no futuras)
        $comentarios  = $esFutura ? collect() : $narracion->comments()->with('subscriber')->get();

        // Comentario propio del suscriptor actual (null si no ha comentado)
        $miComentario = ($subscriber && !$esFutura)
            ? NarracionComment::where('narracion_id', $narracion->id)
                ->where('subscriber_id', $subscriber->id)
                ->first()
            : null;

        $serie = $this->datosSerie($request, $narracion, $esAdmin);

        // Admin ve todo sin restricciones
        if ($esAdmin) {
            return view('narraciones.show-literario',
                array_merge(compact('narracion', 'esAdmin', 'esSubscriptor', 'esFutura', 'subscriber', 'comentarios', 'miComentario'), $serie));
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
            return view('narraciones.show-literario',
                array_merge(compact('narracion', 'esAdmin', 'esSubscriptor', 'esFutura', 'subscriber', 'comentarios', 'miComentario'), $serie));
        }

        // Serie por capítulos: si el capítulo anterior lo pide, hay que haberlo valorado (👍 o 👎)
        if ($narracion->after_id && $narracion->estado === 'publicado') {
            $previo = Narracion::publicado()
                ->where('fecha_publicacion', '<=', $hoy)
                ->find($narracion->after_id);

            if ($previo && $previo->pide_valoracion && !$this->haValorado($request, $previo)) {
                return redirect()->route('narraciones.show', $previo->slug)
                    ->with('serie_aviso', 'Para seguir con el siguiente capítulo, valora primero este con 👍 o 👎.');
            }
        }

        // Promocion: acceso permitido pero contenido limitado en la vista
        // Marca de «leído» en este navegador para la página de la serie
        $leidasCookie = array_filter(array_map('intval', explode(',', (string) $request->cookie('narraciones_leidas'))));
        $leidasCookie[] = $narracion->id;
        Cookie::queue('narraciones_leidas', implode(',', array_slice(array_unique($leidasCookie), -300)), 60 * 24 * 365);

        // Lectura única por visitante: recargar o volver a abrir no suma
        $nuevaLectura = DB::table('narracion_lecturas')->insertOrIgnore([
            'narracion_id' => $narracion->id,
            'identifier'   => $this->identificadorVotante($request),
            'created_at'   => now(),
        ]);
        if ($nuevaLectura) {
            $narracion->increment('count_read');
        }

        return view('narraciones.show-literario',
            array_merge(compact('narracion', 'esAdmin', 'esSubscriptor', 'esFutura', 'subscriber', 'comentarios', 'miComentario'), $serie));
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
    // Reacciones 👍 👎
    // ─────────────────────────────────────────────────────────────────────────

    public function react(Request $request, string $slug)
    {
        $request->validate(['tipo' => 'required|in:like,dislike']);

        $narracion = Narracion::where('slug', $slug)
            ->where('estado', 'publicado')
            ->where('fecha_publicacion', '<=', now()->toDateString())
            ->firstOrFail();

        // Construir identificador único del votante
        $identifier = $this->identificadorVotante($request);

        // Intentar insertar; si ya existe (unique constraint) → ya votó
        $yaVotado = NarracionReaction::where('narracion_id', $narracion->id)
            ->where('identifier', $identifier)
            ->exists();

        if ($yaVotado) {
            return response()->json([
                'ok'       => false,
                'motivo'   => 'ya_votado',
                'likes'    => $narracion->count_feedback,
                'dislikes' => $narracion->count_dislikes,
            ]);
        }

        // Registrar reacción e incrementar contador
        NarracionReaction::create([
            'narracion_id' => $narracion->id,
            'identifier'   => $identifier,
            'tipo'         => $request->tipo,
        ]);

        if ($request->tipo === 'like') {
            $narracion->increment('count_feedback');
        } else {
            $narracion->increment('count_dislikes');
        }

        $narracion->refresh();

        return response()->json([
            'ok'       => true,
            'tipo'     => $request->tipo,
            'likes'    => $narracion->count_feedback,
            'dislikes' => $narracion->count_dislikes,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Comentarios de suscriptores
    // ─────────────────────────────────────────────────────────────────────────

    public function comment(Request $request, string $slug)
    {
        // Solo suscriptores verificados
        if (!$this->esSubscritorVerificado($request) && !$this->esAdmin()) {
            return redirect()->route('suscripcion.form', ['slug' => $slug])
                ->with('error', 'Debes ser suscriptor verificado para comentar.');
        }

        $narracion = Narracion::where('slug', $slug)
            ->visible()
            ->where('fecha_publicacion', '<=', now()->toDateString())
            ->firstOrFail();

        $request->validate([
            'contenido' => 'required|string|min:3|max:1000',
        ], [
            'contenido.required' => 'El comentario no puede estar vacío.',
            'contenido.min'      => 'El comentario debe tener al menos 3 caracteres.',
            'contenido.max'      => 'El comentario no puede superar los 1000 caracteres.',
        ]);

        // Obtener el subscriber
        $subscriber = Subscriber::where('token', $request->cookie('subscriber_token'))->first();

        if (!$subscriber) {
            return back()->with('error', 'No se ha podido identificar tu cuenta de suscriptor.');
        }

        // Un solo comentario por suscriptor y narración
        $yaExiste = NarracionComment::where('narracion_id', $narracion->id)
            ->where('subscriber_id', $subscriber->id)
            ->exists();

        if ($yaExiste) {
            return back()->with('error', 'Ya has dejado un comentario en esta narración. Puedes modificarlo o eliminarlo.');
        }

        NarracionComment::create([
            'narracion_id'  => $narracion->id,
            'subscriber_id' => $subscriber->id,
            'contenido'     => $request->contenido,
        ]);

        return redirect()
            ->route('narraciones.show', $slug)
            ->withFragment('comentarios')
            ->with('success', 'Comentario publicado.');
    }

    public function updateComment(Request $request, string $slug, int $commentId)
    {
        $comment = NarracionComment::findOrFail($commentId);

        // Solo el propio suscriptor o el admin pueden editar
        $subscriber = Subscriber::where('token', $request->cookie('subscriber_token'))->first();
        $esPropio   = $subscriber && $comment->subscriber_id === $subscriber->id;

        if (!$this->esAdmin() && !$esPropio) {
            abort(403);
        }

        $request->validate([
            'contenido' => 'required|string|min:3|max:1000',
        ], [
            'contenido.required' => 'El comentario no puede estar vacío.',
            'contenido.min'      => 'El comentario debe tener al menos 3 caracteres.',
            'contenido.max'      => 'El comentario no puede superar los 1000 caracteres.',
        ]);

        $comment->update(['contenido' => $request->contenido]);

        return redirect()
            ->route('narraciones.show', $slug)
            ->withFragment('comentarios')
            ->with('success', 'Comentario actualizado.');
    }

    public function deleteComment(Request $request, string $slug, int $commentId)
    {
        $comment = NarracionComment::findOrFail($commentId);

        // Admin o el propio suscriptor pueden eliminar
        $subscriber = Subscriber::where('token', $request->cookie('subscriber_token'))->first();
        $esPropio   = $subscriber && $comment->subscriber_id === $subscriber->id;

        if (!$this->esAdmin() && !$esPropio) {
            abort(403);
        }

        $comment->delete();

        return redirect()
            ->route('narraciones.show', $slug)
            ->withFragment('comentarios')
            ->with('success', 'Comentario eliminado.');
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

        // Filtro por destacada (orden = 1000 preferente / 0 automático)
        if ($request->filled('destacada')) {
            $query->where('orden', $request->input('destacada'));
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
            'destacadas' => (clone $baseStats)->where('orden', 1000)->count(),
        ];

        $narraciones = $query->paginate(20)->withQueryString();

        return view('admin.narraciones.index-literario', compact('narraciones', 'stats'));
    }

    public function create()
    {
        return view('admin.narraciones.create-literario', ['candidatas' => SerieOrden::candidatas()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'            => 'required|string|max:255',
            'contenido'         => 'required',
            'fecha_publicacion' => 'required|date',
            'estado'            => 'required|in:borrador,publicado,promocion',
            'orden'             => 'nullable|integer|in:0,1000',
            'after_id'          => ['nullable', 'integer', 'exists:narracions,id', 'unique:narracions,after_id'],
            'permiso_lectura'   => 'nullable|in:publico,seguidores,privado',
        ], ['after_id.unique' => 'Esa narración ya tiene un capítulo siguiente.']);

        // Corrección automática de estado: publicado + fecha futura → promocion
        // (evita saltarse el sistema de autopublicación y notificaciones)
        $estado = $request->estado;
        if ($estado === 'publicado' && now()->toDateString() < $request->fecha_publicacion) {
            $estado = 'promocion';
        }

        Narracion::create([
            'after_id'          => $request->after_id ?: null,
            'pide_valoracion'   => $request->boolean('pide_valoracion'),
            'titulo'            => $request->titulo,
            'contenido'         => $request->contenido,
            'slug'              => $this->uniqueSlug($request->titulo),
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

    private function uniqueSlug(string $titulo): string
    {
        $base = Str::slug($titulo) ?: 'narracion';
        $slug = $base;

        for ($i = 2; Narracion::where('slug', $slug)->exists(); $i++) {
            $slug = $base . '-' . $i;
        }

        return $slug;
    }

    public function edit($slug)
    {
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        $this->autorizarEdicion($narracion);
        return view('admin.narraciones.edit-literario', ['narracion' => $narracion, 'candidatas' => SerieOrden::candidatas($narracion)]);
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
            'after_id'          => ['nullable', 'integer', 'exists:narracions,id', Rule::unique('narracions', 'after_id')->ignore($narracion->id)],
            'permiso_lectura'   => 'nullable|in:publico,seguidores,privado',
        ], ['after_id.unique' => 'Esa narración ya tiene un capítulo siguiente.']);

        if ($request->filled('after_id') && in_array((int) $request->after_id, SerieOrden::descendientes($narracion), true)) {
            return back()->withInput()->withErrors(['after_id' => 'No puede ir después de sí misma ni de uno de sus propios capítulos siguientes.']);
        }

        // Corrección automática de estado: publicado + fecha futura → promocion
        $estado = $request->estado;
        if ($estado === 'publicado' && now()->toDateString() < $request->fecha_publicacion) {
            $estado = 'promocion';
        }

        $narracion->update([
            'after_id'          => $request->after_id ?: null,
            'pide_valoracion'   => $request->boolean('pide_valoracion'),
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

    public function toggleDestacada($slug)
    {
        $narracion = Narracion::where('slug', $slug)->firstOrFail();
        $this->autorizarEdicion($narracion);

        $narracion->orden = $narracion->orden == 1000 ? 0 : 1000;
        $narracion->save();

        return redirect()->route('admin.narraciones.index', request()->query())
            ->with('success', $narracion->orden == 1000
                ? 'Narración marcada como preferente.'
                : 'Narración vuelve a orden automático.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'action' => 'required|in:estado:publicado,estado:promocion,estado:borrador,destacada:1000,destacada:0,permiso:publico,permiso:seguidores,delete',
        ]);

        $user  = auth()->user();
        $query = Narracion::whereIn('id', $request->ids);
        if ($user->isEditor()) {
            $query->where('user_id', $user->id);
        }

        [$tipo, $valor] = array_pad(explode(':', $request->action, 2), 2, null);

        if ($tipo === 'estado') {
            $n = $query->update(['estado' => $valor]);
            $mensaje = "{$n} narración(es) actualizadas a estado «{$valor}».";
        } elseif ($tipo === 'destacada') {
            $n = $query->update(['orden' => (int) $valor]);
            $mensaje = $valor == 1000
                ? "{$n} narración(es) marcadas como preferentes."
                : "{$n} narración(es) vueltas a orden automático.";
        } elseif ($tipo === 'permiso') {
            $n = $query->update(['permiso_lectura' => $valor]);
            $mensaje = $valor === 'seguidores'
                ? "{$n} narración(es) marcadas como solo suscriptores."
                : "{$n} narración(es) marcadas como público.";
        } else {
            $n = $query->count();
            $query->delete();
            $mensaje = "{$n} narración(es) eliminadas.";
        }

        return redirect()->route('admin.narraciones.index', request()->query())
            ->with('success', $mensaje);
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
