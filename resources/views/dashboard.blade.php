@extends('layouts.literario')

@section('title', 'Dashboard - Memorias sin orden')

@section('content')
<div class="container">
  <!-- Welcome Section -->
  <div class="max-w-4xl mx-auto mb-12">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-3xl font-serif font-bold text-stone-900 mb-2">
            Hola, {{ Auth::user()->name }}
          </h1>
          <p class="text-stone-600">{{ Auth::user()->email }}</p>
        </div>
        <div class="text-right"> 
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-stone-100 text-stone-800">
            <i class="bi bi-person-circle mr-2"></i>
            {{ Auth::user()->role ?? 'Lector' }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-3 gap-6">
        <!-- Stats -->
        <div class="text-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
          <i class="bi bi-book text-4xl text-stone-600 mb-4"></i>
          <div class="text-3xl font-serif font-bold text-stone-900 mb-2">{{ Auth::user()->narraciones()->where('estado', 'publicado')->count() }}</div>
          <div class="text-sm text-stone-600 font-sans tracking-wide">NARRACIONES</div>
        </div>
        <div class="text-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
          <i class="bi bi-calendar text-4xl text-stone-600 mb-4"></i>
          <div class="text-3xl font-serif font-bold text-stone-900 mb-2">{{ Auth::user()->created_at->format('M Y') }}</div>
          <div class="text-sm text-stone-600 font-sans tracking-wide">MIEMBRO DESDE</div>
        </div>
        <div class="text-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
          <i class="bi bi-clock text-4xl text-stone-600 mb-4"></i>
          <div class="text-3xl font-serif font-bold text-stone-900 mb-2">Reciente</div>
          <div class="text-sm text-stone-600 font-sans tracking-wide">ÚLTIMA VISITA</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
  <div class="max-w-4xl mx-auto mb-12">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <h2 class="text-2xl font-serif font-bold text-stone-900 mb-6">
        Acciones Rápidas
      </h2>
      
      <div class="grid grid-cols-4 gap-6">
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('admin.narraciones.index') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-gear text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">ADMINISTRAR</span>
        </a>
        @endif
        
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
        <a href="{{ route('admin.narraciones.create') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-plus-circle text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">NUEVA NARRACIÓN</span>
        </a>
        @endif
        
        <a href="{{ route('narraciones.index') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-book text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">LEER NARRACIONES</span>
        </a>
        
        <a href="{{ route('profile.edit') }}" 
           class="flex flex-col items-center p-8 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-all text-center group">
          <i class="bi bi-person text-4xl text-stone-600 mb-4 group-hover:text-stone-800 transition-colors"></i>
          <span class="text-sm font-sans font-medium text-stone-900 tracking-wide uppercase">MI PERFIL</span>
        </a>
      </div>
    </div>
  </div>
  @endif

  <!-- Following Section -->
  <div class="max-w-4xl mx-auto mb-12">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <h2 class="text-2xl font-serif font-bold text-stone-900 mb-6">
        Autores que Sigues
      </h2>
      
      <div id="following-authors" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Los autores que sigue el usuario se cargarán aquí -->
        <div class="text-center py-8">
          <i class="bi bi-people text-4xl text-stone-300 mb-4"></i>
          <p class="text-stone-600 font-sans">Aún no sigues a ningún autor</p>
          <p class="text-sm text-stone-500 font-sans mt-2">Descubre nuevas narraciones y sigue a sus autores</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-stone-200 p-8">
      <h2 class="text-2xl font-serif font-bold text-stone-900 mb-6">
        Actividad Reciente
      </h2>
      
      @php
          $recentNarraciones = Auth::user()->narraciones()
              ->where('estado', 'publicado')
              ->orderBy('fecha_publicacion', 'desc')
              ->take(3)
              ->get();
      @endphp
      
      @if($recentNarraciones->count() > 0)
        <div class="space-y-4">
          @foreach($recentNarraciones as $narracion)
            <div class="flex items-center p-4 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
              <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 bg-stone-300 rounded-full flex items-center justify-center">
                  <i class="bi bi-book text-stone-600"></i>
                </div>
              </div>
              <div class="flex-grow">
                <h4 class="font-sans font-medium text-stone-900 mb-1">
                  <a href="{{ route('narraciones.show', $narracion->slug) }}" 
                     class="text-stone-900 hover:text-blue-600 transition-colors">
                    {{ $narracion->titulo }}
                  </a>
                </h4>
                <p class="text-sm text-stone-600">
                  Publicada el {{ $narracion->fecha_publicacion->format('d/m/Y') }}
                </p>
                <div class="flex items-center mt-2 space-x-4">
                  <span class="text-xs px-2 py-1 rounded-full 
                    @switch($narracion->permiso_lectura)
                      @case('publico')
                        bg-blue-100 text-blue-700
                        @break
                      @case('seguidores')
                        bg-purple-100 text-purple-700
                        @break
                    @endswitch
                  ">
                    @switch($narracion->permiso_lectura)
                      @case('publico')
                        <i class="bi bi-globe mr-1"></i> Público
                        @break
                      @case('seguidores')
                        <i class="bi bi-people-fill mr-1"></i> Seguidores
                        @break
                    @endswitch
                  </span>
                  <span class="text-xs text-stone-500">
                    <i class="bi bi-eye mr-1"></i> {{ $narracion->count_read }} lecturas
                  </span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-12">
          <i class="bi bi-clock-history text-5xl text-stone-300 mb-4"></i>
          <p class="text-stone-600 font-sans">No hay actividad reciente</p>
          <p class="text-sm text-stone-500 font-sans mt-2">Tu actividad aparecerá aquí</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar autores que sigue el usuario
    loadFollowingAuthors();
});

