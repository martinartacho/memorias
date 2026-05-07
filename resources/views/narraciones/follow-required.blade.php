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
          <!-- Botón de seguir autor -->
          <button 
            id="follow-author-btn"
            class="follow-author-btn inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white font-sans text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors"
            data-author-id="{{ $narracion->user_id }}"
            data-following="{{ auth()->user()->following()->where('followed_id', $narracion->user_id)->exists() ? 'true' : 'false' }}">
            <i class="bi bi-person-plus mr-2"></i>
            <span class="follow-text">Seguir autor para leer</span>
          </button>
          
          <a href="{{ route('narraciones.index') }}" 
             class="inline-flex items-center justify-center px-6 py-3 border border-stone-300 text-stone-700 font-sans text-sm font-medium rounded-lg hover:bg-stone-50 transition-colors">
            <i class="bi bi-arrow-left mr-2"></i>
            Volver a narraciones
          </a>
        @else
          <a href="{{ route('login') }}" 
             class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-sans text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
            <i class="bi bi-person-plus mr-2"></i>
            Iniciar sesión para seguir
          </a>
          <a href="{{ route('register') }}" 
             class="inline-flex items-center justify-center px-6 py-3 border border-stone-300 text-stone-700 font-sans text-sm font-medium rounded-lg hover:bg-stone-50 transition-colors">
            Crear cuenta
          </a>
        @endif
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de Follow en vista follow-required (simplificada con alerta)
    const followBtn = document.getElementById('follow-author-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            alert('Función de seguimiento en construcción. Próximamente podrás seguir a este autor para acceder a su contenido exclusivo.');
        });
    }
});

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-stone-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 100);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>
@endpush
