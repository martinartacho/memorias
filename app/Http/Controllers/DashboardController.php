<?php

namespace App\Http\Controllers;

use App\Models\Narracion;
use App\Models\NarracionComment;
use App\Models\Subscriber;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user          = auth()->user();
        $hoy           = now()->toDateString();
        $esSubscriptor = Subscriber::tokenIsValid($request->cookie('subscriber_token'));
        $suscriptores  = collect();

        if ($user->isAdmin()) {
            $stats = [
                'total'      => Narracion::count(),
                'publicadas' => Narracion::where('estado', 'publicado')->count(),
                'promocion'  => Narracion::where('estado', 'promocion')->count(),
                'borradores' => Narracion::where('estado', 'borrador')->count(),
                'usuarios'   => \App\Models\User::count(),
            ];
            $suscriptores = Subscriber::orderByRaw('verified_at IS NULL DESC, created_at DESC')->get();

        } elseif ($user->isEditor()) {
            $q = fn() => Narracion::where('user_id', $user->id);
            $stats = [
                'total'      => $q()->count(),
                'publicadas' => $q()->where('estado', 'publicado')->count(),
                'promocion'  => $q()->where('estado', 'promocion')->count(),
                'borradores' => $q()->where('estado', 'borrador')->count(),
            ];
            $suscriptores = Subscriber::orderByRaw('verified_at IS NULL DESC, created_at DESC')->get();

        } else {
            $base = fn() => Narracion::visible()
                ->where('fecha_publicacion', '<=', $hoy)
                ->where('permiso_lectura', $esSubscriptor ? '!=' : '=', $esSubscriptor ? 'privado' : 'publico');
            $stats = [
                'total'      => $base()->count(),
                'publicadas' => $base()->where('estado', 'publicado')->count(),
                'promocion'  => $base()->where('estado', 'promocion')->count(),
                'borradores' => 0,
            ];
        }

        // ── Activitats recents (admin + editor) ───────────────────────────────
        $activitats = collect();

        if ($user->isAdmin() || $user->isEditor()) {

            // Comentaris recents
            $commentsQuery = NarracionComment::with(['narracion', 'subscriber'])
                ->orderBy('created_at', 'desc')
                ->limit(15);

            // Editor: solo en les seves narracions
            if ($user->isEditor()) {
                $commentsQuery->whereHas('narracion', fn($q) => $q->where('user_id', $user->id));
            }

            $comentarisRecents = $commentsQuery->get()->map(fn($c) => [
                'tipus'    => 'comentari',
                'icono'    => 'bi-chat-text',
                'color'    => 'var(--accent)',
                'text'     => Str::limit($c->contenido, 90),
                'alias'    => Str::before($c->subscriber->email ?? 'lector', '@'),
                'narracio' => $c->narracion->titulo,
                'url'      => route('narraciones.show', $c->narracion->slug) . '#comentarios',
                'data'     => $c->created_at,
            ]);

            // Subscriptors recents (sols admin)
            $subscriptorsRecents = collect();
            if ($user->isAdmin()) {
                $subscriptorsRecents = Subscriber::orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(fn($s) => [
                        'tipus'    => 'subscriptor',
                        'icono'    => $s->isVerified() ? 'bi-envelope-check' : 'bi-envelope-plus',
                        'color'    => $s->isVerified() ? '#16a34a' : 'var(--gold)',
                        'text'     => $s->email,
                        'alias'    => null,
                        'narracio' => null,
                        'url'      => null,
                        'data'     => $s->created_at,
                    ]);
            }

            $activitats = $comentarisRecents
                ->concat($subscriptorsRecents)
                ->sortByDesc('data')
                ->take(10)
                ->values();
        }

        return view('dashboard', compact('user', 'stats', 'esSubscriptor', 'suscriptores', 'activitats'));
    }
}
