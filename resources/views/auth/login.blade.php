@extends('layouts.literario')

@section('title', 'Iniciar Sesión - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto mt-16">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">Iniciar Sesión</h1>
      <p class="text-stone-600">Accede a tu colección literaria</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
      <form method="POST" action="{{ route('login') }}">
        @csrf

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
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="email" 
                   autofocus
                   placeholder="tu@email.com">
          </div>
          
          @error('email')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
          <label for="password" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Contraseña
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
                   autocomplete="current-password"
                   placeholder="•••••••••">
          </div>
          
          @error('password')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-6">
          <label class="flex items-center">
            <input type="checkbox" 
                   name="remember" 
                   class="h-4 w-4 text-stone-900 focus:ring-stone-900 border-stone-300 rounded"
                   {{ old('remember') ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-stone-600 font-sans">Recordarme</span>
          </label>
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
          <button type="submit" 
                  class="w-full flex justify-center items-center px-4 py-2 bg-stone-900 text-stone-100 font-sans text-sm font-medium tracking-wide uppercase hover:bg-stone-800 transition-colors rounded-md">
            <i class="bi bi-box-arrow-in-right mr-2"></i>
            Iniciar Sesión
          </button>
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
          <div class="text-center">
            <a href="{{ route('password.request') }}" 
               class="text-sm text-stone-600 hover:text-stone-900 font-sans transition-colors">
              ¿Olvidaste tu contraseña?
            </a>
          </div>
        @endif
      </form>
    </div>

    <!-- Register Link -->
    @if (Route::has('register'))
      <div class="text-center mt-6">
        <p class="text-stone-600 font-sans">
          ¿No tienes una cuenta? 
          <a href="{{ route('register') }}" 
             class="text-stone-900 hover:text-stone-700 font-medium transition-colors">
            Regístrate aquí
          </a>
        </p>
      </div>
    @endif
  </div>
</main>
@endsection
