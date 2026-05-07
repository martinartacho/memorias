@extends('layouts.literario')

@section('title', 'Feedback - Memorias sin orden')

@section('content')
<main class="container">
  <div class="max-w-2xl mx-auto py-16">
    <div class="bg-white rounded-lg border border-stone-300 p-8">
      <!-- Icono de feedback -->
      <div class="text-stone-400 mb-6 text-center">
        <span class="material-icons text-6xl">feedback</span>
      </div>

      <!-- Título -->
      <h2 class="text-3xl font-serif font-bold text-stone-900 mb-4 text-center">
        ¿Te gusta esta narración?
      </h2>

      <!-- Información de la narración -->
      <div class="bg-stone-50 rounded-lg p-6 mb-6">
        <h3 class="text-xl font-serif font-bold text-stone-900 mb-2">
          {{ $narracion->titulo }}
        </h3>
        <p class="text-stone-600 mb-4">
          {!! Str::limit(strip_tags($narracion->contenido), 150) !!}
        </p>
        <div class="flex items-center justify-between text-sm text-stone-500">
          <span>Por {{ $narracion->user->name }}</span>
          <span>{{ $narracion->fecha_publicacion->format('d/m/Y') }}</span>
        </div>
      </div>

      <!-- Formulario de Feedback -->
      <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="narracion_id" value="{{ $narracion->id }}">
        
        <!-- Tipo de Feedback -->
        <div>
          <label class="block font-sans text-sm font-medium text-stone-700 mb-2">
            Tu opinión <span class="text-red-600">*</span>
          </label>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50 transition-colors">
              <input type="radio" name="tipo_feedback" value="excelente" required class="mr-3">
              <div>
                <div class="font-sans font-medium text-stone-900">Excelente ❤️</div>
                <div class="font-sans text-sm text-stone-600">Me encanta</div>
              </div>
            </label>
            
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50 transition-colors">
              <input type="radio" name="tipo_feedback" value="bueno" required class="mr-3">
              <div>
                <div class="font-sans font-medium text-stone-900">Bueno 👍</div>
                <div class="font-sans text-sm text-stone-600">Me gusta</div>
              </div>
            </label>
            
            <label class="flex items-center p-4 border border-stone-300 rounded-lg cursor-pointer hover:bg-stone-50 transition-colors">
              <input type="radio" name="tipo_feedback" value="regular" required class="mr-3">
              <div>
                <div class="font-sans font-medium text-stone-900">Regular 😐</div>
                <div class="font-sans text-sm text-stone-600">Está bien</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Comentario -->
        <div>
          <label class="block font-sans text-sm font-medium text-stone-700 mb-2">
            Comentario (opcional)
          </label>
          <textarea 
            name="comentario" 
            rows="4" 
            class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-transparent font-sans"
            placeholder="Cuéntanos qué te pareció esta narración..."></textarea>
        </div>

        <!-- Email -->
        <div>
          <label class="block font-sans text-sm font-medium text-stone-700 mb-2">
            Tu email <span class="text-red-600">*</span>
          </label>
          <input 
            type="email" 
            name="email" 
            required
            class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-transparent font-sans"
            placeholder="tu@email.com"
            value="{{ auth()->check() ? auth()->user()->email : old('email') }}">
        </div>

        <!-- Botones de acción -->
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="{{ route('narraciones.show', $narracion->slug) }}" 
             class="inline-flex items-center justify-center px-6 py-3 font-sans text-sm font-medium text-stone-600 bg-white border border-stone-300 rounded-lg hover:bg-stone-50 transition-colors">
            <span class="material-icons mr-2">arrow_back</span>
            Volver a la narración
          </a>
          
          <button type="submit" 
                  class="inline-flex items-center justify-center px-6 py-3 font-sans text-sm font-medium text-white bg-stone-700 rounded-lg hover:bg-stone-800 transition-colors">
            <span class="material-icons mr-2">send</span>
            Enviar Feedback
          </button>
        </div>
      </form>

      <!-- Mensaje de éxito -->
      @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
          <div class="flex items-start">
            <span class="material-icons text-green-600 mr-3">check_circle</span>
            <div class="text-sm text-green-800">
              <p class="font-medium">{{ session('success') }}</p>
            </div>
          </div>
        </div>
      @endif

      <!-- Mensaje de error -->
      @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 rounded-lg border border-red-200">
          <div class="flex items-start">
            <span class="material-icons text-red-600 mr-3">error</span>
            <div class="text-sm text-red-800">
              <p class="font-medium">{{ session('error') }}</p>
            </div>
          </div>
        </div>
      @endif

      <!-- Mensaje informativo -->
      <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start">
          <span class="material-icons text-blue-600 mr-3">info</span>
          <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Tu feedback es importante</p>
            <p>Ayuda al autor a mejorar sus narraciones y crea una comunidad más activa. Todos los comentarios son revisados antes de publicarse.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
