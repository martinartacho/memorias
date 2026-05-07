@extends('layouts.literario')

@section('title', 'Seguir autor - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-2xl mx-auto py-16">
    <div class="bg-white rounded-lg border border-stone-300 p-8 text-center">
      <!-- Icono de candado -->
      <div class="text-stone-400 mb-6">
        <i class="bi bi-lock-fill text-6xl"></i>
      </div>

      <!-- Título -->
      <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4">
        Contenido exclusivo para seguidores
      </h2>

      <!-- Información de la narración -->
      <div class="bg-stone-50 rounded-lg p-6 mb-6 text-left">
        <h3 class="text-xl font-serif font-bold text-stone-900 mb-2">
          {{ $narracion->titulo }}
        </h3>
        <p class="text-stone-600 mb-4">
          {!! Str::limit(strip_tags($narracion->contenido), 150) !!}
        </p>
        <div class="flex items-center text-sm text-stone-500">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
          Por {{ $narracion->user->name }}
          <span class="mx-2">·</span>
          {{ $narracion->fecha_publicacion->format('d M Y') }}
        </div>
      </div>

      <!-- Mensaje explicativo -->
      <p class="text-stone-600 mb-8">
        Esta narración está disponible exclusivamente para seguidores del autor. 
        Para poder leerla, necesitas seguir a {{ $narracion->user->name }}.
      </p>

      <!-- Botones de acción -->
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        @if(auth()->check())
          <button class="px-6 py-3 bg-stone-900 text-white font-sans text-sm tracking-wider uppercase hover:bg-stone-800 transition-colors">
            <i class="bi bi-person-plus mr-2"></i>
            Seguir a {{ $narracion->user->name }}
          </button>
        @else
          <a href="{{ route('login') }}" 
             class="px-6 py-3 bg-stone-900 text-white font-sans text-sm tracking-wider uppercase hover:bg-stone-800 transition-colors inline-flex items-center">
            <i class="bi bi-box-arrow-in-right mr-2"></i>
            Iniciar sesión para seguir
          </a>
        @endif
        
        <a href="{{ route('narraciones.index') }}" 
           class="px-6 py-3 bg-white border border-stone-300 text-stone-700 font-sans text-sm tracking-wider uppercase hover:bg-stone-50 transition-colors">
          Volver a narraciones
        </a>
      </div>

      <!-- Información adicional -->
      <div class="mt-8 text-sm text-stone-500">
        <p>Al seguir a un autor, recibirás:</p>
        <ul class="mt-2 text-left max-w-md mx-auto space-y-1">
          <li>• Acceso a sus narraciones exclusivas para seguidores</li>
          <li>• Notificaciones de nuevas publicaciones</li>
          <li>• Contenido personalizado en tu feed</li>
        </ul>
      </div>
    </div>
  </div>
</main>
@endsection
