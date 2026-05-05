@extends('layouts.literario')

@section('title', 'Registrarse - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto mt-16">
    <div class="text-center mb-12">
      <h1 class="text-4xl font-serif font-bold text-stone-900 mb-4">Crear Cuenta</h1>
      <p class="text-stone-600">Únete a nuestra comunidad literaria</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-6">
          <label for="name" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Nombre Completo
          </label>
          <input id="name" 
                 type="text" 
                 class="w-full px-4 py-3 border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                 name="name" 
                 value="{{ old('name') }}" 
                 required 
                 autocomplete="name" 
                 autofocus
                 placeholder="José Martín Artacho">
          
          @error('name')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Email -->
        <div class="mb-6">
          <label for="email" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Correo Electrónico
          </label>
          <input id="email" 
                 type="email" 
                 class="w-full px-4 py-3 border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                 name="email" 
                 value="{{ old('email') }}" 
                 required 
                 autocomplete="email"
                 placeholder="tu@email.com">
          
          @error('email')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div class="mb-6">
          <label for="password" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Contraseña
          </label>
          <input id="password" 
                 type="password" 
                 class="w-full px-4 py-3 border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                 name="password" 
                 required 
                 autocomplete="new-password"
                 placeholder="••••••••">
          
          @error('password')
            <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
          <label for="password-confirm" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Confirmar Contraseña
          </label>
          <input id="password-confirm" 
                 type="password" 
                 class="w-full px-4 py-3 border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                 name="password_confirmation" 
                 required 
                 autocomplete="new-password"
                 placeholder="••••••••">
        </div>

        <!-- Submit Button -->
        <div class="mb-6">
          <button type="submit" 
                  class="w-full flex justify-center items-center px-6 py-3 bg-stone-900 text-stone-100 font-sans text-sm font-medium tracking-wide uppercase hover:bg-stone-800 transition-colors rounded-md">
            <i class="bi bi-person-plus mr-2"></i>
            Crear Cuenta
          </button>
        </div>
      </form>
    </div>

    <!-- Login Link -->
    @if (Route::has('login'))
      <div class="text-center mt-8">
        <p class="text-stone-600 font-sans">
          ¿Ya tienes una cuenta? 
          <a href="{{ route('login') }}" 
             class="text-stone-900 hover:text-stone-700 font-medium transition-colors">
            Inicia sesión aquí
          </a>
        </p>
      </div>
    @endif
  </div>
</main>
@endsection
