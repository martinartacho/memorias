@extends('layouts.literario')

@section('title', $narracion->titulo . ' - Memorias sin orden')

@section('content')
<main class="container">
  <!-- Alert Messages -->
  @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
      {!! session('success') !!}
    </div>
  @endif
  
  @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
      <span class="material-icons text-red-600 mr-2">error</span>
      {{ session('error') }}
    </div>
  @endif
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
              <span class="material-icons text-blue-600" title="Público">public</span>
              <span class="text-blue-600 text-sm ml-1">Público</span>
              @break
            @case('seguidores')
              <span class="material-icons text-purple-600" title="Solo seguidores">group</span>
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
        <a href="{{ route('narraciones.index') }}" class="read-more">← Volver</a>
        <div class="flex items-center space-x-4">
            <!-- Follow/Unfollow Author Button -->
            @if(auth()->check() && auth()->id() != $narracion->user_id)
                @php
                    $isFollowing = auth()->user()->following()->where('followed_id', $narracion->user_id)->exists();
                @endphp
                <button 
                    id="follow-btn" 
                    class="follow-btn inline-flex items-center px-4 py-2 {{ $isFollowing ? 'bg-gray-600 hover:bg-gray-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white text-sm font-sans rounded-lg transition-colors"
                    data-author-id="{{ $narracion->user_id }}"
                    data-following="{{ $isFollowing ? 'true' : 'false' }}">
                    <span class="material-icons mr-2">{{ $isFollowing ? 'person_remove' : 'person_add' }}</span>
                    <span class="follow-text">{{ $isFollowing ? 'Siguiendo' : 'Seguir autor' }}</span>
                </button>
            @endif
            
            <!-- Feedback Button -->
            <button onclick="window.location.href='{{ route('narraciones.feedback', $narracion->slug) }}'" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-sans rounded-lg hover:bg-purple-700 transition-colors">
                <span class="material-icons mr-2">thumb_up</span>
                <span>Valorar</span>
            </button>
            
            <!-- Share Button (placeholder) -->
            {{-- <button class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-sans rounded-lg hover:bg-gray-600 transition-colors" 
                    title="Compartir" 
                    onclick="alert('Función de compartir próximamente')">
                <span class="material-icons mr-2">share</span>
                <span>Compartir</span>
            </button> --}}
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
                  <span class="material-icons text-blue-600" title="Público">public</span>
                  @break
                @case('seguidores')
                  <span class="material-icons text-purple-600" title="Solo seguidores">group</span>
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
<!-- Funciones globales -->
<script>
// Función global para cerrar modal
function closeLikeModal() {
    const modal = document.getElementById('like-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.classList.remove('overflow-y-hidden');
    }
}

// Función global para manejar acciones de like
function handleLikeAction(action) {
  const messages = {
    'love': '¡Te encanta esta narración! ❤️',
    'like': '¡Te gusta esta narración! 👍',
    'interesting': '¡Encuentras interesante esta narración! 💡'
  };
  
  // Mostrar mensaje
  showToast(messages[action]);
  
  // Actualizar botón visualmente (con validación)
  const likeBtn = document.getElementById('like-btn');
  if (likeBtn) {
    const icon = likeBtn.querySelector('i');
    if (icon) {
      icon.textContent = 'favorite';
      likeBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
      likeBtn.classList.add('bg-red-600', 'hover:bg-red-700');
    }
  }
  
  // Cerrar modal
  closeLikeModal();
}

document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad de Follow/Unfollow con formulario (sin AJAX)
    const followBtn = document.getElementById('follow-btn');
    
    if (followBtn) {
        followBtn.addEventListener('click', function() {
            const authorId = this.dataset.authorId;
            const isFollowing = this.dataset.following === 'true';
            
            // Crear formulario temporal para enviar petición
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = isFollowing ? `/unfollow/${authorId}` : `/follow/${authorId}`;
            
            // Agregar CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        });
    }
    
    // Funcionalidad de Like con modal
    const likeBtn = document.getElementById('like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            // Abrir modal de Me Gusta
            const modal = document.getElementById('like-modal');
            if (modal) {
                modal.style.display = 'block';
                document.body.classList.add('overflow-y-hidden');
            }
        });
    }
    
    // Cerrar al hacer clic fuera
    window.onclick = function(event) {
        const modal = document.getElementById('like-modal');
        if (event.target == modal) {
            closeLikeModal();
        }
    }
    
    // Event listeners para botones del modal
    const closeBtn = document.getElementById('close-modal-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeLikeModal);
    }
    
    const loveBtn = document.getElementById('love-btn');
    if (loveBtn) {
        loveBtn.addEventListener('click', () => handleLikeAction('love'));
    }
    
    const likeBtnAction = document.getElementById('like-btn-action');
    if (likeBtnAction) {
        likeBtnAction.addEventListener('click', () => handleLikeAction('like'));
    }
    
    const interestingBtn = document.getElementById('interesting-btn');
    if (interestingBtn) {
        interestingBtn.addEventListener('click', () => handleLikeAction('interesting'));
    }
    
    const cancelBtn = document.getElementById('cancel-modal-btn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeLikeModal);
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

function handleLikeAction(action) {
  const messages = {
    'love': '¡Te encanta esta narración! ',
    'like': '¡Te gusta esta narración! ',
    'interesting': '¡Encuentras interesante esta narración! '
  };
  
  // Actualizar botón visualmente
  const likeBtn = document.getElementById('like-btn');
  if (likeBtn) {
    const icon = likeBtn.querySelector('i');
    icon.textContent = 'favorite';
    likeBtn.classList.remove('bg-red-500', 'hover:bg-red-600');
    likeBtn.classList.add('bg-red-600', 'hover:bg-red-700');
  }
  
  // Cerrar modal
  closeLikeModal();
}
</script>
@endpush

<!-- Modal Me Gusta -->
<div id="like-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
  <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
    <div class="p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-serif font-bold text-stone-900">
          <span class="material-icons text-red-500 mr-2">favorite</span>
          Me Gusta
        </h3>
        <button id="close-modal-btn" class="text-stone-400 hover:text-stone-600 transition-colors">
          <span class="material-icons text-xl">close</span>
        </button>
      </div>
      
      <div class="text-center py-6">
        <div class="mb-4">
          <span class="material-icons text-6xl text-red-500">favorite</span>
        </div>
        
        <h4 class="text-lg font-serif font-medium text-stone-900 mb-2">
          ¿Te gusta esta narración?
        </h4>
        
        <p class="text-stone-600 mb-6">
          Tu feedback ayuda al autor a crear más contenido increíble. Próximamente podrás guardar tus preferencias.
        </p>
        
        <div class="space-y-3">
          <button 
            id="love-btn"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-red-500 text-white font-sans font-medium rounded-lg hover:bg-red-600 transition-colors">
            <span class="material-icons mr-2">favorite</span>
            Me encanta ❤️
          </button>
          
          <button 
            id="like-btn-action"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-stone-600 text-white font-sans font-medium rounded-lg hover:bg-stone-700 transition-colors">
            <span class="material-icons mr-2">thumb_up</span>
            Me gusta 👍
          </button>
          
          <button 
            id="interesting-btn"
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-500 text-white font-sans font-medium rounded-lg hover:bg-blue-600 transition-colors">
            <span class="material-icons mr-2">lightbulb</span>
            Interesante 💡
          </button>
        </div>
        
        <div class="mt-4 pt-4 border-t border-stone-200">
          <button 
            id="cancel-modal-btn"
            class="text-stone-600 hover:text-stone-800 font-sans text-sm transition-colors">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
