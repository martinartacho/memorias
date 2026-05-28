<?php

use App\Http\Controllers\NarracionController;
use App\Http\Controllers\SubscriberController;
use App\Models\Narracion;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// ─── Rutas públicas ─────────────────────────────────────────────────────────

Route::get('/', [NarracionController::class, 'index'])->name('home');
Route::get('/narraciones', [NarracionController::class, 'index'])->name('narraciones.index');
Route::get('/narracion/{slug}', [NarracionController::class, 'show'])->name('narraciones.show');
Route::get('/narracion/{slug}/descargar', [NarracionController::class, 'downloadPdf'])->name('narraciones.pdf');
Route::get('/releases', fn() => view('releases'))->name('releases');

// ─── Suscripción por email (reemplaza el sistema de follow) ─────────────────

Route::get('/suscribirse', [SubscriberController::class, 'showForm'])->name('suscripcion.form');
Route::post('/suscribirse', [SubscriberController::class, 'subscribe'])->name('suscripcion.store');
Route::get('/suscripcion/acceder/{token}', [SubscriberController::class, 'acceder'])->name('suscripcion.acceder');
Route::get('/suscripcion/baja/{token}', [SubscriberController::class, 'showBaja'])->name('suscripcion.baja');
Route::delete('/suscripcion/baja/{token}', [SubscriberController::class, 'baja'])->name('suscripcion.baja.confirmar');

// ─── Autenticación ──────────────────────────────────────────────────────────

Auth::routes();

// ─── Área de usuario autenticado ────────────────────────────────────────────

Route::get('/dashboard', function (Request $request) {
    $user = auth()->user();
    $hoy  = now()->toDateString();

    $esSubscriptor = Subscriber::tokenIsValid($request->cookie('subscriber_token'));

    $suscriptores = collect(); // solo cargados para admin y editor

    if ($user->isAdmin()) {
        // ── Admin: todas las narraciones del sistema ──────────────────────────
        $stats = [
            'total'      => Narracion::count(),
            'publicadas' => Narracion::where('estado', 'publicado')->count(),
            'promocion'  => Narracion::where('estado', 'promocion')->count(),
            'borradores' => Narracion::where('estado', 'borrador')->count(),
            'usuarios'   => \App\Models\User::count(),
        ];
        $suscriptores = Subscriber::orderByRaw('verified_at IS NULL ASC, created_at DESC')->get();

    } elseif ($user->isEditor()) {
        // ── Editor: solo sus propias narraciones ──────────────────────────────
        $q = fn() => Narracion::where('user_id', $user->id);
        $stats = [
            'total'      => $q()->count(),
            'publicadas' => $q()->where('estado', 'publicado')->count(),
            'promocion'  => $q()->where('estado', 'promocion')->count(),
            'borradores' => $q()->where('estado', 'borrador')->count(),
        ];
        $suscriptores = Subscriber::orderByRaw('verified_at IS NULL ASC, created_at DESC')->get();

    } else {
        // ── Lector: narraciones accesibles según suscripción ──────────────────
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
})->middleware('auth')->name('dashboard');

// Solicitud de rol de editor (lectors)
Route::post('/dashboard/solicitar-editor', function (Request $request) {
    $user = auth()->user();

    // Guardar en sesión para no permitir múltiples envíos
    if (session('editor_solicitado')) {
        return back()->with('info', 'Ya enviaste una solicitud. El administrador la revisará pronto.');
    }

    Mail::raw(
        "El usuario \"{$user->name}\" ({$user->email}) solicita el rol de editor en Memorias sin orden.\n\n" .
        "Motivo: " . $request->input('motivo', 'Sin indicar') . "\n\n" .
        "Miembro desde: " . $user->created_at->format('d/m/Y'),
        fn ($m) => $m->to('hola@artacho.org')->subject('Solicitud de rol de editor')
    );

    session(['editor_solicitado' => true]);

    return back()->with('success', 'Solicitud enviada. El administrador te responderá por email.');
})->middleware('auth')->name('dashboard.solicitar-editor');

Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::get('/profile/password', [App\Http\Controllers\ProfileController::class, 'editPassword'])->middleware('auth')->name('profile.password.edit');
Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->middleware('auth')->name('profile.password.update');

// ─── Administración (solo admin/editor) ─────────────────────────────────────

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dictado por voz
    Route::get('/dictar', [App\Http\Controllers\DictadoController::class, 'index'])->name('dictar');
    Route::post('/dictar/guardar', [App\Http\Controllers\DictadoController::class, 'guardar'])->name('dictar.guardar');

    // Narraciones
    Route::get('/narraciones', [NarracionController::class, 'adminIndex'])->name('narraciones.index');
    Route::get('/narraciones/create', [NarracionController::class, 'create'])->name('narraciones.create');
    Route::post('/narraciones', [NarracionController::class, 'store'])->name('narraciones.store');
    Route::get('/narraciones/{slug}/edit', [NarracionController::class, 'edit'])->name('narraciones.edit');
    Route::put('/narraciones/{slug}', [NarracionController::class, 'update'])->name('narraciones.update');
    Route::delete('/narraciones/{slug}', [NarracionController::class, 'destroy'])->name('narraciones.destroy');
    Route::post('/narraciones/autosave', [NarracionController::class, 'autosave'])->name('narraciones.autosave');
    Route::post('/narraciones/upload-image', [NarracionController::class, 'uploadImage'])->name('narraciones.uploadImage');

    // Usuarios (solo admin)
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [App\Http\Controllers\UserController::class, 'updateRole'])->name('users.role');

    // Líneas argumentales (admin + editor sobre las propias)
    Route::get('/lineas', [App\Http\Controllers\LineaController::class, 'index'])->name('lineas.index');
    Route::get('/lineas/create', [App\Http\Controllers\LineaController::class, 'create'])->name('lineas.create');
    Route::post('/lineas', [App\Http\Controllers\LineaController::class, 'store'])->name('lineas.store');
    Route::get('/lineas/{linea}/edit', [App\Http\Controllers\LineaController::class, 'edit'])->name('lineas.edit');
    Route::put('/lineas/{linea}', [App\Http\Controllers\LineaController::class, 'update'])->name('lineas.update');
    Route::delete('/lineas/{linea}', [App\Http\Controllers\LineaController::class, 'destroy'])->name('lineas.destroy');
    // Nodos
    Route::post('/lineas/{linea}/nodos', [App\Http\Controllers\LineaController::class, 'addNodo'])->name('lineas.nodos.add');
    Route::delete('/lineas/{linea}/nodos/{nodo}', [App\Http\Controllers\LineaController::class, 'removeNodo'])->name('lineas.nodos.remove');
    // Reordenar (lineal) — drag & drop
    Route::post('/lineas/{linea}/reordenar', [App\Http\Controllers\LineaController::class, 'reordenar'])->name('lineas.reordenar');
    // Conexiones (ramificada)
    Route::post('/lineas/{linea}/conexiones', [App\Http\Controllers\LineaController::class, 'addConexion'])->name('lineas.conexiones.add');
    Route::delete('/lineas/{linea}/conexiones/{conexion}', [App\Http\Controllers\LineaController::class, 'removeConexion'])->name('lineas.conexiones.remove');
});

// Índice público de líneas argumentales
Route::get('/lineas', [App\Http\Controllers\LineaController::class, 'publicIndex'])->name('lineas.public');
