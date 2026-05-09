@extends('layouts.literario')

@section('title', 'Eliminar Cuenta - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-md mx-auto mt-16">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
        <span class="material-icons text-red-500 mr-2">warning</span>
        Eliminar Cuenta
      </h1>
      <p class="text-stone-600">Esta acción es permanente e irreversible</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-6">
      @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <span class="material-icons text-red-600 mr-2">error</span>
            {{ $errors->first() }}
          </div>
        </div>
      @endif

      <form method="POST" action="{{ route('account.destroy') }}">
        @csrf
        @method('DELETE')

        <div class="mb-6">
          <p class="text-stone-600 mb-4 font-medium">¿Estás seguro de que deseas eliminar tu cuenta?</p>
          <p class="text-sm text-stone-500 mb-4">Esta acción eliminará permanentemente:</p>
          <ul class="list-disc list-inside text-sm text-stone-600 space-y-1 ml-4">
            <li>Tu perfil y toda tu información personal</li>
            <li>Todas tus narraciones y su contenido</li>
            <li>Tus seguidores y los usuarios que sigues</li>
            <li>Todos tus comentarios y feedback</li>
            <li>Tu historial de actividad en la plataforma</li>
          </ul>
        </div>

        <!-- Password Confirmation -->
        <div class="mb-6">
          <label for="password" class="block text-sm font-sans font-medium text-stone-700 mb-2">
            Confirma tu contraseña para continuar
          </label>
          <input 
            id="password" 
            name="password" 
            type="password" 
            class="w-full px-3 py-2 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
            placeholder="Ingresa tu contraseña"
            required
          >
          @error('password')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
          <a href="{{ route('dashboard') }}" 
             class="flex-1 text-center px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium">
            Cancelar y Volver
          </a>
          <button type="submit" 
                  class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
            <span class="material-icons mr-2">delete_forever</span>
            Eliminar Mi Cuenta
          </button>
        </div>
      </form>
    </div>
  </div>
</main>
@endsection
