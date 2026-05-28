<?php

namespace App\Http\Controllers;

use App\Models\Narracion;
use App\Models\Subscriber;
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

        return view('dashboard', compact('user', 'stats', 'esSubscriptor', 'suscriptores'));
    }
}
