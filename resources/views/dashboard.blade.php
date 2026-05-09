@extends('layouts.literario')

@section('title', 'Dashboard - Memorias sin orden')

@section('content')
<div class="container">
  <!-- Alert Messages -->
  @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
      <span class="material-icons text-green-600 mr-2">check_circle</span>
      {{ session('success') }}
    </div>
  @endif
  
  @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
      <span class="material-icons text-red-600 mr-2">error</span>
      {{ session('error') }}
    </div>
  @endif
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
        Descubrir Autores
      </h2>
      
      @php
        // Obtener todos los autores excepto el usuario actual
        $authors = \App\Models\User::where('id', '!=', Auth::id())
            ->whereIn('role', ['admin', 'editor']) // Mostrar autores, admins y editores
            ->withCount('narraciones')
            ->get();
      @endphp
      
      @if($authors->count() > 0)
        <div class="space-y-4">
          @foreach($authors as $author)
            <div class="flex items-center justify-between p-4 bg-stone-50 rounded-lg border border-stone-200 hover:shadow-md transition-shadow">
              <div class="flex items-center">
                <div class="w-12 h-12 bg-stone-300 rounded-full flex items-center justify-center mr-4">
                  <i class="bi bi-person text-stone-600"></i>
                </div>
                <div>
                  <h4 class="font-sans font-medium text-stone-900">{{ $author->name }}</h4>
                  <p class="text-sm text-stone-600">{{ $author->narraciones_count }} narraciones publicadas</p>
                </div>
              </div>
              
              @php
                $isFollowing = Auth::user()->following()->where('followed_id', $author->id)->exists();
              @endphp
              
              <button 
                onclick="toggleFollow({{ $author->id }}, this)"
                class="follow-toggle-btn inline-flex items-center px-4 py-2 text-sm font-sans rounded-lg transition-colors
                  @if($isFollowing)
                    bg-green-600 text-white hover:bg-green-700
                  @else
                    bg-purple-600 text-white hover:bg-purple-700
                  @endif
                "
                data-following="{{ $isFollowing ? 'true' : 'false' }}"
                data-author-name="{{ $author->name }}">
                <i class="bi 
                  @if($isFollowing)
                    bi-person-check
                  @else
                    bi-person-plus
                  @endif
                  mr-2"></i>
                <span class="follow-text">
                  @if($isFollowing)
                    Siguiendo
                  @else
                    Seguir
                  @endif
                </span>
              </button>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-8">
          <i class="bi bi-people text-4xl text-stone-300 mb-4"></i>
          <p class="text-stone-600 font-sans">No hay otros autores disponibles</p>
        </div>
      @endif
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

<!-- Scripts -->
<script>
function toggleFollow(authorId, button) {
    const authorName = button.dataset.authorName;
    const isFollowing = button.dataset.following === 'true';
    
    // Deshabilitar botón durante la petición
    button.disabled = true;
    button.classList.add('opacity-50', 'cursor-not-allowed');
    
    // Determinar endpoint
    const url = isFollowing ? `/unfollow/${authorId}` : `/follow/${authorId}`;
    
    // Crear formulario temporal para enviar petición
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    // Agregar CSRF token
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    form.appendChild(csrfToken);
    
    // Enviar formulario
    document.body.appendChild(form);
    form.submit();
}
</script>
