@extends('layouts.literario')

@section('title', 'Enlace Enviado - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto mt-16">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <div class="text-center mb-8">
        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="bi bi-envelope-check text-green-600 text-2xl"></i>
        </div>
        <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
          Enlace Enviado
        </h1>
        <p class="text-stone-600">
          Hemos enviado un enlace de restablecimiento de contraseña a tu correo electrónico.
        </p>
      </div>

      <div class="bg-stone-50 rounded-lg p-4 mb-6">
        <div class="flex items-start">
          <i class="bi bi-info-circle text-stone-600 mr-3 mt-0.5"></i>
          <div class="text-sm text-stone-600 font-sans">
            <p class="mb-2">
              <strong>Importante:</strong> El enlace de restablecimiento expirará en {{ config('auth.passwords.users.expire', 60) }} minutos.
            </p>
            <p>
              Si no recibes el email, revisa tu carpeta de spam o correos no deseados.
            </p>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="space-y-3">
        <a href="{{ route('login') }}" 
           class="w-full flex justify-center items-center px-4 py-3 bg-stone-900 text-stone-100 font-sans text-sm font-medium tracking-wide uppercase hover:bg-stone-800 transition-colors rounded-md">
          <i class="bi bi-arrow-left mr-2"></i>
          Volver al Inicio de Sesión
        </a>
        
        <a href="{{ route('password.request') }}" 
           class="w-full flex justify-center items-center px-4 py-2 border border-stone-300 text-stone-700 font-sans text-sm font-medium tracking-wide hover:bg-stone-50 transition-colors rounded-md">
          <i class="bi bi-envelope mr-2"></i>
          Enviar otro enlace
        </a>
      </div>
    </div>

    <!-- Help Section -->
    <div class="text-center mt-6">
      <p class="text-sm text-stone-500 font-sans">
        ¿Necesitas ayuda? 
        <a href="#" class="text-stone-700 hover:text-stone-900 font-medium transition-colors">
          Contacta al administrador
        </a>
      </p>
    </div>
  </div>
</main>
@endsection
