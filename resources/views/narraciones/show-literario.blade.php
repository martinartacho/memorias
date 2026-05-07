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
            @case('privado')
              <i class="bi bi-lock-fill text-red-600" title="Privado"></i>
              <span class="text-red-600 text-sm ml-1">Privado</span>
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
        <div class="flex space-x-4">
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
        $relatedQuery = \App\Models\Narracion::where('id', '!=', $narracion->id)
            ->publicado()
            ->orderByFecha();
        
        // Aplicar misma lógica de permisos que en index
        if (auth()->check()) {
            $followedAuthorIds = auth()->user()->following()->pluck('followed_id');
            $relatedQuery->where(function($q) use ($followedAuthorIds) {
                $q->where('permiso_lectura', 'publico')
                  ->orWhere(function($subQuery) use ($followedAuthorIds) {
                      $subQuery->whereIn('user_id', $followedAuthorIds)
                               ->where('permiso_lectura', 'seguidores');
                  });
            });
        } else {
            $relatedQuery->where('permiso_lectura', 'publico');
        }
        
        // NUNCA mostrar narraciones privadas
        $relatedQuery->where('permiso_lectura', '!=', 'privado');
        
        $related = $relatedQuery->take(3)->get();
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
                @case('privado')
                  <i class="bi bi-lock-fill text-red-600" title="Privado"></i>
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
