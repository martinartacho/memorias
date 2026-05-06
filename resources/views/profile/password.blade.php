@extends('layouts.literario')

@section('title', 'Cambiar Contraseña - Memorias sin orden')

@section('content')
<div class="container">
  <div class="max-w-2xl mx-auto mt-16">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <div class="mb-8">
        <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
          Cambiar Contraseña
        </h1>
        <p class="text-stone-600">Actualiza tu contraseña de acceso</p>
      </div>

      @if (session('status') === 'password-updated')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
          <div class="flex items-center">
            <i class="bi bi-check-circle text-green-600 mr-3"></i>
            <p class="text-green-800 font-sans">Contraseña actualizada correctamente.</p>
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('profile.password.update') }}">
        @csrf
        @method('PUT')

        <!-- Current Password -->
        <div class="mb-6">
          <label for="current_password" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Contraseña Actual
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-lock text-stone-400"></i>
            </div>
            <input id="current_password" 
                   type="password" 
                   class="w-full pl-10 pr-4 py-3 text-base border border-stone-300 rounded-md focus:ring-2 focus:ring-stone-900 focus:border-stone-900 font-sans text-stone-900 placeholder-stone-400" 
                   name="current_password" 
                   required 
                   autocomplete="current-password"
                   placeholder="•••••••••">
          </div>
          
          @error('current_password')
            <p class="mt-1 text-sm text-red-600 font-sans">{{ $message }}</p>
          @enderror
        </div>

        <!-- New Password -->
        <div class="mb-6">
          <label for="password" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Nueva Contraseña
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-shield-lock text-stone-400"></i>
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
          <label for="password_confirmation" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Confirmar Nueva Contraseña
          </label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-shield-check text-stone-400"></i>
            </div>
            <input id="password_confirmation" 
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

        <!-- Password Requirements -->
        <div class="mb-6 p-4 bg-stone-50 rounded-md border border-stone-200">
          <div class="flex items-start">
            <i class="bi bi-info-circle text-stone-600 mr-3 mt-0.5"></i>
            <div class="text-sm text-stone-600 font-sans">
              <p class="mb-2">
                <strong>Requisitos de contraseña:</strong>
              </p>
              <ul class="list-disc list-inside space-y-1 ml-4">
                <li>Mínimo 8 caracteres</li>
                <li>Debe coincidir con la confirmación</li>
                <li>Usa letras, números y símbolos</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
          <a href="{{ route('profile.edit') }}" 
             class="text-stone-600 hover:text-stone-900 font-sans transition-colors">
            <i class="bi bi-arrow-left mr-2"></i>
            Volver al Perfil
          </a>
          
          <button type="submit" 
                  class="flex items-center px-6 py-3 bg-stone-900 text-stone-100 font-sans text-sm font-medium tracking-wide uppercase hover:bg-stone-800 transition-colors rounded-md">
            <i class="bi bi-shield-check mr-2"></i>
            Cambiar Contraseña
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
