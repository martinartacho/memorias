<?php

use App\Http\Controllers\NarracionController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [NarracionController::class, 'index'])->name('home');
Route::get('/narraciones', [NarracionController::class, 'index'])->name('narraciones.index');
Route::get('/narracion/{slug}', [NarracionController::class, 'show'])->name('narraciones.show');

// Rutas de administración (temporalmente sin protección)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/narraciones', [NarracionController::class, 'adminIndex'])->name('narraciones.index');
    Route::get('/narraciones/create', [NarracionController::class, 'create'])->name('narraciones.create');
    Route::post('/narraciones', [NarracionController::class, 'store'])->name('narraciones.store');
    Route::get('/narraciones/{id}/edit', [NarracionController::class, 'edit'])->name('narraciones.edit');
    Route::put('/narraciones/{id}', [NarracionController::class, 'update'])->name('narraciones.update');
    Route::delete('/narraciones/{id}', [NarracionController::class, 'destroy'])->name('narraciones.destroy');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
