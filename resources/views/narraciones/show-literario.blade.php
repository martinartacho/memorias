@extends('layouts.literario')

@section('title', $narracion->titulo . ' - Memorias sin orden')

@section('content')
<main class="container">
  <div class="featured">
    <div class="featured-meta">
      <div class="section-label">Narración</div>
      <div class="featured-number">{{ str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT) }}</div>
      <div class="featured-category">Narración · {{ $narracion->fecha_publicacion->format('Y') }}</div>
    </div>
    <div class="featured-divider"></div>
    <div class="featured-text">
      <div class="featured-author">
        Publicado · {{ $narracion->fecha_publicacion->format('F Y') }}
        <span class="ml-3">
          @switch($narracion->permiso_lectura)
            @case('publico')
              <i class="bi bi-globe text-blue-600" title="Público"></i>
              <span class="text-blue-600 text-sm ml-1">Público</span>
              @break
            @case('seguidores')
              <i class="bi bi-people-fill text-purple-600" title="Solo seguidores"></i>
              <span class="text-purple-600 text-sm ml-1">Solo seguidores</span>
              @break
          @endswitch
        </span>
      </div>
      <h2>{{ $narracion->titulo }}</h2>
      <div class="modal-body prose prose-lg max-w-none">
        {!! $narracion->contenido !!}
      </div>
      
      <div class="mt-8 flex items-center justify-between">
        <a href="{{ route('narraciones.index') }}" class="read-more">← Volver a todas las narraciones</a>
        <div class="flex items-center space-x-4">
            <!-- Follow/Unfollow Author Button -->
            @if(auth()->check() && auth()->id() != $narracion->user_id)
                <button 
                    id="follow-btn" 
                    class="follow-btn inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-sans rounded-lg hover:bg-purple-700 transition-colors"
                    data-author-id="{{ $narracion->user_id }}"
                    data-following="{{ auth()->user()->following()->where('followed_id', $narracion->user_id)->exists() ? 'true' : 'false' }}">
                    <i class="bi bi-person-plus mr-2"></i>
                    <span class="follow-text">Seguir autor</span>
                </button>
            @endif
            
            <!-- Like Button -->
            <button 
                id="like-btn" 
                class="like-btn inline-flex items-center px-4 py-2 bg-red-500 text-white text-sm font-sans rounded-lg hover:bg-red-600 transition-colors"
                data-narracion-id="{{ $narracion->id }}">
                <i class="bi bi-heart mr-2"></i>
                <span>Me Gusta</span>
            </button>
            
            <!-- Share Button (placeholder) -->
            <button class="read-more" title="Compartir" onclick="alert('Función de compartir próximamente')">
                Compartir →
            </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Related Stories -->
  <div class="grid-label">
    Otras memorias <span>Explora más historias</span>
  </div>

  <div class="stories-grid">
    @php
        // Mostrar TODAS las narraciones publicadas como relacionadas
        // El filtrado por permisos se maneja al hacer clic
        $related = \App\Models\Narracion::where('id', '!=', $narracion->id)
            ->publicado()
            ->orderByFecha()
            ->take(3)
            ->get();
    @endphp
    @foreach($related as $key => $story)
        <div class="story-card" onclick="window.location.href='{{ route('narraciones.show', $story->slug) }}'">
          <div class="card-num">{{ str_pad($key + 1, 2, '0', STR_PAD_LEFT) }}</div>
          <div class="card-tag">
            Narración
            <span class="ml-2">
              @switch($story->permiso_lectura)
                @case('publico')
                  <i class="bi bi-globe text-blue-600" title="Público"></i>
                  @break
                @case('seguidores')
                  <i class="bi bi-people-fill text-purple-600" title="Solo seguidores"></i>
                  @break
              @endswitch
            </span>
          </div>
          <div class="card-title">{{ $story->titulo }}</div>
          <div class="card-author">{{ $story->fecha_publicacion->format('F Y') }}</div>
          <p class="card-excerpt">{!! Str::limit(strip_tags($story->contenido), 120) !!}</p>
        </div>
    @endforeach
    
    @if($related->count() < 3)
        @for($i = $related->count(); $i < 3; $i++)
            <div class="story-card opacity-50">
              <div class="card-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</div>
              <div class="card-tag">Próximamente</div>
              <div class="card-title">Nueva historia</div>
              <div class="card-author">En construcción</div>
              <p class="card-excerpt">Pronto podrás disfrutar de nuevas narraciones...</p>
            </div>
        @endfor
    @endif
  </div>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de Follow/Unfollow
    const followBtn = document.getElementById('follow-btn');
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            const authorId = this.dataset.authorId;
            const isFollowing = this.dataset.following === 'true';
            
            fetch(`/follow/toggle/${authorId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                // Actualizar estado del botón
                this.dataset.following = data.following;
                const icon = this.querySelector('i');
                const text = this.querySelector('.follow-text');
                
                if (data.following) {
                    icon.className = 'bi bi-person-check mr-2';
                    text.textContent = 'Siguiendo';
                    this.classList.remove('bg-purple-600', 'hover:bg-purple-700');
                    this.classList.add('bg-green-600', 'hover:bg-green-700');
                } else {
                    icon.className = 'bi bi-person-plus mr-2';
                    text.textContent = 'Seguir autor';
                    this.classList.remove('bg-green-600', 'hover:bg-green-700');
                    this.classList.add('bg-purple-600', 'hover:bg-purple-700');
                }
                
                // Mostrar mensaje
                showToast(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error al procesar la solicitud');
            });
        });
    }
    
    // Funcionalidad de Like
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn) {
        let isLiked = false;
        
        likeBtn.addEventListener('click', function() {
            const narracionId = this.dataset.narracionId;
            const icon = this.querySelector('i');
            
            isLiked = !isLiked;
            
            if (isLiked) {
                icon.className = 'bi bi-heart-fill mr-2';
                this.classList.remove('bg-red-500', 'hover:bg-red-600');
                this.classList.add('bg-red-600', 'hover:bg-red-700');
                showToast('¡Te gusta esta narración!');
            } else {
                icon.className = 'bi bi-heart mr-2';
                this.classList.remove('bg-red-600', 'hover:bg-red-700');
                this.classList.add('bg-red-500', 'hover:bg-red-600');
                showToast('Has quitado el "Me Gusta"');
            }
            
            // Mostrar alerta temporal mientras se implementa la funcionalidad completa
            showToast('¡Función "Me Gusta" próximamente disponible!');
        });
    }
});

// Función para mostrar notificaciones toast
function showToast(message) {
    // Crear elemento toast
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-stone-800 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300';
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Animación de entrada
    setTimeout(() => {
        toast.style.opacity = '1';
    }, 100);
    
    // Eliminar después de 3 segundos
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>
@endpush
