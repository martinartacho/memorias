@extends('layouts.literario')

@section('title', 'Restablecer Contraseña - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto mt-16">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">Restablecer Contraseña</h1>
      <p class="text-stone-600">Crea una nueva contraseña para tu cuenta</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
      <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Email -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Email -->
        <div class="mb-4">
          <label for="email" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Correo Electrónico
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-envelope text-stone-400"></i>
            </div>
            <input id="email" 
                   type="email" 
                   class="w-full pl-10 pr-4 py-3 text-base border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                   name="email" 
                   value="{{ $email ?? old('email') }}" 
                   required 
                   autocomplete="email"
                   placeholder="tu@email.com">
          </div>
          
          @error('email')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="password" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Nueva Contraseña
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-lock text-stone-400"></i>
            </div>
            <input id="password" 
                   type="password" 
                   class="w-full pl-10 pr-4 py-3 text-base border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   placeholder="•••••••••">
          </div>
          
          @error('password')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
          <label for="password-confirm" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Confirmar Nueva Contraseña
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-shield-lock text-stone-400"></i>
            </div>
            <input id="password-confirm" 
                   type="password" 
                   class="w-full pl-10 pr-4 py-3 text-base border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   placeholder="•••••••••">
          </div>
          
          @error('password_confirmation')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
          <button type="submit" 
                  class="w-full flex justify-center items-center px-4 py-2 bg-stone-900 text-stone-100 font-sans text-sm font-medium tracking-wide uppercase hover:bg-stone-800 transition-colors rounded-md">
            <i class="bi bi-shield-check mr-2"></i>
            Restablecer Contraseña
          </button>
        </div>
      </form>
    </div>

    <!-- Back to Login -->
    <div class="text-center mt-6">
      <a href="{{ route('login') }}" 
         class="text-sm text-stone-600 hover:text-stone-900 font-sans transition-colors">
        <i class="bi bi-arrow-left mr-2"></i>
        Volver al Inicio de Sesión
      </a>
    </div>
  </div>
</main>
@endsection
