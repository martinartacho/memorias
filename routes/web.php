<?php

use App\Http\Controllers\NarracionController;
use App\Http\Controllers\FollowController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/', [NarracionController::class, 'index'])->name('home');
Route::get('/narraciones', [NarracionController::class, 'index'])->name('narraciones.index');
Route::get('/narracion/{slug}', [NarracionController::class, 'show'])->name('narraciones.show');
Route::get('/narracion/{slug}/follow-required', [NarracionController::class, 'followRequired'])->name('narraciones.follow-required');
Route::get('/narracion/{slug}/feedback', [NarracionController::class, 'feedbackForm'])->name('narraciones.feedback');
Route::post('/feedback', [NarracionController::class, 'storeFeedback'])->name('feedback.store');

// Rutas para sistema de seguimiento
Route::middleware('auth')->group(function () {
    Route::post('/follow/{authorId}', [FollowController::class, 'follow'])->name('follow.follow');
    Route::post('/unfollow/{authorId}', [FollowController::class, 'unfollow'])->name('follow.unfollow');
    Route::post('/follow/toggle/{authorId}', [FollowController::class, 'toggle'])->name('follow.toggle');
    Route::get('/following', [FollowController::class, 'following'])->name('follow.following');
    Route::get('/followers', [FollowController::class, 'followers'])->name('follow.followers');
});

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

// Rutas de gestión de cuenta
Route::get('/account/delete', [App\Http\Controllers\AccountController::class, 'delete'])->middleware('auth')->name('account.delete');
Route::delete('/account', [App\Http\Controllers\AccountController::class, 'destroy'])->middleware('auth')->name('account.destroy');

// Rutas de verificación de email
Route::get('/verify-email/{token}', [App\Http\Controllers\EmailVerificationController::class, 'verify'])->name('verify.email');
Route::get('/verify-email', [App\Http\Controllers\EmailVerificationController::class, 'notice'])->name('verify.notice');
Route::post('/verify-email/resend', [App\Http\Controllers\EmailVerificationController::class, 'resend'])->name('verify.resend');

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
    
    // Rutas de gestión de usuarios y feedback
    Route::get('/followers', [App\Http\Controllers\AdminController::class, 'followers'])->name('followers');
    Route::get('/feedback', [App\Http\Controllers\AdminController::class, 'feedback'])->name('feedback');
    Route::post('/feedback/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveFeedback'])->name('feedback.approve');
    Route::post('/feedback/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectFeedback'])->name('feedback.reject');
    Route::delete('/feedback/{id}', [App\Http\Controllers\AdminController::class, 'deleteFeedback'])->name('feedback.delete');
});
