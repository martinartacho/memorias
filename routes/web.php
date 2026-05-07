<?php

use App\Http\Controllers\NarracionController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [NarracionController::class, 'index'])->name('home');
Route::get('/narraciones', [NarracionController::class, 'index'])->name('narraciones.index');
Route::get('/narracion/{slug}', [NarracionController::class, 'show'])->name('narraciones.show');
Route::get('/narracion/{slug}/follow-required', [NarracionController::class, 'followRequired'])->name('narraciones.follow-required');

// Rutas de autenticación
Auth::routes();

// Dashboard para usuarios autenticados
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Rutas de perfil de usuario
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->middleware('auth')->name('profile.edit');
Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->middleware('auth')->name('profile.update');
Route::get('/profile/password', [App\Http\Controllers\ProfileController::class, 'editPassword'])->middleware('auth')->name('profile.password.edit');
Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->middleware('auth')->name('profile.password.update');

// Rutas de administración (protegidas)
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/narraciones', [NarracionController::class, 'adminIndex'])->name('narraciones.index');
    Route::get('/narraciones/create', [NarracionController::class, 'create'])->name('narraciones.create');
    Route::post('/narraciones', [NarracionController::class, 'store'])->name('narraciones.store');
    Route::get('/narraciones/{slug}/edit', [NarracionController::class, 'edit'])->name('narraciones.edit');
    Route::put('/narraciones/{slug}', [NarracionController::class, 'update'])->name('narraciones.update');
    Route::delete('/narraciones/{slug}', [NarracionController::class, 'destroy'])->name('narraciones.destroy');
    Route::post('/narraciones/autosave', [NarracionController::class, 'autosave'])->name('narraciones.autosave');
    Route::post('/narraciones/upload-image', [NarracionController::class, 'uploadImage'])->name('narraciones.uploadImage');
});