function loadFollowingAuthors() {
    fetch('/following', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('following-authors');
        
        if (data.error) {
            console.error(data.error);
            return;
        }
        
        if (data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="bi bi-people text-4xl text-stone-300 mb-4"></i>
                    <p class="text-stone-600 font-sans">Aún no sigues a ningún autor</p>
                    <p class="text-sm text-stone-500 font-sans mt-2">Descubre nuevas narraciones y sigue a sus autores</p>
                </div>
            `;
        } else {
            container.innerHTML = data.map(author => `
                <div class="bg-stone-50 rounded-lg p-4 border border-stone-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-stone-300 rounded-full flex items-center justify-center mr-3">
                                <i class="bi bi-person text-stone-600"></i>
                            </div>
                            <div>
                                <h4 class="font-sans font-medium text-stone-900">${author.name}</h4>
                                <p class="text-sm text-stone-600">${author.narraciones_count} narraciones</p>
                            </div>
                        </div>
                        <button 
                            class="unfollow-author-btn text-xs text-red-600 hover:text-red-700 font-sans"
                            data-author-id="${author.id}">
                            <i class="bi bi-person-dash"></i>
                        </button>
                    </div>
                    <div class="text-xs text-stone-500">
                        Siguiendo desde ${new Date(author.followed_at).toLocaleDateString()}
                    </div>
                </div>
            `).join('');
            
            // Añadir event listeners para botones de unfollow
            document.querySelectorAll('.unfollow-author-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const authorId = this.dataset.authorId;
                    const authorCard = this.closest('.bg-stone-50');
                    
                    if (confirm('¿Dejar de seguir a este autor?')) {
                        unfollowAuthor(authorId, authorCard);
                    }
                });
            });
        }
    })
    .catch(error => {
        console.error('Error loading following authors:', error);
    });
}

function unfollowAuthor(authorId, authorCard) {
    fetch(`/unfollow/${authorId}`, {
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
        
        // Animación de salida
        authorCard.style.opacity = '0';
        authorCard.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            authorCard.remove();
            
            // Recargar la lista si no quedan autores
            const remainingAuthors = document.querySelectorAll('#following-authors .bg-stone-50');
            if (remainingAuthors.length === 0) {
                loadFollowingAuthors();
            }
        }, 300);
        
        showToast(data.message);
    })
    .catch(error => {
        console.error('Error unfollowing author:', error);
        showToast('Error al dejar de seguir al autor');
    });
}

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
